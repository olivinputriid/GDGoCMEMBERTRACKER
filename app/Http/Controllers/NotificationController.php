<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Member;
use App\Models\PortalNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    
    public function markRead(PortalNotification $notification): JsonResponse|RedirectResponse
    {
        $notification->update(['is_read' => true]);

        if (request()->expectsJson()) {
            return response()->json([
                'unreadCount' => PortalNotification::where('is_read', false)->count(),
            ]);
        }

        return $notification->link
            ? redirect($notification->link)
            : back();
    }

   
    public function markAllRead(): JsonResponse|RedirectResponse
    {
        PortalNotification::where('is_read', false)->update(['is_read' => true]);

        if (request()->expectsJson()) {
            return response()->json(['unreadCount' => 0]);
        }

        return back()->with('toast', ['type' => 'info', 'message' => 'All notifications marked as read.']);
    }

 
    public function search(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        if ($q === '') {
            return response()->json(['members' => [], 'activities' => []]);
        }

        $members = Member::with('division')
            ->where(function ($query) use ($q): void {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('member_code', 'like', "%{$q}%")
                    ->orWhereHas('division', fn ($dq) => $dq->where('name', 'like', "%{$q}%"));
            })
            ->orderByDesc('points')
            ->take(5)
            ->get()
            ->map(fn (Member $member): array => [
                'id' => $member->id,
                'name' => $member->name,
                'subtitle' => "{$member->member_code} • {$member->division->name}",
                'points' => $member->points,
                'url' => route('members.show', $member),
            ]);

        $activities = Activity::where('title', 'like', "%{$q}%")
            ->orWhere('category', 'like', "%{$q}%")
            ->orderByDesc('event_date')
            ->take(5)
            ->get()
            ->map(fn (Activity $activity): array => [
                'id' => $activity->id,
                'title' => $activity->title,
                'subtitle' => "{$activity->category} • {$activity->event_date->format('M d, Y')}",
                'status' => $activity->status,
                'url' => route('activities.index', ['search' => $activity->title]),
            ]);

        return response()->json([
            'members' => $members,
            'activities' => $activities,
        ]);
    }
}
