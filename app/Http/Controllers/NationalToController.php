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
        $travelOrders = Auth::guard('client')->user()->nationalTos()->latest()->get();
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

        return response()->json([
            'success' => true,
            'message' => 'National Travel Order created successfully!',
            'data' => $travelOrder
        ]);
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