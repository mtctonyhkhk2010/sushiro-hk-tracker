<?php

use Illuminate\Support\Facades\Schedule;

if (now()->hour >= 9 && now()->hour <= 22) Schedule::command('app:get-stores-group-queues')->everyMinute();

if (now()->hour >= 9 && now()->hour <= 22) Schedule::command('app:push-notification-for-queue')->everyMinute();

Schedule::command('app:get-new-stores')->dailyAt('10:00');
