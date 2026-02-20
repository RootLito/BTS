<?php

namespace App\Http\Controllers;

use App\Models\TripTicket;
use App\Models\Driver;
use App\Models\Vehicle;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function __construct()
    {
        $this->syncTripStatuses();
    }

    protected function syncTripStatuses()
    {
        if (now()->hour >= 7) {
            $today = now()->toDateString();

            $expiredTrips = TripTicket::where('status', 'Approved')
                ->where('end_date', '<', $today)
                ->get();

            foreach ($expiredTrips as $trip) {
                if ($trip->driver_id) {
                    Driver::where('id', $trip->driver_id)->update(['status' => 'Available']);
                }

                if ($trip->vehicle_id) {
                    Vehicle::where('id', $trip->vehicle_id)->update(['status' => 'Available']);
                }

                $trip->update(['status' => 'Completed']);
            }
        }
    }
}