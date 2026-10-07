@extends('layouts.app')
@section('title', 'Edit Lead')

@section('content')
<div class="container-fluid mt-3" style="max-width: 800px;">
    <h1 class="mb-4">Edit Lead</h1>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form method="POST" action="{{ route('leads.update', $lead->id) }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label fw-bold">Customer / Vendor</label>
                    <div class="p-3 border rounded bg-light" style="border-left: 4px solid #0d6efd !important;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fw-bold text-primary fs-5">{{ $lead->vendor->name ?? '-' }}</span>
                            @if($lead->vendor && $lead->vendor->companyname)
                            <span class="badge bg-secondary text-white fs-6">{{ $lead->vendor->companyname }}</span>
                            @endif
                        </div>
                        <div class="text-muted">
                            @if($lead->vendor && $lead->vendor->mobile1)
                            <span class="me-3"><i class="fa fa-phone me-1"></i> {{ $lead->vendor->mobile1 }}</span>
                            @endif
                            @if($lead->vendor && $lead->vendor->mobile2)
                            <span><i class="fa fa-phone-square me-1"></i> {{ $lead->vendor->mobile2 }}</span>
                            @endif
                        </div>
                    </div>
                    <input type="hidden" name="customer_id" value="{{ $lead->vendor_id }}">
                </div>
                <div class="mb-3">
                    <label for="lead_source" class="form-label">Lead Source</label>
                    <input type="text" name="lead_source" id="lead_source" value="{{ $lead->lead_source }}" class="form-control" readonly>
                </div>
                <div class="mb-3">
                    <label for="interested_products" class="form-label">Interested Products</label>
                    <textarea name="interested_products" id="interested_products" class="form-control" rows="3">{{ $lead->interested_products }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="estimated_value" class="form-label">Estimated Value (₹)</label>
                    <input type="number" step="0.01" name="estimated_value" id="estimated_value" value="{{ $lead->estimated_value }}" class="form-control" placeholder="e.g. 50000">
                </div>
                <div class="mb-3">
                    <label for="notes" class="form-label">Internal Notes / Requirements</label>
                    <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Additional requirements or meeting notes...">{{ $lead->notes }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" disabled class="form-select">
                        <option value="new" {{ $lead->status == 'new' ? 'selected' : '' }}>New</option>
                        <option value="approved" {{ $lead->status == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="hold" {{ $lead->status == 'hold' ? 'selected' : '' }}>Hold</option>
                        <option value="rejected" {{ $lead->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="confirmed" {{ $lead->status == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    </select>
                    <input type="hidden" name="status" value="{{ $lead->status }}">
                </div>
                <div class="mb-3">
                    <label for="workflow_stage" class="form-label">Workflow Stage</label>
                    <select name="workflow_stage" id="workflow_stage" disabled class="form-select">
                        <option value="sales_executive" {{ $lead->workflow_stage == 'sales_executive' ? 'selected' : '' }}>Sales Executive</option>
                        <option value="sales_head_review" {{ $lead->workflow_stage == 'sales_head_review' ? 'selected' : '' }}>Sales Head Review</option>
                        <option value="accounts" {{ $lead->workflow_stage == 'accounts' ? 'selected' : '' }}>Accounts Review</option>
                        <option value="md_approval" {{ $lead->workflow_stage == 'md_approval' ? 'selected' : '' }}>MD Approval</option>
                        <option value="completed" {{ $lead->workflow_stage == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                    <input type="hidden" name="workflow_stage" value="{{ $lead->workflow_stage }}">
                </div>
                <button type="submit" class="btn btn-primary">Update Lead</button>
                <a href="{{ route('leads.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#customer_id').select2({
            width: '100%'
        });
    });
</script>
@endsection
