@extends('layouts.app')

@section('content')
    <div class="container-flux m-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>{{ __('Manage Addresses') }}</span>
                        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                            data-bs-target="#addNewAddressModal">
                            <i class="fa fa-plus"></i> Add Address
                        </button>
                    </div>
                    <div class="card-body">
                        @include('modals/addCustomerAddress')

                        <style>
                            .highlight {
                                background-color: Bisque !important;
                                /* Ensure it overrides other styles */
                            }
                        </style>
                        <!-- Table -->
                        <div class="table-responsive">
                            <table id='empTable' class='datatable table table-striped table-bordered table-hover'
                                style="width:100%; white-space: nowrap;">
                                <thead>
                                    <tr>
                                        <th>Sl.No</th>
                                        <th>Name</th>
                                        <th>Company</th>
                                        <!-- <th>Address</th> -->
                                        <th>City</th>
                                        <!-- <th>District</th>
                                        <th>State</th> -->
                                        <th>Pincode</th>
                                        <th>GST No</th>
                                        <th>Mobile 1</th>
                                        <th>Mobile 2</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Script -->
    <script type="text/javascript">
        // CSRF Token
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $(document).ready(function() {

            // Initialize
            var empTable = $('#empTable').DataTable({
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
              
                scrollX: true,
                autoWidth: false,
                stateSave: true,
                bDestroy: true,
                processing: true,
                language: { processing: "<svg class='loader' id='loader' viewBox='0 0 340 340'><circle cx='170' cy='170' r='160' stroke='#E2007C'/><circle cx='170' cy='170' r='135' stroke='#404041'/><circle cx='170' cy='170' r='110' stroke='#E2007C'/><circle cx='170' cy='170' r='85' stroke='#404041'/></svg>"},
                serverSide: true,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                dom: 'Bfrtip',
                buttons: ['excel', 'pageLength'],
                ajax: "{{ route('getVendorsListData') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'companyname'
                    },
                    // {
                    //     data: 'address'
                    // },
                    {
                        data: 'city'
                    },
                    // {
                    //     data: 'district'
                    // },
                    // {
                    //     data: 'state'
                    // },
                    {
                        data: 'pincode'
                    },
                    {
                        data: 'gstno'
                    },
                    {
                        data: 'mobile1'
                    },
                    {
                        data: 'mobile2'
                    },
                    {
                        data: 'status'
                    },
                    {
                        data: 'action',
                        visible: {{ (Auth::user()->hasMenuPermission('Manage', 'vendor', 'edit') || Auth::user()->hasMenuPermission('Manage', 'vendor', 'view') || Auth::user()->hasMenuPermission('Manage', 'vendor', 'delete')) ? 'true' : 'false' }}
                    },
                ],
                createdRow: function(row, data, dataIndex) {
                    if (data["status"] === "Inactive") {
                        $(row).css("background-color", "Bisque");
                        $(row).addClass("highlight");
                    }
                },
            });
        });
    </script>
@endsection
