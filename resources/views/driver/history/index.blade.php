@extends('layouts.driver_mobile')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-bold">Delivery History</h4>
    <div class="d-flex gap-2">
        <span class="badge bg-success">{{ $completedDeliveries->count() }} completed</span>
        <span class="badge bg-warning">{{ $returnedOrders->count() }} returned</span>
    </div>
</div>

@if($completedDeliveries->isEmpty())
<div class="text-center py-5">
    <i class="bi bi-check-circle display-1 text-muted"></i>
    <h5 class="mt-3 text-muted">No completed deliveries yet</h5>
    <p class="text-muted">Your completed deliveries will appear here.</p>
</div>
@else
<div class="row">
    @foreach($completedDeliveries as $delivery)
    <div class="col-12 mb-3">
        <div class="card shadow-sm border-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="card-title mb-0">Order #{{ $delivery->order->id }}</h6>
                    <span class="badge bg-success">
                        <i class="bi bi-check-circle me-1"></i>Completed
                    </span>
                </div>
                
                <div class="row mb-2">
                    <div class="col-6">
                        <small class="text-muted">Customer:</small><br>
                        <strong>{{ $delivery->order->user->name }}</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Completed:</small><br>
                        <strong>{{ $delivery->updated_at->format('M d, Y g:i A') }}</strong>
                    </div>
                </div>
                
                <div class="mb-2">
                    <small class="text-muted">Delivery Address:</small><br>
                    <strong>{{ $delivery->delivery_address ?? 'Address not specified' }}</strong>
                </div>
                
                <div class="row mb-3">
                    <div class="col-6">
                        <small class="text-muted">Scheduled Date:</small><br>
                        <strong>{{ \Carbon\Carbon::parse($delivery->delivery_date)->format('M d, Y') }}</strong>
                    </div>
                    <div class="col-6">
                        <small class="text-muted">Scheduled Time:</small><br>
                        <strong>{{ $delivery->delivery_time ?? 'Not specified' }}</strong>
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    <a href="{{ route('driver.history.show', $delivery->id) }}" class="btn btn-outline-primary btn-sm flex-fill">
                        <i class="bi bi-eye me-1"></i>View Details
                    </a>
                    <button class="btn btn-outline-secondary btn-sm" onclick="showDeliveryNotes({{ $delivery->id }})">
                        <i class="bi bi-chat me-1"></i>Notes
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Pagination -->
@if($completedDeliveries->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $completedDeliveries->links() }}
</div>
@endif
@endif

<!-- Returned Orders Section -->
@if($returnedOrders->isNotEmpty())
<div class="mt-5">
    <h5 class="fw-bold text-warning mb-3">
        <i class="bi bi-arrow-return-left me-2"></i>Returned Orders
    </h5>
    <div class="row">
        @foreach($returnedOrders as $order)
        <div class="col-12 mb-3">
            <div class="card shadow-sm border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="card-title mb-0">Order #{{ $order->id }}</h6>
                        <span class="badge" style="background-color: #ffc107; color: #000; font-weight: 600;">
                            <i class="bi bi-arrow-return-left me-1"></i>Returned
                        </span>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-6">
                            <small class="text-muted">Customer:</small><br>
                            <strong>{{ $order->user->name }}</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Contact:</small><br>
                            <strong>{{ $order->user->contact_number ?? 'N/A' }}</strong>
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <small class="text-muted">Delivery Address:</small><br>
                        <strong>{{ $order->delivery->delivery_address ?? 'N/A' }}</strong>
                    </div>
                    
                    <div class="row mb-2">
                        <div class="col-6">
                            <small class="text-muted">Total Amount:</small><br>
                            <strong>₱{{ number_format($order->total_price, 2) }}</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Return Date:</small><br>
                            <strong>{{ $order->returned_at ? $order->returned_at->format('M d, Y H:i') : 'N/A' }}</strong>
                        </div>
                    </div>
                    
                    @if($order->return_reason)
                    <div class="mb-2">
                        <small class="text-muted">Return Reason:</small><br>
                        <strong class="text-warning">{{ $order->return_reason }}</strong>
                    </div>
                    @endif
                    
                    <div class="d-flex gap-2">
                        <a href="{{ route('driver.orders.show', $order->id) }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-eye me-1"></i>View Details
                        </a>
                        <button class="btn btn-outline-secondary btn-sm" onclick="showReturnNotes({{ $order->id }})">
                            <i class="bi bi-sticky me-1"></i>Notes
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <!-- Pagination for returned orders -->
    @if($returnedOrders->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $returnedOrders->links() }}
    </div>
    @endif
</div>
@endif

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
@endsection 