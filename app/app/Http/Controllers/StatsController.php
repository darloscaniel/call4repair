<?php

namespace App\Http\Controllers;

use App\Enums\CallStatus;
use App\Models\Call;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    /**
     * Aggregated metrics for the dashboard charts. Everything is scoped to the
     * calls the current user is allowed to see, so a technician only gets
     * numbers about their own assigned calls.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $canViewAll = $user->can('view all calls');

        // Calls grouped by status (always returns all four buckets).
        $rawByStatus = Call::visibleTo($user)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $byStatus = [];
        foreach (CallStatus::values() as $status) {
            $byStatus[$status] = (int) ($rawByStatus[$status] ?? 0);
        }

        // Calls created per day over the last 14 days (grouped in PHP to stay
        // portable across MySQL/SQLite).
        $start = now()->startOfDay()->subDays(13);
        $perDay = [];
        for ($i = 0; $i < 14; $i++) {
            $perDay[$start->copy()->addDays($i)->toDateString()] = 0;
        }

        Call::visibleTo($user)
            ->where('created_at', '>=', $start)
            ->pluck('created_at')
            ->each(function ($createdAt) use (&$perDay) {
                $day = $createdAt->toDateString();
                if (array_key_exists($day, $perDay)) {
                    $perDay[$day]++;
                }
            });

        $perDayList = [];
        foreach ($perDay as $date => $total) {
            $perDayList[] = ['date' => $date, 'count' => $total];
        }

        // Workload per employee — only meaningful for users who see all calls.
        $perEmployee = [];
        if ($canViewAll) {
            $perEmployee = Employee::withCount('calls')
                ->orderByDesc('calls_count')
                ->get()
                ->filter(fn (Employee $e) => $e->calls_count > 0)
                ->take(8)
                ->map(fn (Employee $e) => ['name' => $e->name, 'count' => $e->calls_count])
                ->values()
                ->all();
        }

        return response()->json([
            'totals' => [
                'calls'     => array_sum($byStatus),
                'open'      => $byStatus['open'],
                'employees' => $canViewAll ? Employee::count() : null,
            ],
            'by_status'    => $byStatus,
            'per_day'      => $perDayList,
            'per_employee' => $perEmployee,
            'can_view_all' => $canViewAll,
        ]);
    }
}
