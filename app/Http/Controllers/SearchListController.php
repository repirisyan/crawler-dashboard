<?php

namespace App\Http\Controllers;

use App\Models\Comodity;
use App\Models\Keyword;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SearchListController extends Controller
{
    public $comodity;

    public $keyword;

    public function __construct()
    {
        $this->comodity = new Comodity;
        $this->keyword = new Keyword;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Inertia::render('SearchList/Index', [
            'keywords' => $this->keyword->getKeywords($request->all()),
            'comodities' => $this->comodity->getAllComodity(),
            'params' => $request->all(),
        ]);
    }

    public function changeStatus(Request $request, $id)
    {
        try {
            $this->keyword->changeStatus($id, $request->status);

            return;
        } catch (Exception $e) {
            return response()->json($e->getMessage());
        }
    }
}
