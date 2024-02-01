<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectOffers extends Model
{
    use HasFactory;

    protected $guarded;

    protected $with = ['options', 'selectedOption',];


    public function options()
    {
        return $this->hasMany(OfferOptions::class, 'offer_id', 'id');
    }

    public function project(){
        return $this->belongsTo(Project::class, 'project_id', 'id');
    }

    //get selected option
    public function selectedOption()
    {
        return $this->hasOne(OfferOptions::class, 'id', 'selected_option');
    }
    protected $casts = [
        'created_at' => 'datetime:Y-m-d'
    ];
}
