@extends('layouts.portal')

@section('title', 'Admin Profile — GDGoC UNSRI Admin Portal')
@section('page_title', 'Admin Profile')
@section('page_subtitle', 'Administrator account settings & chapter controls')

@section('content')
@php
    $adminInitials = collect(explode(' ', $admin->name))->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('');
@endphp
<div class="space-y-6">
    <!-- Admin Header Card -->
    <section class="portal-card p-6 sm:p-8 animate-fade-up">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-5">
            <div class="flex items-center gap-5">
                @if ($admin->avatar_url)
                    <img src="{{ $admin->avatar_url }}"
                         alt="{{ $admin->name }}"
                         class="w-20 h-20 rounded-3xl object-cover shadow-md border-2 border-blue-100 shrink-0">
                @else
                    <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-[#173FA8] to-[#123087] flex items-center justify-center text-2xl font-black text-white shadow-md shrink-0">
                        {{ $adminInitials ?: 'AD' }}
                    </div>
                @endif

                <div>
                    <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">{{ $admin->name }}</h2>
                    <p class="text-xs sm:text-sm text-slate-400 mt-0.5">
                        Portal Administrator — GDGoC UNSRI
                    </p>
                    <div class="mt-2 flex flex-wrap items-center gap-2">
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-[#163CA8]">Administrator</span>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Full Access (Edit, Upload, Delete)</span>
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-500">{{ $admin->email }}</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <button type="button"
                        data-modal-open="edit-admin-profile-modal"
                        class="px-5 py-2.5 rounded-full bg-[#163CA8] hover:bg-[#123189] text-white text-xs sm:text-sm font-bold shadow-md shadow-blue-900/15 btn-press cursor-pointer">
                    Edit &amp; Upload Photo
                </button>
                @if ($admin->avatar_url)
                    <form action="{{ route('profile.avatar.delete') }}" method="POST" onsubmit="return confirm('Remove admin profile photo?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2.5 rounded-full border border-red-200 text-red-600 hover:bg-red-50 text-xs sm:text-sm font-bold btn-press cursor-pointer">
                            Delete Photo
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- 5 Admin Overview Metric Boxes -->
        <div class="mt-6 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5">
            <div class="rounded-2xl bg-[#EEF2FF] p-4 text-center">
                <div class="text-xl sm:text-2xl font-black text-[#163CA8]" data-counter="{{ $summary['totalMembers'] }}">0</div>
                <div class="text-xs text-slate-400 font-medium mt-0.5">Total Members</div>
            </div>
            <div class="rounded-2xl bg-[#ECFDF3] p-4 text-center">
                <div class="text-xl sm:text-2xl font-black text-emerald-600" data-counter="{{ $summary['activeMembers'] }}">0</div>
                <div class="text-xs text-slate-400 font-medium mt-0.5">Active Members</div>
            </div>
            <div class="rounded-2xl bg-[#FFF6EB] p-4 text-center">
                <div class="text-xl sm:text-2xl font-black text-amber-600" data-counter="{{ $summary['totalEvents'] }}">0</div>
                <div class="text-xs text-slate-400 font-medium mt-0.5">Activities</div>
            </div>
            <div class="rounded-2xl bg-[#EEF2FF] p-4 text-center">
                <div class="text-xl sm:text-2xl font-black text-[#163CA8]" data-counter="{{ $attendanceCount }}">0</div>
                <div class="text-xs text-slate-400 font-medium mt-0.5">Attendance Logs</div>
            </div>
            <div class="rounded-2xl bg-[#FEF2F2] p-4 text-center col-span-2 sm:col-span-1">
                <div class="text-xl sm:text-2xl font-black text-rose-600" data-counter="{{ $totalPointsDistributed }}" data-format="comma">0</div>
                <div class="text-xs text-slate-400 font-medium mt-0.5">Points Issued</div>
            </div>
        </div>
    </section>

    <!-- Bottom 2 Columns: Admin Management Shortcuts & Recent Activity Feed -->
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <div class="lg:col-span-5 portal-card p-6 animate-fade-up animate-stagger-1">
            <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2 mb-5">
                <span>🛡️</span>
                <span>Admin Management Access</span>
            </h3>

            <div class="space-y-3">
                <a href="{{ route('members.index') }}" class="flex items-center justify-between p-4 rounded-2xl bg-[#F8FAFC] hover:bg-blue-50/70 transition-colors group">
                    <div class="flex items-center gap-3">
                        <span class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center text-lg">👥</span>
                        <div>
                            <div class="text-sm font-bold text-slate-900 group-hover:text-[#163CA8]">Members Directory</div>
                            <div class="text-xs text-slate-400">Add, edit, upload photo &amp; delete members</div>
                        </div>
                    </div>
                    <span class="text-sm font-bold text-[#163CA8]">→</span>
                </a>

                <a href="{{ route('activities.index') }}" class="flex items-center justify-between p-4 rounded-2xl bg-[#F8FAFC] hover:bg-amber-50/70 transition-colors group">
                    <div class="flex items-center gap-3">
                        <span class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center text-lg">🗓️</span>
                        <div>
                            <div class="text-sm font-bold text-slate-900 group-hover:text-amber-700">Activities &amp; Events</div>
                            <div class="text-xs text-slate-400">Create, upload banners, check-in &amp; delete</div>
                        </div>
                    </div>
                    <span class="text-sm font-bold text-amber-600">→</span>
                </a>

                <a href="{{ route('attendance.index') }}" class="flex items-center justify-between p-4 rounded-2xl bg-[#F8FAFC] hover:bg-emerald-50/70 transition-colors group">
                    <div class="flex items-center gap-3">
                        <span class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-lg">📅</span>
                        <div>
                            <div class="text-sm font-bold text-slate-900 group-hover:text-emerald-700">Attendance Records</div>
                            <div class="text-xs text-slate-400">Record, update status &amp; manage logs</div>
                        </div>
                    </div>
                    <span class="text-sm font-bold text-emerald-600">→</span>
                </a>
            </div>
        </div>

        <div class="lg:col-span-7 portal-card p-6 animate-fade-up animate-stagger-2">
            <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2 mb-5">
                <span>📊</span>
                <span>Recent Point Transactions</span>
            </h3>

            <div class="space-y-3">
                @forelse ($recentLogs as $log)
                    <a href="{{ route('members.show', $log->member) }}"
                       class="flex items-center justify-between gap-3 p-3.5 rounded-2xl bg-[#F8FAFC] hover:bg-slate-100/80 transition-colors">
                        <div class="flex items-center gap-3 min-w-0">
                            <x-member-avatar :member="$log->member" size="sm" />
                            <div class="min-w-0">
                                <div class="text-sm font-bold text-slate-900 truncate">{{ $log->member->name }} — {{ $log->title }}</div>
                                <div class="text-xs text-slate-400">{{ $log->category }} • {{ $log->awarded_at->format('M j, Y') }}</div>
                            </div>
                        </div>
                        <span class="text-sm font-extrabold text-emerald-600 shrink-0">+{{ $log->points }} pts</span>
                    </a>
                @empty
                    <div class="py-8 text-center text-xs text-slate-400">No point logs recorded yet.</div>
                @endforelse
            </div>
        </div>
    </section>
</div>

<!-- Edit Admin Profile & Upload Photo Modal -->
<div id="edit-admin-profile-modal" class="portal-modal fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/45 backdrop-blur-xs p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 sm:p-7 animate-modal-pop">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-xl font-extrabold text-slate-900">Edit Admin Profile</h2>
                <p class="text-xs text-slate-400 mt-0.5">Update administrator account &amp; upload photo</p>
            </div>
            <button type="button" data-modal-close class="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-500">✕</button>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Admin Name *</label>
                <input type="text" name="name" value="{{ $admin->name }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#163CA8] text-sm outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Email *</label>
                <input type="email" name="email" value="{{ $admin->email }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#163CA8] text-sm outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Upload Profile Photo (JPG, PNG, WEBP)</label>
                <input type="file" name="avatar" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-[#163CA8] hover:file:bg-blue-100">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">New Password</label>
                    <input type="password" name="password" placeholder="Optional" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Confirm</label>
                    <input type="password" name="password_confirmation" placeholder="Repeat" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none">
                </div>
            </div>

            <div class="pt-2 flex justify-end gap-2.5">
                <button type="button" data-modal-close class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600">Cancel</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#163CA8] hover:bg-[#123189] text-white text-xs font-bold shadow-md btn-press cursor-pointer">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
