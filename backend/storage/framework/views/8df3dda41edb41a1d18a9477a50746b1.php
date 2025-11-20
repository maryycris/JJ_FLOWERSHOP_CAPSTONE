

<?php $__env->startSection('admin_content'); ?>
<div class="container-fluid pt-4">
    <h1 class="h3 mb-4 text-gray-800">Notifications</h1>

    <!-- Simple Search Bar (button inside the input, no surrounding box) -->
    <form method="GET" action="<?php echo e(route('admin.notifications.index')); ?>" class="mb-3">
        <div class="position-relative">
            <input type="text" class="form-control search-input-with-button" id="search" name="search"
                   value="<?php echo e(request('search')); ?>"
                   placeholder="Search by date, user name, or notification type (e.g., Product_added)...">
            <button type="submit" class="btn btn-primary position-absolute top-0 end-0 h-100 px-4 rounded-start-0">
                <i class="bi bi-search"></i>
            </button>
        </div>
    </form>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="notifications-container">
                <div class="list-group list-group-flush">
                <?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $data = $notification->data;
                    $isClickable = !empty($data['action_url']);
                    $icon = $data['icon'] ?? 'fas fa-bell';
                    $color = $data['color'] ?? 'primary';
                    $title = $data['title'] ?? ucfirst($data['type'] ?? 'Notification');
                    $message = $data['message'] ?? 'No message';
                    $targetUrl = $data['action_url'] ?? 'javascript:void(0)';
                    
                    // Override URL for specific notification types
                    if (isset($data['type'])) {
                        if ($data['type'] === 'order_completed' && isset($data['order_id'])) {
                            // Order completed -> redirect to order details
                            $targetUrl = route('admin.orders.show', $data['order_id']);
                            $isClickable = true;
                        } elseif ($data['type'] === 'product_change_request') {
                            // Product change request -> redirect to product catalog
                            $targetUrl = route('admin.products.index');
                            $isClickable = true;
                        } elseif ($data['type'] === 'product_approval') {
                            // Product approval -> redirect to product catalog
                            $targetUrl = route('admin.products.index');
                            $isClickable = true;
                        } elseif ($data['type'] === 'inventory_change') {
                            // Inventory changes request -> redirect to inventory changes request tab
                            $targetUrl = route('admin.inventory.index') . '#inventory-logs';
                            $isClickable = true;
                        }
                    }
                ?>
                <a href="<?php echo e($targetUrl); ?>" class="list-group-item d-flex justify-content-between align-items-start text-decoration-none notification-item <?php echo e($notification->read() ? 'bg-light text-muted' : ''); ?>" 
                   data-notification-id="<?php echo e($notification->id); ?>"
                   data-is-clickable="<?php echo e($isClickable ? 'true' : 'false'); ?>">
                    <div class="ms-2 me-auto">
                        <div class="d-flex align-items-center mb-1">
                            <i class="<?php echo e($icon); ?> text-<?php echo e($color); ?> me-2"></i>
                            <div class="fw-bold"><?php echo e($title); ?></div>
                        </div>
                        <span class="text-reset"><?php echo e($message); ?></span>
                        <div class="text-muted small">Date: <?php echo e($notification->created_at->format('Y-m-d H:i')); ?></div>
                    </div>
                    <div class="d-flex align-items-center">
                        <?php if(!$notification->read()): ?>
                            <div class="badge bg-<?php echo e($color); ?> rounded-pill me-2">New</div>
                        <?php endif; ?>
                        <?php if($isClickable): ?>
                            <i class="fas fa-external-link-alt text-muted me-2"></i>
                        <?php endif; ?>
                        <div class="form-check">
                            <input class="form-check-input mark-as-read-checkbox" type="checkbox" data-notification-id="<?php echo e($notification->id); ?>" <?php echo e($notification->read() ? 'checked disabled' : ''); ?>>
                            <label class="form-check-label" for="notificationCheck<?php echo e($notification->id); ?>"></label>
                        </div>
                    </div>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="list-group-item">
                    <p class="text-center mb-0">No new notifications.</p>
                </div>
                <?php endif; ?>
                </div>
            </div>
            
            
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Handle checkbox clicks
        document.querySelectorAll('.mark-as-read-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function(e) {
                e.stopPropagation(); // Prevent triggering the link click
                const notificationId = this.dataset.notificationId;
                const isChecked = this.checked;

                if (isChecked) {
                    fetch(`/admin/notifications/${notificationId}/mark-as-read`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({})
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            this.closest('.list-group-item').classList.add('bg-light', 'text-muted');
                            this.disabled = true;
                        } else {
                            alert('Failed to mark notification as read.');
                            this.checked = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while marking notification as read.');
                        this.checked = false;
                    });
                }
            });
        });

        // Handle notification link clicks using event delegation
        document.querySelectorAll('.notification-item').forEach(notificationLink => {
            notificationLink.addEventListener('click', function(e) {
                // Don't interfere with checkbox clicks
                if (e.target.type === 'checkbox' || e.target.closest('.form-check')) {
                    return;
                }

                const isClickable = this.dataset.isClickable === 'true';
                if (!isClickable) {
                    return;
                }

                e.preventDefault();
                e.stopPropagation();

                const notificationId = this.dataset.notificationId;
                const targetUrl = this.getAttribute('href');

                console.log('Notification clicked:', notificationId, 'Redirecting to:', targetUrl);

                // Immediately update the UI
                this.classList.add('bg-light', 'text-muted');

                // Remove "New" badge
                const newBadge = this.querySelector('.badge');
                if (newBadge) {
                    newBadge.remove();
                }

                // Check the checkbox
                const checkbox = this.querySelector('.mark-as-read-checkbox');
                if (checkbox && !checkbox.checked) {
                    checkbox.checked = true;
                    checkbox.disabled = true;
                }

                // Mark as read in background
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (csrfToken) {
                    fetch(`/admin/notifications/${notificationId}/mark-as-read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Notification marked as read:', notificationId);
                    })
                    .catch(error => {
                        console.error('Error marking notification as read:', error);
                    });
                }

                // Redirect after short delay
                setTimeout(() => {
                    window.location.href = targetUrl;
                }, 100);
            });
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .notifications-container {
        max-height: 500px;
        overflow-y: auto;
        border: 1px solid #e3e6f0;
        border-radius: 0.5rem;
        background: #fff;
    }

    /* Search input with button inside */
    .search-input-with-button {
        padding-right: 3.25rem; /* space for the button */
        box-shadow: none;
    }
    
    /* Custom scrollbar styling */
    .notifications-container::-webkit-scrollbar {
        width: 8px;
    }
    
    .notifications-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    
    .notifications-container::-webkit-scrollbar-thumb {
        background: #5E8458;
        border-radius: 4px;
    }
    
    .notifications-container::-webkit-scrollbar-thumb:hover {
        background: #4a6b45;
    }
    
    .list-group-item {
        border-color: #e3e6f0;
        padding: 1rem 1.25rem;
        margin-bottom: 0.5rem;
        border-radius: 0.5rem;
        transition: background-color 0.2s ease;
        border-left: none;
        border-right: none;
    }
    
    .list-group-item:first-child {
        border-top: none;
    }
    
    .list-group-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }
    
    .list-group-item:hover {
        background-color: #f8f9fc;
    }
    
    .list-group-item .fw-bold {
        color: #385E42; /* Dark green for headings */
    }
    
    .search-form .form-label {
        font-weight: 600;
        color: #385E42;
    }
</style>
<?php $__env->stopPush(); ?> 
<?php echo $__env->make('layouts.admin_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\frontend\resources\views/admin/notifications/index.blade.php ENDPATH**/ ?>