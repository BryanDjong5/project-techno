<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\PasswordOtp;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    public function showForgot()
    {
        return response()->file(public_path('forgot-password.html'));
    }

    public function sendOtp(Request $request)
    {
        $email = $request->email;
        $user  = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'Email tidak terdaftar'
            ], 404);
        }

        // Generate OTP 6 digit
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Hapus OTP lama
        PasswordOtp::where('email', $email)->delete();

        // Simpan OTP baru
        PasswordOtp::create([
            'email'      => $email,
            'otp'        => $otp,
            'expires_at' => Carbon::now()->addMinutes(2)
        ]);

        Mail::raw("Kode OTP reset password Gem Station kamu: {$otp}\n\nOtp hanya berlaku selama 2 menit.", function ($msg) use ($email, $otp) {
            $msg->to($email)
                ->subject('Kode OTP Reset Password - Gem Station');
        });

        return response()->json([
            'status'  => true,
            'message' => 'OTP berhasil dikirim ke email'
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $record = PasswordOtp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->first();

        if (!$record) {
            return response()->json([
                'status'  => false,
                'message' => 'Kode OTP salah'
            ], 400);
        }

        if (Carbon::now()->isAfter($record->expires_at)) {
            return response()->json([
                'status'  => false,
                'message' => 'Kode OTP sudah kadaluarsa'
            ], 400);
        }

        return response()->json([
            'status'  => true,
            'message' => 'OTP valid'
        ]);
    }

    public function resetPassword(Request $request)
    {
        $record = PasswordOtp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->first();

        if (!$record) {
            return response()->json([
                'status'  => false,
                'message' => 'OTP tidak valid'
            ], 400);
        }

        if (Carbon::now()->isAfter($record->expires_at)) {
            return response()->json([
                'status'  => false,
                'message' => 'OTP sudah kadaluarsa'
            ], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status'  => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        PasswordOtp::where('email', $request->email)->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Password berhasil direset'
        ]);
    }
}