<?php

namespace App\Http\Controllers;

use App\Models\Marketplace;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MarketplaceController extends Controller
{
    public $marketplace;

    public function __construct(){
        $this->marketplace = new Marketplace();
    }

    public function index(){
        return Inertia::render('Marketplace',[
            'marketplaces' => $this->marketplace->getAllData()
        ]);
    }

    public function maintenance(Request $request,$id){
        try {
            $this->marketplace->toggleMaintenance(!$request->maintenance,$id);
            return;
        } catch (Exception $e) {
            return response()->json($e->getMessage());
        }
    }
}
