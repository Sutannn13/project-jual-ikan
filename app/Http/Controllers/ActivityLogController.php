<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by model type
        if ($request->filled('model')) {
            $query->where('model', $request->model);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->to);
        }

        $logs = $query->paginate(25)->withQueryString();

        // Stats
        $stats = [
            'today'     => ActivityLog::whereDate('created_at', today())->count(),
            'this_week' => ActivityLog::where('created_at', '>=', now()->startOfWeek())->count(),
            'total'     => ActivityLog::count(),
        ];

        // Unique actions for filter dropdown
        $actions = ActivityLog::distinct()->pluck('action')->sort()->values();
        $models  = ActivityLog::distinct()->whereNotNull('model')->pluck('model')->sort()->values();
        $users   = User::whereIn('id', ActivityLog::distinct()->pluck('user_id'))->get(['id', 'name', 'role']);

        return view('admin.activity-log.index', compact('logs', 'stats', 'actions', 'models', 'users'));
    }
}
