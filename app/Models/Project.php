<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $guarded;
    protected $with = ['images', 'scopeFiles',];


    public function images(){
        return $this->hasMany(ProjectImages::class);
    }

    public function scopeFiles(){
        return $this->hasMany(ProjectScopeFiles::class);
    }
}
