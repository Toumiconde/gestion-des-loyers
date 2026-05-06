<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index()
    {
        // Seulement l'admin peut voir les logs
        $logs = ActivityLog::with('user')->latest()->paginate(20);
        return view('activity_logs.index', compact('logs'));
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