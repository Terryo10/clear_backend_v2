<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Frequency;
use App\Models\KeyFactors;
use Illuminate\Http\Request;

class KeyFactorController extends Controller
{
    public function getKeyFactors(){
        return $this->jsonSuccess(200, 'Request Successful', KeyFactors::all(), 'key_factor');

    }

    public function getFrequencies(){
        return $this->jsonSuccess(200, 'Request Successful', Frequency::all(), 'frequency');
    }
}
