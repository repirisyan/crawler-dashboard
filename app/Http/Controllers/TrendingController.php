<?php

namespace App\Http\Controllers;

use App\Models\Marketplace;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TrendingController extends Controller
{
    public $marketplace;

    public function __construct()
    {
        $this->marketplace = new Marketplace();
    }

    public function index(){
        return Inertia::render('Trending/Index',[
            'marketplaces' => $this->marketplace->getAllData(),
        ]);
    }
}
