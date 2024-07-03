<?php

namespace App\Http\Controllers;

use App\Models\Comodity;
use App\Models\Marketplace;
use App\Models\Supervision;
use App\Models\TempItem;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SupervisionController extends Controller
{
    public $supervision;

    public $comodity;

    public $marketplace;

    public function __construct()
    {
        $this->supervision = new Supervision();
        $this->comodity = new Comodity();
        $this->marketplace = new Marketplace();
    }

    public function index()
    {
        return Inertia::render('Supervision/Index', [
            'comodities' => $this->comodity->getAllComodity(),
            'marketplaces' => $this->marketplace->getAllData(),
        ]);
    }

    public function data(Request $request)
    {
        return response()->json($this->supervision->getDatas($request));
    }

    public function checkLink(Request $request, $id)
    {
        try {
            Supervision::find($id)->update([
                'check' => ! $request->status,
                'last_check' => now(),
            ]);

            return;
        } catch (Exception $e) {
            return response()->json($request);
        }
    }

    public function store(Request $request)
    {
        try {
            $ids = $request->input('ids');
            DB::transaction(function () use ($ids) {
                $products = TempItem::whereIn('id', $ids)->get();
                foreach ($products as $product) {
                    $this->supervision->storeData($product);
                }
                // TempItem::whereIn('id',$ids)->delete();
            });

            return response()->json('Data Saved');
        } catch (Exception $e) {
            return response()->json($e->getMessage(), $e->getCode());
        }
    }

    public function solved(Request $request)
    {
        try {
            Supervision::whereIn('id', $request->ids)->update([
                'status' => true,
                'solved_at' => now(),
            ]);

            return response()->json('Data Solved');
        } catch (Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    public function destroy(Request $request)
    {
        try {
            Supervision::whereIn('id', $request->ids)->delete();

            return response()->json('Data Deleted');
        } catch (Exception $e) {
            return response()->json($e->getMessage(), $e->getCode());
        }
    }
}
