<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\DocumentTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NationalToController extends Controller
{
    public function index()
    {
        // 1. Eager load document trackings and their clients just like the local trip tickets
        $travelOrders = Auth::guard('client')->user()
            ->nationalTos()
            ->with(['documentTrackings.client'])
            ->latest()
            ->get();

        // 2. Map through each national order to generate the identical stepper data structure
        $travelOrders->transform(function ($order) {
            $sorted = $order->documentTrackings->sortBy('id')->values();
            $milestones = collect();

            foreach ($sorted as $track) {
                $officeName = $track->client?->office;
                if ($officeName) {
                    $milestones->push($officeName);
                }
            }

            $uniqueRoutes = $milestones->unique()->values();

            $order->stepper_steps = $uniqueRoutes->map(function ($routeName, $index) use ($sorted, $uniqueRoutes) {
                $receivedLog = $sorted->first(function ($track) use ($routeName) {
                    return $track->client?->office === $routeName && $track->status === 'Received';
                });

                $releasedLog = $sorted->first(function ($track) use ($routeName) {
                    return $track->client?->office === $routeName && $track->status === 'Released';
                });

                $dateFormat = 'M j Y g:iA';
                $isFirstNode = ($index === 0);

                if ($isFirstNode && !$receivedLog) {
                    $receivedText = 'Not Applicable';
                } else {
                    $receivedText = $receivedLog?->date_received ? $receivedLog->date_received->format($dateFormat) : '--:--';
                }

                if ($routeName === 'FAS') {
                    $releasedText = 'Not Applicable';
                    $isReleased = !is_null($receivedLog);
                } else {
                    $releasedText = $releasedLog?->date_released ? $releasedLog->date_released->format($dateFormat) : '--:--';
                    $isReleased = !is_null($releasedLog);
                }

                return [
                    'name' => $routeName,
                    'is_released' => $isReleased,
                    'received_at' => $receivedText,
                    'released_at' => $releasedText,
                    'has_next_line' => $index < (count($uniqueRoutes) - 1)
                ];
            });

            return $order;
        });

        return view('client.national-to', compact('travelOrders'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'to_no' => 'nullable|string',
            'date' => 'nullable|date',
            'personnel' => 'required|array',
            'personnel.*.name' => 'nullable|string',
            'personnel.*.position' => 'nullable|string',
            'route' => 'nullable|string',
            'departure' => 'nullable|date',
            'return_date' => 'nullable|date',
            'purpose' => 'nullable|string',
            'rd' => 'nullable|string',
            'oic' => 'nullable|string',
        ]);

        $travelOrder = Auth::guard('client')->user()->nationalTos()->create($validated);

        return back()->with('success', 'National Travel Order created successfully!');
    }

    public function show($id)
    {
        $travelOrder = Auth::guard('client')->user()->nationalTos()->findOrFail($id);

        if (is_string($travelOrder->personnel)) {
            $decoded = json_decode($travelOrder->personnel, true);
            $travelOrder->personnel = is_array($decoded) ? $decoded : [['name' => '', 'position' => '']];
        }

        $travelOrder->date = $travelOrder->date ? $travelOrder->date->format('Y-m-d') : '';
        $travelOrder->departure = $travelOrder->departure ? $travelOrder->departure->format('Y-m-d') : '';
        $travelOrder->return_date = $travelOrder->return_date ? $travelOrder->return_date->format('Y-m-d') : '';

        $offices = Client::whereNotNull('office')
            ->where('office', '!=', '')
            ->distinct()
            ->pluck('office')
            ->toArray();

        $latestTracking = DocumentTracking::where('national_to_id', $travelOrder->id)
            ->latest()
            ->first();

        return view('client.to-print', compact('travelOrder', 'offices', 'latestTracking'));
    }

    public function track(Request $request, $id)
    {
        $travelOrder = Auth::guard('client')->user()->nationalTos()->findOrFail($id);

        $request->validate([
            'document_no' => 'required|string',
            'route' => 'required|string',
            'remarks' => 'nullable|string',
        ]);

        DocumentTracking::create([
            'national_to_id' => $travelOrder->id,
            'trip_ticket_id' => null,
            'is_national' => true,
            'client_id' => Auth::guard('client')->id(),
            'document_no' => $request->document_no,
            'route_from' => 'Client Office',
            'route_to' => $request->route,
            'status' => 'Released',
            'date_released' => now(),
            'date_received' => null,
            'remarks' => $request->remarks,
        ]);

        return back()->with('success', 'Document forwarded successfully!');
    }

    public function update(Request $request, $id)
    {
        $travelOrder = Auth::guard('client')->user()->nationalTos()->findOrFail($id);

        if ($request->has('personnel') && is_string($request->input('personnel'))) {
            $decodedPersonnel = json_decode($request->input('personnel'), true);
            $request->merge(['personnel' => $decodedPersonnel]);
        }

        $validated = $request->validate([
            'to_no' => 'nullable|string',
            'date' => 'nullable|date',
            'personnel' => 'required|array',
            'personnel.*.name' => 'nullable|string',
            'personnel.*.position' => 'nullable|string',
            'route' => 'nullable|string',
            'departure' => 'nullable|date',
            'return_date' => 'nullable|date',
            'purpose' => 'nullable|string',
            'rd' => 'nullable|string',
            'oic' => 'nullable|string',
        ]);

        $travelOrder->update($validated);

        return back()->with('success', 'National Travel Order updated successfully!');
    }

    public function destroy($id)
    {
        $travelOrder = Auth::guard('client')->user()->nationalTos()->findOrFail($id);
        $travelOrder->delete();

        return redirect()
            ->route('client.national-to')
            ->with('success', 'National Travel Order deleted successfully!');
    }
}