<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\Activity;
use App\Models\Attendance;
use App\Models\Division;
use App\Models\Member;
use App\Models\PointLog;
use App\Models\PortalNotification;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $divisionsData = [
            ['name' => 'Machine Learning', 'type' => 'curriculum', 'color' => '#163CA8', 'description' => 'Artificial intelligence, deep learning, and data science initiatives.'],
            ['name' => 'Mobile Development', 'type' => 'curriculum', 'color' => '#34A853', 'description' => 'Flutter, Kotlin, and native Android cross-platform app building.'],
            ['name' => 'UI/UX Design', 'type' => 'curriculum', 'color' => '#EA4335', 'description' => 'User research, wireframing, prototyping, and design systems.'],
            ['name' => 'Front-End Development', 'type' => 'curriculum', 'color' => '#FBBC04', 'description' => 'Modern web interfaces, accessibility, and interactive frameworks.'],
            ['name' => 'Back-End Development', 'type' => 'curriculum', 'color' => '#163CA8', 'description' => 'Scalable APIs, cloud architecture, and database engineering.'],
            ['name' => 'Cyber Security', 'type' => 'curriculum', 'color' => '#EA4335', 'description' => 'AppSec, network defense, ethical hacking, and CTF challenges.'],
            ['name' => 'Game Development', 'type' => 'curriculum', 'color' => '#34A853', 'description' => 'Game mechanics, Unity/Godot engines, and interactive storytelling.'],
            ['name' => 'Community Development', 'type' => 'non-curriculum', 'color' => '#F97316', 'description' => 'Member engagement, onboarding, and developer community growth.'],
            ['name' => 'Public Relations', 'type' => 'non-curriculum', 'color' => '#EA4335', 'description' => 'External partnerships, campus outreach, and media communications.'],
            ['name' => 'Creative Design', 'type' => 'non-curriculum', 'color' => '#FBBC04', 'description' => 'Visual branding, social media assets, and event merchandise.'],
            ['name' => 'Project Management', 'type' => 'non-curriculum', 'color' => '#163CA8', 'description' => 'Program timelines, cross-division coordination, and delivery.'],
            ['name' => 'Copywriting', 'type' => 'non-curriculum', 'color' => '#34A853', 'description' => 'Editorial content, event announcements, and developer storytelling.'],
        ];

        $divisions = [];
        foreach ($divisionsData as $item) {
            $divisions[$item['name']] = Division::updateOrCreate(
                ['slug' => Str::slug($item['name'])],
                [
                    'name' => $item['name'],
                    'type' => $item['type'],
                    'color' => $item['color'],
                    'description' => $item['description'],
                ]
            );
        }

        $achievementsData = [
            ['name' => 'Active Member', 'slug' => 'active-member', 'icon' => '⚡', 'bg_color' => '#EEF2FF', 'text_color' => '#163CA8', 'min_points' => 300, 'description' => 'Consistently participates in GDGoC UNSRI programs.'],
            ['name' => 'Top Contributor', 'slug' => 'top-contributor', 'icon' => '🏆', 'bg_color' => '#FEF3C7', 'text_color' => '#B45309', 'min_points' => 1000, 'description' => 'Contributed extensively to workshops, mentoring, or OSS.'],
            ['name' => 'Champion', 'slug' => 'champion', 'icon' => '🥇', 'bg_color' => '#FFEDD5', 'text_color' => '#C2410C', 'min_points' => 1200, 'description' => 'Achieved podium placement in major tech competitions.'],
            ['name' => 'Competition Star', 'slug' => 'competition-star', 'icon' => '⭐', 'bg_color' => '#FEE2E2', 'text_color' => '#B91C1C', 'min_points' => 900, 'description' => 'Represented GDGoC UNSRI across multiple delegations.'],
            ['name' => 'Point Leader', 'slug' => 'point-leader', 'icon' => '💯', 'bg_color' => '#FCE7F3', 'text_color' => '#BE185D', 'min_points' => 1400, 'description' => 'Reached elite point standings on the chapter leaderboard.'],
            ['name' => 'Consistent', 'slug' => 'consistent', 'icon' => '📅', 'bg_color' => '#E0E7FF', 'text_color' => '#1E40AF', 'min_points' => 700, 'description' => 'Maintained outstanding attendance across events.'],
            ['name' => 'Tech Explorer', 'slug' => 'tech-explorer', 'icon' => '🔭', 'bg_color' => '#DCFCE7', 'text_color' => '#15803D', 'min_points' => 500, 'description' => 'Completed cross-division workshops and study jams.'],
        ];

        $achievements = [];
        foreach ($achievementsData as $item) {
            $achievements[$item['slug']] = Achievement::updateOrCreate(
                ['slug' => $item['slug']],
                $item
            );
        }

        $membersData = [
            [
                'member_code' => 'GDG001',
                'name' => 'Rafi Aditya Pratama',
                'email' => 'rafi.aditya@gdgocunsri.org',
                'phone' => '+62 812-7100-0001',
                'division' => 'Machine Learning',
                'role_title' => 'Division Lead',
                'batch' => 2022,
                'points' => 1450,
                'attendance_rate' => 95,
                'contributions_count' => 18,
                'competitions_count' => 5,
                'status' => 'Active',
                'avatar_color' => '#163CA8',
                'bio' => 'Machine Learning Division Lead at GDGoC UNSRI passionate about computer vision, TensorFlow, and mentoring campus developers.',
                'badges' => ['active-member', 'top-contributor', 'champion', 'competition-star', 'point-leader', 'consistent', 'tech-explorer'],
            ],
            [
                'member_code' => 'GDG002',
                'name' => 'Siti Nurhaliza Rahman',
                'email' => 'siti.nurhaliza@gdgocunsri.org',
                'phone' => '+62 812-7100-0002',
                'division' => 'UI/UX Design',
                'role_title' => 'Core Team',
                'batch' => 2022,
                'points' => 1320,
                'attendance_rate' => 92,
                'contributions_count' => 15,
                'competitions_count' => 2,
                'status' => 'Active',
                'avatar_color' => '#EA4335',
                'bio' => 'Product designer and design system advocate driving human-centered workshops at GDGoC UNSRI.',
                'badges' => ['active-member', 'top-contributor', 'consistent', 'tech-explorer'],
            ],
            [
                'member_code' => 'GDG003',
                'name' => 'Bima Sakti Wijaya',
                'email' => 'bima.sakti@gdgocunsri.org',
                'phone' => '+62 812-7100-0003',
                'division' => 'Mobile Development',
                'role_title' => 'Member',
                'batch' => 2023,
                'points' => 1180,
                'attendance_rate' => 88,
                'contributions_count' => 11,
                'competitions_count' => 1,
                'status' => 'Active',
                'avatar_color' => '#34A853',
                'bio' => 'Flutter & Kotlin builder focused on cross-platform community apps.',
                'badges' => ['active-member', 'top-contributor', 'consistent', 'tech-explorer'],
            ],
            [
                'member_code' => 'GDG004',
                'name' => 'Annisa Putri Handayani',
                'email' => 'annisa.putri@gdgocunsri.org',
                'phone' => '+62 812-7100-0004',
                'division' => 'Back-End Development',
                'role_title' => 'Core Team',
                'batch' => 2023,
                'points' => 1095,
                'attendance_rate' => 90,
                'contributions_count' => 10,
                'competitions_count' => 1,
                'status' => 'Active',
                'avatar_color' => '#163CA8',
                'bio' => 'Backend engineer specializing in Laravel, Go microservices, and Google Cloud.',
                'badges' => ['active-member', 'top-contributor', 'consistent'],
            ],
            [
                'member_code' => 'GDG005',
                'name' => 'Dimas Fauzan Hakim',
                'email' => 'dimas.fauzan@gdgocunsri.org',
                'phone' => '+62 812-7100-0005',
                'division' => 'Cyber Security',
                'role_title' => 'Division Lead',
                'batch' => 2022,
                'points' => 980,
                'attendance_rate' => 85,
                'contributions_count' => 8,
                'competitions_count' => 3,
                'status' => 'Active',
                'avatar_color' => '#F97316',
                'bio' => 'CTF competitor and security researcher leading Cyber Security study jams.',
                'badges' => ['active-member', 'competition-star', 'consistent'],
            ],
            [
                'member_code' => 'GDG006',
                'name' => 'Larasati Dewi Kusuma',
                'email' => 'larasati.dewi@gdgocunsri.org',
                'phone' => '+62 812-7100-0006',
                'division' => 'Community Development',
                'role_title' => 'Secretary',
                'batch' => 2023,
                'points' => 870,
                'attendance_rate' => 82,
                'contributions_count' => 9,
                'competitions_count' => 0,
                'status' => 'Active',
                'avatar_color' => '#10B981',
                'bio' => 'Organizes onboarding programs, member retention initiatives, and chapter operations.',
                'badges' => ['active-member', 'consistent', 'tech-explorer'],
            ],
            [
                'member_code' => 'GDG007',
                'name' => 'Rizky Firmansyah',
                'email' => 'rizky.firmansyah@gdgocunsri.org',
                'phone' => '+62 812-7100-0007',
                'division' => 'Front-End Development',
                'role_title' => 'Member',
                'batch' => 2024,
                'points' => 720,
                'attendance_rate' => 78,
                'contributions_count' => 6,
                'competitions_count' => 1,
                'status' => 'Active',
                'avatar_color' => '#6366F1',
                'bio' => 'Web developer building responsive interactive UIs with Tailwind and TypeScript.',
                'badges' => ['active-member', 'tech-explorer'],
            ],
            [
                'member_code' => 'GDG008',
                'name' => 'Kartika Sari Indah',
                'email' => 'kartika.sari@gdgocunsri.org',
                'phone' => '+62 812-7100-0008',
                'division' => 'Creative Design',
                'role_title' => 'Member',
                'batch' => 2024,
                'points' => 640,
                'attendance_rate' => 75,
                'contributions_count' => 5,
                'competitions_count' => 0,
                'status' => 'Active',
                'avatar_color' => '#EC4899',
                'bio' => 'Visual designer crafting event posters, social campaigns, and brand assets.',
                'badges' => ['active-member', 'tech-explorer'],
            ],
            [
                'member_code' => 'GDG009',
                'name' => 'Arief Budiman Santoso',
                'email' => 'arief.budiman@gdgocunsri.org',
                'phone' => '+62 812-7100-0009',
                'division' => 'Machine Learning',
                'role_title' => 'Former Lead',
                'batch' => 2021,
                'points' => 2100,
                'attendance_rate' => 93,
                'contributions_count' => 22,
                'competitions_count' => 4,
                'status' => 'Alumni',
                'avatar_color' => '#D97706',
                'bio' => 'Former GDGoC UNSRI Lead and mentor supporting AI engineering tracks.',
                'badges' => ['active-member', 'top-contributor', 'champion', 'competition-star', 'point-leader', 'consistent', 'tech-explorer'],
            ],
            [
                'member_code' => 'GDG010',
                'name' => 'Nadia Fitria Azzahra',
                'email' => 'nadia.fitria@gdgocunsri.org',
                'phone' => '+62 812-7100-0010',
                'division' => 'Mobile Development',
                'role_title' => 'Member',
                'batch' => 2024,
                'points' => 520,
                'attendance_rate' => 70,
                'contributions_count' => 4,
                'competitions_count' => 0,
                'status' => 'Active',
                'avatar_color' => '#0EA5E9',
                'bio' => 'Exploring mobile UI architecture and Firebase integrations.',
                'badges' => ['active-member', 'tech-explorer'],
            ],
            [
                'member_code' => 'GDG011',
                'name' => 'Fadhil Ramadan Putra',
                'email' => 'fadhil.ramadan@gdgocunsri.org',
                'phone' => '+62 812-7100-0011',
                'division' => 'Game Development',
                'role_title' => 'Member',
                'batch' => 2023,
                'points' => 310,
                'attendance_rate' => 45,
                'contributions_count' => 2,
                'competitions_count' => 0,
                'status' => 'Inactive',
                'avatar_color' => '#64748B',
                'bio' => 'Indie game enthusiast experimenting with Godot and shader graphs.',
                'badges' => ['active-member'],
            ],
            [
                'member_code' => 'GDG012',
                'name' => 'Mutiara Anggraeni',
                'email' => 'mutiara.anggraeni@gdgocunsri.org',
                'phone' => '+62 812-7100-0012',
                'division' => 'Public Relations',
                'role_title' => 'Core Team',
                'batch' => 2022,
                'points' => 760,
                'attendance_rate' => 80,
                'contributions_count' => 7,
                'competitions_count' => 1,
                'status' => 'Active',
                'avatar_color' => '#8B5CF6',
                'bio' => 'Coordinates university media partnerships and external tech community collaborations.',
                'badges' => ['active-member', 'consistent', 'tech-explorer'],
            ],
        ];

        $members = [];
        foreach ($membersData as $item) {
            $badges = $item['badges'];
            $divisionName = $item['division'];
            unset($item['badges'], $item['division']);

            $member = Member::updateOrCreate(
                ['member_code' => $item['member_code']],
                array_merge($item, ['division_id' => $divisions[$divisionName]->id])
            );

            $syncIds = [];
            foreach ($badges as $slug) {
                if (isset($achievements[$slug])) {
                    $syncIds[$achievements[$slug]->id] = ['awarded_at' => now()->subDays(rand(5, 90))];
                }
            }
            $member->achievements()->sync($syncIds);
            $members[$member->member_code] = $member;
        }

        $activitiesData = [
            [
                'title' => 'GDG DevFest 2025',
                'slug' => 'gdg-devfest-2025',
                'category' => 'Conference',
                'status' => 'Completed',
                'event_date' => '2025-11-15',
                'location' => 'Auditorium Universitas Sriwijaya',
                'max_attendees' => 50,
                'initial_attendees' => 45,
                'points_reward' => 100,
                'icon' => 'target',
                'accent_color' => '#163CA8',
                'description' => 'Annual flagship developer conference featuring Cloud, AI/ML, and Mobile engineering talks.',
            ],
            [
                'title' => 'Flutter for Beginners Workshop',
                'slug' => 'flutter-for-beginners-workshop',
                'category' => 'Workshop',
                'status' => 'Completed',
                'event_date' => '2025-10-20',
                'location' => 'Lab Komputer Fasilkom UNSRI',
                'max_attendees' => 40,
                'initial_attendees' => 32,
                'points_reward' => 50,
                'icon' => 'tools',
                'accent_color' => '#D97706',
                'description' => 'Hands-on workshop building your first cross-platform Flutter application with state management.',
            ],
            [
                'title' => 'Study Jam: ML Fundamentals',
                'slug' => 'study-jam-ml-fundamentals',
                'category' => 'Study Jam',
                'status' => 'Completed',
                'event_date' => '2025-12-01',
                'location' => 'Hybrid — Ruang Seminar & Google Meet',
                'max_attendees' => 30,
                'initial_attendees' => 28,
                'points_reward' => 75,
                'icon' => 'book',
                'accent_color' => '#16A34A',
                'description' => 'Deep dive into supervised learning, model evaluation, and Vertex AI workflows.',
            ],
            [
                'title' => 'Monthly Meeting — Jan 2026',
                'slug' => 'monthly-meeting-jan-2026',
                'category' => 'Meeting',
                'status' => 'Completed',
                'event_date' => '2026-01-10',
                'location' => 'GDGoC Basecamp & Online',
                'max_attendees' => 45,
                'initial_attendees' => 38,
                'points_reward' => 20,
                'icon' => 'clipboard',
                'accent_color' => '#64748B',
                'description' => 'Monthly sync covering semester roadmap, division OKRs, and event logistics.',
            ],
            [
                'title' => 'Hackathon: Build for Sustainability',
                'slug' => 'hackathon-build-for-sustainability',
                'category' => 'Hackathon',
                'status' => 'Completed',
                'event_date' => '2026-02-14',
                'location' => 'Student Center UNSRI Indralaya',
                'max_attendees' => 25,
                'initial_attendees' => 22,
                'points_reward' => 150,
                'icon' => 'laptop',
                'accent_color' => '#DC2626',
                'description' => '24-hour campus hackathon prototyping green-tech solutions powered by Gemini & Firebase.',
            ],
            [
                'title' => 'UX Research Workshop',
                'slug' => 'ux-research-workshop',
                'category' => 'Workshop',
                'status' => 'Completed',
                'event_date' => '2026-03-05',
                'location' => 'Fasilkom Creative Studio',
                'max_attendees' => 20,
                'initial_attendees' => 18,
                'points_reward' => 50,
                'icon' => 'microscope',
                'accent_color' => '#D97706',
                'description' => 'Practical usability testing, user interviews, and insight synthesis session.',
            ],
            [
                'title' => 'GDG Info Session 2026',
                'slug' => 'gdg-info-session-2026',
                'category' => 'Recruitment',
                'status' => 'Upcoming',
                'event_date' => '2026-09-20',
                'location' => 'Auditorium Utama UNSRI',
                'max_attendees' => 200,
                'initial_attendees' => 0,
                'points_reward' => 30,
                'icon' => 'megaphone',
                'accent_color' => '#163CA8',
                'description' => 'Open recruitment & chapter onboarding info session for incoming 2026 GDGoC UNSRI members.',
            ],
        ];

        $activities = [];
        foreach ($activitiesData as $item) {
            $activities[$item['slug']] = Activity::updateOrCreate(
                ['slug' => $item['slug']],
                $item
            );
        }

        $attendancesData = [
            ['GDG001', 'gdg-devfest-2025', 'Present', '2025-11-15'],
            ['GDG002', 'gdg-devfest-2025', 'Present', '2025-11-15'],
            ['GDG003', 'gdg-devfest-2025', 'Late', '2025-11-15'],
            ['GDG004', 'gdg-devfest-2025', 'Present', '2025-11-15'],
            ['GDG005', 'gdg-devfest-2025', 'Excused', '2025-11-15'],
            ['GDG006', 'gdg-devfest-2025', 'Present', '2025-11-15'],
            ['GDG007', 'gdg-devfest-2025', 'Absent', '2025-11-15'],
            ['GDG001', 'study-jam-ml-fundamentals', 'Present', '2025-12-01'],
            ['GDG002', 'study-jam-ml-fundamentals', 'Present', '2025-12-01'],
            ['GDG003', 'flutter-for-beginners-workshop', 'Present', '2025-10-20'],
        ];

        foreach ($attendancesData as [$code, $slug, $status, $date]) {
            Attendance::updateOrCreate(
                [
                    'member_id' => $members[$code]->id,
                    'activity_id' => $activities[$slug]->id,
                ],
                [
                    'status' => $status,
                    'recorded_at' => $date,
                ]
            );
        }

        if (PointLog::count() === 0) {
            $pointLogsData = [
                // Rafi Aditya Pratama recent point activity (matches My Profile screenshot)
                ['GDG001', 'hackathon-build-for-sustainability', 'Hackathon 2026 — 1st Place', 'Competition Win', 200, '2026-02-14'],
                ['GDG001', 'gdg-devfest-2025', 'GDG DevFest 2025 — Speaker', 'Event Staff', 150, '2025-11-15'],
                ['GDG001', null, 'ML Workshop Series (4 sessions)', 'Mentoring', 160, '2025-09-01'],
                ['GDG001', 'study-jam-ml-fundamentals', 'Study Jam: ML Fundamentals', 'Study Jam', 75, '2025-12-01'],

                // Monthly growth distribution across 2025 & 2026 for all members
                ['GDG009', null, 'Chapter Mentorship & AI Summit', 'Mentoring', 320, '2025-01-20'],
                ['GDG002', null, 'Design System Kickoff', 'Contribution', 420, '2025-02-18'],
                ['GDG003', null, 'Mobile Study Sprint', 'Study Jam', 340, '2025-03-14'],
                ['GDG004', null, 'API Architecture Workshop', 'Event Staff', 510, '2025-04-22'],
                ['GDG005', null, 'Campus CTF Qualifier', 'Competition Win', 410, '2025-05-19'],
                ['GDG001', null, 'Google Cloud Study Jam', 'Study Jam', 560, '2025-06-15'],
                ['GDG006', null, 'Community Gathering Mid-Year', 'Event Staff', 460, '2025-07-11'],
                ['GDG009', null, 'National Solution Challenge Finalist', 'Competition Win', 640, '2025-08-25'],
                ['GDG007', null, 'Front-End Bootcamp Cohort', 'Study Jam', 290, '2025-09-12'],

                // 2026 point logs (including current year & month)
                ['GDG002', 'monthly-meeting-jan-2026', 'Monthly Meeting — Jan 2026', 'Meeting', 220, '2026-01-10'],
                ['GDG003', 'hackathon-build-for-sustainability', 'Hackathon Sustainability Runner-up', 'Competition Win', 380, '2026-02-14'],
                ['GDG002', 'ux-research-workshop', 'UX Research Workshop Lead', 'Event Staff', 440, '2026-03-05'],
                ['GDG004', null, 'Cloud Backend Bootcamp', 'Mentoring', 360, '2026-04-16'],
                ['GDG005', null, 'Security Audit Clinic', 'Contribution', 310, '2026-05-20'],
                ['GDG008', null, 'Creative Design Showcase', 'Contribution', 290, '2026-06-18'],
                ['GDG012', null, 'PR & Partnership Outreach', 'Contribution', 350, '2026-07-14'],
                ['GDG010', null, 'Kotlin Compose Jam', 'Study Jam', 270, '2026-08-19'],
                ['GDG001', null, 'Info Session Preparation Lead', 'Event Staff', 390, '2026-09-10'],
                ['GDG004', null, 'October OSS Sprint', 'Contribution', 420, '2026-10-15'],
                ['GDG001', null, 'November Tech Mentoring', 'Mentoring', 475, '2026-11-12'],
                ['GDG002', null, 'November UI Review Clinic', 'Contribution', 410, '2026-11-18'],
                ['GDG003', null, 'November Flutter Code Lab', 'Study Jam', 320, '2026-11-21'],
            ];

            foreach ($pointLogsData as [$code, $activitySlug, $title, $category, $points, $date]) {
                PointLog::create([
                    'member_id' => $members[$code]->id,
                    'activity_id' => $activitySlug ? $activities[$activitySlug]->id : null,
                    'title' => $title,
                    'category' => $category,
                    'points' => $points,
                    'awarded_at' => $date,
                ]);
            }
        }

        if (PortalNotification::count() === 0) {
            PortalNotification::insert([
                [
                    'title' => 'GDG Info Session 2026 Upcoming',
                    'message' => 'Recruitment event scheduled for Sep 20, 2026 at Auditorium Utama UNSRI.',
                    'type' => 'activity',
                    'is_read' => false,
                    'link' => '/activities',
                    'created_at' => now()->subHours(2),
                    'updated_at' => now()->subHours(2),
                ],
                [
                    'title' => 'Rafi Aditya Close to 1500 pts',
                    'message' => 'Only 50 points remaining to unlock the next achievement milestone.',
                    'type' => 'member',
                    'is_read' => false,
                    'link' => '/profile',
                    'created_at' => now()->subHours(5),
                    'updated_at' => now()->subHours(5),
                ],
                [
                    'title' => 'Attendance Records Updated',
                    'message' => '10 attendance entries synced across DevFest, Study Jam, and Flutter Workshop.',
                    'type' => 'attendance',
                    'is_read' => false,
                    'link' => '/attendance',
                    'created_at' => now()->subDay(),
                    'updated_at' => now()->subDay(),
                ],
                [
                    'title' => 'Leaderboard Standings Refreshed',
                    'message' => 'Arief Budiman, Rafi Aditya, and Siti Nurhaliza hold top 3 podium spots.',
                    'type' => 'system',
                    'is_read' => false,
                    'link' => '/leaderboard',
                    'created_at' => now()->subDays(2),
                    'updated_at' => now()->subDays(2),
                ],
            ]);
        }

        User::updateOrCreate(
            ['email' => 'admin@gdgocunsri.org'],
            [
                'name' => 'Admin GDGoC UNSRI',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'member_id' => null,
                'locale' => 'en',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'rafi.aditya@gdgocunsri.org'],
            [
                'name' => 'Rafi Aditya Pratama',
                'password' => Hash::make('password'),
                'role' => 'member',
                'member_id' => $members['GDG001']->id,
                'locale' => 'en',
                'email_verified_at' => now(),
            ]
        );
    }
}
