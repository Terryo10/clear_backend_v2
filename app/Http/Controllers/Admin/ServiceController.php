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
        return response()->json(["services" => $services, "status" => 200, "message" => "data fetched successfully"]);
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
                'name' => 'required',
                'image*' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                // 'status' => 'boolean',
            ]
        );
        if ($validator->fails()) {
            return response()->json(['status' => 401, 'message' => "validation failed", "errors" => $validator->errors()]);
        } else {
            $images_new = "";
            $path = "";
            // if ($file = $request->file('image')) {
            //     $var = date_create();
            //     $date = date_format($var, 'Ymd');
            //     $imageName = $date . '_' . $file->getClientOriginalName();
            //     $file->move(public_path() . '/uploads/', $imageName);
            //     $url = '/uploads/' . $imageName;
            //     $images_new = $url;
            // }

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $path = $image->store('images'); // Store the image in the storage/app/images directory
            }
            $input = array(
                "name" => $request->name,
                "image" => $path,
                "status" => $request->status,
            );

            $service = Service::create($input);

            return $this->jsonSuccess(200, 'Request Successful', $service, 'service');
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
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'string|max:255',
        ]);
        $service = Service::findOrfail($request->input('id'));

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $images_new = $image->store('images'); // Store the image in the storage/app/images directory
        } else {
            $images_new = $service->image; // Preserve existing image if no new image is uploaded
        }

        $inputs = [
            'image' => $images_new,
            'status' => $request->status,
            'name' => $request->name
        ];

        $service->update($inputs);
        return $this->jsonSuccess(200, 'Request Successful', $service, 'service');
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
