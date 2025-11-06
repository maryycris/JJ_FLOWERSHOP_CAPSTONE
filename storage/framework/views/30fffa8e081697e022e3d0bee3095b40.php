

<?php $__env->startSection('content'); ?>
<?php $__env->startPush('styles'); ?>
<style>
/* Loyalty Cards Styling - matching invoice page hierarchy */
.card-title {
    font-size: 1.1rem !important;
    font-weight: 600;
}

.card-header h5, .card-header h6 {
    font-size: 0.95rem !important;
    font-weight: 600;
}

/* Table styling */
.table {
    font-size: 0.85rem;
    background-color: white;
}

.table thead th {
    font-size: 0.8rem !important;
    font-weight: 600;
    padding: 0.5rem 0.3rem;
    vertical-align: middle;
    background-color: #e6f4ea;
}

.table tbody td {
    font-size: 0.85rem;
    padding: 0.4rem 0.3rem;
    vertical-align: middle;
}

/* Form controls */
.form-control {
    font-size: 0.85rem;
}

.form-label {
    font-size: 0.85rem;
    font-weight: 500;
}

/* Buttons */
.btn-success {
    font-size: 0.85rem;
    padding: 0.35rem 0.7rem;
}

.btn-success i, .btn-success .bi {
    font-size: 0.85rem;
}

.btn-sm {
    font-size: 0.85rem;
    padding: 0.25rem 0.5rem;
}
</style>
<?php $__env->stopPush(); ?>

<div class="container-fluid" style="margin-top: -2rem; padding-top: 0.5rem;">
    <div class="card shadow mb-4">
        <div class="card-header" style="background: #e6f4ea;">
            <h5 class="card-title mb-0" style="font-size: 0.95rem; font-weight: 600;">Loyalty Cards Management</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Stamps</th>
                            <th>Updated</th>
                            <th style="width:260px">Adjust</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center justify-content-between">
                                    <?php
                                        $userName = 'Unknown User';
                                        if (isset($card->user) && $card->user) {
                                            $userName = $card->user->first_name ?? ($card->user->name ?? 'Unknown User');
                                        }
                                    ?>
                                    <span><?php echo e($userName); ?> (ID: <?php echo e($card->user_id); ?>)</span>
                                    <a href="<?php echo e(route('admin.loyalty.history', $card)); ?>" class="btn btn-success btn-sm">
                                        <i class="bi bi-clock-history"></i> History
                                    </a>
                                </div>
                            </td>
                            <td><?php echo e($card->stamps_count); ?>/5</td>
                            <td><?php echo e($card->updated_at->diffForHumans()); ?></td>
                            <td>
                                <form method="POST" action="<?php echo e(route('admin.loyalty.adjust', $card)); ?>" class="d-flex gap-2">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PUT'); ?>
                                    <input type="number" name="delta" class="form-control" value="1" min="-5" max="5" style="width:80px">
                                    <input type="text" name="reason" class="form-control" placeholder="Reason (optional)">
                                    <button class="btn btn-success btn-sm">
                                        <i class="bi bi-check-lg"></i> Apply
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="4" class="text-center">No loyalty cards yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if($cards->hasPages()): ?>
                <?php if (isset($component)) { $__componentOriginal41032d87daf360242eb88dbda6c75ed1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal41032d87daf360242eb88dbda6c75ed1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pagination','data' => ['currentPage' => $cards->currentPage(),'totalPages' => $cards->lastPage(),'baseUrl' => request()->url(),'queryParams' => request()->query()]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pagination'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['currentPage' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($cards->currentPage()),'totalPages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($cards->lastPage()),'baseUrl' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->url()),'queryParams' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->query())]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal41032d87daf360242eb88dbda6c75ed1)): ?>
<?php $attributes = $__attributesOriginal41032d87daf360242eb88dbda6c75ed1; ?>
<?php unset($__attributesOriginal41032d87daf360242eb88dbda6c75ed1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal41032d87daf360242eb88dbda6c75ed1)): ?>
<?php $component = $__componentOriginal41032d87daf360242eb88dbda6c75ed1; ?>
<?php unset($__componentOriginal41032d87daf360242eb88dbda6c75ed1); ?>
<?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layouts.admin_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/admin/loyalty/index.blade.php ENDPATH**/ ?>