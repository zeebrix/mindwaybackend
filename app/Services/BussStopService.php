<?php

namespace App\Services;

use App\Repositories\BussStopsRepository;

class BussStopService {

    /**
     * @var BussStopsRepository
     */
    protected $repository;

    public function __construct(BussStopsRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * @param array $filters
     * @return mixed
     */
    public function store(array $modelValues = []) {
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

    /**
     * @param array $filters
     * @return mixed
     */
    public function findBussStop(array $modelValues = []) {
        if ($stops = $this->repository->getAll($modelValues, false, [], ["id", "title", "short_title", "latitude", "longitude"])) {
            return response()->json([
                        'code' => 200,
                        'status' => 'Success',
                        'message' => 'Buss stop results fetched successfully.',
                        'data' => [$stops]], 200);
        }

        return response()->json([
                    'code' => 421,
                    'status' => 'Error',
                    'message' => 'Buss stop unable to registered. Something went wrong.'], 421);
    }

}
