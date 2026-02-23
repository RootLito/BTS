<?php

namespace App\Http\Controllers;

use App\Models\TripTicket;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\Notification;
use Illuminate\Http\Request;

class TripTicketController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }
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
            'driver_id' => 'nullable|exists:drivers,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
        ]);

        $validated['client_id'] = auth()->id();
        $validated['office'] = auth()->user()->office;
        $validated['status'] = 'Pending';

        $tripTicket = TripTicket::create($validated);

        Notification::create([
            'trip_id' => $tripTicket->id,
            'message' => 'New trip ticket created for ' . $validated['destination'],
            'is_admin' => true,
            'is_viewed' => false,
        ]);

        return redirect()->route('client.booking')->with('status', 'Booking submitted successfully!');
    }


    public function storeBooking(Request $request)
    {
        $request->merge([
            'passengers' => json_decode($request->passengers, true)
        ]);

        $validated = $request->validate([
            'office' => 'required|string|max:255',
            'purpose' => 'required|string',
            'destination' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'passengers' => 'required|array|min:1',
            'driver_id' => 'nullable|exists:drivers,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
        ]);

        $validated['client_id'] = null;
        $validated['status'] = 'Pending';

        $tripTicket = TripTicket::create($validated);

        Notification::create([
            'trip_id' => $tripTicket->id,
            'message' => 'New trip ticket created for ' . $validated['destination'],
            'is_admin' => true,
            'is_viewed' => false,
        ]);

        return redirect()->route('admin.book')->with('status', 'Booking recorded successfully!');
    }

    public function destroyBooking($id)
    {
        $ticket = TripTicket::findOrFail($id);

        if (!$ticket->client_id || $ticket->client_id === auth()->id()) {
            $ticket->delete();

            return redirect()->back()->with('status', 'Booking deleted successfully.');
        }

        return redirect()->back()->with('error', 'You do not have permission to delete this record.');
    }



    public function assignDriver(Request $request, TripTicket $tripTicket)
    {
        $request->validate(['driver_id' => 'nullable|exists:drivers,id']);

        // 1. Reset Old Driver/Vehicle Status to Available
        if ($tripTicket->driver_id) {
            Driver::where('id', $tripTicket->driver_id)->update(['status' => 'Available']);
        }
        if ($tripTicket->vehicle_id) {
            Vehicle::where('id', $tripTicket->vehicle_id)->update(['status' => 'Available']);
        }

        // 2. Assign New Driver
        if ($request->driver_id) {
            $driver = Driver::with('vehicle')->findOrFail($request->driver_id);

            $tripTicket->update([
                'driver_id' => $driver->id,
                'vehicle_id' => $driver->vehicle_id,
            ]);

            // If trip is already Approved, mark new driver as "On Trip"
            if ($tripTicket->status === 'Approved') {
                $driver->update(['status' => 'On Trip']);
                if ($driver->vehicle)
                    $driver->vehicle->update(['status' => 'On Trip']);
            }

            // Send Notification for Driver Assignment
            Notification::create([
                'trip_id' => $tripTicket->id,
                'message' => "Driver {$driver->name} has been assigned to your trip to {$tripTicket->destination}.",
                'is_admin' => false,
            ]);
        } else {
            // Unassign everything
            $tripTicket->update(['driver_id' => null, 'vehicle_id' => null]);
        }

        return redirect()->back()->with('success', 'Driver assignment updated!');
    }

    // FUNCTION 2: Only handles Approval/Cancellation
    public function updateStatus(Request $request, TripTicket $tripTicket)
    {
        $request->validate(['status' => 'required|in:Approved,Cancelled,Completed']);
        $newStatus = $request->status;

        // 1. Update Driver/Vehicle Status based on the move
        if ($newStatus === 'Approved') {
            if ($tripTicket->driver_id)
                Driver::where('id', $tripTicket->driver_id)->update(['status' => 'On Trip']);
            if ($tripTicket->vehicle_id)
                Vehicle::where('id', $tripTicket->vehicle_id)->update(['status' => 'On Trip']);

            // STORE NOTIFICATION ONLY ON APPROVE
            Notification::create([
                'trip_id' => $tripTicket->id,
                'message' => "Your trip ticket to {$tripTicket->destination} has been Approved!",
                'is_admin' => false,
            ]);
        } else if (in_array($newStatus, ['Cancelled', 'Completed'])) {
            if ($tripTicket->driver_id)
                Driver::where('id', $tripTicket->driver_id)->update(['status' => 'Available']);
            if ($tripTicket->vehicle_id)
                Vehicle::where('id', $tripTicket->vehicle_id)->update(['status' => 'Available']);

            // NO notification stored here as per your request
        }

        $tripTicket->update(['status' => $newStatus]);

        return redirect()->back()->with('success', "Trip marked as {$newStatus}!");
    }



    public function destroy(TripTicket $tripTicket)
    {
        $tripTicket->delete();
        return back()->with('status', 'Trip deleted successfully!');
    }





    public function showTicket(TripTicket $tripTicket)
    {
        $tripTicket->load(['driver', 'vehicle']);
        return view('client.ticket', compact('tripTicket'));
    }

    // 2. Update your update method to allow the new fields
    public function addInfo(Request $request, TripTicket $tripTicket)
    {
        $validated = $request->validate([
            'purpose2' => 'nullable|string',
            'passengers2' => 'nullable|string',
            'supervisor' => 'nullable|string',
        ]);

        $tripTicket->update($validated);

        return redirect()->back()->with('success', 'Details added successfully!');
    }



}
