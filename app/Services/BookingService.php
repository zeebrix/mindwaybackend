<?php

namespace App\Services;

use App\Repositories\BookingsRepository;
use App\Repositories\BookingDetailsRepository;

class BookingService {

    /**
     * @var BookingsRepository
     */
    protected $repository;
    protected $BookingDetailsRepository;
    public function __construct(BookingsRepository $repository, BookingDetailsRepository $BookingDetailsRepository) {
        $this->repository = $repository;
        $this->BookingDetailsRepository = $BookingDetailsRepository;
    }

    /**
     * @param array $filters
     * @return mixed
     */
    public function bookings(array $modelValues = []) {
        $booking = array(
            "ticket_no" => $modelValues["ticket_no"],
            "buss_id" => $modelValues["buss_id"],
            "pickup_buss_stop_id" => $modelValues["pickup_buss_stop_id"],
            "dropoff_buss_stop_id" => $modelValues["dropoff_buss_stop_id"],
            "booking_date" => $modelValues["booking_date"],
            "route_id" => $modelValues["route_id"],
            "customer_id" => $modelValues["customer_id"],
            "customer_name" => $modelValues["customer_name"],
            "total_buss_stops_covered" => $modelValues["total_buss_stops_covered"],
            "total_passengers" => $modelValues["total_passengers"],
            "total_seats" => $modelValues["total_seats"],
            "total_fare" => $modelValues["total_fare"],
            "is_paid" => $modelValues["is_paid"] ?? 0 ,
            "travel_started_at" => $modelValues["travel_started_at"],
            "travel_ended_at" => $modelValues["travel_ended_at"],
            "seat_no" => $modelValues["seat_no"],
        );

        if($booking = $this->repository->bookings($booking)) {
        $data = json_decode($modelValues["bookings"]);
        foreach ($data as $data1)
            {
                $bookingDetails = array(
                "booking_id" => $booking->id,
                "passenger_phone" =>  $data1->passenger_phone,
                "passenger_name" => $data1->passenger_name,
                "passenger_cnic" => $data1->passenger_cnic,
                "passenger_age" => $data1->passenger_age,
                "seat_id" => $data1->seat_id,

                );
                $this->BookingDetailsRepository->bookings($bookingDetails);
        }

        }
        // dd($passenger_phone);

        // if($booking = $this->repository->bookings($booking)) {
        //     foreach ($modelValues["bookings"] as $bookingDetailAdd) {

                // $bookingDetails = array(
                    // "booking_id" => $booking->id,


					//echo "ye lo".$fname11
                    // "passenger_phone" => $bookingDetailAdd["passenger_phone"],
                    // "passenger_name" => $bookingDetailAdd["passenger_name"],
                    // "passenger_cnic" => $bookingDetailAdd["passenger_cnic"],
                    // "passenger_age" => $bookingDetailAdd["passenger_age"],
                // );


                // $this->BookingDetailsRepository->bookings($data1);
            // }
            return response()->json([
                'code' =>200,
                'status'=>'Success',
                'message' => 'Your ticket booked successfully!.',


            ],200);
        }

        // return response()->json([
        //     'code' => 421,
        //     'status' => 'Error',
        //     'message' => 'unable to book ticket. Something went wrong.'
        // ],421);

    }

// }
