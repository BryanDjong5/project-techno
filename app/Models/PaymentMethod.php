<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'nama',
        'kode',
        'no_rekening',
        'atas_nama',
        'logo',
        'aktif'
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}