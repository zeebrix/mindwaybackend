<?php

namespace App\Services;

use App\Repositories\CouponRepository;

class CouponService {

    /**
     * @var CouponRepository
     */
    protected $repository;

    public function __construct(CouponRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * @param array $filters
     * @return mixed
     */
    public function addCoupon(array $modelValues = []) {
        if ($this->repository->addCoupon($modelValues)) {
            return response()->json([
                        'code' => 200,
                        'status' => 'Success',
                        'message' => 'Coupon Add successfully.'], 200);
        }

        return response()->json([
                    'code' => 421,
                    'status' => 'Error',
                    'message' => 'unable to add Coupon. Something went wrong.'], 421);
    }


    public function applyCoupon(array $modelValues = []){
        if($this->repository->applyCoupon($modelValues)){

        }
    }

}
