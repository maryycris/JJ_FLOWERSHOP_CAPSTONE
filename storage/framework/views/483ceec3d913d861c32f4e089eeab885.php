

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold">Delivery History</h4>
    <span class="badge bg-success"><?php echo e($completedDeliveries->count()); ?> completed</span>
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

<script>
function showDeliveryNotes(deliveryId) {
    // This would open a modal or navigate to a notes page
    alert('Delivery notes feature coming soon!');
}
</script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.driver_mobile', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/driver/history/index.blade.php ENDPATH**/ ?>