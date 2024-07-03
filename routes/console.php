<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:daily-clear-crawling');
Schedule::command('app:daily-crawling');
