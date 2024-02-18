<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    protected $guarded;

    use HasFactory;
    protected $with = ['contractor'];

    public function contractor()
    {
        return $this->hasOne(User::class, 'id', 'contractor_id');
    }
}
