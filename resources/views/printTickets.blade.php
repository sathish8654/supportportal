@extends('layouts.app')

@section('content')
    <div class="container-flux m-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center py-2">
                        <span class="mb-0">{{ __('Print Tickets') }}</span>
                        <div class="d-flex gap-2">
                            <button type="button" name="filter" id="filter" class="btn btn-sm btn-info text-white">Filter</button>
                            <button type="button" name="reset" id="reset" class="btn btn-sm btn-success" onclick="javascript:window.location.reload()">Reset</button>
                            <button type="button" name="formPrint" id="formPrint" class="formPrint btn btn-sm btn-info text-white" onclick="postToRoute()">
                                <i class="fa fa-print me-1"></i> Print
                            </button>
                        </div>
                    </div>

                    <div class="card-body">


                        <style>
                            p {
                                margin-bottom: 0em;
                                margin-top: 0em;
                            }

                            .vl {
                                border-left: 4px solid black;
                                height: 280px;
                            }
                        </style>

<!-- <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" /> -->

                        <div class="row input-daterange g-2">
                            <div class="col-md-2">
                                <label for="name" class="control-label fw-bold">Company <span class="text-danger">*</span></label>
                                <select name="filter_company" class="form-control filter_company select2">
                                    <option value="">Select Company</option>
                                    @foreach ($company_name as $company)
                                        <option value="{{ $company->id }}">
                                            {{ $company->companyname }}-{{ $company->id }}</option>
                                    @endforeach
                                </select>
                            </div>

                             <div class="col-md-2">
                                <label for="name" class="control-label fw-bold">Vendor <span class="text-danger">*</span></label>
                                <select name="filter_vendor" class="form-control filter_vendor vendorList select2">
                                    <option value="">Select vendor</option>
                                    <option value="ALL">ALL</option>
                                    @foreach ($vendor_name as $vendor)
                                        <option value="{{ $vendor->LRticketsvendorid }}">
                                            {{ $vendor->vendorname }}-{{ $vendor->vendormobile1 }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                    

                            <div class="col-md-2">
                                <label for="name" class="control-label fw-bold">From Date</label>
                                <input type="date" name="from_date" id="from_date" class="form-control" placeholder="From Date" />
                            </div>

                            <div class="col-md-2">
                                <label for="name" class="control-label fw-bold">To Date</label>
                                <input type="date" name="to_date" id="to_date" class="form-control" placeholder="To Date" />
                            </div>

                               </div>
                        <div class="row input-daterange g-2">

                            <div class="col-md-2">
                                <label for="filter_case" class="control-label fw-bold">Case</label>
                                <select name="filter_case" id="filter_case" class="form-control select2">
                                    <option value="">Select Case</option>
                                    <option value="ALL">ALL</option>
                                    @foreach ($cases as $caseItem)
                                        <option value="{{ $caseItem->id }}">{{ $caseItem->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label for="filter_category" class="control-label fw-bold">Category</label>
                                <select name="filter_category" id="filter_category" class="form-control select2">
                                    <option value="">Select Category</option>
                                    <option value="ALL">ALL</option>
                                    @foreach ($categories as $catItem)
                                        <option value="{{ $catItem->category }}">{{ $catItem->category }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 mt-2">
                                <label for="filter_model" class="control-label fw-bold">Model</label>
                                <select name="filter_model" id="filter_model" class="form-control select2">
                                    <option value="">Select Model</option>
                                    <option value="ALL">ALL</option>
                                    @foreach ($models as $modItem)
                                        <option value="{{ $modItem->model }}">{{ $modItem->model }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2 mt-2">
                                <label for="name" class="control-label fw-bold">Ticket Status</label>
                                <select name="filter_status" id="filter_status" class="form-control select2">
                                    <option value="">Select Status</option>
                                    <option value="ALL">ALL</option>
                                    <option value="1">Open</option>
                                    <option value="2">Closed</option>
                                    <option value="3">Assigned</option>
                                </select>
                            </div>

                            <div class="col-md-2 mt-2">
                                <label for="name" class="control-label fw-bold">Delivery Status</label>
                                <select name="filter_delivery_status" id="filter_delivery_status" class="form-control select2">
                                    <option value="">Select Delivery Status</option>
                                    <option value="ALL">ALL</option>
                                    <option value="1">Pending</option>
                                    <option value="2">Delivered</option>
                                </select>
                            </div>
                        </div>


                        <div class="mt-4">
                            <table id="ticketsList" class="table table-bordered table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Ticket</th>
                                        <th>Vendor</th>
                                        <th>Case</th>
                                        <th>Category</th>
                                        <th>Model</th>
                                        <th>Serial</th>
                                        <th>Note</th>
                                        <th>Cost</th>
                                        <th>Solution</th>
                                        <th>Create by</th>
                                        <th>Closed by</th>
                                        <!-- <th>Days</th> -->
                                        <th>Status</th>
                                    </tr>
                                </thead>
                            </table>
                            <br>
                        </div>

                    </div>
            </div>
        </div>
    </div>

    @include('modals/viewTicketsHistory')


    <script type="text/javascript">
        $(document).ready(function() {

            $('.select2').select2({});

            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            fill_datatable();

            function fill_datatable(filter_status = '', filter_vendor = '', filter_delivery_status = '', filter_case = '', filter_category = '', filter_model = '') {

                var from_date = $('#from_date').val();
                var to_date = $('#to_date').val();

                var dataTable = $('#ticketsList').DataTable({
                    initComplete: function(settings) {
                    //settings.nTable.id --> Get table ID
                    $('#'+settings.nTable.id+'_filter input').wrap(`
                    <div class="d-inline-flex position-relative"></div>
                    `).after(`
                    <button type="button" class="close position-absolute" aria-label="Close" style="right:5px">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    `).attr('required','required').attr('title','Search');

                    // Click Event on Clear button
                    $(document).on('click', '#'+settings.nTable.id+'_filter button', function(){
                    $('#'+settings.nTable.id).DataTable({
                        "retrieve": true,
                        }).search('').draw(); // reDraw table
                    });
                },
                    dom: 'Bfrtip',
                    columnDefs: [{
                            className: "dt-head-center"
                        },
                        {
                            className: 'text-center',
                            targets: [0, 8, 10, 11]
                        },
                    ],
                    buttons: [
                        'pageLength'
                    ],
                    responsive: true,
                    processing: true,
                    language: { processing: "<svg class='loader' id='loader' viewBox='0 0 340 340'><circle cx='170' cy='170' r='160' stroke='#E2007C'/><circle cx='170' cy='170' r='135' stroke='#404041'/><circle cx='170' cy='170' r='110' stroke='#E2007C'/><circle cx='170' cy='170' r='85' stroke='#404041'/></svg>"},
                    serverSide: true,
                    lengthMenu: [
                        [10, 25, 50, -1],
                        [10, 25, 50, "All"]
                    ],
                    ajax: {
                        url: "{{ route('printTickets') }}",
                        data: {
                            filter_status: filter_status,
                            filter_vendor: filter_vendor,
                            filter_delivery_status: filter_delivery_status,
                            filter_case: filter_case,
                            filter_category: filter_category,
                            filter_model: filter_model,
                            from_date: from_date,
                            to_date: to_date,
                        },
                        error: function(xhr, error, thrown) {
                            try {
                                let res = JSON.parse(xhr.responseText);
                                alert(res.message || "An error occurred while loading data.");
                            } catch (e) {
                                alert("Internal Server Error (500). Please check Laravel logs.");
                            }
                        }
                    },
                    columns: [
                        {
                            data: 'idBtn',
                            name: 'idBtn'
                        },
                        {
                            data: 'LRvendor_name',
                            name: 'LRvendor_name',
                            render: function(data, type, row) {
                                let name = data ? data.trim() : '';
                                let company = row['LRvendor_companyname'] ? row['LRvendor_companyname'].trim() : '';
                                if (name.toLowerCase() === company.toLowerCase()) {
                                    return name;
                                }
                                return (name + ' ' + company).trim();
                            }
                        },
                        {
                            data: 'case',
                            name: 'case'
                        },
                        {
                            data: 'category',
                            name: 'category'
                        },
                        {
                            data: 'model',
                            name: 'model'
                        },
                        {
                            data: 'serial',
                            name: 'serial'
                        },
                        {
                            data: 'note',
                            name: 'note'
                        },
                        {
                            data: 'cost',
                            name: 'cost'
                        },
                        {
                            data: 'solution',
                            name: 'solution'
                        },
                        {
                            data: null,
                            render: function(data, type, row) {
                                var name = row.user_name ? row.user_name : '';
                                var date = row.created_at ? row.created_at : '';
                                return (name + " " + date).trim();
                            }
                        },
                        {
                            data: null,
                            render: function(data, type, row) {
                                var name = row.user_name_updated ? row.user_name_updated : '';
                                var date = row.updated_at ? row.updated_at : '';
                                return (name + " " + date).trim();
                            }
                        },
                        // {
                        //     data: 'daysCount',
                        //     name: 'daysCount'
                        // },
                        {
                            data: 'status',
                            name: 'status'
                        }
                    ]
                });
            }

            $('#filter').click(function() {
                var filter_company = $('.filter_company').val();
                var filter_vendor = $('.filter_vendor').val();
                var filter_status = $('#filter_status').val();
                var filter_delivery_status = $('#filter_delivery_status').val();
                var filter_case = $('#filter_case').val();
                var filter_category = $('#filter_category').val();
                var filter_model = $('#filter_model').val();
                var from_date = $('#from_date').val();
                var to_date = $('#to_date').val();
                var filter = 'YES';

                //get vendor name
                $.ajax({
                    type: 'POST',
                    url: "{{ route('printTickets') }}",
                    data: {
                        _token: CSRF_TOKEN,
                        filter_vendor: filter_vendor,
                        filter_status: filter_status,
                        filter_delivery_status: filter_delivery_status,
                        filter_case: filter_case,
                        filter_category: filter_category,
                        filter_model: filter_model,
                        from_date: from_date,
                        to_date: to_date,
                        filter: filter,
                    },
                    dataType: 'html',
                    success: function(data) {

                        // $('#ticketsList').html(data);

                        // $('.dt-buttons, .dataTables_paginate, .dataTables_filter, .dataTables_info').hide();

                        // $('.vendorList').empty().append(data);

                        // $(".liveskytech" + key1).hide();
                        // $(".chezhiyan" + key1).hide();
                    },
                    error: function(response) {
                        try {
                            let res = JSON.parse(response.responseText);
                            alert(res.message || "An error occurred while filtering data.");
                        } catch (e) {
                            alert("Internal Server Error (500). Please check Laravel logs.");
                        }
                    }
                });


                // if(filter_status != '' &&  filter_status != '')
                // if (filter_vendor != '' && filter_company != '') {
                $('#ticketsList').DataTable().destroy();
                fill_datatable(filter_status, filter_vendor, filter_delivery_status, filter_case, filter_category, filter_model);
                // }
                // else {
                //   alert('Select Company and Vendor in the filter');
                // }
                if (filter_status == 2 && filter_delivery_status == 1) { //multiple check box disabled
                    $('#checkall').attr('disabled', false);
                    $('.openDiv').css('display', 'block');
                } else {
                    $('#checkall').attr('disabled', true);
                    $('.openDiv').css('display', 'none');
                }
                // alert('hii');
            });

            $('#reset').click(function() {
                $('#filter_status').val('').trigger('change');
                $('#filter_vendor').val('').trigger('change');
                $('#filter_delivery_status').val('').trigger('change');
                $('#filter_case').val('').trigger('change');
                $('#filter_category').val('').trigger('change');
                $('#filter_model').val('').trigger('change');
                $('.filter_company').val('').trigger('change');
                $('#from_date').val('');
                $('#to_date').val('');
                $('#ticketsList').DataTable().destroy();
                fill_datatable();
            });

        });
    </script>



    <!-- Script -->
    <script type="text/javascript">
        // CSRF Token
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

        // $(document).ready(function() {


        //     $('.input-daterange').datepicker({
        //         todayBtn: 'linked',
        //         format: 'yyyy-mm-dd',
        //         autoclose: true
        //     });




        // });
    </script>

    <script>
        function postToRoute() {

            var filter_company = $('.filter_company').val();
            var filter_vendor = $('.filter_vendor').val();
            var filter_status = $('#filter_status').val();
            var filter_delivery_status = $('#filter_delivery_status').val();
            var filter_case = $('#filter_case').val();
            var filter_category = $('#filter_category').val();
            var filter_model = $('#filter_model').val();
            var from_date = $('#from_date').val();
            var to_date = $('#to_date').val();

            if (filter_company != '' && filter_vendor != '' && filter_vendor != 'ALL') {

                // Define the data you want to send

                var data = {
                    filter_company: filter_company,
                    filter_vendor: filter_vendor,
                    filter_status: filter_status,
                    filter_delivery_status: filter_delivery_status,
                    filter_case: filter_case,
                    filter_category: filter_category,
                    filter_model: filter_model,
                    from_date: from_date,
                    to_date: to_date,
                    // Add more attributes as needed
                };

                // Create a form element
                var form = document.createElement("form");
                form.method = "POST";
                form.action = "{{ route('printAck') }}";
                form.target = "_blank"; // Open in a new tab

                // Add CSRF token
                var csrfToken = "{{ csrf_token() }}";
                var csrfInput = document.createElement("input");
                csrfInput.type = "hidden";
                csrfInput.name = "_token";
                csrfInput.value = csrfToken;
                form.appendChild(csrfInput);

                // Add the data to the form as hidden inputs
                for (var key in data) {
                    if (data.hasOwnProperty(key)) {
                        var hiddenField = document.createElement("input");
                        hiddenField.type = "hidden";
                        hiddenField.name = key;
                        hiddenField.value = data[key];
                        form.appendChild(hiddenField);
                    }
                }

                // Append the form to the body and submit it
                document.body.appendChild(form);
                form.submit();
            } else {
                alert('Please select both Company and Vendor (do not select ALL for Vendor).');
            }
        }
    </script>
@endsection
