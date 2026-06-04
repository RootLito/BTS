<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TripTicket;
use App\Models\TravelOrder;
use App\Models\Client;
use App\Models\DocumentTracking;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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



    public function receive(TripTicket $tripTicket)
    {
        $userOffice = is_object(auth()->user()->office) ? auth()->user()->office->name : auth()->user()->office;
        $currentClientId = auth()->id();
        $lastGlobalTracking = DocumentTracking::where('trip_ticket_id', $tripTicket->id)
            ->latest()
            ->first();
        if ($lastGlobalTracking) {
            if ($lastGlobalTracking->status === 'Released' && $lastGlobalTracking->route_from === $userOffice) {
                return redirect()->back()->with('error', 'You cannot receive this document because your office released it.');
            }
        }
        $lastClientTracking = DocumentTracking::where('trip_ticket_id', $tripTicket->id)
            ->where('client_id', $currentClientId)
            ->latest()
            ->first();

        if ($lastClientTracking && $lastClientTracking->status === 'Received') {
            return redirect()->back()->with('error', 'Document has already been received by your office.');
        }
        $comingFrom = $lastGlobalTracking ? $lastGlobalTracking->route_from : 'Origin';
        DocumentTracking::create([
            'trip_ticket_id' => $tripTicket->id,
            'client_id' => $currentClientId,
            'document_no' => $tripTicket->document_no ?? $lastGlobalTracking->document_no ?? '',
            'route_from' => $comingFrom,
            'route_to' => 'Not Applicable',
            'status' => 'Received',
            'date_released' => null,
            'date_received' => now(),
        ]);
        return redirect()->back()->with('success', 'Document logged at ' . $userOffice);
    }


    public function track(Request $request, TripTicket $tripTicket)
    {
        $validated = $request->validate([
            'document_no' => 'required|string',
            'route' => 'required|string',
            'remarks' => 'nullable|string',
        ]);

        $userOffice = auth()->user()->office;

        DocumentTracking::create([
            'trip_ticket_id' => $tripTicket->id,
            'client_id' => auth()->id(),
            'document_no' => $validated['document_no'],
            'route_from' => $userOffice,
            'route_to' => $validated['route'],
            'remarks' => $validated['remarks'],
            'status' => 'Released',
            'date_released' => now(),
            'date_received' => null,
        ]);

        return redirect()->back()->with('success', 'Document successfully released and forwarded to ' . $validated['route'] . '!');
    }

    // public function index()
    // {
    //     $userOffice = auth()->user()->office;
    //     $userId = auth()->id();

    //     $documents = DocumentTracking::with(['tripTicket.notes'])
    //         ->where(function ($query) use ($userOffice, $userId) {
    //             $query->where('route_to', $userOffice)
    //                 ->orWhere('route_from', $userOffice)
    //                 ->orWhereHas('tripTicket', function ($q) use ($userId) {
    //                     $q->where('client_id', $userId);
    //                 });
    //         })
    //         ->latest()
    //         ->get()
    //         ->unique('trip_ticket_id')
    //         ->values();

    //     $offices = Client::whereNotNull('office')
    //         ->where('office', '!=', '')
    //         ->pluck('office')
    //         ->unique()
    //         ->values();

    //     return view('client.document-tracking', compact('documents', 'offices'));
    // }
    public function index()
    {
        $userOffice = auth()->user()->office;
        $userId = auth()->id();

        $documents = DocumentTracking::with(['tripTicket.notes'])
            ->where(function ($query) use ($userOffice, $userId) {
                $query->where('route_to', $userOffice)
                    ->orWhere('route_from', $userOffice)
                    ->orWhereHas('tripTicket', function ($q) use ($userId) {
                        $q->where('client_id', $userId);
                    });
            })
            ->latest()
            ->get()
            ->unique('trip_ticket_id')
            ->values();

        $receivedTicketIds = DocumentTracking::where('status', 'Received')
            ->where(function ($q) use ($userOffice) {
                $q->where('route_from', $userOffice)
                    ->orWhere('route_to', $userOffice); 
            })
            ->pluck('trip_ticket_id')
            ->toArray();

        $documents->transform(function ($doc) use ($receivedTicketIds, $userOffice) {
            $doc->is_new = ($doc->route_to === $userOffice && $doc->status === 'Released' && !in_array($doc->trip_ticket_id, $receivedTicketIds));
            return $doc;
        });

        $offices = Client::whereNotNull('office')
            ->where('office', '!=', '')
            ->pluck('office')
            ->unique()
            ->values();

        return view('client.document-tracking', compact('documents', 'offices'));
    }

    public function showTracking(TripTicket $tripTicket)
    {
        $travelOrder = TravelOrder::where('trip_id', $tripTicket->id)->first();

        $documentNo = DocumentTracking::where('trip_ticket_id', $tripTicket->id)
            ->whereNotNull('document_no')
            ->where('document_no', '!=', '')
            ->latest('id')
            ->value('document_no') ?? 'N/A';

        $trackings = DocumentTracking::with('client')
            ->where('trip_ticket_id', $tripTicket->id)
            ->where('document_no', $documentNo)
            ->orderBy('id', 'asc')
            ->get();

        $offices = Client::whereNotNull('office')
            ->where('office', '!=', '')
            ->pluck('office')
            ->unique()
            ->values();

        $dateFormat = 'M j Y g:iA';

        $trackings = $trackings->map(function ($track, $index) use ($trackings, $dateFormat) {
            $duration = '';
            $currentDate = $track->status === 'Received' ? $track->date_received : $track->date_released;

            if ($track->status === 'Received') {
                $track->formatted_date = $track->date_received ? $track->date_received->format($dateFormat) : '--:--';
            } else {
                $track->formatted_date = $track->date_released ? $track->date_released->format($dateFormat) : '--:--';
            }

            if ($index > 0) {
                $prevTrack = $trackings->get($index - 1);
                $prevDate = $prevTrack->status === 'Received' ? $prevTrack->date_received : $prevTrack->date_released;

                if ($currentDate && $prevDate) {
                    $diff = $prevDate->diff($currentDate);
                    $duration = sprintf('%02dD %02d:%02d', $diff->days, $diff->h, $diff->i);
                }
            }

            $track->calculated_duration = $duration;
            return $track;
        });

        $trackings = $trackings->reverse()->values();

        return view('client.document-action', compact('tripTicket', 'travelOrder', 'trackings', 'documentNo', 'offices'));
    }

    public function destroy($id)
    {
        $tracking = DocumentTracking::findOrFail($id);
        $tripTicketId = $tracking->trip_ticket_id;
        $tracking->delete();

        return redirect()->route('client.document-tracking.show', $tripTicketId)
            ->with('status', 'Tracking record successfully removed.');
    }



    // GENERATE TO 
    public function generateTo(TripTicket $tripTicket)
    {
        $userOffice = is_object(auth()->user()->office) ? auth()->user()->office->name : auth()->user()->office;
        $currentClientId = auth()->id();
        $lastGlobalTracking = DocumentTracking::where('trip_ticket_id', $tripTicket->id)
            ->latest()
            ->first();
        $comingFrom = $lastGlobalTracking ? $lastGlobalTracking->route_from : 'Origin';

        try {
            DB::transaction(function () use ($tripTicket, $currentClientId, $lastGlobalTracking, $comingFrom) {
                if (empty($tripTicket->to_no)) {
                    $periodKey = Carbon::now()->format('Yn');

                    $counter = DB::table('travel_order_counters')
                        ->where('period_key', $periodKey)
                        ->lockForUpdate()
                        ->first();

                    if (!$counter) {
                        DB::table('travel_order_counters')->insert([
                            'trip_ticket_id' => $tripTicket->id,
                            'period_key' => $periodKey,
                            'current_value' => 1,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        $nextValue = 1;
                    } else {
                        $nextValue = $counter->current_value + 1;
                        DB::table('travel_order_counters')
                            ->where('id', $counter->id)
                            ->update([
                                'trip_ticket_id' => $tripTicket->id,
                                'current_value' => $nextValue,
                                'updated_at' => now(),
                            ]);
                    }

                    $tripTicket->to_no = $periodKey . str_pad($nextValue, 3, '0', STR_PAD_LEFT);
                    $tripTicket->save();
                }

                DocumentTracking::create([
                    'trip_ticket_id' => $tripTicket->id,
                    'client_id' => $currentClientId,
                    'document_no' => $tripTicket->document_no ?? $lastGlobalTracking->document_no ?? '',
                    'route_from' => $comingFrom,
                    'route_to' => 'Not Applicable',
                    'status' => 'Received',
                    'date_released' => null,
                    'date_received' => now(),
                ]);
            });

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to generate TO: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Document received and TO (' . $tripTicket->to_no . ') generated successfully at ' . $userOffice);
    }
}
