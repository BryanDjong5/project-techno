<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * URI yang tidak dicek CSRF
     */
    protected $except = [
        '/register',
        '/login',
        '/buy', 
        '/rating',
        '/cart/add',
        '/cart/update-qty',
        '/cart/remove',
        '/cart/checkout',
        '/cart/clear',  
        '/chat/send',   
        '/user-info',
    ];
}