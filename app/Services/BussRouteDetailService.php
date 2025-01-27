<?php

namespace App\Services;

use App\Repositories\BussRouteDetailRepository;

class BussRouteDetailService {

    /**
     * @var BussStopsRepository
     */
    protected $repository;

    public function __construct(BussRouteDetailRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * @param array $filters
     * @return mixed
     */
    public function store(array $modelValues = []) {
        dd($modelValues);
        if ($this->repository->store($modelValues)) {
            return response()->json([
                        'code' => 200,
                        'status' => 'Success',
                        'message' => 'Buss stop registered successfully.'], 200);
        }

        return response()->json([
                    'code' => 421,
                    'status' => 'Error',
                    'message' => 'Buss stop unable to registered. Something went wrong.'], 421);
    }

}
