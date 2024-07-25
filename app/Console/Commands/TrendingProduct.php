<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TrendingProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:trending-product';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawling Trending Product in all marketplace';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $request = new \stdClass();
        $request->engine = "trending";
        Http::post(env('APP_CRAWLER_GATEWAY').'/crawler', $request);
    }
}
