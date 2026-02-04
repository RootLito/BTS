<?php

namespace App\Http\Controllers;

use App\Models\TripTicket;
use Illuminate\Http\Request;

class TripTicketController extends Controller
{
    public function index()
    {
        $tickets = TripTicket::with(['driver', 'vehicle'])->latest()->get();
        return view('client.trips.index', compact('tickets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'purpose' => 'required|string',
            'destination' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'passengers' => 'required', 
            'driver_id' => 'required|exists:drivers,id',
            'vehicle_id' => 'required|exists:vehicles,id',
        ]);

        $validated['passengers'] = json_decode($request->passengers, true);
        $validated['status'] = 'Pending';

        TripTicket::create($validated);

        return redirect()->route('trips.index')->with('status', 'Trip ticket created!');
    }

    public function show(TripTicket $tripTicket)
    {
        return view('client.trips.show', compact('tripTicket'));
    }

    public function update(Request $request, TripTicket $tripTicket)
    {
        $validated = $request->validate([
            'status' => 'required|string',
        ]);

        $tripTicket->update($validated);
        return redirect()->back()->with('success', 'Trip Ticket updated!');
    }

    public function destroy(TripTicket $tripTicket)
    {
        $tripTicket->delete();
        return redirect()->back()->with('success', 'Trip Ticket deleted!');
    }
}
