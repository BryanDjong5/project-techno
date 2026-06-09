<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    protected $fillable = [
        'user_id',
        'game',
        'product',
        'category',
        'price',
        'description',
        'image',
        'status'
    ];
}