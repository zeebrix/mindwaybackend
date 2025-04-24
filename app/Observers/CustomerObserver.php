<?php

namespace App\Observers;

use App\Models\Customer;
use App\Models\CustomreBrevoData;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;
use App\Services\BrevoService;

class CustomerObserver
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
     * Handle the Customer "created" event.
     *
     * @param  \App\Models\Customer  $customer
     * @return void
     */
    public function created(Customer $customer)
    {
        $customerId = $customer->id;
        $brevoData = CustomreBrevoData::where('app_customer_id', $customerId)->first();
        if ($brevoData) {
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
        if ($brevoData) {
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

            try {
                $brevo = new BrevoService();
                $brevo->removeUserFromList($customer->email);
            } catch (\Throwable $th) {
                Log::info("Brevo Observer delete error  " . $th->getMessage());
            }
            $firebaseUser = $this->auth->getUserByEmail($customer->email);
            $this->auth->updateUser($firebaseUser->uid, ['disabled' => true]);
            // $this->auth->deleteUser($firebaseUser->uid);
            $this->auth->revokeRefreshTokens($firebaseUser->uid);
            try {
                $customer3 = CustomreBrevoData::where('app_customer_id', $customer->id)->first();
                if ($customer3) {
                    $customer3->delete();
                }
            } catch (\Throwable $th) {
                Log::info("Observer Brevo Data delete error " . $th->getMessage());
            }
            Log::info("Firebase user disabled", ['uid' => $firebaseUser->uid, 'email' => $customer->email]);
        } catch (\Throwable $th) {
            Log::info("Firebase user disabled Error " . $th->getMessage());
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
