<?php

namespace App\Http\Controllers;

use App\Models\Comodity;
use App\Models\Marketplace;
use Inertia\Inertia;

class SupervisionController extends Controller
{

    public $comodity;

    public $marketplace;

    public function __construct()
    {
        $this->comodity = new Comodity;
        $this->marketplace = new Marketplace;
    }

    public function index()
    {
        return Inertia::render('Supervision/Index', [
            'comodities' => $this->comodity->getAllComodity(),
            'marketplaces' => $this->marketplace->getAllData(),
        ]);
    }
}
