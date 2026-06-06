<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller {
    public function loginPengguna(Request $request){
    $request->validate([
        "email" => 'required|email',
        "password" => Hash::make($request->password)
    ]);

    $user = User::where('email', $request->email)->first();

    if(!$user){
        return response()->json([
            'message' => 'Email tidak sesuai'
        ], 404);
    }

    $cekPassword = Hash::check(
        $request->password,
        $user->password
    );

    if(!$cekPassword){
        return response()->json([
            'message' => 'Maaf, password salah'
        ]);
    }

    return response()->json([
        'message' => 'Login berhasil!'
    ]);
}

}

