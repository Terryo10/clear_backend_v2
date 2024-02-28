<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationController extends Controller
{
    public function emailVerify($user_id, Request $request)
    {
        if (!$request->hasValidSignature()) {
            return response()->json(["msg" => "Invalid/Expired url provided."], 401);
        }

        $user = User::findOrFail($user_id);

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return view('email.verificationSuccess');
    }

    public function fullfillEmailVerification($user_id, Request $request)
    {
        if (!$request->hasValidSignature()) {
            return redirect()->back();
        }

        $user = User::findOrFail($user_id);

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return redirect()->to('/email/success');
    }

    //create function to resend email verification
    public function resendEmailVerification(Request $request)
    {
        $user = User::where('email', Auth::user()->email)->first();

        $user->sendEmailVerificationNotification();
        return $this->jsonSuccess(200, "Email verification sent successfully", [], 'verificationLink');
    }

    public function verificationEmailSuccess()
    {
        return view('auth.verification-email-success');
    }
}
