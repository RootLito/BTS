<?php

namespace App\Http\Controllers;

use App\Models\TripTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ClientController extends Controller
{
    public function index()
    {
        $clientId = Auth::id();
        $currentYear = Carbon::now()->year;

        $totalTrips = TripTicket::where('client_id', $clientId)
            ->whereYear('start_date', $currentYear)
            ->count();

        $lastYearTrips = TripTicket::where('client_id', $clientId)
            ->whereYear('start_date', $currentYear - 1)
            ->count();

        // $percentageChange = $lastYearTrips > 0 
        //     ? (($totalTrips - $lastYearTrips) / $lastYearTrips) * 100 
        //     : 0;
        if ($lastYearTrips == 0) {
            $percentageChange = $totalTrips > 0 ? 100 : 0;
        } else {
            $percentageChange = (($totalTrips - $lastYearTrips) / $lastYearTrips) * 100;
        }

        $pendingTrips = TripTicket::where('client_id', $clientId)
            ->whereYear('start_date', $currentYear)
            ->where('status', 'pending')
            ->count();

        $completedTripsCount = TripTicket::where('client_id', $clientId)
            ->whereYear('start_date', $currentYear)
            ->where('status', 'completed')
            ->count();

        $recentTrips = TripTicket::where('client_id', $clientId)
            ->where('status', 'completed')
            ->latest('start_date')
            ->take(5)
            ->get();

        $monthlyData = TripTicket::where('client_id', $clientId)
            ->whereYear('start_date', $currentYear)
            ->select(DB::raw('MONTH(start_date) as month'), DB::raw('count(*) as count'))
            ->groupBy('month')
            ->pluck('count', 'month')
            ->toArray();

        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartData[] = $monthlyData[$i] ?? 0;
        }

        $maxVal = count($chartData) > 0 ? max($chartData) : 0;
        $peakMonthIndex = array_search($maxVal, $chartData);
        $peakMonthName = Carbon::create()->month($peakMonthIndex + 1)->format('M');

        return view('client.home', compact(
            'totalTrips',
            'percentageChange',
            'pendingTrips',
            'completedTripsCount',
            'recentTrips',
            'chartData',
            'maxVal',
            'peakMonthName'
        ));
    }
}