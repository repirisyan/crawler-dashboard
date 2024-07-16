<?php

namespace App\Http\Controllers;

use App\Models\Comodity;
use App\Models\Keyword;
use App\Models\Marketplace;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CrawlerController extends Controller
{
    public $crawler;

    public $comodity;

    public $keyword;

    public function __construct()
    {
        $this->crawler = new Marketplace();
        $this->comodity = new Comodity();
        $this->keyword = new Keyword();
    }

    public function index()
    {
        return Inertia::render('Crawler/Index', [
            'crawlers' => $this->crawler->getAllData(),
            'comodities' => $this->comodity->getAllComodity(),
            'keywords' => $this->keyword->getAllKeyword(),
            'user_id' => Auth::user()->id,
        ]);
    }

    public function getStatus($id)
    {
        return response()->json($this->crawler->getStatus($id));
    }
}
