<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class loginController extends Controller
{


    public function __construct()
    {
    }


    public function login(Request $request)
    {

        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|max:191',
                'password' => 'required',

            ]
        );


        if ($validator->fails()) {
            return response()->json(['status' => 404, 'errors' => $validator->getMessageBag(), 'message' => 'validation error']);
        } else {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => 401,
                    'message' => 'Invalid Credentials',

                ]);
            } else {
                // $token = $user->createToken($user->email . '_Token')->plainTextToken;
                $token = $user->createToken('apiToken')->plainTextToken;
                return response()->json([
                    'status' => 200,
                    'username' => $user->lastname,
                    'user' => $user,
                    'token' => $token,
                    'message' => 'Logged in Successfully ',
                ]);
            }
        }
    }


    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Successfully logged out',
        ]);
    }
}
