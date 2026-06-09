<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ProfileController extends Controller
{
    public function showProfile()
    {
        return response()->file(public_path('profile.html'));
    }

    public function getProfile()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => false], 401);
        }

        return response()->json([
            'status' => true,
            'user'   => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'balance'    => $user->balance ?? 0,
                'created_at' => $user->created_at->format('d M Y'),
            ]
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => false], 401);
        }

        $request->validate([
            'name' => 'required|string|min:3',
        ]);

        $user->name = $request->name;
        $user->save();

        return response()->json([
            'status'  => true,
            'message' => 'Profil berhasil diupdate!'
        ]);
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => false], 401);
        }

        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|min:6',
        ]);

        if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'status'  => false,
                'message' => 'Password lama salah'
            ], 400);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'status'  => true,
            'message' => 'Password berhasil diubah!'
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status'  => true,
            'message' => 'Logout berhasil'
        ]);
    }

    public function deleteAccount()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => false], 401);
        }

        Auth::logout();
        User::destroy($user->id);

        return response()->json([
            'status'  => true,
            'message' => 'Akun berhasil dihapus'
        ]);
    }
}