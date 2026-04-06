# Монитор доменов

Система мониторинга доступности доменов на **Laravel 12 + Vue 3**, разработанная как проект уровня Senior / Team Lead.

## Возможности

| Категория | Описание |
|---|---|
| **Аутентификация** | Регистрация, вход, выход, rate-limiting, защищённые маршруты |
| **Домены** | CRUD с проверкой прав владельца через Policy |
| **Настройки проверок** | Интервал (1–1440 мин.), таймаут (1–60 с), метод (HEAD / GET) |
| **Планировщик** | Команда `domains:check` запускается каждую минуту; проверяет только те домены, у которых наступило время |
| **Асинхронные задачи** | `CheckDomainJob` — уникальный per-domain, 2 повтора с задержкой |
| **История** | Каждая проверка сохраняется: дата, HTTP-код, время ответа, ошибка |
| **Статистика** | Доступность за 7 дней (%), среднее время ответа |
| **Уведомления** ✨ | Email при переходе домена в статус «недоступен» (только при смене статуса) |
| **Docker** | app, Nginx, MySQL, воркер очереди, планировщик, Mailpit — одной командой |
| **Фронтенд** | Vue 3 SPA, Vue Router, Pinia, Axios, Tailwind CSS, Vite |

---

## Архитектура

```
┌─────────────┐   HTTP   ┌──────────┐
│   Браузер   │─────────▶│  Nginx   │
└─────────────┘          └────┬─────┘
                              │ FastCGI
                         ┌────▼──────────────────┐
                         │  PHP-FPM (Laravel)     │
                         │  API Controllers       │
                         │  Services / DTOs       │
                         └────┬──────────────────┘
                              │
              ┌───────────────┼───────────────┐
              │               │               │
        ┌─────▼──────┐ ┌──────▼──────┐ ┌─────▼───────┐
        │    MySQL    │ │  Очередь    │ │ Планировщик │
        │  domains    │ │  (jobs)     │ │  каждую 1м  │
        │  check_logs │ └──────┬──────┘ └─────┬───────┘
        └─────────────┘        │               │
                         ┌─────▼───────────────▼──┐
                         │      Воркер очереди      │
                         │   CheckDomainJob         │
                         │   DomainMonitorService   │
                         │   DomainCheckService     │
                         └──────────────────────────┘
```

### Структура модулей

```
app/Modules/
├── Auth/           Контроллеры, Requests, маршруты
├── Dashboard/      Контроллер, маршруты
├── Domain/         Контроллер, Модель, Policy, Requests, ServiceProvider, маршруты
└── Monitoring/     Команда, DTO, Job, Модель, Уведомление, Сервисы, ServiceProvider
```

### Ключевые архитектурные решения

| Решение | Обоснование |
|---|---|
| **Сервисный слой** | `DomainCheckService`, `DomainMonitorService` — бизнес-логика вне контроллеров и джобов |
| **DTO** `CheckResult` | Иммутабельный объект-значение; строгая типизация между слоями |
| **Policy** `DomainPolicy` | Проверка владения ресурсом на уровне фреймворка |
| **Unique job** | `uniqueId()` предотвращает параллельные дублирующиеся задачи для одного домена |
| **Scope** `dueForCheck()` | Единственное место логики расписания — переиспользуется в команде и тестах |
| **Уведомление только при смене статуса** | Нет спама при каждой неудачной проверке |
| **Session auth (Sanctum SPA)** | Без JWT — нет токенов в localStorage, нет XSS-уязвимости |
| **`useFormErrors` composable** | Единый контракт извлечения Laravel 422 ошибок по всем формам |

---

## Быстрый старт (Docker)

```bash
# 1. Клонировать и настроить
git clone <repo-url> domain-monitor && cd domain-monitor
cp .env.example .env

# 2. Запустить все сервисы
docker compose up -d --build

# 3. Первоначальная настройка
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose exec app npm install && npm run build

# 4. Открыть в браузере
open http://localhost:8080

# Логин: admin@example.com / password
```

**Mailpit** (предпросмотр писем): http://localhost:8025

---

## Запуск без Docker

```bash
composer install
cp .env.example .env
# Заполнить .env: DB_*, MAIL_*, QUEUE_CONNECTION=database

php artisan key:generate
php artisan migrate --seed

npm install
npm run dev   # Vite dev server с HMR

# В отдельных терминалах:
php artisan serve           # Веб-сервер
php artisan queue:work       # Воркер задач
php artisan schedule:work    # Планировщик (dev-режим)
```

---

## Artisan-команды

```bash
# Поставить в очередь проверки всех доменов с наступившим временем
php artisan domains:check

# Принудительная проверка конкретного домена
php artisan domains:check --domain-id=1
```

---

## Структура фронтенда

```
resources/js/
├── api/          client.js (Axios), auth.js, domains.js, dashboard.js
├── stores/       auth.js (Pinia), flash.js (тосты)
├── router/       index.js (ленивые маршруты + beforeEach-guard)
├── composables/  useFormErrors.js (Laravel 422 → реактивные ошибки)
├── utils/        date.js (diffForHumans + formatDatetime через Intl API)
├── components/   AppLayout, GuestLayout, DomainForm, StatusBadge,
│                 FlashMessages, Pagination, FormField
└── views/        auth/Вход+Регистрация, Главная, domains/Список+Создание+Редактирование+Детали
```

---

## Схема базы данных

```sql
domains
  id, user_id, name, url,
  check_interval (мин.), request_timeout (с), check_method (HEAD|GET),
  is_active, status (unknown|up|down), last_checked_at,
  notify_on_failure, notification_email,
  created_at, updated_at

check_logs
  id, domain_id,
  checked_at, is_up, http_code, response_time_ms, error_message
```

---

## Переменные окружения

| Переменная | По умолчанию | Описание |
|---|---|---|
| `QUEUE_CONNECTION` | `database` | В продакшне рекомендуется `redis` |
| `MAIL_MAILER` | `smtp` | В разработке — `log` |
| `DB_CONNECTION` | `mysql` | Также поддерживается `pgsql` |
| `SANCTUM_STATEFUL_DOMAINS` | `localhost` | Домены SPA для Sanctum |
