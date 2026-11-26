<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Attendance;
use App\Models\Member;
use App\Models\PointLog;
use App\Models\PortalNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    
    public function index(Request $request): View
    {
        $user = $request->user();
        $isAdmin = $user?->isAdmin() ?? false;
        $activityFilter = (string) $request->query('activity', 'all');
        $search = trim((string) $request->query('search', ''));

        $currentMember = null;
        if (! $isAdmin) {
            $currentMember = $user?->member_id
                ? Member::find($user->member_id)
                : Member::where('email', $user?->email)->first();
        }

        $baseQuery = Attendance::with(['member.division', 'activity'])
            ->orderByDesc('recorded_at')
            ->orderByDesc('id');

        if (! $isAdmin) {
            if ($currentMember) {
                $baseQuery->where('member_id', $currentMember->id);
            } else {
                $baseQuery->whereRaw('1 = 0');
            }
        }

        if ($activityFilter !== '' && $activityFilter !== 'all') {
            $baseQuery->where('activity_id', (int) $activityFilter);
        }

        if ($search !== '') {
            $baseQuery->where(function ($q) use ($search): void {
                $q->whereHas('member', fn ($mq) => $mq->where('name', 'like', "%{$search}%")->orWhere('member_code', 'like', "%{$search}%"))
                    ->orWhereHas('activity', fn ($aq) => $aq->where('title', 'like', "%{$search}%"));
            });
        }

        $attendances = $baseQuery->get();

        $counts = [
            'Present' => $attendances->where('status', 'Present')->count(),
            'Late' => $attendances->where('status', 'Late')->count(),
            'Excused' => $attendances->where('status', 'Excused')->count(),
            'Absent' => $attendances->where('status', 'Absent')->count(),
        ];

        $activities = Activity::orderByDesc('event_date')->get();
        $members = $isAdmin ? Member::orderBy('member_code')->get() : collect();
        $memberOverview = $isAdmin
            ? Member::orderByDesc('attendance_rate')->orderBy('member_code')->take(10)->get()
            : collect();

        return view('portal.attendance', [
            'attendances' => $attendances,
            'counts' => $counts,
            'activities' => $activities,
            'members' => $members,
            'memberOverview' => $memberOverview,
            'currentMember' => $currentMember,
            'selectedActivity' => $activityFilter,
            'search' => $search,
        ]);
    }

    
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'activity_id' => ['required', 'exists:activities,id'],
            'status' => ['required', Rule::in(['Present', 'Late', 'Excused', 'Absent'])],
            'recorded_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:255'],
            'award_points' => ['nullable', 'boolean'],
        ]);

        $existing = Attendance::where('member_id', $validated['member_id'])
            ->where('activity_id', $validated['activity_id'])
            ->first();

        $attendance = Attendance::updateOrCreate(
            [
                'member_id' => $validated['member_id'],
                'activity_id' => $validated['activity_id'],
            ],
            [
                'status' => $validated['status'],
                'recorded_at' => $validated['recorded_at'],
                'notes' => $validated['notes'] ?? null,
            ]
        );

        $member = Member::findOrFail($validated['member_id']);
        $activity = Activity::findOrFail($validated['activity_id']);
        $member->recalculateAttendanceRate();

        $shouldAward = $request->boolean('award_points', true)
            && ! $existing
            && in_array($validated['status'], ['Present', 'Late'], true)
            && $activity->points_reward > 0;

        if ($shouldAward) {
            PointLog::create([
                'member_id' => $member->id,
                'activity_id' => $activity->id,
                'title' => $activity->title,
                'category' => $activity->category,
                'points' => $activity->points_reward,
                'awarded_at' => $validated['recorded_at'],
            ]);

            $member->increment('points', $activity->points_reward);
            $member->increment('contributions_count');
            $member->refresh();
            $member->syncMilestoneAchievements();
        }

        PortalNotification::create([
            'title' => 'Attendance Recorded',
            'message' => "{$member->name} marked {$attendance->status} for {$activity->title}.",
            'type' => 'attendance',
            'link' => route('attendance.index'),
        ]);

        return back()->with('toast', [
            'type' => 'success',
            'message' => "Attendance saved for {$member->name} ({$attendance->status}).",
        ]);
    }

    
    public function update(Request $request, Attendance $attendance): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['Present', 'Late', 'Excused', 'Absent'])],
            'notes' => ['nullable', 'string', 'max:255'],
        ]);

        $attendance->update($validated);
        $attendance->member->recalculateAttendanceRate();

        return back()->with('toast', [
            'type' => 'success',
            'message' => "Updated {$attendance->member->name}'s status to {$attendance->status}.",
        ]);
    }

    
    public function destroy(Attendance $attendance): RedirectResponse
    {
        $member = $attendance->member;
        $attendance->delete();
        $member->recalculateAttendanceRate();

        return back()->with('toast', ['type' => 'info', 'message' => 'Attendance record removed.']);
    }
}
