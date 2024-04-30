<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:get-stores-group-queues')->everyMinute();
