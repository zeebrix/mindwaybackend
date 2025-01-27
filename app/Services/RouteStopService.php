<?php

namespace App\Services;

use App\Repositories\RouteStopsRepository;

class RouteStopService {

    /**
     * @var RoutesRepository
     */
    protected $repository;

    public function __construct(RouteStopsRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * @param array $filters
     * @return mixed
     */
    public function store(array $modelValues = []) {
        dd($modelValues);
        $route = array(
            "title" => $modelValues["title"],
            "code" => $modelValues["code"],
            "status" => $modelValues["status"] ?? 0
        );

        if ($route = $this->repository->store($route)) {

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
