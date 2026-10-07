<style>
.ui-front {
    z-index: 5000;
}
</style>
<!-- Modal -->
<div id="updateModal" class="modal "  tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg ui-front" role="document">
    <!-- <div class="modal-dialog modal-lg"> -->

        <!-- Modal content-->
        <div class="modal-content">
            <form id="SubmitFormAdd">

                <div class="modal-header">
                    <h4 class="modal-title">Update </h4>
                    <button type="button" class="close" data-bs-dismiss="modal"  aria-label="Close">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="alert alert-success" role="alert" id="successMsg" style="display: none"> Added
                        successfully!
                    </div>
                
                        <div class="p-3 border bg-light">

                            <div class="row gx-5">

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="simStatus">Sim Status</label>
                                        <select id='simStatus' class="form-control">
                                            <option value=''>--Select--</option>
                                            @foreach ( $simStatus as $status)
                                            <option value="{{ $status->id }}">{{ $status->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="renew">Renewal</label>
                                        <select id='renew' class="form-control" required="true">
                                            <option value=''>--Select--</option>
                                            <option value='1'>Renew</option>
                                            <option value='2'>Not-renew</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="cost">SIM Cost</label>
                                        <input type="text" class="form-control" id="cost"
                                            placeholder="Enter SIM Cost" required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name">Activate Date</label>
                                        <input type="date" class="form-control date" id="activateDate" required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="renewalDate">RenewalDate</label>
                                        <input type="date" class="form-control" id="renewalDate" required>

                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="payment">Payment</label>
                                        <select id='payment' class="form-control">
                                            <option value=''>--Select--</option>
                                            <option value='1'>Paid</option>
                                            <option value='2'>Pending</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="p-3 border bg-light">
                            <div class="row gx-5">

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name">SIM No</label>
                                        <div id="responsesimNo" style="color:red"></div>
                                        <input type="text" class="form-control" id="simNo"
                                            placeholder="Enter SIM Number" required>


                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">

                                        <label for="name">SIM Phone Number</label>
                                        <div id="responsesimPhoneNumber" style="color:red"></div>
                                        <input type="text" class="form-control" id="simPhoneNumber"
                                            placeholder="Enter SIM Phone Number" required>
                                    </div>
                                </div>
                         

                            
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name">Customer Name</label>
                                        <input type="text" class="form-control" id="custName"
                                            placeholder="Enter Customer name" required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name">Name on Aadhar</label>
                                        <input type="text" class="form-control" id="custAadharName"
                                            placeholder="Enter Aadhar name" required>
                                    </div>
                                </div>
                                                     
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name">Contact Number</label>
                                        <input type="text" class="form-control" id="custContactNumber"
                                            placeholder="Enter Contact Number" required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name">Aadhar Number</label>
                                        <input type="text" class="form-control" id="custAadharNumber"
                                            placeholder="Enter Aadhar Number" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="name">Address</label>
                                <input type="text" class="form-control" id="custAddress" placeholder="Enter Address"
                                    required>
                            </div>
                        </div>
                        <br>
                        <div class="p-3 border bg-light">
                            <div class="row gx-5">
                                <div class="col-md-3">
                                    <!-- Department Dropdown -->
                                    <div class="form-group">
                                        <label for="simOperator">SIM Operator</label>
                                        <select id='simOperator' class="form-control">
                                            <option value=''>--Select--</option>
                                            <option value='1'>AirTel</option>
                                            <option value='2'>BSNL</option>
                                            <option value='3'>JIO</option>
                                        </select>
                                        <div id="responsesimOperator" style="color:red"></div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Department Dropdown -->
                                    <div class="form-group">
                                        <label for="kycStatus">KYC</label>
                                        <select id='kycStatus' class="form-control">
                                            <option value=''>--Select--</option>
                                            <option value='1'>Updated</option>
                                            <option value='2'>Pending</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="name">IMEI</label>&nbsp;
                                        <input type="radio" name="actions" value="Yes" id="imeiYes"
                                            class="static_class" /> Yes &nbsp;</label>
                                        <input type="radio" checked="checked" name="actions" value="No" id="imeiNo"
                                            class="static_class" /> No </label>
                                        <div id="extra" style="display:none;">
                                            <input type="text" class="form-control" id="imei"
                                                placeholder="Enter IMEI Number" required>
                                        </div>
                                        <div id="responseIMEI" style="color:red"></div>

                                    </div>

                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="name">Unit Serial</label>
                                        <input type="text" class="form-control" id="unitSerialNumber"
                                            placeholder="Enter Unit Serial" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                  
                    <br>
                    <div class="p-3 border bg-light">
                        <div class="row gx-5">
                            <div class="col-md">
                                <div class="form-group">
                                    <label for="careOff">Care-Off</label>
                                    <textarea type="text" class="form-control" id="careOff"
                                        placeholder="Enter CareOff name" required> </textarea>
                                </div>
                            </div>
                            <div class="row UpdateInfo"  id="UpdateInfo">
                                <div class="col-md-6">
                                    <label for="message-text" class="col-form-label">Created by:</label>
                                    <input type="text" class="form-control" id="created_by" readonly>
                                    <input type="text" class="form-control" id="created_at" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="message-text" class="col-form-label">Last updated by:</label>
                                    <input type="text" class="form-control" id="updated_by" readonly>
                                    <input type="text" class="form-control" id="updated_at" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="p-3 border bg-light">
                        <div class="row gx-5">
                            <div class="col-md">
                                <div class="form-group">
                                    <label for="note">Note</label>
                                    <textarea type="text" class="form-control" id="note" placeholder="Enter note"
                                        required> </textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="pull-right">
                        <input id="txt_empid" type="hidden">
                        <button class="btn btn-success" id="SubmitUpdate" style="display:none;">Update</button>
                        <button class="btn btn-success btn-submit" id="submitCreate">Create</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>




<!-- Script -->
<script type="text/javascript">


    $(function () {
        $(".addNew").click(function () {
            $('#SubmitFormAdd')[0].reset();
            // $("#updateModal").modal('show');
            $("#SubmitUpdate").hide();
            $("#submitCreate").show();
        });
    });


    $('#updateModal').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
        $("#SubmitUpdate").hide();
        $("#submitCreate").show();
        $("#extra").hide("fast");
    })


    $.ajaxSetup({

        // CSRF Token
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    //    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content'); 
    $(document).ready(function () {

        // $("#updateModal").modal();

        $(".UpdateInfo").hide();
        $('input:radio[id=imeiNo]:checked').val();

        // Craete new record
        $(".btn-submit").click(function (e) {
            e.preventDefault();

            var simStatus = $("#simStatus").val().trim();
            var renew = $("#renew").val().trim();
            var cost = $("#cost").val().trim();
            var simNo = $("#simNo").val().trim();
            var simPhoneNumber = $("#simPhoneNumber").val().trim();
            var simOperator = $("#simOperator").val().trim();
            var activateDate = $("#activateDate").val().trim();
            var custName = $("#custName").val().trim();
            var custAadharName = $("#custAadharName").val().trim();
            var custContactNumber = $("#custContactNumber").val().trim();
            var custAadharNumber = $("#custAadharNumber").val().trim();
            var custAddress = $("#custAddress").val().trim();
            var imei = $("#imei").val().trim();
            var unitSerialNumber = $("#unitSerialNumber").val().trim();
            var kycStatus = $("#kycStatus").val().trim();
            var payment = $("#payment").val().trim();
            var careOff = $("#careOff").val().trim();
            var note = $("#note").val().trim();
            var renewalDate = $("#renewalDate").val().trim();
            var imeiYes = $('input:radio[id=imeiYes]:checked').val();
            var imeiNo = $('input:radio[id=imeiNo]:checked').val();
            //  $("#imeiYes").val().trim();
            // $('input:radio[id=yes]:checked').val();

            $.ajax({
                type: 'POST',
                url: "{{ route('addNewSim') }}",
                data: {
                    simStatus: simStatus,
                    renew: renew,
                    cost: cost,
                    simNo: simNo,
                    simPhoneNumber: simPhoneNumber,
                    simOperator: simOperator,
                    activateDate: activateDate,
                    custName: custName,
                    custAadharName: custAadharName,
                    custContactNumber: custContactNumber,
                    custAadharNumber: custAadharNumber,
                    custAddress: custAddress,
                    imei: imei,
                    unitSerialNumber: unitSerialNumber,
                    kycStatus: kycStatus,
                    payment: payment,
                    careOff: careOff,
                    note: note,
                    renewalDate: renewalDate,
                    imeiYes: imeiYes,
                    imeiNo: imeiNo,

                },
                success: function (response) {
                     alert(response.message);
                     $('#updateModal').modal('toggle');
                    $('#userSimTable').DataTable().ajax.reload()


                    // $("#submitCreate").hide();
                    // $("#submitUpdate").hide();
                    $('#successMsg').show();
                    // $('#SubmitFormAdd').empty();

                },
                error: function (response) {
                    alert(JSON.parse(response.responseText).message);
                    $("#responseIMEI").text(JSON.parse(response.responseText).error);
                    $("#responseIMEI").text(JSON.parse(response.responseText).errors
                        .imei[0]);
                    $("#responsesimNo").text(JSON.parse(response.responseText).errors
                        .simNo[0]);
                    $("#responsesimPhoneNumber").text(JSON.parse(response.responseText)
                        .errors.simPhoneNumber[0]);
                    $("#responsesimOperator").text(JSON.parse(response.responseText).errors
                        .simOperator[0]);
                },
            });
        });

        // get record info for modal data
        $('#userSimTable').on('click', '.updateUser, .viewUser', function () {
            var isViewOnly = $(this).hasClass('viewUser') || $(this).data('view-only') == 1;

            if (isViewOnly) {
                $('#updateModal .modal-title').text('View Details');
                $('#updateModal input, #updateModal select').prop('disabled', true);
                $('#SubmitUpdate').hide();
                $('#UpdateInfo').show();
                $('#submitCreate').hide();
            } else {
                $('#updateModal .modal-title').text('Update');
                $('#updateModal input, #updateModal select').prop('disabled', false);
                $('#SubmitUpdate').show();
                $('#UpdateInfo').show();
                $('#submitCreate').hide();
            }

            var id = $(this).data('id');

            $('#txt_empid').val(id);

            // AJAX request
            $.ajax({
                url: "{{ route('getUserSimList') }}",
                type: 'post',
                data: {
                    _token: CSRF_TOKEN,
                    id: id
                },

                dataType: 'json',
                success: function (response) {

                    if (response.success == 1) {

                        if (response.simStatus == 3) {
                            $('#renewalDate').attr("disabled", false)
                        } else {
                            $('#renewalDate').attr("disabled", true)
                        }

                        $("#extra").show("fast");
                        // $('input:radio[id=yes]:checked').val();
                        // $("#yes").is(":checked", true);
                        $("#imeiYes").prop("checked", true);

                        $('#simStatus').val(response.simStatus);
                        $('#renew').val(response.renew);
                        $('#cost').val(response.cost);
                        $('#simNo').val(response.simNo);
                        $('#simPhoneNumber').val(response.simPhoneNumber);
                        $('#simOperator').val(response.simOperator);
                        $('#activateDate').val(response.activateDate);
                        $('#custName').val(response.custName);
                        $('#custAadharName').val(response.custAadharName);
                        $('#custContactNumber').val(response.custContactNumber);
                        $('#custAadharNumber').val(response.custAadharNumber);
                        $('#custAddress').val(response.custAddress);
                        $('#imei').val(response.imei);
                        $('#unitSerialNumber').val(response.unitSerialNumber);
                        $('#kycStatus').val(response.kycStatus);
                        $('#payment').val(response.payment);
                        $('#careOff').val(response.careOff);
                        $('#note').val(response.note);
                        $('#renewalDate').val(response.renewalDate);
                        $('#created_by').val(response.created_by);
                        $('#created_at').val(response.created_at);
                        $('#updated_by').val(response.updated_by);
                        $('#updated_at').val(response.updated_at);
                        //  userSimTable.ajax.reload();

                    } else {
                        alert("Invalid ID.");
                    }
                }
            });

        });

        // Update record 
        $('#SubmitUpdate').click(function (e) {

            e.preventDefault();

            var id = $('#txt_empid').val();
            var simStatus = $("#simStatus").val().trim();
            var renew = $("#renew").val().trim();
            var cost = $("#cost").val().trim();
            var simNo = $("#simNo").val().trim();
            var simPhoneNumber = $("#simPhoneNumber").val().trim();
            var simOperator = $("#simOperator").val().trim();
            var activateDate = $("#activateDate").val().trim();
            var custName = $("#custName").val().trim();
            var custAadharName = $("#custAadharName").val().trim();
            var custContactNumber = $("#custContactNumber").val().trim();
            var custAadharNumber = $("#custAadharNumber").val().trim();
            var custAddress = $("#custAddress").val().trim();
            var imei = $("#imei").val().trim();
            var unitSerialNumber = $("#unitSerialNumber").val().trim();
            var kycStatus = $("#kycStatus").val().trim();
            var payment = $("#payment").val().trim();
            var careOff = $("#careOff").val().trim();
            var note = $("#note").val().trim();
            var renewalDate = $("#renewalDate").val().trim();

            if (id != '' && simNo != '') {

                // AJAX request
                $.ajax({
                    url: "{{ route('updateSimInfo') }}",
                    type: 'post',
                    data: {
                        id: id,
                        simStatus: simStatus,
                        renew: renew,
                        cost: cost,
                        simNo: simNo,
                        simPhoneNumber: simPhoneNumber,
                        simOperator: simOperator,
                        activateDate: activateDate,
                        custName: custName,
                        custAadharName: custAadharName,
                        custContactNumber: custContactNumber,
                        custAadharNumber: custAadharNumber,
                        custAddress: custAddress,
                        imei: imei,
                        unitSerialNumber: unitSerialNumber,
                        kycStatus: kycStatus,
                        payment: payment,
                        careOff: careOff,
                        note: note,
                        renewalDate: renewalDate,
                    },
                    dataType: 'json',
                    success: function (response) {
                        alert(response.message);
                        $('#updateModal').modal('toggle');
                        $('#userSimTable').DataTable().ajax.reload()
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
        $('#userSimTable').on('click', '.deleteUserSim', function () {

            var id = $(this).data('id');

            var deleteConfirm = confirm("Are you sure?");
            if (deleteConfirm == true) {
                // AJAX request
                $.ajax({
                    url: "{{ route('deleteUserSimList') }}",
                    type: 'post',
                    data: {
                        _token: CSRF_TOKEN,
                        id: id
                    },
                    success: function (response) {
                        alert(response.message);
                        table.row(this).remove().draw(false);
                        userSimTable.ajax.reload();

                        //  window.location.reload();
                    },

                    error: function (response) {
                        alert(JSON.parse(response.responseText).message);
                    }
                });
            }

        });

    });


    $(function () {
        $(".static_class").click(function () {
            if ($(this).val() === "Yes")
                $("#extra").show("fast");
            else
                $("#extra").hide("fast");
        });
    });
</script>