@extends('layouts.app')
@section('title', 'Quotations')

@section('content')
@include('modals/manageQuotation')

<div class="container-fluid mt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Quotations</h4>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#manageQuotationModal">
                <i class="fa fa-plus me-1"></i> Create Quotation
            </button>
            <ul class="nav nav-tabs" role="tablist" id="quotationsTabList">
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#add" type="button" role="tab">Add Form</button></li>
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#list" type="button" role="tab">Show All</button></li>
            </ul>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="tab-content mt-3">
        <!-- Add Quotation -->
        <div class="tab-pane fade" id="add" role="tabpanel">
            <div class="card shadow-sm border-0" style="max-width: 800px;">
                <div class="card-header bg-white">Create Quotation (Accounts)</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('quotations.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Lead *</label>
                            <select name="lead_id" class="form-select" required>
                                <option value="">Select Lead</option>
                                @foreach($leads as $lead)
                                <option value="{{ $lead->id }}">{{ $lead->vendor->companyname ?? $lead->vendor->name ?? 'Lead #' . $lead->id }} - {{ $lead->status }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <h6 class="mt-4 border-bottom pb-2">Products</h6>
                        <div class="row g-2">
                            @foreach($categories->where('parent_id', '>', 0) as $product)
                            @php
                                $displayName = ($product->parent->name ?? '-') . ' / ' . $product->name;
                                if (!empty($product->model_number)) {
                                    $displayName .= ' - ' . $product->model_number;
                                }
                            @endphp
                            <div class="col-md-6">
                                <div class="input-group input-group-sm mb-2">
                                    <span class="input-group-text" style="width: 200px; font-size: 0.75rem;">{{ $displayName }}</span>
                                    <input type="number" name="items[{{ $loop->index }}][quantity]" class="form-control" placeholder="Qty" min="0">
                                    <input type="hidden" name="items[{{ $loop->index }}][product_id]" value="{{ $product->id }}">
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <button type="submit" class="btn btn-primary mt-3">Create Quotation</button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Quotations List -->
        <div class="tab-pane fade show active" id="list" role="tabpanel">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">Quotation List</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        @if($quotations->count() > 0)
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Workflow Stage</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($quotations as $quotation)
                                <tr>
                                    <td>{{ $quotation->vendor->companyname ?? $quotation->vendor->name ?? $quotation->lead->vendor->companyname ?? $quotation->lead->vendor->name ?? '-' }}</td>
                                    <td>₹{{ number_format($quotation->total_amount, 2) }}</td>
                                    <td><span class="badge bg-{{ $quotation->status == 'approved' ? 'success' : ($quotation->status == 'rejected' ? 'danger' : 'secondary') }}">{{ $quotation->status }}</span></td>
                                    <td><small>{{ ucfirst(str_replace('_', ' ', $quotation->lead->workflow_stage ?? 'pending')) }}</small></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            @if($quotation->status == 'draft')
                                            <button type="button" class="btn btn-sm btn-outline-warning edit-quotation-btn"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#manageQuotationModal"
                                                    data-id="{{ $quotation->id }}"
                                                    data-lead-id="{{ $quotation->lead_id }}"
                                                    data-status="{{ $quotation->status }}"
                                                    data-subtotal="{{ $quotation->subtotal }}"
                                                    data-gst-amount="{{ $quotation->gst_amount }}"
                                                    data-transport-charges="{{ $quotation->transport_charges }}"
                                                    data-installation-charges="{{ $quotation->installation_charges }}"
                                                    data-total-amount="{{ $quotation->total_amount }}"
                                                    data-items="{{ json_encode($quotation->items) }}">
                                                Edit
                                            </button>
                                            @endif

                                             @if(in_array($quotation->status, ['submitted_to_head', 'pending']))
                                             <form action="{{ route('quotation-workflow.approve', $quotation->id) }}" method="POST" class="d-inline">
                                                 @csrf @method('PUT')
                                                 <button class="btn btn-sm btn-success">Approve</button>
                                             </form>
                                             @endif
                                             
                                             @if(in_array($quotation->status, ['approved', 'approved_by_head', 'approved_by_accounts', 'approved_by_md']))
                                             <form action="{{ route('quotation-workflow.customer-confirmed', $quotation->id) }}" method="POST" class="d-inline">
                                                 @csrf @method('PUT')
                                                 <button class="btn btn-sm btn-primary">Customer Confirmed</button>
                                             </form>
                                             @endif
                                            
                                            @if(!in_array($quotation->status, ['approved_by_head', 'approved_by_accounts', 'approved_by_md', 'accepted_by_customer']))
                                            <form action="{{ route('quotations.destroy', $quotation->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this quotation?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                            </form>
                                            @endif
                                            
                                            <a href="{{ route('quotations.print', $quotation->id) }}" target="_blank" class="btn btn-sm btn-outline-info">Print</a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <p class="p-3 mb-0 text-muted">No quotations found.</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="mt-3">
                {{ $quotations->links() }}
            </div>
        </div>
    </div>
</div>

<script>
    // Handle URL hash routing to activate the correct tab (e.g. #add or #list)
    document.addEventListener("DOMContentLoaded", function() {
        var hash = window.location.hash;
        if (hash) {
            var tabTrigger = document.querySelector('#quotationsTabList button[data-bs-target="' + hash + '"]');
            if (tabTrigger) {
                var tab = new bootstrap.Tab(tabTrigger);
                tab.show();
            }
        }
    });
</script>
@endsection
