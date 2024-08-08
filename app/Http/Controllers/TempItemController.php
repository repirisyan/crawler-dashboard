<?php

namespace App\Http\Controllers;

use App\Models\TempItem;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TempItemController extends Controller
{
    protected $temp_item;

    public function __construct()
    {
        $this->temp_item = new TempItem;
    }

    public function tempItemData(Request $request)
    {
        try {
            return response()->json($this->temp_item->getData($request));
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function getTotalData(){
        try {
            $data = Cache::remember('total_temp_item', 86400, function () {
                return $this->temp_item->getTotalItem();
            });
            
            return response()->json($data);
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

    public function deleteItem(Request $request)
    {
        try {
            TempItem::whereIn('id', $request->ids)->delete();

            return;
        } catch (Exception $e) {
            return response()->json($e->getMessage());
        }
    }
}
