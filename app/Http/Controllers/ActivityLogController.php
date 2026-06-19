<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index()
    {
        $selectedYear = session('selected_year', date('Y'));
        $selectedMonth = session('selected_month');
        
        $query = ActivityLog::with('user')->whereYear('created_at', $selectedYear);
        
        if ($selectedMonth) {
            $query->whereMonth('created_at', $selectedMonth);
        }

        $logs = $query->latest()->paginate(20);
        return view('activity_logs.index', compact('logs', 'selectedYear', 'selectedMonth'));
    }

    public function show(ActivityLog $activityLog)
    {
        return view('activity_logs.show', compact('activityLog'));
    }

    public function create() { abort(403); }
    public function store(Request $request) { abort(403); }
    public function edit(ActivityLog $activityLog) { abort(403); }
    public function update(Request $request, ActivityLog $activityLog) { abort(403); }
    public function destroy(ActivityLog $activityLog) { abort(403); }
}