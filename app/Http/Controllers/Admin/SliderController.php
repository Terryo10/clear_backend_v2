<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\Images\ImageRepoInterface;
use App\Models\Slider;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    public $imageRepoInterface;

    public function __construct(ImageRepoInterface $imageRepoInterface)
    {
        $this->imageRepoInterface = $imageRepoInterface;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexAdmin()
    {
        return $this->jsonSuccess(200, "Sliders", Slider::all(), 'sliders');
    }

    public function index()
    {
        return $this->jsonSuccess(200, "Sliders", Slider::all(), 'sliders');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        //check if request have image


        $image_url = $this->imageRepoInterface->uploadImage($request->file('image'), 'sliders');

        //check if request has name
       $slider = Slider::create([
            'image_url' => $image_url,
            'name' => $request->name ? $request->name : '',
        ]);

        return $this->jsonSuccess(200, "Slider Created", $slider, 'sliders');


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Slider  $slider
     * @return \Illuminate\Http\Response
     */
    public function show(Slider $slider)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Slider  $slider
     * @return \Illuminate\Http\Response
     */
    public function edit(Slider $slider)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Slider  $slider
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Slider $slider)
    {
        //update image
        if ($request->hasFile('image')) {
            $image_url = $this->imageRepoInterface->uploadImage($request->file('image'), 'sliders');
            $slider->update([
                'image_url' => $image_url,
            ]);
        }

      return $this->jsonSuccess(200, "Slider Updated", $slider, 'slider');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Slider  $slider
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Slider $slider)
    {
        $slider->delete();
       return $this->jsonSuccess(200, "Slider Deleted", $slider, 'slider');
    }
}
