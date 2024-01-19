<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

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
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:255',
                'image*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'status' => 'boolean',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['status' => 401, 'message' => "validation failes", "errors" => $validator->errors()]);
        } else {
            $images_new = "";
            if ($file = $request->file('image')) {
                $var = date_create();
                $date = date_format($var, 'Ymd');
                $imageName = $date . '_' . $file->getClientOriginalName();
                $file->move(public_path() . '/uploads/', $imageName);
                $url = 'public/uploads/' . $imageName;
                $images_new = $url;
            }
            $input = [
                "name" => $request->name,
                "image" => $images_new,
                "status" => $request->status,
            ];
            // Create a new service
            $service = Service::create($input);

            return response()->json(["status" => 200, "message" => "service created successfully"]);
        }
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
