
<!-- Modal -->
<div class="modal fade" id="updateUserModal" tabindex="-1" role="dialog" aria-labelledby="userModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            @csrf
            <div class="modal-header">
                <h4 class="modal-title" id="userModalTitle">Add User Device</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="container px-4">
                    <div class="p-3 border bg-light">
                        <div class="row gx-5">
                            <input type="hidden" class="form-control" id="dev_usr_id">
                            <input type="hidden" class="form-control" id="modes" value="1">
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="device_firstname">Firstname *</label>
                                    <input type="text" class="form-control" id="device_firstname" placeholder="Enter firstname">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="device_mobile">Mobile *</label>
                                    <input type="number" class="form-control" id="device_mobile" placeholder="Enter Mobile Number">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="device_imei">IMEI *</label>
                                    <input type="text" class="form-control" id="device_imei" placeholder="Enter IMEI">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="device_village">Village *</label>
                                    <input type="text" class="form-control" id="device_village" placeholder="Enter Village">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="device_city">City *</label>
                                    <input type="text" class="form-control" id="device_city" name="contact" placeholder="Enter City" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-success save_device_user" id="SubmitUpdate1" style="display:none;">Update</button>
                    <button type="button" class="btn btn-success save_device_user" id="submitCreate1">Create</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script -->
<script type="text/javascript">
$(document).ready(function () {
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

    function sendUserDeviceAjax(confirmAddImei) {
        var device_firstname = $("#device_firstname").val().trim();
        var device_mobile    = $("#device_mobile").val().trim();
        var device_imei      = $("#device_imei").val().trim();
        var device_village   = $("#device_village").val().trim();
        var device_city      = $("#device_city").val().trim();
        var modes            = $("#modes").val().trim();
        var dev_usr_id       = $("#dev_usr_id").val();
        
        if (device_firstname === "") { alert("First name is required"); $("#device_firstname").focus(); return false; } 
        if (device_mobile === "") { alert("Mobile number is required"); $("#device_mobile").focus(); return false; } 
        if (device_imei === "") { alert("IMEI is required"); $("#device_imei").focus(); return false; } 
        if (device_village === "") { alert("Village is required"); $("#device_village").focus(); return false; } 
        if (device_city === "") { alert("City is required"); $("#device_city").focus(); return false; }

        $.ajax({
            type: 'POST',
            url: "{{ route('addUpdateUserDevice') }}",
            data: {
                _token: CSRF_TOKEN,
                device_firstname: device_firstname,
                device_mobile: device_mobile,
                device_imei: device_imei,
                device_village: device_village,
                device_city: device_city,
                modes: modes,
                dev_usr_id: dev_usr_id,
                confirm_add_imei: confirmAddImei ? 1 : 0,
            },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'imei_not_found') {
                    var userConfirm = confirm(response.message);
                    if (userConfirm) {
                        sendUserDeviceAjax(true);
                    }
                    return;
                }

                alert(response.success || (response.msg == 1 ? 'Updated successfully' : 'Success'));
                $('#updateUserModal').modal('hide');
                $('#empTable').DataTable().ajax.reload(null, false);
            },
            error: function (response) {
                if (response.responseJSON && response.responseJSON.message) {
                    alert(response.responseJSON.message);
                } else {
                    alert('Error saving record');
                }
            }
        });
    }

    // Create or update record
    $(".save_device_user").click(function (e) {
        e.preventDefault();
        sendUserDeviceAjax(false);
    });
});
</script>
