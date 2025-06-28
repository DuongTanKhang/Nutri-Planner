<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        // Nếu request không chờ JSON (web) thì redirect về route 'login'
        // Nếu request API thì trả null để trả lỗi 401 không redirect
        if (! $request->expectsJson()) {
            return route('login'); // Hoặc null nếu bạn không có route login
        }
    }
}
