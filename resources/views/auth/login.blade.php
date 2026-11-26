<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Log In — GDGoC UNSRI Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen relative flex flex-col justify-between overflow-x-hidden bg-[#F4F6FB] text-slate-900">
   
    <div class="fixed inset-0 pointer-events-none overflow-hidden -z-10">
        <div class="animate-float-slow absolute -top-24 -left-20 w-[440px] h-[440px] rounded-full bg-blue-200/45 blur-3xl"></div>
        <div class="animate-float-reverse absolute top-1/4 -right-24 w-[420px] h-[420px] rounded-full bg-amber-200/40 blur-3xl"></div>
        <div class="animate-float-slow absolute -bottom-24 left-1/4 w-[460px] h-[460px] rounded-full bg-rose-200/35 blur-3xl"></div>
        <div class="animate-float-reverse absolute bottom-10 right-1/4 w-[360px] h-[360px] rounded-full bg-emerald-200/35 blur-3xl"></div>
    </div>

    
    <header class="w-full max-w-6xl mx-auto px-6 pt-6 flex items-center justify-end">
        <div class="relative">
            <button type="button"
                    data-dropdown-toggle="role-quick-dropdown"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/80 backdrop-blur-sm border border-slate-200/70 text-sm font-bold text-[#163CA8] shadow-xs hover:bg-white transition-all btn-press">
                <span id="role-label">Admin</span>
                <span class="w-5 h-5 rounded-full bg-[#163CA8] text-white inline-flex items-center justify-center text-[11px]">👤</span>
                <svg class="w-3.5 h-3.5 text-[#163CA8]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
            </button>

            <div id="role-quick-dropdown" data-dropdown-panel class="hidden absolute right-0 mt-2 w-60 bg-white rounded-2xl shadow-xl border border-slate-100 p-2 z-40">
                <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">Quick Fill Portal Account</div>
                <button type="button" onclick="fillDemoAccount('admin@gdgocunsri.org', 'password', 'Admin')"
                        class="w-full text-left px-3 py-2 rounded-xl hover:bg-blue-50 transition-colors flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-slate-800">Admin GDGoC UNSRI</div>
                        <div class="text-[11px] text-slate-400">admin@gdgocunsri.org</div>
                    </div>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-100 text-[#163CA8]">Admin</span>
                </button>
                <button type="button" onclick="fillDemoAccount('rafi.aditya@gdgocunsri.org', 'password', 'Member')"
                        class="w-full text-left px-3 py-2 rounded-xl hover:bg-emerald-50 transition-colors flex items-center justify-between mt-1">
                    <div>
                        <div class="text-xs font-bold text-slate-800">Rafi Aditya Pratama</div>
                        <div class="text-[11px] text-slate-400">rafi.aditya@gdgocunsri.org</div>
                    </div>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">Member</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Centered Card -->
    <main class="flex-1 flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-[440px] bg-white/95 backdrop-blur-md rounded-3xl shadow-[0_20px_60px_rgba(15,23,42,0.08)] border border-white p-8 sm:p-10 animate-fade-up">
            <!-- Brand Header -->
            <div class="flex items-center justify-center gap-3 mb-7">
                <x-gdg-logo size="md" />
                <span class="text-lg font-bold text-slate-800 tracking-tight">Google Developer Groups</span>
            </div>

            <div class="text-center mb-6">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Log In</h1>
                <p class="text-sm text-slate-400 mt-1.5">Use your GDGoC Account</p>
            </div>

            @if ($errors->any())
                <div class="mb-5 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-xs text-red-700">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('login.submit') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <input id="login-email"
                           type="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           placeholder="Email"
                           class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-[#163CA8] focus:ring-3 focus:ring-blue-100 text-sm text-slate-800 placeholder-slate-400 outline-none transition-all">
                </div>

                <div class="relative">
                    <input id="login-password"
                           type="password"
                           name="password"
                           required
                           placeholder="Enter Your Password"
                           class="w-full px-4 py-3 pr-11 rounded-xl border border-slate-200 focus:border-[#163CA8] focus:ring-3 focus:ring-blue-100 text-sm text-slate-800 placeholder-slate-400 outline-none transition-all">
                    <button type="button"
                            onclick="togglePasswordVisibility('login-password', this)"
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 p-1"
                            aria-label="Toggle password visibility">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                </div>

                <div class="flex items-center justify-between pt-0.5">
                    <button type="button" data-modal-open="forgot-password-modal" class="text-xs font-bold text-[#163CA8] hover:underline">
                        Forgot Password?
                    </button>
                    <label class="inline-flex items-center gap-1.5 text-xs text-slate-500 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-slate-300 text-[#163CA8] focus:ring-[#163CA8]">
                        <span>Remember me</span>
                    </label>
                </div>

                <button type="submit"
                        class="w-full py-3 rounded-full bg-[#163CA8] hover:bg-[#123189] text-white text-sm font-bold shadow-md shadow-blue-900/15 transition-all btn-press cursor-pointer">
                    Log In
                </button>
            </form>

            <!-- Divider -->
            <div class="my-5 flex items-center gap-3">
                <div class="flex-1 h-px bg-slate-200"></div>
                <span class="text-xs text-slate-400 font-medium">Or</span>
                <div class="flex-1 h-px bg-slate-200"></div>
            </div>

            <!-- Google Login Button -->
            <button type="button"
                    data-modal-open="google-auth-modal"
                    class="w-full py-2.5 px-4 rounded-xl border border-slate-200 hover:bg-slate-50 bg-white flex items-center justify-center gap-2.5 text-sm font-semibold text-slate-700 transition-all btn-press cursor-pointer">
                <svg class="w-4 h-4" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.17z"/>
                    <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.11-6.72-4.96H1.29v3.14C3.26 21.3 7.31 24 12 24z"/>
                    <path fill="#FBBC05" d="M5.28 14.24c-.24-.72-.38-1.49-.38-2.24s.14-1.52.38-2.24V6.62H1.29C.47 8.24 0 10.06 0 12s.47 3.76 1.29 5.38l3.99-3.14z"/>
                    <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.31 0 3.26 2.7 1.29 6.62l3.99 3.14c.95-2.85 3.6-4.96 6.72-4.96z"/>
                </svg>
                <span>Log In with Google</span>
            </button>

            <p class="mt-6 text-center text-xs text-slate-400">
                Don't have an account?
                <button type="button" data-modal-open="register-modal" class="font-bold text-[#163CA8] hover:underline cursor-pointer">
                    Create Account
                </button>
            </p>

            <!-- Language Switcher -->
            <div class="mt-7 flex justify-end">
                <form action="{{ route('locale.switch') }}" method="POST">
                    @csrf
                    <select name="locale" onchange="this.form.submit()" class="text-xs text-slate-400 hover:text-slate-600 bg-transparent outline-none cursor-pointer">
                        <option value="en" @selected(session('locale', 'en') === 'en')>English (United States)</option>
                        <option value="id" @selected(session('locale', 'en') === 'id')>Bahasa Indonesia</option>
                    </select>
                </form>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="py-5 text-center text-xs text-slate-400">
        © {{ now()->year }} Google Developer Group. All rights reserved
    </footer>

    <!-- Create Account Modal -->
    <div id="register-modal" class="portal-modal fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/45 backdrop-blur-xs p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6 sm:p-8 animate-modal-pop">
            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-900">Create GDGoC Account</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Automatically registers your member profile</p>
                </div>
                <button type="button" data-modal-close class="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-500">✕</button>
            </div>
            <form action="{{ route('register.submit') }}" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Full Name</label>
                    <input type="text" name="name" required placeholder="e.g. Budi Santoso" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#163CA8] text-sm outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Email</label>
                    <input type="email" name="email" required placeholder="name@gdgocunsri.org" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#163CA8] text-sm outline-none">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Division</label>
                        <select name="division_id" required class="w-full px-3 py-2.5 rounded-xl border border-slate-200 focus:border-[#163CA8] text-sm outline-none bg-white">
                            @foreach ($divisions as $division)
                                <option value="{{ $division->id }}">{{ $division->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Batch Year</label>
                        <input type="number" name="batch" value="{{ now()->year }}" min="2018" max="2030" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#163CA8] text-sm outline-none">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Password</label>
                        <input type="password" name="password" required minlength="6" placeholder="Min 6 chars" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#163CA8] text-sm outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Confirm</label>
                        <input type="password" name="password_confirmation" required minlength="6" placeholder="Repeat" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#163CA8] text-sm outline-none">
                    </div>
                </div>
                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" data-modal-close class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#163CA8] hover:bg-[#123189] text-white text-xs font-bold shadow-md btn-press">Create Account</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Forgot Password Modal -->
    <div id="forgot-password-modal" class="portal-modal fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/45 backdrop-blur-xs p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-sm w-full p-6 animate-modal-pop">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-extrabold text-slate-900">Reset Password</h2>
                <button type="button" data-modal-close class="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-500">✕</button>
            </div>
            <p class="text-xs text-slate-500 mb-4">Enter your registered GDGoC email address to receive reset instructions.</p>
            <form action="{{ route('password.forgot') }}" method="POST" class="space-y-4">
                @csrf
                <input type="email" name="email" required placeholder="your.email@gdgocunsri.org" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-[#163CA8] text-sm outline-none">
                <div class="flex justify-end gap-2">
                    <button type="button" data-modal-close class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600">Cancel</button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-[#163CA8] text-white text-xs font-bold btn-press">Send Reset Link</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Google Sign-In Quick Demo Modal -->
    <div id="google-auth-modal" class="portal-modal fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/45 backdrop-blur-xs p-4">
        <div class="bg-white rounded-3xl shadow-2xl max-w-sm w-full p-6 animate-modal-pop">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg font-extrabold text-slate-900">Google Sign-In</h2>
                <button type="button" data-modal-close class="w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center text-slate-500">✕</button>
            </div>
            <p class="text-xs text-slate-500 leading-relaxed mb-4">
                Sign in with your GDGoC UNSRI Google Workspace account or select a seeded portal account below to preview right away:
            </p>
            <div class="space-y-2 mb-4">
                <button type="button" onclick="fillDemoAndSubmit('admin@gdgocunsri.org', 'password')" class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl border border-slate-200 hover:border-blue-400 hover:bg-blue-50/50 text-left transition-all">
                    <div>
                        <div class="text-xs font-bold text-slate-800">Admin GDGoC UNSRI</div>
                        <div class="text-[11px] text-slate-400">admin@gdgocunsri.org</div>
                    </div>
                    <span class="text-xs font-bold text-[#163CA8]">Continue →</span>
                </button>
                <button type="button" onclick="fillDemoAndSubmit('rafi.aditya@gdgocunsri.org', 'password')" class="w-full flex items-center justify-between px-4 py-2.5 rounded-xl border border-slate-200 hover:border-emerald-400 hover:bg-emerald-50/50 text-left transition-all">
                    <div>
                        <div class="text-xs font-bold text-slate-800">Rafi Aditya Pratama</div>
                        <div class="text-[11px] text-slate-400">rafi.aditya@gdgocunsri.org</div>
                    </div>
                    <span class="text-xs font-bold text-emerald-700">Continue →</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Toast Stack -->
    <div id="toast-stack" class="fixed bottom-5 right-5 z-[70] flex flex-col gap-2 pointer-events-none">
        @if (session('toast'))
            <div data-auto-dismiss class="animate-toast-in pointer-events-auto px-4 py-3 rounded-xl shadow-lg text-sm font-medium bg-[#163CA8] text-white">
                {{ session('toast')['message'] ?? '' }}
            </div>
        @endif
    </div>

    <script>
        function togglePasswordVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            if (!input) {
                return;
            }
            input.type = input.type === 'password' ? 'text' : 'password';
            btn.classList.toggle('text-[#163CA8]');
        }

        function fillDemoAccount(email, password, label) {
            document.getElementById('login-email').value = email;
            document.getElementById('login-password').value = password;
            document.getElementById('role-label').textContent = label;
            document.getElementById('role-quick-dropdown').classList.add('hidden');
        }

        function fillDemoAndSubmit(email, password) {
            const emailInput = document.getElementById('login-email');
            const passInput = document.getElementById('login-password');
            emailInput.value = email;
            passInput.value = password;
            emailInput.closest('form').submit();
        }
    </script>
</body>
</html>
