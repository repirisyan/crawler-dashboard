<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:daily-crawling')->dailyAt('01:00');
