<?php

namespace App\Http\Controllers;

use App\Models\Comodity;
use App\Models\Marketplace;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IndexingController extends Controller
{
    public $marketplace;

    public $comodity;

    public function __construct()
    {
        $this->marketplace = new Marketplace();
        $this->comodity = new Comodity();
    }

    public function index(){
        return Inertia::render('Indexing/Index',[
            'marketplaces' => $this->marketplace->getAllData(),
            'comodities' => $this->comodity->getAllComodity(),
        ]);
    }
}
