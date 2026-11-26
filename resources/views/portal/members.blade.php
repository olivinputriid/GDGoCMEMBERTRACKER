@extends('layouts.portal')

@section('title', 'Members — GDGoC UNSRI Portal')
@section('page_title', 'Members')
@section('page_subtitle', auth()->user()?->isAdmin() ? 'Browse and manage member profiles' : 'Browse GDGoC UNSRI member directory (View Only)')

@section('content')
@php
    $isAdmin = auth()->user()?->isAdmin() ?? false;
@endphp
<div class="space-y-6">
    <!-- Filter & Action Toolbar -->
    <section class="portal-card p-4 sm:p-5 animate-fade-up">
        <form id="members-filter-form" action="{{ route('members.index') }}" method="GET" class="flex flex-col gap-3.5">
            <input type="hidden" name="status" id="filter-status-input" value="{{ $filters['status'] }}">
            <input type="hidden" name="view" id="filter-view-input" value="{{ $filters['view'] }}">

            <div class="flex flex-col lg:flex-row lg:items-center gap-3">
                <!-- Search Input -->
                <div class="relative flex-1">
                    <input type="text"
                           name="search"
                           value="{{ $filters['search'] }}"
                           placeholder="Search name, ID, division..."
                           class="w-full pl-4 pr-10 py-2.5 rounded-full border border-slate-200 focus:border-[#163CA8] focus:ring-3 focus:ring-blue-100 text-sm outline-none transition-all">
                    <button type="submit" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-[#163CA8]" aria-label="Search members">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
                    </button>
                </div>

                <!-- Division Select -->
                <div class="w-full lg:w-52">
                    <select name="division"
                            onchange="this.form.submit()"
                            class="w-full px-4 py-2.5 rounded-full border border-slate-200 focus:border-[#163CA8] text-sm font-medium text-slate-700 bg-white outline-none cursor-pointer">
                        <option value="all" @selected($filters['division'] === 'all')>All Divisions</option>
                        @foreach ($divisions as $division)
                            <option value="{{ $division->slug }}" @selected($filters['division'] === $division->slug)>
                                {{ $division->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Pills -->
                <div class="flex flex-wrap items-center gap-2">
                    @foreach (['All', 'Active', 'Inactive', 'Alumni'] as $statusOption)
                        @php $active = $filters['status'] === $statusOption; @endphp
                        <button type="button"
                                onclick="setStatusFilter('{{ $statusOption }}')"
                                class="px-4 py-2 rounded-full text-xs font-bold border transition-all btn-press cursor-pointer {{ $active ? 'bg-[#163CA8] border-[#163CA8] text-white shadow-sm' : 'bg-white border-slate-200 text-slate-500 hover:border-slate-300 hover:text-slate-800' }}">
                            {{ $statusOption }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                <!-- List vs Grid Toggle -->
                <div class="inline-flex items-center gap-1.5">
                    <button type="button"
                            onclick="setViewMode('list')"
                            title="List View"
                            class="w-9 h-9 rounded-xl border flex items-center justify-center transition-all btn-press cursor-pointer {{ $filters['view'] !== 'grid' ? 'bg-[#163CA8] border-[#163CA8] text-white shadow-xs' : 'bg-white border-slate-200 text-slate-500 hover:bg-slate-50' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <button type="button"
                            onclick="setViewMode('grid')"
                            title="Grid View"
                            class="w-9 h-9 rounded-xl border flex items-center justify-center transition-all btn-press cursor-pointer {{ $filters['view'] === 'grid' ? 'bg-[#163CA8] border-[#163CA8] text-white shadow-xs' : 'bg-white border-slate-200 text-slate-500 hover:bg-slate-50' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zm10 0a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/></svg>
                    </button>
                    @if ($filters['search'] !== '' || $filters['division'] !== 'all' || $filters['status'] !== 'All')
                        <a href="{{ route('members.index') }}" class="ml-2 text-xs font-semibold text-slate-400 hover:text-[#163CA8]">Reset filters</a>
                    @endif
                </div>

                @if ($isAdmin)
                    <button type="button"
                            data-modal-open="add-member-modal"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#163CA8] hover:bg-[#123189] text-white text-xs sm:text-sm font-bold shadow-md shadow-blue-900/15 btn-press cursor-pointer">
                        <span>+ Add Member</span>
                    </button>
                @else
                    <span class="px-3.5 py-1.5 rounded-full bg-slate-100 text-slate-500 text-xs font-bold">View Only Mode</span>
                @endif
            </div>
        </form>
    </section>

    <!-- Members List or Grid -->
    @if ($filters['view'] === 'grid')
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 animate-fade-up animate-stagger-1">
            @forelse ($members as $member)
                @php
                    $statusClasses = match ($member->status) {
                        'Alumni' => 'bg-blue-100 text-[#163CA8]',
                        'Inactive' => 'bg-slate-100 text-slate-500',
                        default => 'bg-emerald-100 text-emerald-700',
                    };
                    $attColor = $member->attendance_rate >= 90 ? '#16A34A' : ($member->attendance_rate >= 70 ? '#D97706' : '#DC2626');
                @endphp
                <div class="portal-card portal-card-hover p-5 flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between gap-3">
                            <a href="{{ route('members.show', $member) }}" class="flex items-center gap-3 group min-w-0">
                                <x-member-avatar :member="$member" size="md" />
                                <div class="min-w-0">
                                    <div class="text-sm font-extrabold text-slate-900 group-hover:text-[#163CA8] truncate">{{ $member->name }}</div>
                                    <div class="text-xs text-slate-400">{{ $member->member_code }} • Batch {{ $member->batch }}</div>
                                </div>
                            </a>
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold shrink-0 {{ $statusClasses }}">{{ $member->status }}</span>
                        </div>

                        <div class="mt-4 flex items-center justify-between text-xs text-slate-500">
                            <span class="font-semibold text-slate-700">{{ $member->division->name }}</span>
                            <span class="font-extrabold text-[#163CA8] text-sm">{{ $member->points }} pts</span>
                        </div>

                        <div class="mt-3">
                            <div class="flex items-center justify-between text-[11px] text-slate-400 mb-1">
                                <span>Attendance</span>
                                <span class="font-bold text-slate-600">{{ $member->attendance_rate }}%</span>
                            </div>
                            <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full rounded-full progress-fill" style="width: 0%; background-color: {{ $attColor }};" data-bar-width="{{ $member->attendance_rate }}"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                        <a href="{{ route('members.show', $member) }}" class="text-xs font-bold text-[#163CA8] hover:underline">View Profile →</a>
                        @if ($isAdmin)
                            <div class="flex items-center gap-2">
                                <button type="button"
                                        onclick='openEditMemberModal(@json($member))'
                                        class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-xs font-semibold text-slate-700 cursor-pointer">
                                    Edit
                                </button>
                                <button type="button"
                                        onclick="openAwardPointsModal({{ $member->id }}, @js($member->name))"
                                        class="px-2.5 py-1 rounded-lg bg-blue-50 hover:bg-blue-100 text-xs font-semibold text-[#163CA8] cursor-pointer">
                                    + Pts
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full portal-card p-10 text-center text-sm text-slate-400">
                    No members match your current filters.
                </div>
            @endforelse
        </section>
    @else
        <section class="portal-card overflow-hidden animate-fade-up animate-stagger-1">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 text-[11px] font-extrabold uppercase tracking-wider text-slate-400 bg-slate-50/50">
                            <th class="py-3.5 px-5">Member</th>
                            <th class="py-3.5 px-4">Division</th>
                            <th class="py-3.5 px-4">Batch</th>
                            <th class="py-3.5 px-4">Points</th>
                            <th class="py-3.5 px-4">Attendance</th>
                            <th class="py-3.5 px-4">Status</th>
                            <th class="py-3.5 px-5 text-right">{{ $isAdmin ? 'Actions' : 'Profile' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse ($members as $member)
                            @php
                                $statusClasses = match ($member->status) {
                                    'Alumni' => 'bg-blue-100 text-[#163CA8]',
                                    'Inactive' => 'bg-slate-100 text-slate-500',
                                    default => 'bg-emerald-100 text-emerald-700',
                                };
                                $attColor = $member->attendance_rate >= 90 ? '#16A34A' : ($member->attendance_rate >= 70 ? '#D97706' : '#DC2626');
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors group">
                                <td class="py-3.5 px-5">
                                    <a href="{{ route('members.show', $member) }}" class="flex items-center gap-3">
                                        <x-member-avatar :member="$member" size="sm" />
                                        <div>
                                            <div class="font-bold text-slate-900 group-hover:text-[#163CA8] transition-colors">{{ $member->name }}</div>
                                            <div class="text-xs text-slate-400">{{ $member->member_code }}</div>
                                        </div>
                                    </a>
                                </td>
                                <td class="py-3.5 px-4 text-xs sm:text-sm text-slate-500 font-medium">{{ $member->division->name }}</td>
                                <td class="py-3.5 px-4 text-xs sm:text-sm text-slate-400">{{ $member->batch }}</td>
                                <td class="py-3.5 px-4 font-extrabold text-[#163CA8]">{{ $member->points }}</td>
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-2.5 min-w-[110px]">
                                        <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full progress-fill" style="width: 0%; background-color: {{ $attColor }};" data-bar-width="{{ $member->attendance_rate }}"></div>
                                        </div>
                                        <span class="text-xs font-semibold text-slate-500 w-8">{{ $member->attendance_rate }}%</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold {{ $statusClasses }}">
                                        {{ $member->status }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-5 text-right relative">
                                    @if ($isAdmin)
                                        <button type="button"
                                                data-dropdown-toggle="member-action-{{ $member->id }}"
                                                class="w-8 h-8 rounded-full hover:bg-slate-100 inline-flex items-center justify-center text-slate-400 hover:text-slate-700 font-bold cursor-pointer">
                                            ···
                                        </button>
                                        <div id="member-action-{{ $member->id }}" data-dropdown-panel class="hidden absolute right-5 mt-1 w-44 bg-white rounded-2xl shadow-xl border border-slate-100 z-40 p-1.5 text-left">
                                            <a href="{{ route('members.show', $member) }}" class="block px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                                👤 View Profile
                                            </a>
                                            <button type="button"
                                                    onclick='openEditMemberModal(@json($member))'
                                                    class="w-full text-left px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50 cursor-pointer">
                                                ✏️ Edit &amp; Upload
                                            </button>
                                            <button type="button"
                                                    onclick="openAwardPointsModal({{ $member->id }}, @js($member->name))"
                                                    class="w-full text-left px-3 py-2 rounded-xl text-xs font-semibold text-[#163CA8] hover:bg-blue-50 cursor-pointer">
                                                ⭐ Award Points
                                            </button>
                                            <form action="{{ route('members.destroy', $member) }}" method="POST" onsubmit="return confirm('Delete member {{ addslashes($member->name) }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full text-left px-3 py-2 rounded-xl text-xs font-semibold text-red-600 hover:bg-red-50 cursor-pointer">
                                                    🗑️ Delete
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <a href="{{ route('members.show', $member) }}" class="text-xs font-bold text-[#163CA8] hover:underline">
                                            View →
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-10 text-center text-sm text-slate-400">
                                    No members found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-3.5 border-t border-slate-100 text-xs text-slate-400 flex items-center justify-between gap-4">
                <span>Showing {{ $members->firstItem() ?? 0 }}–{{ $members->lastItem() ?? 0 }} of {{ $members->total() }} members</span>
                <div class="flex items-center gap-1">
                    @if ($members->onFirstPage())
                        <span class="px-3 py-1.5 rounded-lg bg-slate-50 text-slate-300 text-xs font-semibold cursor-not-allowed">← Prev</span>
                    @else
                        <a href="{{ $members->previousPageUrl() }}&{{ http_build_query(request()->except('page')) }}" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 text-xs font-semibold text-slate-600 transition">← Prev</a>
                    @endif
                    <span class="px-3 py-1.5 text-xs font-bold text-slate-700">{{ $members->currentPage() }} / {{ $members->lastPage() }}</span>
                    @if ($members->hasMorePages())
                        <a href="{{ $members->nextPageUrl() }}&{{ http_build_query(request()->except('page')) }}" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-slate-50 text-xs font-semibold text-slate-600 transition">Next →</a>
                    @else
                        <span class="px-3 py-1.5 rounded-lg bg-slate-50 text-slate-300 text-xs font-semibold cursor-not-allowed">Next →</span>
                    @endif
                </div>
            </div>
        </section>
    @endif
</div>

@if ($isAdmin)
    <!-- Add Member Modal -->
    <div id="add-member-modal" class="portal-modal fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/45 backdrop-blur-xs p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 sm:p-7 max-h-[90vh] overflow-y-auto animate-modal-pop">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900">Add New Member</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Register a new GDGoC UNSRI member into the database</p>
                </div>
                <button type="button" data-modal-close class="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-500">✕</button>
            </div>

            <form action="{{ route('members.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Full Name *</label>
                        <input type="text" name="name" required placeholder="e.g. Putri Maharani" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#163CA8] text-sm outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Member ID</label>
                        <input type="text" name="member_code" placeholder="Auto (GDG...)" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#163CA8] text-sm outline-none uppercase">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Email *</label>
                        <input type="email" name="email" required placeholder="member@gdgocunsri.org" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#163CA8] text-sm outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Phone</label>
                        <input type="text" name="phone" placeholder="+62 812-..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#163CA8] text-sm outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Upload Member Photo (Optional)</label>
                    <input type="file" name="avatar" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-[#163CA8] hover:file:bg-blue-100">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Division *</label>
                        <select name="division_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#163CA8] text-sm outline-none bg-white">
                            @foreach ($divisions as $division)
                                <option value="{{ $division->id }}">{{ $division->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Role *</label>
                        <select name="role_title" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#163CA8] text-sm outline-none bg-white">
                            <option value="Member">Member</option>
                            <option value="Core Team">Core Team</option>
                            <option value="Division Lead">Division Lead</option>
                            <option value="Secretary">Secretary</option>
                            <option value="Former Lead">Former Lead</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Batch *</label>
                        <input type="number" name="batch" value="{{ now()->year }}" min="2018" max="2030" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-[#163CA8] text-sm outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Initial Pts</label>
                        <input type="number" name="points" value="100" min="0" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-[#163CA8] text-sm outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Attendance %</label>
                        <input type="number" name="attendance_rate" value="100" min="0" max="100" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-[#163CA8] text-sm outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status *</label>
                        <select name="status" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-[#163CA8] text-sm outline-none bg-white">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                            <option value="Alumni">Alumni</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Bio / Notes</label>
                    <textarea name="bio" rows="2" placeholder="Interests, tech stack, or contribution summary..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#163CA8] text-sm outline-none"></textarea>
                </div>

                <div class="pt-2 flex justify-end gap-2.5">
                    <button type="button" data-modal-close class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#163CA8] hover:bg-[#123189] text-white text-xs font-bold shadow-md btn-press cursor-pointer">Save Member</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Member Modal -->
    <div id="edit-member-modal" class="portal-modal fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/45 backdrop-blur-xs p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 sm:p-7 max-h-[90vh] overflow-y-auto animate-modal-pop">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-xl font-extrabold text-slate-900">Edit Member</h2>
                <button type="button" data-modal-close class="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-500">✕</button>
            </div>

            <form id="edit-member-form" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Full Name *</label>
                        <input id="edit-member-name" type="text" name="name" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#163CA8] text-sm outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Email *</label>
                        <input id="edit-member-email" type="email" name="email" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#163CA8] text-sm outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Upload New Photo (Optional)</label>
                    <input type="file" name="avatar" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-[#163CA8] hover:file:bg-blue-100">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Division *</label>
                        <select id="edit-member-division" name="division_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#163CA8] text-sm outline-none bg-white">
                            @foreach ($divisions as $division)
                                <option value="{{ $division->id }}">{{ $division->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Role *</label>
                        <input id="edit-member-role" type="text" name="role_title" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#163CA8] text-sm outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Batch *</label>
                        <input id="edit-member-batch" type="number" name="batch" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-[#163CA8] text-sm outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Contributions</label>
                        <input id="edit-member-contributions" type="number" name="contributions_count" min="0" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-[#163CA8] text-sm outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Competitions</label>
                        <input id="edit-member-competitions" type="number" name="competitions_count" min="0" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-[#163CA8] text-sm outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status *</label>
                        <select id="edit-member-status" name="status" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-[#163CA8] text-sm outline-none bg-white">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                            <option value="Alumni">Alumni</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Bio</label>
                    <textarea id="edit-member-bio" name="bio" rows="2" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#163CA8] text-sm outline-none"></textarea>
                </div>

                <div class="pt-2 flex justify-end gap-2.5">
                    <button type="button" data-modal-close class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#163CA8] hover:bg-[#123189] text-white text-xs font-bold shadow-md btn-press cursor-pointer">Update Member</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Award Points Modal -->
    <div id="award-points-modal" class="portal-modal fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/45 backdrop-blur-xs p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 animate-modal-pop">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-extrabold text-slate-900">Award Points</h2>
                    <p id="award-points-subtitle" class="text-xs text-slate-400"></p>
                </div>
                <button type="button" data-modal-close class="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-500">✕</button>
            </div>

            <form id="award-points-form" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Activity / Reason *</label>
                    <input type="text" name="title" required placeholder="e.g. GDG Workshop Speaker" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#163CA8] text-sm outline-none">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Category *</label>
                        <select name="category" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm outline-none bg-white">
                            <option value="Contribution">Contribution</option>
                            <option value="Event Staff">Event Staff</option>
                            <option value="Mentoring">Mentoring</option>
                            <option value="Study Jam">Study Jam</option>
                            <option value="Competition Win">Competition Win</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Points *</label>
                        <input type="number" name="points" value="50" min="1" max="5000" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Awarded Date *</label>
                    <input type="date" name="awarded_at" value="{{ now()->toDateString() }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm outline-none">
                </div>
                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" data-modal-close class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600">Cancel</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-[#163CA8] text-white text-xs font-bold shadow-md btn-press cursor-pointer">Award Points</button>
                </div>
            </form>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
    function setStatusFilter(status) {
        document.getElementById('filter-status-input').value = status;
        document.getElementById('members-filter-form').submit();
    }

    function setViewMode(mode) {
        document.getElementById('filter-view-input').value = mode;
        document.getElementById('members-filter-form').submit();
    }

    @if ($isAdmin)
    function openEditMemberModal(member) {
        document.getElementById('edit-member-form').action = `{{ url('/members') }}/${member.id}`;
        document.getElementById('edit-member-name').value = member.name || '';
        document.getElementById('edit-member-email').value = member.email || '';
        document.getElementById('edit-member-division').value = member.division_id;
        document.getElementById('edit-member-role').value = member.role_title || '';
        document.getElementById('edit-member-batch').value = member.batch || 2024;
        document.getElementById('edit-member-contributions').value = member.contributions_count || 0;
        document.getElementById('edit-member-competitions').value = member.competitions_count || 0;
        document.getElementById('edit-member-status').value = member.status || 'Active';
        document.getElementById('edit-member-bio').value = member.bio || '';
        window.openPortalModal('edit-member-modal');
    }

    function openAwardPointsModal(memberId, memberName) {
        document.getElementById('award-points-form').action = `{{ url('/members') }}/${memberId}/points`;
        document.getElementById('award-points-subtitle').textContent = `Assign points to ${memberName}`;
        window.openPortalModal('award-points-modal');
    }
    @endif
</script>
@endpush
