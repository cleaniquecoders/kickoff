<?php

namespace Database\Seeders;

use App\Models\User;
use App\Notifications\DefaultNotification;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superadmin = User::role('superadmin')->first();

        if (! $superadmin) {
            $this->command->warn('Superadmin user not found. Skipping notification seeding.');

            return;
        }

        $notifications = $this->getDummyNotifications();

        foreach ($notifications as $notification) {
            $superadmin->notify(new DefaultNotification(
                $notification['subject'],
                $notification['message'],
                $notification['url'] ?? null
            ));
        }

        $this->command->info('Seeded '.count($notifications).' notifications for superadmin.');
    }

    /**
     * Get dummy notifications data.
     */
    private function getDummyNotifications(): array
    {
        return [
            [
                'subject' => 'Welcome to the Application',
                'message' => 'Welcome aboard! Your account has been successfully created. Explore the dashboard to get started.',
                'url' => route('dashboard'),
            ],
            [
                'subject' => 'Security Alert',
                'message' => 'A new login was detected from your account. If this was you, no action is needed.',
                'url' => null,
            ],
            [
                'subject' => 'System Update Available',
                'message' => 'A new system update is available. Please review the changelog for important changes.',
                'url' => null,
            ],
            [
                'subject' => 'Profile Completion Reminder',
                'message' => 'Your profile is 80% complete. Add your profile photo to finish setting up your account.',
                'url' => route('settings.profile.edit'),
            ],
            [
                'subject' => 'New Feature: Notifications',
                'message' => 'We have added a new notifications feature. You can now manage all your notifications from the dashboard.',
                'url' => route('notifications.index'),
            ],
        ];
    }
}
