<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function halamanBeli()
    {
        return response()->file(public_path('buy.html'));
    }

    public function buyNow(Request $request)
    {
        $user = Auth::user();

        $price = (int) preg_replace('/[^0-9]/', '', $request->price);

        if (!$user || $user->balance < $price) {
            return response()->json([
                'status' => false,
                'message' => 'Saldo tidak cukup'
            ], 400);
        }

        $user->balance -= $price;
        $user->save();

        $order = Order::create([
            'game' => $request->game,
            'product' => $request->product,
            'price' => $price,
            'status' => 'paid'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Pembelian berhasil',
            'data' => $order,
            'balance' => $user->balance
        ]);
    }
}