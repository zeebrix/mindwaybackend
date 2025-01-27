<?php

namespace App\Services;

use App\Repositories\BussesRepository;

class BussesService {

    /**
     * @var BussesRepository
     */
    protected $repository;

    public function __construct(BussesRepository $repository) {
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
                        'message' => 'Buss registered successfully.'], 200);
        }

        return response()->json([
                    'code' => 421,
                    'status' => 'Error',
                    'message' => 'Buss unable to registered. Something went wrong.'], 421);
    }




}
