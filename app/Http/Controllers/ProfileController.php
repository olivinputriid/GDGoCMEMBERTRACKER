<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Attendance;
use App\Models\Division;
use App\Models\Member;
use App\Models\PointLog;
use App\Models\PortalNotification;
use App\Services\PortalStatsService;
use App\Support\ImageUploader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(public PortalStatsService $statsService) {}

    
    public function show(): View
    {
        $user = Auth::user();

        if ($user?->isAdmin()) {
            $summary = $this->statsService->getOrganizationSummary();
            $recentLogs = PointLog::with('member')->latest('awarded_at')->latest('id')->take(5)->get();

            return view('portal.admin-profile', [
                'admin' => $user,
                'summary' => $summary,
                'attendanceCount' => Attendance::count(),
                'totalPointsDistributed' => (int) Member::sum('points'),
                'recentNotifications' => PortalNotification::latest()->take(5)->get(),
                'recentLogs' => $recentLogs,
            ]);
        }

        $member = $user?->member_id
            ? Member::find($user->member_id)
            : Member::where('email', $user?->email)->first();

        if (! $member) {
            $member = Member::orderBy('id')->firstOrFail();
        }

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
            'isOwnProfile' => true,
        ]);
    }

   
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $avatarUrl = ImageUploader::replace($request->file('avatar'), 'avatars', $user->avatar_url);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'avatar_url' => $avatarUrl,
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return back()->with('toast', ['type' => 'success', 'message' => 'Admin profile updated successfully!']);
    }

    
    public function updateMember(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if (!$user || $user->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:120'],
            'bio'      => ['nullable', 'string', 'max:500'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'avatar'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        // Update user account name
        $userUpdate = ['name' => $validated['name']];
        if (!empty($validated['password'])) {
            $userUpdate['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }
        $user->update($userUpdate);

        
        $member = $user->member_id
            ? \App\Models\Member::find($user->member_id)
            : \App\Models\Member::where('email', $user->email)->first();

        if ($member) {
            $avatarUrl = \App\Support\ImageUploader::replace($request->file('avatar'), 'avatars', $member->avatar_url);
            $member->update([
                'name'      => $validated['name'],
                'bio'       => $validated['bio'] ?? $member->bio,
                'avatar_url' => $avatarUrl,
            ]);
        }

        return back()->with('toast', ['type' => 'success', 'message' => 'Profile updated successfully!']);
    }

    
    public function deleteAvatar(): RedirectResponse
    {
        $user = Auth::user();
        if (! $user) {
            abort(403);
        }

        ImageUploader::delete($user->avatar_url);
        $user->update(['avatar_url' => null]);

        return back()->with('toast', ['type' => 'info', 'message' => 'Admin profile photo removed.']);
    }
}
