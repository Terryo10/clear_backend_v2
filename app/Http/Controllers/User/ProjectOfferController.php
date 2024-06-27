<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectOfferResource;
use App\Http\Resources\ProjectResource;
use App\Interfaces\Images\ImageRepoInterface;
use App\Interfaces\Offers\OfferRepoInterface;
use App\Models\OfferOptions;
use App\Models\Project;
use App\Models\ProjectOffers;
// use Barryvdh\DomPDF\PDF;
// use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Http\Request;

class ProjectOfferController extends Controller
{
    public $imageRepoInterface;
    public $offerRepo;

    public function __construct(ImageRepoInterface $imageRepoInterface, OfferRepoInterface $offerRepo)
    {
        $this->imageRepoInterface = $imageRepoInterface;
        $this->offerRepo = $offerRepo;
    }

    //create functiom to store offer
    public function store(Request $request)
    {
        $offerData = $request->validate([
            'project_id' => 'required',
        ]);
        $this->offerRepo->create($offerData, $request->options);
        return $this->jsonSuccess(200, "Offer Created", null, "offer");
    }

    //create function to update offer
    public function update(Request $request, ProjectOffers $offer)
    {
        $offerData = $request->validate([
            'project_id' => 'required',
        ]);
        $this->offerRepo->update($offerData, $request->options);
        return $this->jsonSuccess(200, "Offer Updated", null, "offer");
    }

    //create function to delete offer
    public function delete($id)
    {
        $offer =  OfferOptions::findOrfail($id);
        $offer->delete();
        return $this->jsonSuccess(200, "Offer Deleted", null, "offer");
    }

    //create function to show offer
    public function show(ProjectOffers $offer, Request $request)
    {
        $offer = ProjectOffers::find($request->id);
        return $this->jsonSuccess(200, "Offer Retrieved", new ProjectOfferResource($offer), "offer");
    }

    //create function to accept offer
    public function accept(Request $request)
    {
        $data = $request->validate([
            'id' => 'required',
            'manager_signature' => 'required',
        ]);

        $this->offerRepo->acceptOffer($request->id, $data);
        return $this->jsonSuccess(200, "Offer Accepted", null, "offer");
    }

    //create function to sign offer
    public function sign(ProjectOffers $offer, Request $request)
    {
        $data = $request->validate([
            'id' => 'required',
            'selected_option' => 'required|numeric',
            'signature' => 'required|file',
        ]);

        $data['signature'] = $request->file('signature');
        $this->offerRepo->sign($request->id, $data);
        return $this->jsonSuccess(200, "Signed Off", null, "offer");
    }
    public function signClient(ProjectOffers $offer, Request $request)
    {
        $data = $request->validate([
            'id' => 'required',
            'selected_option' => 'required|numeric',
            'signature' => 'required|file',
        ]);
        $data['signature'] = $request->file('signature');

        $this->offerRepo->sign($request->id, $data);
        return $this->jsonSuccess(200, "Signed Off", null, "offer");
    }

    //create function to download offer as pdf
    public function downloadOfferAsPdf(Request $request, $id, $selected_option)
    {
        // $data = $request->validate([
        //     'id' => 'required',
        //     'selected_option' => 'required|numeric',
        // ]);

        $offer = ProjectOffers::find($id);
        $option = OfferOptions::find($selected_option);

        $data = [
            'title' => $offer->project->title,
            'service' => $offer->project->service->name,
            'status' => $offer->project->status,
            'option' => $option->option_name ?? "",
            'cost' => $option->cost ?? "0",
            'start_date' => $option->start_date ?? "",
            'end_date' => $option->end_date ?? "",
            'contactor_name' => $option->contractor ?? null ? $option->contractor->first_name . " " . $option->contractor->last_name : "N/A",
            'contract_terms_conditions' => $option->contract_terms_conditions ?? "",
            'execution_plan' => $option->execution_plan ?? "",
            'scope_of_work' => $option->scope_of_work ?? "",
            'site_info' => $option->site_info ?? "",
        ];



        $pdf = \PDF::loadView('offer', $data);
        return $pdf->download($offer->project->title . $option->option_name . '.pdf');
    }

    public function filter(Request $request)
    {
        if ($request->status == "All") {
            $requests = Project::where('status', "Request Sent")->orWhere('status', 'Bids Ready for Approval')->orderBy('created_at', 'DESC')->paginate(20);
        } else {
            $requests = Project::where('status', $request->status)->orderBy('created_at', 'DESC')->paginate(20);
        }

        return ProjectResource::collection($requests);
    }
}
