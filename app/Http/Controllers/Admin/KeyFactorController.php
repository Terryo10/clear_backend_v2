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
        return response()->json($keyFactors);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return response()->json(['message' => 'Not supported'], 405);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $keyFactor = new KeyFactor();
        $keyFactor->name = $request->input('name');
        $keyFactor->save();

        return response()->json($keyFactor, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(KeyFactor $keyFactor)
    {
        return response()->json($keyFactor);
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
        $keyFactor->name = $request->input('name');
        $keyFactor->save();

        return response()->json($keyFactor);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(KeyFactor $keyFactor)
    {
        $keyFactor->delete();

        return response()->json(null, 204);
    }
}
