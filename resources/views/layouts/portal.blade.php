<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'GDGoC UNSRI Admin Portal')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F8FAFC] text-slate-900 min-h-screen antialiased">
    @php
        $user = auth()->user();
        $isAdmin = $user?->isAdmin() ?? false;
        $userAvatarUrl = $user?->avatar_url ?? $user?->member?->avatar_url;
        $userInitials = collect(explode(' ', $user?->name ?? 'Admin'))->filter()->take(2)->map(fn($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
        $navItems = [
            ['route' => 'home', 'label' => 'Home', 'icon' => '🏠', 'match' => 'home'],
            ['route' => 'dashboard', 'label' => 'Dashboard', 'icon' => '⊞', 'match' => 'dashboard'],
            ['route' => 'members.index', 'label' => 'Members', 'icon' => '👥', 'match' => 'members.*'],
            ['route' => 'leaderboard.index', 'label' => 'Leaderboard', 'icon' => '🏆', 'match' => 'leaderboard.*'],
            ['route' => 'attendance.index', 'label' => 'Attendance', 'icon' => '📅', 'match' => 'attendance.*'],
            ['route' => 'activities.index', 'label' => 'Activities', 'icon' => '🗓️', 'match' => 'activities.*'],
            ['route' => 'profile.show', 'label' => 'My Profile', 'icon' => '👤', 'match' => 'profile.*'],
        ];
    @endphp

    <!-- Mobile Sidebar Backdrop -->
    <div id="sidebar-backdrop" class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs z-40 hidden lg:hidden transition-opacity"></div>

    <div class="min-h-screen flex">
        <!-- Fixed Left Sidebar -->
        <aside id="portal-sidebar"
               class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-[#173FA8] via-[#15399C] to-[#123189] text-white flex flex-col justify-between transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-out shadow-xl lg:shadow-none">
            <div>
                <!-- Brand Header -->
                <div class="px-5 py-5 flex items-center justify-between border-b border-white/10">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center shadow-md group-hover:scale-105 transition-transform">
                            <x-gdg-logo size="sm" />
                        </div>
                        <div>
                            <div class="text-sm font-extrabold tracking-tight leading-tight">GDGoC UNSRI</div>
                            <div class="text-[10px] font-semibold tracking-widest text-blue-200 uppercase">{{ $isAdmin ? 'ADMIN PORTAL' : 'MEMBER PORTAL' }}</div>
                        </div>
                    </a>
                    <button id="sidebar-close" type="button" class="lg:hidden p-1.5 rounded-lg text-blue-100 hover:bg-white/10" aria-label="Close navigation">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Navigation Menu -->
                <nav class="px-3.5 py-5 space-y-1.5">
                    @foreach ($navItems as $item)
                        @php $isActive = request()->routeIs($item['match']); @endphp
                        <a href="{{ route($item['route']) }}"
                           class="flex items-center justify-between px-4 py-3 rounded-xl text-sm font-semibold transition-all duration-200 {{ $isActive ? 'bg-white text-[#163CA8] shadow-md' : 'text-blue-100/90 hover:bg-white/10 hover:text-white hover:translate-x-0.5' }}">
                            <span class="flex items-center gap-3">
                                <span class="text-base leading-none">{{ $item['icon'] }}</span>
                                <span>{{ $item['label'] }}</span>
                            </span>
                            @if ($isActive)
                                <span class="w-2 h-2 rounded-full bg-[#F97316]"></span>
                            @endif
                        </a>
                    @endforeach
                </nav>
            </div>

            <!-- Bottom User Card -->
            <div class="p-4">
                <div class="rounded-2xl bg-white/10 border border-white/15 p-3.5 backdrop-blur-sm">
                    <div class="flex items-center gap-3">
                        @if ($userAvatarUrl)
                            <img src="{{ $userAvatarUrl }}" alt="{{ $user?->name }}" class="w-9 h-9 rounded-full object-cover border-2 border-blue-200/70 shrink-0">
                        @else
                            <div class="w-9 h-9 rounded-full border-2 border-blue-200/70 bg-white/15 flex items-center justify-center text-xs font-bold shrink-0">
                                {{ $userInitials }}
                            </div>
                        @endif
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('profile.show') }}" class="block text-xs font-bold text-white truncate hover:underline">{{ $user?->name ?? 'Admin GDGoC UNSRI' }}</a>
                            <div class="flex items-center gap-1.5 text-[11px] text-blue-200 mt-0.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span>{{ $isAdmin ? 'Admin • Online' : 'Member • Online' }}</span>
                            </div>
                        </div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" title="Log Out" class="p-1.5 rounded-lg text-blue-100 hover:bg-white/15 hover:text-white transition-colors btn-press">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Workspace -->
        <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
            <!-- Top Header Bar -->
            <header class="sticky top-0 z-30 bg-white/90 backdrop-blur-md border-b border-slate-100 px-4 sm:px-8 py-4">
                <div class="max-w-7xl mx-auto flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3 min-w-0">
                        <button id="sidebar-toggle" type="button" class="lg:hidden p-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 btn-press" aria-label="Open navigation">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        <div class="min-w-0">
                            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight truncate">@yield('page_title', 'GDGoC UNSRI')</h1>
                            <p class="text-xs sm:text-sm text-slate-400 truncate">@yield('page_subtitle', 'Admin Portal')</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2.5 sm:gap-3">
                        <!-- Global Live Search -->
                        <div id="global-search-wrapper" class="relative hidden md:block w-64 lg:w-72">
                            <input id="global-search-input"
                                   type="text"
                                   autocomplete="off"
                                   data-search-url="{{ route('portal.search') }}"
                                   placeholder="Search anything..."
                                   class="w-full bg-[#F1F5F9] border border-transparent focus:border-blue-300 focus:bg-white rounded-full pl-4 pr-10 py-2 text-sm text-slate-700 placeholder-slate-400 outline-none transition-all">
                            <svg class="w-4 h-4 text-slate-400 absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
                            </svg>
                            <div id="global-search-results" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-xl border border-slate-100 z-50 overflow-hidden"></div>
                        </div>

                        <!-- Notification Bell -->
                        <div class="relative">
                            <button type="button"
                                    data-dropdown-toggle="notifications-dropdown"
                                    class="relative w-10 h-10 rounded-full bg-[#F8FAFC] border border-slate-200/80 hover:bg-slate-100 flex items-center justify-center transition-colors btn-press"
                                    aria-label="Notifications">
                                <span class="text-base">🔔</span>
                                @if (($unreadNotificationCount ?? 0) > 0)
                                    <span class="absolute -top-1 -right-1 min-w-[20px] h-5 px-1 rounded-full bg-[#EA4335] text-white text-[11px] font-bold flex items-center justify-center shadow-sm">
                                        {{ $unreadNotificationCount }}
                                    </span>
                                @endif
                            </button>

                            <div id="notifications-dropdown" data-dropdown-panel class="hidden absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-xl border border-slate-100 z-50 overflow-hidden">
                                <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                                    <div>
                                        <h3 class="text-sm font-bold text-slate-900">Notifications</h3>
                                        <p class="text-[11px] text-slate-400">{{ $unreadNotificationCount ?? 0 }} unread updates</p>
                                    </div>
                                    @if (($unreadNotificationCount ?? 0) > 0)
                                        <form action="{{ route('notifications.read-all') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-xs font-semibold text-[#163CA8] hover:underline">Mark all read</button>
                                        </form>
                                    @endif
                                </div>
                                <div class="max-h-80 overflow-y-auto divide-y divide-slate-50">
                                    @forelse ($portalNotifications ?? [] as $notification)
                                        <form action="{{ route('notifications.read', $notification) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="w-full text-left px-4 py-3 hover:bg-slate-50 transition-colors flex items-start gap-3 {{ $notification->is_read ? 'opacity-65' : 'bg-blue-50/30' }}">
                                                <span class="mt-1 w-2 h-2 rounded-full shrink-0 {{ $notification->is_read ? 'bg-slate-300' : 'bg-[#163CA8]' }}"></span>
                                                <div class="min-w-0 flex-1">
                                                    <div class="text-xs font-bold text-slate-800">{{ $notification->title }}</div>
                                                    <div class="text-xs text-slate-500 mt-0.5 leading-relaxed">{{ $notification->message }}</div>
                                                    <div class="text-[10px] text-slate-400 mt-1">{{ $notification->created_at?->diffForHumans() }}</div>
                                                </div>
                                            </button>
                                        </form>
                                    @empty
                                        <div class="px-4 py-6 text-center text-xs text-slate-400">No notifications yet.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Top Right User Avatar Menu -->
                        <div class="relative">
                            <button type="button"
                                    data-dropdown-toggle="user-menu-dropdown"
                                    class="w-10 h-10 rounded-full border-2 border-[#163CA8] bg-white hover:bg-blue-50 flex items-center justify-center text-xs font-extrabold text-[#163CA8] overflow-hidden transition-colors btn-press"
                                    aria-label="Account Menu">
                                @if ($userAvatarUrl)
                                    <img src="{{ $userAvatarUrl }}" alt="{{ $user?->name }}" class="w-full h-full object-cover">
                                @else
                                    {{ $userInitials }}
                                @endif
                            </button>

                            <div id="user-menu-dropdown" data-dropdown-panel class="hidden absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-slate-100 z-50 p-2">
                                <div class="px-3 py-2 border-b border-slate-100 mb-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-xs font-bold text-slate-800 truncate">{{ $user?->name }}</span>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $isAdmin ? 'bg-blue-100 text-[#163CA8]' : 'bg-emerald-100 text-emerald-700' }}">{{ $isAdmin ? 'Admin' : 'Member' }}</span>
                                    </div>
                                    <div class="text-[11px] text-slate-400 truncate mt-0.5">{{ $user?->email }}</div>
                                </div>
                                <a href="{{ route('profile.show') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                    <span>👤</span> My Profile
                                </a>
                                <a href="{{ route('members.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                    <span>👥</span> {{ $isAdmin ? 'Manage Members' : 'Browse Members' }}
                                </a>
                                <form action="{{ route('logout') }}" method="POST" class="mt-1 pt-1 border-t border-slate-100">
                                    @csrf
                                    <button type="submit" class="w-full text-left flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold text-red-600 hover:bg-red-50">
                                        <span>🚪</span> Log Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 px-4 sm:px-8 py-6 sm:py-8">
                <div class="max-w-7xl mx-auto">
                    @if ($errors->any())
                        <div class="mb-6 rounded-2xl bg-red-50 border border-red-200 px-5 py-4 text-sm text-red-700 animate-fade-up">
                            <div class="font-bold mb-1">Please check the following errors:</div>
                            <ul class="list-disc list-inside space-y-0.5 text-xs">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Toast Notifications Stack -->
    <div id="toast-stack" class="fixed bottom-5 right-5 z-[70] flex flex-col gap-2 pointer-events-none">
        @if (session('toast'))
            @php
                $toast = session('toast');
                $toastColor = match ($toast['type'] ?? 'success') {
                    'error' => 'bg-red-600 text-white',
                    'info' => 'bg-[#163CA8] text-white',
                    default => 'bg-emerald-600 text-white',
                };
            @endphp
            <div data-auto-dismiss class="animate-toast-in pointer-events-auto flex items-center gap-2.5 px-4 py-3 rounded-xl shadow-lg text-sm font-medium {{ $toastColor }}">
                <span>{{ $toast['message'] ?? '' }}</span>
            </div>
        @endif
    </div>

    @stack('scripts')
</body>
</html>
