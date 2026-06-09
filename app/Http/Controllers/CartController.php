<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Factory;

class CartController extends Controller
{
    private function getFirebaseDb()
    {
        return (new Factory)
            ->withServiceAccount(config('firebase.credentials.file'))
            ->createDatabase();
    }

    public function getCart(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Maaf, anda belum login'], 401);
        }

        $db   = $this->getFirebaseDb();
        $cart = $db->getReference('carts/' . $user->id)->getValue();

        return response()->json([
            'status' => true, // fix: boolean bukan string
            'data'   => $cart ? array_values((array) $cart) : []
        ]);
    }

    public function addToCart(Request $request)
    {
        $user = Auth::user(); // fix: $user tidak didefinisikan sebelumnya
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Maaf, Anda belum login'], 401);
        }

        $request->validate([
            'game'    => 'required|string',
            'product' => 'required|string',
            'price'   => 'required|string',
        ]);

        $db   = $this->getFirebaseDb();
        $ref  = $db->getReference('carts/' . $user->id);
        $cart = $ref->getValue() ?? [];

        $found = false;
        foreach ($cart as $key => $item) {
            if ($item['product'] === $request->product) {
                $ref->getChild($key . '/qty')->set($item['qty'] + 1);
                $found = true;
                break;
            }
        }

        if (!$found) {
            $ref->push([
                'game'    => $request->game,
                'product' => $request->product,
                'price'   => $request->price,
                'qty'     => 1,
            ]);
        }

        $updatedCart = $db->getReference('carts/' . $user->id)->getValue();

        return response()->json([
            'status'  => true,
            'message' => 'Berhasil ditambahkan ke keranjang',
            'data'    => $updatedCart ? array_values((array) $updatedCart) : []
        ]);
    }

    public function updateQty(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Belum login'], 401);
        }

        $db  = $this->getFirebaseDb();
        $ref = $db->getReference('carts/' . $user->id . '/' . $request->key);

        if ($request->qty <= 0) {
            $ref->remove();
        } else {
            $ref->update(['qty' => $request->qty]);
        }

        return response()->json(['status' => true]);
    }

    public function removeItem(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Belum login'], 401);
        }

        $db = $this->getFirebaseDb();
        $db->getReference('carts/' . $user->id . '/' . $request->key)->remove();

        return response()->json(['status' => true, 'message' => 'Item dihapus']);
    }

    public function checkout(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Belum login'], 401);
        }

        $db   = $this->getFirebaseDb();
        $cart = $db->getReference('carts/' . $user->id)->getValue();

        if (!$cart) {
            return response()->json(['status' => false, 'message' => 'Keranjang kosong'], 400);
        }

        $total = 0;
        foreach ($cart as $item) {
            $price  = (int) preg_replace('/[^0-9]/', '', $item['price']);
            $total += $price * $item['qty'];
        }

        if ($user->balance < $total) {
            return response()->json([
                'status'  => false,
                'message' => 'Saldo tidak cukup. Saldo kamu: Rp ' . number_format($user->balance, 0, ',', '.')
            ], 400);
        }

        $user->balance -= $total;
        $user->save();

        $orders = [];
        foreach ($cart as $item) {
            $price    = (int) preg_replace('/[^0-9]/', '', $item['price']);
            $orders[] = Order::create([
                'game'    => $item['game'],
                'product' => $item['product'],
                'price'   => $price * $item['qty'],
                'status'  => 'paid',
            ]);
        }

        $db->getReference('carts/' . $user->id)->remove();

        return response()->json([
            'status'  => true,
            'message' => 'Checkout berhasil!',
            'orders'  => $orders,
            'balance' => $user->balance
        ]);
    }

    public function clearCart()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Belum login'], 401);
        }

        $db = $this->getFirebaseDb();
        $db->getReference('carts/' . $user->id)->remove();

        return response()->json(['status' => true, 'message' => 'Keranjang dikosongkan']);
    }
}

