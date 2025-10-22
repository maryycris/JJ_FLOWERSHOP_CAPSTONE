

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold">Delivery History</h4>
    <div class="d-flex gap-2">
        <span class="badge bg-success"><?php echo e($completedDeliveries->count()); ?> completed</span>
        <span class="badge bg-warning"><?php echo e($returnedOrders->count()); ?> returned</span>
    </div>
</div>

<?php if($completedDeliveries->isEmpty()): ?>
<div class="text-center py-5">
    <i class="bi bi-check-circle display-1 text-muted"></i>
    <h5 class="mt-3 text-muted">No completed deliveries yet</h5>
    <p class="text-muted">Your completed deliveries will appear here.</p>
</div>
<?php else: ?>
<div class="row">
    <?php $__currentLoopData = $completedDeliveries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $delivery): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="col-12 mb-3">
        <div class="card shadow-sm border-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="card-title mb-0">Order #<?php echo e($delivery->order->id); ?></h6>
                    <span class="badge bg-success">
                        <i class="bi bi-check-circle me-1"></i>Completed
                    </span>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6">
                        <small class="text-muted">Customer:</small><br>
                        <strong><?php echo e($delivery->order->user->name); ?></strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Completed:</small><br>
                        <strong><?php echo e($delivery->updated_at->format('M d, Y g:i A')); ?></strong>
                    </div>
                </div>
                
                <div class="mb-2">
                    <small class="text-muted">Delivery Address:</small><br>
                    <strong><?php echo e($delivery->delivery_address ?? 'Address not specified'); ?></strong>
                </div>
                
                <div class="row mb-3">
                    <div class="col-6">
                        <small class="text-muted">Scheduled Date:</small><br>
                        <strong><?php echo e(\Carbon\Carbon::parse($delivery->delivery_date)->format('M d, Y')); ?></strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Scheduled Time:</small><br>
                        <strong><?php echo e($delivery->delivery_time ?? 'Not specified'); ?></strong>
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    <a href="<?php echo e(route('driver.history.show', $delivery->id)); ?>" class="btn btn-outline-primary btn-sm flex-fill">
                        <i class="bi bi-eye me-1"></i>View Details
                    </a>
                    <button class="btn btn-outline-secondary btn-sm" onclick="showDeliveryNotes(<?php echo e($delivery->id); ?>)">
                        <i class="bi bi-chat me-1"></i>Notes
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<!-- Pagination -->
<?php if($completedDeliveries->hasPages()): ?>
<div class="d-flex justify-content-center mt-4">
    <?php echo e($completedDeliveries->links()); ?>

</div>
<?php endif; ?>
<?php endif; ?>

<!-- Returned Orders Section -->
<?php if($returnedOrders->isNotEmpty()): ?>
<div class="mt-5">
    <h5 class="fw-bold text-warning mb-3">
        <i class="bi bi-arrow-return-left me-2"></i>Returned Orders
    </h5>
    <div class="row">
        <?php $__currentLoopData = $returnedOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-12 mb-3">
            <div class="card shadow-sm border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="card-title mb-0">Order #<?php echo e($order->id); ?></h6>
                        <span class="badge" style="background-color: #ffc107; color: #000; font-weight: 600;">
                            <i class="bi bi-arrow-return-left me-1"></i>Returned
                        </span>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-6">
                            <small class="text-muted">Customer:</small><br>
                            <strong><?php echo e($order->user->name); ?></strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Contact:</small><br>
                            <strong><?php echo e($order->user->contact_number ?? 'N/A'); ?></strong>
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <small class="text-muted">Delivery Address:</small><br>
                        <strong><?php echo e($order->delivery->delivery_address ?? 'N/A'); ?></strong>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-6">
                            <small class="text-muted">Total Amount:</small><br>
                            <strong>₱<?php echo e(number_format($order->total_price, 2)); ?></strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Return Date:</small><br>
                            <strong><?php echo e($order->returned_at ? $order->returned_at->format('M d, Y H:i') : 'N/A'); ?></strong>
                        </div>
                    </div>
                    
                    <?php if($order->return_reason): ?>
                    <div class="mb-2">
                        <small class="text-muted">Return Reason:</small><br>
                        <strong class="text-warning"><?php echo e($order->return_reason); ?></strong>
                    </div>
                    <?php endif; ?>
                    
                    <div class="d-flex gap-2">
                        <a href="<?php echo e(route('driver.orders.show', $order->id)); ?>" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-eye me-1"></i>View Details
                        </a>
                        <button class="btn btn-outline-secondary btn-sm" onclick="showReturnNotes(<?php echo e($order->id); ?>)">
                            <i class="bi bi-sticky me-1"></i>Notes
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    
    <!-- Pagination for returned orders -->
    <?php if($returnedOrders->hasPages()): ?>
    <div class="d-flex justify-content-center mt-4">
        <?php echo e($returnedOrders->links()); ?>

    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<script>
function showDeliveryNotes(deliveryId) {
    // This would open a modal or navigate to a notes page
    alert('Delivery notes feature coming soon!');
}

function showReturnNotes(orderId) {
    // This would open a modal or navigate to a notes page
    alert('Return notes feature coming soon!');
}
</script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.driver_mobile', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/driver/history/index.blade.php ENDPATH**/ ?>