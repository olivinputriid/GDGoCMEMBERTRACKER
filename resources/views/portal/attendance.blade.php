@extends('layouts.portal')

@section('title', 'Attendance — GDGoC UNSRI Portal')
@section('page_title', auth()->user()?->isAdmin() ? 'Attendance' : 'My Attendance')
@section('page_subtitle', auth()->user()?->isAdmin() ? 'Activity attendance records' : 'Your personal event attendance history')

@section('content')
@php
    $isAdmin = auth()->user()?->isAdmin() ?? false;
    $attendedEventsTotal = $counts['Present'] + $counts['Late'];
@endphp
<div class="space-y-6">
    @if (! $isAdmin && $currentMember)
        <!-- Member Personal Attendance Banner -->
        <section class="portal-card p-5 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gradient-to-r from-[#F8FAFF] to-[#F2FBF5] animate-fade-up">
            <div class="flex items-center gap-4">
                <x-member-avatar :member="$currentMember" size="lg" />
                <div>
                    <h2 class="text-base sm:text-lg font-extrabold text-slate-900">{{ $currentMember->name }}</h2>
                    <p class="text-xs text-slate-500">
                        You have attended <span class="font-extrabold text-[#163CA8]">{{ $attendedEventsTotal }}</span> {{ Str::plural('event', $attendedEventsTotal) }}
                        ({{ $counts['Present'] }} Present, {{ $counts['Late'] }} Late)
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <div class="text-xl font-black text-emerald-600">{{ $currentMember->attendance_rate }}%</div>
                    <div class="text-[11px] font-semibold text-slate-400">Attendance Rate</div>
                </div>
            </div>
        </section>
    @endif

    <!-- 4 Attendance Summary Stat Cards -->
    <section class="grid grid-cols-2 lg:grid-cols-4 gap-4 animate-fade-up">
        <div class="portal-card portal-card-hover p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-xl shrink-0">✅</div>
            <div>
                <div class="text-2xl font-black text-emerald-600" data-counter="{{ $counts['Present'] }}">0</div>
                <div class="text-xs font-bold text-slate-400">Present</div>
            </div>
        </div>

        <div class="portal-card portal-card-hover p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-xl shrink-0">⏰</div>
            <div>
                <div class="text-2xl font-black text-amber-600" data-counter="{{ $counts['Late'] }}">0</div>
                <div class="text-xs font-bold text-slate-400">Late</div>
            </div>
        </div>

        <div class="portal-card portal-card-hover p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-xl shrink-0">📝</div>
            <div>
                <div class="text-2xl font-black text-[#163CA8]" data-counter="{{ $counts['Excused'] }}">0</div>
                <div class="text-xs font-bold text-slate-400">Excused</div>
            </div>
        </div>

        <div class="portal-card portal-card-hover p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center text-xl shrink-0">❌</div>
            <div>
                <div class="text-2xl font-black text-rose-600" data-counter="{{ $counts['Absent'] }}">0</div>
                <div class="text-xs font-bold text-slate-400">Absent</div>
            </div>
        </div>
    </section>

    <!-- Activity Filter Pills & Record Button -->
    <section class="flex flex-wrap items-center justify-between gap-3 animate-fade-up animate-stagger-1">
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('attendance.index') }}"
               class="px-4 py-2 rounded-full text-xs font-bold border transition-all btn-press {{ $selectedActivity === 'all' ? 'bg-[#163CA8] border-[#163CA8] text-white shadow-sm' : 'bg-white border-slate-200 text-slate-500 hover:border-slate-300' }}">
                All
            </a>
            @foreach ($activities as $activity)
                @php $active = (string) $activity->id === $selectedActivity; @endphp
                <a href="{{ route('attendance.index', ['activity' => $activity->id]) }}"
                   class="px-4 py-2 rounded-full text-xs font-bold border transition-all btn-press {{ $active ? 'bg-[#163CA8] border-[#163CA8] text-white shadow-sm' : 'bg-white border-slate-200 text-slate-500 hover:border-slate-300' }}">
                    {{ $activity->title }}
                </a>
            @endforeach
        </div>

        @if ($isAdmin)
            <button type="button"
                    data-modal-open="record-attendance-modal"
                    class="px-5 py-2.5 rounded-full bg-[#163CA8] hover:bg-[#123189] text-white text-xs font-bold shadow-md shadow-blue-900/15 btn-press cursor-pointer">
                + Record Attendance
            </button>
        @endif
    </section>

    <!-- Attendance Table -->
    <section class="portal-card overflow-hidden animate-fade-up animate-stagger-2">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 text-[11px] font-extrabold uppercase tracking-wider text-slate-400 bg-slate-50/50">
                        <th class="py-3.5 px-5">Member</th>
                        <th class="py-3.5 px-4">Activity</th>
                        <th class="py-3.5 px-4">Date</th>
                        <th class="py-3.5 px-4">Status</th>
                        @if ($isAdmin)
                            <th class="py-3.5 px-5 text-right">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($attendances as $record)
                        @php
                            $badgeStyle = match ($record->status) {
                                'Present' => 'bg-emerald-100 text-emerald-700',
                                'Late' => 'bg-amber-100 text-amber-700',
                                'Excused' => 'bg-blue-100 text-[#163CA8]',
                                default => 'bg-rose-100 text-rose-700',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-5">
                                <a href="{{ route('members.show', $record->member) }}" class="flex items-center gap-3 group">
                                    <x-member-avatar :member="$record->member" size="sm" />
                                    <span class="font-bold text-slate-900 group-hover:text-[#163CA8] transition-colors">{{ $record->member->name }}</span>
                                </a>
                            </td>
                            <td class="py-3.5 px-4 text-xs sm:text-sm text-slate-600 font-medium">{{ $record->activity->title }}</td>
                            <td class="py-3.5 px-4 text-xs sm:text-sm text-slate-400 whitespace-nowrap">{{ $record->recorded_at->format('M j, Y') }}</td>
                            <td class="py-3.5 px-4">
                                @if ($isAdmin)
                                    <form action="{{ route('attendance.update', $record) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status"
                                                onchange="this.form.submit()"
                                                class="px-3 py-1 rounded-full text-xs font-bold outline-none cursor-pointer {{ $badgeStyle }}">
                                            @foreach (['Present', 'Late', 'Excused', 'Absent'] as $st)
                                                <option value="{{ $st }}" @selected($record->status === $st)>{{ $st }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                @else
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold {{ $badgeStyle }}">
                                        {{ $record->status }}
                                    </span>
                                @endif
                            </td>
                            @if ($isAdmin)
                                <td class="py-3.5 px-5 text-right">
                                    <form action="{{ route('attendance.destroy', $record) }}" method="POST" onsubmit="return confirm('Remove this attendance record?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs font-semibold text-slate-400 hover:text-red-600 transition-colors cursor-pointer">Delete</button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isAdmin ? 5 : 4 }}" class="py-10 text-center text-sm text-slate-400">
                                {{ $isAdmin ? 'No attendance records for this filter yet.' : 'You have no attendance records for this filter yet.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @if ($isAdmin)
        <!-- Member Attendance Overview (Admin Only) -->
        <section class="portal-card p-6 sm:p-7 animate-fade-up animate-stagger-3">
            <h3 class="text-base font-extrabold text-slate-900 mb-5">Member Attendance Overview</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                @foreach ($memberOverview as $member)
                    @php
                        $barColor = $member->attendance_rate >= 90 ? '#16A34A' : ($member->attendance_rate >= 75 ? '#D97706' : '#DC2626');
                        $textColor = $member->attendance_rate >= 90 ? 'text-emerald-600' : ($member->attendance_rate >= 75 ? 'text-amber-600' : 'text-rose-600');
                    @endphp
                    <a href="{{ route('members.show', $member) }}"
                       class="flex items-center gap-3.5 p-3.5 rounded-2xl bg-[#F8FAFC] hover:bg-slate-100/80 transition-colors">
                        <x-member-avatar :member="$member" size="sm" />
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs sm:text-sm font-bold text-slate-800 truncate">{{ $member->short_name }}</span>
                                <span class="text-xs font-extrabold {{ $textColor }}">{{ $member->attendance_rate }}%</span>
                            </div>
                            <div class="w-full h-2 bg-slate-200/70 rounded-full overflow-hidden mt-2">
                                <div class="h-full rounded-full progress-fill" style="width: 0%; background-color: {{ $barColor }};" data-bar-width="{{ $member->attendance_rate }}"></div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @elseif ($currentMember)
        <!-- Personal Attendance Rate Card (Member View Only) -->
        <section class="portal-card p-6 sm:p-7 animate-fade-up animate-stagger-3">
            <h3 class="text-base font-extrabold text-slate-900 mb-4">My Attendance Summary</h3>
            @php
                $barColor = $currentMember->attendance_rate >= 90 ? '#16A34A' : ($currentMember->attendance_rate >= 75 ? '#D97706' : '#DC2626');
            @endphp
            <div class="p-4 rounded-2xl bg-[#F8FAFC]">
                <div class="flex items-center justify-between text-xs sm:text-sm font-bold text-slate-800 mb-2">
                    <span>Overall Event Participation ({{ $attendedEventsTotal }} attended of {{ $attendances->count() }} recorded)</span>
                    <span class="text-emerald-600">{{ $currentMember->attendance_rate }}%</span>
                </div>
                <div class="w-full h-2.5 bg-slate-200/70 rounded-full overflow-hidden">
                    <div class="h-full rounded-full progress-fill" style="width: 0%; background-color: {{ $barColor }};" data-bar-width="{{ $currentMember->attendance_rate }}"></div>
                </div>
            </div>
        </section>
    @endif
</div>

@if ($isAdmin)
    <!-- Record Attendance Modal (Admin Only) -->
    <div id="record-attendance-modal" class="portal-modal fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/45 backdrop-blur-xs p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 sm:p-7 animate-modal-pop">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900">Record Attendance</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Updates member attendance rate automatically</p>
                </div>
                <button type="button" data-modal-close class="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-500">✕</button>
            </div>

            <form action="{{ route('attendance.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Member *</label>
                    <select name="member_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#163CA8] text-sm outline-none bg-white">
                        @foreach ($members as $member)
                            <option value="{{ $member->id }}">{{ $member->member_code }} — {{ $member->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Activity *</label>
                    <select name="activity_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#163CA8] text-sm outline-none bg-white">
                        @foreach ($activities as $activity)
                            <option value="{{ $activity->id }}">{{ $activity->title }} ({{ $activity->event_date->format('M d, Y') }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status *</label>
                        <select name="status" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#163CA8] text-sm outline-none bg-white">
                            <option value="Present">Present</option>
                            <option value="Late">Late</option>
                            <option value="Excused">Excused</option>
                            <option value="Absent">Absent</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Date *</label>
                        <input type="date" name="recorded_at" value="{{ now()->toDateString() }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#163CA8] text-sm outline-none">
                    </div>
                </div>

                <label class="flex items-center gap-2 text-xs text-slate-600 cursor-pointer">
                    <input type="checkbox" name="award_points" value="1" checked class="rounded border-slate-300 text-[#163CA8]">
                    <span>Automatically award activity points if Present / Late</span>
                </label>

                <div class="pt-2 flex justify-end gap-2.5">
                    <button type="button" data-modal-close class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#163CA8] hover:bg-[#123189] text-white text-xs font-bold shadow-md btn-press cursor-pointer">Save Attendance</button>
                </div>
            </form>
        </div>
    </div>
@endif
@endsection
