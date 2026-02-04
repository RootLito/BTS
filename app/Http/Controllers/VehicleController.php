<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    // READ: List all vehicles
    public function index()
    {
        $vehicles = Vehicle::latest()->get();
        return view('admin.vehicle', compact('vehicles'));
    }

    // CREATE: Store a new vehicle
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle' => 'required|string|max:255',
            'plate_no' => 'required|string|unique:vehicles,plate_no',
            'type' => 'required|string',
        ]);

        $validated['status'] = 'Available';

        Vehicle::create($validated);

        return redirect()->back()->with('success', 'Vehicle added successfully!');
    }

    // UPDATE: Update vehicle details
    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'vehicle' => 'required|string|max:255',
            'plate_no' => 'required|string|unique:vehicles,plate_no,' . $vehicle->id,
            'type' => 'required|string',
            'status' => 'required|string',
        ]);

        $vehicle->update($validated);

        return redirect()->back()->with('success', 'Vehicle updated successfully!');
    }

    // DELETE: Remove vehicle from fleet
    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return redirect()->back()->with('success', 'Vehicle removed.');
    }
}