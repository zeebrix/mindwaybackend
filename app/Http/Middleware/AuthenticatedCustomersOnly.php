<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthenticatedCustomersOnly {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next) {
        $response = null;
        $apiAuthToken = $request->headers->get('Authorization'); //API_AUTH_TOKEN From Customers
        if (!$apiAuthToken || empty($apiAuthToken)) {
            $response = response()->json([
                'code' => 401,
                'status' => 'Unauthorized',
                'message' => 'Please provide valid authorization token parameters',
            ]);
        }

        $customer = \App\Models\Customer::where(["status" => 1, "api_auth_token" => $apiAuthToken])->first();
        if (!$customer) {
            $response = response()->json([
                'code' => 401,
                'status' => 'Unauthorized',
                'message' => 'Token mismatched.',
            ]);
        }

        if (!$response) {
            $request->request->add(['customer_id' => $customer->id]);
            $request->request->add(['customer' => $customer->toArray()]);
            $response = $next($request);
        }

        return $response;
    }

}
