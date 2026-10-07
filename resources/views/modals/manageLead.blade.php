<!-- Manage Lead Modal -->
<style>
    #manageLeadModal .select2-container {
        width: 100% !important;
        max-width: 100% !important;
    }
</style>
<div class="modal fade" id="manageLeadModal" tabindex="-1" aria-labelledby="manageLeadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-2 px-3">
                <h5 class="modal-title" id="manageLeadModalLabel"><i class="fa fa-bullhorn me-1"></i> Add / Edit Lead</h5>
                <button type="button" class="btn-close-custom ms-auto" data-bs-dismiss="modal" aria-label="Close" style="background: none; border: none; color: #000000 !important; font-size: 1.75rem; font-weight: bold; line-height: 1; opacity: 0.8; transition: opacity 0.15s ease; padding: 0; margin: 0;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.8'">
                    &times;
                </button>
            </div>
            <form id="leadForm" method="POST" action="{{ route('leads.store') }}">
                @csrf
                <input type="hidden" id="lead_id" name="lead_id" value="0">
                <input type="hidden" id="_method" name="_method" value="POST">
                <input type="hidden" id="lead_customer_hidden_input" name="customer_id" disabled>

                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Customer Dropdown Container -->
                        <div class="col-md-8" id="customer_select_container">
                            <label for="lead_customer_id" class="form-label fw-bold">Customer / Vendor <span class="text-danger">*</span></label>
                            <div class="d-flex gap-2">
                                <div class="flex-grow-1" style="min-width: 0;">
                                    <select name="customer_id" id="lead_customer_id" class="form-select select2" required style="width: 100%">
                                        <option value="" disabled selected>-- Select Customer / Vendor --</option>
                                        @foreach($customers ?? \App\Models\LRvendor::all() as $customer)
                                            <option value="{{ $customer->id }}">
                                                {{ $customer->name }}{{ $customer->mobile1 ? ' / ' . $customer->mobile1 : '' }}{{ $customer->mobile2 ? ' / ' . $customer->mobile2 : '' }}{{ $customer->companyname ? ' (' . $customer->companyname . ')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="button" id="add_customer_btn" class="btn btn-outline-success btn-sm flex-shrink-0" data-bs-toggle="modal" data-bs-target="#addNewAddressModal" title="Add New Customer Address">
                                    <i class="fa fa-plus"></i> Add Customer
                                </button>
                            </div>
                        </div>

                        <!-- Customer Details Read-only Container -->
                        <div class="col-md-8" id="customer_details_container" style="display: none;">
                            <label class="form-label fw-bold">Customer / Vendor</label>
                            <div class="p-2 border rounded bg-light" style="border-left: 4px solid #0d6efd !important;">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="fw-bold text-primary" id="customer_detail_name"></span>
                                    <span class="badge bg-secondary text-white" id="customer_detail_company"></span>
                                </div>
                                <div class="text-muted small">
                                    <span class="me-3" id="customer_detail_mobile1_wrapper"><i class="fa fa-phone me-1"></i> <span id="customer_detail_mobile1"></span></span>
                                    <span id="customer_detail_mobile2_wrapper"><i class="fa fa-phone-square me-1"></i> <span id="customer_detail_mobile2"></span></span>
                                </div>
                            </div>
                        </div>

                        <!-- Lead Source -->
                        <div class="col-md-4">
                            <label for="lead_source_input" class="form-label fw-bold">Lead Source <span class="text-danger">*</span></label>
                            <select name="lead_source" id="lead_source_input" class="form-select form-select-sm" required>
                                <option value="Website">Website</option>
                                <option value="Referral">Referral</option>
                                <option value="Cold Call">Cold Call</option>
                                <option value="Exhibition">Exhibition</option>
                                <option value="Email Campaign">Email Campaign</option>
                                <option value="Direct Inquiry">Direct Inquiry</option>
                            </select>
                        </div>

                        <!-- Interested Products -->
                        <div class="col-md-8">
                            <label for="interested_products_input" class="form-label fw-bold">Interested Products / Services</label>
                            <textarea name="interested_products" id="interested_products_input" class="form-control form-control-sm" rows="2" placeholder="e.g. GPS Tracker, Lora Gateway, Fuel Sensors"></textarea>
                        </div>

                        <!-- Estimated Value -->
                        <div class="col-md-4">
                            <label for="estimated_value_input" class="form-label fw-bold">Estimated Value (₹)</label>
                            <input type="number" step="0.01" name="estimated_value" id="estimated_value_input" class="form-control form-control-sm" placeholder="e.g. 50000">
                        </div>

                        <!-- Lead Status & Stage -->
                        <!-- Lead Status & Stage -->
                        <div class="col-md-6" id="lead_status_container">
                            <label for="lead_status_input" class="form-label fw-bold">Status</label>
                            <select name="status" id="lead_status_input" class="form-select form-select-sm">
                                <option value="new">New</option>
                                <option value="in_discussion">In Discussion</option>
                                <option value="quotation_sent">Quotation Sent</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="hold">Hold</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>

                        <div class="col-md-6" id="workflow_stage_container">
                            <label for="workflow_stage_input" class="form-label fw-bold">Workflow Stage</label>
                            <select name="workflow_stage" id="workflow_stage_input" class="form-select form-select-sm">
                                <option value="sales_executive">Sales Executive</option>
                                <option value="sales_head_review">Sales Head Review</option>
                                <option value="accounts">Accounts Review</option>
                                <option value="md_approval">MD Approval</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>

                        <!-- Notes -->
                        <div class="col-md-12">
                            <label for="lead_notes_input" class="form-label fw-bold">Internal Notes / Requirements</label>
                            <textarea name="notes" id="lead_notes_input" class="form-control form-control-sm" rows="2" placeholder="Additional requirements or meeting notes..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer py-2 px-3">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-sm btn-primary" id="saveLeadBtn"><i class="fa fa-save me-1"></i> Save Lead</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // Initialize Select2
        $('#lead_customer_id').select2({
            dropdownParent: $('#manageLeadModal'),
            width: '100%'
        });

        // Triggered when lead modal is shown
        $('#manageLeadModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');

            if (id) {
                // Edit mode
                $('#manageLeadModalLabel').html('<i class="fa fa-bullhorn me-1"></i> Edit Lead');
                $('#leadForm').attr('action', '{{ route("leads.update", "") }}/' + id);
                $('#_method').val('PUT');
                $('#lead_id').val(id);

                // Populate fields
                $('#lead_source_input').val(button.data('source'));
                $('#interested_products_input').val(button.data('products'));
                $('#estimated_value_input').val(button.data('value'));
                $('#lead_status_input').val(button.data('status'));
                $('#workflow_stage_input').val(button.data('stage'));
                $('#lead_notes_input').val(button.data('notes'));

                // Hide select, show formatted details card
                $('#customer_select_container').hide();
                $('#customer_details_container').show();

                // Populate details card
                var name = button.data('customer-name') || '-';
                var company = button.data('customer-company') || '';
                var mobile1 = button.data('customer-mobile1') || '';
                var mobile2 = button.data('customer-mobile2') || '';

                $('#customer_detail_name').text(name);
                if (company) {
                    $('#customer_detail_company').text(company).show();
                } else {
                    $('#customer_detail_company').hide();
                }

                if (mobile1) {
                    $('#customer_detail_mobile1').text(mobile1);
                    $('#customer_detail_mobile1_wrapper').show();
                } else {
                    $('#customer_detail_mobile1_wrapper').hide();
                }

                if (mobile2) {
                    $('#customer_detail_mobile2').text(mobile2);
                    $('#customer_detail_mobile2_wrapper').show();
                } else {
                    $('#customer_detail_mobile2_wrapper').hide();
                }

                // Disable select input and enable hidden input for database submission
                $('#lead_customer_id').prop('disabled', true).trigger('change');
                $('#lead_customer_hidden_input').val(button.data('customer-id')).prop('disabled', false);

                // Disable Status, Lead Source, and Workflow Stage
                $('#lead_status_input').prop('disabled', true);
                $('#lead_source_input').prop('disabled', true);
                $('#workflow_stage_input').prop('disabled', true);

                // Show Status and Workflow Stage in Edit mode
                $('#lead_status_container').show();
                $('#workflow_stage_container').show();
            } else {
                // Add mode
                $('#manageLeadModalLabel').html('<i class="fa fa-bullhorn me-1"></i> Add Lead');
                $('#leadForm').attr('action', '{{ route("leads.store") }}');
                $('#_method').val('POST');
                $('#lead_id').val(0);

                // Reset fields
                $('#lead_customer_id').val('').trigger('change');
                $('#lead_source_input').val('Website');
                $('#interested_products_input').val('');
                $('#estimated_value_input').val('');
                $('#lead_status_input').val('new');
                $('#workflow_stage_input').val('sales_executive');
                $('#lead_notes_input').val('');

                // Show select, hide details card
                $('#customer_select_container').show();
                $('#customer_details_container').hide();

                // Enable select input and disable hidden input
                $('#lead_customer_id').prop('disabled', false).trigger('change');
                $('#lead_customer_hidden_input').val('').prop('disabled', true);

                // Enable Status, Lead Source, and Workflow Stage
                $('#lead_status_input').prop('disabled', false);
                $('#lead_source_input').prop('disabled', false);
                $('#workflow_stage_input').prop('disabled', false);

                // Show Add Customer button in Add mode
                $('#add_customer_btn').show();

                // Hide Status and Workflow Stage in Add mode
                $('#lead_status_container').hide();
                $('#workflow_stage_container').hide();
            }
        });

        // Enable disabled fields before submitting so they are sent to the backend
        $('#leadForm').on('submit', function() {
            if ($('#lead_customer_hidden_input').prop('disabled')) {
                $('#lead_customer_id').prop('disabled', false);
            }
            $('#lead_status_input, #lead_source_input, #workflow_stage_input').prop('disabled', false);
        });
    });
</script>

