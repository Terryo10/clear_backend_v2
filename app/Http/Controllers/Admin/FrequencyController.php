<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Frequency;
use Illuminate\Http\Request;

class FrequencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $frequencies = Frequency::all();
        return response()->json($frequencies);
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
        $frequency = new Frequency();
        $frequency->name = $request->input('name');
        $frequency->save();

        return response()->json($frequency, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Frequency $frequency)
    {
        return response()->json($frequency);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Frequency $frequency)
    {
        return response()->json(['message' => 'Not supported'], 405);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Frequency $frequency)
    {
        $frequency->name = $request->input('name');
        $frequency->save();

        return response()->json($frequency);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Frequency $frequency)
    {
        $frequency->delete();

        return response()->json(null, 204);
    }
}
