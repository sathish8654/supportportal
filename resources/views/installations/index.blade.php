@extends('layouts.app')
@section('title', 'Installations')

@section('content')
@include('modals/manageInstallation')

<div class="container-fluid mt-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Installations & Field Support</h4>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#manageInstallationModal">
                <i class="fa fa-wrench me-1"></i> Assign Field Installation
            </button>
            <ul class="nav nav-tabs" role="tablist" id="installationsTabList">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#list" type="button" role="tab">All Tasks</button></li>
            </ul>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="tab-content mt-3">
        <!-- Installations List -->
        <div class="tab-pane fade show active" id="list" role="tabpanel">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">Installations List</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Work Order</th>
                                    <th>Customer</th>
                                    <th>Engineer</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($installations as $installation)
                                <tr>
                                    <td>{{ $installation->workOrder->wo_number ?? '-' }}</td>
                                    <td>{{ $installation->workOrder->order->quotation->lead->customer->name ?? '-' }}</td>
                                    <td>{{ $installation->serviceEngineer->name ?? '-' }}</td>
                                    <td><span class="badge bg-{{ $installation->status == 'completed' ? 'success' : 'info' }}">{{ $installation->status }}</span></td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            @if($installation->status != 'completed')
                                            <form action="{{ route('installations.update-status', $installation->id) }}" method="POST" class="d-inline">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="status" value="completed">
                                                <button class="btn btn-sm btn-success">Complete</button>
                                            </form>
                                            @endif
                                            
                                            @if($installation->status != 'completed')
                                            <form action="{{ route('installations.assign', $installation->id) }}" method="POST" class="d-inline">
                                                @csrf @method('PUT')
                                                <button class="btn btn-sm btn-warning">Reassign</button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center text-muted p-3">No installations found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                {{ $installations->links() }}
            </div>
        </div>
        
        <!-- Assign Installation -->
        <div class="tab-pane fade" id="assign" role="tabpanel">
            <div class="card shadow-sm border-0" style="max-width: 800px;">
                <div class="card-header bg-white">Assign Installation to Engineer</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('installations.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Work Order *</label>
                            <select name="work_order_id" class="form-select" required>
                                <option value="">Select Work Order</option>
                                @foreach($workOrders as $wo)
                                <option value="{{ $wo->id }}">WO-{{ $wo->wo_number }} - {{ $wo->order->quotation->lead->customer->name ?? '-' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Service Engineer *</label>
                            <select name="service_engineer_id" class="form-select" required>
                                <option value="">Select Engineer</option>
                                @foreach($engineers as $eng)
                                <option value="{{ $eng->id }}">{{ $eng->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Assign Installation</button>
                    </form>
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
            var tabTrigger = document.querySelector('#installationsTabList button[data-bs-target="' + hash + '"]');
            if (tabTrigger) {
                var tab = new bootstrap.Tab(tabTrigger);
                tab.show();
            }
        }
    });
</script>
@endsection
