<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }
    public function verifyOtp(Request $request)
    {
        // Validate OTPs and update user status

        return redirect()->route('signup.form')->with('success', 'Account verified successfully');
    }
    public function submitForm(Request $request)
    {
        // Validate form data here

        $emailVerificationCode = rand(100000, 999999);
        $phoneVerificationCode = rand(100000, 999999);

        Mail::to($request->email)->send(new VerifyEmail($emailVerificationCode));
        // Use a similar approach for sending SMS with phoneVerificationCode

        // Save user data and verification codes to the database

        return redirect()->route('signup.form')->with('success', 'Verification codes sent successfully');
    }
}
