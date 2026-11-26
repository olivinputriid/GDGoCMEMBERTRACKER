@extends('layouts.portal')

@section('title', 'Leaderboard — GDGoC UNSRI Admin Portal')
@section('page_title', 'Leaderboard')
@section('page_subtitle', 'Member rankings by total points')

@section('content')
<div class="space-y-6">
    <!-- Period Tabs -->
    <div class="flex flex-wrap items-center gap-2.5 animate-fade-up">
        @foreach ([
            'overall' => 'Overall',
            'monthly' => 'Monthly',
            'semester' => 'Semester',
            'annual' => 'Annual',
        ] as $key => $label)
            @php $active = $period === $key; @endphp
            <a href="{{ route('leaderboard.index', ['period' => $key]) }}"
               class="px-5 py-2.5 rounded-full text-xs sm:text-sm font-bold border transition-all btn-press {{ $active ? 'bg-[#163CA8] border-[#163CA8] text-white shadow-md shadow-blue-900/15' : 'bg-white border-slate-200 text-slate-500 hover:border-slate-300 hover:text-slate-800' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <!-- Podium Card -->
    <section class="relative overflow-hidden portal-card p-6 sm:p-10 bg-gradient-to-br from-[#F8FAFF] via-[#FFFDF7] to-[#F3FBF6] animate-fade-up animate-stagger-1">
        <span class="animate-pulse-soft pointer-events-none absolute top-6 left-8 text-amber-500 text-3xl">★</span>
        <span class="animate-float-slow pointer-events-none absolute -top-6 right-16 w-24 h-24 rounded-full bg-blue-100/70"></span>
        <span class="animate-float-reverse pointer-events-none absolute bottom-6 right-8 w-3 h-3 rounded-full bg-rose-500"></span>

        <h2 class="text-center text-xl sm:text-2xl font-black tracking-tight mb-6 relative z-10">
            <span>🏆</span>
            <span class="text-[#163CA8]">Member</span>
            <span class="text-[#D97706]">Rankings</span>
            <span class="text-emerald-600">— {{ ucfirst($period) }}</span>
        </h2>

        <x-top-podium :podium="$podium" />
    </section>

    <!-- Full Rankings Table -->
    <section class="portal-card overflow-hidden animate-fade-up animate-stagger-2">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 text-[11px] font-extrabold uppercase tracking-wider text-slate-400 bg-slate-50/50">
                        <th class="py-3.5 px-5">Rank</th>
                        <th class="py-3.5 px-4">Member</th>
                        <th class="py-3.5 px-4">Division</th>
                        <th class="py-3.5 px-4">Progress</th>
                        <th class="py-3.5 px-4 text-right">Pts</th>
                        <th class="py-3.5 px-5 text-right">Badges</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @foreach ($rankings as $entry)
                        @php
                            $rank = $entry['rank'];
                            $member = $entry['member'];
                            $score = $entry['score'];
                            $barColor = match ($rank) {
                                1 => '#D97706',
                                2 => '#94A3B8',
                                3 => '#C94A38',
                                default => '#163CA8',
                            };
                            $rankBadge = match ($rank) {
                                1 => 'bg-amber-600 text-white',
                                2 => 'bg-slate-400 text-white',
                                3 => 'bg-rose-600 text-white',
                                default => 'text-slate-400',
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <td class="py-3.5 px-5">
                                <span class="w-8 h-8 rounded-full inline-flex items-center justify-center text-xs font-extrabold {{ $rankBadge }}">
                                    {{ $rank }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <a href="{{ route('members.show', $member) }}" class="flex items-center gap-3">
                                    <x-member-avatar :member="$member" size="sm" />
                                    <div>
                                        <div class="font-bold text-slate-900 group-hover:text-[#163CA8] transition-colors">{{ $member->name }}</div>
                                        <div class="text-xs text-slate-400">{{ $member->batch }} • {{ $member->role_title }}</div>
                                    </div>
                                </a>
                            </td>
                            <td class="py-3.5 px-4 text-xs sm:text-sm text-slate-400 font-medium">{{ $member->division->name }}</td>
                            <td class="py-3.5 px-4">
                                <div class="w-32 sm:w-44 h-2 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full progress-fill" style="width: 0%; background-color: {{ $barColor }};" data-bar-width="{{ $entry['progress'] }}"></div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-right font-extrabold text-[#163CA8]">{{ $score }}</td>
                            <td class="py-3.5 px-5 text-right">
                                <div class="inline-flex items-center gap-1">
                                    @foreach ($member->achievements->take(3) as $badge)
                                        <span title="{{ $badge->name }}" class="text-base leading-none">{{ $badge->icon }}</span>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
