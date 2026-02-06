<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;
use App\Models\Vehicle;

class DriverController extends Controller
{
    public function index()
    {
        $drivers = Driver::with('vehicle')->latest()->get();
        $vehicles = Vehicle::all();
        $assignedVehicleIds = Driver::whereNotNull('vehicle_id')->pluck('vehicle_id')->toArray();
        return view('admin.driver', compact('drivers', 'vehicles', 'assignedVehicleIds'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'required|string',
            'vehicle_id' => 'nullable|exists:vehicles,id',
        ]);

        $validated['status'] = 'Available';
        Driver::create($validated);
        return redirect()->back()->with('success', 'Driver added!');
    }

    public function update(Request $request, Driver $driver)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'required|string',
            'status' => 'required|string',
            'vehicle_id' => 'nullable|exists:vehicles,id',
        ]);

        $driver->update($validated);
        return redirect()->back()->with('success', 'Driver updated!');
    }

    public function destroy(Driver $driver)
    {
        $driver->delete();
        return redirect()->back()->with('success', 'Driver deleted!');
    }
}
