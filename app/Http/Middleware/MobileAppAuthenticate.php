<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Response;

class MobileAppAuthenticate {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next) {
        $response = null;
        $appAuthToken = $request->headers->get('app-auth-token'); //APP_AUTH_TOKEN
        if (!$appAuthToken) {
            $response = response()->json([
                'code' => 401,
                'status' => 'Unauthorized',
                'message' => 'Please provide valid app auth parameters',
            ]);
        }

        if (!empty($appAuthToken) && $appAuthToken != \Config::get("constants.APP_AUTH_TOKEN")) {
            $response = response()->json([
                'code' => 401,
                'status' => 'Unauthorized',
                'message' => 'Token mismatched.',
            ]);
        }

        if (!$response) {
            $response = $next($request);
        }

        return $response;
    }

}
