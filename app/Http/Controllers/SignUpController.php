<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
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
        dd($request);
        // Validate email OTP
        $request->validate([
            'emailOtp' => 'required|digits:6',
        ]);

        // Fetch the user by email (you might want to add more checks based on your actual implementation)
        $user = User::where('email_otp', $request->emailOtp)->first();
        echo($user);
        if (!$user) {
            return response()->json(['error' => 'Invalid OTP'], 422);
        }

        // Mark email as verified
        $user->email_verified = true;
        $user->save();

        // Optionally, you can log in the user or perform other actions

        return response()->json(['message' => 'Email verified successfully']);
    }


    public function sendEmailOtp(Request $request)
    {
        // Validate email
        $request->validate([
            'email' => 'required|email',
        ]);

        // Generate OTP
        $emailOtp = mt_rand(100000, 999999);

        // Send OTP to the provided email (replace with your email sending logic)
        Mail::raw("Your OTP is: $emailOtp", function ($message) use ($request) {
            $message->to($request->email)->subject('Email OTP');
        });

        // You might want to store the OTP in the session or database for later verification

        return response()->json(['message' => 'Email OTP sent successfully']);
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

        // Generate OTPs
        $emailOtp = mt_rand(100000, 999999);
        $phoneOtp = mt_rand(100000, 999999);

        // Send OTPs to Mailtrap (replace with actual mail sending logic)
        $this->sendOtpEmail($request->email, $emailOtp);
        $this->sendOtpSms($request->phone, $phoneOtp);

        // Store user and OTP information in the database
        $user = User::create([
            'name' => $request->name,
            'country' => $request->country,
            'email' => $request->email,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'email_otp' => $emailOtp,
            'phone_otp' => $phoneOtp,
        ]);

        return redirect()->route('home', ['user' => $user->id]);
    }

    private function sendOtpEmail($email, $otp)
    {
        // Implement logic to send OTP to email (e.g., using Mailtrap)
        // For simplicity, we assume the mail has been sent.
    }

    private function sendOtpSms($phone, $otp)
    {
        // Implement logic to send OTP to phone (e.g., using external SMS service)
        // For simplicity, we assume the SMS has been sent.
    }
}
