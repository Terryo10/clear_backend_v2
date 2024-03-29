<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfferOptions extends Model
{

    use HasFactory;
    protected $guarded;

    protected $with = ['contractor'];

    public function contractor()
    {
        return $this->belongsTo(User::class, 'contractor_id', 'id');
    }

}
