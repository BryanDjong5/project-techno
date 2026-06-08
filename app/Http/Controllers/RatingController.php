<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rating;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function showRating($orderId)
    {
        $order = Order::findOrFail($orderId);
        return view('rating', compact('order'));
    }

    public function sendRating(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Maaf, Anda belum login! Silahkan login dahulu'], 401);
        }

        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'rating'   => 'required|integer|min:1|max:5',
            'ulasan'   => 'nullable|string|max:500',
        ]);

        $existing = Rating::where('order_id', $request->order_id)
                          ->where('user_id', $user->id)
                          ->first();

        if ($existing) {
            return response()->json(['status' => false, 'message' => 'Sudah pernah memberi ulasan'], 400);
        }

        $rating = Rating::create([
            'order_id' => $request->order_id,
            'user_id'  => $user->id,
            'rating'   => $request->rating,
            'ulasan'   => $request->ulasan,
        ]);

        return response()->json(['status' => true, 'message' => 'Ulasan berhasil dikirim', 'data' => $rating]);
    }
}