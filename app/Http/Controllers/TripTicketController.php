<?php

namespace App\Http\Controllers;

use App\Models\TripTicket;
use App\Models\Driver;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class TripTicketController extends Controller
{
    public function index()
    {
        $tickets = TripTicket::with(['driver', 'vehicle'])
            ->latest()
            ->get();

        $drivers = Driver::all();
        $vehicles = Vehicle::all();

        return view('client.booking', compact(
            'tickets',
            'drivers',
            'vehicles'
        ));
    }

    public function indexAdmin(Request $request)
    {
        $query = TripTicket::with(['driver', 'vehicle']);
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->orWhere('destination', 'like', "%{$search}%");
            });
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        $tickets = $query->latest()->paginate(9);
        $tickets->appends($request->all());
        $drivers = Driver::all();
        $vehicles = Vehicle::all();
        return view('admin.booking', compact('tickets', 'drivers', 'vehicles'));
    }

    public function showAdmin(TripTicket $tripTicket)
    {
        $tripTicket->load(['driver', 'vehicle']);
        $drivers = Driver::with('vehicle')->get();
        return view('admin.booking-summary', compact('tripTicket', 'drivers'));
    }

    public function tripTicket(Request $request)
    {
        $query = TripTicket::with(['driver', 'vehicle'])
            ->where('client_id', auth()->id());

        $query->when($request->search, function ($q, $search) {
            $q->where(function ($inner) use ($search) {
                $inner->where('destination', 'like', "%{$search}%")
                    ->orWhere('purpose', 'like', "%{$search}%");
            });
        });

        $sortOrder = $request->get('sort', 'latest');
        if ($sortOrder === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $tickets = $query->paginate(8)->withQueryString();

        return view('client.trip-ticket', compact('tickets'));
    }


    public function store(Request $request)
    {
        $request->merge([
            'passengers' => json_decode($request->passengers, true)
        ]);

        $validated = $request->validate([
            'purpose' => 'required|string',
            'destination' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'passengers' => 'required|array|min:1',
            'driver_id' => 'exists:drivers,id',
            'vehicle_id' => 'exists:vehicles,id',
        ]);
        $validated['client_id'] = auth()->id();
        $validated['office'] = auth()->user()->office;
        $validated['status'] = 'Pending';

        TripTicket::create($validated);

        return redirect()->route('client.booking')->with('status', 'Booking submitted successfully!');
    }




    // public function update(Request $request, TripTicket $tripTicket)
    // {
    //     $validated = $request->validate([
    //         'status' => 'required|string',
    //     ]);

    //     $tripTicket->update($validated);
    //     return redirect()->back()->with('success', 'Trip Ticket updated!');
    // }

    public function update(Request $request, TripTicket $tripTicket)
    {
        $validated = $request->validate([
            'status' => 'nullable|string|in:Pending,Approved,Cancelled,Completed',
            'driver_id' => 'nullable|exists:drivers,id',
        ]);

        // Handle Driver/Vehicle Assignment Logic
        if ($request->filled('driver_id')) {
            $driver = Driver::find($request->driver_id);
            if ($driver) {
                $validated['vehicle_id'] = $driver->vehicle_id;
            }
        }

        $tripTicket->update($validated);

        // Dynamic message based on what was updated
        $message = $request->has('status')
            ? "Trip status marked as {$request->status}!"
            : "Assignment updated!";

        return redirect()->back()->with('success', $message);
    }

    public function destroy(TripTicket $tripTicket)
    {
        $tripTicket->delete();
        return redirect()->back()->with('success', 'Trip Ticket deleted!');
    }



 
}
