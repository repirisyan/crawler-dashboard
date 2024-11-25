<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:crawling-product')->weekly();
Schedule::command('app:trending-product')->weekly();
