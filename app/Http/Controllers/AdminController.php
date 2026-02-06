<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TripTicket;
use App\Models\Driver;
use App\Models\Vehicle;

class AdminController extends Controller
{
    public function index()
    {
        $totalVehicles = Vehicle::count();
        $availVehicles = Vehicle::where('status', 'Available')->count();
        $tripVehicles = Vehicle::where('status', 'On Trip')->count();
        $maintVehicles = Vehicle::where('status', 'Maintenance')->count();

        $totalDrivers = Driver::count();
        $availDrivers = Driver::where('status', 'Available')->count();
        $tripDrivers = Driver::where('status', 'On Trip')->count();

        $driversList = Driver::latest()->take(10)->get();
        $vehiclesList = Vehicle::latest()->take(10)->get();

        $activeTripsCount = TripTicket::whereMonth('created_at', now()->month)->count();

        $events = TripTicket::all()->map(function ($ticket) {
            return [
                'title' => $ticket->destination,
                'start' => $ticket->created_at->format('Y-m-d'),
                'color' => $ticket->status === 'On Trip' ? '#facc15' : '#10b981',
                'extendedProps' => [
                    'office' => $ticket->office,
                    'driver' => $ticket->driver->name ?? 'No Driver',
                ]
            ];
        });

        return view('admin.dashboard', compact(
            'totalVehicles',
            'availVehicles',
            'tripVehicles',
            'maintVehicles',
            'totalDrivers',
            'availDrivers',
            'tripDrivers',
            'driversList',
            'vehiclesList',
            'activeTripsCount',
            'events',
            'activeTripsCount'
        ));
    }
}
