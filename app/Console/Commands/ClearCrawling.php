<?php

namespace App\Console\Commands;

use App\Models\TempItem;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearCrawling extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-crawling';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncate temp_items table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $yesterday = Carbon::yesterday()->toDateString();
        // TempItem::where('created_at', '<', now()->startOfDay())->delete();
        DB::table('temp_items')->truncate();
    }
}
