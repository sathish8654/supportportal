@extends('layouts.app')
@section('content')
@include('modals/editUnitUserInfo')
@include('modals/updateModalImei')
    <div class="container-flux m-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>{{ __('Manage User Accounts') }}</span>
                        <button type="button" class="btn btn-success btn-sm addNewDevice" data-bs-toggle="modal"
                            data-bs-target="#updateUserModal">
                            <i class="fa fa-user-plus"></i> Add User Device
                        </button>
                    </div>

                    <div class="card-body">

<!-- //////////////////////////// -->
<style>
    #image-preview {
        width: 250px;
        height: 250px;
        border: 2px solid #ccc;
        margin-top: 10px;
        display: block;
        object-fit: cover;
    }
</style>

<script>
    $(document).ready(function() {
        // Preview when file selected
        $("#filePhoto").change(function() {
            readURL(this);
        });

        // Clear preview and reset input
        $("#clearBtn").click(function() {
            $("#image-preview").attr("src", "").hide(); // hide image
            $("#filePhoto").val("");
            $("#image_exist").val(""); // for clear image
            $(".image-btn").hide();
        });
    });

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            $("#image-preview").hide();

            reader.onload = function(e) {
                $('#image-preview')
                    .attr('src', e.target.result)
                    .on('load', function() {
                        $("#image-preview").show();
                        $(".image-btn").show();
                    });
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
                        <!-- Table -->
                        <table id='empTable' class='datatable table table-striped table-bordered table-hover'
                            style="width:100%">
                            <thead>
                                <tr>
                                    <td>SNo</td>
                                    <td>Name</td>
                                    <td>Mobile</td>
                                    <td>IMEI</td>
                                    <td>Village</td>
                                    <td>City</td>
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

            // Initialize DataTable
            var empTable = $('#empTable').DataTable({
                initComplete: function(settings) {
                    $('#' + settings.nTable.id + '_filter input').wrap(`
                        <div class="d-inline-flex position-relative"></div>
                    `).after(`
                        <button type="button" class="close position-absolute" aria-label="Close" style="right:5px">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    `).attr('required','required').attr('title','Search');

                    // Click Event on Clear button
                    $(document).on('click', '#' + settings.nTable.id + '_filter button', function(){
                        $('#' + settings.nTable.id).DataTable({
                            "retrieve": true,
                        }).search('').draw();
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
                language: { 
                    processing: "<svg class='loader' id='loader' viewBox='0 0 340 340'><circle cx='170' cy='170' r='160' stroke='#E2007C'/><circle cx='170' cy='170' r='135' stroke='#404041'/><circle cx='170' cy='170' r='110' stroke='#E2007C'/><circle cx='170' cy='170' r='85' stroke='#404041'/></svg>"
                },
                ajax: "{{ route('getUserListDevice') }}",
                columns: [
                    {
                        data: 'DT_RowIndex',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'username'
                    },
                    {
                        data: 'mobile'
                    },
                    {
                        data: 'imei'
                    },
                    {
                        data: 'village'
                    },
                    {
                        data: 'city'
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false,
                        visible: {{ (Auth::user()->hasMenuPermission('Users', 'userDevice', 'edit') || Auth::user()->hasMenuPermission('Users', 'userDevice', 'view') || Auth::user()->hasMenuPermission('Users', 'userDevice', 'delete')) ? 'true' : 'false' }}
                    },
                ]
            });

            // Add new device button clicked
            $(document).on('click', '.addNewDevice', function() {
                $('#userModalTitle').text('Add User Device');
                $('#dev_usr_id').val('');
                $('#device_firstname').val('');
                $('#device_mobile').val('');
                $('#device_imei').val('');
                $('#device_village').val('');
                $('#device_city').val('');
                $('#modes').val(1);
                $('#SubmitUpdate1').hide();
                $('#submitCreate1').show();
            });

            // Edit User Device clicked
            $('#empTable').on('click', '.editUserInfo', function() {
                var id       = $(this).data('id');
                var username = $(this).data('username');
                var mobile   = $(this).data('mobile');
                var imei     = $(this).data('imei');
                var village  = $(this).data('village');
                var city     = $(this).data('city');

                $('#userModalTitle').text('Edit User Device');
                $('#dev_usr_id').val(id);
                $('#device_firstname').val(username);
                $('#device_mobile').val(mobile);
                $('#device_imei').val(imei);
                $('#device_village').val(village);
                $('#device_city').val(city);
                $('#modes').val(2);

                $('#SubmitUpdate1').show();
                $('#submitCreate1').hide();
            });

            // Edit Unit Info (JOB CARD) clicked
            $('#empTable').on('click', '.editUnitInfo', function() {
                var id = $(this).data('id'); // user id
                var imei = $(this).data('imei'); // imei no
                var username = $(this).data('username'); // username
                var mode = $(this).data('mode');

                $('#txt_imeiid').val(id);
                $('#TextBoxContainer').empty();
                $('#ebContainer').empty();

                // AJAX request
                $.ajax({
                    url: "{{ route('getImeiList') }}",
                    type: 'post',
                    data: {
                        _token: CSRF_TOKEN,
                        id: id,
                        mode: mode,
                        imei: imei,
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success == 1) {
                            // Populate all common fields
                            $('#username').val(username);
                            $('#imei').val(response.imei);
                            $('#note').val(response.note);
                            $('#log_days').val(response.log_days);
                            $('#gsm_module').val(response.gsm_module);
                            $('#sim_scope').val(response.sim_scope);
                            $('#lora_module').val(response.lora_module);
                            $('#lora_freq').val(response.lora_freq);
                            $('#lora_channel').val(response.lora_channel);
                            $('#lora_SF').val(response.lora_SF);
                            $('#lora_joint').val(response.lora_joint);
                            $('#master').val(response.master);
                            $('#master_serial_no').val(response.master_serial_no);
                            $('#master_imei_no').val(response.master_imei_no);
                            $('#master_os').val(response.master_os);
                            $('#repeter').prop('checked', response.repeter === 'yes');
                            $('#valve_type').val(response.valve_type);
                            $('#valve_qty').val(response.valve_qty);
                            $('#butterfly_valve').val(response.butterfly_valve);
                            $('#cable_mtr').val(response.cable_mtr);
                            $('#cable_brand').val(response.cable_brand);
                            $('#high_gain').val(response.high_gain);
                            $('#eb_power_3p').val(response.eb_power_3p);
                            $('#eb_power_1p').val(response.eb_power_1p);
                            $('#solar_power').val(response.solar_power);
                            $('#level_sensor').val(response.level_sensor);
                            $('#presere_sensor').val(response.presere_sensor);
                            $('#mois_sensor').val(response.mois_sensor);
                            $('#npk').val(response.npk);
                            $('#ec').val(response.ec);
                            $('#ph').val(response.ph);
                            $('#all_in').val(response.all_in);
                            $('#tank_count').val(response.tank_count);
                            $('#filter').prop('checked', response.filter === 'yes');
                            $('#flow_sensor').prop('checked', response.flow_sensor === 'yes');
                            $('#weather_monitor').prop('checked', response.weather_monitor === 'yes');
                            $('#fert_sys').prop('checked', response.fert_sys === 'yes');
                            $('#agitator_tape').val(response.agitator_tape);
                            $('#injectTape').val(response.injectTape);
                            $('#valve_fitting').val(response.valve_fitting);
                            $('#install_date').val(response.install_date);
                            $('#prog_folder').val(response.prog_folder);
                            $('#install_actuator').val(response.install_actuator);
                            $('#panel').val(response.panel);
                            $('#install_done').val(response.install_done);
                            $('#sales_person').val(response.sales_person);
                            $('#location').val(response.location);
                            $('#customized').val(response.customized);

                            // Check fertilizer status layout
                            if (response.fert_sys == 'yes') {
                                $('.fertDiv').addClass('d-flex');
                                $('.fertHide').show();
                            } else {
                                $('.fertHide').hide();
                                $('.fertDiv').removeClass('d-flex');
                            }

                            // Populate dynamic valve rows
                            var device_valve_types = response?.device_valve_types ?? [];
                            for (var i = 0; i < device_valve_types.length; i++) {
                                var valve = device_valve_types[i];
                                if (valve.status == 1) {
                                    var row = GetDynamicTextBox();
                                    var $row = $(row);
                                    $row.find("select[name='valve_type[]']").val(valve.name_type);
                                    $row.find("input[name='valve_qty[]']").val(valve.value_qty);
                                    $("#TextBoxContainer").append($row);
                                } else {
                                    var row = GetDynamicTextBox1();
                                    var $row = $(row);
                                    $row.find("select[name='eb_type[]']").val(valve.name_type);
                                    $row.find("input[name='eb_qty[]']").val(valve.value_qty);
                                    $("#ebContainer").append($row);
                                }
                            }

                            // Populate image previews
                            if (response.image_url && response.image_url != '') {
                                $("#image-preview").show();
                                $("#image-preview").attr("src", response.image_url); 
                                $("#imgAppend").attr("href", response.image_url);
                                $('#image_exist').val(response.image_exist); 
                                $('#clearBtn').show();
                            } else {
                                $("#image-preview").hide();
                                $("#imgAppend").attr("href", "#");
                                $('#image_exist').val('');
                                $('#clearBtn').hide();
                            }

                            // Handle mode specific toggle controls
                            if (response.mode == 1) { // for edit
                                $('fieldset').prop('disabled', false);
                                $(".btnSave").show();
                                $(".btnUpdate").hide();
                                $('#mode').val(response.mode);
                            } else {
                                $('#mode').val(response.mode);
                                $(".btnSave").hide();
                                $(".btnUpdate").show();
                                $('fieldset').prop('disabled', false);
                            }
                        } else {
                            alert("Invalid IMEI No !. Please register the valid IMEI No to the User.");
                        }
                    },
                    error: function() {
                        alert('Failed to retrieve IMEI details.');
                    }
                });
            });

            // View unit info clicked
            $('#empTable').on('click', '.viewUnitInfo', function() {
                var id = $(this).data('id');
                var mode = $(this).data('mode');
                var imei = $(this).data('imei');

                $('.appendProductModelData').html('<div class="text-center p-3"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');

                $.ajax({
                    url: "{{ route('appendImeiPrint') }}",
                    type: 'post',
                    data: {
                        _token: CSRF_TOKEN,
                        id: id,
                        mode: mode,
                        imei: imei,
                    },
                    success: function(data) {
                        $('.appendProductModelData').html(data.table_view);
                    },
                    error: function() {
                        $('.appendProductModelData').html('<div class="alert alert-danger">Failed to load unit details.</div>');
                    }
                });
            });

            // Save / update unit record in Job Card modal
            $('.btn_save').click(function() {
                var id = $('#txt_imeiid').val();
                var fileToUpload = $('#filePhoto')[0].files[0];
                var image_exist = $('#image_exist').val();
                var imei = $('#imei').val().trim();
                var mode = $('#mode').val().trim();
                var note = $('#note').val().trim();
                var log_days = $('#log_days').val().trim();
                var gsm_module = $('#gsm_module').val();
                var sim_scope = $('#sim_scope').val();
                var lora_module = $('#lora_module').val();
                var lora_freq = $('#lora_freq').val();
                var lora_channel = $('#lora_channel').val();
                var lora_SF = $('#lora_SF').val();
                var lora_joint = $('#lora_joint').val();
                var master = $('#master').val();
                var master_serial_no = $('#master_serial_no').val();
                var master_imei_no = $('#master_imei_no').val();
                var master_os = $('#master_os').val();
                var repeter = $('#repeter').is(':checked') ? 'yes' : 'no';
                var valve_type = $('#valve_type').val();
                var valve_qty = $('#valve_qty').val();
                var butterfly_valve = $('#butterfly_valve').val();
                var cable_mtr = $('#cable_mtr').val();
                var cable_brand = $('#cable_brand').val();
                var high_gain = $('#high_gain').val();
                var eb_power_3p = $('#eb_power_3p').val();
                var eb_power_1p = $('#eb_power_1p').val();
                var solar_power = $('#solar_power').val();
                var level_sensor = $('#level_sensor').val();
                var presere_sensor = $('#presere_sensor').val();
                var mois_sensor = $('#mois_sensor').val();
                var npk = $('#npk').val();
                var ec = $('#ec').val();
                var ph = $('#ph').val();
                var all_in = $('#all_in').val();
                var tank_count = $('#tank_count').val();
                var filter = $('#filter').is(':checked') ? 'yes' : 'no';
                var flow_sensor = $('#flow_sensor').is(':checked') ? 'yes' : 'no';
                var weather_monitor = $('#weather_monitor').is(':checked') ? 'yes' : 'no';
                var fert_sys = $('#fert_sys').is(':checked') ? 'yes' : 'no';
                var agitator_tape = $('#agitator_tape').val();
                var injectTape = $('#injectTape').val();

                var valve_fitting = $('#valve_fitting').val();
                var install_date = $('#install_date').val();
                var prog_folder = $('#prog_folder').val();
                var install_actuator = $('#install_actuator').val();
                var panel = $('#panel').val();
                var install_done = $('#install_done').val();
                var sales_person = $('#sales_person').val();
                var location = $('#location').val();
                var customized = $('#customized').val();

                // Get all valve_type values
                var valveTypes = $("select[name='valve_type[]']").map(function() {
                    return $(this).val();
                }).get();

                // Get all valve_qty values
                var valveQtys = $("input[name='valve_qty[]']").map(function() {
                    return $(this).val();
                }).get();

                // Get all eb power values
                var ebTypes = $("select[name='eb_type[]']").map(function() {
                    return $(this).val();
                }).get();

                // Get all eb qty values
                var ebQtys = $("input[name='eb_qty[]']").map(function() {
                    return $(this).val();
                }).get();

                // Prepare FormData
                var form_data = new FormData();
                form_data.append('_token', CSRF_TOKEN);
                form_data.append('id', id);
                form_data.append('mode', mode);
                form_data.append('upload', fileToUpload);
                form_data.append('image_exist', image_exist);
                form_data.append('imei', imei);
                form_data.append('note', note);
                form_data.append('log_days', log_days);
                form_data.append('gsm_module', gsm_module);
                form_data.append('sim_scope', sim_scope);
                form_data.append('lora_module', lora_module);
                form_data.append('lora_freq', lora_freq);
                form_data.append('lora_channel', lora_channel);
                form_data.append('lora_SF', lora_SF);
                form_data.append('lora_joint', lora_joint);
                form_data.append('master', master);
                form_data.append('master_serial_no', master_serial_no);
                form_data.append('master_imei_no', master_imei_no);
                form_data.append('master_os', master_os);
                form_data.append('repeter', repeter);
                form_data.append('valve_type', valve_type);
                form_data.append('valve_qty', valve_qty);
                form_data.append('butterfly_valve', butterfly_valve);
                form_data.append('valveTypes', valveTypes);
                form_data.append('valveQtys', valveQtys);
                form_data.append('ebTypes', ebTypes);
                form_data.append('ebQtys', ebQtys);
                form_data.append('cable_mtr', cable_mtr);
                form_data.append('cable_brand', cable_brand);
                form_data.append('high_gain', high_gain);
                form_data.append('eb_power_3p', eb_power_3p);
                form_data.append('eb_power_1p', eb_power_1p);
                form_data.append('solar_power', solar_power);
                form_data.append('level_sensor', level_sensor);
                form_data.append('presere_sensor', presere_sensor);
                form_data.append('mois_sensor', mois_sensor);
                form_data.append('npk', npk);
                form_data.append('ec', ec);
                form_data.append('ph', ph);
                form_data.append('all_in', all_in);
                form_data.append('filter', filter);
                form_data.append('flow_sensor', flow_sensor);
                form_data.append('weather_monitor', weather_monitor);
                form_data.append('fert_sys', fert_sys);
                form_data.append('tank_count', tank_count);
                form_data.append('agitator_tape', agitator_tape);
                form_data.append('injectTape', injectTape);
                form_data.append('valve_fitting', valve_fitting);
                form_data.append('install_date', install_date);
                form_data.append('prog_folder', prog_folder);
                form_data.append('install_actuator', install_actuator);
                form_data.append('panel', panel);
                form_data.append('install_done', install_done);
                form_data.append('sales_person', sales_person);
                form_data.append('location', location);
                form_data.append('customized', customized);
                
                if (imei != '') {
                    $.ajax({
                        url: "{{ route('updateImeiList') }}",
                        type: 'post',
                        data: form_data,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        success: function(response) {
                            if (response.success == 1) {
                                alert(response.msg);
                                $('#imei, #note').val('');
                                $('#txt_imeiid').val(0);
                                $('#updateModalImei').modal('hide');
                                $('#empTable').DataTable().ajax.reload(null, false);
                            } else {
                                alert(response.msg);
                            }
                        },
                        error: function() {
                            alert('Failed to save update.');
                        }
                    });
                } else {
                    alert('Please fill all fields.');
                }
            });

            // Delete record
            $('#empTable').on('click', '.deleteUnit', function() {
                var id = $(this).data('id');

                var deleteConfirm = confirm("Are you sure delete?");
                if (deleteConfirm == true) {
                    $.ajax({
                        url: "{{ route('deleteDeviceUser') }}",
                        type: 'post',
                        data: {
                            _token: CSRF_TOKEN,
                            id: id
                        },
                        success: function(response) {
                            alert(response.message);
                            $('#empTable').DataTable().ajax.reload(null, false);
                        },
                        error: function() {
                            alert('Failed to delete record.');
                        }
                    });
                }
            });

        });

        // Reset modals when closed
        $('#updateModalImei, #updateUserModal').on('hidden.bs.modal', function () {
            $('#TextBoxContainer').empty();
            $('#ebContainer').empty();
            $(this).find('input[type=text], input[type=email], input[type=number], textarea').val('');
            $(this).find('select').prop('selectedIndex', 0);
            $(this).find('input[type=checkbox], input[type=radio]').prop('checked', false);
            $('#SubmitUpdate1').hide();
            $('#submitCreate1').show();
            $('#modes').val(1);
            $('#userModalTitle').text('Add User Device');
        });
    </script>

    <!-- for imei page print module add -->
    <div id="printImei" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">JOB CARD</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="overflow-x: auto;">
                    <div class="appendProductModelData"></div>  
                </div>
            </div>
        </div>
    </div>
@endsection
