<?php

namespace App\Http\Controllers;

use App\Interfaces\Proposals\RequestProposalRepoInterface;
use App\Models\ManagerChat;
use App\Models\Project;
use Illuminate\Http\Request;

class RequestProposalController extends Controller
{
    private $requestProposalRepoInterface;
    public function __construct(RequestProposalRepoInterface $requestProposalRepoInterface)
    {
        $this->requestProposalRepoInterface = $requestProposalRepoInterface;
    }
    public function index()
    {
        return $this->requestProposalRepoInterface->getRequestProposals(auth()->user()->id);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required',
            'contractors' => 'required',
            'message' => 'required'
        ]);
        $project = Project::find($data['project_id']);
        $project->setProjectStatus('sourcing_of_vendors');
        $this->requestProposalRepoInterface->createRequestProposals($data, $project->id);
        return $this->jsonSuccess(200, 'Request Proposal Created', null, 'data');
    }

    public function destroy(Request $request)
    {
        $this->requestProposalRepoInterface->removeContractor($request->id);
        return $this->jsonSuccess(200, 'Contractor removed from project', null, 'data');
    }
}
