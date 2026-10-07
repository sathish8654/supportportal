@extends('layouts.app')
@section('title', 'Orders')

@section('content')
@include('modals/manageOrder')

<div class="container-fluid mt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Sales Orders</h4>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#manageOrderModal">
                <i class="fa fa-plus me-1"></i> Create Sales Order
            </button>
            <ul class="nav nav-tabs" role="tablist" id="ordersTabList">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#list" type="button" role="tab">Show All</button></li>
            </ul>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="tab-content mt-3">
        <!-- Create Order -->
        <div class="tab-pane fade show active" id="create" role="tabpanel">
            <div class="card shadow-sm border-0" style="max-width: 800px;">
                <div class="card-header bg-white">Create Order</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('orders.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Approved Quotation *</label>
                            <select name="quotation_id" class="form-select" required>
                                <option value="">Select Approved Quotation</option>
                                @foreach($quotations as $quotation)
                                <option value="{{ $quotation->id }}">Quotation #{{ $quotation->id }} - {{ $quotation->lead->customer->name ?? '-' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Create Order</button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Orders List -->
        <div class="tab-pane fade" id="list" role="tabpanel">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">Order List</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        @if($orders->count() > 0)
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Production</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                <tr>
                                    <td>{{ $order->quotation->lead->customer->name ?? '-' }}</td>
                                    <td>₹{{ number_format($order->quotation->total_amount ?? 0, 2) }}</td>
                                    <td><span class="badge bg-warning">{{ $order->status }}</span></td>
                                    <td>
                                        @if($order->workOrders && $order->workOrders->count() > 0)
                                            @php $completed = $order->workOrders->where('status', 'completed')->count(); @endphp
                                            <small>{{ $completed }}/{{ $order->workOrders->count() }} items done</small>
                                        @else
                                            <small class="text-muted">Not started</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($order->status == 'verified')
                                        <form action="{{ route('orders.submit-production', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Send to Production?')">
                                            @csrf @method('PUT')
                                            <button class="btn btn-sm btn-primary">Submit to Production</button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <p class="p-3 mb-0 text-muted">No orders found.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Handle URL hash routing to activate the correct tab
    document.addEventListener("DOMContentLoaded", function() {
        var hash = window.location.hash;
        if (hash) {
            var tabTrigger = document.querySelector('#ordersTabList button[data-bs-target="' + hash + '"]');
            if (tabTrigger) {
                var tab = new bootstrap.Tab(tabTrigger);
                tab.show();
            }
        }
    });
</script>
@endsection
