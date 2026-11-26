<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Member;
use App\Models\PortalNotification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    
    public function showLogin(): View
    {
        $divisions = Division::orderBy('name')->get();

        return view('auth.login', [
            'divisions' => $divisions,
        ]);
    }

    
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __('Invalid GDGoC email or password.')]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('home'))
            ->with('toast', ['type' => 'success', 'message' => 'Welcome back to GDGoC UNSRI Portal!']);
    }

   
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email', 'unique:members,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'division_id' => ['required', 'exists:divisions,id'],
            'batch' => ['nullable', 'integer', 'min:2018', 'max:2030'],
        ]);

        $nextNumber = (Member::max('id') ?? 0) + 1;
        $memberCode = 'GDG'.str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
        while (Member::where('member_code', $memberCode)->exists()) {
            $nextNumber++;
            $memberCode = 'GDG'.str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
        }

        $colors = ['#163CA8', '#EA4335', '#34A853', '#F97316', '#6366F1', '#8B5CF6'];
        $member = Member::create([
            'member_code' => $memberCode,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'division_id' => $validated['division_id'],
            'role_title' => 'Member',
            'batch' => (int) ($validated['batch'] ?? now()->year),
            'points' => 50,
            'attendance_rate' => 100,
            'contributions_count' => 0,
            'competitions_count' => 0,
            'status' => 'Active',
            'avatar_color' => $colors[$nextNumber % count($colors)],
            'bio' => 'Joined GDGoC UNSRI Developer Community.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'member',
            'member_id' => $member->id,
            'locale' => session('locale', 'en'),
        ]);

        PortalNotification::create([
            'title' => 'New Member Registered',
            'message' => "{$member->name} ({$member->member_code}) joined GDGoC UNSRI.",
            'type' => 'member',
            'link' => route('members.show', $member),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('home')
            ->with('toast', ['type' => 'success', 'message' => 'Account & member profile created successfully!']);
    }

    public function forgotPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        return back()->with('toast', [
            'type' => 'info',
            'message' => 'If an account matches that email, password reset instructions have been sent.',
        ]);
    }

    
    public function switchLocale(Request $request): RedirectResponse
    {
        $locale = in_array($request->input('locale'), ['en', 'id'], true)
            ? $request->input('locale')
            : 'en';

        session(['locale' => $locale]);
        if (Auth::check()) {
            Auth::user()->update(['locale' => $locale]);
        }

        return back();
    }

   
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('toast', ['type' => 'info', 'message' => 'You have been logged out.']);
    }
}
