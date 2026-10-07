@extends('layouts.app')
@section('title', 'Leads')

@section('content')
@include('modals/manageLead')
@include('modals/addCustomerAddress')

<div class="container-fluid mt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Leads</h4>
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#manageLeadModal">
            <i class="fa fa-plus me-1"></i> Add Lead
        </button>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Stage</th>
                            <th>Source</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leads as $lead)
                        <tr>
                            <td>{{ $lead->vendor->companyname ?? $lead->vendor->name ?? $lead->customer->name ?? '-' }}</td>
                            <td><span class="badge bg-{{ $lead->status == 'confirmed' ? 'success' : ($lead->status == 'rejected' ? 'danger' : 'primary') }}">{{ $lead->status ?? 'new' }}</span></td>
                            <td><small>{{ ucfirst(str_replace('_', ' ', $lead->workflow_stage)) }}</small></td>
                            <td><small>{{ $lead->lead_source ?? '-' }}</small></td>
                            <td><small>{{ $lead->created_at->format('d M Y') }}</small></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-sm btn-outline-warning edit-lead-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#manageLeadModal"
                                            data-id="{{ $lead->id }}"
                                            data-customer-id="{{ $lead->vendor_id }}"
                                            data-customer-name="{{ $lead->vendor->name ?? ($lead->customer->name ?? '') }}"
                                            data-customer-company="{{ $lead->vendor->companyname ?? '' }}"
                                            data-customer-mobile1="{{ $lead->vendor->mobile1 ?? '' }}"
                                            data-customer-mobile2="{{ $lead->vendor->mobile2 ?? '' }}"
                                            data-source="{{ $lead->lead_source }}"
                                            data-products="{{ $lead->interested_products }}"
                                            data-value="{{ $lead->estimated_value }}"
                                            data-status="{{ $lead->status }}"
                                            data-stage="{{ $lead->workflow_stage }}"
                                            data-notes="{{ $lead->notes }}">
                                        Edit
                                    </button>
                                    <a href="{{ route('leads.show', $lead->id) }}" class="btn btn-sm btn-outline-info">View</a>
                                    
                                    @if($lead->workflow_stage == 'sales_executive')
                                    <form action="{{ route('leads.destroy', $lead->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Request delete for Sales Head approval?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                    @endif
                                    
                                    @if($lead->delete_requested)
                                    <form action="{{ route('leads.destroy', $lead->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Approve delete request?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Approve Delete</button>
                                    </form>
                                    @endif
                                    
                                    @if($lead->workflow_stage == 'sales_executive')
                                    <form action="{{ route('lead-workflow.approve-to-accounts', $lead->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Approve lead to Accounts?')">
                                        @csrf @method('PUT')
                                        <button class="btn btn-sm btn-success">Approve</button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted p-3">No leads found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="mt-3">
        {{ $leads->links() }}
    </div>
</div>
@endsection
