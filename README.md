# Монитор доменов

Система мониторинга доступности доменов на **Laravel 12 + Vue 3**.

## Возможности

| Категория | Описание |
|---|---|
| **Аутентификация** | Регистрация, вход, выход, rate-limiting, защищённые маршруты |
| **Домены** | CRUD с проверкой прав владельца через Policy |
| **Настройки проверок** | Интервал (1–1440 мин.), таймаут (1–60 с), метод (HEAD / GET) |
| **Планировщик** | Команда `domains:check` каждую минуту; проверяет только домены с наступившим временем |
| **Асинхронные задачи** | `CheckDomainJob` — уникальный per-domain, 2 повтора с задержкой 30 с |
| **История** | Каждая проверка сохраняется: дата, HTTP-код, время ответа, ошибка |
| **Статистика** | Доступность за 7 дней (%), среднее время ответа |
| **Уведомления** | Email при переходе домена в статус «недоступен» (только при смене статуса) |
| **Docker** | app, Nginx, MySQL, воркер очереди, планировщик, Mailpit — одной командой |
| **Фронтенд** | Vue 3 SPA, Vue Router, Pinia, Axios, Tailwind CSS, Vite |

---

## Архитектура

```
┌─────────────┐   HTTP   ┌──────────┐
│   Браузер   │─────────▶│  Nginx   │
└─────────────┘          └────┬─────┘
                              │ FastCGI
                         ┌────▼────────────────┐
                         │  PHP-FPM (Laravel)   │
                         │  API Controllers     │
                         │  Services / DTOs     │
                         └────┬────────────────┘
                              │
              ┌───────────────┼───────────────┐
              │               │               │
        ┌─────▼──────┐ ┌──────▼──────┐ ┌─────▼───────┐
        │    MySQL    │ │  Очередь    │ │ Планировщик │
        │  domains    │ │  (jobs)     │ │  каждые 1м  │
        │  check_logs │ └──────┬──────┘ └─────┬───────┘
        └─────────────┘        │               │
                         ┌─────▼───────────────▼──┐
                         │     Воркер очереди       │
                         │  CheckDomainJob          │
                         │  DomainMonitorService    │
                         │  DomainCheckService      │
                         └──────────────────────────┘
```

---

## Структура бэкенда

```
app/
├── Http/Controllers/Controller.php   Абстрактный контроллер: success() / error()
├── Models/User.php
├── Traits/HasServiceError.php        Трейт для сервисов: getError() / setError() / clearError()
├── Providers/AppServiceProvider.php
└── Modules/
    ├── Auth/
    │   ├── Controllers/AuthController.php
    │   ├── Requests/LoginRequest.php, RegisterRequest.php
    │   ├── Services/AuthService.php
    │   └── routes/api.php
    ├── Dashboard/
    │   ├── Controllers/DashboardController.php
    │   ├── Services/DashboardService.php
    │   └── routes/api.php
    ├── Domain/
    │   ├── Controllers/DomainController.php
    │   ├── Models/Domain.php
    │   ├── Policies/DomainPolicy.php
    │   ├── Requests/StoreDomainRequest.php, UpdateDomainRequest.php
    │   ├── Services/DomainService.php
    │   ├── DomainServiceProvider.php
    │   └── routes/api.php
    └── Monitoring/
        ├── Commands/CheckDomains.php
        ├── DTOs/CheckResult.php
        ├── Jobs/CheckDomainJob.php
        ├── Models/CheckLog.php
        ├── Notifications/DomainStatusChanged.php, AnonymousNotifiable.php
        ├── Services/DomainCheckService.php, DomainMonitorService.php
        └── MonitoringServiceProvider.php
```

---

## Структура фронтенда

```
resources/js/
├── app.js · App.vue
├── router/index.js              Агрегирует маршруты из модулей
├── shared/                      Общие ресурсы
│   ├── api/client.js            Axios-клиент (Sanctum SPA)
│   ├── components/              AppLayout, GuestLayout, FlashMessages,
│   │                            FormField, StatusBadge, Pagination
│   ├── composables/useFormErrors.js   Laravel 422 → реактивные ошибки
│   ├── stores/flash.js          Pinia: тосты
│   ├── utils/date.js            diffForHumans + formatDatetime (Intl API)
│   └── views/NotFoundView.vue
└── modules/
    ├── auth/
    │   ├── api.js · store.js · routes.js
    │   └── views/LoginView.vue, RegisterView.vue
    ├── dashboard/
    │   ├── api.js · routes.js
    │   └── views/DashboardView.vue
    └── domain/
        ├── api.js · routes.js
        ├── components/DomainForm.vue
        └── views/DomainIndexView, DomainCreateView, DomainEditView, DomainShowView
```

---

## Ключевые архитектурные решения

| Решение | Обоснование |
|---|---|
| **Модульная структура** | Бэкенд и фронтенд организованы по одинаковым модулям: Auth, Dashboard, Domain, Monitoring |
| **Сервисный слой** | Вся бизнес-логика и запросы в БД — в сервисах; контроллер только принимает запрос и возвращает ответ |
| **`HasServiceError` trait** | Единый паттерн обработки ошибок в сервисах: `try-catch` → `setError()` → контроллер проверяет `getError()` |
| **`success()` / `error()`** | Методы базового контроллера исключают дублирование формата JSON-ответов |
| **DTO `CheckResult`** | Иммутабельный объект-значение; строгая типизация между слоями мониторинга |
| **Policy `DomainPolicy`** | Проверка владения ресурсом на уровне фреймворка |
| **Unique job** | `uniqueId()` предотвращает параллельные дублирующиеся задачи для одного домена |
| **Scope `dueForCheck()`** | Единственное место логики расписания — переиспользуется в команде |
| **Уведомление только при смене статуса** | Нет спама при каждой неудачной проверке |
| **Session auth (Sanctum SPA)** | Без JWT — нет токенов в localStorage, нет XSS-уязвимости |

---

## Быстрый старт (Docker)

```bash
# 1. Клонировать репозиторий
git clone <repo-url> domain-monitor && cd domain-monitor

# 2. Запустить все сервисы
#    .env, APP_KEY и миграции создаются автоматически при первом запуске
docker compose up -d --build

# 3. Собрать фронтенд
npm install && npm run build

# 4. Открыть в браузере
open http://localhost:8080
# Логин: admin@example.com / password
```

> Если порт 8080 занят — переопределите переменную: `APP_PORT=8090 docker compose up -d`

**Mailpit** (предпросмотр писем): http://localhost:8025
> Если порт занят другим сервисом — укажите свой: `MAILPIT_UI_PORT=9025 docker compose up -d`

---

## Запуск без Docker

```bash
composer install
cp .env.example .env
# Заполнить .env: DB_*, MAIL_*, QUEUE_CONNECTION=database

php artisan key:generate
php artisan migrate --seed

npm install
npm run dev          # Vite dev server с HMR

# В отдельных терминалах:
php artisan serve            # Веб-сервер
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

## Схема базы данных

```
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
| `APP_PORT` | `8080` | Внешний порт Nginx |
| `DB_EXTERNAL_PORT` | `33060` | Внешний порт MySQL |
| `MAILPIT_UI_PORT` | `8025` | Веб-интерфейс Mailpit |
| `MAILPIT_SMTP_PORT` | `1025` | SMTP-порт Mailpit |
| `QUEUE_CONNECTION` | `database` | В продакшне рекомендуется `redis` |
| `MAIL_MAILER` | `smtp` | В разработке можно использовать `log` |
| `SANCTUM_STATEFUL_DOMAINS` | `localhost` | Домены SPA для Sanctum |
