<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupervisionListRequest;
use App\Imports\SupervisionListImport;
use App\Models\SupervisionList;
use App\Jobs\NotifyUserOfCompletedImport;
use Maatwebsite\Excel\Facades\Excel;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    public function index(Request $request)
    {
        return Inertia::render('SupervisionList', [
            'supervisions' => $this->supervision->getSupervisionList($request->all()),
            'params' => $request->all()
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

    public function import(Request $request){
        try {
            // Check if the file is uploaded
            if (!$request->hasFile('file')) {
                throw new Exception("No Supervision List File Uploaded", 400);
            }

            // Check if the file is of correct type
            if ($request->file('file')->getClientOriginalExtension() != 'xlsx') {
                throw new Exception("Supervision List File Incorrect", 400);
            }
        
            $message = 'Supervision List Import Completed';
            $user_id = Auth::user()->id;
            Excel::import(new SupervisionListImport($user_id), $request->file('file'))->chain([
                new NotifyUserOfCompletedImport($user_id,$message),
            ]);
            return to_route('supervision-list.index')->with('message', [200, 'File Uploaded']);
        } catch (Exception $e) {
            return to_route('supervision-list.index')->with('message', [$e->getCode(), $e->getMessage()]);   
        }
    }
}
