<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(): View
    {
        $logs = AuditLog::with('user')
            ->latest('timestamp')
            ->paginate(25);

        return view('audit-logs.index', [
            'logs' => $logs,
        ]);
    }
}
