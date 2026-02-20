<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Models\Notification;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.flux');
        View::composer('admin.layout', function ($view) {
            $view->with('unreadCount', Notification::where('is_admin', true)
                ->where('is_viewed', false)
                ->count());
        });
        View::composer('client.layout', function ($view) {
            if (auth()->check()) {
                $notifications = Notification::where('is_admin', false)
                    ->where('is_viewed', false)
                    ->whereHas('tripTicket', function ($q) {
                        $q->where('client_id', auth()->id());
                    })
                    ->latest()
                    ->get();

                $view->with([
                    'clientNotifications' => $notifications,
                    'unreadClientCount' => $notifications->count()
                ]);
            }
        });
    }
}
