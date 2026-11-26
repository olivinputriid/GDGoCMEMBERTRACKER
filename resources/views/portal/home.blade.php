@extends('layouts.portal')

@section('title', 'Home — GDGoC UNSRI Portal')
@section('page_title', 'Home')
@section('page_subtitle', auth()->user()?->isAdmin() ? 'Welcome to GDGoC UNSRI Admin Portal' : 'Welcome to GDGoC UNSRI Member Portal')

@section('content')
<div class="space-y-8">
    <!-- Hero Banner -->
    <section class="relative overflow-hidden rounded-3xl border border-slate-100 bg-gradient-to-br from-[#F8FAFF] via-[#FFFBF2] to-[#F3FBF6] px-6 py-12 sm:px-12 sm:py-16 text-center shadow-xs animate-fade-up">
        <!-- Animated Floating Shapes -->
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <span class="animate-float-slow absolute -top-6 -left-6 w-28 h-28 rounded-full bg-amber-400/75"></span>
            <span class="animate-float-reverse absolute top-14 left-1/4 w-12 h-12 rounded-full bg-rose-400/70"></span>
            <span class="animate-float-slow absolute -top-6 right-1/4 w-16 h-16 rounded-full border-4 border-emerald-400/50"></span>
            <span class="animate-pulse-soft absolute top-10 right-12 text-amber-500 text-3xl">★</span>
            <span class="animate-float-reverse absolute bottom-8 left-12 w-3 h-3 rounded-full bg-blue-600"></span>
            <span class="animate-float-slow absolute top-1/2 right-8 text-amber-400 text-2xl">✨</span>
            <span class="animate-float-reverse absolute -bottom-10 -right-8 w-36 h-36 rounded-full border-[14px] border-amber-400/50"></span>
        </div>

        <div class="relative z-10 max-w-3xl mx-auto">
            <div class="inline-flex items-center gap-2.5 px-4 py-1.5 rounded-full bg-white/95 border border-slate-200/80 shadow-xs mb-6">
                <x-gdg-logo size="sm" />
                <span class="text-xs sm:text-sm font-bold text-[#163CA8]">Google Developer Groups Universitas Sriwijaya</span>
            </div>

            <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-[1.12]">
                <span class="text-[#C94A38]">Let's</span>
                <span class="text-[#EA4335]">Connect,</span><br>
                <span class="text-[#163CA8]">Learn,</span>
                <span class="text-[#D97706]">and</span>
                <span class="text-[#2F9E57]">Grow!</span>
            </h2>

            <div class="mt-5 inline-block px-5 py-2.5 rounded-full bg-white/85 border border-slate-100 shadow-xs">
                <p class="text-xs sm:text-sm text-slate-500 font-medium">
                    The centralized member engagement &amp; contribution platform
                </p>
            </div>

            <div class="mt-8 flex flex-wrap items-center justify-center gap-3.5">
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-[#163CA8] hover:bg-[#123189] text-white text-sm font-bold shadow-md shadow-blue-900/15 btn-press">
                    <span>Go to Dashboard</span>
                    <span>→</span>
                </a>
                <a href="{{ route('members.index') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-white hover:bg-blue-50 text-[#163CA8] border-2 border-[#163CA8] text-sm font-bold btn-press">
                    <span>View Members</span>
                    <span>→</span>
                </a>
            </div>
        </div>
    </section>

    <!-- 4 Key Stat Cards -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5 animate-fade-up animate-stagger-1">
        <div class="portal-card portal-card-hover p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-xl shrink-0">👥</div>
            <div>
                <div class="text-2xl font-black text-[#163CA8]" data-counter="{{ $summary['totalMembers'] }}">0</div>
                <div class="text-xs font-bold text-slate-600">Total Members</div>
                <div class="text-[11px] text-slate-400">{{ $summary['activeMembers'] }} active</div>
            </div>
        </div>

        <div class="portal-card portal-card-hover p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-xl shrink-0">📅</div>
            <div>
                <div class="text-2xl font-black text-emerald-600" data-counter="{{ $summary['avgAttendance'] }}" data-suffix="%">0%</div>
                <div class="text-xs font-bold text-slate-600">Avg Attendance</div>
                <div class="text-[11px] text-slate-400">across {{ $summary['totalEvents'] }} events</div>
            </div>
        </div>

        <div class="portal-card portal-card-hover p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-amber-50 flex items-center justify-center text-xl shrink-0">✨</div>
            <div>
                <div class="text-2xl font-black text-amber-600" data-counter="{{ $summary['totalContributions'] }}">0</div>
                <div class="text-xs font-bold text-slate-600">Contributions</div>
                <div class="text-[11px] text-slate-400">verified records</div>
            </div>
        </div>

        <div class="portal-card portal-card-hover p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center text-xl shrink-0">🥇</div>
            <div>
                <div class="text-2xl font-black text-rose-600" data-counter="{{ $summary['totalCompetitions'] }}">0</div>
                <div class="text-xs font-bold text-slate-600">Competitions</div>
                <div class="text-[11px] text-slate-400">delegations</div>
            </div>
        </div>
    </section>

    <!-- Top Members Podium -->
    <section class="portal-card p-6 sm:p-8 animate-fade-up animate-stagger-2">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div>
                <h3 class="text-xl font-extrabold flex items-center gap-2">
                    <span>🏆</span>
                    <span class="text-[#163CA8]">Top</span>
                    <span class="text-[#D97706]">Members</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Ranked by total accumulated points</p>
            </div>
            <a href="{{ route('leaderboard.index') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full border-2 border-[#163CA8] text-[#163CA8] hover:bg-[#163CA8] hover:text-white text-xs font-bold transition-colors btn-press">
                <span>Full Leaderboard</span>
                <span>→</span>
            </a>
        </div>

        <x-top-podium :podium="$topMembers" />
    </section>

    <!-- Divisions Explorer -->
    <section class="portal-card p-6 sm:p-8 animate-fade-up animate-stagger-3">
        <div class="mb-6">
            <h3 class="text-xl font-extrabold flex items-center gap-2">
                <span>📁</span>
                <span class="text-[#C94A38]">Divisions</span>
            </h3>
            <p class="text-xs text-slate-400 mt-0.5">Find your best place to grow</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- Curriculum Divisions -->
            <div class="rounded-2xl bg-[#F2FBF5] border border-emerald-100 p-5 sm:p-6">
                <span class="inline-block px-4 py-1.5 rounded-full bg-emerald-800 text-white text-xs font-bold mb-4">
                    Curriculum Divisions
                </span>
                <div class="flex flex-wrap gap-2.5">
                    @foreach ($curriculumDivisions as $division)
                        <a href="{{ route('members.index', ['division' => $division->slug]) }}"
                           class="px-3.5 py-1.5 rounded-full bg-white border border-emerald-200 text-emerald-800 hover:bg-emerald-700 hover:text-white hover:border-emerald-700 text-xs font-semibold transition-all btn-press">
                            {{ $division->name }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Non-Curriculum Divisions -->
            <div class="rounded-2xl bg-[#FFF9F0] border border-amber-100 p-5 sm:p-6">
                <span class="inline-block px-4 py-1.5 rounded-full bg-amber-700 text-white text-xs font-bold mb-4">
                    Non-Curriculum Divisions
                </span>
                <div class="flex flex-wrap gap-2.5">
                    @foreach ($nonCurriculumDivisions as $division)
                        <a href="{{ route('members.index', ['division' => $division->slug]) }}"
                           class="px-3.5 py-1.5 rounded-full bg-white border border-amber-200 text-amber-800 hover:bg-amber-600 hover:text-white hover:border-amber-600 text-xs font-semibold transition-all btn-press">
                            {{ $division->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- Bottom Royal Blue CTA Banner -->
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#173FA8] to-[#123087] text-white px-6 py-10 sm:py-12 text-center shadow-lg animate-fade-up animate-stagger-4">
        <span class="animate-float-slow pointer-events-none absolute -top-10 right-10 w-36 h-36 rounded-full bg-white/10"></span>
        <span class="animate-pulse-soft pointer-events-none absolute top-6 left-1/4 text-amber-400 text-xl">★</span>
        <span class="animate-float-reverse pointer-events-none absolute -bottom-10 left-16 w-28 h-28 rounded-full border-4 border-white/15"></span>

        <div class="relative z-10 max-w-xl mx-auto">
            <h3 class="text-2xl sm:text-3xl font-black tracking-tight">
                Let's <span class="text-amber-400">Connect,<br class="sm:hidden"> Learn,</span> and Grow!
            </h3>
            <p class="text-xs sm:text-sm text-blue-100 mt-2">Join our growing community of developers</p>
            <a href="{{ route('members.index') }}"
               class="mt-6 inline-flex items-center gap-2 px-6 py-3 rounded-full bg-white hover:bg-blue-50 text-[#163CA8] text-sm font-bold shadow-md btn-press">
                <span>🔹</span>
                <span>Explore Members</span>
            </a>
        </div>
    </section>
</div>
@endsection
