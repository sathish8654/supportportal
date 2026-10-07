<style>
    /* Refined layout for password icons */
    .password-wrapper {
        position: relative;
    }
    .password-wrapper .field-icon {
        position: absolute;
        right: 25px;
        bottom: 10px; /* Aligns with the input field center */
        cursor: pointer;
        color: #6c757d;
        z-index: 10;
    }
</style>

<!-- Modal -->
<div id="updateModal" class="modal" tabindex="-1" role="dialog">

    <div class="modal-dialog modal-xl">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('updateAdminList') }}" method="POST" id="SubmitFormAdd">
                    @csrf
                    <div class="row">
                        <input type="hidden" id="txt_empid" name="id" value="0">
                        <input type="hidden" id="createNew" name="createNew" value="0">

                        <div class="col-md-3">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name"
                                placeholder="Enter Employee name">
                        </div>
                        <div class="col-md-3">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter email">
                        </div>
                        <div class="col-md-3">
                            <label for="mobile">Mobile</label>
                            <input type="number" class="form-control" id="mobile" name="mobile"
                                placeholder="Enter mobile number">
                        </div>

                        @php
                            $isAdmin = (Auth::check() && Auth::user()->current_team_id == 1);
                            $hiddenAttr = $isAdmin ? '' : 'hidden';
                        @endphp

                        <!-- Department Dropdown -->
                        <div class="col-md-3 roleMenu">
                            <label for="user_type">Mobile App Admin</label>
                            <select id='user_type' name='user_type' class="form-control">
                                <option value='' disabled>--Select--</option>
                                <option value='user'>Common User</option>
                                <option value='admin_user'>Admin User</option>
                            </select>
                        </div>

                        <!-- Department Dropdown -->
                        <div class="col roleMenu" id="userRoleAccess" {{ $hiddenAttr }}>
                            <label for="role_id">Support Portal Role</label>
                            <select id='role_id' name='role_id' class="form-control">
                                <option value='' disabled>--Select Role--</option>
                                <option value='1'>Disable All Access</option>
                                <option value='2'>Support Portal Admin</option>
                                <option value='10'>Sales Executive (Role 10)</option>
                                <option value='11'>Sales Head / Manager (Role 11)</option>
                                <option value='12'>Accounts / Finance (Role 12)</option>
                                <option value='13'>Managing Director - MD (Role 13)</option>
                                <option value='14'>Production / Dispatch Head (Role 14)</option>
                                <option value='15'>Service / Field Engineer (Role 15)</option>
                            </select>
                        </div>

                        <!-- Department Dropdown -->
                        <div class="col roleMenu" id="userRoleAllSelect" {{ $hiddenAttr }}>
                            <label for="all_select">Allow All Menu</label>
                            <select id='all_select' name='all_select' class="form-control" onclick="getDropDown()">
                                <option value=''>Disable</option>
                                <option value='1'>Enable</option>
                            </select>
                        </div>

                        <!-- Department Dropdown -->
                        <div class="col roleMenu" id="userRoleSelfView" {{ $hiddenAttr }}>
                            <label for="only_selfview">Only self-view</label>
                            <select id='only_selfview' name='only_selfview' class="form-control"
                                onclick="getDropDown()">
                                <option value=''>Disable</option>
                                <option value='1'>Enable</option>
                            </select>
                        </div>

                        <!-- Department Dropdown -->
                        <div class="col roleMenu" id="userRoleTeamDelete" {{ $hiddenAttr }}>
                            <label for="current_team_id">Allow Account Delete</label>
                            <select id='current_team_id' name='current_team_id' class="form-control"
                                onclick="getDropDown()">
                                <option value=''>Disable</option>
                                <option value='1'>Enable</option>
                            </select>
                        </div>

                        <script>
                            function togglePortalAccessMenus() {
                                var roleVal = $('#role_id').val();
                                if (roleVal == '2') { // Enable
                                    $('#userRoleAllSelect').show();
                                    $('#userRoleSelfView').show();
                                    $('#userRoleTeamDelete').show();
                                    $('#userRoleSpecialDelete').show();
                                    
                                    if ($('#all_select').val() == '1') {
                                        $("#setNewUserPermission").hide();
                                    } else {
                                        $("#setNewUserPermission").show();
                                    }
                                } else { // Disable or Empty
                                    $('#userRoleAllSelect').hide();
                                    $('#userRoleSelfView').hide();
                                    $('#userRoleTeamDelete').hide();
                                    $('#userRoleSpecialDelete').hide();
                                    $("#setNewUserPermission").hide();
                                }
                            }

                            $(document).ready(function () {
                                $("#setNewUserPermission").hide();
                                
                                $('#role_id').on('change', function () {
                                    togglePortalAccessMenus();
                                });

                                $('#all_select').on('change', function () {
                                    togglePortalAccessMenus();
                                });
                            });
                        </script>

                        <div class="col roleMenu" id="userRoleSpecialDelete" {{ $hiddenAttr }}>
                            <label for="all_special_delete">Allow Special Delete</label>
                            <select id='all_special_delete' name='all_special_delete' class="form-control">
                                <option value=''>Disable</option>
                                <option value='1'>Enable</option>
                            </select>
                        </div>
                    </div>
                    <script>
                            function peakPassword(e) {
                                var passwordField = document.getElementById(e);
                                if (passwordField.getAttribute('type') === 'password') {
                                    passwordField.setAttribute('type', 'text');
                                } else {
                                    passwordField.setAttribute('type', 'password');
                                }
                            }
                        </script>
                    <div class="row passwd">
                        <div class="col-md-4 password-wrapper">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="Enter Password">
                            <span class="fa fa-eye-slash field-icon" onClick="peakPassword('password')"></span>
                        </div>
                        <div class="col-md-4 password-wrapper">
                            <label for="password_confirmation">Re-type Password</label>
                            <input type="password" class="form-control" id="password_confirmation"
                                name="password_confirmation" placeholder="Enter Password Again">
                            <span class="fa fa-eye-slash field-icon"
                                onClick="peakPassword('password_confirmation')"></span>

                        </div>
                    </div>
                    <br>

                    <div class="col roleMenu" id="setNewUserPermission">
                        <hr>
                        <h2>Menu & Actions Permissions</h2>
                        <input type="hidden" name="allowed_menus" id="allowed_menus_json">
                        <div id="new-permissions-container" class="border p-3 rounded bg-light">
                            <!-- Dynamic nested menu checkboxes will be rendered here -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <!-- Alert Container -->
                        <div id="alertContainer" style="display: none; color: red;"></div>
                        <button type="submit" class="btn btn-primary">Save</button>
                        <!-- <button type="button" class="btn btn-success btn-sm" id="btn_save">Save</button> -->
                        <button type="button" class="btn btn-default btn-sm" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('SubmitFormAdd').addEventListener('submit', function (e) {
        e.preventDefault(); // Prevent default form submission

        // Serialize the new dynamic permissions checkboxes to JSON
        let allowedMenusJson = { menus: {} };
        $('#new-permissions-container .menu-group').each(function() {
            const mainMenuCheckbox = $(this).find('.main-menu-checkbox');
            const menuTitle = mainMenuCheckbox.data('menu');
            const menuEnabled = mainMenuCheckbox.is(':checked');

            allowedMenusJson.menus[menuTitle] = {
                enabled: menuEnabled,
                submenus: {}
            };

            $(this).find('.submenu-item').each(function() {
                const submenuCheckbox = $(this).find('.submenu-checkbox');
                const subRoute = submenuCheckbox.data('submenu');
                const subEnabled = submenuCheckbox.is(':checked');

                allowedMenusJson.menus[menuTitle].submenus[subRoute] = {
                    enabled: subEnabled,
                    actions: {
                        view: $(this).find(`.action-checkbox[data-action="view"]`).is(':checked'),
                        create: $(this).find(`.action-checkbox[data-action="create"]`).is(':checked'),
                        edit: $(this).find(`.action-checkbox[data-action="edit"]`).is(':checked'),
                        delete: $(this).find(`.action-checkbox[data-action="delete"]`).is(':checked')
                    }
                };
            });
        });

        $('#allowed_menus_json').val(JSON.stringify(allowedMenusJson));

        if ($('#role_id').val() == '2' && $('#all_select').val() != '1') {
            let anyChecked = $('#new-permissions-container input[type="checkbox"]:checked').length > 0;
            if (!anyChecked) {
                let msg = 'Please select at least one menu permission.';
                alert(msg);
                let alertContainer = document.getElementById('alertContainer');
                alertContainer.style.display = 'block';
                alertContainer.innerHTML = msg;
                return; // Stop form submission
            }
        }

        let form = this;
        let formData = new FormData(form);
        let alertContainer = document.getElementById('alertContainer');

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            },
            body: formData,
        })
            .then(response => {
                if (response.status === 422) {
                    return response.json().then(data => {
                        // Display validation errors
                        let errors = data.errors;
                        let errorMessages = Object.values(errors).flat().join('<br>');

                        alertContainer.style.display = 'block';
                        alertContainer.innerHTML = errorMessages;
                    });
                } else {
                    return response.json().then(data => {
                        alertContainer.style.display = 'none';
                        alert(data.message); // Show success message
                        form.reset(); // Optionally reset the form
                        $('#updateModal').modal('toggle');
                        $('#empTable').DataTable().ajax.reload();
                        $('#AppAdminTable').DataTable().ajax.reload();
                        location.reload();

                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });


    // CSRF Token
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    $(document).ready(function () {

        $(".addNewAdmin").click(function () {
            $(".modal-title").html("Create new admin account")
            $('#SubmitFormAdd')[0].reset();
            $('#createNew').val(2);
            // $("#SubmitUpdate").hide();
            // $(".userInfo").hide();
            // $("#submitCreate").show();
            $('#name').attr("disabled", false);
            $('#email').attr("disabled", false);
            $('#mobile').attr("disabled", false);
            $(".passwd").show();
            $(".roleMenu").hide();
        });


        //   $(".updatePassword").click(function() {
        $('#empTable').on('click', '.updatePassword', function () {

            var id = $(this).data('id');
            $('#txt_empid').val(id);
            $('#createNew').val(3);

            $('#updateModal').modal('toggle');

            $(".modal-title").html("Update User password")
            $('#name').attr("disabled", true);
            $('#email').attr("disabled", true);
            $('#mobile').attr("disabled", true);
            $(".roleMenu").hide();
            $(".passwd").show();
            $("#setUserPermission").hide();








            // AJAX request
            $.ajax({
                url: "{{ route('getAdminList') }}",
                type: 'post',
                data: {
                    _token: CSRF_TOKEN,
                    id: id
                },

                dataType: 'json',
                success: function (response) {

                    console.log(response);

                    if (response.success == 1) {

                        $('#name').val(response.name);
                        $('#email').val(response.email);
                        $('#mobile').val(response.mobile);


                    }
                }
            });




        });

        // get record info
        $('#empTable').on('click', '.updateUser', function () {

            $(".modal-title").html("Update admin account")
            $('#name').attr("disabled", false);
            $('#email').attr("disabled", false);
            $('#mobile').attr("disabled", false);
            $(".roleMenu").show();
            $(".passwd").hide();
            $('#createNew').val(0);

            var id = $(this).data('id');
            $('#txt_empid').val(id);

            // AJAX request
            $.ajax({
                url: "{{ route('getAdminList') }}",
                type: 'post',
                data: {
                    _token: CSRF_TOKEN,
                    id: id
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

                        togglePortalAccessMenus();

                        const rowData = response.userData;

                        // Clear and build the new dynamic permissions UI
                        const newContainer = $('#new-permissions-container');
                        newContainer.empty();

                        let allowedMenusObj = {};
                        if (rowData && rowData.allowed_menus) {
                            try {
                                allowedMenusObj = typeof rowData.allowed_menus === 'string' 
                                    ? JSON.parse(rowData.allowed_menus) 
                                    : rowData.allowed_menus;
                            } catch (e) {
                                console.error('Failed to parse allowed_menus:', e);
                            }
                        }
                        if (!allowedMenusObj.menus) {
                            allowedMenusObj.menus = {};
                        }

                        if (response.navbar_menus && response.navbar_menus.length > 0) {
                            response.navbar_menus.forEach(menu => {
                                const menuTitle = menu.title;
                                const menuChecked = (allowedMenusObj.menus[menuTitle] && allowedMenusObj.menus[menuTitle].enabled) ? 'checked' : '';
                                
                                let menuHtml = `
                                    <div class="menu-group mb-3 pb-2 border-bottom">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input main-menu-checkbox" id="menu-${menuTitle}" data-menu="${menuTitle}" ${menuChecked}>
                                            <label class="form-check-label fw-bold text-primary" for="menu-${menuTitle}">${menuTitle}</label>
                                        </div>
                                        <div class="ms-4 submenu-container mt-2">
                                `;

                                menu.submenus.forEach(sub => {
                                    const subRoute = sub.route;
                                    const subLabel = sub.label;
                                    const subChecked = (allowedMenusObj.menus[menuTitle] && 
                                                        allowedMenusObj.menus[menuTitle].submenus && 
                                                        allowedMenusObj.menus[menuTitle].submenus[subRoute] && 
                                                        allowedMenusObj.menus[menuTitle].submenus[subRoute].enabled) ? 'checked' : '';

                                    const subActions = (allowedMenusObj.menus[menuTitle] && 
                                                        allowedMenusObj.menus[menuTitle].submenus && 
                                                        allowedMenusObj.menus[menuTitle].submenus[subRoute] && 
                                                        allowedMenusObj.menus[menuTitle].submenus[subRoute].actions) ? allowedMenusObj.menus[menuTitle].submenus[subRoute].actions : {};

                                    const viewChecked = subActions.view ? 'checked' : '';
                                    const createChecked = subActions.create ? 'checked' : '';
                                    const editChecked = subActions.edit ? 'checked' : '';
                                    const deleteChecked = subActions.delete ? 'checked' : '';

                                    menuHtml += `
                                        <div class="submenu-item mb-3 border-start ps-3 py-1">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input submenu-checkbox" id="sub-${subRoute}" data-menu="${menuTitle}" data-submenu="${subRoute}" ${subChecked}>
                                                <label class="form-check-label fw-semibold" for="sub-${subRoute}">${subLabel}</label>
                                            </div>
                                            <div class="ms-4 actions-container d-flex gap-3 mt-1">
                                                <div class="form-check form-check-inline mb-0">
                                                    <input type="checkbox" class="form-check-input action-checkbox" id="action-${subRoute}-view" data-menu="${menuTitle}" data-submenu="${subRoute}" data-action="view" ${viewChecked}>
                                                    <label class="form-check-label text-muted" for="action-${subRoute}-view">View</label>
                                                </div>
                                                <div class="form-check form-check-inline mb-0">
                                                    <input type="checkbox" class="form-check-input action-checkbox" id="action-${subRoute}-create" data-menu="${menuTitle}" data-submenu="${subRoute}" data-action="create" ${createChecked}>
                                                    <label class="form-check-label text-muted" for="action-${subRoute}-create">Create</label>
                                                </div>
                                                <div class="form-check form-check-inline mb-0">
                                                    <input type="checkbox" class="form-check-input action-checkbox" id="action-${subRoute}-edit" data-menu="${menuTitle}" data-submenu="${subRoute}" data-action="edit" ${editChecked}>
                                                    <label class="form-check-label text-muted" for="action-${subRoute}-edit">Edit</label>
                                                </div>
                                                <div class="form-check form-check-inline mb-0">
                                                    <input type="checkbox" class="form-check-input action-checkbox" id="action-${subRoute}-delete" data-menu="${menuTitle}" data-submenu="${subRoute}" data-action="delete" ${deleteChecked}>
                                                    <label class="form-check-label text-muted" for="action-${subRoute}-delete">Delete</label>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                });

                                menuHtml += `
                                        </div>
                                    </div>
                                `;

                                newContainer.append(menuHtml);
                            });
                        }

                        // Main menu checkbox change
                        newContainer.off('change', '.main-menu-checkbox').on('change', '.main-menu-checkbox', function() {
                            const isChecked = $(this).is(':checked');
                            const menu = $(this).data('menu');
                            newContainer.find(`.submenu-checkbox[data-menu="${menu}"], .action-checkbox[data-menu="${menu}"]`).prop('checked', isChecked);
                        });

                        // Submenu checkbox change
                        newContainer.off('change', '.submenu-checkbox').on('change', '.submenu-checkbox', function() {
                            const isChecked = $(this).is(':checked');
                            const menu = $(this).data('menu');
                            const submenu = $(this).data('submenu');
                            
                            newContainer.find(`.action-checkbox[data-submenu="${submenu}"]`).prop('checked', isChecked);
                            
                            if (isChecked) {
                                newContainer.find(`.main-menu-checkbox[data-menu="${menu}"]`).prop('checked', true);
                            } else {
                                const anySubChecked = newContainer.find(`.submenu-checkbox[data-menu="${menu}"]:checked`).length > 0;
                                if (!anySubChecked) {
                                    newContainer.find(`.main-menu-checkbox[data-menu="${menu}"]`).prop('checked', false);
                                }
                            }
                        });

                        // Action checkbox change
                        newContainer.off('change', '.action-checkbox').on('change', '.action-checkbox', function() {
                            const isChecked = $(this).is(':checked');
                            const menu = $(this).data('menu');
                            const submenu = $(this).data('submenu');

                            if (isChecked) {
                                newContainer.find(`.submenu-checkbox[data-submenu="${submenu}"]`).prop('checked', true);
                                newContainer.find(`.main-menu-checkbox[data-menu="${menu}"]`).prop('checked', true);
                            } else {
                                const anyActionChecked = newContainer.find(`.action-checkbox[data-submenu="${submenu}"]:checked`).length > 0;
                                if (!anyActionChecked) {
                                    newContainer.find(`.submenu-checkbox[data-submenu="${submenu}"]`).prop('checked', false);
                                    
                                    const anySubChecked = newContainer.find(`.submenu-checkbox[data-menu="${menu}"]:checked`).length > 0;
                                    if (!anySubChecked) {
                                        newContainer.find(`.main-menu-checkbox[data-menu="${menu}"]`).prop('checked', false);
                                    }
                                }
                            }
                        });
                        // location.reload();
                        // empTable.ajax.reload();
                    } else {
                        alert("Invalid ID.");
                    }
                }
            });

        });


        var mytable = $('#empTable').DataTable();

        // Delete record
        $('#empTable').on('click', '.deleteUser', function () {
            var id = $(this).data('id');

            var deleteConfirm = confirm("Are you sure?");
            if (deleteConfirm == true) {
                // AJAX request
                $.ajax({
                    url: "{{ route('deleteAdminList') }}",
                    type: 'post',
                    data: {
                        _token: CSRF_TOKEN,
                        id: id
                    },
                    success: function (response) {
                        alert(response.message);
                        mytable.row(this).remove().draw(false);
                        $('#empTable').DataTable().ajax.reload();
                    }
                });
            }

        });


        //  record removeAppAdminAccess
        var mytableAppAdminTable = $('#AppAdminTable').DataTable();
        $('#AppAdminTable').on('click', '.removeAppAdminAccess', function () {
            var id = $(this).data('id');

            var deleteConfirm = confirm("Are you sure?");
            if (deleteConfirm == true) {
                // AJAX request
                $.ajax({
                    url: "{{ route('removeAppAdminAccess') }}",
                    type: 'post',
                    data: {
                        _token: CSRF_TOKEN,
                        id: id
                    },
                    success: function (response) {
                        alert(response.message);
                        mytableAppAdminTable.row(this).remove().draw(false);
                        $('#AppAdminTable').DataTable().ajax.reload()
                    }
                });
            }

        });

    });
</script>