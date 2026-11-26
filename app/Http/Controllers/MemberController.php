<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Member;
use App\Models\PointLog;
use App\Models\PortalNotification;
use App\Services\PortalStatsService;
use App\Support\ImageUploader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function __construct(public PortalStatsService $statsService) {}

    
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $divisionSlug = (string) $request->query('division', 'all');
        $status = (string) $request->query('status', 'All');
        $viewMode = (string) $request->query('view', 'list');

        $query = Member::with('division')->orderByDesc('points')->orderBy('member_code');

        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('member_code', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('role_title', 'like', "%{$search}%")
                    ->orWhereHas('division', fn ($dq) => $dq->where('name', 'like', "%{$search}%"));
            });
        }

        if ($divisionSlug !== '' && $divisionSlug !== 'all') {
            $query->whereHas('division', fn ($dq) => $dq->where('slug', $divisionSlug)->orWhere('name', $divisionSlug));
        }

        if (in_array($status, ['Active', 'Inactive', 'Alumni'], true)) {
            $query->where('status', $status);
        }

        $members = $query->paginate(15);
        $totalMembers = Member::count();
        $divisions = Division::orderBy('type')->orderBy('name')->get();

        return view('portal.members', [
            'members' => $members,
            'totalMembers' => $totalMembers,
            'divisions' => $divisions,
            'filters' => [
                'search' => $search,
                'division' => $divisionSlug,
                'status' => $status,
                'view' => $viewMode,
            ],
        ]);
    }

    /**
     * Store a newly added member in the database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'member_code' => ['nullable', 'string', 'max:20', 'unique:members,member_code'],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', 'unique:members,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'division_id' => ['required', 'exists:divisions,id'],
            'role_title' => ['required', 'string', 'max:80'],
            'batch' => ['required', 'integer', 'min:2018', 'max:2030'],
            'points' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'attendance_rate' => ['nullable', 'integer', 'min:0', 'max:100'],
            'contributions_count' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'competitions_count' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'status' => ['required', Rule::in(['Active', 'Inactive', 'Alumni'])],
            'bio' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $memberCode = trim((string) ($validated['member_code'] ?? ''));
        if ($memberCode === '') {
            $nextNumber = (Member::max('id') ?? 0) + 1;
            $memberCode = 'GDG'.str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
            while (Member::where('member_code', $memberCode)->exists()) {
                $nextNumber++;
                $memberCode = 'GDG'.str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
            }
        }

        $palette = ['#163CA8', '#EA4335', '#34A853', '#F97316', '#6366F1', '#0EA5E9', '#8B5CF6'];
        $points = (int) ($validated['points'] ?? 0);
        $avatarUrl = ImageUploader::replace($request->file('avatar'), 'avatars');

        $member = Member::create([
            'member_code' => strtoupper($memberCode),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'division_id' => $validated['division_id'],
            'role_title' => $validated['role_title'],
            'batch' => (int) $validated['batch'],
            'points' => $points,
            'attendance_rate' => (int) ($validated['attendance_rate'] ?? 100),
            'contributions_count' => (int) ($validated['contributions_count'] ?? 0),
            'competitions_count' => (int) ($validated['competitions_count'] ?? 0),
            'status' => $validated['status'],
            'avatar_color' => $palette[array_rand($palette)],
            'avatar_url' => $avatarUrl,
            'bio' => $validated['bio'] ?? null,
        ]);

        if ($points > 0) {
            PointLog::create([
                'member_id' => $member->id,
                'title' => 'Initial Onboarding Points',
                'category' => 'Onboarding',
                'points' => $points,
                'awarded_at' => now()->toDateString(),
            ]);
        }

        $member->syncMilestoneAchievements();

        PortalNotification::create([
            'title' => 'New Member Added',
            'message' => "{$member->name} ({$member->member_code}) was added to {$member->division->name}.",
            'type' => 'member',
            'link' => route('members.show', $member),
        ]);

        return redirect()->route('members.index')
            ->with('toast', ['type' => 'success', 'message' => "Member {$member->name} added successfully!"]);
    }

    /**
     * Display the specified member's profile and activity history.
     */
    public function show(Member $member): View
    {
        $member->load(['division', 'achievements', 'pointLogs.activity']);
        $rank = $this->statsService->getMemberRank($member);
        $divisions = Division::orderBy('name')->get();

        $step = 500;
        $nextMilestone = max(500, (int) (floor($member->points / $step) + 1) * $step);
        $remainingPoints = max(0, $nextMilestone - $member->points);
        $milestonePercent = (int) min(100, round(($member->points / $nextMilestone) * 100));

        return view('portal.profile', [
            'member' => $member,
            'rank' => $rank,
            'divisions' => $divisions,
            'nextMilestone' => $nextMilestone,
            'remainingPoints' => $remainingPoints,
            'milestonePercent' => $milestonePercent,
            'isOwnProfile' => auth()->user()?->member_id === $member->id,
        ]);
    }

    /**
     * Update the specified member in the database.
     */
    public function update(Request $request, Member $member): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', Rule::unique('members', 'email')->ignore($member->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'division_id' => ['required', 'exists:divisions,id'],
            'role_title' => ['required', 'string', 'max:80'],
            'batch' => ['required', 'integer', 'min:2018', 'max:2030'],
            'status' => ['required', Rule::in(['Active', 'Inactive', 'Alumni'])],
            'contributions_count' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'competitions_count' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'bio' => ['nullable', 'string', 'max:500'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        unset($validated['avatar']);
        $validated['avatar_url'] = ImageUploader::replace($request->file('avatar'), 'avatars', $member->avatar_url);

        $member->update($validated);
        $member->syncMilestoneAchievements();

        return back()->with('toast', ['type' => 'success', 'message' => "{$member->name}'s profile updated!"]);
    }

    /**
     * Delete a member's uploaded avatar photo.
     */
    public function deleteAvatar(Member $member): RedirectResponse
    {
        ImageUploader::delete($member->avatar_url);
        $member->update(['avatar_url' => null]);

        return back()->with('toast', ['type' => 'info', 'message' => "{$member->name}'s profile photo removed."]);
    }

    /**
     * Award points and log point activity for a member.
     */
    public function awardPoints(Request $request, Member $member): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:140'],
            'category' => ['required', 'string', 'max:60'],
            'points' => ['required', 'integer', 'min:1', 'max:5000'],
            'awarded_at' => ['required', 'date'],
        ]);

        PointLog::create([
            'member_id' => $member->id,
            'title' => $validated['title'],
            'category' => $validated['category'],
            'points' => (int) $validated['points'],
            'awarded_at' => $validated['awarded_at'],
        ]);

        $member->increment('points', (int) $validated['points']);

        if (in_array($validated['category'], ['Competition Win', 'Competition'], true)) {
            $member->increment('competitions_count');
        } else {
            $member->increment('contributions_count');
        }

        $member->refresh();
        $member->syncMilestoneAchievements();

        PortalNotification::create([
            'title' => 'Points Awarded',
            'message' => "{$member->name} earned +{$validated['points']} pts for {$validated['title']}.",
            'type' => 'member',
            'link' => route('members.show', $member),
        ]);

        return back()->with('toast', ['type' => 'success', 'message' => "+{$validated['points']} pts awarded to {$member->name}!"]);
    }

    /**
     * Remove the specified member from the database.
     */
    public function destroy(Member $member): RedirectResponse
    {
        $name = $member->name;
        ImageUploader::delete($member->avatar_url);
        $member->delete();

        return redirect()->route('members.index')
            ->with('toast', ['type' => 'info', 'message' => "Member {$name} removed."]);
    }
}
