<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * URI yang tidak dicek CSRF
     */
    protected $except = [
        '/buy',   // ini sudah cukup untuk POST /buy
    ];
}