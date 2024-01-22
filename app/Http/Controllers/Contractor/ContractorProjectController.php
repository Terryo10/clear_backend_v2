<?php

namespace App\Http\Controllers\Contractor;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ContractorProjectController extends Controller
{

    public function contactorProjects()
    {
        return auth()->user();
        $projects = Project::where('contractor_id', auth()->user()->id)
            ->orderBy('created_at', 'DESC')
            ->paginate(20);

        return $this->jsonSuccess(200, 'Proposal Requests Contractor Removed', $projects, 'projects');
    }

}
