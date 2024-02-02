<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Twilio\Rest\Client;
use App\Models\email_verification;  
use Illuminate\Support\Facades\Mail;

class SignUpController extends Controller
{
    public function showForm()
    {
        // Provide country options as needed
        $countries = ['Pakistan', 'China', 'Iran'];

        return view('auth/register', compact('countries'));
    }

    public function verifyEmailOtp(Request $request)
    {
        // Validate email OTP
        $request->validate([
            'emailOtp' => 'required|digits:6',
        ]);

        // Fetch the user by email (you might want to add more checks based on your actual implementation)
        $user = email_verification::where('email_otp', $request->emailOtp)->first();
        if (!$user) {
            return response()->json(['error' => 'Invalid OTP'], 422);
        }

        // Optionally, you can log in the user or perform other actions

        return response()->json(['message' => 'Email verified successfully']);
    }


    public function sendEmailOtp(Request $request)
    {
        // dd($request);
        // Validate email
        $request->validate([
            'email' => 'required|email',
        ]);

        // Generate OTP
        $emailOtp = mt_rand(100000, 999999);
        $email_verification = email_verification::create([
            'email' => $request->email,
            'email_otp' => $emailOtp,
        ]);
        $email_verification->save();
        // Send OTP to the provided email (replace with your email sending logic)
        Mail::raw("Your OTP is: $emailOtp", function ($message) use ($request) {
            $message->to($request->email)->subject('Email OTP');
        });
        
        return response()->json(['message' => 'Email OTP sent successfully']);
    }

    public function sendPhoneOtp(Request $request)
    {
        // Validate phone
        $request->validate([
            'phone' => 'required|numeric|digits:10', // Update as needed
        ]);

        // Generate OTP
        $phoneOtp = mt_rand(100000, 999999);
        // Store OTP in the user's record (you may want to encrypt it or use a more secure approach)
        $userPhone = UserPhone::where('phone', $request->phone)->first();
        $userPhone = $request->phone;
        $userPhone->phone_otp = $phoneOtp;
        $userPhone->save();

        // Send OTP to the provided phone using Twilio
        $this->sendOtpSms($request->phone, $phoneOtp);

        return response()->json(['message' => "Phone OTP sent successfully. Your OTP is: $phoneOtp"]);
    }
    public function sendOtpSms($phone, $otp)
    {
        // Replace these values with your Twilio credentials
        $accountSid = 'your_twilio_account_sid';
        $authToken = 'your_twilio_auth_token';
        $twilioNumber = 'your_twilio_phone_number';

        $client = new Client($accountSid, $authToken);

        try {
            // Send OTP to the provided phone number
            $client->messages->create(
                $phone,
                [
                    'from' => $twilioNumber,
                    'body' => "Your OTP is: $otp",
                ]
            );
        } catch (\Exception $e) {
            // Handle exceptions, log errors, etc.
            // You may want to return a response indicating that the OTP sending failed
            // For simplicity, we're not handling errors here.
        }
    }

    public function signUp(Request $request)
    {
        // Validation rules
        $rules = [
            'name' => 'required',
            'country' => 'required',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|unique:users,phone',
            'gender' => 'required',
        ];

        $request->validate($rules);

        // Store user and OTP information in the database
        $user = User::create([
            'name' => $request->name,
            'country' => $request->country,
            'email' => $request->email,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'email_otp' => $request->emailOtp,
        ]);
        $user->save();
        return redirect()->back()->with('success','User Created successfully');
    }
}
