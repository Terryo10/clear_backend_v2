<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::paginate(20);

        return $this->jsonSuccess(200, 'Request Successful', $users, 'users');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:4',
        ]);

        $user = User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'role' => $request->input('role'),
            'password' => Hash::make($request->input('password')),
        ]);

        return $this->jsonSuccess(200, 'Request Successful', $user, 'user');
    }

    public function getUser(Request $request)
    {
        $user = User::where("email", Auth::user()->email)->first();
        return $this->jsonSuccess(200, 'Request Successful', $user, 'user');
    }
    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return $this->jsonSuccess(200, 'Request Successful', $user, 'user');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:4',
        ]);

        $user->update([
            'password' => Hash::make($request->input('password')),
        ]);

        return $this->jsonSuccess(200, 'Request Successful', $user, 'user');
    }
    public function updateEdit(Request $request)
    {
        $user = User::findOrFail($request->id);
        $request->validate([
            'password' => 'required|string|min:4',
        ]);
        if ($request->input('status')) {
            $user->update([
                'status' => Hash::make($request->input('status')),
            ]);
        }
        $user->update([
            'password' => Hash::make($request->input('password')),
        ]);

        return $this->jsonSuccess(200, 'User Updated Successful', $user, 'user');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    public function updateStatus(Request $request, User $user)
    {
        //update user status
        $data = $request->validate([
            'status' => 'required|string',
        ]);
        $user->status = $data['status'];
        $user->save();
        return $this->jsonSuccess(200, 'Request Successful', $user, 'user');
    }

    public function getContractors()
    {
        $contractors = User::where('role', '=', 'CONTRACTOR')->get();
        return $this->jsonSuccess(200, 'Request Successful', $contractors, 'contractors');
    }

    public function updateProfile(Request $request)
    {
        //write code to update profile

    }
}
