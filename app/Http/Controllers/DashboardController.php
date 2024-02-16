<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use App\Models\ManagerChat;
use App\Models\Project;
use App\Models\RequestProposal;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function admin()
    {
        $clients = User::where('role', 'USER')->count();

        //count clients for that month
        $clientsForMonth = User::where('role', 'USER')->whereMonth('created_at', date('m'))->count();

        //count clients for that day
        $clientsForDay = User::where('role', 'USER')->whereDay('created_at', date('d'))->count();

        $requests = Project::where('status', '!=', 'project_in_progress')->count();

        //get last 5 requests
        $lastRequests = Project::orderBy('created_at', 'desc')->take(5)->get();

        $requestsForMonth = Project::where('status', '!=', 'project_in_progress')->whereMonth('created_at', date('m'))->count();
        $requestsForDay = Project::where('status', '!=', 'project_in_progress')->whereDay('created_at', date('d'))->count();

        $chatRequests = ManagerChat::where('accepted', false)->get();
        $managerChats = ManagerChat::where('accepted', true)->where('manager_id', auth()->user()->id)->get();

        $projects = Project::where('status', 'project_in_progress')->orWhere('status', 'project_completed')->count();

        //get total cosst projects for that month
        $projectMonthTotalCost = Project::where('status', 'project_in_progress')->orWhere('status', 'project_completed')->whereMonth('created_at', date('m'))->sum('budget');
        $projectDayTotalCost = Project::where('status', 'project_in_progress')->orWhere('status', 'project_completed')->whereMonth('created_at', date('d'))->sum('budget');

        // projectTotalCost for projects in progress with transaction status paid
        $projectTotalCost = Project::where('status', 'project_in_progress')->orWhere('status', 'project_completed')->whereHas('transaction', function ($query) {
            $query->where('status', 'paid');
        })->sum('budget');



        return $this->jsonSuccess(200, "Dashboard Data Retrieved", [
            'clients' => $clients,
            'clientsForMonth' => $clientsForMonth,
            'clientsForDay' => $clientsForDay,
            'requests' => $requests,
            'lastRequests' => $lastRequests,
            'requestsForMonth' => $requestsForMonth,
            'requestsForDay' => $requestsForDay,
            'chatRequests' => $chatRequests,
            'managerChats' => $managerChats,
            'projects' => $projects,
            'projectMonthTotalCost' => $projectMonthTotalCost,
            'projectDayTotalCost' => $projectDayTotalCost,
            'projectTotalCost' => $projectTotalCost,
        ], "dashboard");
    }

    public function user()
    {
        $requests = Project::where('status', '!=', 'project_in_progress')->where('user_id', auth()->user()->id)->count();

        $requestsForMonth = Project::where('status', '!=', 'project_in_progress')->where('user_id', auth()->user()->id)->whereMonth('created_at', date('m'))->count();
        $requetsForday = Project::where('status', '!=', 'project_in_progress')->where('user_id', auth()->user()->id)->whereDay('created_at', date('d'))->count();

        $chatRequests = ManagerChat::where('accepted', false)->get();
        $managerChats = ManagerChat::where('accepted', true)->where('manager_id', auth()->user()->id)->get();

        $projects = Project::where('status', 'project_in_progress')->orWhere('status', 'project_completed')->where('user_id', auth()->user()->id)->count();

        //get total cosst projects for that month
        $projectMonthTotalCost = Project::where('status', 'project_in_progress')->orWhere('status', 'project_completed')->where('user_id', auth()->user()->id)->whereMonth('created_at', date('m'))->sum('budget');
        $projectDayTotalCost = Project::where('status', 'project_in_progress')->orWhere('status', 'project_completed')->where('user_id', auth()->user()->id)->whereMonth('created_at', date('d'))->sum('budget');

        $projectTotalCost = Project::where('status', 'project_in_progress')->orWhere('status', 'project_completed')->where('user_id', auth()->user()->id)->whereHas('transaction', function ($query) {
            $query->where('status', 'paid');
        })->sum('budget');
        $projectGroupByMonth = Project::where('user_id', auth()->user()->id)->selectRaw('sum(budget) as total, MONTH(created_at) as month')->groupBy('month')->get();
        $lastRequests = Project::where('user_id', auth()->user()->id)->orderBy('created_at', 'desc')->take(5)->get();

        return $this->jsonSuccess(200, "Dashboard Data Retrieved", [
            'requests' => $requests,
            'requestsForMonth' => $requestsForMonth,
            'requestsForDay' => $requetsForday,
            'chatRequests' => $chatRequests,
            'managerChats' => $managerChats,
            'projects' => $projects,
            'projectMonthTotalCost' => $projectMonthTotalCost,
            'projectDayTotalCost' => $projectDayTotalCost,
            'projectTotalCost' => $projectTotalCost,
            'projectGroupByMonth' => $projectGroupByMonth,
            'lastRequests' => $lastRequests
        ], "dashboard");

    }

    public function contractor()
    {
            $requests = RequestProposal::where('contractor_id', auth()->user()->id)->count();

            $requestsForMonth = RequestProposal::where('contractor_id', auth()->user()->id)->whereMonth('created_at', date('m'))->count();
            $requestsForDay = RequestProposal::where('contractor_id', auth()->user()->id)->whereDay('created_at', date('d'))->count();

            $chatRequests = ManagerChat::where('accepted', false)->get();
            $managerChats = ManagerChat::where('accepted', true)->where('manager_id', auth()->user()->id)->get();

            $projects = Project::where(function ($query) {
                $query->where('status', 'project_in_progress')
                    ->orWhere('status', 'project_completed');
            })
                ->where('contractor_id', auth()->user()->id)
                ->count();

            $projectMonthTotalCost = Project::where(function ($query) {
                $query->where('status', 'project_in_progress')
                    ->orWhere('status', 'project_completed');
            })
                ->where('contractor_id', auth()->user()->id)
                ->whereMonth('created_at', date('m'))->sum('budget');
            $projectDayTotalCost = Project::where(function ($query) {
                $query->where('status', 'project_in_progress')
                    ->orWhere('status', 'project_completed');
            })
                ->where('contractor_id', auth()->user()->id)
                ->whereMonth('created_at', date('d'))->sum('budget');
            $projectTotalCost = Project::where(function ($query) {
                $query->where('status', 'project_in_progress')
                    ->orWhere('status', 'project_completed');
            })
                ->where('contractor_id', auth()->user()->id)
                ->whereHas('transaction', function ($query) {
                    $query->where('status', 'paid');
                })->sum('budget');
            //get total cosst projects for that month

            $projectGroupByMonth = Project::where('contractor_id', auth()->user()->id)->selectRaw('sum(budget) as total, MONTH(created_at) as month')->groupBy('month')->get();
            $lastRequests = Project::where('contractor_id', auth()->user()->id)->orderBy('created_at', 'desc')->take(5)->get();

           return $this->jsonSuccess(200, "Dashboard Data Retrieved", [
                'requests' => $requests,
                'requestsForMonth' => $requestsForMonth,
                'requestsForDay' => $requestsForDay,
                'chatRequests' => $chatRequests,
                'managerChats' => $managerChats,
                'projects' => $projects,
                'projectMonthTotalCost' => $projectMonthTotalCost,
                'projectDayTotalCost' => $projectDayTotalCost,
                'projectTotalCost' => $projectTotalCost,
                'projectGroupByMonth' => $projectGroupByMonth,
                'lastRequests' => $lastRequests
            ], "dashboard");

    }
}
