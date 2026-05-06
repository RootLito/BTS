<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TripTicket;
use App\Models\Driver;
use App\Models\Vehicle;
use Carbon\Carbon;

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
            $startDate = Carbon::parse($ticket->start_date);
            $endDate = Carbon::parse($ticket->end_date);

            $statusColor = match ($ticket->status) {
                'Pending' => '#eab308', 
                'Approved' => '#3b82f6', 
                'Cancelled' => '#ef4444', 
                'Completed' => '#10b981',
                default => '#71717a', 
            };

            return [
                'title' => $ticket->destination,
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->copy()->addDay()->format('Y-m-d'),
                'color' => $statusColor,
                'extendedProps' => [
                    'office' => $ticket->office,
                    'purpose' => $ticket->purpose,
                    'driver' => $ticket->driver->name ?? 'No Driver',
                    'status' => $ticket->status,
                    'display_start' => $startDate->format('M d, Y'),
                    'display_end' => $endDate->format('M d, Y'),
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
            'events'
        ));
    }
}