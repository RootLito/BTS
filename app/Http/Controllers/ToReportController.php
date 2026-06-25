<?php

namespace App\Http\Controllers;

use App\Models\TripTicket;
use App\Models\ToReport;
use App\Models\Client;
use App\Models\NationalTo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\ReportExport;
use Maatwebsite\Excel\Facades\Excel;

class ToReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::guard('client')->user();
        $type = $request->get('type', 'local');

        $relations = $user->office === 'FAS' ? ['toReport', 'client'] : ['toReport'];

        if ($type === 'national') {
            $query = NationalTo::with($relations);
            $query->whereYear('departure', '>=', 2025);

            if ($user->office === 'FAS') {
                $query->whereDate('return_date', '<', now()->toDateString());
            } else {
                $query->where('client_id', $user->id);
            }

            $query->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->get('search');
                $q->where(function ($subQ) use ($search) {
                    $subQ->where('to_no', 'like', "%{$search}%")
                        ->orWhere('route', 'like', "%{$search}%")
                        ->orWhere('purpose', 'like', "%{$search}%");
                });
            });

            $query->when($request->filled('office') && $user->office === 'FAS', function ($q) use ($request) {
                $office = $request->get('office');
                $q->whereHas('client', function ($subQ) use ($office) {
                    $subQ->where('office', $office);
                });
            });

            $query->when($request->filled('year'), function ($q) use ($request) {
                $q->whereYear('departure', $request->get('year'));
            });

            $query->when($request->filled('month'), function ($q) use ($request) {
                $q->whereMonth('departure', $request->get('month'));
            });

        } else {
            $query = TripTicket::with($user->office === 'FAS' ? ['toReport', 'user'] : ['toReport']);
            $query->whereYear('start_date', '>=', 2025);

            if ($user->office === 'FAS') {
                $query->whereDate('end_date', '<', now()->toDateString());
            } else {
                $query->where('client_id', $user->id);
            }

            $query->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->get('search');
                $q->where(function ($subQ) use ($search) {
                    $subQ->where('to_no', 'like', "%{$search}%")->orWhere('destination', 'like', "%{$search}%");
                });
            });

            $query->when($request->filled('office') && $user->office === 'FAS', function ($q) use ($request) {
                $q->whereHas('user', function ($subQ) use ($request) {
                    $subQ->where('office', $request->get('office'));
                });
            });

            $query->when($request->filled('year'), function ($q) {
                $q->whereYear('start_date', request('year'));
            });
            $query->when($request->filled('month'), function ($q) {
                $q->whereMonth('start_date', request('month'));
            });
        }

        $tickets = $query->orderBy($type === 'national' ? 'departure' : 'start_date', 'desc')
            ->paginate(10)
            ->withQueryString();

        $offices = Client::whereNotNull('office')->where('office', '!=', '')->distinct()->orderBy('office', 'asc')->pluck('office');

        return view('client.to-report', compact('tickets', 'offices', 'type'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:local,national',
            'trip_ticket_id' => 'required_if:type,local|exists:trip_tickets,id',
            'national_to_id' => 'required_if:type,national|exists:national_to,id',
            'outputs' => 'required|string|min:5',
        ]);

        $user = Auth::guard('client')->user();

        if ($validated['type'] === 'national') {
            $ticketQuery = NationalTo::query();
            if ($user->office !== 'FAS') {
                $ticketQuery->where('client_id', $user->id);
            }
            $ticket = $ticketQuery->findOrFail($validated['national_to_id']);

            $ticket->toReport()->create([
                'outputs' => $validated['outputs'],
            ]);
        } else {
            $ticketQuery = TripTicket::query();
            if ($user->office !== 'FAS') {
                $ticketQuery->where('client_id', $user->id);
            }
            $ticket = $ticketQuery->findOrFail($validated['trip_ticket_id']);

            $ticket->toReport()->create([
                'outputs' => $validated['outputs'],
            ]);
        }

        return redirect()->back()->with('success', 'Travel report submitted successfully!');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'outputs' => 'required|string|min:5',
        ]);

        $user = Auth::guard('client')->user();

        $report = ToReport::where(function ($query) use ($user) {
            $query->whereHas('tripTicket', function ($q) use ($user) {
                if ($user->office !== 'FAS') {
                    $q->where('client_id', $user->id);
                }
            })->orWhereHas('nationalTo', function ($q) use ($user) {
                if ($user->office !== 'FAS') {
                    $q->where('client_id', $user->id);
                }
            });
        })->findOrFail($id);

        $report->update([
            'outputs' => $validated['outputs'],
        ]);

        return redirect()->back()->with('success', 'Travel report updated successfully!');
    }

    public function destroy($id)
    {
        $user = Auth::guard('client')->user();

        $report = ToReport::where(function ($query) use ($user) {
            $query->whereHas('tripTicket', function ($q) use ($user) {
                if ($user->office !== 'FAS') {
                    $q->where('client_id', $user->id);
                }
            })->orWhereHas('nationalTo', function ($q) use ($user) {
                if ($user->office !== 'FAS') {
                    $q->where('client_id', $user->id);
                }
            });
        })->findOrFail($id);

        $report->delete();

        return redirect()->back()->with('success', 'Travel report deleted successfully!');
    }

    public function export(Request $request)
    {
        $user = Auth::guard('client')->user();
        $type = $request->get('type', 'local');

        if ($type === 'national') {
            $relations = $user->office === 'FAS' ? ['toReport', 'client'] : ['toReport'];
            $query = NationalTo::with($relations)->whereYear('departure', '>=', 2025);

            if ($user->office === 'FAS') {
                $query->has('toReport');
            } else {
                $query->where('client_id', $user->id)
                    ->whereDate('return_date', '<', now()->toDateString());
            }

            $query->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->get('search');
                $q->where(function ($subQ) use ($search) {
                    $subQ->where('to_no', 'like', "%{$search}%")
                        ->orWhere('route', 'like', "%{$search}%");
                });
            });

            $query->when($request->filled('year'), function ($q) use ($request) {
                $q->whereYear('departure', $request->get('year'));
            });

            $query->when($request->filled('month'), function ($q) use ($request) {
                $q->whereMonth('departure', $request->get('month'));
            });

        } else {
            $relations = $user->office === 'FAS' ? ['toReport', 'user'] : ['toReport'];
            $query = TripTicket::with($relations)->whereYear('start_date', '>=', 2025);

            if ($user->office === 'FAS') {
                $query->has('toReport');
            } else {
                $query->where('client_id', $user->id)
                    ->whereDate('end_date', '<', now()->toDateString());
            }

            $query->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->get('search');
                $q->where(function ($subQ) use ($search) {
                    $subQ->where('to_no', 'like', "%{$search}%")
                        ->orWhere('destination', 'like', "%{$search}%");
                });
            });

            $query->when($request->filled('year'), function ($q) use ($request) {
                $q->whereYear('start_date', $request->get('year'));
            });

            $query->when($request->filled('month'), function ($q) use ($request) {
                $q->whereMonth('start_date', $request->get('month'));
            });
        }

        $tickets = $query->orderBy($type === 'national' ? 'departure' : 'start_date', 'desc')->get();

        return Excel::download(new ReportExport($tickets, $type), 'travel_report_' . now()->format('Y-m-d') . '.xlsx');
    }
}