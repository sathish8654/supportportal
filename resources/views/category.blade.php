@extends('layouts.app')
@section('content')
@include('modals/categoryModal')

<div class="container-flux m-4 ">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Manage Category') }}</div>
                <div class="card-body">
                    <div class="starter-template">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="home-tab" data-bs-toggle="tab" href="#home" role="tab"
                                    aria-controls="home" aria-selected="true">Manage Category</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="profile-tab" data-bs-toggle="tab" href="#profile" role="tab"
                                    aria-controls="profile" aria-selected="false">Manage Sub Category</a>
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
                                            <style>
                                            .highlight {
                                                background-color: Bisque !important;
                                                /* Ensure it overrides other styles */
                                            }
                                            </style>
                                            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addParent" >Add Category</button>

                                            <br>
                                            <br>

                             
                                            <!-- Table -->
                                            <table id='categoryTable'
                                                class='datatable table table-striped table-bordered table-hover'
                                                style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <td width="5%">SNO</td>
                                                        <td width="70%">Category</td>
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
                                            <style>
                                            .highlight {
                                                background-color: Bisque !important;
                                                /* Ensure it overrides other styles */
                                            }
                                            </style>
                                            <br>
                                            <br>
                                            <!-- Table -->
                                            <table id='catSubTable'
                                                class='datatable table table-striped table-bordered table-hover'
                                                style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <td width="5%">SNO</td>
                                                        <td width="35%">Category</td>
                                                        <td width="35%">Model</td>
                                                        <td width="35%">Number</td>
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



<script type="text/javascript">
    $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
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
            $('#catSubTable').DataTable().draw();
        } else if (target === '#home') {
            $('#categoryTable').DataTable().draw();
        }
    });


    // Initialize
    var empTable = $('#categoryTable').DataTable({
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
            "url": "{{ route('getCategoryData') }}",
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
                data: 'action'
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

    var partSubTable = $('#catSubTable').DataTable({
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
        ajax: "{{ route('getSubCatData') }}",

        columns: [{
                data: 'DT_RowIndex',
                searchable: false,
                orderable: false
            },
            {
                data: 'name'
            },
            {
                data: 'Subname'
            },
            {
                data: 'model_number'
            },

            {
                data: 'status'
            },
            {
                data: 'action'
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
    ///////////////////////////////////////////////
    $('#categorym').on('change', function(e) {
        var cat_id = e.target.value;
        $.ajax({
            url: "{{ route('subcat') }}",
            type: "POST",
            data: {
                cat_id: cat_id
            },
            success: function(data) {
                var $SubC = $('#sub1category1').empty();
                $SubC.append($("<option></option>").attr("value", '')
                    .text(
                        'Please Select'));
                $.each(data.subcategories[0].subcategories, function(
                    index,
                    sub1category1) {
                    $('#sub1category1').append(
                        '<option value="' + sub1category1
                        .id + '">' + sub1category1
                        .name + '</option>');
                })
            }
        })
    });
    $('#sub1category1').on('change', function(e) {
        var cat_id = e.target.value;
        $.ajax({
            url: "{{ route('subcat') }}",
            type: "POST",
            data: {
                cat_id: cat_id
            },
            success: function(data) {
                var $SubC1 = $('#subcategory1').empty();
                $SubC1.append($("<option></option>").attr("value", '')
                    .text(
                        'Please Select'));
                $.each(data.subcategories[0].subcategories, function(
                    index,
                    subcategory1) {
                    $('#subcategory1').append(
                        '<option value="' + subcategory1
                        .id + '">' + subcategory1.name +
                        '</option>');
                })
            }
        })
    });
});
</script>


<script>
$(document).ready(function() {
    var table = $('#categoryStatus').DataTable({
        responsive: true,
        "order": [
            [3, "desc"]
        ]
    });

    // Event delegation for dynamically added elements
    $('#categoryTable').on('click', '.btn-toggle-status', function() {

        // alert()
        // $('.btn-toggle-status').click(function () {
        var button = $(this);
        var itemId = button.data('id');
        var newStatus = button.data('status') == 1 ? 2 : 1; // Toggle status

        $.ajax({
            url: '/category-status/' + itemId,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                status: newStatus
            },
            success: function(response) {
                if (response.success) {
                    $('#categoryTable').DataTable().ajax.reload();
                    button.data('status', newStatus);
                    button.text(newStatus == 1 ? 'Deactivate' :
                        'Activate');
                    // button.closest('tr').find('td').eq(2).text(newStatus == 1 ? 'Active' : 'Inactive');
                    var statusText = button.closest('tr').find(
                        '.status-text');
                    statusText.text(newStatus == 1 ? 'Active' :
                        'Inactive');
                    statusText.removeClass('text-success text-danger');
                    statusText.addClass(newStatus == 1 ?
                        'text-success' :
                        'text-danger');
                } else {
                    alert('Failed to update status');
                }
            }
        });
    });

    // Delete record
    $('#catSubTable').on('click', '.disableSubCat', function() {
        var id = $(this).data('id');

        var deleteConfirm = confirm("Are you sure disable from the list?");
        if (deleteConfirm == true) {
            // alert(id)
            // AJAX request
            $.ajax({
                url: "{{ route('disableSubCategory') }}",
                type: 'post',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: id
                },
                success: function(response) {
                    // alert(response.message);
                    $('#catSubTable').DataTable().ajax.reload();
                }
            });
        }

    });
});
</script>
@endsection