<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:crawling-product')->monthly();
Schedule::command('app:trending-product')->monthly();
