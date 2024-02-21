<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $guarded;
    protected $with = ['images', 'scopeFiles', 'user', 'service', 'offer', 'proposals', 'requestProposals', 'history', 'projectFeedBack', 'transaction', 'frequency'];

    public function images()
    {
        return $this->hasMany(ProjectImages::class);
    }

    public function scopeFiles()
    {
        return $this->hasMany(ProjectScopeFiles::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function history()
    {
        return $this->hasMany(ProjectHistory::class);
    }


    public function offer()
    {
        return $this->hasOne(ProjectOffers::class, 'project_id', 'id');
    }
    public function frequency()
    {
        return $this->hasOne(Frequency::class, 'id', 'frequency');
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class, 'project_id', 'id');
    }
    public function requestProposals()
    {
        return $this->hasMany(RequestProposal::class, 'project_id', 'id');
    }

    public function chat()
    {
        return $this->hasOne(GroupChat::class, 'project_id', 'id');
    }
    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
    ];

    public function projectFeedBack()
    {
        return $this->hasOne(ProjectRating::class, 'project_id', 'id');
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'project_id', 'id');
    }

    //create an arry of project status
    public static function projectStatus()
    {
        return [
            'request_for_bids_received' => 'Request for Bids Received',
            'sourcing_of_vendors' => 'Sourcing of Vendors In Progress',
            'first_bid_received' => 'First Bid Received',
            'second_bid_received' => 'Second Bid Received',
            'third_bid_received' => 'Third Bid Received',
            'fourth_bid_received' => 'Fourth Bid Received',
            'fifth_bid_received' => 'Fifth Bid Received',
            'bids_ready_for_approval' => 'Bids Ready for Approval',
            'project_being_scheduled' => 'Project Being Scheduled',
            'project_in_progress' => 'Project In Progress',
            'project_completed' => 'Project Completed',
        ];
    }

    //create helper function to get project status
    public function getProjectStatus()
    {
        return self::projectStatus()[$this->status];
    }

    //set project status
    public function setProjectStatus($status)
    {
        $this->status = $status;
        $this->save();
    }

    public function getStatus()
    {
        return self::projectStatus()[$this->status];
    }

    //create an arry of project colors
    public static function projectColors()
    {
        return [
            'request_for_bids_received' => 'yellow',
            'sourcing_of_vendors' => 'yellow',
            'first_bid_received' => 'green',
            'second_bid_received' => 'green',
            'third_bid_received' => 'green',
            'fourth_bid_received' => 'green',
            'fifth_bid_received' => 'green',
            'bids_ready_for_approval' => 'green',
            'project_being_scheduled' => 'purple',
            'project_in_progress' => 'purple',
            'project_completed' => 'purple',
        ];
    }


    //check status is first bid received and set to next status
    public function checkStatus()
    {
        if ($this->status == 'sourcing_of_vendors') {
            $this->setProjectStatus('first_bid_received');
        } elseif ($this->status == 'first_bid_received') {
            $this->setProjectStatus('second_bid_received');
        } elseif ($this->status == 'second_bid_received') {
            $this->setProjectStatus('third_bid_received');
        } elseif ($this->status == 'third_bid_received') {
            $this->setProjectStatus('fourth_bid_received');
        } elseif ($this->status == 'fourth_bid_received') {
            $this->setProjectStatus('fifth_bid_received');
        }
    }
}
