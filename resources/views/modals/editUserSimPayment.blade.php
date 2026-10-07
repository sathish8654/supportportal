<style>
#updateModal .modal-content {
    border: none;
    border-radius: 16px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    overflow: hidden;
}
#updateModal .modal-header {
    background: #ffffff !important;
    color: #1e293b !important;
    border-bottom: 1px solid #e2e8f0 !important;
    padding: 20px 24px;
}
#updateModal .modal-title {
    font-weight: 700;
    letter-spacing: -0.5px;
    color: #1e293b !important;
}
#updateModal .modal-body {
    padding: 30px 24px;
    background-color: #f8f9fa;
}
#updateModal .form-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #495057;
    margin-bottom: 6px;
}
#updateModal .form-control, #updateModal .form-select {
    border-radius: 8px;
    border: 1px solid #ced4da;
    padding: 10px 14px;
    font-size: 0.95rem;
    background-color: white;
    transition: all 0.2s ease-in-out;
}
#updateModal .form-control:focus, #updateModal .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.15);
    background-color: white;
}
#updateModal .section-title {
    font-size: 0.85rem;
    font-weight: 700;
    color: #0d6efd;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-bottom: 16px;
    border-bottom: 2px solid rgba(13, 110, 253, 0.1);
    padding-bottom: 6px;
    margin-top: 10px;
}
#updateModal .modal-footer {
    border-top: 1px solid #dee2e6;
    padding: 16px 24px;
    background-color: white;
}
#updateModal .modal-footer .btn {
    padding: 10px 20px;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.2s;
}
#updateModal .info-box {
    background-color: #eef2f6;
    border-left: 4px solid #0d6efd;
    border-radius: 6px;
    padding: 14px 18px;
    font-size: 0.9rem;
    margin-top: 15px;
}
#updateModal .info-box-title {
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 8px;
}
</style>

<div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="SubmitFormAdd">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa fa-credit-card me-2"></i>SIM Payment Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="alert alert-success" role="alert" id="successMsg" style="display: none">
                        Added successfully!
                    </div>

                    <!-- Section 1: Payment Details -->
                    <div class="section-title">Payment Info</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label" for="date">Paid Date</label>
                            <input type="date" class="form-control date" id="date" max="<?= date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="mode">Payment Mode</label>
                            <select id="mode" class="form-select">
                                <option value="">--Select--</option>
                                <option value="1">Offline</option>
                                <option value="2">Online</option>
                                <option value="3">MobileApp</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="tx_number">Transaction Number</label>
                            <input type="text" class="form-control" id="tx_number" placeholder="Enter Txn Number" required>
                        </div>
                    </div>

                    <!-- Section 2: SIM & Cost -->
                    <div class="section-title">SIM & Customer Details</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label" for="simPhoneNumber">Device SIM Phone No</label>
                            <div id="simPhoneSelectWrapper">
                                <select class="form-select simPhoneNumber" name="simPhoneNumber" id="simPhoneNumber" style="width: 100% !important">
                                    <option value="" selected>Select</option>
                                    @foreach ($simNumber as $item)
                                        <option value="{{ $item->simPhoneNumber }}">{{ $item->simPhoneNumber }} | {{ $item->custName }} | {{ $item->custContactNumber }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="simPhoneDisplayWrapper" style="display: none;">
                                <div id="simPhoneNumberEditDisplay" class="fw-semibold text-dark p-2 bg-white border rounded-3" style="min-height: 38px; line-height: 1.4; font-size: 0.9rem;"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="contact">Customer Contact</label>
                            <input type="number" class="form-control" id="contact" name="contact" placeholder="Contact Number" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="cost">Renew Cost</label>
                            <input type="text" class="form-control" id="cost" placeholder="Renew Cost" required>
                        </div>
                    </div>

                    <!-- Section 3: Subscription & Status -->
                    <div class="section-title">Subscription Validity</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label" for="from_date">From Date</label>
                            <input type="date" class="form-control date" id="from_date" name="from_date" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="to_date">To Date</label>
                            <input type="date" class="form-control" id="to_date" min="<?= date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="status">Status</label>
                            <select id="status" class="form-select">
                                <option value="">--Select--</option>
                                <option value="1">Success</option>
                                <option value="2">Failed</option>
                                <option value="3">Inprogress</option>
                            </select>
                        </div>
                    </div>

                    <!-- Section 4: Notes -->
                    <div class="section-title">Additional Notes</div>
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label class="form-label" for="note">Note</label>
                            <textarea class="form-control" id="note" placeholder="Enter notes here" rows="3" required></textarea>
                        </div>
                    </div>

                    <!-- Meta Information Box -->
                    <div class="info-box userInfo" style="display:none;">
                        <div class="info-box-title"><i class="fa fa-info-circle me-1 text-primary"></i>System Record Meta Info</div>
                        <div class="row g-2">
                            <div class="col-md-6"><small class="text-muted">Sim Name:</small> <span id="sim_id_name_text" class="fw-semibold text-dark"></span></div>
                            <div class="col-md-6"><small class="text-muted">Contact Name:</small> <span id="name_text" class="fw-semibold text-dark"></span></div>
                            <div class="col-md-6"><small class="text-muted">Created By:</small> <span id="created_by_text" class="fw-semibold text-dark"></span></div>
                            <div class="col-md-6"><small class="text-muted">Updated By:</small> <span id="updated_by_text" class="fw-semibold text-dark"></span></div>
                        </div>
                        
                        <!-- Keep hidden inputs to preserve standard JS selectors/values -->
                        <input type="hidden" id="sim_id_name">
                        <input type="hidden" id="name">
                        <input type="hidden" id="created_by">
                        <input type="hidden" id="updated_by">
                    </div>

                </div>
                <div class="modal-footer">
                    <input id="id" type="hidden">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-success" id="SubmitUpdate" style="display:none;">Update Payment</button>
                    <button class="btn btn-success btn-submit" id="submitCreate">Create Record</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Script -->
<script type="text/javascript">

   
    $(document).ready(function () {

        
 var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

    $('#from_date').change(function() {
        var fromDateVal = $(this).val();
        if (fromDateVal) {
            $('#to_date').attr('min', fromDateVal);
        }
    });

    // get modal data

    $('#simPhoneNumber').change(function() {

        if ($(this).is(':disabled')) {
        // Do nothing if the field is disabled
        return;
             }

                var simPhoneNumber = $(this).val();

                if (simPhoneNumber != '') {
                    // AJAX request
                    $.ajax({
                        url: "{{ route('getSiminfo') }}",
                        type: 'post',
                        data: {
                            _token: CSRF_TOKEN,
                            simPhoneNumber: simPhoneNumber,
                        },
                        dataType: 'json',
                        success: function(response) {
                            // console.log(response);
                            if (response.success == 1) {
                                $('#from_date').val(response.renewalDate);
                                $('#contact').val(response.contact);
                            } else {
                                alert("Invalid ID.");
                            }
                        }
                    });
                } else {
                    alert('Please fill all fields.');
                }
            });


            // submit to update modal data

        $('#updateModal').on('shown.bs.modal', function () {
        $('#simPhoneNumber').select2({
            placeholder: 'Select or type',
            allowClear: true,
            tags: true,
            dropdownParent: $('#updateModal')
        });
        });

        $(".addNew").click(function () {
            $(".modal-title").html("Create payment log")
            $('#SubmitFormAdd')[0].reset();
            $("#SubmitUpdate").hide();
            $(".userInfo").hide();
            $("#submitCreate").show();
            $("#simPhoneNumber").select2().select2('val','');
            $('#simPhoneNumber').attr("disabled", false);
            
            // Toggle wrappers to show select
            $('#simPhoneSelectWrapper').show();
            $('#simPhoneDisplayWrapper').hide();
        });

        $(".UpdateInfo").hide();
        $('input:radio[id=imeiNo]:checked').val();

        // Craete new record
        $(".btn-submit").click(function (e) {

            e.preventDefault();

            var simPhoneNumber = $("#simPhoneNumber").val().trim();
            var contact = $("#contact").val().trim();
            var cost = $("#cost").val().trim();
            var from_date = $("#from_date").val().trim();
            var to_date = $("#to_date").val().trim();
            var tx_number = $("#tx_number").val().trim();
            var note = $("#note").val().trim();
            var date = $("#date").val().trim();
            var mode = $("#mode").val().trim();
            var status = $("#status").val().trim();

            if (from_date && to_date && new Date(to_date) < new Date(from_date)) {
                alert("To Date cannot be earlier than From Date.");
                return;
            }

            $.ajax({
                type: 'POST',
                url: "{{ route('addNewPayment') }}",
                data: {
                    _token: CSRF_TOKEN,
                    simPhoneNumber: simPhoneNumber,
                    contact: contact,
                    cost: cost,
                    from_date: from_date,
                    to_date: to_date,
                    tx_number: tx_number,
                    note: note,
                    date: date,
                    mode: mode,
                    status: status

                },
                success: function (response) {
                     alert(response.message);
                    $("#updateModal .close").trigger("click");
                    $('#userSimPaymentTable').DataTable().ajax.reload();
                    $('#successMsg').show();
                    // $('#SubmitFormAdd').empty();

                },
                error: function (response) {
                    alert(JSON.parse(response.responseText).message);
                    },
            });
        });

        // get record info for modal data
        $('#userSimPaymentTable').on('click', '.updatePayment, .viewPayment', function () {
            var isViewOnly = $(this).hasClass('viewPayment') || $(this).data('view-only') == 1;

            var id = $(this).data('id');

            $('#id').val(id);

            if (isViewOnly) {
                $(".modal-title").html("View Details");
                $('#updateModal input, #updateModal select').prop('disabled', true);
                $("#SubmitUpdate").hide();
                $("#UpdateInfo").show();
                $("#submitCreate").hide();
            } else {
                $(".modal-title").html("Update");
                $('#updateModal input, #updateModal select').prop('disabled', false);
                $("#SubmitUpdate").show();
                $("#UpdateInfo").show();
                $("#submitCreate").hide();
                $('#simPhoneNumber').attr("disabled", true);
            }

            // AJAX request
            $.ajax({
                url: "{{ route('getusersimPaymentList') }}",
                type: 'post',
                data: {
                    _token: CSRF_TOKEN,
                    id: id
                },

                dataType: 'json',
                success: function (response) {
                    if (response.success == 1) {
                        // $('input:radio[id=yes]:checked').val();
                        // $("#yes").is(":checked", true);
                        $("#imeiYes").prop("checked", true);
                        $("#simPhoneNumber").val(response.simPhoneNumber).trigger('change');
                        $('#contact').val(response.contact);
                        $('#cost').val(response.cost);
                        $('#from_date').val(response.from_date).trigger('change');
                        $('#to_date').val(response.to_date);
                        $('#tx_number').val(response.tx_number);
                        $('#note').val(response.note);
                        $('#date').val(response.date);
                        $('#mode').val(response.mode);
                        $('#status').val(response.status);

                        $('#name').val(response.name);
                        $('#created_by').val(response.created_by);
                        $('#updated_by').val(response.updated_by);
                        $('#sim_id_name').val(response.sim_id_name);
                        $('#name_text').text(response.name || '-');
                        $('#created_by_text').text(response.created_by || '-');
                        $('#updated_by_text').text(response.updated_by || '-');
                        $('#sim_id_name_text').text(response.sim_id_name || '-');

                        // Populate display element with full details
                        var simDetails = response.simPhoneNumber;
                        if (response.name) simDetails += ' | ' + response.name;
                        if (response.contact) simDetails += ' | ' + response.contact;
                        $('#simPhoneNumberEditDisplay').text(simDetails);
                        
                        // Toggle wrappers to show formatted display
                        $('#simPhoneSelectWrapper').hide();
                        $('#simPhoneDisplayWrapper').show();

                        $(".userInfo").show();


                    } else {
                        alert("Invalid ID.");
                    }
                }
            });

        });

        // Update record
        $('#SubmitUpdate').click(function (e) {

            e.preventDefault();

            var id = $('#id').val();
            var simPhoneNumber = $("#simPhoneNumber").val().trim();
            var contact = $("#contact").val().trim();
            var cost = $("#cost").val().trim();
            var from_date = $("#from_date").val().trim();
            var to_date = $("#to_date").val().trim();
            var tx_number = $("#tx_number").val().trim();
            var note = $("#note").val().trim();
            var date = $("#date").val().trim();
            var mode = $("#mode").val().trim();
            var status = $("#status").val().trim();

            if (from_date && to_date && new Date(to_date) < new Date(from_date)) {
                alert("To Date cannot be earlier than From Date.");
                return;
            }

            if (id != '' && simPhoneNumber != '') {

                // AJAX request
                $.ajax({
                    url: "{{ route('updatePaymentInfo') }}",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    type: 'post',
                    data: {
                        id: id,
                        simPhoneNumber: simPhoneNumber,
                        contact: contact,
                        cost: cost,
                        from_date: from_date,
                        to_date: to_date,
                        tx_number: tx_number,
                        note: note,
                        date: date,
                        mode: mode,
                        status: status
                    },
                    dataType: 'json',
                    success: function (response) {
                        alert(response.message);
                        $("#updateModal .close").trigger("click");
                        $('#userSimPaymentTable').DataTable().ajax.reload();
                    },

                    error: function (response) {
                        alert(JSON.parse(response.responseText).message);
                    }

                });

            } else {
                alert('Please fill all fields.');
            }
        });

        // Delete record
        $('#userSimPaymentTable').on('click', '.deletePayment', function () {
            var id = $(this).data('id');
            var deleteConfirm = confirm("Are you sure?");
            if (deleteConfirm == true) {
                // AJAX request
                $.ajax({
                    url: "{{ route('deletesimPaymentList') }}",
                    type: 'post',
                    data: {
                        _token: CSRF_TOKEN,
                        id: id
                    },
                    success: function (response) {
                        alert(response.message);
                        $('#userSimPaymentTable').DataTable().ajax.reload();
                    },
                    error: function (response) {
                        alert(JSON.parse(response.responseText).message);
                    }
                });
            }

        });
    });
</script>
