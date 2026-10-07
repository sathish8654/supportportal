@extends('layouts.app')
@section('title', 'Lead Workflow')

@section('content')
<div class="container-fluid mt-3">
    <div class="card shadow-sm border-0 mb-4" style="max-width: 1000px; margin: 0 auto;">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Lead Workflow: {{ $lead->customer->name ?? 'Unknown' }}</h5>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6>Lead Details</h6>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th style="width: 120px;">Customer</th>
                            <td>: {{ $lead->customer->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Source</th>
                            <td>: {{ $lead->lead_source ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Current Stage</th>
                            <td>: <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $lead->workflow_stage)) }}</span></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>: <span class="badge bg-primary">{{ $lead->status }}</span></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <h6 class="border-bottom pb-2 mb-3">Workflow History</h6>
            @if($lead->activities && $lead->activities->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Stage Change</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lead->activities->sortByDesc('created_at') as $activity)
                        <tr>
                            <td>{{ $activity->created_at->format('d M Y H:i') }}</td>
                            <td>{{ $activity->user->name ?? 'System' }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $activity->action)) }}</td>
                            <td>
                                @if($activity->stage_from || $activity->stage_to)
                                    {{ $activity->stage_from ? ucfirst(str_replace('_', ' ', $activity->stage_from)) : '-' }} &rarr; {{ $activity->stage_to ? ucfirst(str_replace('_', ' ', $activity->stage_to)) : '-' }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $activity->remarks ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-muted">No workflow history found.</p>
            @endif
            
            @if($lead->quotations && $lead->quotations->count() > 0)
            <h6 class="border-bottom pb-2 mb-3 mt-4">Quotations</h6>
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Quotation #</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lead->quotations as $quotation)
                        <tr>
                            <td>{{ $quotation->id }}</td>
                            <td>₹{{ number_format($quotation->total_amount, 2) }}</td>
                            <td><span class="badge bg-{{ $quotation->status == 'approved' ? 'success' : ($quotation->status == 'rejected' ? 'danger' : 'secondary') }}">{{ $quotation->status }}</span></td>
                            <td>{{ $quotation->created_at->format('d M Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            <div class="mt-4">
                <a href="{{ route('leads.index') }}" class="btn btn-secondary">Back to Leads</a>
            </div>
        </div>
    </div>
</div>
@endsection
