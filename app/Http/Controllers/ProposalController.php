<?php

namespace App\Http\Controllers;

use App\Interfaces\Proposals\RequestProposalRepoInterface;
use App\Models\Proposal;
use Illuminate\Http\Request;

class ProposalController extends Controller
{
    private $requestProposalRepoInterface;

    public function __construct(RequestProposalRepoInterface $requestProposalRepoInterface)
    {
        $this->requestProposalRepoInterface = $requestProposalRepoInterface;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $data = $request->validate([
            'project_id' => 'required',
            'cost' => 'required',
            'description' => 'nullable',
            'start_date' => 'required',
            'end_date' => 'required',
            'execution_plan' => 'required',
            'scope_of_work' => 'required'
        ]);
        if ($this->requestProposalRepoInterface->findProposal($data['project_id'], auth()->user()->id)) {
            return  $this->jsonError(400, 'You have already sent a proposal for this project', null, 'proposal');
        }
        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment');
        }
        $this->requestProposalRepoInterface->createProposal($data);
        return $this->jsonSuccess(200, 'Proposal Sent Successfully', null, 'proposal');


    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'proposal_id',
            'cost',
            'description',
            'start_date',
            'end_date',
            'scope_of_work',
            'execution_plan',
        ]);
        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment');
        }
        $this->requestProposalRepoInterface->updateProposal($data);
        return $this->jsonSuccess(200, 'Proposal Updated Successfully', null, 'proposal');

    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Proposal  $proposal
     * @return \Illuminate\Http\Response
     */
    public function show(Proposal $proposal)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Proposal  $proposal
     * @return \Illuminate\Http\Response
     */
    public function edit(Proposal $proposal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Proposal  $proposal
     * @return \Illuminate\Http\Response
     */

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Proposal  $proposal
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $this->requestProposalRepoInterface->deleteProposal($request->id, $request->request_proposal_id);
       return $this->jsonSuccess(200,'Proposal Deleted Successfully',null, 'proposal
       ');
    }
}
