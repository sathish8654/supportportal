<!-- Manage Order & Work Order Modal -->
<div class="modal fade" id="manageOrderModal" tabindex="-1" aria-labelledby="manageOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white py-2 px-3">
                <h5 class="modal-title" id="manageOrderModalLabel"><i class="fa fa-shopping-cart me-1"></i> Convert Quotation to Sales Order</h5>
                <button type="button" class="btn-close-custom ms-auto" data-bs-dismiss="modal" aria-label="Close" style="background: none; border: none; color: #000000 !important; font-size: 1.75rem; font-weight: bold; line-height: 1; opacity: 0.8; transition: opacity 0.15s ease; padding: 0; margin: 0;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.8'">
                    &times;
                </button>
            </div>
            <form id="orderForm" method="POST" action="{{ route('orders.store') }}">
                @csrf
                <input type="hidden" id="order_id" name="order_id" value="0">

                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Select Approved Quotation -->
                        <div class="col-md-8">
                            <label for="order_quotation_id" class="form-label fw-bold">Select Approved Quotation <span class="text-danger">*</span></label>
                            <select name="quotation_id" id="order_quotation_id" class="form-select form-select-sm" required>
                                <option value="" disabled selected>-- Select Quotation --</option>
                                @foreach($quotations ?? \App\Models\Quotation::with('vendor')->get() as $q)
                                    <option value="{{ $q->id }}" data-amount="{{ $q->total_amount }}">
                                        {{ $q->quotation_number }} - {{ $q->vendor->companyname ?? $q->vendor->name ?? 'Vendor #' . $q->vendor_id }} (₹{{ number_format($q->total_amount, 2) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Advance Amount Received -->
                        <div class="col-md-4">
                            <label for="advance_amount_input" class="form-label fw-bold">Advance Payment Received (₹)</label>
                            <input type="number" step="0.01" name="advance_amount" id="advance_amount_input" class="form-control form-control-sm" placeholder="0.00" value="0.00">
                        </div>

                        <!-- Order Status -->
                        <div class="col-md-6">
                            <label for="order_status_input" class="form-label fw-bold">Order Status</label>
                            <select name="status" id="order_status_input" class="form-select form-select-sm">
                                <option value="confirmed">Confirmed</option>
                                <option value="in_production">In Production</option>
                                <option value="ready_for_dispatch">Ready for Dispatch</option>
                                <option value="dispatched">Dispatched</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>

                        <!-- Payment Status -->
                        <div class="col-md-6">
                            <label for="payment_status_input" class="form-label fw-bold">Payment Status</label>
                            <select name="payment_status" id="payment_status_input" class="form-select form-select-sm">
                                <option value="pending">Pending</option>
                                <option value="partial">Partial Payment</option>
                                <option value="paid">Fully Paid</option>
                            </select>
                        </div>

                        <div class="col-md-12 border-top pt-2">
                            <div class="alert alert-info py-2 px-3 mb-0 small">
                                <i class="fa fa-info-circle me-1"></i> Saving this Sales Order will automatically generate an active <strong>Work Order</strong> for the Production & Assembly team.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer py-2 px-3">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-check me-1"></i> Generate Sales Order</button>
                </div>
            </form>
        </div>
    </div>
</div>
