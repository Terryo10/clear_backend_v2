<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;



class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'first_name' => 'required|min:3',
                'email' => 'required|min:6',
                'password' => 'required|min:3',
                'confirm_password' => 'required|same:password',
                'role'=>'required',
            ]
        );

        if ($validator->fails()) {
            return response()->json(['status' => 401, 'message' => $validator->errors()]);
        } else {
            $user = User::where('email', $request->email)->first();
            if ($user) {
                return response()->json(['status' => 401, 'message' => 'Account is already registered']);
            } else {

                $input = array(
                    'first_name' => $request->input('first_name'),
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'password' => bcrypt($request->password),
                    'role'=> $request->role
                );

                $user = User::create($input);
                $credentials = $request->only('email', 'password');
                $token = $user->createToken($user->email . '_Token')->plainTextToken;

                if (Auth::attempt($credentials)) {
                    return response()->json([
                        'status' => 200,
                        'message' => 'You have been registered successfully',
                        'token' => $token,
                        'role' => $user->role,
                        'user' => $user,
                        ]);
                }
            }
        }
    }
}
