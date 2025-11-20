<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        // Get only notifications for the current admin user
        $query = auth()->user()->notifications();
        
        // Exclude customer-specific notifications (they have different processes/flows)
        // Customer-only notifications to exclude:
        // - order_placed, order_approved, payment_validated, order_delivered, order_completed
        // - order_status, return_approved, refund_processed, loyalty_stamp_reach
        // - driver_assigned_order: Only for drivers
        
        // Filter by notification class type (exclude customer notification classes)
        $customerNotificationClasses = [
            'App\\Notifications\\OrderPlacedNotification',
            'App\\Notifications\\OrderApprovedNotification',
            'App\\Notifications\\OrderPaymentValidatedNotification',
            'App\\Notifications\\OrderDeliveredNotification',
            'App\\Notifications\\OrderCompletedNotification',
            'App\\Notifications\\OrderStatusNotification',
            'App\\Notifications\\ReturnApprovedNotification',
            'App\\Notifications\\DriverAssignedOrderNotification', // Driver-only
        ];
        
        foreach ($customerNotificationClasses as $class) {
            $query->where('type', '!=', $class);
        }
        
        // Also filter by data->type to catch any notifications that might have customer types
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
            $query->whereJsonDoesntContain('data->type', $type);
        }
        
        // Simple search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->get('search');
            $query->where(function($q) use ($searchTerm) {
                // Search in notification data (message, type, title)
                $q->where('data->message', 'like', "%{$searchTerm}%")
                  ->orWhere('data->type', 'like', "%{$searchTerm}%")
                  ->orWhere('data->title', 'like', "%{$searchTerm}%")
                  // Search by date
                  ->orWhereDate('created_at', 'like', "%{$searchTerm}%");
            });
        }
        
        // Fetch notifications with pagination
        $notifications = $query->latest()->paginate(15);
        
        return view('admin.notifications.index', compact('notifications'));
    }

    public function markAsRead(Request $request, $notification)
    {
        // Find the notification for the current user
        $notification = auth()->user()->notifications()->findOrFail($notification);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }
} 