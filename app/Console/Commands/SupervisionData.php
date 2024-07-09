<?php

namespace App\Console\Commands;

use App\Models\SupervisionList;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SupervisionData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:supervision-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add Data from Crawling To Supervision base on Supervision List';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $keywords = SupervisionList::select('name')->get();
        foreach ($keywords as $keyword) {
            $request = new \stdClass();
            $request->keyword = $keyword->name;
            $request->user_id = 1;
            $request->engine = 'supervision';
            Http::post(env('APP_CRAWLER_API').'/crawler', $request);
        }
    }
}
