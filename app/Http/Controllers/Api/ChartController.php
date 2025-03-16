<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Todo;
use App\Http\Controllers\Controller;

class ChartController extends Controller
{
    public function getChartData(Request $request)
    {
        $type = $request->query('type');

        if ($type === 'status') {
            return response()->json([
                'status_summary' => Todo::groupBy('status')
                    ->selectRaw('status, COUNT(*) as count')
                    ->pluck('count', 'status')
            ]);
        }

        if ($type === 'priority') {
            return response()->json([
                'priority_summary' => Todo::groupBy('priority')
                    ->selectRaw('priority, COUNT(*) as count')
                    ->pluck('count', 'priority')
            ]);
        }

        if ($type === 'assignee') {
            $assigneeSummary = Todo::groupBy('assignee')
                ->selectRaw('assignee, COUNT(*) as total_todos, 
                             SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as total_pending_todos,
                             SUM(CASE WHEN status = "completed" THEN time_tracked ELSE 0 END) as total_timetracked_completed_todos')
                ->get()
                ->keyBy('assignee')
                ->toArray();

            return response()->json(['assignee_summary' => $assigneeSummary]);
        }

        return response()->json(['error' => 'Invalid type'], 400);
    }
}
