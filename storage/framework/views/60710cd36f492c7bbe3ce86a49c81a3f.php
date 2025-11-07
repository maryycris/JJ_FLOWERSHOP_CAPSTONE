

<?php $__env->startSection('title', 'Invoice Details'); ?>

<?php $__env->startPush('styles'); ?>
<style>
/* Invoice Details Styling - matching invoice index hierarchy */
.card-title {
    font-size: 1.1rem !important;
    font-weight: 600;
}

.card-header h5 {
    font-size: 0.95rem !important;
    font-weight: 600;
}

.card-body p {
    font-size: 0.85rem;
}

.card-body strong {
    font-size: 0.85rem;
    font-weight: 600;
}

/* Table styling */
.table {
    font-size: 0.85rem;
}

.table thead th {
    font-size: 0.8rem !important;
    font-weight: 600;
    background-color: #e6f4ea;
}

.table tbody td {
    font-size: 0.85rem;
}

/* Order link styling */
.card-body a[href*="orders"], .card-body a[href*="sales-orders"] {
    color: #7bb47b !important;
    text-decoration: none;
    transition: all 0.2s ease;
}

.card-body a[href*="orders"]:hover, .card-body a[href*="sales-orders"]:hover {
    color: #5aa65a !important;
    text-decoration: underline;
}

/* Amount styling */
.text-success {
    font-size: 0.85rem;
}

/* Button sizing in header */
.card-header .card-tools .btn {
    font-size: 0.75rem !important;
    padding: 0.25rem 0.5rem !important;
    line-height: 1.3 !important;
}

.card-header .card-tools .btn i {
    font-size: 0.7rem;
    margin-right: 0.25rem;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid" style="margin-top: -2rem; padding-top: 0.5rem;">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: #e6f4ea;">
                    <h3 class="card-title mb-0" style="font-size: 1.1rem; font-weight: 600;">Invoice Details - <?php echo e($invoice->invoice_number); ?></h3>
                    <div class="card-tools">
                        <a href="<?php echo e(route('invoices.index')); ?>" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Invoices
                        </a>
                        <?php if($invoice->status === 'ready' && $invoice->payment_type === 'cod'): ?>
                            <a href="<?php echo e(route('invoices.payment', $invoice)); ?>" class="btn btn-sm btn-success ms-2">
                                <i class="fas fa-credit-card"></i> Register Payment
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Invoice Information -->
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header" style="background: #e6f4ea;">
                                    <h5>Invoice Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Invoice Number:</strong> <?php echo e($invoice->invoice_number); ?></p>
                                            <p><strong>Order Number:</strong> 
                                                <a href="<?php echo e(route('admin.sales-orders.show', $invoice->order_id)); ?>">#<?php echo e($invoice->order_id); ?></a>
                                            </p>
                                            <p><strong>Invoice Date:</strong> <?php echo e($invoice->created_at->format('M d, Y')); ?></p>
                                            <p><strong>Payment Type:</strong> 
                                                <?php if($invoice->payment_type === 'online'): ?>
                                                    <span class="badge" style="background-color: #4caf50; color: white;">Online</span>
                                                <?php else: ?>
                                                    <span class="badge" style="background-color: #66bb6a; color: white;">COD</span>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Status:</strong> 
                                                <?php if($invoice->status === 'paid'): ?>
                                                    <span class="badge" style="background-color: #28a745; color: white;">Paid</span>
                                                <?php elseif($invoice->status === 'ready'): ?>
                                                    <span class="badge" style="background-color: #90ee90; color: black;">Ready</span>
                                                <?php elseif($invoice->status === 'draft'): ?>
                                                    <span class="badge" style="background-color: #c8e6c9; color: black;">Draft</span>
                                                <?php else: ?>
                                                    <span class="badge" style="background-color: #2d5016; color: white;">Cancelled</span>
                                                <?php endif; ?>
                                            </p>
                                            <?php
                                                $notes = $invoice->order->notes ?? '';
                                                $billName = $invoice->order->user->name ?? 'Walk-in Customer';
                                                $contactFromNotes = null;
                                                $emailFromNotes = null;

                                                if (!empty($notes)) {
                                                    if (preg_match('/Customer:\s*(.*?)(?:[;,]|$)/', $notes, $m)) {
                                                        $billName = trim($m[1]);
                                                    }
                                                    if (preg_match('/Contact:\s*(.*?)(?:[;,]|$)/', $notes, $m)) {
                                                        $contactFromNotes = trim($m[1]);
                                                    }
                                                    if (preg_match('/Email:\s*([^;,\s]+@[^;,\s]+)/', $notes, $m)) {
                                                        $emailFromNotes = trim($m[1]);
                                                    }
                                                }

                                                // Prefer delivery recipient phone if available, else contact from notes, else user's contact
                                                $phone = $invoice->order->delivery->recipient_phone ?? $contactFromNotes ?? ($invoice->order->user->contact_number ?? null);
                                                $email = $emailFromNotes ?? ($invoice->order->user->email ?? null);
                                            ?>
                                            <p><strong>Customer:</strong> <?php echo e($billName); ?></p>
                                            <?php if($email): ?>
                                                <p><strong>Email:</strong> <?php echo e($email); ?></p>
                                            <?php endif; ?>
                                            <p><strong>Phone:</strong> <?php echo e($phone ?? 'N/A'); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Products -->
                            <div class="card mt-3">
                                <div class="card-header" style="background: #e6f4ea;">
                                    <h5>Products</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Quantity</th>
                                                    <th>Unit Price</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $__currentLoopData = $invoice->order->products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <?php
                                                                $productImage = $product->image ?? null;
                                                                if ($productImage && !empty($productImage)) {
                                                                    if (!str_starts_with($productImage, 'http') && !str_starts_with($productImage, '/')) {
                                                                        $productImage = asset('storage/' . $productImage);
                                                                    }
                                                                }
                                                            ?>
                                                            <?php if($productImage): ?>
                                                                <img src="<?php echo e($productImage); ?>" 
                                                                     alt="<?php echo e($product->name); ?>" 
                                                                     class="img-thumbnail" 
                                                                     style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px; display: block;">
                                                            <?php endif; ?>
                                                            <div>
                                                                <strong><?php echo e($product->name); ?></strong>
                                                                <?php if($product->pivot->rating): ?>
                                                                    <br><small class="text-muted">
                                                                        Rating: <?php echo e($product->pivot->rating); ?>/5
                                                                    </small>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><?php echo e($product->pivot->quantity); ?></td>
                                                    <td>₱<?php echo e(number_format($product->price, 2)); ?></td>
                                                    <td>₱<?php echo e(number_format($product->pivot->quantity * $product->price, 2)); ?></td>
                                                </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php $__currentLoopData = $invoice->order->customBouquets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bouquet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php
                                                    $orderQty = $bouquet->pivot->quantity;
                                                    $customData = $bouquet->customization_data ?? [];
                                                    $freshFlowerQty = $customData['freshFlowerQuantity'] ?? 1;
                                                    $artificialFlowerQty = $customData['artificialFlowerQuantity'] ?? 1;
                                                    
                                                    $components = [];
                                                    
                                                    // Wrapper
                                                    if ($bouquet->wrapper) {
                                                        $components[] = "Wrapper: {$bouquet->wrapper} (x{$orderQty})";
                                                    }
                                                    
                                                    // Fresh Flowers
                                                    $freshFlowers = [];
                                                    if ($bouquet->focal_flower_1) {
                                                        $freshFlowers[] = $bouquet->focal_flower_1;
                                                    }
                                                    if ($bouquet->focal_flower_2) {
                                                        $freshFlowers[] = $bouquet->focal_flower_2;
                                                    }
                                                    if ($bouquet->focal_flower_3) {
                                                        $freshFlowers[] = $bouquet->focal_flower_3;
                                                    }
                                                    if (!empty($freshFlowers)) {
                                                        $totalFreshQty = $freshFlowerQty * $orderQty;
                                                        $components[] = "Fresh Flowers: " . implode(', ', $freshFlowers) . " (x{$totalFreshQty})";
                                                    }
                                                    
                                                    // Greenery
                                                    if ($bouquet->greenery) {
                                                        $components[] = "Greenery: {$bouquet->greenery} (x{$orderQty})";
                                                    }
                                                    
                                                    // Artificial Flowers (Filler)
                                                    if ($bouquet->filler) {
                                                        $totalArtificialQty = $artificialFlowerQty * $orderQty;
                                                        $components[] = "Artificial Flowers: {$bouquet->filler} (x{$totalArtificialQty})";
                                                    }
                                                    
                                                    // Ribbon
                                                    if ($bouquet->ribbon) {
                                                        $components[] = "Ribbon: {$bouquet->ribbon} (x{$orderQty})";
                                                    }
                                                    
                                                    $componentDescription = !empty($components) ? implode('<br>', $components) : '';
                                                    $unitPrice = $bouquet->unit_price ?? ($bouquet->total_price / max($orderQty, 1));
                                                ?>
                                                <tr>
                                                    <td>
                                                        <div>
                                                            <strong>Custom Bouquet</strong>
                                                            <?php if(!empty($componentDescription)): ?>
                                                                <div style="font-size: 0.8rem; color: #666; margin-top: 4px; line-height: 1.6;">
                                                                    <?php echo $componentDescription; ?>

                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                    <td><?php echo e($orderQty); ?></td>
                                                    <td>₱<?php echo e(number_format($unitPrice, 2)); ?></td>
                                                    <td>₱<?php echo e(number_format($unitPrice * $orderQty, 2)); ?></td>
                                                </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Delivery Information -->
                            <?php if($invoice->order->delivery): ?>
                            <div class="card mt-3">
                                <div class="card-header" style="background: #e6f4ea;">
                                    <h5>Delivery Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Delivery Date:</strong> <?php echo e($invoice->order->delivery->delivery_date); ?></p>
                                            <p><strong>Delivery Time:</strong> <?php echo e($invoice->order->delivery->delivery_time); ?></p>
                                            <p><strong>Recipient:</strong> <?php echo e($invoice->order->delivery->recipient_name); ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Phone:</strong> <?php echo e($invoice->order->delivery->recipient_phone ?? 'N/A'); ?></p>
                                            <p><strong>Address:</strong> <?php echo e($invoice->order->delivery->delivery_address); ?></p>
                                            <p><strong>Status:</strong> 
                                                <?php
                                                    $deliveryStatus = $invoice->order->delivery->status ?? $invoice->order->order_status ?? 'pending';
                                                ?>
                                                <span class="badge badge-info"><?php echo e(ucfirst($deliveryStatus)); ?></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Payment Summary -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header" style="background: #e6f4ea;">
                                    <h5>Payment Summary</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <p><strong>Subtotal:</strong></p>
                                        </div>
                                        <div class="col-6 text-right">
                                            <p>₱<?php echo e(number_format($invoice->subtotal, 2)); ?></p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <p><strong>Shipping Fee:</strong></p>
                                        </div>
                                        <div class="col-6 text-right">
                                            <p>₱<?php echo e(number_format($invoice->shipping_fee, 2)); ?></p>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-6">
                                            <p><strong>Total Amount:</strong></p>
                                        </div>
                                        <div class="col-6 text-right">
                                            <p class="text-success font-weight-bold">₱<?php echo e(number_format($invoice->total_amount, 2)); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment History -->
                            <?php if($invoice->payments->count() > 0): ?>
                            <div class="card mt-3">
                                <div class="card-header" style="background: #e6f4ea;">
                                    <h5>Payment History</h5>
                                </div>
                                <div class="card-body">
                                    <?php $__currentLoopData = $invoice->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="border-bottom pb-2 mb-2">
                                        <div class="d-flex justify-content-between">
                                            <span><strong><?php echo e(ucfirst($payment->mode_of_payment)); ?></strong></span>
                                            <span class="text-success">₱<?php echo e(number_format($payment->amount, 2)); ?></span>
                                        </div>
                                        <small class="text-muted">
                                            <?php echo e($payment->payment_date->format('M d, Y')); ?>

                                            <?php if($payment->memo): ?>
                                                - <?php echo e($payment->memo); ?>

                                            <?php endif; ?>
                                            <?php if($payment->processedBy): ?>
                                                <br>Processed by: <?php echo e($payment->processedBy->name); ?>

                                            <?php endif; ?>
                                        </small>
                                    </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
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

<?php echo $__env->make('layouts.admin_app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\JJ_Flowershop_Capstone\resources\views/admin/invoices/show.blade.php ENDPATH**/ ?>