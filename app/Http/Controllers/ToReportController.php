<?php

namespace App\Http\Controllers;

use App\Models\TripTicket;
use App\Models\ToReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ToReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard('client')->user();
        $sortDirection = $request->get('sort') === 'oldest' ? 'asc' : 'desc';

        $query = TripTicket::with($user->office === 'FAS' ? ['toReport', 'user'] : ['toReport']);

        if ($user->office !== 'FAS') {
            $query->where('client_id', $user->id);
        }

        $tripTickets = $query->when($request->filled('search'), function ($q) use ($request) {
            $search = $request->get('search');
            $q->where(function ($subQ) use ($search) {
                $subQ->where('to_no', 'like', "%{$search}%")
                    ->orWhere('destination', 'like', "%{$search}%");
            });
        })
            ->orderBy('start_date', $sortDirection)
            ->paginate(10)
            ->withQueryString();

        return view('client.to-report', compact('tripTickets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'trip_ticket_id' => 'required|exists:trip_tickets,id',
            'outputs' => 'required|string|min:5',
        ]);

        $user = Auth::guard('client')->user();

        $ticketQuery = TripTicket::query();
        if ($user->office !== 'FAS') {
            $ticketQuery->where('client_id', $user->id);
        }

        $ticket = $ticketQuery->findOrFail($validated['trip_ticket_id']);

        $ticket->toReport()->create([
            'outputs' => $validated['outputs'],
        ]);

        return redirect()->back()->with('success', 'Travel report submitted successfully!');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'outputs' => 'required|string|min:5',
        ]);

        $user = Auth::guard('client')->user();

        $report = ToReport::whereHas('tripTicket', function ($query) use ($user) {
            if ($user->office !== 'FAS') {
                $query->where('client_id', $user->id);
            }
        })->findOrFail($id);

        $report->update([
            'outputs' => $validated['outputs'],
        ]);

        return redirect()->back()->with('success', 'Travel report updated successfully!');
    }

    public function destroy($id)
    {
        $user = Auth::guard('client')->user();

        $report = ToReport::whereHas('tripTicket', function ($query) use ($user) {
            if ($user->office !== 'FAS') {
                $query->where('client_id', $user->id);
            }
        })->findOrFail($id);

        $report->delete();

        return redirect()->back()->with('success', 'Travel report deleted successfully!');
    }
}