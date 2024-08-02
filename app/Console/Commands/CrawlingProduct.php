<?php

namespace App\Console\Commands;

use App\Models\Keyword;
use App\Models\Marketplace;
use App\Models\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CrawlingProduct extends Command
{
    public $marketplace;

    public $keywords;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:crawling-product';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawling Product base on search list and loop base on marketplace';

    public function __construct()
    {
        parent::__construct();
        $this->marketplace = new Marketplace;
        $this->keywords = new Keyword;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // DB::table('temp_items')->truncate();
        $marketplaces = $this->marketplace->getActiveMarketplace();
        $keywords = $this->keywords->getAllKeyword(true);
        foreach ($marketplaces as $marketplace) {
            foreach ($keywords as $item) {
                $request = new \stdClass;
                $request->marketplace_id = $marketplace->id;
                $request->comodity_id = $item->comodity_id;
                $request->keyword_id = $item->id;
                $request->engine = strtolower($marketplace->name);
                $request->keyword = $item->name;
                $request->comodity = $item->comodity->name;
                $request->sub_comodity = $item->sub_comodity;
                $request->second_level_sub_comodity = $item->second_level_sub_comodity;
                $request->third_level_sub_comodity = $item->third_level_sub_comodity;
                $request->user_id = 1;
                $response = Http::post(env('APP_CRAWLER_GATEWAY').'/crawler', $request);
                Notification::create([
                    'message' => 'Start crawling '.$marketplace->name.', keyword : '.$item->name,
                    'user_id' => 1,
                    'category' => 'info',
                ]);
            }
            if ($response->successful()) {
                $this->marketplace->changeStatus($marketplace->id, true);
            }
        }
    }
}
