<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;



class registerController extends Controller
{
    public function register(Request $request)
    {
        $user = Auth::user();

        // Get the currently authenticated user's ID...
        $id = Auth::id();
        $role = 0; //first registered user will be admin and we will change the role to be 1 if no users inside the db
        $users = User::get();
        if ($users->count() > 0) {
            $role = 0; //for normal users
        } else {
            $role = 1; //first user automatically admin
        }

        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|min:3',
                'email' => 'required|min:6',
                'password' => 'required|min:3',
                'confirm_password' => 'required|same:password',

            ]
        );


        if ($validator->fails()) {
            return response()->json(['status' => 401, 'message' => "Please fill all fields!!!"]);
        } else {
            $user = User::where('email', $request->email)->first();
            if ($user) {
                //user already exist
                return response()->json(['status' => 401, 'message' => 'User Already Exist']);
            } else {

                $input = array(
                    'name' => $request->name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'password' => bcrypt($request->password),
                );

                $user = User::create($input);

                $credentials = $request->only('email', 'password');
                $token = $user->createToken($user->email . '_Token')->plainTextToken;

                if (Auth::attempt($credentials)) {
                    // Authentication passed...
                    return response()->json(['status' => 200, 'message' => 'You have been registered successfully', 'token' => $token, 'role' => $user->role, 'username' => $user->name]);
                }
            }
        }
    }
}
