<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $verificationCode;
    public function __construct($verificationCode)
    {
        $this->verificationCode = $verificationCode;
    }
    
    public function build()
    {
        return $this->view('emails.verify_email')->with(['code' => $this->verificationCode]);
    }
    
    
}
