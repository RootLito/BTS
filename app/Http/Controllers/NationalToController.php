<?php

namespace App\Http\Controllers;

use App\Models\NationalTo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NationalToController extends Controller
{
    /**
     * Display a listing of the national travel orders for the logged-in client.
     */
    public function index()
    {
        $travelOrders = Auth::guard('client')->user()->nationalTos()->latest()->get();

        // Return to your view with the data
        return view('client.national-to', compact('travelOrders'));
    }

    /**
     * Store a newly created travel order in storage.
     */
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

    /**
     * Display the specified travel order.
     */
    public function show($id)
    {
        $travelOrder = Auth::guard('client')->user()->nationalTos()->findOrFail($id);

        // 1. Fix the double-encoded personnel string
        if (is_string($travelOrder->personnel)) {
            $decoded = json_decode($travelOrder->personnel, true);
            // If it successfully decodes into an array, assign it back
            $travelOrder->personnel = is_array($decoded) ? $decoded : [['name' => '', 'position' => '']];
        }

        // 2. Format Carbon instances to YYYY-MM-DD strings for Flux date inputs
        $travelOrder->date = $travelOrder->date ? $travelOrder->date->format('Y-m-d') : '';
        $travelOrder->departure = $travelOrder->departure ? $travelOrder->departure->format('Y-m-d') : '';
        $travelOrder->return_date = $travelOrder->return_date ? $travelOrder->return_date->format('Y-m-d') : '';

        return view('client.to-print', compact('travelOrder'));
    }

    /**
     * Update the specified travel order in storage.
     */
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

        return back()
            ->with('success', 'National Travel Order updated successfully!');
    }

    /**
     * Remove the specified travel order from storage.
     */
    public function destroy($id)
    {
        $travelOrder = Auth::guard('client')->user()->nationalTos()->findOrFail($id);
        $travelOrder->delete();

        return redirect()
            ->route('client.national-to')
            ->with('success', 'National Travel Order deleted successfully!');
    }
}