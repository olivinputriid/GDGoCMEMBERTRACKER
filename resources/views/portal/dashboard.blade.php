@extends('layouts.portal')

@section('title', 'Dashboard — GDGoC UNSRI Admin Portal')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Organization overview & key metrics')

@section('content')
<div class="space-y-6">
    <!-- Hero Blue Welcome Banner -->
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#1944B5] via-[#163CA8] to-[#123189] text-white p-6 sm:p-8 shadow-lg animate-fade-up">
        <span class="animate-float-slow pointer-events-none absolute -top-12 right-12 w-44 h-44 rounded-full bg-white/10"></span>
        <span class="animate-pulse-soft pointer-events-none absolute top-6 right-1/3 text-amber-400 text-2xl">★</span>
        <span class="animate-float-reverse pointer-events-none absolute -bottom-8 right-8 w-24 h-24 rounded-full border-4 border-white/15"></span>

        <div class="relative z-10">
            <p class="text-xs sm:text-sm text-blue-100 font-medium">Welcome back, {{ auth()->user()?->role === 'admin' ? 'Admin' : auth()->user()?->name }} 👋</p>
            <h2 class="text-2xl sm:text-3xl font-black tracking-tight mt-1">
                GDGoC UNSRI — <span class="text-amber-400">Member Dashboard</span>
            </h2>

            <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-3.5 max-w-2xl">
                <div class="rounded-2xl bg-white/12 border border-white/15 backdrop-blur-sm px-4 py-3.5">
                    <div class="text-xl sm:text-2xl font-black" data-counter="{{ $summary['activeMembers'] }}">0</div>
                    <div class="text-xs font-bold text-blue-100 mt-0.5">Active Members</div>
                    <div class="text-[11px] text-blue-200/80">of {{ $summary['totalMembers'] }} total</div>
                </div>

                <div class="rounded-2xl bg-white/12 border border-white/15 backdrop-blur-sm px-4 py-3.5">
                    <div class="text-xl sm:text-2xl font-black" data-counter="{{ $summary['thisMonthPoints'] }}" data-format="comma">0</div>
                    <div class="text-xs font-bold text-blue-100 mt-0.5">This Month Points</div>
                    <div class="text-[11px] text-blue-200/80">
                        {{ $summary['growthPercent'] >= 0 ? '+' : '' }}{{ $summary['growthPercent'] }}% vs last month
                    </div>
                </div>

                <div class="rounded-2xl bg-white/12 border border-white/15 backdrop-blur-sm px-4 py-3.5">
                    <div class="text-xl sm:text-2xl font-black">
                        {{ $summary['nextEvent'] ? $summary['nextEvent']->event_date->format('M d') : 'TBD' }}
                    </div>
                    <div class="text-xs font-bold text-blue-100 mt-0.5">Next Event</div>
                    <div class="text-[11px] text-blue-200/80 truncate">
                        {{ $summary['nextEvent']?->title ?? 'No upcoming events' }}
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4 Metric Cards -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 animate-fade-up animate-stagger-1">
        <div class="portal-card portal-card-hover p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-xl shrink-0">👥</div>
            <div>
                <div class="text-2xl font-black text-[#163CA8]" data-counter="{{ $summary['totalMembers'] }}">0</div>
                <div class="text-xs font-bold text-slate-600">Total Members</div>
                <div class="text-[11px] text-slate-400">{{ $summary['totalMembers'] }} registered</div>
            </div>
        </div>

        <div class="portal-card portal-card-hover p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-xl shrink-0">📅</div>
            <div>
                <div class="text-2xl font-black text-emerald-600" data-counter="{{ $summary['avgAttendance'] }}" data-suffix="%">0%</div>
                <div class="text-xs font-bold text-slate-600">Avg Attendance</div>
                <div class="text-[11px] text-slate-400">{{ $summary['totalEvents'] }} events</div>
            </div>
        </div>

        <div class="portal-card portal-card-hover p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-xl shrink-0">✨</div>
            <div>
                <div class="text-2xl font-black text-amber-600" data-counter="{{ $summary['totalContributions'] }}">0</div>
                <div class="text-xs font-bold text-slate-600">Contributions</div>
                <div class="text-[11px] text-slate-400">verified</div>
            </div>
        </div>

        <div class="portal-card portal-card-hover p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center text-xl shrink-0">🎖️</div>
            <div>
                <div class="text-2xl font-black text-rose-600" data-counter="{{ $summary['achievementsAwarded'] }}">0</div>
                <div class="text-xs font-bold text-slate-600">Achievements</div>
                <div class="text-[11px] text-slate-400">badges awarded</div>
            </div>
        </div>
    </section>

    <!-- Middle 2-Column Section: Top 5 Leaderboard & Recent Activities -->
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-5 animate-fade-up animate-stagger-2">
        <!-- Top 5 Leaderboard -->
        <div class="lg:col-span-5 portal-card p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                    <span>🏆</span>
                    <span>Top 5 Leaderboard</span>
                </h3>
                <a href="{{ route('leaderboard.index') }}" class="text-xs font-bold text-[#163CA8] hover:underline">View all →</a>
            </div>

            <div class="space-y-3.5">
                @php
                    $rankColors = [
                        1 => ['bg' => 'bg-amber-600', 'bar' => '#D97706'],
                        2 => ['bg' => 'bg-slate-400', 'bar' => '#64748B'],
                        3 => ['bg' => 'bg-rose-600', 'bar' => '#DC2626'],
                        4 => ['bg' => 'bg-[#163CA8]', 'bar' => '#163CA8'],
                        5 => ['bg' => 'bg-emerald-600', 'bar' => '#16A34A'],
                    ];
                @endphp
                @foreach ($top5Members as $entry)
                    @php
                        $member = $entry['member'];
                        $rank = $entry['rank'];
                        $style = $rankColors[$rank] ?? ['bg' => 'bg-slate-500', 'bar' => '#163CA8'];
                    @endphp
                    <a href="{{ route('members.show', $member) }}" class="flex items-center gap-3 group hover:bg-slate-50 p-1.5 -mx-1.5 rounded-xl transition-colors">
                        <span class="w-7 h-7 rounded-full {{ $style['bg'] }} text-white text-xs font-extrabold flex items-center justify-center shrink-0">
                            {{ $rank }}
                        </span>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs font-bold text-slate-800 group-hover:text-[#163CA8] truncate">{{ $member->short_name }}</span>
                                <span class="text-xs font-extrabold text-slate-700">{{ $entry['score'] }}</span>
                            </div>
                            <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden mt-1.5">
                                <div class="h-full rounded-full progress-fill" style="width: 0%; background-color: {{ $style['bar'] }}" data-bar-width="{{ $entry['progress'] }}"></div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="lg:col-span-7 portal-card p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                    <span>🗓️</span>
                    <span>Recent Activities</span>
                </h3>
                <a href="{{ route('activities.index') }}" class="text-xs font-bold text-[#163CA8] hover:underline">View all →</a>
            </div>

            <div class="space-y-3">
                @php
                    $activityIcons = [
                        'target' => ['🎯', 'bg-blue-50'],
                        'tools' => ['🛠️', 'bg-amber-50'],
                        'book' => ['📖', 'bg-emerald-50'],
                        'clipboard' => ['📋', 'bg-slate-100'],
                        'laptop' => ['💻', 'bg-rose-50'],
                        'microscope' => ['🔬', 'bg-amber-50'],
                        'megaphone' => ['📣', 'bg-indigo-50'],
                    ];
                @endphp
                @foreach ($recentActivities as $activity)
                    @php [$emoji, $iconBg] = $activityIcons[$activity->icon] ?? ['📅', 'bg-blue-50']; @endphp
                    <a href="{{ route('activities.index', ['search' => $activity->title]) }}"
                       class="flex items-center justify-between gap-3 p-3 rounded-2xl bg-[#F8FAFC] hover:bg-slate-100/80 transition-colors">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-xl {{ $iconBg }} flex items-center justify-center text-lg shrink-0">{{ $emoji }}</div>
                            <div class="min-w-0">
                                <div class="text-sm font-bold text-slate-800 truncate">{{ $activity->title }}</div>
                                <div class="text-xs text-slate-400">{{ $activity->event_date->format('M d, Y') }} • {{ $activity->category }}</div>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <div class="text-xs font-extrabold text-emerald-600">+{{ $activity->points_reward }} pts</div>
                            <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $activity->status === 'Upcoming' ? 'bg-blue-100 text-[#163CA8]' : 'bg-slate-200/80 text-slate-600' }}">
                                {{ $activity->status }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Monthly Point Growth Chart -->
    <section class="portal-card p-6 sm:p-7 animate-fade-up animate-stagger-3">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
            <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                <span>📊</span>
                <span>Monthly Point Growth</span>
            </h3>
            <div class="inline-flex items-center gap-1.5 bg-slate-100 p-1 rounded-full">
                @foreach ([2025, 2026] as $yr)
                    <button type="button"
                            data-year-btn="{{ $yr }}"
                            onclick="switchChartYear({{ $yr }})"
                            class="px-3.5 py-1 rounded-full text-xs font-bold transition-all cursor-pointer {{ $selectedYear === $yr ? 'bg-[#163CA8] text-white shadow-xs' : 'text-slate-500 hover:text-slate-800' }}">
                        {{ $yr }}
                    </button>
                @endforeach
            </div>
        </div>

        <div id="monthly-chart-container" class="h-48 sm:h-56 flex items-end justify-between gap-2 sm:gap-4 pt-6 px-2">
            <!-- Populated dynamically by inline chart controller -->
        </div>
    </section>

    <!-- Bottom 4 Highlight Cards -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 animate-fade-up animate-stagger-4">
        @if ($highlights['mostActive'])
            <a href="{{ route('members.show', $highlights['mostActive']) }}" class="portal-card portal-card-hover p-4 flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-full bg-indigo-50 flex items-center justify-center text-base shrink-0">⚡</div>
                <div class="min-w-0">
                    <div class="text-[10px] font-extrabold tracking-wider text-[#163CA8] uppercase">MOST ACTIVE</div>
                    <div class="text-sm font-extrabold text-slate-900 truncate">{{ $highlights['mostActive']->short_name }}</div>
                    <div class="text-xs text-slate-400">{{ $highlights['mostActive']->attendance_rate }}% attendance</div>
                </div>
            </a>
        @endif

        @if ($highlights['topContributor'])
            <a href="{{ route('members.show', $highlights['topContributor']) }}" class="portal-card portal-card-hover p-4 flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-full bg-amber-50 flex items-center justify-center text-base shrink-0">🏆</div>
                <div class="min-w-0">
                    <div class="text-[10px] font-extrabold tracking-wider text-amber-600 uppercase">TOP CONTRIBUTOR</div>
                    <div class="text-sm font-extrabold text-slate-900 truncate">{{ $highlights['topContributor']->short_name }}</div>
                    <div class="text-xs text-slate-400">{{ $highlights['topContributor']->contributions_count }} contributions</div>
                </div>
            </a>
        @endif

        @if ($highlights['competitionStar'])
            <a href="{{ route('members.show', $highlights['competitionStar']) }}" class="portal-card portal-card-hover p-4 flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-full bg-rose-50 flex items-center justify-center text-base shrink-0">⭐</div>
                <div class="min-w-0">
                    <div class="text-[10px] font-extrabold tracking-wider text-rose-600 uppercase">COMPETITION STAR</div>
                    <div class="text-sm font-extrabold text-slate-900 truncate">{{ $highlights['competitionStar']->short_name }}</div>
                    <div class="text-xs text-slate-400">{{ $highlights['competitionStar']->competitions_count }} competitions</div>
                </div>
            </a>
        @endif

        @if ($highlights['pointLeader'])
            <a href="{{ route('members.show', $highlights['pointLeader']) }}" class="portal-card portal-card-hover p-4 flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center text-base shrink-0">💯</div>
                <div class="min-w-0">
                    <div class="text-[10px] font-extrabold tracking-wider text-emerald-600 uppercase">POINT LEADER</div>
                    <div class="text-sm font-extrabold text-slate-900 truncate">{{ $highlights['pointLeader']->short_name }}</div>
                    <div class="text-xs text-slate-400">{{ number_format($highlights['pointLeader']->points) }} points</div>
                </div>
            </a>
        @endif
    </section>
</div>
@endsection

@push('scripts')
<script>
    const monthlySeries = @json($monthlySeries);

    function switchChartYear(year) {
        const container = document.getElementById('monthly-chart-container');
        if (!container) {
            return;
        }

        document.querySelectorAll('[data-year-btn]').forEach((btn) => {
            const active = parseInt(btn.dataset.yearBtn, 10) === year;
            btn.classList.toggle('bg-[#163CA8]', active);
            btn.classList.toggle('text-white', active);
            btn.classList.toggle('shadow-xs', active);
            btn.classList.toggle('text-slate-500', !active);
        });

        const data = monthlySeries[year] || [];
        const maxPoints = Math.max(100, ...data.map((d) => d.points));

        container.innerHTML = data.map((item) => {
            const pct = item.points > 0 ? Math.max(14, Math.round((item.points / maxPoints) * 100)) : 6;
            return `
                <div class="flex-1 h-full flex flex-col items-center justify-end group">
                    <div class="relative w-full h-full flex items-end justify-center">
                        <div class="opacity-0 group-hover:opacity-100 transition-opacity absolute -top-7 px-2 py-0.5 rounded-md bg-slate-800 text-white text-[10px] font-bold whitespace-nowrap pointer-events-none shadow-md">
                            ${item.points} pts
                        </div>
                        <div class="w-full max-w-[64px] rounded-t-xl chart-bar-fill group-hover:brightness-110 cursor-pointer"
                             style="height: 0%; background-color: ${item.color};"
                             data-bar-height="${pct}">
                        </div>
                    </div>
                    <span class="mt-2.5 text-xs font-medium text-slate-400">${item.label}</span>
                </div>
            `;
        }).join('');

        requestAnimationFrame(() => {
            container.querySelectorAll('[data-bar-height]').forEach((bar) => {
                bar.style.height = `${bar.dataset.barHeight}%`;
            });
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        switchChartYear({{ $selectedYear }});
    });
</script>
@endpush
