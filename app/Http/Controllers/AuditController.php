<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AuditController extends Controller
{
    /**
     * Audit Dashboard (with optional month/year filter)
     */
    public function dashboard(Request $request)
    {
        $month = $request->month;
        $year  = $request->year;

        $query = AuditLog::with('user')->latest();


        if ($month && $year) {
            $query->whereMonth('created_at', $month)
                  ->whereYear('created_at', $year);
        }


        $logs = $query->get();

        return view('audit.dashboard', compact('logs', 'month', 'year'));
    }

    /**
     * Printable Audit Logs
     */
    public function print(Request $request)
    {
        $month = $request->month;
        $year  = $request->year;

        $logs = AuditLog::with('user')
            ->when($month && $year, function ($q) use ($month, $year) {
                $q->whereMonth('created_at', $month)
                ->whereYear('created_at', $year);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return view('audit.print', compact('logs', 'month', 'year'));
    }

}
