<?php

namespace App\Interfaces\Proposals;

interface RequestProposalRepoInterface
{
    public function createRequestProposals($data, $project_id);

    public function removeContractor($id);

    //get all request proposal for a contractor
    public function getRequestProposals($contractor_id);

    public function createProposal(array $data);

    public function findProposal($project_id, $contractor_id);

    public function updateProposal(array $data);

    public function deleteProposal($id, $request_proposal_id);

}
