<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractorRequest extends Model
{
    use HasFactory;
    protected $guarded;

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    public function message()
    {
        return $this->belongsTo(ManagerChatMessage::class);
    }

    public function contractor()
    {
        return $this->belongsTo(User::class, 'contractor_id', 'id');
    }
}
