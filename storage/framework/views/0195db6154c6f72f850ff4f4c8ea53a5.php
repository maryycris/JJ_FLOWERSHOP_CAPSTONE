

<?php $__env->startSection('content'); ?>
<div class="container py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Loyalty History - <?php echo e($card->user->first_name ?? $card->user->name); ?></h4>
        <a href="<?php echo e(route('admin.loyalty.index')); ?>" class="btn btn-outline-secondary">← Back to Cards</a>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Current Status</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="h2 text-success"><?php echo e($card->stamps_count); ?>/5</div>
                        <div class="text-muted">Stamps Collected</div>
                        <?php if($card->stamps_count >= 5): ?>
                            <span class="badge bg-success mt-2">Eligible for 50% discount</span>
                        <?php else: ?>
                            <div class="progress mt-2" style="height: 8px;">
                                <div class="progress-bar bg-success" style="width: <?php echo e(($card->stamps_count / 5) * 100); ?>%"></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Activity Timeline</h6>
                </div>
                <div class="card-body">
                    <?php
                        $activities = collect();
                        
                        // Add stamps
                        foreach($card->stamps as $stamp) {
                            $activities->push([
                                'type' => 'stamp',
                                'date' => $stamp->earned_at ?? $stamp->created_at,
                                'description' => 'Earned stamp from Order #' . $stamp->order->id,
                                'delta' => 1,
                                'order' => $stamp->order
                            ]);
                        }
                        
                        // Add redemptions
                        foreach($card->redemptions as $redemption) {
                            $activities->push([
                                'type' => 'redemption',
                                'date' => $redemption->redeemed_at ?? $redemption->created_at,
                                'description' => 'Redeemed 50% discount (₱' . number_format($redemption->discount_amount, 2) . ') on Order #' . $redemption->order->id,
                                'delta' => -5,
                                'order' => $redemption->order
                            ]);
                        }
                        
                        // Add adjustments
                        foreach($card->adjustments as $adjustment) {
                            $activities->push([
                                'type' => 'adjustment',
                                'date' => $adjustment->created_at,
                                'description' => 'Manual adjustment by ' . $adjustment->adjustedBy->name . 
                                    ($adjustment->reason ? ' - ' . $adjustment->reason : ''),
                                'delta' => $adjustment->delta,
                                'previous' => $adjustment->previous_count,
                                'new' => $adjustment->new_count
                            ]);
                        }
                        
                        $activities = $activities->sortByDesc('date');
                    ?>

                    <?php if($activities->count() > 0): ?>
                        <div class="timeline">
                            <?php $__currentLoopData = $activities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="timeline-item d-flex mb-3">
                                    <div class="timeline-marker me-3">
                                        <?php if($activity['type'] === 'stamp'): ?>
                                            <i class="fas fa-stamp text-success"></i>
                                        <?php elseif($activity['type'] === 'redemption'): ?>
                                            <i class="fas fa-gift text-warning"></i>
                                        <?php else: ?>
                                            <i class="fas fa-edit text-info"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <div class="fw-semibold"><?php echo e($activity['description']); ?></div>
                                                <small class="text-muted"><?php echo e(\Carbon\Carbon::parse($activity['date'])->format('M d, Y g:i A')); ?></small>
                                            </div>
                                            <div class="text-end">
                                                <?php if($activity['type'] === 'adjustment'): ?>
                                                    <span class="badge bg-info"><?php echo e($activity['previous']); ?> → <?php echo e($activity['new']); ?></span>
                                                <?php else: ?>
                                                    <span class="badge <?php echo e($activity['delta'] > 0 ? 'bg-success' : 'bg-warning'); ?>">
                                                        <?php echo e($activity['delta'] > 0 ? '+' : ''); ?><?php echo e($activity['delta']); ?>

                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-history fa-2x mb-2"></i>
                            <div>No activity yet</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline-marker {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
</style>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/admin/loyalty/history.blade.php ENDPATH**/ ?>