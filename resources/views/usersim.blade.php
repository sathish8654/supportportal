@extends('layouts.app')

@section('content')

@include('modals/editUserSim')

    <div class="container-flux m-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>{{ __('Manage User SIM Cards Informations') }}</span>
                        <div class="d-flex align-items-center gap-3">
                            <form action="{{ route('upload') }}" method="POST" enctype="multipart/form-data"
                                class="d-flex align-items-center gap-2 mb-0">
                                @csrf

                                <div class="d-flex align-items-center gap-2">
                                    <input type="file" class="form-control form-control-sm" name="file" accept=".csv,.xlsx,.xls" required>
                                    <button type="submit" class="btn btn-success btn-sm">Upload</button>
                                </div>

                                <a href="/download/sample.xlsx" class="btn btn-outline-info active btn-sm" role="button" aria-pressed="true"><i class="fa fa-download"></i> Download Sample File</a>
                            </form>
                            <button type="button" class="btn btn-success btn-sm addNew" data-bs-toggle="modal"
                                data-bs-target="#updateModal"><i class="fa fa-plus"></i> Add New Sim</button>
                        </div>
                    </div>

                    <!-- bulk upload -->
                    <div class="card-body">


                        @include('modals/barSimStatus')

                        
                        <!-- Table -->
                        <table id='userSimTable'
                            class='datatable table table-striped table-bordered text-center center table-hover'
                            style="width:100%">
                            <thead>
                                <tr>
                                    <td>Sl.No</td>
                                    <td>dueDays</td>
                                    <td>Status</td>
                                    <td>Renewal</td>
                                    <td>SIM No</td>
                                    <td>SIM Phone Number</td>
                                    <td>SIM Operator</td>
                                    <td>Activate Date</td>
                                    <td>Renewal Date</td>
                                    <td>Customer Name</td>
                                    <!-- <td>Name on Aadhar</td>  -->
                                    <!-- <td>Aadhar Number</td>  -->
                                    <!-- <td>Address</td>  -->
                                    <td>Contact Number</td>
                                    <td>Kit-IMEI</td>
                                    <!-- <td>Kit Serial</td> -->
                                    <td>KYC</td>
                                    <td>Payment</td>
                                    <!-- <td>Cost</td> -->
                                    <td>Care-Off</td>
                                    <!-- <td>created_by</td>  -->
                                    <!-- <td>created_at</td>  -->
                                    <!-- <td>updated_by</td>  -->
                                    <!-- <td>updated_at</td> -->
                                    <td>Last Connect</td>
                                    <td>Action</td>
                                </tr>
                            </thead>
                        </table>
                        <br>
                    </div>

                    <input type="hidden" id="payment-filter" />
                    <input type="hidden" id="kycStatusPending-filter" />
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // Check if the URL has the 'trigger=filter-payment' parameter
            const urlParams = new URLSearchParams(window.location.search);
            const trigger = urlParams.get('trigger');
            const status = urlParams.get('status');
            
            if (trigger === 'filter-payment' && status) {
            // Convert status to a number if necessary
            console.log(`data-status="${status}" found.`);

            // Find the .filter-payment element with the matching data-status
            const targetButton = $(`.filter-payment[data-status="${status}"]`);

            if (targetButton.length) {
                console.warn('Matching targetButton element:', targetButton[0]); // Log the first DOM element
                // Trigger the click event for the matched element
                setTimeout(() => {
                        targetButton.trigger('click');
                    }, 2000);
            } else {
                console.warn(`No element with data-status="${statusValue}" found.`);
            }
        }

            if (trigger === 'filter-kycStatusPending' && status) {
            // Convert status to a number if necessary
            console.log(`data-status="${status}" found.`);

            // Find the .filter-kycStatusPending element with the matching data-status
            const targetButton = $(`.filter-kycStatusPending[data-status="${status}"]`);

            if (targetButton.length) {
                console.warn('Matching targetButton element:', targetButton[0]); // Log the first DOM element
                // Trigger the click event for the matched element
                setTimeout(() => {
                        targetButton.trigger('click');
                    }, 2000);
            } else {
                console.warn(`No element with data-status="${statusValue}" found.`);
            }
        }
        });
    </script>

    <!-- Script -->
    <script type="text/javascript">
        // CSRF Token
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $(document).ready(function() {
            // Initialize
            var userSimTable = $('#userSimTable').DataTable({

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
              
                deferRender: true,
                responsive: true,
                stateSave: true,
                bDestroy: true,
                processing: true,
                serverSide: true,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                dom: 'Bfrtip',
                buttons: ['excel', 'pageLength'],
                language: { processing: "<svg class='loader' id='loader' viewBox='0 0 340 340'><circle cx='170' cy='170' r='160' stroke='#E2007C'/><circle cx='170' cy='170' r='135' stroke='#404041'/><circle cx='170' cy='170' r='110' stroke='#E2007C'/><circle cx='170' cy='170' r='85' stroke='#404041'/></svg>"},
                ajax: {
                    url: "{{ route('getUserSimData') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function(d) {
                        d.payment = $('#payment-filter')
                    .val(); // Hidden field or dynamically updated value
                        d.kycStatusPending = $('#kycStatusPending-filter')
                    .val(); // Hidden field or dynamically updated value
                    },
                },
                columns: [{
                        data: 'DT_RowIndex',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'dueDays'
                    },
                    {
                        data: 'simStatus'
                    },
                    {
                        data: 'renew'
                    },
                    {
                        data: 'simNo'
                    },
                    {
                        data: 'simPhoneNumber'
                    },
                    {
                        data: 'simOperatorName'
                    },
                    {
                        data: 'activateDate'
                    },
                    {
                        data: 'renewalDate'
                    },
                    {
                        data: 'custName'
                    },
                    // { data: 'custAadharName' }, 
                    // { data: 'custAadharNumber' }, 
                    // { data: 'custAddress' }, 
                    {
                        data: 'custContactNumber'
                    },
                    {
                        data: 'imei'
                    },
                    // { data: 'unitSerialNumber' }, 
                    {
                        data: 'kycStatus'
                    },
                    {
                        data: 'payment'
                    },
                    // {
                    //     data: 'cost'
                    // },
                    {
                        // data: null, 
                        render: function(data, type, row) {
                            if (row.note == null) {
                                var details = row.careOff;
                                return details;
                            } else if (row.careOff == null) {
                                var details = row.note;
                                return details;
                            } else {
                                var details = row.careOff + " " + row.note;
                                return details;
                            }
                        }
                    },

                    // { data: 'careOff' }, 
                    // { data: 'created_by' }, 
                    // { data: 'created_at' }, 
                    // { data: 'updated_by' }, 
                    // { data: 'updated_at' },
                    {
                        data: 'last_connect'
                    },
                    {
                        data: 'action',
                        visible: {{ (Auth::user()->hasMenuPermission('Users', 'usersim', 'edit') || Auth::user()->hasMenuPermission('Users', 'usersim', 'view') || Auth::user()->hasMenuPermission('Users', 'usersim', 'delete')) ? 'true' : 'false' }}
                    },
                ]
            });


            $(document).on('click', '.filter-payment', function() {
                var payment = $(this).data('status'); // Get payment value from button
                var kycStatusPending = 0; // Get payment value from button

                console.log(`Filter triggered for payment status: ${payment}`);

                // Update hidden input or directly pass it to DataTable
                $('#payment-filter').val(payment);
                $('#kycStatusPending-filter').val(kycStatusPending);
                // Redraw DataTable with the new filter value
                userSimTable.draw();
            });



            $(document).on('click', '.filter-kycStatusPending', function() {
                var kycStatusPending = $(this).data('status'); // Get payment value from button
                var payment = 0; // Get payment value from button
                // Update hidden input or directly pass it to DataTable
                $('#payment-filter').val(payment);
                $('#kycStatusPending-filter').val(kycStatusPending);
                // Redraw DataTable with the new filter value
                userSimTable.draw();
            });

        });
    </script>
@endsection
