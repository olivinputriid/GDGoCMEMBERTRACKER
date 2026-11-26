<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Member;
use App\Services\PortalStatsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(public PortalStatsService $statsService) {}

    
    public function index(Request $request): View
    {
        $summary = $this->statsService->getOrganizationSummary();
        $top5Members = $this->statsService->getRankedMembers('overall')->take(5)->values();

        $recentActivities = Activity::withCount([
            'attendances as present_count' => fn ($q) => $q->whereIn('status', ['Present', 'Late']),
        ])
            ->orderByDesc('event_date')
            ->take(4)
            ->get();

        $monthly2025 = $this->statsService->getMonthlyGrowth(2025);
        $monthly2026 = $this->statsService->getMonthlyGrowth(2026);
        $selectedYear = (int) $request->integer('year', 2025);
        if (! in_array($selectedYear, [2025, 2026], true)) {
            $selectedYear = 2025;
        }

        $mostActive = Member::orderByDesc('attendance_rate')->orderByDesc('points')->first();
        $topContributor = Member::orderByDesc('contributions_count')->orderByDesc('points')->first();
        $competitionStar = Member::orderByDesc('competitions_count')->orderByDesc('points')->first();
        $pointLeader = Member::where('status', 'Active')->orderByDesc('points')->first()
            ?? Member::orderByDesc('points')->first();

        return view('portal.dashboard', [
            'summary' => $summary,
            'top5Members' => $top5Members,
            'recentActivities' => $recentActivities,
            'monthlySeries' => [
                2025 => $monthly2025,
                2026 => $monthly2026,
            ],
            'selectedYear' => $selectedYear,
            'highlights' => [
                'mostActive' => $mostActive,
                'topContributor' => $topContributor,
                'competitionStar' => $competitionStar,
                'pointLeader' => $pointLeader,
            ],
        ]);
    }
}
