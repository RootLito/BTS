<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $drivers = Driver::all();
        $vehicles = Vehicle::all();

        return view('client.booking', compact('drivers', 'vehicles'));
    }
}