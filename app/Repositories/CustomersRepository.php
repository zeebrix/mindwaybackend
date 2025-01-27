<?php

namespace App\Repositories;

use App\Models\Customer;

class CustomersRepository extends BaseRepository {

    /**
     * @param Customers $model
     */
    public function __construct(Customer $model) {
        $this->model = $model;
    }

}
