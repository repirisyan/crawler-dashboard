<?php

namespace App\Console\Commands;

use App\Models\TempItem;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DailyClearCrawling extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:daily-clear-crawling';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete Yesterday Crawling Data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $yesterday = Carbon::yesterday()->toDateString();
        TempItem::whereDate('created_at', $yesterday)->delete();
    }
}
