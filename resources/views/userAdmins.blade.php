@extends('layouts.app')

@section('content')
{{-- The modal content is included from modals/manageAdmins.blade.php --}}
@include('modals/manageAdmins')

<!-- Style -->
<style>
    .highlight-row {
        background-color: #6495ED !important; /* Yellow background for highlighted rows */
    }
</style>

<div class="container-flux m-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 py-2 px-3">
                    <h5 class="mb-0 fw-bold text-dark">{{ __('Manage Admin Accounts') }}</h5>
                    <div class="d-flex align-items-center gap-2">
                        <div class="position-relative" style="min-width: 280px;">
                            <div class="input-group input-group-sm align-items-stretch">
                                <span class="input-group-text bg-white border-end-0 d-flex align-items-center justify-content-center px-2"><i class="fa fa-search text-muted"></i></span>
                                <input type="text" id="adminUserSearchInput" class="form-control border-start-0" placeholder="Search by name, email, or mobile..." autocomplete="off">
                                <span class="input-group-text bg-white border-start-0 d-flex align-items-center justify-content-center px-2" id="clearAdminSearch" style="display:none; cursor:pointer;" title="Clear search">
                                    <i class="fa fa-times-circle text-secondary"></i>
                                </span>
                            </div>
                            <div id="adminSearchAutocomplete" class="dropdown-menu shadow-lg mt-1 p-0" style="display:none; position:absolute; z-index:1050; max-height:300px; overflow-y:auto; right:0; left:auto; width:max-content; min-width:100%; max-width:480px;">
                            </div>
                        </div>
                        <button type="button" class="btn btn-success btn-sm addNewAdmin d-inline-flex align-items-center justify-content-center gap-1 shadow-sm" style="height: 31px; white-space: nowrap;" data-bs-toggle="modal" data-bs-target="#updateModal">
                            <i class="fa fa-plus"></i><i class="fa fa-user"></i> <span>Add New Account</span>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Table -->
                    <h5 class="mb-3 text-secondary">Support portal admin access users</h5>
                    <table id='empTable' class='datatable table table-striped table-bordered table-hover align-middle' style="width:100%">
                    <thead class="table-light">
                        <tr>
                            <th>Sl.No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <!-- <th>IMEI</th> -->
                            <th>AppAdmin</th>
                            <th>PortalAdmin</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
                </div>

                <style>
                    hr {
                      border-style: inset;
                      border-width: 1px;
                      border-block-color: blue;
                    }
                    </style>
                    <hr>

                    <br>
                    <div class="card-body">

                    <!-- Table -->
                    <h5 class="mb-3 text-secondary">Mobile App admin access users</h5>
                    <table id='AppAdminTable' class='datatable table table-striped table-bordered table-hover align-middle'
                        style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th>Sl.No</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <!-- <th>IMEI</th> -->
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                    <br>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Script -->
<script type="text/javascript">
    // CSRF Token
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

    // Reusable function for DataTable search clear
    function setupDataTableSearchClear(tableId) {
        $('#' + tableId + '_filter input').wrap(`<div class="d-inline-flex position-relative"></div>`).after(`<button type="button" class="close position-absolute" aria-label="Close" style="right:5px"><span aria-hidden="true">&times;</span></button>`).attr('required', 'required').attr('title', 'Search');
        $(document).on('click', '#' + tableId + '_filter button', function() {
            $('#' + tableId).DataTable({"retrieve": true,}).search('').draw();
        });
    }

    $(document).ready(function() {
        // Initialize
        var empTable = $('#empTable').DataTable({
            initComplete: function(settings) {
                setupDataTableSearchClear(settings.nTable.id);
              },
            responsive: true,
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
            ajax: "{{ route('getAdminListData') }}",                
            columns: [{
                    data: 'DT_RowIndex',
                    searchable: false,
                    orderable: false
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
                // {
                    // data: 'imei'
                // },
                {
                    data: 'user_type'
                },
                {
                    data: 'role_id'
                },
                {
                    data: 'status'
                },
                {
                    data: 'action',
                    visible: {{ (Auth::user()->hasMenuPermission('Manage', 'userAdmins', 'edit') || Auth::user()->hasMenuPermission('Manage', 'userAdmins', 'delete')) ? 'true' : 'false' }}
                },
            ],
            createdRow: function(row, data, dataIndex) {
                // Check the condition (e.g., if current_team_id is 1)
                if (data.current_team_id == 1) {
                    $(row).addClass('highlight-row'); // Add class to highlight the row
                }
            }
        });
    });

    /* 
       Note: .updateUser, .updatePassword, and .addNewAdmin click handlers 
       are now handled within modals/manageAdmins.blade.php 
    */

    // Retain real-time mismatch check for better UX (Make sure #password_message is added to the modal)
    $(document).on('click', '#empTable .updatePassword', function() {
        // You might want to hide other fields in the modal here via JS if needed
        $('#password_message').text('').removeClass('text-danger text-success');
        $('#password, #password_confirmation').removeClass('is-invalid is-valid');
    });

    $(document).on('click', '.addNewAdmin', function() {
        $('#password_message').text('').removeClass('text-danger text-success');
        $('#password, #password_confirmation').removeClass('is-invalid is-valid');
    });

    // Real-time password mismatch check
    $(document).on('keyup', '#password, #password_confirmation', function () {
        var password = $('#password').val();
        var confirm_password = $('#password_confirmation').val();
        var message = $('#password_message'); // Ensure this element exists in manageAdmins.blade.php

        if (password === '' && confirm_password === '') {
            if(message.length) message.text('').removeClass('text-danger text-success');
            $('#password, #password_confirmation').removeClass('is-invalid is-valid');
            return;
        }

        let saveBtn = $('#SubmitFormAdd button[type="submit"]');
        if (password === confirm_password && password !== '') {
            if(message.length) message.text('Passwords match').removeClass('text-danger').addClass('text-success');
            $('#password, #password_confirmation').removeClass('is-invalid').addClass('is-valid');
            saveBtn.prop('disabled', false);
        } else if (confirm_password !== '') {
            if(message.length) message.text('Passwords do not match').removeClass('text-success').addClass('text-danger');
            $('#password, #password_confirmation').removeClass('is-valid').addClass('is-invalid');
        }
    });

    // Live Autocomplete Search for Admin Users
    $(document).ready(function() {
        var searchTimeout = null;

        function performSearch(query) {
            if (!query || query.trim() === '') {
                $('#adminSearchAutocomplete').hide().empty();
                $('#clearAdminSearch').hide();
                $('#adminUserSearchInput').removeClass('border-end-0');
                return;
            }
            $('#clearAdminSearch').show();
            $('#adminUserSearchInput').addClass('border-end-0');

            $.ajax({
                url: "{{ route('searchAdminUsers') }}",
                type: 'GET',
                data: { query: query },
                dataType: 'json',
                success: function(users) {
                    var container = $('#adminSearchAutocomplete');
                    container.empty();

                    if (!users || users.length === 0) {
                        container.append('<div class="dropdown-item text-muted disabled py-2 px-3 small">No matching accounts found</div>');
                    } else {
                        users.forEach(function(user) {
                            var roleBadge = user.role_id == 2 
                                ? '<span class="badge bg-success ms-1">Portal Admin</span>' 
                                : (user.user_type == 'admin_user' 
                                    ? '<span class="badge bg-info text-dark ms-1">App Admin</span>' 
                                    : '<span class="badge bg-secondary ms-1">User</span>');

                            var itemHtml = `
                                <div class="dropdown-item py-2 px-3 border-bottom search-result-item" style="cursor:pointer;" data-id="${user.id}" data-name="${user.name}" data-email="${user.email}" data-mobile="${user.mobile_no || ''}">
                                    <div class="d-flex justify-content-between align-items-center gap-3">
                                        <div style="min-width: 0;">
                                            <strong class="d-block text-dark text-break">${user.name || 'N/A'} ${roleBadge}</strong>
                                            <small class="text-muted d-block text-break"><i class="fa fa-envelope-o me-1"></i>${user.email || 'No email'}</small>
                                            <small class="text-muted d-block text-break"><i class="fa fa-phone me-1"></i>${user.mobile_no || 'No mobile'}</small>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <button type="button" class="btn btn-xs btn-outline-primary editSearchResultBtn ms-2 d-inline-flex align-items-center gap-1" data-id="${user.id}" title="Edit User">
                                                <i class="fa fa-edit"></i> <span>Edit</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            `;
                            container.append(itemHtml);
                        });
                    }
                    container.show();
                }
            });
        }

        $('#adminUserSearchInput').on('keyup input', function() {
            var query = $(this).val();
            clearTimeout(searchTimeout);

            if (!query || query.trim() === '') {
                $('#clearAdminSearch').hide();
                $('#adminUserSearchInput').removeClass('border-end-0');
                $('#adminSearchAutocomplete').hide().empty();
            } else {
                $('#clearAdminSearch').show();
                $('#adminUserSearchInput').addClass('border-end-0');
            }

            // Filter DataTable in real-time
            if ($.fn.DataTable.isDataTable('#empTable')) {
                $('#empTable').DataTable().search(query).draw();
            }

            // Trigger Autocomplete Dropdown
            searchTimeout = setTimeout(function() {
                performSearch(query);
            }, 250);
        });

        $('#clearAdminSearch').on('click', function() {
            $('#adminUserSearchInput').val('').removeClass('border-end-0');
            $('#adminSearchAutocomplete').hide().empty();
            $(this).hide();
            if ($.fn.DataTable.isDataTable('#empTable')) {
                $('#empTable').DataTable().search('').draw();
            }
        });

        // Click search result item or Edit button inside autocomplete
        $(document).on('click', '.search-result-item', function(e) {
            var userId = $(this).data('id');
            var userName = $(this).data('name');
            var userEmail = $(this).data('email');
            
            $('#adminUserSearchInput').val(userName || userEmail);
            $('#adminSearchAutocomplete').hide();

            // Filter DataTable for this specific user
            if ($.fn.DataTable.isDataTable('#empTable')) {
                $('#empTable').DataTable().search(userName || userEmail).draw();
            }

            // If Edit button inside autocomplete item was clicked, open updateModal
            if ($(e.target).closest('.editSearchResultBtn').length > 0) {
                openEditUserModal(userId);
            }
        });

        // Helper to open edit modal for a specific user ID
        function openEditUserModal(userId) {
            $(".modal-title").html("Update admin account");
            $('#name').attr("disabled", false);
            $('#email').attr("disabled", false);
            $('#mobile').attr("disabled", false);
            $(".roleMenu").show();
            $(".passwd").hide();
            $('#createNew').val(0);
            $('#txt_empid').val(userId);

            $.ajax({
                url: "{{ route('getAdminList') }}",
                type: 'post',
                data: {
                    _token: CSRF_TOKEN,
                    id: userId
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success == 1) {
                        $('#name').val(response.name);
                        $('#email').val(response.email);
                        $('#mobile').val(response.mobile);
                        $('#user_type').val(response.user_type);
                        $('#role_id').val(response.role_id);
                        $('#all_select').val(response.all_select);
                        $('#all_special_delete').val(response.all_special_delete);
                        $('#only_selfview').val(response.only_selfview);
                        $('#current_team_id').val(response.current_team_id);
                        if (typeof togglePortalAccessMenus === 'function') {
                            togglePortalAccessMenus();
                        }
                        $('#updateModal').modal('show');
                    } else {
                        alert("Invalid user ID.");
                    }
                }
            });
        }

        // Hide autocomplete when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#adminUserSearchInput, #adminSearchAutocomplete').length) {
                $('#adminSearchAutocomplete').hide();
            }
        });
    });
</script>

<!-- Script for AppAdminTable -->
<script type="text/javascript">
    $(document).ready(function() {
        var AppAdminTable = $('#AppAdminTable').DataTable({
            initComplete: function(settings) {
                setupDataTableSearchClear(settings.nTable.id);
            },
                responsive: true,
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
                ajax: "{{ route('getAppAdminListData') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        searchable: false,
                        orderable: false
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
                    // {
                    //     data: 'imei'
                    // },
                    {
                        data: 'status'
                    },
                    {
                        data: 'action'
                    },
                ]
            });
        });
    </script>

@endsection
