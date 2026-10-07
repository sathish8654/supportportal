@extends('layouts.app')
@section('content')
@if (Auth::check() && Auth::user()->hasVerifiedEmail() && Auth::user()->role_id === 2)
<div class="container-fluid">
    <div class="row justify-content-center mt-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-white pb-0">
                    <ul class="nav nav-tabs card-header-tabs" id="dashboardTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="service-tab" data-toggle="custom-tab" data-bs-target="#service-content" type="button" role="tab" aria-controls="service-content" aria-selected="true">{{ __('Service') }}</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="sales-tab" data-toggle="custom-tab" data-bs-target="#sales-content" type="button" role="tab" aria-controls="sales-content" aria-selected="false">{{ __('Sales') }}</button>
                        </li>
                    </ul>
                    <script>
                        // Immediate inline tab initialization to bypass any page load script blocks
                        (function() {
                            function initTabs() {
                                var buttons = document.querySelectorAll('#dashboardTabs button[data-toggle="custom-tab"]');
                                
                                // Restore active tab from localStorage if exists
                                var savedTab = localStorage.getItem('activeDashboardTab');
                                if (savedTab) {
                                    var activeBtn = document.querySelector('#dashboardTabs button[data-bs-target="' + savedTab + '"]');
                                    if (activeBtn) {
                                        buttons.forEach(function(b) { b.classList.remove('active'); });
                                        activeBtn.classList.add('active');

                                        var panes = document.querySelectorAll('#dashboardTabsContent .tab-pane');
                                        panes.forEach(function(pane) {
                                            pane.classList.remove('show', 'active');
                                        });
                                        var targetPane = document.querySelector(savedTab);
                                        if (targetPane) {
                                            targetPane.classList.add('show', 'active');
                                        }
                                    }
                                }

                                buttons.forEach(function(btn) {
                                    btn.onclick = function(e) {
                                        e.preventDefault();
                                        buttons.forEach(function(b) { b.classList.remove('active'); });
                                        btn.classList.add('active');

                                        var targetId = btn.getAttribute('data-bs-target');
                                        
                                        // Save active tab to localStorage
                                        localStorage.setItem('activeDashboardTab', targetId);

                                        var panes = document.querySelectorAll('#dashboardTabsContent .tab-pane');
                                        panes.forEach(function(pane) {
                                            pane.classList.remove('show', 'active');
                                        });
                                        var targetPane = document.querySelector(targetId);
                                        if (targetPane) {
                                            targetPane.classList.add('show', 'active');
                                        }
                                    };
                                });
                            }
                            // Execute immediately and also on DOMContentLoaded to be absolutely sure
                            initTabs();
                            document.addEventListener("DOMContentLoaded", initTabs);
                        })();
                    </script>
                </div>

                <div class="card-body">
                    <div class="tab-content" id="dashboardTabsContent">
                        <!-- Service Tab -->
                        <div class="tab-pane active" id="service-content" role="tabpanel" aria-labelledby="service-tab">
                            @if (Auth::user()->permissions->first())
                                @include('modals/barTicketStatus')
                                </div> <!-- Closes unclosed container from barTicketStatus -->
                            @else
                                <style>
                                @keyframes blink {
                                    0% { opacity: 1; }
                                    50% { opacity: 0; }
                                    100% { opacity: 1; }
                                }
                                .blinking-text {
                                    text-align: center;
                                    font-size: 24px;
                                    color: green;
                                    animation: blink 1s infinite;
                                }
                                </style>
                                <div class="container">
                                    <div class="row">
                                        <div class="col-lg-1"></div>
                                        <div class="col-lg-10">
                                            <h1 style="text-align:center;"> Hello {{ Auth::user()->name }}</h1>
                                            <div class="blinking-text">
                                                Menus are not enabled for your account, please contact Administrator!
                                            </div>
                                        </div>
                                        <div class="col-lg-1"></div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Sales Tab -->
                        <div class="tab-pane" id="sales-content" role="tabpanel" aria-labelledby="sales-tab">
                            <div class="row mb-4 pt-3">
                                <div class="col-12">
                                    <h3 class="fw-bold text-dark mb-1">Sales Dashboard</h3>
                                    <p class="text-muted">Overview of your sales operations</p>
                                </div>
                            </div>

                            <div class="row g-4 mb-4">
                                <div class="col-sm-6 col-lg-3">
                                    <a href="{{ route('leads.index') }}" class="text-decoration-none">
                                        <div class="card border-0 shadow-sm p-3" style="background-color: rgba(13, 110, 253, 0.1);">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <h6 class="text-muted mb-1">Total Leads</h6>
                                                    <h2 class="mb-0 text-primary">{{ $salesTotalLeads ?? 0 }}</h2>
                                                </div>
                                                <div class="text-primary" style="font-size: 2.5rem;"><i class="fa fa-users"></i></div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-sm-6 col-lg-3">
                                    <a href="{{ route('orders.index') }}" class="text-decoration-none">
                                        <div class="card border-0 shadow-sm p-3" style="background-color: rgba(25, 135, 84, 0.1);">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <h6 class="text-muted mb-1">Total Orders</h6>
                                                    <h2 class="mb-0 text-success">{{ $salesTotalOrders ?? 0 }}</h2>
                                                </div>
                                                <div class="text-success" style="font-size: 2.5rem;"><i class="fa fa-shopping-cart"></i></div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-sm-6 col-lg-3">
                                    <a href="{{ route('installations.index') }}" class="text-decoration-none">
                                        <div class="card border-0 shadow-sm p-3" style="background-color: rgba(13, 202, 240, 0.1);">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <h6 class="text-muted mb-1">Installations</h6>
                                                    <h2 class="mb-0 text-info">{{ $salesTotalInstallations ?? 0 }}</h2>
                                                </div>
                                                <div class="text-info" style="font-size: 2.5rem;"><i class="fa fa-wrench"></i></div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-sm-6 col-lg-3">
                                    <a href="{{ route('allLogTickets') }}" class="text-decoration-none">
                                        <div class="card border-0 shadow-sm p-3" style="background-color: rgba(255, 193, 7, 0.1);">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <h6 class="text-muted mb-1">Service Tickets</h6>
                                                    <h2 class="mb-0 text-warning">{{ $serviceTicketsCount ?? 0 }}</h2>
                                                </div>
                                                <div class="text-warning" style="font-size: 2.5rem;"><i class="fa fa-ticket"></i></div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <div class="row g-4">
                                <div class="col-lg-8">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0">Recent Leads</h5>
                                            <a href="{{ route('leads.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                                        </div>
                                        <div class="card-body p-0">
                                            <div style="max-height: 300px; overflow-y: auto;">
                                                <table class="table table-hover mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Customer</th>
                                                            <th>Status</th>
                                                            <th>Source</th>
                                                            <th>Date</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($recentLeads ?? [] as $lead)
                                                        <tr>
                                                            <td class="fw-medium">{{ $lead->vendor->companyname ?? $lead->vendor->name ?? '-' }}</td>
                                                            <td><span class="badge bg-primary">{{ ucfirst($lead->status ?? 'new') }}</span></td>
                                                            <td>{{ $lead->lead_source ?? '-' }}</td>
                                                            <td><small>{{ $lead->created_at ? $lead->created_at->format('d M Y') : '-' }}</small></td>
                                                        </tr>
                                                        @empty
                                                        <tr>
                                                            <td colspan="4" class="text-center py-3 text-muted">No leads created yet. Click "+ Add Lead" to get started.</td>
                                                        </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-lg-4">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-white border-bottom">
                                            <h5 class="mb-0">Quick Actions</h5>
                                        </div>
                                        <div class="list-group list-group-flush">
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-dark"><i class="fa fa-user me-2"></i>Customers</span>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addNewAddressModal">Add</button>
                                                    <a href="{{ route('vendor') }}" class="btn btn-outline-secondary btn-sm">Show</a>
                                                </div>
                                            </div>
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-dark"><i class="fa fa-users me-2"></i>Leads</span>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#manageLeadModal">Add</button>
                                                    <a href="{{ route('leads.index') }}" class="btn btn-outline-secondary btn-sm">Show</a>
                                                </div>
                                            </div>
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-dark"><i class="fa fa-file me-2"></i>Quotations</span>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('quotations.index') }}" class="btn btn-info btn-sm">Add</a>
                                                    <a href="{{ route('quotations.index') }}" class="btn btn-outline-secondary btn-sm">Show</a>
                                                </div>
                                            </div>
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-dark"><i class="fa fa-shopping-cart me-2"></i>Orders</span>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('orders.index') }}" class="btn btn-warning btn-sm">Add</a>
                                                    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary btn-sm">Show</a>
                                                </div>
                                            </div>
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <span class="text-dark"><i class="fa fa-wrench me-2"></i>Installations</span>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('installations.index') }}" class="btn btn-secondary btn-sm">Add</a>
                                                    <a href="{{ route('installations.index') }}" class="btn btn-outline-secondary btn-sm">Show</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @include('modals/manageLead')
                            @include('modals/addCustomerAddress')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="d-flex justify-content-center align-items-center vh-100">
    <img src="/avatars/permission.png">
</div>
@endif
@endsection