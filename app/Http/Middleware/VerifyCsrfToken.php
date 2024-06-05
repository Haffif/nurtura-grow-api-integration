<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        '/upload-image',
        '/antares/*',
        '/test',
        '/login',
        '/register',
        '/logout',
        '/refresh-token',
        '/lahan/*',
        '/penanaman/*',
        '/sensor/*',
        '/ml/*',
        '/irrigation/*',
        '/fertilizer/*',
        '/plant/*'
    ];
}
