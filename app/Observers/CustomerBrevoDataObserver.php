<?php

namespace App\Observers;

use App\Models\CustomreBrevoData;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;

class CustomerBrevoDataObserver
{
    protected $auth;

    public function __construct()
    {
        try {
            $this->auth = (new Factory)
            ->withServiceAccount(base_path('public/mw-1/firebase-credentials.json'))
            ->createAuth();        
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
    /**
     * Handle the CustomreBrevoData "created" event.
     *
     * @param  \App\Models\CustomreBrevoData  $customer
     * @return void
     */
    public function created(CustomreBrevoData $customer)
    {
        //
    }

    /**
     * Handle the CustomreBrevoData "updated" event.
     *
     * @param  \App\Models\CustomreBrevoData  $customer
     * @return void
     */
    public function updated(CustomreBrevoData $customer)
    {
        //
    }

    /**
     * Handle the CustomreBrevoData "deleted" event.
     *
     * @param  \App\Models\CustomreBrevoData  $customer
     * @return void
     */
    public function deleted(CustomreBrevoData $customer)
    {
        try {

            $firebaseUser = $this->auth->getUserByEmail($customer->email);
            $this->auth->updateUser($firebaseUser->uid, ['disabled' => true]);
            // $this->auth->deleteUser($firebaseUser->uid);
            $this->auth->revokeRefreshTokens($firebaseUser->uid);
            Log::info("Firebase user disabled", ['uid' => $firebaseUser->uid, 'email' => $customer->email]);
        } catch (\Throwable $th) {
            Log::info("Firebase user disabled Error " . $th->getMessage());
        }
    }

    /**
     * Handle the CustomreBrevoData "restored" event.
     *
     * @param  \App\Models\CustomreBrevoData  $customer
     * @return void
     */
    public function restored(CustomreBrevoData $customer)
    {
        //
    }

    /**
     * Handle the CustomreBrevoData "force deleted" event.
     *
     * @param  \App\Models\CustomreBrevoData  $customer
     * @return void
     */
    public function forceDeleted(CustomreBrevoData $customer)
    {
        //
    }
}
