<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TripTicket;
use App\Models\TravelOrder;

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

        return view('client.travel-order', compact('travelOrder', 'tripTicket'));
    }
}
