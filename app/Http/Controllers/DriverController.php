<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function index()
    {
        $drivers = Driver::latest()->get();
        return view('admin.driver', compact('drivers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'required|string',
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
