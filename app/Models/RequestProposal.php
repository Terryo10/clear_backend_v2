<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestProposal extends Model
{
    protected $guarded;

    use HasFactory;

    public function contractor()
    {
        return $this->belongsTo(User::class, 'contractor_id');
    }

}
