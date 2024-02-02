<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class email_verification extends Model
{
    use HasFactory;
    protected $fillable = [
        'email', 'email_otp',
    ];

}
