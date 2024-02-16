<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserProfileController extends Controller
{
    //create function to get user profile
    public function index()
    {
        $user = User::find(auth()->user()->id);
        return $this->jsonSuccess(200, "User Profile Retrieved", $user->profile, "profile");
    }

    public function userProfile()
    {
        $user = User::find(auth()->user()->id);
        return $this->jsonSuccess(200, 'User Profile fetched successfully', [
            'user' => $user,
        ], 'profile');
    }

    //create function to edit profile details
    public function update(Request $request)
    {
        $user = User::find($request->user_id);
        $userData = $request->only([
            'first_name',
            'last_name',
            'company_name',
        ]);

        $user->update([
            'first_name' => $userData['first_name'],
            'last_name' => $userData['last_name'],
            'company_building_name' => $userData['company_name'],
        ]);

        $profileData = $request->only([
            'gender',
            'home_city',
            'home_country',
            'home_state',
            'home_zip_code',
            'home_address',
            'home_phone',
            'work_title',
            'company_name',
            'work_phone',
            'work_address',
            'work_city',
            'work_state',
            'work_zip_code',
            'about',
            'color'
        ]);
        if (!$user->profile) {
            $user->profile()->create($profileData);
        } else {
            $user->profile()->update($profileData);
        }
        return $this->jsonSuccess(200, "Profile Updated", $user, "user");
    }


    //create function to update password
    public function updatePassword(Request $request)
    {
        $user = User::find(auth()->user()->id);
        $request->validate([
            'password' => 'required|confirmed',
            'old_password' => 'required',

        ]);
        //check if old password is correct
        if (!Hash::check($request->old_password, $user->password)) {
            return redirect()->back()->with('error', 'Old Password is incorrect');
        }

        //check if new password is same as old password
        if (Hash::check($request->password, $user->password)) {
            return redirect()->back()->with('error', 'New Password cannot be same as old password');
        }
        $user->update([
            'password' => bcrypt($request->password),
        ]);
        return redirect()->back()->with('success', 'Password Updated');
    }

    public function updatePasswordApi(Request $request)
    {
        $user = User::find(auth()->user()->id);
        $request->validate([
            'password' => 'required|confirmed',
            'old_password' => 'required',
        ]);
        //check if old password is correct
        if (!Hash::check($request->old_password, $user->password)) {
            return $this->jsonError(422, "Old Password is incorrect");
        }

        //check if new password is same as old password
        if (Hash::check($request->password, $user->password)) {
            return $this->jsonError(422, "New Password cannot be same as old password");
        }
        $user->update([
            'password' => bcrypt($request->password),
        ]);
        return $this->jsonSuccess(200, "Password Updated", $user, "user");
    }


    public function updateEmailApi(Request $request)
    {
        $user = User::find(auth()->user()->id);
        $request->validate([
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);
        //check  if new email is same as old email
        if ($request->email == $user->email) {
            return $this->jsonError(422, "New Email cannot be same as old email");
        }

        $user->update([
            'email' => $request->email,
        ]);
        return $this->jsonSuccess(200, "Email Updated", $user, "user");
    }


    public function updateProfilePictureApi(Request $request)
    {
        $user = User::find(auth()->user()->id);
        $request->validate([
            'profile_photo_path' => 'required',
        ]);
        $imageName = time() . '.' . $request->profile_photo_path->extension();
        $request->profile_photo_path->move(public_path('profile'), $imageName);
        $user->update([
            'profile_photo_path' => $imageName,
        ]);
        return $this->jsonSuccess(200, "Profile Picture Updated", $user, "user");
    }
}
