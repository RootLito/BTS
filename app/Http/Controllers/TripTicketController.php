<?php

namespace App\Http\Controllers;

use App\Models\TripTicket;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\Note;
use App\Models\Notification;
use Illuminate\Http\Request;
use Carbon\Carbon;


class TripTicketController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function index()
    {
        $drivers = Driver::with('latestTrip')
            ->get()
            ->sortBy(function ($driver) {
                return match (strtolower($driver->status)) {
                    'available' => 1,
                    'on trip' => 2,
                    default => 3,
                };
            });

        $tickets = TripTicket::with(['driver', 'vehicle'])->latest()->get();
        $vehicles = Vehicle::all();

        $events = TripTicket::all()->map(function ($ticket) {
            $startDate = Carbon::parse($ticket->start_date);
            $endDate = Carbon::parse($ticket->end_date);

            $color = match ($ticket->status) {
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
                'color' => $color,
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

        return view('client.booking', compact(
            'tickets',
            'drivers',
            'vehicles',
            'events'
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
        return view('admin.booking', compact(
            'tickets',
            'drivers',
            'vehicles',
        ));
    }

    public function adminBook(Request $request)
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

        return view('admin.book', compact(
            'tickets',
            'drivers',
            'vehicles',
            'events'
        ));
    }

    public function showAdmin(TripTicket $tripTicket)
    {
        $tripTicket->load(['driver', 'vehicle']);

        $start = $tripTicket->start_date;
        $end = $tripTicket->end_date;

        $drivers = Driver::with('vehicle')
            ->withExists([
                'latestTrip as is_busy' => function ($query) use ($start, $end, $tripTicket) {
                    $query->where('id', '!=', $tripTicket->id)
                        ->whereIn('status', ['Approved', 'Completed'])
                        ->where(function ($q) use ($start, $end) {
                            $q->whereBetween('start_date', [$start, $end])
                                ->orWhereBetween('end_date', [$start, $end])
                                ->orWhere(function ($overlap) use ($start, $end) {
                                    $overlap->where('start_date', '<=', $start)
                                        ->where('end_date', '>=', $end);
                                });
                        });
                }
            ])
            ->get();

        return view('admin.booking-summary', compact('tripTicket', 'drivers'));
    }

    public function tripTicket(Request $request)
    {
        $query = TripTicket::with(['driver', 'vehicle', 'documentTrackings'])
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
        if ($tripTicket->driver_id) {
            Driver::where('id', $tripTicket->driver_id)->update(['status' => 'Available']);
        }
        if ($tripTicket->vehicle_id) {
            Vehicle::where('id', $tripTicket->vehicle_id)->update(['status' => 'Available']);
        }
        if ($request->driver_id) {
            $driver = Driver::with('vehicle')->findOrFail($request->driver_id);
            $tripTicket->update([
                'driver_id' => $driver->id,
                'vehicle_id' => $driver->vehicle_id,
            ]);
            if ($tripTicket->status === 'Approved') {
                $driver->update(['status' => 'On Trip']);
                if ($driver->vehicle)
                    $driver->vehicle->update(['status' => 'On Trip']);
            }
            Notification::create([
                'trip_id' => $tripTicket->id,
                'message' => "Driver {$driver->name} has been assigned to your trip to {$tripTicket->destination}.",
                'is_admin' => false,
            ]);
        } else {
            $tripTicket->update(['driver_id' => null, 'vehicle_id' => null]);
        }

        return redirect()->back()->with('success', 'Driver assignment updated!');
    }
    public function updateStatus(Request $request, TripTicket $tripTicket)
    {
        $request->validate([
            'status' => 'required|in:Approved,Cancelled,Completed',
            'note' => 'required_if:status,Cancelled|nullable|string|max:500',
        ]);
        $newStatus = $request->status;
        if ($newStatus === 'Cancelled') {
            if ($request->filled('note')) {
                Note::create([
                    'trip_id' => $tripTicket->id,
                    'client_id' => $tripTicket->client_id,
                    'note' => $request->note,
                    'is_read' => false,
                ]);
            }
            Notification::create([
                'trip_id' => $tripTicket->id,
                'message' => "Trip to {$tripTicket->destination} was Cancelled. Reason: " . ($request->note ?? 'Not specified'),
                'is_admin' => false,
            ]);
            if ($tripTicket->driver_id)
                Driver::where('id', $tripTicket->driver_id)->update(['status' => 'Available']);
            if ($tripTicket->vehicle_id)
                Vehicle::where('id', $tripTicket->vehicle_id)->update(['status' => 'Available']);
        } else if ($newStatus === 'Approved') {
            if ($tripTicket->driver_id)
                Driver::where('id', $tripTicket->driver_id)->update(['status' => 'On Trip']);
            if ($tripTicket->vehicle_id)
                Vehicle::where('id', $tripTicket->vehicle_id)->update(['status' => 'On Trip']);

            Notification::create([
                'trip_id' => $tripTicket->id,
                'message' => "Your trip to {$tripTicket->destination} has been Approved!",
                'is_admin' => false,
            ]);
        } else if ($newStatus === 'Completed') {
            if ($tripTicket->driver_id)
                Driver::where('id', $tripTicket->driver_id)->update(['status' => 'Available']);
            if ($tripTicket->vehicle_id)
                Vehicle::where('id', $tripTicket->vehicle_id)->update(['status' => 'Available']);
        }
        $tripTicket->update(['status' => $newStatus]);
        return redirect()->back()->with('success', "Trip status updated to {$newStatus}");
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
