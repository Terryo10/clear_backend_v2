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
        return $this->jsonSuccess(200, 'Request Successful', $frequencies, 'frequencies');
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
        $frequency = new Frequency();
        $frequency->name = $request->input('name');
        $frequency->save();

        return $this->jsonSuccess(200, 'Request Successful', $frequency, 'frequencies');
    }

    /**
     * Display the specified resource.
     */
    public function show(Frequency $frequency)
    {
        return $this->jsonSuccess(200, 'Request Successful', $frequency, 'frequencies');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Frequency $frequency)
    {

        return $this->jsonError(405, 'Not supported');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Frequency $frequency)
    {

        $frequency->update(["name" => $request->name]);

        return $this->jsonSuccess(200, 'Request Successful', $frequency, 'frequencies');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Frequency $frequency)
    {
        $frequency->delete();

        return $this->jsonSuccess(200, 'Data Deleted Successfully!!', [], 'message');
    }
}
