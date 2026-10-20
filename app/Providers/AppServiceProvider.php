<?php

namespace App\Providers;

use App\Models\Message;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        View::composer('*', fn ($view) => $view->with('site', Setting::site()));

        View::composer('layouts.admin', function ($view) {
            $newOrderCount = Order::where('status', 'new')->count();
            $unreadCount = Message::whereColumn('sender_id', 'customer_id')->whereNull('read_at')->count();
            $lowCount = Product::lowStock()->count();

            $view->with([
                'newOrderCount' => $newOrderCount,
                'unreadCount' => $unreadCount,
                'lowCount' => $lowCount,
                'alerts' => $newOrderCount + $unreadCount + $lowCount,
                'latestOrderId' => (int) Order::max('id'),
            ]);
        });
    }
}
