<?php

namespace App\Providers;

use App\Models\PortalNotification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        View::composer('layouts.portal', function ($view): void {
            if (! Schema::hasTable('portal_notifications')) {
                $view->with('portalNotifications', collect())
                    ->with('unreadNotificationCount', 0);

                return;
            }

            $notifications = PortalNotification::latest()->take(8)->get();
            $unreadCount = PortalNotification::where('is_read', false)->count();

            $view->with('portalNotifications', $notifications)
                ->with('unreadNotificationCount', $unreadCount);
        });
    }
}