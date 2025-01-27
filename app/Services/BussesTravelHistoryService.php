<?php

namespace App\Services;

use App\Repositories\BussesTravelHistoryRepository;

class BussesTravelHistoryService {

    /**
     * @var BussesTravelHistoryRepository
     */
    protected $repository;

    public function __construct(BussesTravelHistoryRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * @param array $filters
     * @return mixed
     */
    public function getAll(array $filters = []): mixed {
        return $this->repository->getAll($filters);
    }

}
