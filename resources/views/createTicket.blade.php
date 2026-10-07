@extends('layouts.app')

@section('content')
    <style>
        /* Fix address select dropdown bold style */
        .card-header .select2-container {
            font-weight: normal !important;
        }
        /* Reduce gap between card header and selected address/content */
        .card > .card-body {
            padding-top: 10px !important;
        }
        #addressDiv .card-body {
            padding: 0 !important;
        }
        .form-group {
            margin-top: 0 !important;
            padding-top: 0 !important;
        }
        #addressDiv {
            margin-top: 0 !important;
        }
    </style>
    @include('modals/caseModal')
    @include('modals/categoryModal')
    @include('modals/addCustomerAddress')



    <div class="container-flux m-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">{{ __('Ticket Create') }}</h5>
    <div class="d-flex align-items-center gap-2">
        <select class="form-select form-select-sm w-auto me-2" name="categoryMobile1" id="categoryMobile1" autocomplete="off">
            <option selected disabled>Select Address</option>
            @foreach ($mobileNumber as $item)
                <option value="{{ $item->id }}">{{ $item->mobile1 }} - {{ $item->name }} - {{ $item->companyname }}</option>
            @endforeach
        </select>
        <button type="button" class="btn btn-primary btn-sm align-self-center" data-bs-toggle="modal" data-bs-target="#addNewAddressModal">Add Address</button>
    </div>
</div>
<div class="card-body">
                    

                    <!-- <div class="container"> -->
                    <div class='container-fluid form-group' >
                        <form name="add_item" id="add_item" autocomplete="off">
                            @csrf
                            <div style="display:none;" id="addressDiv">
    <input id="address_id" name="address_id" type="hidden"/>
        <div class="card-body p-0">
            <h5 class="card-title d-flex justify-content-between align-items-center">
                <span id="ct_name"></span>
                <a href="#" id="edit_address_btn" class="btn btn-sm btn-outline-danger" style="display: none;" title="Edit Address">
                    <i class="fa fa-edit"></i> Edit
                </a>
            </h5>
            <h6 class="card-subtitle mb-2 text-muted" id="ct_companyname"></h6>
            <p class="card-text mb-1"><i class="fa fa-phone me-2"></i><span id="ct_mobile1"></span> <span id="ct_mobile2"></span></p>
            <p class="card-text mb-1"><i class="fa fa-map-marker me-2"></i><span id="ct_address"></span>, <span id="ct_city"></span>, <span id="ct_district"></span>, <span id="ct_state"></span> - <span id="ct_pincode"></span></p>
            <p class="card-text mb-1"><i class="fa fa-id-card me-2"></i>GST NO - <span id="ct_gstno"></span></p>
            <div id="ct_metadata_container" style="display: none;">
                <hr class="my-2">
                <div class="d-flex justify-content-between text-muted" style="font-size: 12px;">
                    <span id="ct_created_info"></span>
                    <span id="ct_updated_info"></span>
                </div>
            </div>
        </div>
</div>



                                    <div class="table-responsive">
                                        <table class='table table-bordered table-hover' id="tab_logic">
                                            <thead>
                                                <tr class='info align-middle'>
                                                    <th style='width:7%;'>SL</th>
                                                    <th style='width:15%;'>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span>Case</span>
                                                            <a href="#" data-bs-toggle="modal" data-bs-target="#addCase">
                                                                <i class="fa fa-plus-circle" style="font-size:24px;color:green"></i>
                                                            </a>
                                                        </div>
                                                    </th>
                                                    <th style='width:15%;'>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span>Category</span>
                                                            <a href="#" data-bs-toggle="modal" data-bs-target="#addParent">
                                                                <i class="fa fa-plus-circle" style="font-size:24px;color:green"></i>
                                                            </a>
                                                        </div>
                                                    </th>
                                                    <th style='width:15%;'>
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span>Model</span>
                                                            <a href="#" id="addSubCat1Modal">
                                                                <i class="fa fa-plus-circle" style="font-size:24px;color:green"></i>
                                                            </a>
                                                        </div>
                                                    </th>
                                                    <th style='width:15%;'>Serial</th>
                                                    <th style='width:40%;'>Note</th>
                                                    <th style='width:10%;'>Action</th>
                                                </tr>
                                            </thead>
                                            <thead>
                                                <tr id="addr0" class="align-middle">
                                                    <td class="custom-tbl text-center">
                                                        <input class="form-control form-control-sm text-center" style="width:100%;" type="text" value="1" readonly>
                                                    </td>

                                                    <td class="custom-tbl">
                                                        <select class="browser-default custom-select case " data-id="1"
                                                            name="case[]" style='width:100%;' id="">
                                                            <option selected disabled>Select case</option>
                                                            @foreach ($cases as $case)
                                                                <option value='{{ $case->id }}'>{{ $case->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <!-- onChange="return categorym('1')" -->
                                                    <td class="custom-tbl">
                                                        <select class="browser-default custom-select categorym categorym1"
                                                            data-id="1" name="category[]" style='width:100%;'
                                                            id="">
                                                            <option selected disabled>Select category</option>
                                                            @foreach ($categoris as $item)
                                                                <option value='{{ $item->id }}'>{{ $item->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <!-- onChange="(sub1call($(this).val()))" -->
                                                    <td class="custom-tbl">
                                                        <select class="form-select product1 w-100" id="product1" name="product[]"></select>
                                                    </td>

                                                    <td class="custom-tbl"><input
                                                            class='serialValidate1 form-control form-control-sm'
                                                            style='width:100%;' type="text" id="serial0"
                                                            name="serial[]"
                                                            onkeypress="return /[0-9a-zA-Z]/i.test(event.key)"></td>

                                                    <td class="custom-tbl"><input class='form-control form-control-sm'
                                                            style='width:100%;' type="text" id="pr_desc0"
                                                            name="pr_desc[]"></td>

                                                    <td style="text-align: center" class="custom-tbl"><button
                                                            type="button" id="add"
                                                            class="btn btn-info btn-sm btn_add"><span
                                                                class="fa fa-plus"></span></button></td>
                                                </tr>
                                            </thead>
                                            <tbody id="dynamic_field">
                                            <tbody>
                                        </table>
                                    </div>
                                    <input id="assignUserStatus" name="assignUserStatus" type="hidden">

                                    <div class="col-md-12 form-group">
    <div class="modal-footer d-flex flex-wrap align-items-center justify-content-between">
        <div class="d-flex align-items-center mb-2 mb-md-0">
            <label class="me-2 mb-0">Assign Now</label>
            <div class="form-check form-check-inline mb-0">
                <input class="form-check-input" type="radio" name="actions" id="assignYes" value="AssignYes">
                <label class="form-check-label" for="assignYes">Yes</label>
            </div>
            <div class="form-check form-check-inline mb-0">
                <input class="form-check-input" type="radio" name="actions" id="assignNo" value="AssignNo" checked>
                <label class="form-check-label" for="assignNo">No</label>
            </div>
        </div>
        <div id="extra" class="me-2" style="display:none;">
            <select class="form-select" name="assignUserId" id="assignUserId">
                <option selected disabled>Select Technician</option>
                @foreach ($users as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="btn-group">
            <button type="button" id="submit" name="submit" class="btn btn-success btn_submit">Create</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="javascript:window.location.reload()">Reset</button>
        </div>
    </div>
</div>
</div>                                </div>
                            </div>

                        </form>
                    </div>
                </div></div></div>
            </div>
        </div>
    




    <!-- https://stackoverflow.com/questions/59814416/how-to-prevent-the-first-row-of-a-table-with-two-dropdown-from-dynamically-chang -->

    <!-- Add button function -->
    <script type="text/javascript">
        $(document).ready(function() {
            var postURL = "<?php echo url('addmore'); ?>";
            var i = 1;
            $('#add').click(function() {
                i++;
                $('#dynamic_field').append('<tr id="row' + i + '" class="dynamic-added align-middle">' +
                    '<td class="custom-tbl text-center"><input class="form-control form-control-sm text-center" style="width:100%;" type="text" value="' + i + '" readonly></td>' +
                    '<td class="custom-tbl">' +
                    '<select class="form-select case' + i + '" w-100 data-id="' + i + '" name="case[]" id="">' +
                    "<option selected disabled>Select case</option>" +
                    "@foreach ($cases as $case)" +
                    "<option value='{{ $case->id }}'>{{ $case->name }}</option>" +
                    "@endforeach" +
                    '</select></td>' +
                    '<td class="custom-tbl"><select class="form-select categorym categorym' + i + '" w-100 data-id="' + i + '" name="category[]" id="">' +
                    "<option selected disabled>Select category</option>" +
                    "@foreach ($categoris as $item)" +
                    "<option value='{{ $item->id }}'>{{ $item->name }}</option>" +
                    "@endforeach" +
                    '</select></td>' +
                    '<td class="custom-tbl"><select class="form-select product' + i + '" w-100 onChange="return sub1call(' + i + ')" name="product[]" id="product' + i + '"></select></td>' +
                    '<td class="custom-tbl"><input id="serial' + i +
                    '"class="serialValidate' + i +
                    ' form-control form-control-sm" style="width:100%;" type="text" name="serial[]" onkeypress="return /[0-9a-zA-Z]/i.test(event.key)"></td>' +
                    '<td class="custom-tbl"><input id="pr_desc' + i +
                    '" class="form-control form-control-sm" style="width:100%;" type="text" name="pr_desc[]"></td>' +
                    '<td style="text-align: center" class="custom-tbl"><button type="button" name="remove" id="' + i +
                    '" class="btn btn-danger btn-sm btn_remove">' +
                    '<span class="fa fa-minus"></span></button></td>' +
                    '</tr>');
                // i++;

                $('.case' + i).select2({});
                $('.categorym' + i).select2({});
                $('.product' + i).select2({});


                $('.serialValidate' + i).on('blur', function() {

                    var modelValue = $('#product' + i).val();
                    var serialValue = $(this).val();
                    var $inputField = $(this);

                    $.ajax({
                        url: '/validate-serial', // URL to the Laravel controller method
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}', // CSRF token for security
                            model: modelValue,
                            serial: serialValue,
                        },
                        success: function(response) {
                            if (response.valid) {
                                $('#submit').hide();
                                $('.btn_add').hide();
                                $inputField.css('background-color', 'lightcoral');
                                alert(response.ticket);
                            } else {
                                $('#submit').show();
                                $('.btn_add').show();
                                $inputField.css('background-color', 'lightgreen');
                                alert(response.ticket);
                            }
                        },
                        error: function() {
                            console.log('Seiral Number is empty');
                            // alert('An error occurred while validating the serial number');
                        }
                    });

                });

            });
            // onChange="return sub1call('+i+')"
            $(document).on('click', '.btn_remove', function() {
                var button_id = $(this).attr("id");
                $('#row' + button_id + '').remove();
            });
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $(document).on('click', '#submit', function() {
                // $('.btn_submit').attr('disabled', true);
                let addressId = $('#address_id').val();
                if (!addressId) {
                    alert("Please select an address first.");
                    return false;
                }

                $.ajax({
                    url: "{{ route('submitCreateTickets') }}",
                    method: "POST",
                    data: $('#add_item').serialize(),
                    type: 'json',
                    success: function(response) {
                        if (response.success == 'done') {
                            alert("Ticket Created successfully!");
                            // Reset the address dropdown to clear selected info
                            $('#categoryMobile1').val(null).trigger('change');
                            // Reset all Select2 elements on the page
                            $('select').val(null).trigger('change');
                            // Reset the standard form fields
                            $('#add_item')[0].reset();
                            window.location.reload();
                        }
                        if (response.error) {
                            alert(JSON.stringify(response.error));
                        }
                    },
                    error: function(response) {
                        try {
                            let res = JSON.parse(response.responseText);
                            alert(res.message || "An error occurred on the server.");
                        } catch (e) {
                            alert("Internal Server Error (500). Please check Laravel logs.");
                        }
                    },
                }, );
            });
        });



        // <!-- validate serial number using modal/product id -->
        $(document).ready(function() {

            $('.serialValidate1').on('blur', function() {
                var modelValue = $('#product1').val();
                var serialValue = $(this).val();
                var $inputField = $(this);

                $.ajax({
                    url: '/validate-serial', // URL to the Laravel controller method
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}', // CSRF token for security
                        model: modelValue,
                        serial: serialValue,
                    },
                    success: function(response) {
                        if (response.valid) {
                            $('#submit').hide();
                            $('.btn_add').hide();
                            $inputField.css('background-color', 'lightcoral');
                            alert(response.ticket);
                        } else {
                            $('#submit').show();
                            $('.btn_add').show();
                            $inputField.css('background-color', 'lightgreen');
                            alert(response.ticket);
                        }
                    },
                    error: function() {
                        console.log('Seiral Number is empty');
                        // alert('An error occurred while validating the serial number');
                    }
                });
            });
        });


        // <!-- Script for dropdown menu -->

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        // $(document).ready(function(){

        $(document).on('change', ".categorym", function(e) {

            var cat_id = $(this).val();
            var key = $(this).data('id');

            // alert(cat_id);
            $.ajax({
                url: "{{ route('subcat') }}",
                type: "POST",
                data: {
                    cat_id: cat_id
                },
                success: function(data) {
                    // var $SubC = $('#product' + j).empty();
                    // $('.product'+keyValue).empty().append();
                    $('.product' + key).empty().append($("<option selected disabled></option>").attr("value", '').text(
                        'Please Select'));
                    $.each(data.subcategories[0].subcategories, function(index, product) {
                        $('.product' + key).append('<option value="' + product.id + '">' +
                            product.name +
                            '</option>');
                    })
                }
            })

        });

        var j = 1;
        var k = 1;

        function sub1call(cid) {
            var cat_id = cid;
            $.ajax({
                url: "{{ route('subcat') }}",
                type: "POST",
                data: {
                    cat_id: cat_id
                },
                success: function(data) {
                    var $SubC1 = $('#sub2category' + j).empty();
                    $SubC1.append($("<option></option>").attr("value", '').text('Please Select'));
                    $.each(data.subcategories[0].subcategories, function(index, sub2category) {
                        $('#sub2category' + j).append('<option value="' + sub2category.id + '">' +
                            sub2category.name +
                            '</option>');
                    })
                    j++;
                    k++;
                }
            })
        };

        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $(document).ready(function() {

            $('#categoryMobile1').select2({});
            $('#assignUserId').select2({});
            $('.case').select2({});
            $('.categorym').select2({});
            $('.product1').select2({});

        });
        $(document).ready(function() {
            $('#categoryMobile1').change(function() {
                var vendorid = $(this).val();
                if (vendorid) {
                    // AJAX request
                    $.ajax({
                        url: "{{ route('createTicket.getVendorInfo') }}",
                        type: 'post',
                        data: {
                            _token: CSRF_TOKEN,
                            vendorid: vendorid,
                        },
                        dataType: 'json',
                        success: function(response) {
                            // console.log(response);
                            if (response.success == 1) {
                                $('#address_id').val(response.id);

                                $("#ct_name").text(response.name);

                                $('#ct_mobile1').text(response.mobile1);
                                $('#ct_mobile2').text(response.mobile2);
                                $('#ct_companyname').text(response.companyname);
                                $('#ct_address').text(response.address);
                                $('#ct_city').text(response.city);
                                $('#ct_district').text(response.district);
                                $('#ct_state').text(response.state);
                                $('#ct_pincode').text(response.pincode);
                                $('#ct_gstno').text(response.gstno);

                                if ((response.created_by && response.created_at) || (response.updated_by && response.updated_at)) {
                                    $('#ct_metadata_container').show();
                                    if (response.created_by && response.created_at) {
                                        $('#ct_created_info').html('Created by: ' + response.created_by + ' @ ' + response.created_at);
                                    } else {
                                        $('#ct_created_info').html('');
                                    }
                                    if (response.updated_by && response.updated_at) {
                                        $('#ct_updated_info').html('Updated by: ' + response.updated_by + ' @ ' + response.updated_at);
                                    } else {
                                        $('#ct_updated_info').html('');
                                    }
                                } else {
                                    $('#ct_metadata_container').hide();
                                }

                                $('#edit_address_btn').show();
                                //  $('#addresDiv').show();
                                $('#addressDiv').css('display', '')

                            } else {
                                alert("Invalid ID.");
                            }
                        }
                    });
                } else {
                    $('#addressDiv').css('display', 'none');
                    $('#edit_address_btn').hide();
                }
            });

            $(document).on('click', '#edit_address_btn', function(e) {
                e.preventDefault();
                var id = $('#address_id').val();
                if (!id) return;

                $("#addNewAddressModal #SubmitUpdate").show();
                $("#addNewAddressModal #submitCreate").hide();

                // AJAX request
                $.ajax({
                    url: "{{ route('getVendorAddressInfo') }}",
                    type: 'post',
                    data: {
                        _token: CSRF_TOKEN,
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
                            $('#pincode').val(response.pincode);
                            $('#gstno').val(response.gstno);
                            
                            // Show the modal
                            var editModal = new bootstrap.Modal(document.getElementById('addNewAddressModal'));
                            editModal.show();
                        }
                    }
                });
            });
        });


        // show and hide the Assign function menu
        $(function() {
            $("input[name='actions']").click(function() {
                if ($(this).val() === "AssignNo") {
                    $("#extra").hide("fast");
                    $("#assignUserStatus").val("");
                } else {
                    $("#extra").show("fast");
                    $("#assignUserStatus").val("AssignUserEnable");
                }
            });

            // Initial alignment check on page load / form reset
            if ($("input[name='actions']:checked").val() === "AssignYes") {
                $("#extra").show();
                $("#assignUserStatus").val("AssignUserEnable");
            } else {
                $("#extra").hide();
                $("#assignUserStatus").val("");
            }
        });
    </script>
@endsection
