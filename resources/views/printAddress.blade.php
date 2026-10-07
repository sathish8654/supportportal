@extends('layouts.app')

@section('content')
<style>
    /* Screen Control Bar Styles */
    .control-panel-card {
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        border: 1px solid #e2e8f0;
        background: #ffffff;
    }
    
    /* Parcel Cover Shipping Label Box Styles */
    .parcel-label-card {
        border: 2px solid #1a202c;
        border-radius: 10px;
        background: #ffffff;
        padding: 16px 20px;
        margin-bottom: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.04);
        position: relative;
        transition: all 0.2s ease;
    }
    
    .label-print-box {
        display: flex;
        justify-content: space-between;
        align-items: stretch;
        width: 100%;
        gap: 15px;
    }
    
    .address-column {
        flex: 0 0 47.5%;
        max-width: 47.5%;
        display: flex;
        flex-direction: column;
        position: relative;
        padding: 12px 16px;
        border-radius: 8px;
        background: #f8fafc;
        border: 1px solid #cbd5e1;
    }
    
    .address-badge {
        font-size: 0.8rem;
        font-weight: 800;
        letter-spacing: 1.5px;
        padding: 3px 10px;
        border-radius: 4px;
        display: inline-block;
        width: fit-content;
        margin-bottom: 8px;
        text-transform: uppercase;
    }
    
    .badge-from {
        background: #e2e8f0;
        color: #1e293b;
        border: 1px solid #94a3b8;
    }
    
    .badge-to {
        background: #0d6efd;
        color: #ffffff;
        border: 1px solid #0a58ca;
    }
    
    .address-content {
        flex: 1;
    }
    
    .company-title {
        font-size: 1.2rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 4px;
        word-break: break-word;
        line-height: 1.25;
    }
    
    .contact-name {
        font-size: 0.95rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 6px;
    }
    
    .address-line {
        font-size: 0.92rem;
        color: #1e293b;
        margin-bottom: 4px;
        word-break: break-word;
        line-height: 1.35;
    }
    
    .address-location {
        font-size: 0.92rem;
        color: #1e293b;
        margin-bottom: 6px;
        line-height: 1.35;
    }
    
    .address-phone, .address-gst {
        font-size: 0.88rem;
        color: #0f172a;
        font-weight: 600;
        margin-top: 2px;
    }
    
    .divider-column {
        flex: 0 0 3%;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    .vertical-divider {
        height: 100%;
        width: 2px;
        border-left: 2px dashed #64748b;
    }
    
    /* Layout Scaling Modes for Screen & Print */
    /* Mode Portrait: Fits up to 4 rows per page */
    .mode-portrait .parcel-label-card {
        margin-bottom: 12px;
        padding: 10px 14px;
    }
    .mode-portrait .company-title { font-size: 1.05rem; }
    .mode-portrait .contact-name { font-size: 0.88rem; }
    .mode-portrait .address-line, 
    .mode-portrait .address-location { font-size: 0.85rem; line-height: 1.25; }
    .mode-portrait .address-phone, 
    .mode-portrait .address-gst { font-size: 0.8rem; }

    /* Mode Landscape: Fits up to 2 rows per page */
    .mode-landscape .parcel-label-card {
        margin-bottom: 20px;
        padding: 18px 24px;
    }
    .mode-landscape .company-title { font-size: 1.35rem; }
    .mode-landscape .contact-name { font-size: 1.05rem; }
    .mode-landscape .address-line, 
    .mode-landscape .address-location { font-size: 1.02rem; line-height: 1.4; }
    .mode-landscape .address-phone, 
    .mode-landscape .address-gst { font-size: 0.95rem; }

    /* Dynamic Page Print Orientation Style (Injected via JS) */
</style>
<style id="dynamicPagePrintStyle">
    @media print {
        @page {
            size: portrait;
            margin: 8mm;
        }
    }
</style>
<style>
    /* Print Specific Media Styles */
    @media print {
        body {
            background: #ffffff !important;
            color: #000000 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        .noprint, .no-print, header, footer, nav, .navbar, .card-header, #sidebar, .btn, .control-panel-card {
            display: none !important;
        }

        .parcel-label-card {
            border: 2px solid #000000 !important;
            box-shadow: none !important;
            background: #ffffff !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        .address-column {
            border: 1px solid #666666 !important;
            background: #ffffff !important;
        }

        .badge-from {
            border: 1px solid #000000 !important;
            background: #e2e8f0 !important;
            color: #000000 !important;
        }

        .badge-to {
            border: 1px solid #000000 !important;
            background: #000000 !important;
            color: #ffffff !important;
        }

        .vertical-divider {
            border-left: 2px dashed #000000 !important;
        }
    }
</style>

<div class="container-fluid py-3 px-4" style="max-width: 1400px;">
    <!-- Top Control Bar (Non-Printable) -->
    <div class="card control-panel-card noprint mb-4">
        <div class="card-body p-3">
            <div class="row align-items-center g-3">
                <div class="col-md-4">
                    <h5 class="mb-0 fw-bold text-primary"><i class="fa fa-print me-2"></i>Parcel Address Cover Printing</h5>
                    <span class="text-muted small">Generate From & To shipping labels to paste on parcel covers</span>
                </div>
                <div class="col-md-8 d-flex justify-content-end align-items-center gap-2">
                    <select id="orientationSelect" class="form-select form-select-sm" style="width: auto;" onchange="updateOrientation();">
                        <option value="portrait" selected>Portrait (4 Rows)</option>
                        <option value="landscape">Landscape (2 Rows)</option>
                    </select>
                    <button type="button" id="add" class="btn btn-outline-primary btn-sm px-3 fw-bold">
                        <i class="fa fa-plus me-1"></i> Add Parcel Row
                    </button>
                    <button type="button" class="btn btn-success btn-sm px-4 fw-bold" onclick="window.print();">
                        <i class="fa fa-print me-1"></i> Print Labels
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Parcel Labels Container -->
    <div id="addressContainer" class="mode-portrait">
        
        <!-- Row 1 (Static Initial Row) -->
        <div class="parcel-label-card" id="parcelRow1">
            <!-- Non-printable Dropdown Selection for Row 1 -->
            <div class="noprint row align-items-center g-3 mb-3 pb-3 border-bottom">
                <div class="col-md-5">
                    <label class="form-label text-secondary small fw-bold mb-1"><i class="fa fa-building me-1"></i>FROM (Company Address)</label>
                    <select name="filter_company" class="form-control filter_company select2" data-id="1" style="width: 100%;">
                        <option value="">Select Company</option>
                        @foreach ($company_name as $company)
                            <option value="{{ $company->id }}">{{ $company->companyname }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label text-secondary small fw-bold mb-1"><i class="fa fa-truck me-1"></i>TO (Vendor / Recipient Address)</label>
                    <select class="form-control select2 vedorAddress" name="vedorAddress" data-id="1" style="width: 100%;">
                        <option selected value="">Select Vendor</option>
                        @foreach ($mobileNumber as $item)
                            <option value="{{ $item->id }}">{{ $item->mobile1 }} - {{ $item->name }} - {{ $item->companyname }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 text-end align-self-end">
                    <span class="badge bg-secondary">Parcel #1</span>
                </div>
            </div>

            <!-- Printable Label Pair 1 -->
            <div class="label-print-box printDiv">
                <!-- FROM Card -->
                <div class="address-column from-column" id="fromCard1">
                    <div class="address-badge badge-from">FROM:</div>
                    <div class="address-content">
                        <h5 class="company-title" id="comp_companyname1">Select Company</h5>
                        <div class="contact-name" id="comp_contact_name1"></div>
                        <div class="address-line" id="comp_address1"></div>
                        <div class="address-location">
                            <span id="comp_city1"></span><span id="comp_district_wrapper1" style="display:none;">, <span id="comp_district1"></span></span><br>
                            <span id="comp_state1"></span> <span id="comp_pincode_wrapper1" style="display:none;">- <strong><span id="comp_pincode1"></span></strong></span>
                        </div>
                        <div class="address-phone" id="comp_phone_wrapper1" style="display:none;">Ph: <span id="comp_mobile11"></span><span id="comp_mobile2_wrapper1" style="display:none;"> / <span id="comp_mobile21"></span></span></div>
                        <div class="address-gst" id="comp_tagGst1" style="display:none;">GSTIN: <span id="comp_gstno1"></span></div>
                    </div>
                </div>

                <!-- Vertical Divider -->
                <div class="divider-column" id="fromCardBar1">
                    <div class="vertical-divider"></div>
                </div>

                <!-- TO Card -->
                <div class="address-column to-column" id="toCard1">
                    <div class="address-badge badge-to">TO:</div>
                    <div class="address-content">
                        <h5 class="company-title" id="vendor_companyname1">Select Vendor</h5>
                        <div class="contact-name" id="vendor_name1"></div>
                        <div class="address-line" id="vendor_address1"></div>
                        <div class="address-location">
                            <span id="vendor_city1"></span><span id="vendor_district_wrapper1" style="display:none;">, <span id="vendor_district1"></span></span><br>
                            <span id="vendor_state1"></span> <span id="vendor_pincode_wrapper1" style="display:none;">- <strong><span id="vendor_pincode1"></span></strong></span>
                        </div>
                        <div class="address-phone" id="vendor_phone_wrapper1" style="display:none;">Ph: <span id="vendor_mobile11"></span><span id="vendor_mobile2_wrapper1" style="display:none;"> / <span id="vendor_mobile21"></span></span></div>
                        <div class="address-gst" id="tagGst1" style="display:none;">GSTIN: <span id="vendor_gstno1"></span></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dynamic Additional Rows -->
        <div id="dynamic_field"></div>
    </div>
</div>

<script type="text/javascript">
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    var i = 1;

    function updateOrientation() {
        var orientation = $('#orientationSelect').val();
        var maxRows = orientation === 'portrait' ? 4 : 2;
        
        // Dynamically inject CSS @page rule to set actual printer orientation
        $('#dynamicPagePrintStyle').html('@media print { @page { size: ' + orientation + '; margin: 8mm; } }');

        // Update layout mode on main container
        $('#addressContainer').removeClass('mode-portrait mode-landscape').addClass('mode-' + orientation);

        // Manage row visibility based on max allowed rows
        $('.parcel-label-card').each(function(index) {
            if (index < maxRows) {
                $(this).removeClass('d-none');
            } else {
                $(this).addClass('d-none');
            }
        });
    }

    $(document).ready(function() {
        $('.select2').select2({});
        updateOrientation();

        // Add New Parcel Row
        $('#add').click(function() {
            var orientation = $('#orientationSelect').val();
            var maxRows = orientation === 'portrait' ? 4 : 2;
            var currentCount = $('.parcel-label-card').length;

            if (currentCount >= maxRows) {
                alert('In ' + orientation + ' mode, maximum ' + maxRows + ' parcel rows are supported per page.');
                return false;
            }

            i++;
            var htmlRow = `
            <div class="parcel-label-card" id="parcelRow${i}">
                <div class="noprint row align-items-center g-3 mb-3 pb-3 border-bottom">
                    <div class="col-md-5">
                        <label class="form-label text-secondary small fw-bold mb-1"><i class="fa fa-building me-1"></i>FROM (Company Address)</label>
                        <select class="form-control select2 filter_company" name="filter_company" data-id="${i}" style="width: 100%;">
                            <option selected value="">Select Company</option>
                            @foreach ($company_name as $company)
                                <option value="{{ $company->id }}">{{ $company->companyname }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label text-secondary small fw-bold mb-1"><i class="fa fa-truck me-1"></i>TO (Vendor / Recipient Address)</label>
                        <select class="form-control select2 vedorAddress" name="vedorAddress" data-id="${i}" style="width: 100%;">
                            <option selected value="">Select Vendor</option>
                            @foreach ($mobileNumber as $item)
                                <option value="{{ $item->id }}">{{ $item->mobile1 }} - {{ $item->name }} - {{ $item->companyname }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 text-end align-self-end">
                        <button type="button" class="btn btn-outline-danger btn-sm btn_remove fw-bold" data-id="${i}" title="Remove Row">
                            <i class="fa fa-trash me-1"></i> Remove
                        </button>
                    </div>
                </div>

                <div class="label-print-box printDiv">
                    <div class="address-column from-column" id="fromCard${i}">
                        <div class="address-badge badge-from">FROM:</div>
                        <div class="address-content">
                            <h5 class="company-title" id="comp_companyname${i}">Select Company</h5>
                            <div class="contact-name" id="comp_contact_name${i}"></div>
                            <div class="address-line" id="comp_address${i}"></div>
                            <div class="address-location">
                                <span id="comp_city${i}"></span><span id="comp_district_wrapper${i}" style="display:none;">, <span id="comp_district${i}"></span></span><br>
                                <span id="comp_state${i}"></span> <span id="comp_pincode_wrapper${i}" style="display:none;">- <strong><span id="comp_pincode${i}"></span></strong></span>
                            </div>
                            <div class="address-phone" id="comp_phone_wrapper${i}" style="display:none;">Ph: <span id="comp_mobile1${i}"></span><span id="comp_mobile2_wrapper${i}" style="display:none;"> / <span id="comp_mobile2${i}"></span></span></div>
                            <div class="address-gst" id="comp_tagGst${i}" style="display:none;">GSTIN: <span id="comp_gstno${i}"></span></div>
                        </div>
                    </div>

                    <div class="divider-column" id="fromCardBar${i}">
                        <div class="vertical-divider"></div>
                    </div>

                    <div class="address-column to-column" id="toCard${i}">
                        <div class="address-badge badge-to">TO:</div>
                        <div class="address-content">
                            <h5 class="company-title" id="vendor_companyname${i}">Select Vendor</h5>
                            <div class="contact-name" id="vendor_name${i}"></div>
                            <div class="address-line" id="vendor_address${i}"></div>
                            <div class="address-location">
                                <span id="vendor_city${i}"></span><span id="vendor_district_wrapper${i}" style="display:none;">, <span id="vendor_district${i}"></span></span><br>
                                <span id="vendor_state${i}"></span> <span id="vendor_pincode_wrapper${i}" style="display:none;">- <strong><span id="vendor_pincode${i}"></span></strong></span>
                            </div>
                            <div class="address-phone" id="vendor_phone_wrapper${i}" style="display:none;">Ph: <span id="vendor_mobile1${i}"></span><span id="vendor_mobile2_wrapper${i}" style="display:none;"> / <span id="vendor_mobile2${i}"></span></span></div>
                            <div class="address-gst" id="tagGst${i}" style="display:none;">GSTIN: <span id="vendor_gstno${i}"></span></div>
                        </div>
                    </div>
                </div>
            </div>`;

            $('#dynamic_field').append(htmlRow);
            $('.select2').select2({});
            updateOrientation();
        });

        // Remove Row
        $(document).on('click', '.btn_remove', function() {
            var rowId = $(this).attr("data-id");
            $('#parcelRow' + rowId).remove();
            updateOrientation();
        });

        // Vendor Select (TO Address)
        $(document).on('change', ".vedorAddress", function(e) {
            var key = $(this).attr('data-id');
            var vendorid = $(this).val();

            if (vendorid != '' && vendorid != 'Select Vendor') {
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
                            var displayCompany = response.companyname ? response.companyname : (response.name || '');
                            var displayName = (response.name && response.companyname && response.name !== response.companyname) ? ('Attn: ' + response.name) : '';
                            
                            $('#vendor_companyname' + key).text(displayCompany);
                            $('#vendor_name' + key).text(displayName);
                            $('#vendor_address' + key).text(response.address || '');
                            $('#vendor_city' + key).text(response.city || '');

                            if (response.district) {
                                $('#vendor_district' + key).text(response.district);
                                $('#vendor_district_wrapper' + key).show();
                            } else {
                                $('#vendor_district_wrapper' + key).hide();
                            }

                            $('#vendor_state' + key).text(response.state || '');
                            
                            if (response.pincode) {
                                $('#vendor_pincode' + key).text(response.pincode);
                                $('#vendor_pincode_wrapper' + key).show();
                            } else {
                                $('#vendor_pincode_wrapper' + key).hide();
                            }

                            if (response.mobile1) {
                                $('#vendor_mobile1' + key).text(response.mobile1);
                                $('#vendor_phone_wrapper' + key).show();
                            } else {
                                $('#vendor_phone_wrapper' + key).hide();
                            }

                            if (response.mobile2) {
                                $('#vendor_mobile2' + key).text(response.mobile2);
                                $('#vendor_mobile2_wrapper' + key).show();
                            } else {
                                $('#vendor_mobile2_wrapper' + key).hide();
                            }

                            if (response.gstno != null && response.gstno != '') {
                                $('#tagGst' + key).show();
                                $('#vendor_gstno' + key).text(response.gstno);
                            } else {
                                $('#tagGst' + key).hide();
                            }
                        }
                    }
                });
            } else {
                $('#vendor_companyname' + key).text('Select Vendor');
                $('#vendor_name' + key).text('');
                $('#vendor_address' + key).text('');
                $('#vendor_city' + key).text('');
                $('#vendor_district_wrapper' + key).hide();
                $('#vendor_state' + key).text('');
                $('#vendor_pincode_wrapper' + key).hide();
                $('#vendor_phone_wrapper' + key).hide();
                $('#tagGst' + key).hide();
            }
        });

        // Company Select (FROM Address)
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
                        var displayCompany = response.companyname ? response.companyname : (response.name || '');
                        var displayName = (response.name && response.companyname && response.name !== response.companyname) ? ('Attn: ' + response.name) : '';

                        $('#comp_companyname' + key).text(displayCompany);
                        $('#comp_contact_name' + key).text(displayName);
                        $('#comp_address' + key).text(response.address || '');
                        $('#comp_city' + key).text(response.city || '');

                        if (response.district) {
                            $('#comp_district' + key).text(response.district);
                            $('#comp_district_wrapper' + key).show();
                        } else {
                            $('#comp_district_wrapper' + key).hide();
                        }

                        $('#comp_state' + key).text(response.state || '');

                        if (response.pincode) {
                            $('#comp_pincode' + key).text(response.pincode);
                            $('#comp_pincode_wrapper' + key).show();
                        } else {
                            $('#comp_pincode_wrapper' + key).hide();
                        }

                        if (response.mobile1) {
                            $('#comp_mobile1' + key).text(response.mobile1);
                            $('#comp_phone_wrapper' + key).show();
                        } else {
                            $('#comp_phone_wrapper' + key).hide();
                        }

                        if (response.mobile2) {
                            $('#comp_mobile2' + key).text(response.mobile2);
                            $('#comp_mobile2_wrapper' + key).show();
                        } else {
                            $('#comp_mobile2_wrapper' + key).hide();
                        }

                        if (response.gstno != null && response.gstno != '') {
                            $('#comp_tagGst' + key).show();
                            $('#comp_gstno' + key).text(response.gstno);
                        } else {
                            $('#comp_tagGst' + key).hide();
                        }
                    }
                });
            } else {
                $('#comp_companyname' + key).text('Select Company');
                $('#comp_contact_name' + key).text('');
                $('#comp_address' + key).text('');
                $('#comp_city' + key).text('');
                $('#comp_district_wrapper' + key).hide();
                $('#comp_state' + key).text('');
                $('#comp_pincode_wrapper' + key).hide();
                $('#comp_phone_wrapper' + key).hide();
                $('#comp_tagGst' + key).hide();
            }
        });
    });
</script>
@endsection
