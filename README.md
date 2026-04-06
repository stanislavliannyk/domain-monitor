# Domain Monitor

A production-ready domain uptime monitoring service built with **Laravel 12**, designed to demonstrate Senior/Team Lead engineering practices.

## Features

| Category | Detail |
|---|---|
| **Auth** | Registration, login, logout, rate-limited login, protected routes |
| **Domains** | CRUD with per-user policy enforcement |
| **Check settings** | Interval (1–1440 min), timeout (1–60 s), method (HEAD/GET) |
| **Scheduler** | `domains:check` command dispatched every minute; only runs checks whose interval has elapsed |
| **Async jobs** | `CheckDomainJob` queued, unique per domain, 2 retries with back-off |
| **History** | Every check saved with date, HTTP code, response time, error message |
| **Statistics** | 7-day uptime %, avg response time |
| **Notifications** ✨ | Email alert on domain going down (status-change only, not every failure) |
| **Docker** | App, Nginx, MySQL, queue worker, scheduler, Mailpit — all in one `docker-compose up` |

---

## Architecture Overview

```
┌─────────────┐   HTTP   ┌──────────┐
│   Browser   │─────────▶│  Nginx   │
└─────────────┘          └────┬─────┘
                              │ FastCGI
                         ┌────▼──────────┐
                         │  PHP-FPM app  │
                         │  Controllers  │
                         │  Services     │
                         └────┬──────────┘
                              │
              ┌───────────────┼───────────────┐
              │               │               │
        ┌─────▼──────┐ ┌──────▼──────┐ ┌─────▼──────┐
        │   MySQL     │ │   Queue DB  │ │  Scheduler  │
        │  (domains,  │ │  (jobs)     │ │  (every 1m) │
        │  check_logs)│ └──────┬──────┘ └─────┬───────┘
        └─────────────┘        │               │
                         ┌─────▼───────────────▼──┐
                         │     Queue Worker         │
                         │   CheckDomainJob         │
                         │   DomainMonitorService   │
                         │   DomainCheckService     │
                         └──────────────────────────┘
```

### Key design decisions

- **Service layer** (`DomainCheckService`, `DomainMonitorService`) keeps business logic out of controllers and jobs.
- **DTO** (`CheckResult`) is an immutable value object — no mutable state passes between layers.
- **Policy** (`DomainPolicy`) enforces ownership at the framework level, not scattered in controllers.
- **Unique jobs** (`uniqueId()`) prevent duplicate concurrent checks for the same domain.
- **Status-change notifications** — alerts fire only when transitioning to "down", not on every failed check.
- **Scope `dueForCheck`** — single place that encodes the scheduling logic, reused by command and tests.

---

## Quick Start (Docker)

```bash
# 1. Clone and configure
git clone <repo-url> domain-monitor && cd domain-monitor
cp .env.example .env

# 2. Start all services
docker compose up -d --build

# 3. First-time setup
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed

# 4. Open in browser
open http://localhost:8080

# Login: admin@example.com / password
```

**Mailpit** (email preview): http://localhost:8025

---

## Manual Setup (without Docker)

```bash
composer install
cp .env.example .env
# Edit .env: set DB_*, MAIL_*, QUEUE_CONNECTION=database

php artisan key:generate
php artisan migrate --seed

# Run all three in separate terminals:
php artisan serve                        # Web server
php artisan queue:work                   # Job worker
php artisan schedule:work                # Scheduler (dev)
```

---

## Artisan Commands

```bash
# Dispatch checks for all due domains
php artisan domains:check

# Force-check a specific domain
php artisan domains:check --domain-id=1

# Manually process jobs (alternative to daemon)
php artisan queue:work --once
```

---

## Project Structure

```
app/
├── Console/Commands/
│   └── CheckDomains.php          # Dispatches jobs for due domains
├── DTOs/
│   └── CheckResult.php           # Immutable result value object
├── Http/
│   ├── Controllers/
│   │   ├── Auth/                 # Login, register, logout
│   │   ├── DashboardController   # Overview + stats
│   │   └── DomainController      # Full CRUD + check-now
│   └── Requests/                 # Form validation
├── Jobs/
│   └── CheckDomainJob.php        # Queued, unique, retryable
├── Models/
│   ├── Domain.php                # Scopes: active(), dueForCheck()
│   └── CheckLog.php
├── Notifications/
│   └── DomainStatusChanged.php   # Queued mail notification
├── Policies/
│   └── DomainPolicy.php          # Ownership enforcement
└── Services/
    ├── DomainCheckService.php    # HTTP check via Guzzle
    └── DomainMonitorService.php  # Orchestrates check → log → notify
```

---

## Database Schema

```sql
domains
  id, user_id (FK), name, url,
  check_interval (minutes), request_timeout (seconds), check_method (HEAD|GET),
  is_active, status (unknown|up|down), last_checked_at,
  notify_on_failure, notification_email,
  created_at, updated_at

check_logs
  id, domain_id (FK),
  checked_at, is_up, http_code, response_time_ms, error_message
```

Indexes on `(is_active, last_checked_at)` and `(domain_id, checked_at)` make scheduling and history queries efficient at scale.

---

## Testing

```bash
php artisan test
```

Tests use SQLite in-memory and mock `DomainCheckService` so no real HTTP calls are made.

---

## Environment Variables

| Variable | Default | Description |
|---|---|---|
| `QUEUE_CONNECTION` | `database` | Use `redis` for production |
| `MAIL_MAILER` | `smtp` | Set to `log` during development |
| `DB_CONNECTION` | `mysql` | Also supports `pgsql` |
| `CHECK_INTERVAL_DEFAULT` | `5` | Default check interval (minutes) |
| `CHECK_TIMEOUT_DEFAULT` | `10` | Default request timeout (seconds) |
