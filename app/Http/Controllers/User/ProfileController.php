<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{

    public function index()
    {
        $user = User::find(auth()->user()->id);
        return $this->jsonSuccess(200, "User Profile Retrieved", $user->profile, "profile");
    }

    public function updateProfilePicture(Request $request){
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            // Generate a unique file name
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('public/profile-photos', $fileName);
            $user = auth()->user();
            $user->profile_photo_path = $fileName;
            $user->save();

            return response()->json(['message' => 'Profile picture updated successfully']);
        }

        return response()->json(['message' => 'Failed to update profile picture'], 400);
    }

    public function updateUserPassword(Request $request){
        $user = User::find(auth()->user()->id);
        $request->validate([
            'password' => 'required|confirmed',
            'old_password' => 'required',
        ]);
        //check if old password is correct
        if (!Hash::check($request->old_password, $user->password)) {
            return $this->jsonError(422, "Old Password is incorrect", null, "user");
        }

        //check if new password is same as old password
        if (Hash::check($request->password, $user->password)) {
            return $this->jsonError(422, "New Password cannot be same as old password", null, "user");
        }
        $user->update([
            'password' => bcrypt($request->password),
        ]);
        return $this->jsonSuccess(200, "Password Updated", $user, "user");

    }

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
}
