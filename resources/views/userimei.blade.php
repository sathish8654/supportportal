@extends('layouts.app')

@section('content')
    <div class="container-flux m-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('Associated user IMEI') }}</div>

                    <div class="card-body">


                        <!-- Modal -->
                        <div id="updateModal" class="modal" role="dialog">
                            <div class="modal-dialog modal-xl">

                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Update</h4>
                                        <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label for="log_days">LogDays</label>
                                                <input type="text" class="form-control" id="log_days"
                                                    placeholder="Enter Days Count" required readonly>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="name">Name</label>
                                                <input type="text" class="form-control" id="name"
                                                    placeholder="Enter name" readonly>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="email">Email</label>
                                                <input type="email" class="form-control" id="email"
                                                    placeholder="Enter email" readonly>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="mobile">Mobile</label>
                                                <input type="text" class="form-control" id="mobile"
                                                    placeholder="Enter mobile number" readonly>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="user_imei">User IMEI</label>
                                                <input type="text" class="form-control" id="user_imei"
                                                    placeholder="Enter imei" readonly>
                                            </div>


                                            <div class="col-md-3">
                                                <label for="device_name">device_name</label>
                                                <input type="text" class="form-control" id="device_name"
                                                    placeholder="Enter imei">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="imei">Selected IMEI</label>
                                                <input type="text" class="form-control" id="imei"
                                                    placeholder="Enter imei">
                                            </div>

                                            <!-- Department Dropdown -->
                                            <div class="col">
                                                <label for="user_type">Type</label>
                                                <select id='user_type' class="form-control" disabled>
                                                    <option value=''>--Select--</option>
                                                    <option value='user'>User</option>
                                                    <option value='admin_user'>AdminUser</option>
                                                </select>
                                            </div>
                                        </div>
                                        <br>
                                        <span style="color:blue"># The account name, email and mobile number can be edit at the UserList menu!</span>
                                        <div class="modal-footer">
                                            <input type="hidden" id="txt_empid" value="0">
                                            <button type="button" class="btn btn-success btn-sm"
                                                id="btn_save">Save</button>
                                            <button type="button" class="btn btn-default btn-sm"
                                                data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Table -->
                        <table id='imeiTable' class='datatable table table-striped table-bordered table-hover'
                            width="100%">
                            <thead>
                                <tr>
                                    <td>Sl.No</td>
                                    <td>Log days</td>
                                    <td>User Name</td>
                                    <td>Email</td>
                                    <td>Mobile</td>
                                    <td>User IMEI</td>
                                    <td>Device Name</td>
                                    <td>Selected IMEI</td>
                                    <td>Type</td>
                                    <th>Last Connect</th>
                                    <!-- <td>Status</td> -->
                                    <td>Action</td>
                                </tr>
                            </thead>
                        </table>
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
            var imeiTable = $('#imeiTable').DataTable({

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
                ajax: "{{ route('getUserImeiData') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'log_days'
                    },
                    {
                        data: 'user_name'
                    },
                    {
                        data: 'user_email'
                    },
                    {
                        data: 'user_mobile'
                    },
                    {
                        data: 'user_imei'
                    },
                    {
                        data: 'device_name'
                    },
                    {
                        data: 'imei'
                    },
                    {
                        data: 'user_types'
                    },
                    {
                        data: 'last_connect'
                    },
                    //  { data: 'status' },
                    {
                        data: 'action',
                        visible: {{ (Auth::user()->hasMenuPermission('Users', 'userimei', 'edit') || Auth::user()->hasMenuPermission('Users', 'userimei', 'view') || Auth::user()->hasMenuPermission('Users', 'userimei', 'delete')) ? 'true' : 'false' }}
                    },
                ]
            });

            // Get record info
            $('#imeiTable').on('click', '.updateUser, .viewUser', function() {
                var isViewOnly = $(this).hasClass('viewUser') || $(this).data('view-only') == 1;

                if (isViewOnly) {
                    $('#updateModal .modal-title').text('View Details');
                    $('#updateModal input, #updateModal select').prop('disabled', true);
                    $('#btn_save').hide();
                } else {
                    $('#updateModal .modal-title').text('Update');
                    $('#updateModal input, #updateModal select').prop('disabled', false);
                    $('#log_days').prop('readonly', true);
                    $('#btn_save').show();
                }

                var id = $(this).data('id');

                $('#txt_empid').val(id);
                // AJAX request
                $.ajax({
                    url: "{{ route('getUserImei') }}",
                    type: 'post',
                    data: {
                        _token: CSRF_TOKEN,
                        id: id
                    },

                    dataType: 'json',
                    success: function(response) {

                        if (response.success == 1) {

                            $('#name').val(response.user_name);
                            $('#email').val(response.user_email);
                            $('#mobile').val(response.user_mobile);
                            $('#user_imei').val(response.user_imei);
                            $('#user_type').val(response.user_types);
                            $('#imei').val(response.imei);
                            $('#device_name').val(response.device_name);
                            $('#log_days').val(response.log_days);

                            imeiTable.ajax.reload();
                        } else {
                            alert("Invalid ID.");
                        }
                    }
                });

            });

            // Save user 
            $('#btn_save').click(function() {
                var id = $('#txt_empid').val();

                var imei = $('#imei').val().trim();
                var log_days = $('#log_days').val().trim();
                var device_name = $('#device_name').val().trim();

                if (imei != '') {

                    // AJAX request
                    $.ajax({
                        url: "{{ route('updateUserImei') }}",
                        type: 'post',
                        data: {
                            _token: CSRF_TOKEN,
                            id: id,
                            imei: imei,
                            device_name: device_name,
                            log_days: log_days
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success == 1) {
                                alert(response.msg);

                                // Empty and reset the values
                                $('#imei', '#device_name').val('');
                                //   $('#gender').val('Male');
                                $('#txt_empid').val(0);

                                // Reload DataTable
                                imeiTable.ajax.reload();

                                // Close modal
                                $('#updateModal').modal('toggle');
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
            var table = $('#imeiTable').DataTable();
            $('#imeiTable').on('click', '.deleteUser', function() {
                var id = $(this).data('id');
                var deleteConfirm = confirm("Are you sure delete? \nThis action will remove only the child account and parent account remains active at UserList menu.");
                if (deleteConfirm == true) {
                    // AJAX request
                    $.ajax({
                        url: "{{ route('deleteUserImei') }}",
                        type: 'post',
                        data: {
                            _token: CSRF_TOKEN,
                            id: id
                        },
                        success: function(response) {
                            alert(response.message);
                            table.row(this).remove().draw(false);
                        }
                    });
                }

            });

        });
    </script>
@endsection
