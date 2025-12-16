<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Vehicle;
use App\Models\Booking;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $months = (int)($request->input('months') ?: 6);
        $summary = Cache::remember("dashboard_summary", 600, function () {
            return [
                'vehicles' => Vehicle::count(),
                'pending' => Booking::where('status', 'pending')->count(),
                'approved' => Booking::where('status', 'approved')->count(),
            ];
        });

        $labels = Cache::remember("dashboard_labels_last_{$months}_months", 600, function () use ($months) {
            $start = now()->copy()->subMonths($months - 1)->startOfMonth();
            $list = [];
            for ($i = 0; $i < $months; $i++) {
                $list[] = $start->copy()->addMonths($i)->locale('id')->isoFormat('MMM');
            }
            return $list;
        });

        $usage = Cache::remember("dashboard_usage_last_{$months}_months", 600, function () use ($months) {
            $start = now()->copy()->subMonths($months - 1)->startOfMonth();
            $end = now()->copy()->endOfMonth();
            $rows = Booking::selectRaw('YEAR(start_time) as y, MONTH(start_time) as m, COUNT(*) as c')
                ->whereBetween('start_time', [$start, $end])
                ->groupBy(DB::raw('YEAR(start_time), MONTH(start_time)'))
                ->orderBy(DB::raw('YEAR(start_time)'))
                ->orderBy(DB::raw('MONTH(start_time)'))
                ->get()
                ->mapWithKeys(function ($r) {
                    return [sprintf('%04d-%02d', $r->y, $r->m) => (int) $r->c];
                })
                ->all();
            $result = [];
            for ($i = 0; $i < $months; $i++) {
                $d = $start->copy()->addMonths($i);
                $key = $d->format('Y-m');
                $result[] = (int)($rows[$key] ?? 0);
            }
            return $result;
        });

        $fromDate = now()->copy()->subMonths($months - 1)->startOfMonth()->toDateString();
        $toDate = now()->copy()->endOfMonth()->toDateString();

        return view('dashboard.index', [
            'summary' => $summary,
            'usage' => $usage,
            'labels' => $labels,
            'months' => $months,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
        ]);
    }
}

