<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectHistory extends Model
{

    use HasFactory;

    protected $guarded;

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    //create date casts to this formart example November 20, 2020
    protected $casts = [
        'created_at' => 'datetime:F j, Y',
        'updated_at' => 'datetime:F j, Y'
    ];
}
