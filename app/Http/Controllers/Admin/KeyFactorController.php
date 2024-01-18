<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KeyFactor;
use Illuminate\Http\Request;

class KeyFactorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $keyFactors = KeyFactor::all();
        return $this->jsonSuccess(200, 'Request Successful', $keyFactors, 'keyFactors');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return $this->jsonError(405, 'Not supported');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $keyFactor = new KeyFactor();
        $keyFactor->name = $request->input('name');
        $keyFactor->save();

        return $this->jsonSuccess(200, 'Request Successful', $keyFactor, 'keyFactors');
    }

    /**
     * Display the specified resource.
     */
    public function show(KeyFactor $keyFactor)
    {
        return $this->jsonSuccess(200, 'Request Successful', $keyFactor, 'keyFactor');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KeyFactor $keyFactor)
    {
        return response()->json(['message' => 'Not supported'], 405);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, KeyFactor $keyFactor)
    {

        $keyFactor->update(["name" => $request->name]);

        return $this->jsonSuccess(200, 'Request Updated Successful', $keyFactor, 'keyfactor');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KeyFactor $keyFactor)
    {
        $keyFactor->delete();

        return $this->jsonSuccess(200, 'Data Deleted Successfully!!', [], 'message');
    }
}
