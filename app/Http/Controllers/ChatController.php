<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

class ChatController extends Controller
{
    public function showChat()
    {
        return response()->file(public_path('chat.html'));
    }

    public function userInfo()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['status' => false], 401);
        }

        return response()->json([
            'status' => true,
            'user'   => [
                'id'   => $user->id,
                'name' => $user->name,
            ]
        ]);
    }

    public function send(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['status' => false], 401);
        }

        return response()->json(['status' => true]);
    }
}