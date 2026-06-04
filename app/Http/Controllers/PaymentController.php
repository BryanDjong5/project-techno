<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Services\FirebaseService;        // ← FIREBASE: tambah ini
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)  // ← FIREBASE: inject
    {
        $this->firebase = $firebase;
    }

    public function getMetodePembayaran()
    {
        $metode = PaymentMethod::where('aktif', true)->get(); 

        return response()->json([
            'status' => 'sukses',
            'data'   => $metode,
        ]);
    }

    public function showWaiting($orderId, $metodekode)
    {
        $order  = Order::findOrFail($orderId);              
        $metode = PaymentMethod::where('kode', $metodekode)
                               ->where('aktif', true)
                               ->firstOrFail();              
        $firebasePath = 'orders/' . $orderId . '/status';  

        return response()->json([
            'status' => 'sukses',
            'data'   => [
                'order_id'      => $order->id,
                'status'        => $order->status,           
                'jumlah'        => $order->total_harga,      
                'firebase_path' => $firebasePath,            
                'metode'        => [
                    'nama'        => $metode->nama,         
                    'no_rekening' => $metode->no_rekening,   
                    'atas_nama'   => $metode->atas_nama,     
                    'logo'        => $metode->logo,          
                ],
            ],
        ]);
    }


    public function konfirmasiTransfer(Request $request)
    {
        $request->validate([
            'order_id'          => 'required|exists:orders,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
        ]);

        $order  = Order::findOrFail($request->order_id);           
        $metode = PaymentMethod::findOrFail($request->payment_method_id); 

        if ($order->user_id !== Auth::id()) {
            return response()->json([
                'status'  => 'gagal',
                'message' => 'Akses ditolak',
            ], 403);
        }

        try {
            DB::transaction(function () use ($order, $metode) {
                $order->status = 'menunggu_verifikasi';
                $order->save();                                      

                Payment::create([                                    
                    'user_id'           => $order->user_id,
                    'order_id'          => $order->id,
                    'payment_method_id' => $metode->id,
                    'jumlah'            => $order->total_harga,
                    'status'            => 'menunggu_verifikasi',
                ]);
            });

            $this->firebase->updateStatusOrder(                      
                $order->id,
                'menunggu_verifikasi'
            );

            $this->firebase->simpanNotifikasi(                      
                $order->user_id,
                'Pembayaran kamu sedang diverifikasi oleh admin'
            );

            return response()->json([
                'status'  => 'sukses',
                'message' => 'Konfirmasi diterima, menunggu verifikasi admin',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function batalkanPesanan($orderId)
    {
        $order = Order::findOrFail($orderId);                        

        if ($order->user_id !== Auth::id()) {
            return response()->json([
                'status'  => 'gagal',
                'message' => 'Akses ditolak',
            ], 403);
        }

        $order->status = 'cancelled';
        $order->save();                                              
        // FIREBASE: Kasih tau frontend order dibatalkan
        $this->firebase->updateStatusOrder($orderId, 'cancelled');   

        $this->firebase->simpanNotifikasi(                           
            $order->user_id,
            'Pesanan anda telah dibatalkan'
        );

        return response()->json([
            'status'  => 'sukses',
            'message' => 'Pesanan berhasil dibatalkan',
        ]);
    }
}