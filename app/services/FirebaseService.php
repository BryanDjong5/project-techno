<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Database;

class FirebaseService
{
    protected $database;

    public function __construct()
    {
        $firebase = (new Factory)
            ->withServiceAccount(config('firebase.credentials.file'))
            ->withDatabaseUri(config('firebase.database.url'));

        $this->database = $firebase->createDatabase();
    }

    public function updateStatusOrder($orderId, $status)
    {
        $this->database
            ->getReference('orders/' . $orderId . '/status')
            ->set($status);
    }

    public function simpanNotifikasi($userId, $pesan)
    {
        $this->database
            ->getReference('notifikasi/' . $userId)
            ->push([
                'pesan'      => $pesan,
                'dibaca'     => false,
                'created_at' => now()->toDateTimeString(),
            ]);
    }
}