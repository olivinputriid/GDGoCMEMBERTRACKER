<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Services\PortalStatsService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(public PortalStatsService $statsService) {}

  
    public function index(): View
    {
        $summary = $this->statsService->getOrganizationSummary();
        $topMembers = $this->statsService->getRankedMembers('overall')->take(3)->values();

        $curriculumDivisions = Division::where('type', 'curriculum')->orderBy('id')->get();
        $nonCurriculumDivisions = Division::where('type', 'non-curriculum')->orderBy('id')->get();

        return view('portal.home', [
            'summary' => $summary,
            'topMembers' => $topMembers,
            'curriculumDivisions' => $curriculumDivisions,
            'nonCurriculumDivisions' => $nonCurriculumDivisions,
        ]);
    }
}
