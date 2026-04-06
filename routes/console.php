<?php

use Illuminate\Support\Facades\Schedule;

/*
 * The scheduler runs `domains:check` every minute.
 * The command itself determines which domains are actually due,
 * so running it every minute is safe and adds no significant overhead.
 */
Schedule::command('domains:check')
    ->everyMinute()
    ->withoutOverlapping(10)
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/scheduler.log'));
