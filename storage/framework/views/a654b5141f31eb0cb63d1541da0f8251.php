

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-success">
                <i class="fas fa-undo me-2"></i>Return Details - Order #<?php echo e($order->id); ?>

            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('admin.returns.index')); ?>">Return Management</a></li>
                    <li class="breadcrumb-item active">Order #<?php echo e($order->id); ?></li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('admin.returns.index')); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Returns
            </a>
            <?php if($order->return_status === 'pending'): ?>
                <button class="btn btn-success" onclick="showReturnAction(<?php echo e($order->id); ?>, '<?php echo e($order->return_status); ?>')">
                    <i class="fas fa-cog me-1"></i>Take Action
                </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <!-- Order Information -->
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Order Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Order ID:</strong> #<?php echo e($order->id); ?></p>
                            <p><strong>Order Date:</strong> <?php echo e($order->created_at->format('M d, Y H:i')); ?></p>
                            <p><strong>Order Status:</strong> 
                                <span class="badge bg-warning text-dark"><?php echo e(ucfirst(str_replace('_', ' ', $order->status))); ?></span>
                            </p>
                            <p><strong>Total Amount:</strong> ₱<?php echo e(number_format($order->total_price, 2)); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Return Date:</strong> <?php echo e($order->returned_at ? $order->returned_at->format('M d, Y H:i') : 'N/A'); ?></p>
                            <p><strong>Return Status:</strong> 
                                <?php switch($order->return_status):
                                    case ('pending'): ?>
                                        <span class="badge bg-info">Pending Review</span>
                                        <?php break; ?>
                                    <?php case ('approved'): ?>
                                        <span class="badge bg-success">Approved</span>
                                        <?php break; ?>
                                    <?php case ('rejected'): ?>
                                        <span class="badge bg-danger">Rejected</span>
                                        <?php break; ?>
                                    <?php case ('resolved'): ?>
                                        <span class="badge bg-secondary">Resolved</span>
                                        <?php break; ?>
                                <?php endswitch; ?>
                            </p>
                            <p><strong>Return Reason:</strong> <?php echo e($order->return_reason); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Customer Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> <?php echo e($order->user->name); ?></p>
                            <p><strong>Email:</strong> <?php echo e($order->user->email); ?></p>
                            <p><strong>Phone:</strong> <?php echo e($order->user->contact_number ?? 'N/A'); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Address:</strong> <?php echo e($order->delivery->delivery_address ?? 'N/A'); ?></p>
                            <?php if($order->delivery && $order->delivery->special_instructions): ?>
                                <p><strong>Special Instructions:</strong> <?php echo e($order->delivery->special_instructions); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Driver Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-truck me-2"></i>Driver Information</h5>
                </div>
                <div class="card-body">
                    <?php if($order->returnedByDriver): ?>
                        <p><strong>Driver Name:</strong> <?php echo e($order->returnedByDriver->name); ?></p>
                        <p><strong>Driver Email:</strong> <?php echo e($order->returnedByDriver->email); ?></p>
                        <p><strong>Driver Phone:</strong> <?php echo e($order->returnedByDriver->contact_number ?? 'N/A'); ?></p>
                    <?php else: ?>
                        <p class="text-muted">Driver information not available</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Order Items</h5>
                </div>
                <div class="card-body">
                    <?php if($order->products->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $order->products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($product->name); ?></td>
                                        <td><?php echo e($product->pivot->quantity); ?></td>
                                        <td>₱<?php echo e(number_format($product->price, 2)); ?></td>
                                        <td>₱<?php echo e(number_format($product->pivot->quantity * $product->price, 2)); ?></td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No products found for this order.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Return Actions & Notes -->
        <div class="col-md-4">
            <!-- Return Notes -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Return Notes</h5>
                </div>
                <div class="card-body">
                    <?php if($order->return_notes): ?>
                        <p><strong>Driver Notes:</strong></p>
                        <p class="text-muted"><?php echo e($order->return_notes); ?></p>
                    <?php else: ?>
                        <p class="text-muted">No additional notes provided.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Admin Actions -->
            <?php if($order->return_status === 'pending'): ?>
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Admin Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-success" onclick="showReturnAction(<?php echo e($order->id); ?>, '<?php echo e($order->return_status); ?>')">
                            <i class="fas fa-check me-1"></i>Review Return
                        </button>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Refund Processing -->
            <?php if($order->return_status === 'approved' && !$order->refund_processed_at): ?>
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Refund Processing</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-warning" onclick="showRefundModal(<?php echo e($order->id); ?>, <?php echo e($order->total_price); ?>)">
                            <i class="fas fa-money-bill-wave me-1"></i>Process Refund
                        </button>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Status History -->
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Status History</h5>
                </div>
                <div class="card-body">
                    <?php if($order->statusHistories->count() > 0): ?>
                        <div class="timeline">
                            <?php $__currentLoopData = $order->statusHistories->sortByDesc('created_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $history): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="timeline-item mb-3">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <div class="bg-primary rounded-circle p-2">
                                            <i class="fas fa-circle text-white" style="font-size: 0.5rem;"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1"><?php echo e(ucfirst(str_replace('_', ' ', $history->status))); ?></h6>
                                        <p class="text-muted small mb-1"><?php echo e($history->notes); ?></p>
                                        <small class="text-muted"><?php echo e($history->created_at->format('M d, Y H:i')); ?></small>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No status history available.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include the same modals from index page -->
<?php echo $__env->make('admin.returns.partials.modals', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<script>
// Include the same JavaScript functions from index page
function showReturnAction(orderId, currentStatus) {
    document.getElementById('orderId').value = orderId;
    document.getElementById('return_status').value = '';
    document.getElementById('admin_notes').value = '';
    
    const modal = new bootstrap.Modal(document.getElementById('returnActionModal'));
    modal.show();
}

function showRefundModal(orderId, maxAmount) {
    document.getElementById('refundOrderId').value = orderId;
    document.getElementById('refund_amount').max = maxAmount;
    document.getElementById('refund_amount').value = maxAmount;
    
    const modal = new bootstrap.Modal(document.getElementById('refundModal'));
    modal.show();
}

function submitReturnAction() {
    const form = document.getElementById('returnActionForm');
    const formData = new FormData(form);
    const orderId = formData.get('order_id');
    
    fetch(`/admin/returns/${orderId}/update-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Action Submitted!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred while submitting the action.'
        });
    });
}

function processRefund() {
    const form = document.getElementById('refundForm');
    const formData = new FormData(form);
    const orderId = formData.get('order_id');
    
    fetch(`/admin/returns/${orderId}/process-refund`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Refund Processed!',
                text: data.message,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred while processing the refund.'
        });
    });
}
</script>

<style>
/* Ensure proper positioning without sidebar overlap */
.container-fluid {
    margin-left: 0 !important;
    padding-left: 20px !important;
    padding-right: 20px !important;
    max-width: 100% !important;
    width: 100% !important;
}

/* Ensure content doesn't get cut off */
.row {
    margin-left: 0 !important;
    margin-right: 0 !important;
}

.col-12, .col-md-6, .col-md-4, .col-md-8 {
    padding-left: 10px !important;
    padding-right: 10px !important;
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/admin/returns/show.blade.php ENDPATH**/ ?>