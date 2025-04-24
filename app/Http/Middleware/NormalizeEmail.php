<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class NormalizeEmail
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        $emails = ['email', 'user_email', 'contact_email']; // any email fields you expect

        foreach ($emails as $field) {
            if ($request->has($field)) {
                $request->merge([
                    $field => strtolower($request->input($field)),
                ]);
            }
        }

        return $next($request);
    }
}
