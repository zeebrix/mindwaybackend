<?php

namespace App\Services;

use App\Repositories\BookingDetailsRepository;

class bookingDetailService {

    /**
     * @var BookingDetailsRepository
     */
    protected $repository;

    public function __construct(BookingDetailsRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * @param array $filters
     * @return mixed
     */
    public function bookingDetails(array $modelValues = []) {
        if($this->repository->bookingDetails($modelValues)) {
            return response()->json([
                'code' =>200,
                'status'=>'Success',
                'message' => 'Your booking details add successfully!.'
            ],200);
        }

        return response()->json([
            'code' => 421,
            'status' => 'Error',
            'message' => 'unable to add your booking details. Something went wrong.'
        ],421);

    }

}
