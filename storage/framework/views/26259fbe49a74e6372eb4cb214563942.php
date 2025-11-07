

<?php $__env->startSection('title', 'Invoice Management'); ?>

<?php $__env->startPush('styles'); ?>
<style>
/* Invoice Table Styling - matching clerk invoice page */
#invoicesTable.table {
    font-size: 0.85rem;
    background-color: white;
}

#invoicesTable.table thead th {
    font-size: 0.8rem !important;
    font-weight: 600;
    padding: 0.5rem 0.3rem;
    vertical-align: middle;
    background-color: #e6f4ea;
}

#invoicesTable.table tbody td {
    font-size: 0.85rem;
    padding: 0.4rem 0.3rem;
    vertical-align: middle;
    background-color: white;
}

.card-title {
    font-size: 1.1rem;
    font-weight: 600;
}

/* Order link styling */
.order-link {
    color: #7bb47b !important;
    text-decoration: none;
    transition: all 0.2s ease;
}

.order-link:hover {
    color: #5aa65a !important;
    text-decoration: underline;
}

/* Actions column - center icons */
#invoicesTable.table thead th:last-child,
#invoicesTable.table tbody td:last-child {
    text-align: center;
}

/* Action buttons - black icon only, background on hover - HIGH SPECIFICITY */
#invoicesTable tbody td .btn-group .invoice-action-btn,
#invoicesTable tbody td .btn-group a.invoice-action-btn,
table#invoicesTable tbody td .btn-group .invoice-action-btn,
table#invoicesTable tbody td .btn-group a.invoice-action-btn {
    background: transparent !important;
    border: none !important;
    background-color: transparent !important;
    color: #000000 !important;
    padding: 0.3rem 0.4rem !important;
    transition: all 0.2s ease !important;
    display: inline-flex !important;
    align-items: center;
    justify-content: center;
    text-decoration: none !important;
    border-radius: 3px;
    margin: 0 !important;
    box-shadow: none !important;
    outline: none !important;
    min-width: auto !important;
    width: auto !important;
    height: auto !important;
}

#invoicesTable tbody td .btn-group .invoice-action-btn i,
#invoicesTable tbody td .btn-group .invoice-action-btn i.fas,
table#invoicesTable tbody td .btn-group .invoice-action-btn i {
    font-size: 0.85rem !important;
    color: #000000 !important;
    margin: 0 !important;
    padding: 0 !important;
    line-height: 1 !important;
}

#invoicesTable tbody td .btn-group .invoice-action-btn:hover,
#invoicesTable tbody td .btn-group a.invoice-action-btn:hover,
table#invoicesTable tbody td .btn-group .invoice-action-btn:hover {
    background-color: #7bb47b !important;
    background: #7bb47b !important;
}

#invoicesTable tbody td .btn-group .invoice-action-btn:hover i,
#invoicesTable tbody td .btn-group .invoice-action-btn:hover i.fas,
table#invoicesTable tbody td .btn-group .invoice-action-btn:hover i {
    color: #ffffff !important;
}

/* Remove btn-group spacing and override Bootstrap */
#invoicesTable.table .btn-group,
#invoicesTable.table tbody .btn-group {
    display: flex !important;
    justify-content: center !important;
    align-items: center;
    gap: 0.1rem !important;
    border: none !important;
    box-shadow: none !important;
    padding: 0 !important;
    margin: 0 !important;
}

/* Invoice Pagination - Smaller and No White Background - Exclusive to Invoice Page */
.card-body .pagination-container {
    background: transparent !important;
    padding: 0.1rem 0 !important;
    box-shadow: none !important;
    margin: 0.10rem 0 0 0 !important;
}

.card-body .pagination-custom {
    font-size: 0.7rem !important;
    margin: 0 !important;
}

.card-body .pagination-custom .page-link {
    color: #7bb47b !important;
    background-color: white !important;
    border: 1px solid #e6f4ea !important;
    padding: 0.3rem 0.5rem !important;
    font-size: 0.7rem !important;
    margin: 0 2px !important;
    border-radius: 4px !important;
    transition: all 0.2s ease !important;
    font-weight: 500 !important;
    min-width: 28px !important;
    height: 28px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.card-body .pagination-custom .page-link:hover {
    color: #fff !important;
    background-color: #7bb47b !important;
    border-color: #7bb47b !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 2px 4px rgba(123, 180, 123, 0.3) !important;
}

.card-body .pagination-custom .page-item.active .page-link {
    color: #fff !important;
    background-color: #7bb47b !important;
    border-color: #7bb47b !important;
    box-shadow: 0 2px 8px rgba(123, 180, 123, 0.4) !important;
}

.card-body .pagination-custom .page-item.disabled .page-link {
    color: #6c757d !important;
    background-color: #fff !important;
    border-color: #dee2e6 !important;
    cursor: not-allowed !important;
}

.card-body .pagination-custom .page-item.disabled .page-link:hover {
    color: #6c757d !important;
    background-color: #fff !important;
    border-color: #dee2e6 !important;
    transform: none !important;
    box-shadow: none !important;
}

/* Search form styling */
.form-control, .form-select {
    font-size: 0.85rem;
}

.btn-success {
    font-size: 0.85rem;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid" style="margin-top: -2rem; padding-top: 0.5rem;">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: #e6f4ea;">
                    <h3 class="card-title mb-0" style="font-size: 1.1rem; font-weight: 600;">Invoice Management</h3>
                </div>
                <div class="card-body">
                    <!-- Search and Filter -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form method="GET" action="" class="d-flex">
                                <input type="text" name="search" class="form-control me-2" placeholder="Search by invoice number or customer name..." value="<?php echo e(request('search')); ?>">
                                <button type="submit" class="btn btn-success">Search</button>
                            </form>
                        </div>
                        <div class="col-md-3">
                            <form method="GET" action="">
                                <?php if(request('search')): ?>
                                    <input type="hidden" name="search" value="<?php echo e(request('search')); ?>">
                                <?php endif; ?>
                                <select class="form-select" name="status" onchange="this.form.submit()">
                                    <option value="">All Status</option>
                                    <option value="ready" <?php echo e(request('status') == 'ready' ? 'selected' : ''); ?>>Ready</option>
                                    <option value="paid" <?php echo e(request('status') == 'paid' ? 'selected' : ''); ?>>Paid</option>
                                </select>
                            </form>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="invoicesTable">
                            <thead>
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Payment Type</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $notes = $invoice->order->notes ?? '';
                                    $customerName = $invoice->order->user->name ?? 'Walk-in Customer';
                                    if (!empty($notes) && preg_match('/Customer:\s*(.*?)(?:[;,]|$)/', $notes, $m)) {
                                        $customerName = trim($m[1]);
                                    }
                                ?>
                                <tr>
                                    <td>
                                        <strong><?php echo e($invoice->invoice_number); ?></strong>
                                    </td>
                                    <td>
                                        <a href="<?php echo e(route('admin.sales-orders.show', $invoice->order_id)); ?>" class="order-link">
                                            #<?php echo e($invoice->order_id); ?>

                                        </a>
                                    </td>
                                    <td><?php echo e($customerName); ?></td>
                                    <td><?php echo e($invoice->created_at->format('M d, Y')); ?></td>
                                    <td>
                                        <span class="text-success font-weight-bold">
                                            ₱<?php echo e(number_format($invoice->total_amount, 2)); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <?php if($invoice->status === 'paid'): ?>
                                            <span class="badge" style="background-color: #28a745; color: white;">Paid</span>
                                        <?php elseif($invoice->status === 'ready'): ?>
                                            <span class="badge" style="background-color: #90ee90; color: black;">Ready</span>
                                        <?php elseif($invoice->status === 'draft'): ?>
                                            <span class="badge" style="background-color: #c8e6c9; color: black;">Draft</span>
                                        <?php else: ?>
                                            <span class="badge" style="background-color: #2d5016; color: white;">Cancelled</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($invoice->payment_type === 'online'): ?>
                                            <span class="badge" style="background-color: #4caf50; color: white;">Online</span>
                                        <?php else: ?>
                                            <span class="badge" style="background-color: #66bb6a; color: white;">COD</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group" style="display: flex; justify-content: center; gap: 0.15rem;">
                                            <a href="<?php echo e(route('invoices.show', $invoice->id)); ?>" 
                                               class="invoice-action-btn" 
                                               title="View Invoice">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <?php if($invoice->status === 'ready'): ?>
                                                <a href="<?php echo e(route('invoices.payment', $invoice)); ?>" 
                                                   class="invoice-action-btn" 
                                                   title="Register Payment">
                                                    <i class="fas fa-credit-card"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="fas fa-file-invoice fa-3x mb-3"></i>
                                        <br>
                                        No invoices found
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if($invoices->hasPages()): ?>
                        <?php if (isset($component)) { $__componentOriginal41032d87daf360242eb88dbda6c75ed1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal41032d87daf360242eb88dbda6c75ed1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pagination','data' => ['currentPage' => $invoices->currentPage(),'totalPages' => $invoices->lastPage(),'baseUrl' => request()->url(),'queryParams' => request()->query()]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pagination'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['currentPage' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoices->currentPage()),'totalPages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoices->lastPage()),'baseUrl' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->url()),'queryParams' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request()->query())]); ?>
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
    </div>
</div>

<!-- SweetAlert Success Message -->
<?php if(session('success')): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '<?php echo e(session('success')); ?>',
                showConfirmButton: false,
                timer: 3000
            });
        });
    </script>
<?php endif; ?>

<?php if(session('error')): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '<?php echo e(session('error')); ?>',
                showConfirmButton: false,
                timer: 3000
            });
        });
    </script>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/admin/invoices/index.blade.php ENDPATH**/ ?>