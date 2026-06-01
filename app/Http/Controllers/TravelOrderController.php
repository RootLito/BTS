<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TripTicket;
use App\Models\TravelOrder;
use App\Models\Client;
use App\Models\DocumentTracking;

class TravelOrderController extends Controller
{
    public function store(Request $request, TripTicket $tripTicket)
    {
        $tripId = $tripTicket->id ?? $request->route('tripTicket');

        if (!$tripId) {
            return back()->withErrors(['error' => 'Invalid Trip Ticket ID.']);
        }

        $validated = $request->validate([
            'date' => 'required|date',
            'personnel' => 'required|array',
            'personnel.*.name' => 'nullable|string',
            'personnel.*.salary' => 'nullable|string',
            'personnel.*.position' => 'nullable|string',
            'personnel.*.office' => 'nullable|string',
            'departure' => 'nullable|string',
            'return' => 'nullable|string',
            'destination' => 'nullable|string',
            'purpose' => 'nullable|string',
            'recommended_by' => 'nullable|array',
            'recommended_by.*.name' => 'nullable|string',
            'recommended_by.*.position' => 'nullable|string',
        ]);

        $personnel = array_values(array_filter($validated['personnel'], function ($person) {
            return isset($person['name']) && trim($person['name']) !== '';
        }));

        $recommended = [];
        if (!empty($validated['recommended_by'])) {
            $recommended = array_values(array_filter($validated['recommended_by'], function ($recommender) {
                return isset($recommender['name']) && trim($recommender['name']) !== '';
            }));
        }

        $travelOrder = TravelOrder::updateOrCreate(
            ['trip_id' => $tripId],
            [
                'date' => $validated['date'],
                'personnel' => $personnel,
                'departure' => $validated['departure'] ?? null,
                'return' => $validated['return'] ?? null,
                'destination' => $validated['destination'] ?? null,
                'purpose' => $validated['purpose'] ?? null,
                'recommended_by' => $recommended,
            ]
        );

        return redirect()->route('client.travel-order.show', $tripId);
    }

    public function show(TripTicket $tripTicket)
    {
        $travelOrder = TravelOrder::where('trip_id', $tripTicket->id)->first();

        if (!$travelOrder) {
            $travelOrder = new TravelOrder([
                'trip_id' => $tripTicket->id,
                'personnel' => [],
                'recommended_by' => [],
            ]);
        }
        $latestTracking = DocumentTracking::where('trip_ticket_id', $tripTicket->id)
            ->latest()
            ->first();

        $offices = Client::whereNotNull('office')
            ->where('office', '!=', '')
            ->pluck('office')
            ->unique()
            ->values();
        return view('client.travel-order', compact('travelOrder', 'tripTicket', 'offices', 'latestTracking'));
    }


    // public function track(Request $request, TripTicket $tripTicket)
    // {
    //     $validated = $request->validate([
    //         'document_no' => 'required|string',
    //         'route' => 'required|string',
    //         'remarks' => 'nullable|string',
    //     ]);

    //     try {
    //         DocumentTracking::create([
    //             'trip_ticket_id' => $tripTicket->id,
    //             'document_no' => $validated['document_no'],
    //             'route_from' => auth()->user()->office,
    //             'route_to' => $validated['route'],
    //             'remarks' => $validated['remarks'],
    //             'status' => 'Forwarded',
    //             'date_released' => now(),
    //             'date_received' => null,
    //         ]);

    //         return redirect()
    //             ->back()
    //             ->with('success', 'Document successfully forwarded to ' . $validated['route'] . '!');

    //     } catch (\Exception $e) {
    //         return redirect()
    //             ->back()
    //             ->with('error', 'Could not forward the document. Please try again.');
    //     }
    // }
    public function receive(TripTicket $tripTicket)
    {
        $userOffice = auth()->user()->office;

        // Find the latest tracking entry routed to this user's office that hasn't been received yet
        $tracking = DocumentTracking::where('trip_ticket_id', $tripTicket->id)
            ->where('route_to', $userOffice)
            ->whereNull('date_received')
            ->latest()
            ->first();

        if (!$tracking) {
            return redirect()->back()->with('error', 'No pending document found to receive.');
        }

        // Mark as received
        $tracking->update([
            'status' => 'Received',
            'date_received' => now(),
        ]);

        return redirect()->back()->with('success', 'Document successfully marked as Received!');
    }

    public function track(Request $request, TripTicket $tripTicket)
    {
        $validated = $request->validate([
            'document_no' => 'required|string',
            'route' => 'required|string',
            'remarks' => 'nullable|string',
        ]);

        try {
            $userOffice = auth()->user()->office;

            // 1. First, check if there is an open "Received" document for this office
            // and safely compute the duration spent in this office before releasing it.
            $currentTracking = DocumentTracking::where('trip_ticket_id', $tripTicket->id)
                ->where('route_to', $userOffice)
                ->whereNotNull('date_received')
                ->latest()
                ->first();

            // 2. Create the next step log (The Release event)
            DocumentTracking::create([
                'trip_ticket_id' => $tripTicket->id,
                'document_no' => $validated['document_no'],
                'route_from' => $userOffice,
                'route_to' => $validated['route'],
                'remarks' => $validated['remarks'],
                'status' => 'Released', // Changed from 'Forwarded' to match your front-end expectations
                'date_released' => now(),
                'date_received' => null, // Will be filled by the receiving office
            ]);

            return redirect()
                ->back()
                ->with('success', 'Document successfully released and forwarded to ' . $validated['route'] . '!');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Could not forward the document. Please try again.');
        }
    }

    public function index()
    {
        $userOffice = auth()->user()->office;
        $documents = DocumentTracking::where('route_to', $userOffice)
            ->with(['tripTicket.notes'])
            ->get()
            ->unique('trip_ticket_id');
        $offices = Client::whereNotNull('office')
            ->where('office', '!=', '')
            ->pluck('office')
            ->unique()
            ->values();

        return view('client.document-tracking', compact('documents', 'offices'));
    }

    public function showTracking(TripTicket $tripTicket)
    {
        // Retrieve the associated Travel Order details
        $travelOrder = TravelOrder::where('trip_id', $tripTicket->id)->first();

        // Retrieve tracking logs
        $trackings = DocumentTracking::where('trip_ticket_id', $tripTicket->id)
            ->latest()
            ->get();

        // Get document number from any existing log entry
        $documentNo = $trackings->first()?->document_no ?? 'N/A';

        // Retrieve active list of offices for the dropdown menu selection
        $offices = Client::whereNotNull('office')
            ->where('office', '!=', '')
            ->pluck('office')
            ->unique()
            ->values();

        return view('client.document-action', compact('tripTicket', 'travelOrder', 'trackings', 'documentNo', 'offices'));
    }
}
