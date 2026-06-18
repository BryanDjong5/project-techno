<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordOtp extends Model
{
    protected $table    = 'password_otp';
    protected $fillable = ['email', 'otp', 'expires_at'];
}