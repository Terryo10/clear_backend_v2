<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function updateProfilePicture(Request $request){
        $request->validate([
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            // Generate a unique file name
            $fileName = time() . '_' . $file->getClientOriginalName();
            // Store the file in the public/profile-photos directory
            $file->storeAs('public/profile-photos', $fileName);

            // Update the user's profile_photo_path field in the database

            // Assuming you have a logged-in user
            $user = auth()->user();
            $user->profile_photo_path = $fileName;
            $user->save();

            return response()->json(['message' => 'Profile picture updated successfully']);
        }

        return response()->json(['message' => 'Failed to update profile picture'], 400);
    }

    public function updateUserPassword(Request $request){
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8',
        ]);

        $user = auth()->user();

        // Check if the current password matches
        if (!Hash::check($request->input('current_password'), $user->password)) {
            return response()->json(['message' => 'The current password is incorrect'], 400);
        }

        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        return response()->json(['message' => 'Password updated successfully']);
    }
}
