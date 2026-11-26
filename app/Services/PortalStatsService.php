<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Member;
use App\Models\PointLog;
use Illuminate\Support\Collection;

class PortalStatsService
{
    
    public function getOrganizationSummary(): array
    {
        $totalMembers = Member::count();
        $activeMembers = Member::where('status', 'Active')->count();
        $avgAttendance = (int) round((float) (Member::avg('attendance_rate') ?? 0));
        $totalEvents = Activity::count();
        $totalContributions = (int) Member::sum('contributions_count');
        $totalCompetitions = (int) Member::sum('competitions_count');
        $achievementsAwarded = (int) Member::withCount('achievements')->get()->sum('achievements_count');

        $currentYear = (int) now()->format('Y');
        $currentMonth = (int) now()->format('n');

        $thisMonthPoints = (int) PointLog::whereYear('awarded_at', $currentYear)
            ->whereMonth('awarded_at', $currentMonth)
            ->sum('points');

        $prevDate = now()->subMonth();
        $lastMonthPoints = (int) PointLog::whereYear('awarded_at', (int) $prevDate->format('Y'))
            ->whereMonth('awarded_at', (int) $prevDate->format('n'))
            ->sum('points');

        $growthPercent = $lastMonthPoints > 0
            ? (int) round((($thisMonthPoints - $lastMonthPoints) / $lastMonthPoints) * 100)
            : ($thisMonthPoints > 0 ? 100 : 0);

        $nextEvent = Activity::where('status', 'Upcoming')
            ->orderBy('event_date')
            ->first();

        return [
            'totalMembers' => $totalMembers,
            'activeMembers' => $activeMembers,
            'avgAttendance' => $avgAttendance,
            'totalEvents' => $totalEvents,
            'totalContributions' => $totalContributions,
            'totalCompetitions' => $totalCompetitions,
            'achievementsAwarded' => $achievementsAwarded,
            'thisMonthPoints' => $thisMonthPoints,
            'growthPercent' => $growthPercent,
            'nextEvent' => $nextEvent,
        ];
    }

    
    public function getMonthlyGrowth(int $year): array
    {
        $colors = ['#1E40AF', '#C94A38', '#D97706', '#2F9E57'];
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        $logs = PointLog::whereYear('awarded_at', $year)->get(['points', 'awarded_at']);

        $byMonth = array_fill(1, 12, 0);
        foreach ($logs as $log) {
            $monthIndex = (int) $log->awarded_at->format('n');
            $byMonth[$monthIndex] += (int) $log->points;
        }

       
        $lastActiveMonth = 9;
        for ($m = 12; $m >= 10; $m--) {
            if ($byMonth[$m] > 0) {
                $lastActiveMonth = $m;
                break;
            }
        }

        $series = [];
        for ($m = 1; $m <= $lastActiveMonth; $m++) {
            $series[] = [
                'label' => $months[$m - 1],
                'points' => $byMonth[$m],
                'color' => $colors[($m - 1) % count($colors)],
            ];
        }

        return $series;
    }

    /**
     * Get ranked members for a given period ('overall', 'monthly', 'semester', 'annual').
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function getRankedMembers(string $period = 'overall'): Collection
    {
        $members = Member::with(['division', 'achievements'])->get();

        $periodPoints = [];
        if ($period !== 'overall') {
            $query = PointLog::query();

            if ($period === 'monthly') {
                $query->whereYear('awarded_at', (int) now()->format('Y'))
                    ->whereMonth('awarded_at', (int) now()->format('n'));
            } elseif ($period === 'semester') {
                $query->where('awarded_at', '>=', now()->subMonths(6)->startOfDay());
            } elseif ($period === 'annual') {
                $query->whereYear('awarded_at', (int) now()->format('Y'));
            }

            $periodPoints = $query->selectRaw('member_id, SUM(points) as total_points')
                ->groupBy('member_id')
                ->pluck('total_points', 'member_id')
                ->all();
        }

        $ranked = $members->map(function (Member $member) use ($period, $periodPoints): array {
            $score = $period === 'overall'
                ? (int) $member->points
                : (int) ($periodPoints[$member->id] ?? 0);

            return [
                'member' => $member,
                'score' => $score,
            ];
        })->sortByDesc(function (array $entry): array {
            return [$entry['score'], $entry['member']->points, -$entry['member']->id];
        })->values();

        $maxScore = max(1, (int) ($ranked->first()['score'] ?? 1));

        return $ranked->map(function (array $entry, int $index) use ($maxScore): array {
            return [
                'rank' => $index + 1,
                'member' => $entry['member'],
                'score' => $entry['score'],
                'progress' => (int) round(($entry['score'] / $maxScore) * 100),
            ];
        });
    }

    /**
     * Compute a member's overall rank among all members.
     */
    public function getMemberRank(Member $member): int
    {
        $higherCount = Member::where('points', '>', $member->points)
            ->orWhere(function ($query) use ($member): void {
                $query->where('points', $member->points)
                    ->where('id', '<', $member->id);
            })
            ->count();

        return $higherCount + 1;
    }
}
