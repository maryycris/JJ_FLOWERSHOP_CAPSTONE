<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Dashboard</h2>
            
            <!-- Order Stats -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <a href="<?php echo e(route('clerk.orders.index', ['status' => 'pending'])); ?>" class="text-decoration-none">
                        <div class="card text-center h-100 pending-card">
                            <div class="card-body">
                                <h3 class="text-warning"><?php echo e($pendingOrdersCount ?? 0); ?></h3>
                                <p class="mb-0">Pending Orders</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="<?php echo e(route('clerk.orders.index', ['status' => 'approved'])); ?>" class="text-decoration-none">
                        <div class="card text-center h-100 approved-card">
                            <div class="card-body">
                                <h3 class="text-info"><?php echo e($approvedOrdersCount ?? 0); ?></h3>
                                <p class="mb-0">Approved Orders</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="<?php echo e(route('clerk.orders.index', ['status' => 'on_delivery'])); ?>" class="text-decoration-none">
                        <div class="card text-center h-100 delivery-card">
                            <div class="card-body">
                                <h3 class="text-primary"><?php echo e($onDeliveryCount ?? 0); ?></h3>
                                <p class="mb-0">On Delivery</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="<?php echo e(route('clerk.orders.index', ['status' => 'completed', 'today' => 1])); ?>" class="text-decoration-none">
                        <div class="card text-center h-100 completed-card">
                            <div class="card-body">
                                <h3 class="text-success"><?php echo e($completedTodayCount ?? 0); ?></h3>
                                <p class="mb-0">Completed Today</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            
            <!-- Restock Alert -->
            <div class="row">
                <div class="col-md-6 mb-4">
                    <a href="<?php echo e(route('clerk.inventory.manage')); ?>" class="text-decoration-none">
                        <div class="card h-100 restock-alert-card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-exclamation-triangle me-2"></i>Restock Alerts
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if(isset($restockProducts) && count($restockProducts)): ?>
                                    <?php $__currentLoopData = $restockProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                            <span><?php echo e($product->name); ?></span>
                                            <span class="badge bg-danger"><?php echo e($product->stock); ?> left</span>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <p class="text-muted mb-0">All products are well stocked</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->startPush('styles'); ?>
<style>
.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

a:hover .card {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Pending Orders - Pale Orange */
.pending-card {
    background-color: #fef9e7;
    border: 1px solid #f4d03f;
}

/* Approved Orders - Pale Sky Blue */
.approved-card {
    background-color: #f0f8ff;
    border: 1px solid #85c1e9;
}

/* On Delivery - Pale Blue */
.delivery-card {
    background-color: #f0f4ff;
    border: 1px solid #a8d8ff;
}

/* Completed Today - Pale Green */
.completed-card {
    background-color: #f0f9f0;
    border: 1px solid #a8e6a8;
}

/* Restock Alert - Pale Red */
.restock-alert-card {
    background-color: #fdf2f2;
    border: 1px solid #f5b7b1;
}
</style>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.clerk_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/clerk/dashboard.blade.php ENDPATH**/ ?>