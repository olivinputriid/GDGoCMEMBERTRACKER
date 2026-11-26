@extends('layouts.portal')

@section('title', 'Activities — GDGoC UNSRI Portal')
@section('page_title', 'Activities')
@section('page_subtitle', 'Organizational events and programs')

@section('content')
@php
    $isAdmin = auth()->user()?->isAdmin() ?? false;
@endphp
<div class="space-y-6">
    <!-- Filter Tabs & Create Button -->
    <div class="flex flex-wrap items-center justify-between gap-3 animate-fade-up">
        <div class="flex flex-wrap items-center gap-2.5">
            @foreach ([
                'all' => 'All',
                'upcoming' => 'Upcoming',
                'completed' => 'Completed',
            ] as $key => $label)
                @php $active = $activeTab === $key; @endphp
                <a href="{{ route('activities.index', ['tab' => $key, 'search' => $search]) }}"
                   class="px-5 py-2.5 rounded-full text-xs sm:text-sm font-bold border transition-all btn-press {{ $active ? 'bg-[#163CA8] border-[#163CA8] text-white shadow-md shadow-blue-900/15' : 'bg-white border-slate-200 text-slate-500 hover:border-slate-300 hover:text-slate-800' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        @if ($isAdmin)
            <button type="button"
                    data-modal-open="create-activity-modal"
                    class="px-5 py-2.5 rounded-full bg-[#163CA8] hover:bg-[#123189] text-white text-xs sm:text-sm font-bold shadow-md shadow-blue-900/15 btn-press cursor-pointer">
                + Create Activity
            </button>
        @else
            <span class="px-3.5 py-1.5 rounded-full bg-slate-100 text-slate-500 text-xs font-bold">View Only</span>
        @endif
    </div>

    <!-- Activity Cards 2-Column Grid -->
    <section class="grid grid-cols-1 md:grid-cols-2 gap-5 animate-fade-up animate-stagger-1">
        @php
            $categoryMeta = [
                'Conference' => ['icon' => '🎯', 'iconBg' => 'bg-blue-50', 'pill' => 'bg-blue-100 text-[#163CA8]', 'bar' => '#163CA8'],
                'Workshop' => ['icon' => '🛠️', 'iconBg' => 'bg-amber-50', 'pill' => 'bg-amber-100 text-amber-700', 'bar' => '#D97706'],
                'Study Jam' => ['icon' => '📖', 'iconBg' => 'bg-emerald-50', 'pill' => 'bg-emerald-100 text-emerald-700', 'bar' => '#16A34A'],
                'Meeting' => ['icon' => '📋', 'iconBg' => 'bg-slate-100', 'pill' => 'bg-slate-200/80 text-slate-600', 'bar' => '#64748B'],
                'Hackathon' => ['icon' => '💻', 'iconBg' => 'bg-rose-50', 'pill' => 'bg-rose-100 text-rose-700', 'bar' => '#DC2626'],
                'Recruitment' => ['icon' => '📣', 'iconBg' => 'bg-indigo-50', 'pill' => 'bg-blue-100 text-[#163CA8]', 'bar' => '#163CA8'],
            ];
        @endphp

        @forelse ($activities as $activity)
            @php
                $meta = $categoryMeta[$activity->category] ?? ['icon' => '📅', 'iconBg' => 'bg-blue-50', 'pill' => 'bg-blue-100 text-[#163CA8]', 'bar' => '#163CA8'];
                $isUpcoming = $activity->status === 'Upcoming';
                $activityPayload = [
                    'id' => $activity->id,
                    'title' => $activity->title,
                    'category' => $activity->category,
                    'status' => $activity->status,
                    'event_date' => $activity->event_date->format('Y-m-d'),
                    'event_date_formatted' => $activity->event_date->format('M j, Y'),
                    'location' => $activity->location,
                    'max_attendees' => $activity->max_attendees,
                    'joined_count' => $activity->joined_count,
                    'points_reward' => $activity->points_reward,
                    'banner_url' => $activity->banner_url,
                    'description' => $activity->description,
                ];
            @endphp

            <article onclick='openActivityDetailModal(@json($activityPayload))'
                     class="portal-card portal-card-hover p-6 cursor-pointer flex flex-col justify-between">
                <div>
                    @if ($activity->banner_url)
                        <img src="{{ $activity->banner_url }}" alt="{{ $activity->title }}" class="w-full h-36 object-cover rounded-2xl mb-4 border border-slate-100">
                    @endif

                    <div class="flex items-start justify-between gap-3">
                        <div class="w-12 h-12 rounded-2xl {{ $meta['iconBg'] }} flex items-center justify-center text-2xl shrink-0">
                            {{ $meta['icon'] }}
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $isUpcoming ? 'bg-blue-100 text-[#163CA8]' : 'bg-slate-100 text-slate-500' }}">
                            {{ $activity->status }}
                        </span>
                    </div>

                    <h3 class="mt-4 text-base sm:text-lg font-extrabold text-slate-900 leading-snug">{{ $activity->title }}</h3>
                    <span class="inline-block mt-2 px-3 py-0.5 rounded-full text-[11px] font-bold {{ $meta['pill'] }}">
                        {{ $activity->category }}
                    </span>

                    <div class="mt-4 space-y-1.5 text-xs text-slate-400">
                        <div class="flex items-center gap-2">
                            <span>📅</span>
                            <span>{{ $activity->event_date->format('M j, Y') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span>👥</span>
                            <span>{{ $activity->joined_count }}/{{ $activity->max_attendees }} attendees</span>
                        </div>
                    </div>
                </div>

                <div class="mt-5">
                    <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full progress-fill" style="width: 0%; background-color: {{ $meta['bar'] }};" data-bar-width="{{ $activity->progress_percent }}"></div>
                    </div>

                    <div class="mt-3.5 flex items-center justify-between text-xs">
                        <span class="text-slate-400 font-medium">{{ $activity->joined_count }}/{{ $activity->max_attendees }} joined</span>
                        <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 font-extrabold">+{{ $activity->points_reward }} pts</span>
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-full portal-card p-10 text-center text-sm text-slate-400">
                No activities found for this filter.
            </div>
        @endforelse

        @if ($activities->hasPages())
        <div class="mt-4 col-span-full flex items-center justify-center gap-2">
            @if (!$activities->onFirstPage())
                <a href="{{ $activities->previousPageUrl() }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-xs font-semibold text-slate-600 transition">← Prev</a>
            @endif
            <span class="px-3 py-2 text-xs font-bold text-slate-500">{{ $activities->currentPage() }} / {{ $activities->lastPage() }}</span>
            @if ($activities->hasMorePages())
                <a href="{{ $activities->nextPageUrl() }}" class="px-4 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-xs font-semibold text-slate-600 transition">Next →</a>
            @endif
        </div>
        @endif
    </section>
</div>

@if ($isAdmin)
    <!-- Create Activity Modal (Admin Only) -->
    <div id="create-activity-modal" class="portal-modal fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/45 backdrop-blur-xs p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 sm:p-7 max-h-[90vh] overflow-y-auto animate-modal-pop">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900">Create Activity</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Add an upcoming or completed GDGoC UNSRI event</p>
                </div>
                <button type="button" data-modal-close class="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-500">✕</button>
            </div>

            <form action="{{ route('activities.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Activity Title *</label>
                    <input type="text" name="title" required placeholder="e.g. Cloud Study Jam 2026" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#163CA8] text-sm outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Category *</label>
                        <select name="category" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none bg-white">
                            <option value="Workshop">Workshop</option>
                            <option value="Conference">Conference</option>
                            <option value="Study Jam">Study Jam</option>
                            <option value="Hackathon">Hackathon</option>
                            <option value="Recruitment">Recruitment</option>
                            <option value="Meeting">Meeting</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status *</label>
                        <select name="status" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none bg-white">
                            <option value="Upcoming">Upcoming</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Event Date *</label>
                        <input type="date" name="event_date" value="{{ now()->addDays(7)->toDateString() }}" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Max Attendees *</label>
                        <input type="number" name="max_attendees" value="100" min="1" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Points Reward *</label>
                        <input type="number" name="points_reward" value="50" min="0" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Upload Event Banner / Poster (Optional)</label>
                    <input type="file" name="banner" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-[#163CA8] hover:file:bg-blue-100">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Location</label>
                    <input type="text" name="location" placeholder="e.g. Auditorium UNSRI / Google Meet" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Description</label>
                    <textarea name="description" rows="2" placeholder="Short event summary..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none"></textarea>
                </div>

                <div class="pt-2 flex justify-end gap-2.5">
                    <button type="button" data-modal-close class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#163CA8] hover:bg-[#123189] text-white text-xs font-bold shadow-md btn-press cursor-pointer">Create Activity</button>
                </div>
            </form>
        </div>
    </div>
@endif

<!-- Activity Detail Modal (Admin gets Edit/Upload/Check-In/Delete; Member gets Read-Only view) -->
<div id="activity-detail-modal" class="portal-modal fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/45 backdrop-blur-xs p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 sm:p-7 max-h-[90vh] overflow-y-auto animate-modal-pop">
        <div class="flex items-start justify-between gap-3 mb-4">
            <div>
                <span id="detail-act-category" class="inline-block px-2.5 py-0.5 rounded-full bg-blue-100 text-[#163CA8] text-[11px] font-bold mb-1.5"></span>
                <h2 id="detail-act-title" class="text-xl font-extrabold text-slate-900"></h2>
                <p id="detail-act-meta" class="text-xs text-slate-400 mt-1"></p>
            </div>
            <button type="button" data-modal-close class="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-500">✕</button>
        </div>

        <img id="detail-act-banner" src="" alt="Activity Banner" class="hidden w-full h-44 object-cover rounded-2xl mb-4 border border-slate-100">

        <p id="detail-act-desc" class="text-xs sm:text-sm text-slate-600 bg-slate-50 rounded-2xl p-4 mb-5"></p>

        @if ($isAdmin)
            <!-- Quick Member Check-in -->
            <div class="rounded-2xl border border-slate-200 p-4 mb-5">
                <h3 class="text-xs font-extrabold uppercase tracking-wider text-slate-500 mb-3">Quick Check-In Member</h3>
                <form id="detail-checkin-form" method="POST" class="flex flex-col sm:flex-row gap-2.5">
                    @csrf
                    <select name="member_id" required class="flex-1 px-3 py-2 rounded-xl border border-slate-200 text-xs outline-none bg-white">
                        @foreach ($members as $member)
                            <option value="{{ $member->id }}">{{ $member->member_code }} — {{ $member->name }}</option>
                        @endforeach
                    </select>
                    <select name="status" class="px-3 py-2 rounded-xl border border-slate-200 text-xs outline-none bg-white">
                        <option value="Present">Present</option>
                        <option value="Late">Late</option>
                    </select>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold btn-press cursor-pointer">
                        Check In
                    </button>
                </form>
            </div>

            <!-- Edit Activity Form -->
            <details class="rounded-2xl border border-slate-200 p-4 mb-4">
                <summary class="text-xs font-extrabold text-[#163CA8] cursor-pointer">Edit Activity &amp; Upload Banner</summary>
                <form id="detail-edit-form" method="POST" enctype="multipart/form-data" class="space-y-3 mt-4">
                    @csrf
                    @method('PUT')
                    <input id="edit-act-title" type="text" name="title" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs outline-none">
                    <div class="grid grid-cols-2 gap-2.5">
                        <select id="edit-act-category" name="category" class="px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                            <option value="Workshop">Workshop</option>
                            <option value="Conference">Conference</option>
                            <option value="Study Jam">Study Jam</option>
                            <option value="Hackathon">Hackathon</option>
                            <option value="Recruitment">Recruitment</option>
                            <option value="Meeting">Meeting</option>
                        </select>
                        <select id="edit-act-status" name="status" class="px-3 py-2 rounded-xl border border-slate-200 text-xs bg-white">
                            <option value="Upcoming">Upcoming</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-3 gap-2.5">
                        <input id="edit-act-date" type="date" name="event_date" required class="px-3 py-2 rounded-xl border border-slate-200 text-xs">
                        <input id="edit-act-max" type="number" name="max_attendees" min="1" required class="px-3 py-2 rounded-xl border border-slate-200 text-xs">
                        <input id="edit-act-points" type="number" name="points_reward" min="0" required class="px-3 py-2 rounded-xl border border-slate-200 text-xs">
                    </div>
                    <input type="file" name="banner" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-[#163CA8]">
                    <input id="edit-act-location" type="text" name="location" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs">
                    <textarea id="edit-act-desc" name="description" rows="2" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs"></textarea>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-[#163CA8] text-white text-xs font-bold cursor-pointer">Save Changes</button>
                </form>
            </details>

            <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t border-slate-100">
                <div class="flex items-center gap-2">
                    <form id="detail-delete-form" method="POST" onsubmit="return confirm('Delete this activity?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3.5 py-2 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 text-xs font-bold cursor-pointer">Delete Activity</button>
                    </form>
                    <form id="detail-delete-banner-form" method="POST" class="hidden" onsubmit="return confirm('Remove banner image?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3.5 py-2 rounded-xl border border-red-200 text-red-600 text-xs font-bold cursor-pointer">Remove Banner</button>
                    </form>
                </div>
                <button type="button" data-modal-close class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600">Close</button>
            </div>
        @else
            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                <span class="text-xs text-slate-400 font-medium">Read-only event details</span>
                <button type="button" data-modal-close class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600">Close</button>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openActivityDetailModal(act) {
        document.getElementById('detail-act-category').textContent = `${act.category} • ${act.status}`;
        document.getElementById('detail-act-title').textContent = act.title;
        document.getElementById('detail-act-meta').textContent = `📅 ${act.event_date_formatted} • 📍 ${act.location || 'UNSRI'} • 👥 ${act.joined_count}/${act.max_attendees} • +${act.points_reward} pts`;
        document.getElementById('detail-act-desc').textContent = act.description || 'No additional description provided.';

        const bannerImg = document.getElementById('detail-act-banner');
        if (act.banner_url) {
            bannerImg.src = act.banner_url;
            bannerImg.classList.remove('hidden');
        } else {
            bannerImg.src = '';
            bannerImg.classList.add('hidden');
        }

        @if ($isAdmin)
        document.getElementById('detail-checkin-form').action = `{{ url('/activities') }}/${act.id}/check-in`;
        document.getElementById('detail-edit-form').action = `{{ url('/activities') }}/${act.id}`;
        document.getElementById('detail-delete-form').action = `{{ url('/activities') }}/${act.id}`;

        const deleteBannerForm = document.getElementById('detail-delete-banner-form');
        if (act.banner_url) {
            deleteBannerForm.action = `{{ url('/activities') }}/${act.id}/banner`;
            deleteBannerForm.classList.remove('hidden');
        } else {
            deleteBannerForm.classList.add('hidden');
        }

        document.getElementById('edit-act-title').value = act.title;
        document.getElementById('edit-act-category').value = act.category;
        document.getElementById('edit-act-status').value = act.status;
        document.getElementById('edit-act-date').value = act.event_date;
        document.getElementById('edit-act-max').value = act.max_attendees;
        document.getElementById('edit-act-points').value = act.points_reward;
        document.getElementById('edit-act-location').value = act.location || '';
        document.getElementById('edit-act-desc').value = act.description || '';
        @endif

        window.openPortalModal('activity-detail-modal');
    }
</script>
@endpush
