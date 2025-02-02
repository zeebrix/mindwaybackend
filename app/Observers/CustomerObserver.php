<?php

namespace App\Observers;

use App\Models\Customer;
use App\Models\CustomreBrevoData;

class CustomerObserver
{
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
        //
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
