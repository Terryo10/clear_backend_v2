<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = Service::all();
        return response()->json($services);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Not implemented in this example
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|string|max:255',
            'status' => 'boolean',
        ]);

        // Create a new service
        $service = Service::create($request->all());

        return response()->json($service, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        return response()->json($service);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        // Not implemented in this example
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        // Validate the request data
        $request->validate([
            'name' => 'string|max:255',
            'image' => 'string|max:255',
            'status' => 'boolean',
        ]);

        // Update the service
        $service->update($request->all());

        return response()->json($service);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        // Delete the service
        $service->delete();

        return response()->json(null, 204);
    }
}
