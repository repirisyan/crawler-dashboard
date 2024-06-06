<?php

namespace App\Http\Controllers;

use App\Models\Marketplace;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class CrawlerController extends Controller
{
    public $crawler;

    public function __construct()
    {
        $this->crawler = new Marketplace();    
    }

    public function index(){
        return Inertia::render('Crawler/Index',[
            'crawlers' => $this->crawler->getData()
        ]);
    }

    public function crawlerData(Request $request){
        try {
            $response = Http::post(env('APP_CRAWLER_API').'/crawler',$request);
            if($response->successful()){
                $this->crawler->changeStatus($request->marketplace_id,true);
            }
            return response($response->successful());
        } catch (Exception $e) {
            return response($e->getMessage());
        }
    }

    public function getStatus($id){
        return response()->json($this->crawler->getStatus($id));
    }
}
