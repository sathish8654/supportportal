@extends('layouts.app')

@section('content')

@include('modals/addParts')
@include('modals/addPartsSub')

<!--
    https://www.bootstrapdash.com/blog/bootstrap-tab-examples
 Bootstrap Tabs and Masonry by SitePoint
  -->

<div class="container-flux m-4 ">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Manage parts') }}</div>
                <div class="card-body">


                    <div class="row">
                        <div class="col-md">
                            <div class="starter-template">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="home-tab" data-bs-toggle="tab" href="#home"
                                            role="tab" aria-controls="home" aria-selected="true">Manage Part Name</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="profile-tab" data-bs-toggle="tab" href="#profile"
                                            role="tab" aria-controls="profile" aria-selected="false">Manage Part Number/
                                            Value and Price</a>
                                    </li>
                                </ul>

                                <!-- tab content starts here -->
                                <div class="tab-content" id="myTabContent">

                                    <!-- content 1 -->
                                    <div class="tab-pane masonry-container fade show active" id="home" role="tabpanel"
                                        aria-labelledby="home-tab">
                                        <!-- <div class="card-columns"> -->


                                        <div class="row justify-content-center">
                                            <div class="col-md-12">
                                                <!-- <div class="card"> -->
                                                <!-- <div class="card-header">{{ __('Manage Part Name') }}</div> -->
                                                <div class="card-body">
                                                    <button type="button" class="btn btn-primary float-right"
                                                        data-bs-toggle="modal" data-bs-target="#addNewPartModal">
                                                        Add Part
                                                    </button>

                                                    <style>
                                                    .highlight {
                                                        background-color: Bisque !important;
                                                        /* Ensure it overrides other styles */
                                                    }
                                                    </style>
                                                    <br>
                                                    <br>
                                                    <!-- Table -->
                                                    <table id='empTable'
                                                        class='datatable table table-striped table-bordered table-hover'
                                                        style="width:100%">
                                                        <thead>
                                                            <tr>
                                                                <td width="5%">Sl.No</td>
                                                                <td width="70%">Name</td>
                                                                <td width="15%">status</td>
                                                                <td width="10%">Action</td>
                                                            </tr>
                                                        </thead>
                                                    </table>
                                                    <!-- </div> -->
                                                    <!-- </div> -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- content 2 -->
                                    <div class="tab-pane fade masonry-container" id="profile" role="tabpanel"
                                        aria-labelledby="profile-tab">
                                        <!-- <div class="card-group"> -->

                                        <div class="row justify-content-center">
                                            <div class="col-md-12">
                                                <!-- <div class="card-header">{{ __('Manage Part Number/ Value and Price') }}</div> -->
                                                <div class="card-body">
                                                    <button type="button" class="btn btn-primary float-right"
                                                        data-bs-toggle="modal" data-bs-target="#addNewPartSubModal">
                                                        Add Part Sub
                                                    </button>

                                                    <style>
                                                    .highlight {
                                                        background-color: Bisque !important;
                                                        /* Ensure it overrides other styles */
                                                    }
                                                    </style>
                                                    <br>
                                                    <br>
                                                    <!-- Table -->
                                                    <table id='partSubTable'
                                                        class='datatable table table-striped table-bordered table-hover'
                                                        style="width:100%">
                                                        <thead>
                                                            <tr>
                                                                <td width="5%">Sl.No</td>
                                                                <td width="35%">Part Name</td>
                                                                <td width="35%">Part Sub Name</td>
                                                                <td width="10%">Part Value</td>
                                                                <td width="10%">Status</td>
                                                                <td width="5%">Action</td>
                                                            </tr>
                                                        </thead>
                                                    </table>
                                                </div>
                                                <!-- /////////////////////////// -->
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

// keep the current tab active on page reload
$(document).ready(function() {
    $('a[data-bs-toggle="tab"]').on('show.bs.tab', function(e) {
        localStorage.setItem('activeTab', $(e.target).attr('href'));
    });
    var activeTab = localStorage.getItem('activeTab');
    if (activeTab) {
        var tabTrigger = new bootstrap.Tab($('#myTab a[href="' + activeTab + '"]')[0]);
        tabTrigger.show();
    }

    // When tab is shown, reload the corresponding DataTable
    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
        var target = $(e.target).attr('href'); // e.g., "#tab1" or "#tab2"
        if (target === '#profile') {
            $('#partSubTable').DataTable().draw();
        } else if (target === '#home') {
            $('#empTable').DataTable().draw();
        }
    });
});
//////////////////////////////////////////
// CSRF Token
$(document).ready(function() {

    // Initialize
    var empTable = $('#empTable').DataTable({
        initComplete: function(settings) {
            //settings.nTable.id --> Get table ID
            $('#' + settings.nTable.id + '_filter input').wrap(`
                  <div class="d-inline-flex position-relative"></div>
                `).after(`
                  <button type="button" class="close position-absolute" aria-label="Close" style="right:5px">
                    <span aria-hidden="true">&times;</span>
                  </button>
                `).attr('required', 'required').attr('title', 'Search');

            // Click Event on Clear button
            $(document).on('click', '#' + settings.nTable.id + '_filter button',
                function() {
                    $('#' + settings.nTable.id).DataTable({
                        "retrieve": true,
                    }).search('').draw(); // reDraw table
                });
        },

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
        ajax: {
            "url": "{{ route('getpartsListData') }}",
            "type": "POST",
            'data': function(data) {

                data.filterId = 1;

            }
        },
        columns: [{
                data: 'DT_RowIndex',
                searchable: false,
                orderable: false
            },
            {
                data: 'name'
            },

            {
                data: 'status'
            },
            {
                data: 'action',
                visible: {{ (Auth::user()->hasMenuPermission('Parts', 'parts', 'edit') || Auth::user()->hasMenuPermission('Parts', 'parts', 'view') || Auth::user()->hasMenuPermission('Parts', 'parts', 'delete')) ? 'true' : 'false' }}
            },
        ],
        ordering: true,
        order: [
            [2, "desc"],
            [1, 'asc']
        ],
        createdRow: function(row, data, dataIndex) {
            if (data["status"] === "Inactive") {
                $(row).css("background-color", "Bisque");
                $(row).addClass("highlight");
            }
        },
    });




    ///////////////// part SubTable /////////////////////////

    var partSubTable = $('#partSubTable').DataTable({
        initComplete: function(settings) {
            //settings.nTable.id --> Get table ID
            $('#' + settings.nTable.id + '_filter input').wrap(`
                  <div class="d-inline-flex position-relative"></div>
                `).after(`
                  <button type="button" class="close position-absolute" aria-label="Close" style="right:5px">
                    <span aria-hidden="true">&times;</span>
                  </button>
                `).attr('required', 'required').attr('title', 'Search');

            // Click Event on Clear button
            $(document).on('click', '#' + settings.nTable.id + '_filter button',
                function() {
                    $('#' + settings.nTable.id).DataTable({
                        "retrieve": true,
                    }).search('').draw(); // reDraw table
                });
        },

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
        ajax: "{{ route('getpartsSubListData') }}",
        // ajax: {
        //     "url": "{{ route('getpartsSubListData') }}",
        //         "type": "POST",
        //         'data': function(data){

        //             data.filterId = 2;

        //         }
        //     },
        columns: [{
                data: 'DT_RowIndex',
                searchable: false,
                orderable: false
            },
            {
                data: 'name'
            },
            {
                data: 'partSubname'
            },
            {
                data: 'part_value'
            },
            {
                data: 'status'
            },
            {
                data: 'action',
                visible: {{ (Auth::user()->hasMenuPermission('Parts', 'parts', 'edit') || Auth::user()->hasMenuPermission('Parts', 'parts', 'view') || Auth::user()->hasMenuPermission('Parts', 'parts', 'delete')) ? 'true' : 'false' }}
            },
        ],
        //         ordering:true,
        //   order:[[3,"desc"],[5,'asc']],
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