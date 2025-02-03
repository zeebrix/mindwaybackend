<?php

namespace App\Observers;

use App\Models\Customer;
use App\Models\CustomreBrevoData;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;
class CustomerObserver
{
    protected $auth;

    public function __construct()
    {
        $this->auth = (new Factory)
            ->withServiceAccount(base_path('public/mw-1/firebase-credentials.json'))
            ->createAuth();
    }
    /**
     * Handle the Customer "created" event.
     *
     * @param  \App\Models\Customer  $customer
     * @return void
     */
    public function created(Customer $customer)
    {
        $customerId = $customer->id;
        $brevoData = CustomreBrevoData::where('app_customer_id', $customerId)->first();
        if($brevoData){
            $departId = $customer->department_id;
            $brevoData->department_id = $departId;
            $brevoData->save();
        }
    }

    /**
     * Handle the Customer "updated" event.
     *
     * @param  \App\Models\Customer  $customer
     * @return void
     */
    public function updated(Customer $customer)
    {
        $customerId = $customer->id;
        $brevoData = CustomreBrevoData::where('app_customer_id', $customerId)->first();
        if($brevoData){
            $departId = $customer->department_id;
            $brevoData->department_id = $departId;
            $brevoData->save();
        }
    }

    /**
     * Handle the Customer "deleted" event.
     *
     * @param  \App\Models\Customer  $customer
     * @return void
     */
    public function deleted(Customer $customer)
    {
        try {
              $firebaseUser = $this->auth->getUserByEmail($customer->email);
              $this->auth->deleteUser($firebaseUser->uid);
              $this->auth->revokeRefreshTokens($firebaseUser->uid);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    /**
     * Handle the Customer "restored" event.
     *
     * @param  \App\Models\Customer  $customer
     * @return void
     */
    public function restored(Customer $customer)
    {
        //
    }

    /**
     * Handle the Customer "force deleted" event.
     *
     * @param  \App\Models\Customer  $customer
     * @return void
     */
    public function forceDeleted(Customer $customer)
    {
        //
    }
}
