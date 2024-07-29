<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupervisionListRequest;
use App\Models\SupervisionList;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SupervisionListController extends Controller
{
    public $supervision;

    public function __construct()
    {
        $this->supervision = new SupervisionList();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('SupervisionList', [
            'supervisions' => $this->supervision->getAllData(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SupervisionListRequest $request)
    {
        try {
            $this->supervision->storeData($request->all());

            return to_route('supervision-list.index')->with('message', [200, 'Data Saved']);
        } catch (Exception $e) {
            return to_route('supervision-list.index')->with('message', [$e->getCode(), $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return response()->json($this->supervision->getData($id));
    }

    public function changeStatus(Request $request, $id){
        try {
            $this->supervision->changeStatus($id, $request->status);
            return;
        } catch (Exception $e) {
            return response()->json($e->getMessage());
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(SupervisionListRequest $request, string $id)
    {
        try {
            $this->supervision->updateData($id, $request->all());

            return to_route('supervision-list.index')->with('message', [200, 'Data Updated']);
        } catch (Exception $e) {
            return to_route('supervision-list.index')->with('message', [$e->getCode(), $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->supervision->destroy($id);

            return to_route('supervision-list.index')->with('message', [200, 'Data Deleted']);
        } catch (Exception $e) {
            return to_route('supervision-list.index')->with('message', [$e->getCode(), $e->getMessage()]);
        }
    }
}
