<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\Attendance;
use App\Models\Division;
use App\Models\Member;
use App\Models\PortalNotification;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PortalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_login_page_renders_and_authenticates_admin(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Google Developer Groups')
            ->assertSee('Log In');

        $response = $this->post('/login', [
            'email' => 'admin@gdgocunsri.org',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();
    }

    public function test_user_can_register_and_creates_linked_member(): void
    {
        $division = Division::firstOrFail();

        $response = $this->post('/register', [
            'name' => 'Test Developer',
            'email' => 'dev.new@gdgocunsri.org',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'division_id' => $division->id,
            'batch' => 2025,
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'dev.new@gdgocunsri.org', 'role' => 'member']);
        $this->assertDatabaseHas('members', ['email' => 'dev.new@gdgocunsri.org', 'name' => 'Test Developer']);
    }

    public function test_admin_profile_shows_admin_account_and_supports_upload_edit_delete(): void
    {
        Storage::fake('uploads');
        $admin = User::where('email', 'admin@gdgocunsri.org')->firstOrFail();
        $this->actingAs($admin);

        $this->get('/profile')
            ->assertOk()
            ->assertSeeText('Admin Profile')
            ->assertSeeText('Admin GDGoC UNSRI')
            ->assertSeeText('Portal Administrator')
            ->assertDontSeeText('Rafi Aditya Pratama');

        $avatar = UploadedFile::fake()->image('admin-avatar.png', 200, 200);
        $this->put('/profile', [
            'name' => 'Super Admin GDGoC',
            'email' => 'admin@gdgocunsri.org',
            'avatar' => $avatar,
        ])->assertRedirect();

        $admin->refresh();
        $this->assertSame('Super Admin GDGoC', $admin->name);
        $this->assertNotNull($admin->avatar_url);
        Storage::disk('uploads')->assertExists(substr($admin->avatar_url, strlen('/uploads/')));

        $this->delete('/profile/avatar')->assertRedirect();
        $admin->refresh();
        $this->assertNull($admin->avatar_url);
    }

    public function test_all_portal_pages_render_for_authenticated_admin(): void
    {
        $admin = User::where('email', 'admin@gdgocunsri.org')->firstOrFail();
        $this->actingAs($admin);

        $this->get('/home')->assertOk()->assertSeeText('Google Developer Groups Universitas Sriwijaya');
        $this->get('/dashboard')->assertOk()->assertSeeText('Member Dashboard');
        $this->get('/members')->assertOk()->assertSeeText('Rafi Aditya Pratama')->assertSeeText('+ Add Member');
        $this->get('/members?view=grid')->assertOk()->assertSeeText('GDG001');
        $this->get('/leaderboard')->assertOk()->assertSeeText('Member Rankings');
        $this->get('/leaderboard?period=monthly')->assertOk()->assertSeeText('Monthly');
        $this->get('/leaderboard?period=semester')->assertOk()->assertSeeText('Semester');
        $this->get('/leaderboard?period=annual')->assertOk()->assertSeeText('Annual');
        $this->get('/attendance')->assertOk()->assertSeeText('Member Attendance Overview')->assertSeeText('+ Record Attendance');
        $this->get('/activities')->assertOk()->assertSeeText('GDG DevFest 2025')->assertSeeText('+ Create Activity');
        $this->get('/activities?tab=upcoming')->assertOk()->assertSeeText('GDG Info Session 2026');
    }

    public function test_member_sees_own_profile_own_attendance_and_is_view_only(): void
    {
        $memberUser = User::where('email', 'rafi.aditya@gdgocunsri.org')->firstOrFail();
        $targetMember = Member::where('member_code', 'GDG002')->firstOrFail();
        $activity = Activity::firstOrFail();
        $attendance = Attendance::firstOrFail();
        $division = Division::firstOrFail();

        $this->actingAs($memberUser);

        // Profile points to Rafi Aditya Pratama in view-only mode
        $this->get('/profile')
            ->assertOk()
            ->assertSeeText('Rafi Aditya Pratama')
            ->assertSeeText('View Only')
            ->assertDontSeeText('Edit & Upload');

        // Members & Activities pages hide management buttons
        $this->get('/members')->assertOk()->assertSeeText('View Only Mode')->assertDontSeeText('+ Add Member');
        $this->get('/activities')->assertOk()->assertSeeText('View Only')->assertDontSeeText('+ Create Activity');

        // Attendance shows only Rafi's events, not other members like Bima Sakti Wijaya
        $this->get('/attendance')
            ->assertOk()
            ->assertSeeText('Rafi Aditya Pratama')
            ->assertSeeText('GDG DevFest 2025')
            ->assertSeeText('Study Jam: ML Fundamentals')
            ->assertDontSeeText('Bima Sakti Wijaya')
            ->assertDontSeeText('+ Record Attendance');

        // All mutating routes return 403 Forbidden for members
        $this->post('/members', ['name' => 'Hacker', 'email' => 'x@y.z', 'division_id' => $division->id, 'role_title' => 'Member', 'batch' => 2025, 'status' => 'Active'])->assertForbidden();
        $this->put("/members/{$targetMember->id}", ['name' => 'X'])->assertForbidden();
        $this->delete("/members/{$targetMember->id}")->assertForbidden();
        $this->post("/members/{$targetMember->id}/points", ['title' => 'X', 'category' => 'Contribution', 'points' => 10, 'awarded_at' => '2026-01-01'])->assertForbidden();

        $this->post('/activities', ['title' => 'Fake'])->assertForbidden();
        $this->put("/activities/{$activity->id}", ['title' => 'Fake'])->assertForbidden();
        $this->delete("/activities/{$activity->id}")->assertForbidden();
        $this->post("/activities/{$activity->id}/check-in", ['member_id' => $targetMember->id, 'status' => 'Present'])->assertForbidden();

        $this->post('/attendance', ['member_id' => $targetMember->id, 'activity_id' => $activity->id, 'status' => 'Present', 'recorded_at' => '2026-01-01'])->assertForbidden();
        $this->patch("/attendance/{$attendance->id}", ['status' => 'Absent'])->assertForbidden();
        $this->delete("/attendance/{$attendance->id}")->assertForbidden();

        $this->put('/profile', ['name' => 'Changed', 'email' => $memberUser->email])->assertForbidden();
        $this->delete('/profile/avatar')->assertForbidden();
    }

    public function test_admin_can_add_edit_upload_award_points_and_delete_member(): void
    {
        Storage::fake('uploads');
        $admin = User::where('email', 'admin@gdgocunsri.org')->firstOrFail();
        $division = Division::firstOrFail();
        $this->actingAs($admin);

        $photo = UploadedFile::fake()->image('member.jpg', 300, 300);

        $this->post('/members', [
            'name' => 'Putri Maharani',
            'email' => 'putri.maharani@gdgocunsri.org',
            'division_id' => $division->id,
            'role_title' => 'Core Team',
            'batch' => 2024,
            'points' => 250,
            'attendance_rate' => 96,
            'status' => 'Active',
            'avatar' => $photo,
        ])->assertRedirect(route('members.index'));

        $member = Member::where('email', 'putri.maharani@gdgocunsri.org')->firstOrFail();
        $this->assertSame(250, $member->points);
        $this->assertNotNull($member->avatar_url);
        Storage::disk('uploads')->assertExists(substr($member->avatar_url, strlen('/uploads/')));

        $this->put("/members/{$member->id}", [
            'name' => 'Putri Maharani Updated',
            'email' => 'putri.maharani@gdgocunsri.org',
            'division_id' => $division->id,
            'role_title' => 'Division Lead',
            'batch' => 2024,
            'status' => 'Active',
            'contributions_count' => 3,
            'competitions_count' => 1,
        ])->assertRedirect();

        $this->post("/members/{$member->id}/points", [
            'title' => 'Cloud Workshop Speaker',
            'category' => 'Event Staff',
            'points' => 150,
            'awarded_at' => now()->toDateString(),
        ])->assertRedirect();

        $member->refresh();
        $this->assertSame(400, $member->points);
        $this->assertSame('Putri Maharani Updated', $member->name);

        $this->delete("/members/{$member->id}")
            ->assertRedirect(route('members.index'));
        $this->assertDatabaseMissing('members', ['id' => $member->id]);
    }

    public function test_admin_can_create_activity_with_banner_check_in_and_record_attendance(): void
    {
        Storage::fake('uploads');
        $admin = User::where('email', 'admin@gdgocunsri.org')->firstOrFail();
        $member = Member::where('member_code', 'GDG010')->firstOrFail();
        $initialPoints = $member->points;
        $this->actingAs($admin);

        $banner = UploadedFile::fake()->image('banner.png', 800, 400);

        $this->post('/activities', [
            'title' => 'Gemini AI Bootcamp',
            'category' => 'Workshop',
            'status' => 'Upcoming',
            'event_date' => '2026-10-15',
            'location' => 'UNSRI Hall',
            'max_attendees' => 80,
            'points_reward' => 60,
            'banner' => $banner,
        ])->assertRedirect(route('activities.index'));

        $activity = Activity::where('title', 'Gemini AI Bootcamp')->firstOrFail();
        $this->assertNotNull($activity->banner_url);
        Storage::disk('uploads')->assertExists(substr($activity->banner_url, strlen('/uploads/')));

        $this->post("/activities/{$activity->id}/check-in", [
            'member_id' => $member->id,
            'status' => 'Present',
        ])->assertRedirect();

        $member->refresh();
        $this->assertSame($initialPoints + 60, $member->points);
        $this->assertDatabaseHas('attendances', [
            'member_id' => $member->id,
            'activity_id' => $activity->id,
            'status' => 'Present',
        ]);
    }

    public function test_global_search_and_notifications_work(): void
    {
        $admin = User::where('email', 'admin@gdgocunsri.org')->firstOrFail();
        $this->actingAs($admin);

        $this->getJson('/portal/search?q=Rafi')
            ->assertOk()
            ->assertJsonFragment(['name' => 'Rafi Aditya Pratama']);

        $this->assertGreaterThan(0, PortalNotification::where('is_read', false)->count());

        $this->post('/notifications/read-all')->assertRedirect();
        $this->assertSame(0, PortalNotification::where('is_read', false)->count());
    }
}
