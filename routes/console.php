<?php

use Illuminate\Support\Facades\Schedule;

// Schedule::command('app:daily-clear-crawling')->weeklyOn(7,'00:00');
// Schedule::command('app:daily-crawling')->weeklyOn(7,'01:00');

// Schedule::command('app:daily-clear-crawling');
// Schedule::command('app:daily-crawling');
Schedule::command('app:index-crawling');


