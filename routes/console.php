<?php

use Illuminate\Support\Facades\Schedule;

/*
 * Планировщик запускает `domains:check` каждую минуту.
 * Команда сама определяет, какие домены нуждаются в проверке,
 * поэтому запуск каждую минуту безопасен и не создаёт лишней нагрузки.
 * Команда регистрируется через MonitoringServiceProvider.
 */
Schedule::command('domains:check')
    ->everyMinute()
    ->withoutOverlapping(10)
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/scheduler.log'));
