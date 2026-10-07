    @extends('layouts.app')

    @section('content')

    
    <div class="container-flux m-4">
    
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <span>{{ __('Manage IMEI') }}</span>
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <form action="{{ route('userAddImei.upload') }}" method="POST" enctype="multipart/form-data"
                                class="d-flex align-items-center gap-2 mb-0">
                                @csrf
                                <div class="d-flex align-items-center gap-2">
                                    <input type="file" class="form-control form-control-sm" name="file" accept=".csv,.xlsx,.xls,.txt" required>
                                    <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-upload"></i> Upload</button>
                                </div>
                                <a href="{{ route('userAddImei.downloadSample') }}" class="btn btn-outline-info active btn-sm" role="button" aria-pressed="true">
                                    <i class="fa fa-download"></i> Download Sample File
                                </a>
                            </form>
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                                data-bs-target="#addNewImeiMany">
                                <i class="fa fa-plus"></i> Add New IMEI
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        <!-- Modal -->
                        <div id="updateModalImei" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog" role="document">

                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Manage IMEI</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="imei" class="form-label">IMEI</label>
                                                <input type="text" class="form-control" id="imei" placeholder="Enter IMEI" readonly>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="log_days" class="form-label">Log Days</label>
                                                <input type="text" class="form-control" id="log_days" name="log_days" placeholder="Enter Log Days">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="status" class="form-label">Status</label>
                                                <select class="form-select" id="status" name="status" required>
                                                    <option value="active">Active</option>
                                                    <option value="inactive">Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label for="note" class="form-label">Note</label>
                                                <textarea class="form-control" id="note" name="note" rows="3" placeholder="Enter Note"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <input type="hidden" id="txt_imeiid" value="0">
                                        <button type="button" class="btn btn-success btn-sm" id="btn_save">Save</button>
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>

                            </div>
                        </div>


                        <!-- https://www.codermen.com/submit-laravel-form-using-ajax-post-method-with-validation/ -->
                        <div class="modal fade" id="addNewImeiMany" tabindex="-1" role="dialog"
                            aria-labelledby="addNewImeiModalLabelMany" aria-hidden="true">
                            <div class="modal-dialog modal-xl" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="addNewImeiModalLabelMany">Add New IMEI</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-success" role="alert" id="successMsgMany"
                                            style="display: none"> IMEI added
                                            successfully! </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="panel panel-default">
                                                    <div class="panel-body">
                                                        <form name="add_item" id="add_item" class="form-inline">
                                                            {{ csrf_field() }}
                                                            <div class="table-responsive">
                                                                <table class='table table-bordered table-hover'
                                                                    id="tab_logic">
                                                                    <thead>
                                                                        <tr class='info'>
                                                                            <th style='width:10%;'>SL.NO.</th>
                                                                            <th style='width:40%;'>IMEI</th>
                                                                            <th style='width:40%;'>Note</th>
                                                                            <th style='width:10%;'>Action</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody id="dynamic_field">
                                                                        <tr id="addr0">
                                                                            <td class="custom-tbl"><input
                                                                                    class='form-control input-sm'
                                                                                    style='width:100%;' type="text"
                                                                                    value="1" id="pr_item0"
                                                                                    name="pr_item[]" readonly required>
                                                                            </td>
                                                                            <td class="custom-tbl"><input
                                                                                    class='form-control input-sm'
                                                                                    style='width:100%;' type="text"
                                                                                    id="pr_unit0" name="pr_unit[]">
                                                                            </td>
                                                                            <td class="custom-tbl"><input
                                                                                    class='form-control input-sm'
                                                                                    style='width:100%;' type="text"
                                                                                    id="pr_desc0" name="pr_desc[]">
                                                                            </td>
                                                                            <td class="custom-tbl"><button
                                                                                    type="button" id="add"
                                                                                    class="btn btn-info btn-sm"><span
                                                                                        class="glyphicon glyphicon-plus">+</span></button>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                                <button type="button" id="submit" name="submit"
                                                                    class="btn btn-success">Submit</button>
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Table -->
                        <table id='empTable'
                            class='datatable table table-striped table-bordered text-center center table-hover'
                            width="100%">
                            <thead>
                                <tr>
                                    <td>Sl.No</td>
                                    <td>Log Days</td>
                                    <td>IMEI</td>
                                    <td>Owner</td>
                                    <td>Note</td>
                                    <td>UpdatedBy</td>
                                    <td>UpdatedOn</td>
                                    <td>last_connect</td>
                                    <td>Status</td>
                                    <td>Action</td>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>



        <script type="text/javascript">
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
                    ajax: "{{ route('getImeiListData') }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'log_days'
                        },
                        {
                            data: 'imei'
                        },
                        {
                            data: 'owner'
                        },
                        {
                            data: 'note'
                        },
                        {
                            data: 'updated_byname'
                        },
                        {
                            data: 'updated_at'
                        },
                        {
                            data: 'last_connect'
                        },
                        {
                            data: 'status'
                        },
                        {
                            data: 'action',
                            searchable: false,
                            orderable: false,
                            visible: {{ (Auth::user()->hasMenuPermission('Manage', 'userAddImei', 'edit') || Auth::user()->hasMenuPermission('Manage', 'userAddImei', 'view') || Auth::user()->hasMenuPermission('Manage', 'userAddImei', 'delete')) ? 'true' : 'false' }}
                        },
                    ]
                });
            });



            // <!--Add button function -- >

            $(document).ready(function() {
                var postURL = "<?php echo url('addmore'); ?>";
                var i = 1;

                $('#add').click(function() {
                    i++;
                    $('#dynamic_field').append('<tr id="row' + i +
                        '" class="dynamic-added"><td class="custom-tbl"><input id="pr_item' + i +
                        '" class="form-control input-sm"style="width:100%;" type="text" value="' + i +
                        '" name="pr_item[]" readonly required></td><td class="custom-tbl"><input id="pr_unit' +
                        i +
                        '"class="form-control input-sm" style="width:100%;" type="text" name="pr_unit[]"></td><td class="custom-tbl"><input id="pr_desc' +
                        i +
                        '" class="form-control input-sm" style="width:100%;" type="text" name="pr_desc[]"></td><td class="custom-tbl"><button type="button" name="remove" id="' +
                        i +
                        '" class="btn btn-danger btn-sm btn_remove"><span class="glyphicon glyphicon-remove">-</span></button></td></tr>'
                    );
                });

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
                    // $('#submit').click(function(){    
                    $.ajax({
                        url: "{{ route('userAddImei.submitAddImeiMany') }}",
                        method: "POST",
                        data: $('#add_item').serialize(),
                        type: 'json',

                        success: function(response) {

                            if (response.success == 'done') {
                                $('#successMsgMany').show();
                                $('#pr_unit0', '#pr_desc0').val('');
                                // alert(response.sucess);
                            }
                            if (response.error == 'error') {
                                alert("Duplicate ID, please check IMEI.");

                            }
                        },
                    }, );
                });




                // CSRF Token

                    // get modal record data
                $('#empTable').on('click', '.updateUser', function() {
                    var id = $(this).data('id');
                    var mode = $(this).data('mode');

                    $('#txt_imeiid').val(id);

                    // AJAX request
                    $.ajax({
                        url: "{{ route('getImeiList') }}",
                        type: 'post',
                        data: {
                            _token: CSRF_TOKEN,
                            id: id,
                            mode: mode,
                        },

                        dataType: 'json',
                        success: function(response) {
                            console.log(response);
                            if (response.success == 1) {
                               if (mode==1){// for edit
                                 $('#imei').val(response.imei);
                                 $('#log_days').val(response.log_days);
                                 $('#status').val(response.status);
                                 $('#note').val(response.note);
                               }
                              
                                empTable.ajax.reload();

                            } else {
                                alert("Invalid ID.");
                            }
                        }
                    });

                });

                $('#empTable').on('click', '.updateUser1', function() {
                    var id = $(this).data('id');
                    var mode = $(this).data('mode');
                    // AJAX request
                    $.ajax({
                        url: "{{ route('appendImeiPrint') }}",
                        type: 'post',
                        data: {
                            _token: CSRF_TOKEN,
                            id: id,
                            mode: mode,
                        },
                        success: function(data) {
                // alert(response.message);
                            $('.appendProductModelData').html(data.table_view);
                        }
                    });
                });
                var empTable = $('#empTable').DataTable();


                // Save to update record
                $('#btn_save').click(function() {
                    var id = $('#txt_imeiid').val();

                    var imei = $('#imei').val().trim();
                    var log_days = $('#log_days').val().trim();
                    var status = $('#status').val();
                    var note = $('#note').val().trim();

                    if (imei != '') {

                        // AJAX request
                        $.ajax({
                            url: "{{ route('updateImeiList') }}",
                            type: 'post',
                            data: {
                                _token: CSRF_TOKEN,
                                id: id,
                                imei: imei,
                                log_days: log_days,
                                status: status,
                                note: note
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success == 1) {
                                    alert(response.msg);

                                    // Empty and reset the values
                                    $('#imei').val('');
                                    $('#log_days').val('');
                                    $('#note').val('');
                                    $('#status').val('active');
                                    $('#txt_imeiid').val(0);

                                    // Close modal
                                    $('#updateModalImei').modal('toggle');
                                    $('#empTable').DataTable().ajax.reload()
                                    

                                } else {
                                    alert(response.msg);
                                }
                            }
                        });

                    } else {
                        alert('Please fill all fields.');
                    }
                });

                // Delete record
                $('#empTable').on('click', '.deleteUser', function() {
                    var id = $(this).data('id');

                    var deleteConfirm = confirm("Are you sure?");
                    if (deleteConfirm == true) {
                        // AJAX request
                        $.ajax({
                            url: "{{ route('deleteImeiList') }}",
                            type: 'post',
                            data: {
                                _token: CSRF_TOKEN,
                                id: id
                            },
                            success: function(response) {
                                if (response.success == 1) {
                                    alert("Record deleted.");

                                    // Reload DataTable
                                    empTable.ajax.reload();
                                } else {
                                    alert("Invalid ID.");
                                }
                            }
                        });
                    }
                });
            });

            $('#updateModalImei').on('hidden.bs.modal', function () {
                
                // If you also want to clear dynamically appended rows
                $("#TextBoxContainer").empty();
            });
        </script>

    <!-- for imei page print module add -->
    <div id="printImei" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Manage IMEI</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="overflow-x: auto;">
                    <div class="appendProductModelData"></div>  
                </div>
            </div>
        </div>
    </div>
@endsection
