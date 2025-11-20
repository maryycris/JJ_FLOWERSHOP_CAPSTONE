<?php

namespace App\Providers;

use App\Models\CartItem;
use App\Models\CustomizeItem;
use App\Models\Delivery;
use App\Models\Message;
use App\Models\Order;
use App\Models\PendingInventoryAddition;
use App\Models\PendingInventoryChange;
use App\Models\Product;
use Illuminate\Support\Facades\Schema;
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
        // Force HTTPS in production or if FORCE_HTTPS is set (Railway provides HTTPS)
        if (config('app.env') === 'production' || 
            config('app.env') === 'staging' || 
            env('FORCE_HTTPS', false)) {
            \URL::forceScheme('https');
        }

        View::composer('layouts.admin_app', function ($view) {
            $counts = [
                'pending_products' => 0,
                'pending_sales_orders' => 0,
                'pending_customize_items' => 0,
                'unread_notifications' => 0,
                'unread_messages' => 0,
            ];

            if (auth()->check() && auth()->user()->role === 'admin') {
                if (Schema::hasTable('products')) {
                    $counts['pending_products'] = Product::where(function ($query) {
                        $query->where('is_approved', false)
                              ->orWhereNull('is_approved');
                    })->count();
                }

                if (Schema::hasTable('orders')) {
                    $counts['pending_sales_orders'] = Order::where('type', 'online')
                        ->where(function ($query) {
                            $query->whereIn('order_status', ['pending', 'quotation'])
                                  ->orWhere(function ($inner) {
                                      $inner->whereNull('order_status')
                                            ->whereIn('status', ['pending', 'quotation']);
                                  });
                        })
                        ->count();
                }

                if (Schema::hasTable('customize_items')) {
                    $counts['pending_customize_items'] = CustomizeItem::where(function ($query) {
                        $query->where('status', false)
                              ->orWhere('is_approved', false)
                              ->orWhereNull('is_approved');
                    })->count();
                }

                if (Schema::hasTable('notifications')) {
                    $notificationQuery = auth()->user()->unreadNotifications();

                    $customerNotificationClasses = [
                        'App\\Notifications\\OrderPlacedNotification',
                        'App\\Notifications\\OrderApprovedNotification',
                        'App\\Notifications\\OrderPaymentValidatedNotification',
                        'App\\Notifications\\OrderDeliveredNotification',
                        'App\\Notifications\\OrderCompletedNotification',
                        'App\\Notifications\\OrderStatusNotification',
                        'App\\Notifications\\ReturnApprovedNotification',
                        'App\\Notifications\\DriverAssignedOrderNotification',
                    ];

                    foreach ($customerNotificationClasses as $class) {
                        $notificationQuery->where('type', '!=', $class);
                    }

                    $customerNotificationTypes = [
                        'order_placed',
                        'order_approved',
                        'payment_validated',
                        'order_delivered',
                        'order_completed',
                        'order_status',
                        'return_approved',
                        'refund_processed',
                        'driver_assigned_order',
                    ];

                    foreach ($customerNotificationTypes as $type) {
                        $notificationQuery->whereJsonDoesntContain('data->type', $type);
                    }

                    $counts['unread_notifications'] = $notificationQuery->count();
                }

                if (Schema::hasTable('messages')) {
                    $counts['unread_messages'] = Message::where('receiver_id', auth()->id())
                        ->where('is_read', false)
                        ->count();
                }
            }

            $view->with('adminNavCounts', $counts);
        });

        View::composer('layouts.customer_app', function ($view) {
            $counts = [
                'unread_notifications' => 0,
                'unread_messages' => 0,
                'cart_items' => 0,
            ];

            if (auth()->check() && auth()->user()->role === 'customer') {
                if (Schema::hasTable('notifications')) {
                    $counts['unread_notifications'] = auth()->user()->unreadNotifications()->count();
                }

                if (Schema::hasTable('messages')) {
                    $counts['unread_messages'] = Message::where('receiver_id', auth()->id())
                        ->where('is_read', false)
                        ->count();
                }

                if (Schema::hasTable('cart_items')) {
                    $counts['cart_items'] = CartItem::where('user_id', auth()->id())->count();
                }
            }

            $view->with('customerNavCounts', $counts);
        });

        View::composer('layouts.clerk_app', function ($view) {
            $counts = [
                'unread_notifications' => 0,
                'unread_messages' => 0,
                'pending_orders' => 0,
                'pending_inventory_requests' => 0,
            ];

            if (auth()->check() && auth()->user()->role === 'clerk') {
                if (Schema::hasTable('notifications')) {
                    $counts['unread_notifications'] = auth()->user()->unreadNotifications()->count();
                }

                if (Schema::hasTable('messages')) {
                    $counts['unread_messages'] = Message::where('receiver_id', auth()->id())
                        ->where('is_read', false)
                        ->count();
                }

                if (Schema::hasTable('orders')) {
                    $counts['pending_orders'] = Order::where(function ($query) {
                        $query->whereIn('order_status', ['pending', 'quotation'])
                              ->orWhere(function ($inner) {
                                  $inner->whereNull('order_status')
                                        ->whereIn('status', ['pending', 'quotation']);
                              });
                    })->count();
                }

                $pendingInventory = 0;
                if (Schema::hasTable('pending_inventory_changes')) {
                    $pendingInventory += PendingInventoryChange::where('status', 'pending')->count();
                }
                if (Schema::hasTable('pending_inventory_additions')) {
                    $pendingInventory += PendingInventoryAddition::where('status', 'pending')->count();
                }
                $counts['pending_inventory_requests'] = $pendingInventory;
            }

            $view->with('clerkNavCounts', $counts);
        });

        View::composer(['layouts.driver_app', 'layouts.driver_mobile'], function ($view) {
            $counts = [
                'unread_notifications' => 0,
                'unread_messages' => 0,
                'active_deliveries' => 0,
            ];

            if (auth()->check() && auth()->user()->role === 'driver') {
                if (Schema::hasTable('notifications')) {
                    $counts['unread_notifications'] = auth()->user()->unreadNotifications()->count();
                }

                if (Schema::hasTable('messages')) {
                    $counts['unread_messages'] = Message::where('receiver_id', auth()->id())
                        ->where('is_read', false)
                        ->count();
                }

                if (Schema::hasTable('deliveries')) {
                    $counts['active_deliveries'] = Delivery::where('driver_id', auth()->id())
                        ->whereNotIn('status', ['delivered', 'completed', 'cancelled', 'returned'])
                        ->count();
                }
            }

            $view->with('driverNavCounts', $counts);
        });
    }
} 