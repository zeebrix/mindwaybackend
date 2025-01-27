<?php

namespace App\Services;

use App\Repositories\RoutesRepository;
use App\Repositories\RouteStopsRepository;
use App\Repositories\BussRouteDetailRepository;

class RouteService {

    /**
     * @var RoutesRepository
     */
    protected $repository;
    protected $routeStopsRepository;
    protected $bussRouteDetailRepository;

    public function __construct(RoutesRepository $repository, RouteStopsRepository $routeStopsRepository, BussRouteDetailRepository $bussRouteDetailRepository) {
        $this->repository = $repository;
        $this->routeStopsRepository = $routeStopsRepository;
        $this->bussRouteDetailRepository = $bussRouteDetailRepository;
    }

    /**
     * @param array $filters
     * @return mixed
     */
    public function store(array $modelValues = []) {
        $route = array(
            "title" => $modelValues["title"],
            "code" => $modelValues["code"],
            "status" => $modelValues["status"] ?? 0
        );

        if ($route = $this->repository->store($route)) {
            foreach ($modelValues["stops"] as $stop) {
                $routeStops = array(
                    "route_id" => $route->id,
                    "buss_stop_id" => $stop["buss_stop_id"]
                );

                $this->routeStopsRepository->store($routeStops);
            }

            return response()->json([
                        'code' => 200,
                        'status' => 'Success',
                        'message' => 'Route registered successfully.'], 200);
        }

        return response()->json([
                    'code' => 421,
                    'status' => 'Error',
                    'message' => 'Route unable to registered. Something went wrong.'], 421);
    }

    /**
     * @param array $filters
     * @return mixed
     */
    public function linkBussToRoute(array $modelValues = []) {
        $bussRouteDetail = \App\Models\BussesRouteDetails::where([
                    "buss_id" => $modelValues["buss_id"]
                ])
                ->orWhere([
                    "route_id" => $modelValues["route_id"]
                ])
                ->orWhere([
                    "driver_id" => $modelValues["driver_id"]
                ])
                ->first();

        if ($bussRouteDetail) {
            return response()->json([
                        'code' => 421,
                        'status' => 'Error',
                        'message' => 'This route or buss is already occupied. Please cancel its route or try with another combination.'], 421);
        }

        if (\App\Models\BussesRouteDetails::create($modelValues)) {

            return response()->json([
                        'code' => 200,
                        'status' => 'Success',
                        'message' => 'Route assigned to the buss successfully.'], 200);
        }


        return response()->json([
                    'code' => 421,
                    'status' => 'Error',
                    'message' => 'Route unable to assign to buss. Something went wrong.'], 421);
    }

}
