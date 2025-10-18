<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4" style="min-height: 60vh;">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8">
            <div class="card shadow mb-4" style="background: white; border-radius: 8px;">
        <div class="card-body" style="padding: 2rem;">
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="mb-1"><i class="fas fa-bell me-2"></i>Notifications</h4>
                    <p class="text-muted mb-0">Stay updated with your event status</p>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="markAllAsRead()">
                        <i class="fas fa-check-double me-1"></i>Mark All Read
                    </button>
                </div>
            </div>

            <div class="notifications-container">
                <?php if($notifications->count() > 0): ?>
                    <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $data = $notification->data;
                            
                            // Handle different notification data formats
                            if (isset($data['title'])) {
                                // New format with rich data
                                $isClickable = !empty($data['action_url']);
                                $icon = $data['icon'] ?? 'fas fa-bell';
                                $color = $data['color'] ?? 'primary';
                                $title = $data['title'];
                                $message = $data['message'] ?? 'No message';
                            } else {
                                // Legacy format - create rich display from basic data
                                $isClickable = false;
                                $icon = 'fas fa-bell';
                                $color = 'primary';
                                $title = 'Order Update';
                                $message = $data['message'] ?? 'No message';
                                
                                // Add order-specific styling
                                if (isset($data['order_id'])) {
                                    $title = 'Order #' . $data['order_id'];
                                    $isClickable = true;
                                    $icon = 'fas fa-shopping-bag';
                                    $color = 'success';
                                }
                            }
                        ?>
                        
                        <div class="notification-item p-3 border-bottom <?php echo e($notification->read_at ? '' : 'bg-light'); ?>" 
                             data-notification-id="<?php echo e($notification->id); ?>"
                             data-clickable="<?php echo e($isClickable ? 'true' : 'false'); ?>"
                             data-action-url="<?php echo e($isClickable ? $data['action_url'] : ''); ?>"
                             style="cursor: pointer; transition: all 0.2s ease; <?php echo e($isClickable ? 'border-left: 4px solid #007bff;' : ''); ?>; border-radius: 8px; margin-bottom: 8px;"
                             onclick="handleNotificationClick('<?php echo e($notification->id); ?>', '<?php echo e($isClickable ? $data['action_url'] : ''); ?>')"
                             onmouseover="this.style.backgroundColor='#e3f2fd'; this.style.transform='translateX(5px)';"
                             onmouseout="this.style.backgroundColor='<?php echo e($notification->read_at ? '' : '#f8f9fa'); ?>'; this.style.transform='translateX(0px)';">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <i class="<?php echo e($icon); ?> text-<?php echo e($color); ?>"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 <?php echo e($notification->read_at ? 'text-muted' : 'fw-bold'); ?>" style="font-size: 1.1rem; color: #2c3e50;">
                                        <?php echo e($title); ?>

                                    </h6>
                                    <p class="mb-1" style="color: #555; font-size: 0.95rem; line-height: 1.4;"><?php echo e($message); ?></p>
                                    <small class="text-muted" style="font-size: 0.8rem;"><?php echo e($notification->created_at->diffForHumans()); ?></small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <?php if(!$notification->read_at): ?>
                                        <div class="badge bg-<?php echo e($color); ?> rounded-pill me-2" style="font-size: 0.7rem;">New</div>
                                    <?php endif; ?>
                                    <?php if($isClickable): ?>
                                        <div class="d-flex align-items-center text-primary" style="font-size: 0.8rem;">
                                            <i class="fas fa-external-link-alt me-1"></i>
                                            <span>Click to view</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <div class="text-center py-5" style="background: #f8f9fa; border-radius: 8px; margin: 2rem 0;">
                        <i class="fas fa-bell-slash text-muted" style="font-size: 3rem;"></i>
                        <h5 class="text-muted mt-3">No notifications yet</h5>
                        <p class="text-muted">You'll receive notifications when your order status changes</p>
                    </div>
                <?php endif; ?>
            </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
function markAllAsRead() {
    // Show loading state
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Marking as read...';
    button.disabled = true;

    // Make AJAX request
    fetch('<?php echo e(route("customer.notifications.markAllAsRead")); ?>', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload page to show updated notifications
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to mark notifications as read'));
            button.innerHTML = originalText;
            button.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error marking notifications as read');
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

function handleNotificationClick(notificationId, actionUrl) {
    // Add visual feedback
    const notificationItem = document.querySelector(`[data-notification-id="${notificationId}"]`);
    if (notificationItem) {
        notificationItem.style.backgroundColor = '#d4edda';
        notificationItem.style.borderLeft = '4px solid #28a745';
    }
    
    // Mark notification as read first
    markNotificationAsRead(notificationId);
    
    // Then navigate if there's an action URL
    if (actionUrl) {
        // Show loading indicator
        if (notificationItem) {
            const clickIndicator = notificationItem.querySelector('.text-primary');
            if (clickIndicator) {
                clickIndicator.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Opening...';
            }
        }
        
        setTimeout(() => {
            window.location.href = actionUrl;
        }, 300);
    } else {
        // If no action URL, just mark as read
        setTimeout(() => {
            if (notificationItem) {
                notificationItem.style.backgroundColor = '#f8f9fa';
            }
        }, 1000);
    }
}

function markNotificationAsRead(notificationId) {
    fetch(`<?php echo e(url('customer/notifications')); ?>/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update visual state
            const notificationItem = document.querySelector(`[data-notification-id="${notificationId}"]`);
            if (notificationItem) {
                notificationItem.classList.remove('bg-light');
                notificationItem.classList.add('text-muted');
                
                // Remove "New" badge
                const badge = notificationItem.querySelector('.badge');
                if (badge) {
                    badge.remove();
                }
            }
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
    });
}

// Add hover effects
document.addEventListener('DOMContentLoaded', function() {
    const notificationItems = document.querySelectorAll('.notification-item');
    notificationItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8f9fa';
        });
        
        item.addEventListener('mouseleave', function() {
            if (!this.classList.contains('bg-light')) {
                this.style.backgroundColor = '';
            }
        });
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.customer_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/customer/notifications/index.blade.php ENDPATH**/ ?>