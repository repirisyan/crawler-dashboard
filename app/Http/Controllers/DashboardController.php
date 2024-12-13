<?php

namespace App\Http\Controllers;

use App\Models\Marketplace;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public $crawler;

    public function __construct()
    {
        $this->crawler = new Marketplace;
    }

    public function index()
    {
        return Inertia::render('Dashboard/Index', [
            'crawlers' => $this->crawler->getAllData(),
        ]);
    }
}
