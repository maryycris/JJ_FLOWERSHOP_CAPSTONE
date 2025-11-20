@extends('layouts.admin_app')

@section('title', 'Invoice Details')

@push('styles')
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
@endpush

@section('content')
<div class="container-fluid" style="margin-top: -2rem; padding-top: 0.5rem;">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center" style="background: #e6f4ea;">
                    <h3 class="card-title mb-0" style="font-size: 1.1rem; font-weight: 600;">Invoice Details - {{ $invoice->invoice_number }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Invoices
                        </a>
                        @if($invoice->status === 'ready' && $invoice->payment_type === 'cod')
                            <a href="{{ route('invoices.payment', $invoice) }}" class="btn btn-sm btn-success ms-2">
                                <i class="fas fa-credit-card"></i> Register Payment
                            </a>
                        @endif
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
                                            <p><strong>Invoice Number:</strong> {{ $invoice->invoice_number }}</p>
                                            <p><strong>Order Number:</strong> 
                                                <a href="{{ route('admin.sales-orders.show', $invoice->order_id) }}">{{ $invoice->order->so_number_format ?? 'SO-' . str_pad($invoice->order_id, 5, '0', STR_PAD_LEFT) }}</a>
                                            </p>
                                            <p><strong>Invoice Date:</strong> {{ $invoice->created_at->format('M d, Y') }}</p>
                                            <p><strong>Payment Type:</strong> 
                                                @if($invoice->payment_type === 'online')
                                                    <span class="badge" style="background-color: #4caf50; color: white;">Online</span>
                                                @else
                                                    <span class="badge" style="background-color: #66bb6a; color: white;">COD</span>
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Status:</strong> 
                                                @if($invoice->status === 'paid')
                                                    <span class="badge" style="background-color: #28a745; color: white;">Paid</span>
                                                @elseif($invoice->status === 'ready')
                                                    <span class="badge" style="background-color: #90ee90; color: black;">Ready</span>
                                                @elseif($invoice->status === 'draft')
                                                    <span class="badge" style="background-color: #c8e6c9; color: black;">Draft</span>
                                                @else
                                                    <span class="badge" style="background-color: #2d5016; color: white;">Cancelled</span>
                                                @endif
                                            </p>
                                            @php
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
                                            @endphp
                                            <p><strong>Customer:</strong> {{ $billName }}</p>
                                            @if($email)
                                                <p><strong>Email:</strong> {{ $email }}</p>
                                            @endif
                                            <p><strong>Phone:</strong> {{ $phone ?? 'N/A' }}</p>
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
                                                @foreach($invoice->order->products as $product)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            @php
                                                                $productImage = $product->image ?? null;
                                                                if ($productImage && !empty($productImage)) {
                                                                    if (!str_starts_with($productImage, 'http') && !str_starts_with($productImage, '/')) {
                                                                        $productImage = asset('storage/' . $productImage);
                                                                    }
                                                                }
                                                            @endphp
                                                            @if($productImage)
                                                                <img src="{{ $productImage }}" 
                                                                     alt="{{ $product->name }}" 
                                                                     class="img-thumbnail" 
                                                                     style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px; display: block;">
                                                            @endif
                                                            <div>
                                                                <strong>{{ $product->name }}</strong>
                                                                @if($product->pivot->rating)
                                                                    <br><small class="text-muted">
                                                                        Rating: {{ $product->pivot->rating }}/5
                                                                    </small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ $product->pivot->quantity }}</td>
                                                    <td>₱{{ number_format($product->price, 2) }}</td>
                                                    <td>₱{{ number_format($product->pivot->quantity * $product->price, 2) }}</td>
                                                </tr>
                                                @endforeach
                                                @foreach($invoice->order->customBouquets as $bouquet)
                                                @php
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
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <div>
                                                            <strong>Custom Bouquet</strong>
                                                            @if(!empty($componentDescription))
                                                                <div style="font-size: 0.8rem; color: #666; margin-top: 4px; line-height: 1.6;">
                                                                    {!! $componentDescription !!}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td>{{ $orderQty }}</td>
                                                    <td>₱{{ number_format($unitPrice, 2) }}</td>
                                                    <td>₱{{ number_format($unitPrice * $orderQty, 2) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Delivery Information -->
                            @if($invoice->order->delivery || $invoice->order->type === 'online')
                            <div class="card mt-3">
                                <div class="card-header" style="background: #e6f4ea;">
                                    <h5>Delivery Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Delivery Date:</strong> {{ $invoice->order->delivery->delivery_date ?? 'N/A' }}</p>
                                            <p><strong>Delivery Time:</strong> {{ $invoice->order->delivery->delivery_time ?? 'N/A' }}</p>
                                            <p><strong>Recipient:</strong> {{ $invoice->order->delivery->recipient_name ?? $invoice->order->user->name ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Phone:</strong> {{ $invoice->order->delivery->recipient_phone ?? $invoice->order->user->contact_number ?? 'N/A' }}</p>
                                            <p><strong>Address:</strong> 
                                                @php
                                                    // Try multiple sources for delivery address
                                                    $address = null;
                                                    
                                                    // 1. Try delivery record's delivery_address
                                                    if ($invoice->order->delivery) {
                                                        $deliveryAddress = trim($invoice->order->delivery->delivery_address ?? '');
                                                        if (!empty($deliveryAddress) && $deliveryAddress !== 'N/A') {
                                                            $address = $deliveryAddress;
                                                        }
                                                    }
                                                    
                                                    // 2. If empty, try to get from user's default address
                                                    if (empty($address)) {
                                                        $defaultAddress = $invoice->order->user->addresses()->where('is_default', true)->first();
                                                        if ($defaultAddress) {
                                                            $address = trim(collect([
                                                                $defaultAddress->street_address,
                                                                $defaultAddress->barangay,
                                                                $defaultAddress->municipality ?: $defaultAddress->city,
                                                                'Cebu, Philippines'
                                                            ])->filter()->implode(', '));
                                                        }
                                                    }
                                                    
                                                    // 3. If still empty, try any address from user
                                                    if (empty($address)) {
                                                        $anyAddress = $invoice->order->user->addresses()->first();
                                                        if ($anyAddress) {
                                                            $address = trim(collect([
                                                                $anyAddress->street_address,
                                                                $anyAddress->barangay,
                                                                $anyAddress->municipality ?: $anyAddress->city,
                                                                'Cebu, Philippines'
                                                            ])->filter()->implode(', '));
                                                        }
                                                    }
                                                    
                                                    // 4. If still empty, check order notes for address
                                                    if (empty($address) && !empty($invoice->order->notes)) {
                                                        if (preg_match('/Address:\s*(.+?)(?:[;,]|$)/i', $invoice->order->notes, $matches)) {
                                                            $address = trim($matches[1]);
                                                        }
                                                    }
                                                    
                                                    // If still empty, show N/A
                                                    echo $address ?: 'N/A';
                                                @endphp
                                            </p>
                                            <p><strong>Status:</strong> 
                                                @php
                                                    $deliveryStatus = 'pending';
                                                    
                                                    // Try to get status from delivery
                                                    if ($invoice->order->delivery && $invoice->order->delivery->status) {
                                                        $deliveryStatus = $invoice->order->delivery->status;
                                                    }
                                                    // Try order_status
                                                    elseif ($invoice->order->order_status) {
                                                        $deliveryStatus = $invoice->order->order_status;
                                                    }
                                                    // Try status field
                                                    elseif ($invoice->order->status) {
                                                        $deliveryStatus = $invoice->order->status;
                                                    }
                                                    
                                                    // Determine badge style (using inline styles like invoice status)
                                                    $statusLower = strtolower($deliveryStatus);
                                                    $badgeStyle = 'background-color: #ffc107; color: black;'; // Default: warning (pending)
                                                    
                                                    if ($statusLower === 'assigned' || $statusLower === 'approved') {
                                                        $badgeStyle = 'background-color: #17a2b8; color: white;'; // info
                                                    } elseif ($statusLower === 'on_delivery') {
                                                        $badgeStyle = 'background-color: #007bff; color: white;'; // primary
                                                    } elseif ($statusLower === 'delivered' || $statusLower === 'completed') {
                                                        $badgeStyle = 'background-color: #28a745; color: white;'; // success
                                                    } elseif ($statusLower === 'cancelled') {
                                                        $badgeStyle = 'background-color: #dc3545; color: white;'; // danger
                                                    }
                                                    
                                                    $statusDisplay = ucfirst(str_replace('_', ' ', $deliveryStatus));
                                                @endphp
                                                <span class="badge" style="{{ $badgeStyle }}">{{ $statusDisplay }}</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Payment Summary -->
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header" style="background: #e6f4ea;">
                                    <h5>Payment Summary</h5>
                                </div>
                                <div class="card-body">
                                    @php
                                        // Calculate subtotal from products and custom bouquets
                                        $productsSubtotal = $invoice->order->products->sum(function($product) {
                                            return $product->pivot->quantity * $product->price;
                                        });
                                        
                                        $customBouquetsSubtotal = $invoice->order->customBouquets->sum(function($bouquet) {
                                            $unitPrice = $bouquet->unit_price ?? ($bouquet->total_price / max($bouquet->pivot->quantity, 1));
                                            return $unitPrice * $bouquet->pivot->quantity;
                                        });
                                        
                                        $calculatedSubtotal = $productsSubtotal + $customBouquetsSubtotal;
                                        $shippingFee = $invoice->order->delivery->shipping_fee ?? $invoice->shipping_fee ?? 0;
                                        
                                        // If shipping_fee is 0 or null, calculate it from the difference between total_price and subtotal
                                        if ($shippingFee == 0 && $invoice->order->total_price > $calculatedSubtotal) {
                                            $shippingFee = $invoice->order->total_price - $calculatedSubtotal;
                                        }
                                        
                                        $calculatedTotal = $calculatedSubtotal + $shippingFee;
                                    @endphp
                                    <div class="row">
                                        <div class="col-6">
                                            <p><strong>Subtotal:</strong></p>
                                        </div>
                                        <div class="col-6 text-right">
                                            <p>₱{{ number_format($calculatedSubtotal, 2) }}</p>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <p><strong>Shipping Fee:</strong></p>
                                        </div>
                                        <div class="col-6 text-right">
                                            <p>₱{{ number_format($shippingFee, 2) }}</p>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-6">
                                            <p><strong>Total Amount:</strong></p>
                                        </div>
                                        <div class="col-6 text-right">
                                            <p class="text-success font-weight-bold">₱{{ number_format($calculatedTotal, 2) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment History -->
                            @if($invoice->payments->count() > 0)
                            <div class="card mt-3">
                                <div class="card-header" style="background: #e6f4ea;">
                                    <h5>Payment History</h5>
                                </div>
                                <div class="card-body">
                                    @foreach($invoice->payments as $payment)
                                    @php
                                        // Calculate correct total from order if payment amount seems wrong
                                        $productsSubtotal = $invoice->order->products->sum(function($product) {
                                            return $product->pivot->quantity * $product->price;
                                        });
                                        
                                        $customBouquetsSubtotal = $invoice->order->customBouquets->sum(function($bouquet) {
                                            $unitPrice = $bouquet->unit_price ?? ($bouquet->total_price / max($bouquet->pivot->quantity, 1));
                                            return $unitPrice * $bouquet->pivot->quantity;
                                        });
                                        
                                        $calculatedSubtotal = $productsSubtotal + $customBouquetsSubtotal;
                                        $shippingFee = $invoice->order->delivery->shipping_fee ?? $invoice->shipping_fee ?? 0;
                                        
                                        if ($shippingFee == 0 && $invoice->order->total_price > $calculatedSubtotal) {
                                            $shippingFee = $invoice->order->total_price - $calculatedSubtotal;
                                        }
                                        
                                        $correctTotalAmount = $calculatedSubtotal + $shippingFee;
                                        // Use correct total if payment amount is significantly different (likely wrong)
                                        $displayPaymentAmount = abs($payment->amount - $correctTotalAmount) > 0.01 ? $correctTotalAmount : $payment->amount;
                                    @endphp
                                    <div class="border-bottom pb-2 mb-2">
                                        <div class="d-flex justify-content-between">
                                            <span><strong>{{ ucfirst($payment->mode_of_payment) }}</strong></span>
                                            <span class="text-success">₱{{ number_format($displayPaymentAmount, 2) }}</span>
                                        </div>
                                        <small class="text-muted">
                                            {{ $payment->payment_date->format('M d, Y') }}
                                            @if($payment->memo)
                                                - {{ $payment->memo }}
                                            @endif
                                            @if($payment->processedBy)
                                                <br>Processed by: {{ $payment->processedBy->name }}
                                            @endif
                                        </small>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert Success Message -->
@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 3000
            });
        });
    </script>
@endif

@if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '{{ session('error') }}',
                showConfirmButton: false,
                timer: 3000
            });
        });
    </script>
@endif

@endsection
