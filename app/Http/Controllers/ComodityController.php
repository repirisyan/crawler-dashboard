<?php

namespace App\Http\Controllers;

use App\Http\Requests\ComodityRequest;
use App\Models\Comodity;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ComodityController extends Controller
{
    public $comodity;

    public function __construct()
    {
        $this->comodity = new Comodity();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Inertia::render('Comodity', [
            'comodities' => $this->comodity->getComodities($request->search),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ComodityRequest $request)
    {
        try {
            $this->comodity->storeData($request->all());

            return to_route('comodity.index')->with('message', [200, 'Data Saved']);
        } catch (Exception $e) {
            return to_route('comodity.index')->with('message', [$e->getCode(), $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return response()->json($this->comodity->getComodity($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ComodityRequest $request, string $id)
    {
        try {
            $this->comodity->updateData($id, $request->all());

            return to_route('comodity.index')->with('message', [200, 'Data Updated']);
        } catch (Exception $e) {
            return to_route('comodity.index')->with('message', [$e->getCode(), $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->comodity->destroyData($id);

            return to_route('comodity.index')->with('message', [200, 'Data Deleted']);
        } catch (Exception $e) {
            return to_route('comodity.index')->with('message', [$e->getCode(), $e->getMessage()]);
        }
    }
}
