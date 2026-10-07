@extends('layouts.app')

@section('content')
    <div class='container-fluid ' style="width:95%">
        <Br>

        <style>
            p {
                margin-bottom: 0.3rem !important;
                margin-top: 0em;
                line-height: 1.4 !important;
            }
            .printDiv h5, .printDiv p, .printDiv span {
                line-height: 1.4 !important;
            }

            .vl {
    border-left: 4px solid black;
    height: 100%;
}
/* Flex layout for address rows */
/* Ensure the address row never wraps – keep From & To on the same line */
.printDiv {
    flex-wrap: nowrap !important;
}
/* Tighten column sizing and prevent Bootstrap flex‑grow from breaking layout */
.printDiv > .col-sm-5,
.printDiv > .col-sm-1 {
    flex: 0 0 auto !important;
    min-width: 0 !important;
    box-sizing: border-box;
}


/* Make cards borderless and transparent globally (on screen and print), hide headers */
.printDiv .card {
    height: 100%;
    display: flex;
    flex-direction: column;
    border: none !important;
    background: transparent !important;
    box-shadow: none !important;
}
.printDiv .card-header {
    display: none !important;
}
.printDiv .card-body {
    flex: 1 1 auto;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: 0 !important;
}
.printDiv .col-sm-5 {
    flex: 0 0 40% !important;
    max-width: 40% !important;
    width: 40% !important;
    display: flex;
    flex-direction: column !important;
    justify-content: space-between !important;
}
.printDiv .col-sm-1 {
    flex: 0 0 4% !important;
    max-width: 4% !important;
    width: 4% !important;
    display: flex;
    align-items: stretch !important;
    justify-content: center !important;
}
            /* Ensure card text wraps and columns stay equal height */
            .printDiv .card-text {
                word-break: break-word;
            }
            .printDiv .card-title {
                word-break: break-word;
            }
            .printDiv > .col-sm-5 {
                align-self: stretch;
            }
            .printDiv.portrait-font .card, .printDiv.portrait-font .card-body, .printDiv.portrait-font .card-title, .printDiv.portrait-font p, .printDiv.portrait-font span { font-size: 13px !important; }
            .printDiv.landscape-font .card, .printDiv.landscape-font .card-body, .printDiv.landscape-font .card-title, .printDiv.landscape-font p, .printDiv.landscape-font span { font-size: 11px !important; }


            @media print {
                .no-print { display: none !important; }
                .address-box { width: 100%; margin: 0 auto 1rem auto; page-break-inside: avoid; }
                .printDiv { display: flex !important; justify-content: space-between !important; align-items: stretch !important; page-break-inside: avoid !important; flex-wrap: nowrap !important; }
                
                .printDiv .col-sm-5 { 
                    flex: 0 0 40% !important; 
                    max-width: 40% !important; 
                    width: 40% !important; 
                    display: flex; 
                    flex-direction: column !important; 
                    justify-content: space-between !important; 
                }
                
                .printDiv .col-sm-1 { 
                    flex: 0 0 4% !important; 
                    max-width: 4% !important; 
                    width: 4% !important; 
                    display: flex; 
                    align-items: stretch !important; 
                    justify-content: center !important;
                }
                
                .vl {
                    height: 100%;
                    border-left: 4px solid black !important;
                    align-self: stretch !important;
                    margin: 0 auto !important;
                }
                
                /* Hide cards border/header on print */
                .printDiv .card {
                    border: none !important;
                    background: transparent !important;
                    box-shadow: none !important;
                }
                .printDiv .card-header {
                    display: none !important;
                }
                .printDiv .card-body {
                    padding: 0 !important;
                }
                
                /* unified spacing for From and To cards */
                .printDiv #fromCard {
                    margin-right: 0.4rem !important;
                }
                .printDiv #toCard {
                    margin-left: 0.4rem !important;
                }
                .printDiv #fromCardBar {
                    margin: 0 0.2rem !important;
                }
                
                .printDiv.portrait-font .card, .printDiv.portrait-font .card-body, .printDiv.portrait-font .card-title, .printDiv.portrait-font p, .printDiv.portrait-font span { font-size: 13px !important; }
                .printDiv.landscape-font .card, .printDiv.landscape-font .card-body, .printDiv.landscape-font .card-title, .printDiv.landscape-font p, .printDiv.landscape-font span { font-size: 11px !important; }
            }
            
            .col-sm-1 {
              flex: 0 0 auto;
              width: 2%;
            }
        </style>

        <script>
            function previewPrint() {
    // Use Print.js to show a preview and then print the .printDiv element
    if (typeof printJS !== 'function') {
        console.error('Print.js library not loaded');
        return;
    }
    // Apply current orientation classes before printing
    var orientation = $('#orientationSelect').val();
    $('.printDiv').removeClass('portrait-font landscape-font').addClass(orientation + '-font');
    // Print the element using element reference; enable style scanning so CSS is applied in the preview/print
    var printElem = document.querySelector('.printDiv');
    printJS({
        printable: printElem,
        type: 'html',
        scanStyles: true, // allow Print.js to include page styles
        targetStyles: ['*']
    });
}

function updateOrientation() {
    var orientation = $('#orientationSelect').val();
    var maxRows = orientation === 'portrait' ? 4 : 2; // total rows to display
    // Base rows (static address-box) always show first two rows (From & To)
    $('.address-box').hide();
    $('.address-box').slice(0, 2).show();
    // Show dynamic rows up to the allowed total rows minus the two base rows
    var extraRows = maxRows - 2;
    if (extraRows > 0) {
        $('.dynamic-added').hide();
        $('.dynamic-added').slice(0, extraRows).show();
    } else {
        $('.dynamic-added').hide();
    }
    // Adjust font size via CSS class
    $('.printDiv').removeClass('portrait-font landscape-font').addClass(orientation + '-font');
}

// Initialise orientation on page load
$(document).ready(function() {
    updateOrientation();
});


        </script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/print-js/1.6.0/print.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/print-js/1.6.0/print.min.js"></script>
        <div class="container noprint" style="color:black;font-size:22px;">
            <div class="row align-items-center mb-3">
                <div class='col-md-4'>
                    <div class="form-group mb-0" style='width:100%;'>
                        <select name="filter_company" class="browser-default form-control filter_company select2"
                            data-id="1" style="width: 100%;" required>
                            <option value="">Select Company</option>
                            @foreach ($company_name as $company)
                                <option value="{{ $company->id }}">{{ $company->companyname }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class='col-md-4'>
                    <div class="form-group mb-0" style="width:100%;">
                        <select class="browser-default select2 vedorAddress" name="vedorAddress" data-id="1" style="width: 100%;">
                            <option selected value="">Select Vendor</option>
                            @foreach ($mobileNumber as $item)
                                <option value="{{ $item->id }}">{{ $item->mobile1 }} - {{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class='col-md-4 d-flex justify-content-end align-items-center gap-2'>
                    <button type="button" id="add" class="btn btn-info btn-sm"><i class="fa fa-plus"></i></button>
                    <button type="button" class="btn btn-success btn-sm" onclick="previewPrint();"><i class="fa fa-print"></i></button>
                    <select id="orientationSelect" class="form-select form-select-sm ms-2" style="width:120px;" onchange="updateOrientation();">
                        <option value="portrait">Portrait</option>
                        <option value="landscape">Landscape</option>
                    </select>
                </div>
            </div>
            <Br>
            <style>
                .label {
                    comp_ margin-right: 10px;
                    /* Adjust this value to add more or less space */
                }
            </style>
<div class="container" style="color:black;font-size:22px;">
            <div class="row">
                <div class="address-box">
                    <div class="row printDiv">
                        <div class="col-sm-5 d-none" id="fromCard1">
                            <div class="card border-dark mb-3" style="max-width: 45rem;">
                                <div class="card-header">From</div>
                                <div class="card">
                                    <div class="card-body liveskytech1">
                                        <h5 class="card-title" style="color:black;font-size:22px;">
                                            <span id="comp_companyname1"></span>
                                        </h5>
                                        <p class="card-text" id="comp_address1"></p>
                                        <p class="card-text" id="comp_district1"></p>
                                        <p class="card-text"><span id="comp_state1"></span> - <span id="comp_pincode1"></span></p>
                                        <p class="card-text">Phone : <span id="comp_mobile11"></span> , <span id="comp_mobile21"></span></p>
                                        <p class="card-text" id="tagGst" style="display:none">GST : <span id="comp_gstno1"></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                <div class="col-sm-1 d-none" id="fromCardBar1">
                    <div class="vl"></div>
                </div>

                <div class="col-sm-5 d-none" id="toCard1">
                    <div class="card border-dark mb-3" style="max-width: 45rem;">
                        <div class="card-header">To</div>
                        <div class="card">
                            <div class="card-body">
                                <!-- <span id="vendor_name1"></span>- -->
                                <h5 class="card-title" style="color:black;font-size:22px;">
                                    <span id="vendor_companyname1"></span>
                                </h5>
                                <p class="card-text" id="vendor_address1"></p>
                                <p class="card-text" id="vendor_city1"></p>
                                <p class="card-text" id="vendor_district1"></p>
                                <p class="card-text"><span id="vendor_state1"></span> - <span id="vendor_pincode1"></span>
                                </p>
                                <p class="card-text">Phone : <span id="vendor_mobile11"></span> , <span
                                        id="vendor_mobile21"></span></p>
                                <p class="card-text" id="tagGst" style="display:none">GST : <span
                                        id="vendor_gstno1"></span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
          </div>

            <!-- </div> -->

            <!-- <div class="container"> -->

            <div id="dynamic_field">
                <div>



                    <!-- https://stackoverflow.com/questions/59814416/how-to-prevent-the-first-row-of-a-table-with-two-dropdown-from-dynamically-chang -->

                    <!-- Add button function -->

                    <script type="text/javascript">
                        $(document).ready(function() {
                            var postURL = "<?php echo url('addmore'); ?>";
                            var i = 2;
                            $('#add').click(function() {
                                i++;
                                $('#dynamic_field').append(
                                    '<div style="color:black;font-size:22px;"><div id="row' + i +
                                    '" class="dynamic-added mb-3">' +
                                    '<div class="row align-items-center">' +
                                    '<div class="col-md-4">' +
                                    '<div class="noprint form-group mb-0" style="width:100%;">' +
                                    '<select class="browser-default select2 filter_company" name="filter_company" data-id="' + i + '" style="width: 100%;">' +
                                    '<option selected value="">Select Company</option>' +
                                    '@foreach ($company_name as $company)' +
                                    '<option value="{{ $company->id }}">{{ $company->companyname }}</option>' +
                                    '@endforeach' +
                                    '</select>' +
                                    '</div>' +
                                    '</div>' +
                                    '<div class="col-md-4">' +
                                    '<div class="noprint form-group mb-0" style="width:100%;">' +
                                    '<select class="browser-default select2 vedorAddress" name="vedorAddress" data-id="' + i + '" style="width: 100%;">' +
                                    '<option selected value="">Select Vendor</option>' +
                                    '@foreach ($mobileNumber as $item)' +
                                    '<option value="{{ $item->id }}">{{ $item->mobile1 }} - {{ $item->name }}</option>' +
                                    '@endforeach' +
                                    '</select>' +
                                    '</div>' +
                                    '</div>' +
                                    '<div class="col-md-4 d-flex justify-content-end align-items-center">' +
                                    '<button type="button" name="remove" id="' + i + '" class="btn btn-danger btn-sm btn_remove" title="Remove Row"><i class="fa fa-minus"></i> Remove</button>' +
                                    '</div>' +
                                    '</div>' +
                                    '<Br>' +
                                    '<div class="row printDiv">' +
                                    '<div class="col-sm-5 d-none" id="fromCard' + i + '">' +
                                    '<div class="card border-dark mb-3" style="max-width: 35rem;">' +
                                    '<div class="card-header">From</div>' +
                                    '<div class="card">' +
                                    '<div class="card-body company' + i + '">' +
                                    '<h5 class="card-title" style="color:black;font-size:22px;"><span id="comp_companyname' +
                                    i + '"></span></h5>' +
                                    '<p class="card-text" id="comp_address' + i + '"></p>' +
                                    '<p class="card-text" id="comp_district' + i + '"></p>' +
                                    '<p class="card-text"><span id="comp_state' + i +
                                    '"></span> - <span id="comp_pincode' + i +
                                    '"></span></p>' +
                                    '<p class="card-text">Phone : <span id="comp_mobile1' + i +
                                    '"></span> , <span id="comp_mobile2' + i +
                                    '"></span></p>' +
                                    '<p class="card-text" id="tagGst' + i +
                                    '" style="display:none">GST : <span id="comp_gstno' + i + '">' +
                                    '</div>' +
                                    '</div>' +
                                    '</div>' +
                                    '</div>' +
                                    '<div class="col-sm-1 d-none" id="fromCardBar' + i + '">' +
                                    '<div class="vl"></div>' +
                                    '</div>' +
                                    '<div class="col-sm-5 d-none" id="toCard' + i + '">' +
                                    '<div class="card border-dark mb-3" style="max-width: 35rem;">' +
                                    '<div class="card-header">To</div>' +
                                    '<div class="card">' +
                                    '<div class="card-body">' +
                                    // '<span id="vendor_name' + i + '"></span>' +
                                    '<h5 class="card-title" style="color:black;font-size:22px;"><span id="vendor_companyname' +
                                    i + '"></span></h5>' +
                                    '<p class="card-text" id="vendor_address' + i + '"></p>' +
                                    '<p class="card-text" id="vendor_city' + i + '"></p>' +
                                    '<p class="card-text" id="vendor_district' + i + '"></p>' +
                                    '<p class="card-text"><span id="vendor_state' + i +
                                    '"></span> - <span id="vendor_pincode' + i +
                                    '"></span></p>' +
                                    '<p class="card-text">Phone : <span id="vendor_mobile1' + i +
                                    '"></span> , <span id="vendor_mobile2' + i +
                                    '"></span></p>' +
                                    '<p class="card-text" id="tagGst' + i +
                                    '" style="display:none">GST : <span id="vendor_gstno' + i + '">' +
                                    '</div>' +
                                    '</div>' +
                                    '</div>' +
                                    '</div>' +
                                    '</div>');
                                i++;
                                $('.select2').select2({});
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

                        });
                    </script>

                    <!-- Script for dropdown menu -->

                    <script type="text/javascript">
                        let getName;

                        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

                        $(document).ready(function() {

                            $('.vedorAddress').select2({});

                            $(document).on('change', ".vedorAddress", function(e) {
                                // $('#vedorAddress').change(function() {
                                var key = $(this).attr('data-id');
                                var vendorid = $(this).val();
                                // alert(key);
                                // $('.categoryMobile1').select2({});
                                // alert(key);
                                if (vendorid != '' && vendorid != 'Select Vendor') {
                                    // AJAX request
                                    $.ajax({
                                        url: "{{ route('printAddress.getVendorInfo') }}",
                                        type: 'post',
                                        data: {
                                            _token: CSRF_TOKEN,
                                            vendorid: vendorid,
                                        },
                                        dataType: 'json',
                                        success: function(response) {
                                            if (response.success == 1) {
                                                $("#toCard" + key).removeClass("d-none");
                                                $("#fromCardBar" + key).removeClass("d-none");
                                                $('#address_id' + key).val(response.id);
                                                $('#vendor_name' + key).html(response.name);
                                                $('#vendor_companyname' + key).html(response.companyname);
                                                $('#vendor_mobile1' + key).html(response.mobile1);
                                                $('#vendor_mobile2' + key).html(response.mobile2);
                                                $('#vendor_address' + key).html(response.address);
                                                $('#vendor_city' + key).html(response.city);
                                                $('#vendor_district' + key).html(response.district);
                                                $('#vendor_state' + key).html(response.state);
                                                $('#vendor_pincode' + key).html(response.pincode);
                                                if (response.gstno != null) {
                                                    $("#tagGst").css("display", "block");
                                                    $("#tagGst" + key).css("display", "block");
                                                    $('#vendor_gstno' + key).html(response.gstno);
                                                }
                                            } else {
                                                alert("Invalid ID.");
                                            }
                                        }
                                    });
                                } else {
                                    $("#toCard" + key).addClass("d-none");
                                    $("#fromCardBar" + key).addClass("d-none");
                                }
                            });
                        });




                        $(document).on('change', ".filter_company", function(e) {

                            var id = $(this).val();
                            var key = $(this).attr('data-id');

                            if (id != '' && id != 'Select Company') {
                                $.ajax({
                                    url: "{{ route('getVendorAddressInfo') }}",
                                    type: "POST",
                                    data: {
                                        _token: CSRF_TOKEN,
                                        id: id
                                    },
                                    dataType: 'json',

                                    success: function(response) {
                                        $("#fromCard" + key).removeClass("d-none");
                                        $("#fromCardBar" + key).removeClass("d-none");

                                        $('#comp_companyname' + key).html(response.companyname);
                                        $('#comp_mobile1' + key).html(response.mobile1);
                                        $('#comp_mobile2' + key).html(response.mobile2);
                                        $('#comp_address' + key).html(response.address);
                                        $('#comp_city' + key).html(response.city);
                                        $('#comp_district' + key).html(response.district);
                                        $('#comp_state' + key).html(response.state);
                                        $('#comp_pincode' + key).html(response.pincode);
                                        if (response.gstno != null) {
                                            $("#tagGst").css("display", "block");
                                            $("#tagGst" + key).css("display", "block");
                                            $('#comp_gstno' + key).html(response.gstno);
                                        }
                                    }
                                });
                            } else {
                                $("#fromCard" + key).addClass("d-none");
                                $("#fromCardBar" + key).addClass("d-none");
                            }

                        });
                    </script>
                @endsection
