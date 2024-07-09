<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class IndexCrawling extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:index-crawling';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Indexing Data Crawling';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $request = new \stdClass();
        $request->engine = 'indexing';
        Http::post(env('APP_CRAWLER_API').'/crawler', $request);
    }
}
