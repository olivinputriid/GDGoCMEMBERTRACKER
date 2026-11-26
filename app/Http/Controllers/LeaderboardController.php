<?php

namespace App\Http\Controllers;

use App\Services\PortalStatsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    public function __construct(public PortalStatsService $statsService) {}

   
    public function index(Request $request): View
    {
        $period = strtolower((string) $request->query('period', 'overall'));
        if (! in_array($period, ['overall', 'monthly', 'semester', 'annual'], true)) {
            $period = 'overall';
        }

        $ranked = $this->statsService->getRankedMembers($period);

        return view('portal.leaderboard', [
            'period' => $period,
            'podium' => $ranked->take(3)->values(),
            'rankings' => $ranked,
        ]);
    }
}
