@extends('layouts.clerk_app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            
            <!-- Order Stats -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <a href="{{ route('clerk.orders.index', ['status' => 'pending']) }}" class="text-decoration-none">
                        <div class="card text-center h-100 pending-card">
                            <div class="card-body">
                                <h3 class="text-warning">{{ $pendingOrdersCount ?? 0 }}</h3>
                                <p class="mb-0">Pending Orders</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="{{ route('clerk.orders.index', ['status' => 'approved']) }}" class="text-decoration-none">
                        <div class="card text-center h-100 approved-card">
                            <div class="card-body">
                                <h3 class="text-info">{{ $approvedOrdersCount ?? 0 }}</h3>
                                <p class="mb-0">Approved Orders</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="{{ route('clerk.orders.index', ['status' => 'on_delivery']) }}" class="text-decoration-none">
                        <div class="card text-center h-100 delivery-card">
                            <div class="card-body">
                                <h3 class="text-primary">{{ $onDeliveryCount ?? 0 }}</h3>
                                <p class="mb-0">On Delivery</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 mb-3">
                    <a href="{{ route('clerk.orders.index', ['status' => 'completed', 'today' => 1]) }}" class="text-decoration-none">
                        <div class="card text-center h-100 completed-card">
                            <div class="card-body">
                                <h3 class="text-success">{{ $completedTodayCount ?? 0 }}</h3>
                                <p class="mb-0">Completed Today</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            
            <!-- Restock Alert -->
            <div class="row">
                <div class="col-md-6 mb-4">
                    <a href="{{ route('clerk.inventory.manage') }}" class="text-decoration-none">
                        <div class="card h-100 restock-alert-card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-exclamation-triangle me-2"></i>Restock Alerts
                                </h5>
                            </div>
                            <div class="card-body">
                                @if(isset($restockProducts) && count($restockProducts))
                                    @foreach($restockProducts as $product)
                                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                                            <span>{{ $product->name }}</span>
                                            <span class="badge bg-danger">{{ $product->stock }} left</span>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-muted mb-0">All products are well stocked</p>
                                @endif
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@push('styles')
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
@endpush
@endsection
