<?php

namespace App\Http\Controllers;

use App\Http\Requests\KeywordRequest;
use App\Models\Comodity;
use App\Models\Keyword;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class KeywordController extends Controller
{
    public $keyword;

    public $comodity;

    public function __construct()
    {
        $this->keyword = new Keyword;
        $this->comodity = new Comodity;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Inertia::render('Keyword', [
            'keywords' => $this->keyword->getKeywords($request->all()),
            'comodities' => $this->comodity->getAllComodity(),
            'params' => $request->all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(KeywordRequest $request)
    {
        try {
            $this->keyword->storeData($request->all());

            return to_route('keyword.index')->with('message', [200, 'Data Saved']);
        } catch (Exception $e) {
            return to_route('keyword.index')->with('message', [$e->getCode(), $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            return response()->json($this->keyword->getKeyword($id));
        } catch (Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(KeywordRequest $request, string $id)
    {
        try {
            $this->keyword->updateData($request->all(), $id);

            return to_route('keyword.index')->with('message', [200, 'Data Updated']);
        } catch (Exception $e) {
            return to_route('keyword.index')->with('message', [$e->getCode(), $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->keyword->deleteData($id);

            return to_route('keyword.index')->with('message', [200, 'Data Deleted']);
        } catch (Exception $e) {
            return to_route('keyword.index')->with('message', [$e->getCode(), $e->getMessage()]);
        }
    }
}
