<!-- Manage Quotation Modal -->
<div class="modal fade" id="manageQuotationModal" tabindex="-1" aria-labelledby="manageQuotationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" style="max-width: 95%;">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-2 px-3">
                <h5 class="modal-title" id="manageQuotationModalLabel"><i class="fa fa-file-text-o me-1"></i> Create / Edit Quotation</h5>
                <button type="button" class="btn-close-custom ms-auto" data-bs-dismiss="modal" aria-label="Close" style="background: none; border: none; color: #000000 !important; font-size: 1.75rem; font-weight: bold; line-height: 1; opacity: 0.8; transition: opacity 0.15s ease; padding: 0; margin: 0;" onmouseover="this.style.opacity='1'" onmouseout="this.style.opacity='0.8'">
                    &times;
                </button>
            </div>
            <form id="quotationForm" method="POST" action="{{ route('quotations.store') }}">
                @csrf
                <input type="hidden" id="quotation_id" name="quotation_id" value="0">
                <input type="hidden" id="quotation_method" name="_method" value="POST">

                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <!-- Select Lead -->
                        <div class="col-md-6">
                            <label for="quotation_lead_id" class="form-label fw-bold">Select Lead <span class="text-danger">*</span></label>
                            <select name="lead_id" id="quotation_lead_id" class="form-select form-select-sm" required>
                                <option value="" disabled selected>-- Select Lead --</option>
                                @foreach($leads ?? \App\Models\Lead::with('vendor')->get() as $lead)
                                    <option value="{{ $lead->id }}">
                                        Lead #{{ $lead->id }} - {{ $lead->vendor->companyname ?? $lead->vendor->name ?? 'Vendor #' . $lead->vendor_id }} ({{ ucfirst($lead->status) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status -->
                        <div class="col-md-3">
                            <label for="quotation_status" class="form-label fw-bold">Quotation Status</label>
                            <select name="status" id="quotation_status" class="form-select form-select-sm">
                                <option value="draft">Draft</option>
                                <option value="submitted_to_head">Submitted to Sales Head</option>
                                <option value="approved_by_head">Approved by Sales Head</option>
                                <option value="approved_by_accounts">Approved by Accounts</option>
                                <option value="approved_by_md">Approved by MD</option>
                                <option value="accepted_by_customer">Accepted by Customer</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                    </div>

                    <h6 class="fw-bold border-bottom pb-2 text-primary d-flex justify-content-between align-items-center">
                        <span>Quotation Line Items</span>
                        <button type="button" class="btn btn-xs btn-outline-success" id="addQuotationRowBtn">
                            <i class="fa fa-plus"></i> Add Item Row
                        </button>
                    </h6>

                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-bordered align-middle" id="quotationItemsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 15%;">Category</th>
                                    <th style="width: 15%;">Model</th>
                                    <th style="width: 10%;">Model No.</th>
                                    <th>Item Description</th>
                                    <th style="width: 8%;">Qty</th>
                                    <th style="width: 12%;">Unit Price (₹)</th>
                                    <th style="width: 8%;">GST %</th>
                                    <th style="width: 12%;">Total Price (₹)</th>
                                    <th style="width: 5%;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="quotationItemsBody">
                                <!-- Dynamic rows loaded via JS -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Totals Summary -->
                    <div class="row justify-content-end">
                        <div class="col-md-5">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th class="text-end">Subtotal (₹):</th>
                                    <td style="width: 140px;"><input type="number" step="0.01" name="subtotal" id="q_subtotal" class="form-control form-control-sm text-end" value="0.00" readonly></td>
                                </tr>
                                <tr>
                                    <th class="text-end">GST Amount (₹):</th>
                                    <td><input type="number" step="0.01" name="gst_amount" id="q_gst_amount" class="form-control form-control-sm text-end" value="0.00" readonly></td>
                                </tr>
                                <tr>
                                    <th class="text-end">Transport Charges (₹):</th>
                                    <td><input type="number" step="0.01" name="transport_charges" id="q_transport_charges" class="form-control form-control-sm text-end" value="0.00"></td>
                                </tr>
                                <tr>
                                    <th class="text-end">Installation Charges (₹):</th>
                                    <td><input type="number" step="0.01" name="installation_charges" id="q_installation_charges" class="form-control form-control-sm text-end" value="0.00"></td>
                                </tr>
                                <tr class="border-top fw-bold fs-6">
                                    <th class="text-end text-primary">Grand Total (₹):</th>
                                    <td><input type="number" step="0.01" name="total_amount" id="q_total_amount" class="form-control form-control-sm text-end fw-bold text-primary" value="0.00" readonly></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="modal-footer py-2 px-3">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-save me-1"></i> Save Quotation</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        var allCategories = @json($categories ?? \App\Models\LRcategory::where('status', 1)->orderBy('name', 'asc')->get());

        // Function to dynamically append a quotation line item row
        function addRow(item = null) {
            var index = $('#quotationItemsBody tr').length;
            var categoryId = '';
            var modelId = '';
            var modelNumber = '';
            var description = item ? (item.item_description || '') : '';
            var quantity = item ? item.quantity : 1;
            var unitPrice = item ? item.unit_price : '0.00';
            var gstRate = item ? item.gst_rate : 18;
            var totalPrice = item ? item.total_price : '0.00';

            // Find category and model number for loaded item
            if (item && item.product_id) {
                modelId = item.product_id;
                var modelObj = allCategories.find(c => c.id == modelId);
                if (modelObj) {
                    categoryId = modelObj.parent_id;
                    modelNumber = modelObj.model_number !== null ? modelObj.model_number : '';
                }
            }

            // Generate Category options
            var catSelect = '<select class="form-select form-select-sm category-select" style="min-width: 120px;">';
            catSelect += '<option value="">-- Category --</option>';
            allCategories.forEach(function(cat) {
                if (cat.parent_id == 0) {
                    var selected = (categoryId == cat.id) ? 'selected' : '';
                    catSelect += '<option value="' + cat.id + '" ' + selected + '>' + cat.name + '</option>';
                }
            });
            catSelect += '</select>';

            // Generate Model options
            var modelSelect = '<select name="products[' + index + '][product_id]" class="form-select form-select-sm model-select" style="min-width: 120px;">';
            modelSelect += '<option value="">-- Model --</option>';
            if (categoryId) {
                allCategories.forEach(function(sub) {
                    if (sub.parent_id == categoryId) {
                        var selected = (modelId == sub.id) ? 'selected' : '';
                        modelSelect += '<option value="' + sub.id + '" data-model-number="' + (sub.model_number !== null ? sub.model_number : '') + '" ' + selected + '>' + sub.name + '</option>';
                    }
                });
            }
            modelSelect += '</select>';

            var rowHtml = '<tr>' +
                '<td>' + catSelect + '</td>' +
                '<td>' + modelSelect + '</td>' +
                '<td><input type="text" class="form-control form-control-sm model-number-input text-center" value="' + modelNumber + '" readonly style="background-color: #e9ecef;"></td>' +
                '<td><input type="text" name="products[' + index + '][description]" class="form-control form-control-sm item-desc" placeholder="Item description" value="' + description + '" required></td>' +
                '<td><input type="number" name="products[' + index + '][quantity]" class="form-control form-control-sm item-qty text-center" value="' + quantity + '" min="1" required></td>' +
                '<td><input type="number" step="0.01" name="products[' + index + '][unit_price]" class="form-control form-control-sm item-price text-end" value="' + unitPrice + '" required></td>' +
                '<td><input type="number" step="0.01" name="products[' + index + '][gst_rate]" class="form-control form-control-sm item-gst text-center" value="' + gstRate + '"></td>' +
                '<td><input type="number" step="0.01" name="products[' + index + '][total_price]" class="form-control form-control-sm item-total text-end" value="' + totalPrice + '" readonly style="background-color: #e9ecef;"></td>' +
                '<td class="text-center"><button type="button" class="btn btn-xs btn-outline-danger removeRowBtn"><i class="fa fa-trash-o"></i></button></td>' +
                '</tr>';

            $('#quotationItemsBody').append(rowHtml);
        }

        // Calculate single row totals
        function calculateRow(row) {
            var qty = parseFloat(row.find('.item-qty').val()) || 0;
            var price = parseFloat(row.find('.item-price').val()) || 0;
            var gstRate = parseFloat(row.find('.item-gst').val()) || 0;

            var subtotal = qty * price;
            var gstAmount = subtotal * (gstRate / 100);
            var total = subtotal + gstAmount;

            row.find('.item-total').val(total.toFixed(2));
            calculateTotals();
        }

        // Calculate all summary totals
        function calculateTotals() {
            var subtotalSum = 0;
            var gstSum = 0;

            $('#quotationItemsBody tr').each(function() {
                var qty = parseFloat($(this).find('.item-qty').val()) || 0;
                var price = parseFloat($(this).find('.item-price').val()) || 0;
                var gstRate = parseFloat($(this).find('.item-gst').val()) || 0;

                var rowSubtotal = qty * price;
                subtotalSum += rowSubtotal;
                gstSum += rowSubtotal * (gstRate / 100);
            });

            $('#q_subtotal').val(subtotalSum.toFixed(2));
            $('#q_gst_amount').val(gstSum.toFixed(2));

            var userGst = gstSum;
            var transport = parseFloat($('#q_transport_charges').val()) || 0;
            var installation = parseFloat($('#q_installation_charges').val()) || 0;

            var grandTotal = subtotalSum + userGst + transport + installation;
            $('#q_total_amount').val(grandTotal.toFixed(2));
        }

        // Category selection listener: update Model options
        $(document).on('change', '.category-select', function() {
            var catId = $(this).val();
            var row = $(this).closest('tr');
            var modelSelect = row.find('.model-select');
            var modelNumInput = row.find('.model-number-input');

            // Clear model dropdown & model number
            modelSelect.html('<option value="">-- Model --</option>');
            modelNumInput.val('');

            if (catId) {
                allCategories.forEach(function(sub) {
                    if (sub.parent_id == catId) {
                        modelSelect.append('<option value="' + sub.id + '" data-model-number="' + (sub.model_number !== null ? sub.model_number : '') + '">' + sub.name + '</option>');
                    }
                });
            }
            calculateRow(row);
        });

        // Model selection listener: update Model number & description
        $(document).on('change', '.model-select', function() {
            var row = $(this).closest('tr');
            var option = $(this).find('option:selected');
            var modelNumber = option.data('model-number') !== undefined && option.data('model-number') !== null ? option.data('model-number') : '';
            var modelName = option.val() ? option.text().trim() : '';

            row.find('.model-number-input').val(modelNumber);
            if (option.val()) {
                row.find('.item-desc').val(modelName);
                if (modelNumber === '' || modelNumber === null || modelNumber === undefined) {
                    alert('Model number needs to be updated at Manage Category menu.');
                }
            }
            calculateRow(row);
        });

        // Event listeners for recalculating row values
        $(document).on('input change', '.item-qty, .item-price, .item-gst', function() {
            calculateRow($(this).closest('tr'));
        });

        $(document).on('input change', '#q_transport_charges, #q_installation_charges', function() {
            calculateTotals();
        });

        // Remove row
        $(document).on('click', '.removeRowBtn', function() {
            if ($('#quotationItemsBody tr').length > 1) {
                $(this).closest('tr').remove();
                calculateTotals();
            } else {
                alert('At least one item row is required.');
            }
        });

        // Add row click listener
        $('#addQuotationRowBtn').on('click', function() {
            addRow();
        });

        // Modal show hook to configure Add / Edit modes
        $('#manageQuotationModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');

            if (id) {
                // Edit mode
                $('#manageQuotationModalLabel').html('<i class="fa fa-file-text-o me-1"></i> Edit Quotation');
                $('#quotationForm').attr('action', '{{ route("quotations.update", "") }}/' + id);
                $('#quotation_method').val('PUT');
                $('#quotation_id').val(id);

                // Populate fields
                $('#quotation_lead_id').val(button.data('lead-id')).prop('disabled', true);
                $('#quotation_status').val(button.data('status'));
                $('#q_subtotal').val(button.data('subtotal'));
                $('#q_gst_amount').val(button.data('gst-amount'));
                $('#q_transport_charges').val(button.data('transport-charges'));
                $('#q_installation_charges').val(button.data('installation-charges'));
                $('#q_total_amount').val(button.data('total-amount'));

                // Load existing items
                $('#quotationItemsBody').empty();
                var items = button.data('items');
                if (items && items.length > 0) {
                    items.forEach(function(item) {
                        addRow(item);
                    });
                    // Force recalculation in case database values were zero or incorrect
                    calculateTotals();
                } else {
                    addRow();
                }
            } else {
                // Add mode
                $('#manageQuotationModalLabel').html('<i class="fa fa-file-text-o me-1"></i> Create Quotation');
                $('#quotationForm').attr('action', '{{ route("quotations.store") }}');
                $('#quotation_method').val('POST');
                $('#quotation_id').val(0);

                // Reset fields
                $('#quotation_lead_id').val('').prop('disabled', false);
                $('#quotation_status').val('draft');
                $('#q_subtotal').val('0.00');
                $('#q_gst_amount').val('0.00');
                $('#q_transport_charges').val('0.00');
                $('#q_installation_charges').val('0.00');
                $('#q_total_amount').val('0.00');

                $('#quotationItemsBody').empty();
                addRow();
            }
        });

        // Re-enable disabled fields before submitting so form sends lead_id
        $('#quotationForm').on('submit', function() {
            $('#quotation_lead_id').prop('disabled', false);
        });
    });
</script>
