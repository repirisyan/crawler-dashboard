<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchListRequest;
use App\Models\Comodity;
use App\Models\Keyword;
use App\Models\SearchList;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SearchListController extends Controller
{
    public $comodity;

    public $keyword;

    public $search_list;

    public function __construct()
    {
        $this->comodity = new Comodity();
        $this->keyword = new Keyword();
        $this->search_list = new SearchList();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('SearchList', [
            'comodities' => $this->comodity->getAllComodity(),
            'keywords' => $this->keyword->getAllKeyword(),
            'search_list' => $this->search_list->getDatas(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SearchListRequest $request)
    {
        try {
            $this->search_list->storeData($request->all());

            return to_route('search-list.index')->with('message', [200, 'Data Saved']);
        } catch (Exception $e) {
            return to_route('search-list.index')->with('message', [$e->getCode(), $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            return response()->json($this->search_list->getData($id));
        } catch (Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SearchListRequest $request, string $id)
    {
        try {
            $this->search_list->updateData($request->all(), $id);

            return to_route('search-list.index')->with('message', [200, 'Data Updated']);
        } catch (Exception $e) {
            return to_route('search-list.index')->with('message', [$e->getCode(), $e->getMessage()]);
        }
    }

    public function changeStatus(Request $request, $id){
        try {
            $this->search_list->changeStatus($id, $request->status);
            return;
        } catch (Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->search_list->deleteData($id);

            return to_route('search-list.index')->with('message', [200, 'Data Deleted']);
        } catch (Exception $e) {
            return to_route('search-list.index')->with('message', [$e->getCode(), $e->getMessage()]);
        }
    }
}
