<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:daily-clear-crawling')->weeklyOn(7,'00:00');
Schedule::command('app:daily-crawling')->weeklyOn(7,'01:00');
Schedule::command('app:index-crawler')->weeklyOn(1,'00:00');


