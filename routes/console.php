<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('domains:check')
    ->everyMinute()
    ->withoutOverlapping(10)
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/scheduler.log'));
