<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;

class AuditController extends Controller
{
    public function dashboard()
    {
        $logs = AuditLog::with('user')
            ->latest()
            ->limit(30)
            ->get();

        return view('audit.dashboard', compact('logs'));
    }
}
