<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KeyFactors;
use Illuminate\Http\Request;

class KeyFactorsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $keyFactors = KeyFactors::all();
        return $this->jsonSuccess(200, 'Request Successful', $keyFactors, 'key_factors');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $keyFactors = new KeyFactors();
        $keyFactors->name = $request->input('name');
        $keyFactors->save();

        return $this->jsonSuccess(200, 'Request Successful', $keyFactors, 'keyFactor');
    }

    /**
     * Display the specified resource.
     */
    public function show(KeyFactors $keyFactors)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(KeyFactors $keyFactors)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,$id)
    {
        $keyFactors = KeyFactors::findOrfail($id);
         $keyFactors->update(["name"=> $request->input('name') ]);
        return $this->jsonSuccess(200, 'Request Successful', $keyFactors, 'keyFactor');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $keyFactors = KeyFactors::find($id);
        $keyFactors->delete();

        return $this->jsonSuccess(200, 'Data Deleted Successfully!!','deleted','deleted');
    }
}
