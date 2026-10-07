
<style>
.ui-front {
    z-index: 5000;
}
#addNewAddressModal .modal-content {
    border: none;
    border-radius: 16px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    overflow: hidden;
}
#addNewAddressModal .modal-body {
    padding: 30px 24px;
    background-color: #f8f9fa;
}
#addNewAddressModal .form-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #495057;
    margin-bottom: 6px;
}
#addNewAddressModal .form-control, #addNewAddressModal .form-select {
    border-radius: 8px;
    border: 1px solid #ced4da;
    padding: 10px 14px;
    font-size: 0.95rem;
    background-color: white;
    transition: all 0.2s ease-in-out;
}
#addNewAddressModal .form-control:focus, #addNewAddressModal .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.15);
    background-color: white;
}
#addNewAddressModal .section-title {
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
#addNewAddressModal .modal-footer {
    border-top: 1px solid #dee2e6;
    padding: 16px 24px;
    background-color: white;
}
#addNewAddressModal .modal-footer .btn {
    padding: 10px 20px;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.2s;
}
#addNewAddressModal .modal-footer .btn-success {
    background-color: #198754;
    border: none;
}
#addNewAddressModal .modal-footer .btn-success:hover {
    background-color: #157347;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(25, 135, 84, 0.25);
}
#addNewAddressModal .modal-footer .btn-secondary {
    background-color: #6c757d;
    border: none;
}
#addNewAddressModal .modal-footer .btn-secondary:hover {
    background-color: #5c636a;
    transform: translateY(-1px);
}
</style>

<div class="modal fade" id="addNewAddressModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg ui-front" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-address-card me-2"></i>Address Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-success" role="alert" id="successMsg" style="display: none"> 
                    Added successfully!
                </div>
                
                <form id="SubmitFormAdd">
                    <!-- Section 1: Customer Info -->
                    <div class="section-title">General Information</div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label" for="companyname">Company Name</label>
                            <input type="text" placeholder="Enter Company Name" id="companyname" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="name">Contact Person Name</label>
                            <input type="text" placeholder="Enter Contact Person Name" id="name" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="mobile1">Mobile 1</label>
                            <input type="number" placeholder="Primary Mobile Number" id="mobile1" class="form-control" onkeypress="return /[0-9]/i.test(event.key)">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="mobile2">Mobile 2 (Optional)</label>
                            <input type="number" placeholder="Secondary Mobile Number" id="mobile2" class="form-control" onkeypress="return /[0-9]/i.test(event.key)">
                        </div>
                    </div>

                    <!-- Section 2: Address Info -->
                    <div class="section-title">Address Location</div>
                    <div class="row g-3 mb-2">
                        <div class="col-12">
                            <label class="form-label" for="address">Full Address</label>
                            <input type="text" placeholder="Enter Street, Building, Suite, etc." id="address" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="city">City</label>
                            <input type="text" placeholder="City" id="city" class="typeahead form-control city">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="district">District</label>
                            <input type="text" placeholder="District" id="district" class="typeahead form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="state">State</label>
                            <input type="text" placeholder="State" id="state" class="typeahead form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="pincode">Postcode / ZIP</label>
                            <input type="number" placeholder="Enter Post Code" id="pincode" class="form-control" onkeypress="return /[0-9]/i.test(event.key)">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="gstno">GST Number</label>
                            <input type="text" placeholder="GST Registration Number" id="gstno" class="form-control" onkeypress="return /[0-9a-zA-Z]/i.test(event.key)">
                        </div>
                    </div>

                    <input id="addr_id" type="hidden">
                </form>

                <div id="metadataContainer" style="display: none;">
                    <hr class="my-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap text-muted" style="font-size: 0.85rem;">
                        <div class="mb-1">
                            <i class="fa fa-user me-1 text-primary"></i> <strong>Created By:</strong> <span id="v_created_by_info" class="text-dark fw-semibold">NA</span>
                        </div>
                        <div class="mb-1">
                            <i class="fa fa-edit me-1 text-info"></i> <strong>Updated By:</strong> <span id="v_updated_by_info" class="text-dark fw-semibold">NA</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-success" id="SubmitUpdate" style="display:none;">Update Address</button>
                <button class="btn btn-success btn-submit" id="submitCreate">Create Address</button>
            </div>
        </div>
    </div>
</div>




<script type="text/javascript">

$('#addNewAddressModal').on('hidden.bs.modal', function () {
    $(this).find('form').trigger('reset'); // clear modal
    $('#metadataContainer').hide();
})


    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(".btn-submit").click(function (e) {

        e.preventDefault();

        var name = $("#name").val();
        var companyname = $("#companyname").val();
        var mobile1 = $("#mobile1").val();
        var mobile2 = $("#mobile2").val();
        var address = $("#address").val();
        var city = $("#city").val();
        var district = $("#district").val();
        var pincode = $("#pincode").val();
        var state = $("#state").val();
        var gstno = $("#gstno").val();

        $.ajax({
            type: 'POST',
            url: "{{ route('addNewVendor') }}",
            data: {
                name: name,
                companyname: companyname,
                mobile1: mobile1,
                mobile2: mobile2,
                address: address,
                city: city,
                district: district,
                pincode: pincode,
                state: state,
                gstno: gstno,
            },
            success: function (response) {
                $('#successMsg').show();
                $('#SubmitFormAdd').empty();

                console.log(response);
            },
            success: function (response) {
                alert(response.message);
                window.location.reload();
            },
            error: function (response) {
                alert(JSON.parse(response.responseText).message);
                // $('#nameErrorMsg').text(response.responseJSON.errors.name);
                // alert("Duplicate or Invalid entry, please check!")
            },
        });
    });

    // get vendor address into modal
    // CSRF Token
    $(document).ready(function () {
        // get record info
        $('#empTable').on('click', '.getAddress, .viewVendor', function () {
            var isViewOnly = $(this).hasClass('viewVendor') || $(this).data('view-only') == 1;

            if (isViewOnly) {
                $('#addNewAddressModal .modal-title').html('<i class="fa fa-eye me-2 text-info"></i>View Address Details');
                $('#addNewAddressModal input, #addNewAddressModal select, #addNewAddressModal textarea').prop('disabled', true);
                $('#addNewAddressModal #SubmitUpdate').hide();
                $('#addNewAddressModal #submitCreate').hide();
            } else {
                $('#addNewAddressModal .modal-title').html('<i class="fa fa-edit me-2 text-primary"></i>Update Address Details');
                $('#addNewAddressModal input, #addNewAddressModal select, #addNewAddressModal textarea').prop('disabled', false);
                $('#addNewAddressModal #SubmitUpdate').show();
                $('#addNewAddressModal #submitCreate').hide();
            }

            var id = $(this).data('id');

            // AJAX request
            $.ajax({
                url: "{{ route('getVendorAddressInfo') }}",
                type: 'post',
                data: {
                    id: id
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success == 1) {
                        $('#addr_id').val(response.id);
                        $('#name').val(response.name);
                        $('#companyname').val(response.companyname);
                        $('#mobile1').val(response.mobile1);
                        $('#mobile2').val(response.mobile2);
                        $('#address').val(response.address);
                        $('#city').val(response.city);
                        $('#district').val(response.district);
                        $('#state').val(response.state);
                        $('#gstno').val(response.gstno);
                        $('#pincode').val(response.pincode);

                        $('#v_created_by_info').text(response.created_by_info || 'NA');
                        $('#v_updated_by_info').text(response.updated_by_info || 'NA');
                        $('#metadataContainer').show();

                    } else {
                        alert("Invalid ID.");
                    }
                }
            });

        });



        // Update record
        $('#addNewAddressModal #SubmitUpdate').click(function (e) {
            e.preventDefault();

            var id = $('#addr_id').val();
            var name = $('#name').val().trim();
            var companyname = $('#companyname').val().trim();
            var address = $('#address').val().trim();
            var district = $('#district').val().trim();
            var city = $('#city').val().trim();
            var state = $('#state').val().trim();
            var pincode = $('#pincode').val().trim();
            var mobile1 = $('#mobile1').val().trim();
            var mobile2 = $('#mobile2').val().trim();
            var gstno = $('#gstno').val().trim();

            if (id != '' && name != '') {

                // AJAX request
                $.ajax({
                    url: "{{ route('updateVendorAddress') }}",
                    type: 'post',
                    data: {
                        id: id,
                        name: name,
                        companyname: companyname,
                        address: address,
                        district: district,
                        city: city,
                        state: state,
                        mobile1: mobile1,
                        mobile2: mobile2,
                        pincode: pincode,
                        gstno: gstno,
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success == 1) {
                            alert(response.msg);

                            $('#empTable').DataTable().ajax.reload();

                            // reset form
                            document.getElementById("SubmitFormAdd").reset();

                            // Close modal
                            $('#addNewAddressModal').modal('toggle');
                        } else {
                            //   alert(response.msg);
                            alert('Please verify data.');
                        }
                    },
                    error: function (response) {
                        alert("Duplicate entry, please check mobile number");
                    }

                });

            } else {
                alert('Please fill all fields.');
            }
        });

        var mytable = $('#ticketsList').DataTable();

        // Delete record
        $('#empTable').on('click', '.deleteVendorAdd', function () {
            var id = $(this).data('id');
            var deleteConfirm = confirm("Are you sure?");
            if (deleteConfirm == true) {
                // AJAX request
                $.ajax({
                    url: "{{ route('deleteVendorAddress') }}",
                    type: 'post',
                    data: {
                        _token: CSRF_TOKEN,
                        id: id
                    },
                    success: function (response) {
                        alert(response.message);
                        $('#empTable').DataTable().ajax.reload();
                    }
                });
            }

        });

    });

    var path1 = "{{ route('autocompleteCity') }}";

    $("#city").autocomplete({
        source: function (request, response) {
            $.ajax({
                url: path1,
                type: 'GET',
                dataType: "json",
                data: {
                    search: request.term
                },
                success: function (data) {
                    response(data);
                }
            });
        },
        select: function (event, ui) {
            var value = ui.item.value;
            $('#city').val(value);
            console.log(value);
            return false;
        }
    });
</script>





<script type="text/javascript">
    var path2 = "{{ route('autocompleteDistrict') }}";

    $("#district").autocomplete({
        source: function (request, response) {
            $.ajax({
                url: path2,
                type: 'GET',
                dataType: "json",
                data: {
                    search: request.term
                },
                success: function (data) {
                    response(data);
                }
            });
        },
        select: function (event, ui) {
            $('#district').val(ui.item.label);
            console.log(ui.item);
            return false;
        }
    });
</script>



<script type="text/javascript">
    var path3 = "{{ route('autocompleteState') }}";

    $("#state").autocomplete({
        source: function (request, response) {
            $.ajax({
                url: path3,
                type: 'GET',
                dataType: "json",
                data: {
                    search: request.term
                },
                success: function (data) {
                    response(data);
                }
            });
        },
        select: function (event, ui) {
            $('#state').val(ui.item.label);
            console.log(ui.item);
            return false;
        }
    });
</script>
