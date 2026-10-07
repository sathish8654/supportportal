<!-- Manage Installation Modal -->
<div class="modal fade" id="manageInstallationModal" tabindex="-1" aria-labelledby="manageInstallationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white py-2 px-3">
                <h5 class="modal-title" id="manageInstallationModalLabel"><i class="fa fa-wrench me-1"></i> Field Installation Assignment</h5>
                <button type="button" class="btn-close-custom ms-auto" data-bs-dismiss="modal" aria-label="Close" style="background: none; border: none; color: #000000 !important; font-size: 1.75rem; font-weight: bold; line-height: 1; opacity: 0.8; transition: opacity 0.15s ease; padding: 0; margin: 0;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.8'">
                    &times;
                </button>
            </div>
            <form id="installationForm" method="POST" action="{{ route('installations.store') }}">
                @csrf
                <input type="hidden" id="installation_id" name="installation_id" value="0">

                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Select Work Order -->
                        <div class="col-md-6">
                            <label for="inst_work_order_id" class="form-label fw-bold">Select Work Order <span class="text-danger">*</span></label>
                            <select name="work_order_id" id="inst_work_order_id" class="form-select form-select-sm" required>
                                <option value="" disabled selected>-- Select Work Order --</option>
                                @foreach($workOrders ?? \App\Models\WorkOrder::with('order.vendor')->get() as $wo)
                                    <option value="{{ $wo->id }}">
                                        WO #{{ $wo->wo_number }} - Order #{{ $wo->order->order_number ?? $wo->order_id }} ({{ $wo->order->vendor->companyname ?? $wo->order->vendor->name ?? 'Vendor' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Assign Service Engineer (Role 15) -->
                        <div class="col-md-6">
                            <label for="service_engineer_id" class="form-label fw-bold">Assign Service Engineer (Role 15) <span class="text-danger">*</span></label>
                            <select name="service_engineer_id" id="service_engineer_id" class="form-select form-select-sm" required>
                                <option value="" disabled selected>-- Select Field Engineer --</option>
                                @foreach($engineers ?? \App\Models\User::whereIn('role_id', [15, 2])->get() as $engineer)
                                    <option value="{{ $engineer->id }}">
                                        {{ $engineer->name }} ({{ $engineer->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Scheduled Date -->
                        <div class="col-md-6">
                            <label for="scheduled_date_input" class="form-label fw-bold">Scheduled Installation Date</label>
                            <input type="date" name="scheduled_date" id="scheduled_date_input" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
                        </div>

                        <!-- Installation Status -->
                        <div class="col-md-6">
                            <label for="inst_status_input" class="form-label fw-bold">Installation Status</label>
                            <select name="status" id="inst_status_input" class="form-select form-select-sm">
                                <option value="assigned">Assigned</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                                <option value="issues_reported">Issues Reported</option>
                            </select>
                        </div>

                        <!-- Customer Feedback / Installation Notes -->
                        <div class="col-md-12">
                            <label for="customer_feedback_input" class="form-label fw-bold">Customer Feedback & Installation Remarks</label>
                            <textarea name="customer_feedback" id="customer_feedback_input" class="form-control form-control-sm" rows="3" placeholder="Enter field engineer remarks or customer feedback..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer py-2 px-3">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-sm btn-info text-white"><i class="fa fa-paper-plane me-1"></i> Assign Installation</button>
                </div>
            </form>
        </div>
    </div>
</div>
