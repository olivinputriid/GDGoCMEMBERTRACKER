@extends('layouts.portal')

@section('title', ($member->name ?? 'My Profile') . ' — GDGoC UNSRI Portal')
@section('page_title', $isOwnProfile ? 'My Profile' : 'Member Profile')
@section('page_subtitle', 'Your personal dashboard and history')

@section('content')
@php
    $isAdmin = auth()->user()?->isAdmin() ?? false;
@endphp
<div class="space-y-6">
    <!-- Profile Overview Card -->
    <section class="portal-card p-6 sm:p-8 animate-fade-up">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-5">
            <div class="flex items-center gap-5">
                @if ($member->avatar_url)
                    <img src="{{ $member->avatar_url }}"
                         alt="{{ $member->name }}"
                         class="w-20 h-20 rounded-3xl object-cover shadow-md border-2 border-blue-100 shrink-0">
                @else
                    <div class="w-20 h-20 rounded-3xl flex items-center justify-center text-2xl font-black text-white shadow-md shrink-0"
                         style="background: linear-gradient(135deg, {{ $member->avatar_color }}, {{ $member->avatar_color }}cc);">
                        {{ $member->initials }}
                    </div>
                @endif
                <div>
                    <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">{{ $member->name }}</h2>
                    <p class="text-xs sm:text-sm text-slate-400 mt-0.5">
                        {{ $member->role_title }} — {{ $member->division->name }}
                    </p>
                    <div class="mt-2 flex flex-wrap items-center gap-2">
                        @php
                            $statusClasses = match ($member->status) {
                                'Alumni' => 'bg-blue-100 text-[#163CA8]',
                                'Inactive' => 'bg-slate-100 text-slate-500',
                                default => 'bg-emerald-100 text-emerald-700',
                            };
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusClasses }}">{{ $member->status }}</span>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-500">{{ $member->member_code }} • Batch {{ $member->batch }}</span>
                        @if (! $isAdmin)
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700">View Only</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                @if ($isAdmin)
                    <button type="button"
                            data-modal-open="edit-profile-modal"
                            class="px-5 py-2.5 rounded-full border-2 border-[#163CA8] text-[#163CA8] hover:bg-blue-50 text-xs sm:text-sm font-bold btn-press cursor-pointer">
                        Edit &amp; Upload
                    </button>
                    @if ($member->avatar_url)
                        <form action="{{ route('members.avatar.delete', $member) }}" method="POST" onsubmit="return confirm('Remove profile photo?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2.5 rounded-full border border-red-200 text-red-600 hover:bg-red-50 text-xs font-bold btn-press cursor-pointer">
                                Remove Photo
                            </button>
                        </form>
                    @endif
                    <form action="{{ route('members.destroy', $member) }}" method="POST" onsubmit="return confirm('Delete member {{ addslashes($member->name) }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2.5 rounded-full bg-red-50 hover:bg-red-100 text-red-600 text-xs sm:text-sm font-bold btn-press cursor-pointer">
                            Delete
                        </button>
                    </form>
                @endif
                <button type="button"
                        onclick="shareProfileUrl('{{ route('members.show', $member) }}')"
                        class="px-5 py-2.5 rounded-full bg-[#163CA8] hover:bg-[#123189] text-white text-xs sm:text-sm font-bold shadow-md shadow-blue-900/15 btn-press cursor-pointer">
                    Share
                </button>
                @if ($isOwnProfile && !auth()->user()?->isAdmin())
                <button type="button" data-modal-open="edit-my-profile-modal"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#163CA8] hover:bg-[#123189] text-white text-xs font-bold shadow-md btn-press cursor-pointer">
                    ✏️ Edit Profile
                </button>
                @endif
            </div>
        </div>

        @if ($member->bio)
            <p class="mt-4 text-xs sm:text-sm text-slate-500 leading-relaxed max-w-3xl">{{ $member->bio }}</p>
        @endif

        <!-- 5 Metric Boxes -->
        <div class="mt-6 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5">
            <div class="rounded-2xl bg-[#EEF2FF] p-4 text-center">
                <div class="text-xl sm:text-2xl font-black text-[#163CA8]" data-counter="{{ $member->points }}">0</div>
                <div class="text-xs text-slate-400 font-medium mt-0.5">Total Points</div>
            </div>

            <div class="rounded-2xl bg-[#FFF6EB] p-4 text-center">
                <div class="text-xl sm:text-2xl font-black text-amber-600">#{{ $rank }}</div>
                <div class="text-xs text-slate-400 font-medium mt-0.5">Rank</div>
            </div>

            <div class="rounded-2xl bg-[#ECFDF3] p-4 text-center">
                <div class="text-xl sm:text-2xl font-black text-emerald-600" data-counter="{{ $member->attendance_rate }}" data-suffix="%">0%</div>
                <div class="text-xs text-slate-400 font-medium mt-0.5">Attendance</div>
            </div>

            <div class="rounded-2xl bg-[#FFF6EB] p-4 text-center">
                <div class="text-xl sm:text-2xl font-black text-amber-600" data-counter="{{ $member->contributions_count }}">0</div>
                <div class="text-xs text-slate-400 font-medium mt-0.5">Contributions</div>
            </div>

            <div class="rounded-2xl bg-[#FEF2F2] p-4 text-center col-span-2 sm:col-span-1">
                <div class="text-xl sm:text-2xl font-black text-rose-600" data-counter="{{ $member->competitions_count }}">0</div>
                <div class="text-xs text-slate-400 font-medium mt-0.5">Competitions</div>
            </div>
        </div>
    </section>

    <!-- Bottom 2 Columns: My Achievements & Recent Point Activity -->
    <section class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Achievements -->
        <div class="lg:col-span-4 portal-card p-6 animate-fade-up animate-stagger-1">
            <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2 mb-5">
                <span>🎖️</span>
                <span>{{ $isOwnProfile ? 'My Achievements' : 'Achievements' }}</span>
            </h3>

            <div class="grid grid-cols-2 gap-3">
                @forelse ($member->achievements as $badge)
                    <div class="rounded-2xl p-4 text-center transition-transform hover:-translate-y-0.5"
                         style="background-color: {{ $badge->bg_color }};"
                         title="{{ $badge->description }}">
                        <div class="text-2xl mb-1.5">{{ $badge->icon }}</div>
                        <div class="text-xs font-extrabold leading-tight" style="color: {{ $badge->text_color }};">
                            {{ $badge->name }}
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 py-8 text-center text-xs text-slate-400">
                        Earn points from activities to unlock your first achievement badge!
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Point Activity -->
        <div class="lg:col-span-8 portal-card p-6 flex flex-col justify-between animate-fade-up animate-stagger-2">
            <div>
                <div class="flex items-center justify-between gap-2 mb-5">
                    <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <span>📊</span>
                        <span>Recent Point Activity</span>
                    </h3>
                    @if ($isAdmin)
                        <button type="button"
                                data-modal-open="profile-award-points-modal"
                                class="px-3.5 py-1.5 rounded-full bg-blue-50 hover:bg-blue-100 text-[#163CA8] text-xs font-bold btn-press cursor-pointer">
                            + Log Points
                        </button>
                    @endif
                </div>

                <div class="space-y-3">
                    @forelse ($member->pointLogs->take(6) as $log)
                        <div class="flex items-center justify-between gap-3 p-3.5 rounded-2xl bg-[#F8FAFC] hover:bg-slate-100/80 transition-colors">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-emerald-100/80 flex items-center justify-center text-lg shrink-0">🏅</div>
                                <div class="min-w-0">
                                    <div class="text-sm font-bold text-slate-900 truncate">{{ $log->title }}</div>
                                    <div class="text-xs text-slate-400">{{ $log->category }} • {{ $log->awarded_at->format('M j, Y') }}</div>
                                </div>
                            </div>
                            <span class="text-sm font-extrabold text-emerald-600 shrink-0">+{{ $log->points }} pts</span>
                        </div>
                    @empty
                        <div class="py-8 text-center text-xs text-slate-400">No point activity logged yet.</div>
                    @endforelse
                </div>
            </div>

            <!-- Milestone Progress Banner -->
            <div class="mt-6 rounded-2xl bg-[#DCE8FF] p-5">
                <div class="flex items-center justify-between gap-2 text-xs sm:text-sm font-extrabold text-[#163CA8]">
                    <span>Progress to {{ $nextMilestone }} pts milestone</span>
                    <span>{{ $member->points }}/{{ $nextMilestone }}</span>
                </div>
                <div class="w-full h-2.5 bg-white/75 rounded-full overflow-hidden mt-3">
                    <div class="h-full rounded-full bg-[#163CA8] progress-fill" style="width: 0%;" data-bar-width="{{ $milestonePercent }}"></div>
                </div>
                <p class="text-xs text-[#163CA8]/80 font-medium mt-2.5">
                    Only {{ $remainingPoints }} pts more to unlock the next achievement!
                </p>
            </div>
        </div>
    </section>
</div>

@if ($isAdmin)
    <!-- Edit Member Profile Modal (Admin Only) -->
    <div id="edit-profile-modal" class="portal-modal fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/45 backdrop-blur-xs p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 sm:p-7 max-h-[90vh] overflow-y-auto animate-modal-pop">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-xl font-extrabold text-slate-900">Edit Member Profile</h2>
                <button type="button" data-modal-close class="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-500">✕</button>
            </div>

            <form action="{{ route('members.update', $member) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Full Name *</label>
                        <input type="text" name="name" value="{{ $member->name }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Email *</label>
                        <input type="email" name="email" value="{{ $member->email }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Upload Member Photo (JPG, PNG, WEBP)</label>
                    <input type="file" name="avatar" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-[#163CA8] hover:file:bg-blue-100">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Division *</label>
                        <select name="division_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none bg-white">
                            @foreach ($divisions as $division)
                                <option value="{{ $division->id }}" @selected($member->division_id === $division->id)>{{ $division->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Role *</label>
                        <input type="text" name="role_title" value="{{ $member->role_title }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Batch *</label>
                        <input type="number" name="batch" value="{{ $member->batch }}" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Contributions</label>
                        <input type="number" name="contributions_count" value="{{ $member->contributions_count }}" min="0" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Competitions</label>
                        <input type="number" name="competitions_count" value="{{ $member->competitions_count }}" min="0" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status *</label>
                        <select name="status" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm outline-none bg-white">
                            @foreach (['Active', 'Inactive', 'Alumni'] as $st)
                                <option value="{{ $st }}" @selected($member->status === $st)>{{ $st }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Bio</label>
                    <textarea name="bio" rows="2" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none">{{ $member->bio }}</textarea>
                </div>

                <div class="pt-2 flex justify-end gap-2.5">
                    <button type="button" data-modal-close class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#163CA8] hover:bg-[#123189] text-white text-xs font-bold shadow-md btn-press cursor-pointer">Save Profile</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Log Point Activity Modal (Admin Only) -->
    <div id="profile-award-points-modal" class="portal-modal fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/45 backdrop-blur-xs p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 animate-modal-pop">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-extrabold text-slate-900">Log Point Activity</h2>
                <button type="button" data-modal-close class="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-500">✕</button>
            </div>
            <form action="{{ route('members.points', $member) }}" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Activity Title *</label>
                    <input type="text" name="title" required placeholder="e.g. Solution Challenge Mentor" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Category *</label>
                        <select name="category" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white">
                            <option value="Competition Win">Competition Win</option>
                            <option value="Event Staff">Event Staff</option>
                            <option value="Mentoring">Mentoring</option>
                            <option value="Study Jam">Study Jam</option>
                            <option value="Contribution">Contribution</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Points *</label>
                        <input type="number" name="points" value="50" min="1" max="5000" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Date *</label>
                    <input type="date" name="awarded_at" value="{{ now()->toDateString() }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm">
                </div>
                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" data-modal-close class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-[#163CA8] text-white text-xs font-bold btn-press cursor-pointer">Add Points</button>
                </div>
            </form>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
    function shareProfileUrl(url) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(url).then(() => {
                window.showToast('Profile link copied to clipboard!', 'info');
            }).catch(() => {
                window.showToast('Profile URL: ' + url, 'info');
            });
        } else {
            window.showToast('Profile URL: ' + url, 'info');
        }
    }
</script>
@endpush
