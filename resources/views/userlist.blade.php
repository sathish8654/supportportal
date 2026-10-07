@extends('layouts.app')

@section('content')

    <div class="container-flux m-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('Manage User Accounts') }}</div>

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
                                                <label for="log_days">Log Days</label>
                                                <input type="text" class="form-control" id="log_days"
                                                    placeholder="Enter Days Count" required readonly>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="name">Name</label>
                                                <input type="text" class="form-control" id="name"
                                                    placeholder="Enter Employee name" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label for="email">Email</label>
                                                <input type="email" class="form-control" id="email"
                                                    placeholder="Enter email">
                                            </div>
                                            <div class="col-md-3">
                                                <label for="mobile">Mobile</label>
                                                <input type="text" class="form-control" id="mobile"
                                                    placeholder="Enter mobile number">
                                            </div>
                                        </div>

                                        <div class="row">

                                            <div class="col-md-3">
                                                <label for="imei">IMEI</label>
                                                <input type="text" class="form-control" id="imei"
                                                    placeholder="Enter imei">
                                            </div>

                                            <!-- Department Dropdown -->
                                            <div class="col-md-3">
                                                <label for="user_type">Mobile User</label>
                                                <select id='user_type' class="form-control">
                                                    <option value='' disabled>--Select--</option>
                                                    <option value='user'>User</option>
                                                    <option value='admin_user'>AdminUser</option>
                                                </select>
                                            </div>


                                        </div>
                                        <br>
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
                        <table id='empTable' class='datatable table table-striped table-bordered table-hover '
                            style="width:100%">
                            <thead>
                                <tr>
                                    <td>Sl.No</td>
                                    <td>Log</td>
                                    <td>Name</td>
                                    <td>Email</td>
                                    <td>Mobile</td>
                                    <td>IMEI</td>
                                    <!-- <td>Mobile User</td> -->
                                    <!-- <td>S.P.Access</td> -->
                                    <td>Last Connect</td>
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
            // $('.loader').show();

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
                ajax: "{{ route('getUserListData') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'log_days'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'email'
                    },
                    {
                        data: 'mobile_no'
                    },
                    {
                        data: 'imei'
                    },
                    // {
                    //     data: 'user_type'
                    // },
                    // {
                    //     data: 'role_id'
                    // },
                    {
                        data: 'last_connect'
                    },
                    //  { data: 'status' },
                    {
                        data: 'action',
                        visible: {{ (Auth::user()->hasMenuPermission('Users', 'userlist', 'edit') || Auth::user()->hasMenuPermission('Users', 'userlist', 'view') || Auth::user()->hasMenuPermission('Users', 'userlist', 'delete')) ? 'true' : 'false' }}
                    },
                ]
            });

            // get record info
            $('#empTable').on('click', '.updateUser, .viewUser', function() {
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
                    url: "{{ route('getUserList') }}",
                    type: 'post',
                    data: {
                        _token: CSRF_TOKEN,
                        id: id
                    },

                    dataType: 'json',
                    success: function(response) {

                        if (response.success == 1) {

                            $('#name').val(response.name);
                            $('#email').val(response.email);
                            $('#mobile').val(response.mobile);
                            $('#imei').val(response.imei);
                            $('#user_type').val(response.user_type || 'user');
                            $('#role_id').val(response.role_id || '1');
                            $('#log_days').val(response.log_days);

                            $('#city-dropdown').html(
                                '<option value="">-- Select City --</option>');

                            $.each(response.user_type, function(key, value) {

                                $("#city-dropdown").append('<option value="' + value
                                    .id + '">' + value.user_type + '</option>');
                            });

                            $.each(response.role_id, function(key, value) {

                                $("#city-dropdown").append('<option value="' + value
                                    .id + '">' + value.role_id + '</option>');
                            });
                            empTable.ajax.reload();
                        } else {
                            alert("Invalid ID.");
                        }
                    }
                });

            });

            // update / save user record
            $('#btn_save').click(function() {
                var id = $('#txt_empid').val();
                var name = $('#name').val().trim();
                var email = $('#email').val().trim();
                var mobile = $('#mobile').val().trim();
                var imei = $('#imei').val().trim();
                var user_type = $('#user_type').val().trim();
                var role_id = $('#role_id').val();
                var log_days = $('#log_days').val().trim();

                if (name != '' && email != '' && mobile != '' && imei != '' && user_type != '') {

                    // AJAX request
                    $.ajax({

                        url: "{{ route('updateUserList') }}",
                        type: 'post',
                        data: {
                            _token: CSRF_TOKEN,
                            id: id,
                            name: name,
                            email: email,
                            mobile: mobile,
                            imei: imei,
                            user_type: user_type,
                            role_id: role_id,
                            log_days: log_days
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success == 1) {
                                alert(response.msg);
                                // Empty and reset the values
                                $('#name', '#email', '#mobile', '#imei', '#user_type','#role_id', '#log_days').val('');
                                $('#txt_empid').val(0);

                                // Reload DataTable
                                // empTable.ajax.reload();
                                // Close modal
                                $('#updateModal').modal('toggle');
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


            var mytable = $('#empTable').DataTable();

            // Delete record
            $('#empTable').on('click', '.deleteUser', function() {
                var id = $(this).data('id');

                var deleteConfirm = confirm("Are you sure delete? \nThis action will remove the account from both the UserList and the associated UserIMEI accounts. \nIt means the account will be completely deleted.");
                if (deleteConfirm == true) {
                    // AJAX request
                    $.ajax({
                        url: "{{ route('deleteUserList') }}",
                        type: 'post',
                        data: {
                            _token: CSRF_TOKEN,
                            id: id
                        },
                        success: function(response) {
                            alert(response.message);
                            mytable.row(this).remove().draw(false);
                        }
                    });
                }

            });

        });
    </script>
@endsection
