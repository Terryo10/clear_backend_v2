<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
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
            if ($request->device_token) {
                $user->update(["fcm_token" => $request->device_token]);
            }

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

    public function forgotPassword(Request $request)
    {

        $request->validate([
            'email' => 'required|email|exists:users',
        ]);

        $token = Str::random(64);

        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);

        // dd($token);

        Mail::send('emails.forgotPassword', ['token' => $token, 'email' => $request->email], function ($message) use ($request) {
            $message->to($request->email);
            $message->subject('Reset Password');
        });
        $email = $request->input('email');

        return response()->json(['status' => 200, 'success' => true, "message" => "Reset password link sent to $email . Follow the link to reset your password and login again"]);
    }
    public function addProject(Request $request)
    {

        $token = Str::random(64);

        DB::table('vendor_project_submits')->insert([
            'company_name' => $request->company_name,
            'company_address' => $request->company_address,
            'company_contact' => $request->company_contact,
            'phone_number' => $request->phone_number,
            'email_address' => $request->email_address,
            'website' => $request->website,
            'company_business_entity' => $request->company_business_entity,
            'company_establishment_date' => $request->company_establishment_date,
            'brief_description_of_your_company' => $request->brief_description_of_your_company,
            'service_coverage_area' => $request->service_coverage_area,
            'number_of_personnel_on_staff' => $request->number_of_personnel_on_staff,
            'company_licences_certifications_or_awards' => $request->company_licences_certifications_or_awards,
            'types_of_commercial_properties_currently_served' => $request->types_of_commercial_properties_currently_served,
            'commercial_building_services_offered' => $request->commercial_building_services_offered,
            'how_did_you_hear_about_us' => $request->how_did_you_hear_about_us,
            'does_your_company_have_general_liability_insurance' => $request->does_your_company_have_general_liability_insurance,
            'does_your_company_have_workers_compensation_insurance' => $request->does_your_company_have_workers_compensation_insurance,
            'created_at' => Carbon::now()
        ]);

        // dd($token);

        Mail::send('emails.vendors', ['company_name' => $request->company_name, 'email_address' => $request->email_address], function ($message) use ($request) {
            $message->to("nash@clearbuildingsolutions.com");
            // $message->to("pikigene01@gmail.com");
            $message->subject('New Vendor Project Request');
        });
        $email = $request->input('email');

        return response()->json(['status' => 200, 'success' => true, "message" => "Your Project Have been send to ADMIN"]);
    }
    public function showResetPasswordForm($token)
    {
        return view('Auth/ResetPassword', ['token' => $token]);
    }
    public function submitResetPasswordForm(Request $request)
    {
        // $request->validate([
        //     'password' => 'required|string|min:6|confirmed',
        //     'password_confirmation' => 'required'
        // ]);

        $updatePassword = DB::table('password_resets')
            ->where([
                'token' => $request->email_token
            ])
            ->first();

        if (!$updatePassword) {
            return back()->withInput()->with('error', 'Invalid token!');
        }

        $user = User::where('email', $updatePassword->email)
            ->update(['password' => Hash::make($request->password)]);

        DB::table('password_resets')->where(['token' => $request->token])->delete();

        return redirect('/email/success')->with('message', 'Your password has been changed!');
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Successfully logged out',
        ]);
    }

    public function refreshUser()
    {
        $user = Auth::user();
        $token = $user->createToken('apiToken')->plainTextToken;
        return response()->json([
            'status' => 200,
            'user' => $user,
            'token' => $token,
            'message' => 'Refreshed Successfully ',
        ]);
    }
}
