<?php

namespace App\Services;

use App\Repositories\BussesSeatsRepository;

class SeatsService {

    /**
     * @var BussesSeatsRepository
     */
    protected $repository;

    public function __construct(BussesSeatsRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * @param array $filters
     * @return mixed
     */


    public function seats(array $modelValues = []) {

        if($this->repository->seats($modelValues)) {
            return response()->json([
                'code' =>200,
                'status'=>'Success',
                'message' => 'Seat select successfully.'
            ],200);
        }

        return response()->json([
            'code' => 421,
            'status' => 'Error',
            'message' => 'unable to select this seat. Something went wrong.'
        ],421);

    }


}
