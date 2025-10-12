<?php

use Illuminate\Support\Facades\Schedule;

if (now()->hour >= 9 && now()->hour <= 22) Schedule::command('app:get-stores-group-queues')->everyTenSeconds();

if (now()->hour >= 9 && now()->hour <= 22) Schedule::command('app:push-notification-for-queue')->everyTenSeconds();

Schedule::command('app:get-new-stores')->dailyAt('10:00');
