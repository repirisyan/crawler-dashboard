<?php

namespace App\Http\Controllers;

use App\Models\Marketplace;
use App\Models\Notification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class CrawlerController extends Controller
{
    public $crawler;

    public function __construct()
    {
        $this->crawler = new Marketplace();
    }

    public function index()
    {
        return Inertia::render('Crawler/Index', [
            'crawlers' => $this->crawler->getData(),
            'user_id' => Auth::user()->id,
        ]);
    }

    public function crawlerData(Request $request)
    {
        try {
            if ($this->crawler->getStatus($request->marketplace_id)) {
                return response('This Engine is on working', 400);
            }

            $response = Http::post(env('APP_CRAWLER_API').'/crawler', $request);
            Notification::create([
                'message' => 'Start crawling '.$request->marketplace.', keyword : '.$request->keyword,
                'user_id' => auth()->user()->id,
            ]);
            if ($response->successful()) {
                $this->crawler->changeStatus($request->marketplace_id, true);
            }

            return response($response->successful());
        } catch (Exception $e) {
            return response($e->getMessage());
        }
    }

    public function getStatus($id)
    {
        return response()->json($this->crawler->getStatus($id));
    }
}
