<?php

namespace App\Http\Controllers;

use App\Models\TempItem;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TempItemController extends Controller
{
    protected $temp_item;

    public function __construct()
    {
        $this->temp_item = new TempItem();
    }

    public function tempItemData(Request $request)
    {
        try {
            return response()->json($this->temp_item->getData($request));
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function truncateData()
    {
        try {
            return DB::table('temp_items')->truncate();
        } catch (Exception $e) {
            return response()->json($e->getMessage());
        }
    }
}
