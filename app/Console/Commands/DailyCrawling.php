<?php

namespace App\Console\Commands;

use App\Models\Marketplace;
use App\Models\Notification;
use App\Models\SearchList;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class DailyCrawling extends Command
{
    public $marketplace;

    public $search_list;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:daily-crawling';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
        $this->marketplace = new Marketplace();
        $this->search_list = new SearchList();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $marketplaces = $this->marketplace->getActiveMarketplace();
        $search_list = $this->search_list->getAllData();
        foreach ($marketplaces as $marketplace) {
            foreach ($search_list as $item) {
                $request = new \stdClass();
                $request->marketplace_id = $marketplace->id;
                $request->comodity_id = $item->comodity_id;
                $request->keyword_id = $item->keyword_id;
                $request->marketplace = strtolower($marketplace->name);
                $request->keyword = $item->keyword->name;
                $request->user_id = 1;
                $response = Http::post(env('APP_CRAWLER_API').'/crawler', $request);
                Notification::create([
                    'message' => 'Start crawling '.$marketplace->name.', keyword : '.$item->keyword->name,
                    'user_id' => 1,
                ]);
            }
            if ($response->successful()) {
                $this->marketplace->changeStatus($marketplace->id, true);
            }
        }
    }
}
