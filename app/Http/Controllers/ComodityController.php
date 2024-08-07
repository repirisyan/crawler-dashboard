<?php

namespace App\Http\Controllers;

use App\Http\Requests\ComodityRequest;
use App\Imports\ComoditiesImport;
use App\Jobs\NotifyUserOfCompletedImport;
use App\Models\Comodity;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class ComodityController extends Controller
{
    public $comodity;

    public function __construct()
    {
        $this->comodity = new Comodity;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Inertia::render('Comodity/Index', [
            'comodities' => $this->comodity->getComodities($request->all()),
            'params' => $request->all(),
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

    /**
     * Import Comodity Data from Excel.
     */
    public function import(Request $request)
    {
        try {
            // Check if the file is uploaded
            if (! $request->hasFile('file')) {
                throw new Exception('No Category File Uploaded', 400);
            }

            // Check if the file is of correct type
            if ($request->file('file')->getClientOriginalExtension() != 'xlsx') {
                throw new Exception('Category File Incorrect', 400);
            }

            $message = 'Category Import Completed';
            $user_id = Auth::user()->id;
            Excel::import(new ComoditiesImport($user_id), $request->file('file'))->chain([
                new NotifyUserOfCompletedImport($user_id, $message),
            ]);

            return to_route('comodity.index')->with('message', [200, 'File Uploaded']);
        } catch (Exception $e) {
            return to_route('comodity.index')->with('message', [$e->getCode(), $e->getMessage()]);
        }
    }
}
