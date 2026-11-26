<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Attendance;
use App\Models\Member;
use App\Models\PointLog;
use App\Models\PortalNotification;
use App\Support\ImageUploader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ActivityController extends Controller
{
   
    public function index(Request $request): View
    {
        $tab = strtolower((string) $request->query('tab', 'all'));
        $search = trim((string) $request->query('search', ''));

        $query = Activity::with([
            'attendances.member',
        ])->withCount([
            'attendances as present_count' => fn ($q) => $q->whereIn('status', ['Present', 'Late']),
        ])->orderByRaw("CASE WHEN status = 'Upcoming' THEN 0 ELSE 1 END")
            ->orderBy('event_date');

        if ($tab === 'upcoming') {
            $query->where('status', 'Upcoming');
        } elseif ($tab === 'completed') {
            $query->where('status', 'Completed');
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search): void {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        $activities = $query->paginate(12);
        $members = auth()->user()?->isAdmin()
            ? Member::orderBy('name')->get()
            : collect();

        return view('portal.activities', [
            'activities' => $activities,
            'members' => $members,
            'activeTab' => in_array($tab, ['all', 'upcoming', 'completed'], true) ? $tab : 'all',
            'search' => $search,
        ]);
    }


    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:140'],
            'category' => ['required', Rule::in(['Conference', 'Workshop', 'Study Jam', 'Meeting', 'Hackathon', 'Recruitment'])],
            'status' => ['required', Rule::in(['Upcoming', 'Completed'])],
            'event_date' => ['required', 'date'],
            'location' => ['nullable', 'string', 'max:140'],
            'max_attendees' => ['required', 'integer', 'min:1', 'max:5000'],
            'points_reward' => ['required', 'integer', 'min:0', 'max:2000'],
            'description' => ['nullable', 'string', 'max:1000'],
            'banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $iconMap = [
            'Conference' => ['target', '#163CA8'],
            'Workshop' => ['tools', '#D97706'],
            'Study Jam' => ['book', '#16A34A'],
            'Meeting' => ['clipboard', '#64748B'],
            'Hackathon' => ['laptop', '#DC2626'],
            'Recruitment' => ['megaphone', '#163CA8'],
        ];

        [$icon, $accentColor] = $iconMap[$validated['category']] ?? ['calendar', '#163CA8'];

        $slug = Str::slug($validated['title']);
        if (Activity::where('slug', $slug)->exists()) {
            $slug .= '-'.Str::lower(Str::random(4));
        }

        $bannerUrl = ImageUploader::replace($request->file('banner'), 'activities');

        $activity = Activity::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'category' => $validated['category'],
            'status' => $validated['status'],
            'event_date' => $validated['event_date'],
            'location' => $validated['location'] ?? 'Universitas Sriwijaya',
            'max_attendees' => (int) $validated['max_attendees'],
            'initial_attendees' => 0,
            'points_reward' => (int) $validated['points_reward'],
            'icon' => $icon,
            'accent_color' => $accentColor,
            'banner_url' => $bannerUrl,
            'description' => $validated['description'] ?? null,
        ]);

        PortalNotification::create([
            'title' => 'New Activity Created',
            'message' => "{$activity->title} ({$activity->category}) scheduled for {$activity->event_date->format('M d, Y')}.",
            'type' => 'activity',
            'link' => route('activities.index'),
        ]);

        return redirect()->route('activities.index')
            ->with('toast', ['type' => 'success', 'message' => "Activity '{$activity->title}' created!"]);
    }


    public function update(Request $request, Activity $activity): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:140'],
            'category' => ['required', Rule::in(['Conference', 'Workshop', 'Study Jam', 'Meeting', 'Hackathon', 'Recruitment'])],
            'status' => ['required', Rule::in(['Upcoming', 'Completed'])],
            'event_date' => ['required', 'date'],
            'location' => ['nullable', 'string', 'max:140'],
            'max_attendees' => ['required', 'integer', 'min:1', 'max:5000'],
            'points_reward' => ['required', 'integer', 'min:0', 'max:2000'],
            'description' => ['nullable', 'string', 'max:1000'],
            'banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        unset($validated['banner']);
        $validated['banner_url'] = ImageUploader::replace($request->file('banner'), 'activities', $activity->banner_url);

        $activity->update($validated);

        return back()->with('toast', ['type' => 'success', 'message' => "Activity '{$activity->title}' updated!"]);
    }


    public function deleteBanner(Activity $activity): RedirectResponse
    {
        ImageUploader::delete($activity->banner_url);
        $activity->update(['banner_url' => null]);

        return back()->with('toast', ['type' => 'info', 'message' => 'Activity banner image removed.']);
    }


    public function checkIn(Request $request, Activity $activity): RedirectResponse
    {
        $validated = $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'status' => ['required', Rule::in(['Present', 'Late'])],
        ]);

        $member = Member::findOrFail($validated['member_id']);
        $alreadyRecorded = Attendance::where('member_id', $member->id)
            ->where('activity_id', $activity->id)
            ->exists();

        Attendance::updateOrCreate(
            ['member_id' => $member->id, 'activity_id' => $activity->id],
            ['status' => $validated['status'], 'recorded_at' => now()->toDateString()]
        );

        $member->recalculateAttendanceRate();

        if (! $alreadyRecorded && $activity->points_reward > 0) {
            PointLog::create([
                'member_id' => $member->id,
                'activity_id' => $activity->id,
                'title' => $activity->title,
                'category' => $activity->category,
                'points' => $activity->points_reward,
                'awarded_at' => now()->toDateString(),
            ]);

            $member->increment('points', $activity->points_reward);
            $member->increment('contributions_count');
            $member->refresh();
            $member->syncMilestoneAchievements();
        }

        return back()->with('toast', [
            'type' => 'success',
            'message' => "{$member->name} checked in to {$activity->title}!",
        ]);
    }


    public function destroy(Activity $activity): RedirectResponse
    {
        $title = $activity->title;
        ImageUploader::delete($activity->banner_url);
        $activity->delete();

        return redirect()->route('activities.index')
            ->with('toast', ['type' => 'info', 'message' => "Activity '{$title}' deleted."]);
    }
}
