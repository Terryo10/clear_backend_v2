<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProjectHistoryResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectHistoryController extends Controller
{
    //create function to get latest project history
    public function getLatestHistory(Request $request)
    {
        $user  = Auth::user();
        return $this->jsonSuccess(200, "Latest History", ProjectHistoryResource::collection($user->project_history), "history");
    }
}
