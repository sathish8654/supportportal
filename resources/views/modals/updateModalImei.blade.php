                    <div id="updateModalImei" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-xl" role="document">

                                <!-- Modal content-->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">JOB CARD</h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                    <fieldset disabled>
                                         <!-- ////////////// for new form -->
                    <div class="row mb-1">
                      <div class="col-md-3">   
                     <label for="imei">IMEI</label>
                     <input type="text" class="form-control mb-1 mb-1" id="imei" placeholder="Enter IMEI" readonly>
                       </div>
                       <div class="col-md-3">   
                     <label for="imei">Username</label>
                     <input type="text" class="form-control mb-1 mb-1" id="username" readonly>
                       </div>
                       <div class="col-md-3">
                 <!-- <div class="mb-1"> -->
                     <label for="note">Master Serial No:</label>
                     <input type="text" class="form-control mb-1" id="log_days" placeholder="Enter Log days count">
                     </div>
                      <div class="col-md-3">
                      <label  for="noteLog">GSM Module Type:</label>
                         <select class="form-control mb-1 custom-select browser-default" id="gsm_module" required>
                             <option selected disabled>Select GSM</option>
                             @if(!empty($gsm_module))
                             @foreach ($gsm_module as $item)
                                 <option value="{{ $item->id }}">{{ $item->name }} </option>
                             @endforeach
                             @endif
                         </select>
                     </div>
                     <div class="col-md-3">
                     <label  for="noteLog">Sim Scope:</label>
                         <select class="form-control mb-1 custom-select browser-default" id="sim_scope" required>
                             <option selected disabled>Select Sim</option>
                             @if(!empty($sim_scope))
                             @foreach ($sim_scope as $item)
                                 <option value="{{ $item->id }}">{{ $item->name }} </option>
                             @endforeach
                             @endif
                         </select>
                    
                     </div>
                     <div class="col-md-3">
                     <label  for="noteLog">LORA Module:</label>
                         <select class="form-control mb-1 custom-select browser-default" id="lora_module" required>
                             <option selected disabled>Select</option>
                             @if(!empty($lora_module))
                             @foreach ($lora_module as $item)
                                 <option value="{{ $item->id }}">{{ $item->name }} </option>
                             @endforeach
                             @endif
                             
                         </select>
                     </div>
                     <div class="col-md-3">
                     <label  for="noteLog">LORA Frequency:</label>
                     <input type="text" class="form-control mb-1" id="lora_freq">
                     </div>
                     <!-- <div class="col-md-3">
                     <label  for="noteLog">LORA Frequency:</label>
                         <select class="form-control mb-1 custom-select browser-default" id="lora_freq" required>
                             <option selected disabled>Select</option>
                             <option value="Nill">Nill</option>
                             @for ($i = 0; $i <= 23; $i++)
                             <option value="{{ $i }}">{{ $i }}</option>
                             @endfor

                         </select>
                     </div> -->
                     <!-- <div class="col-md-3">
                     <label  for="noteLog">LORA Channel:</label>
                         <select class="form-control mb-1 custom-select browser-default" id="lora_channel" required>
                             <option selected disabled>Select</option>
                             <option value="Nill">Nill</option>
                             @for ($i = 0; $i <= 23; $i++)
                             <option value="{{ $i }}">{{ $i }}</option>
                             @endfor

                         </select>
                     </div> -->
                     <div class="col-md-3">
                     <label  for="noteLog">LORA Channel:</label>
                     <input type="text" class="form-control mb-1" id="lora_channel">
                     </div>
                     <div class="col-md-3">
                     <label  for="noteLog">LORA SF:</label>
                         <select class="form-control mb-1 custom-select browser-default" id="lora_SF" required>
                             <option selected disabled>Select</option>
                             <option value="Nill">Nill</option>
                             @for ($i = 0; $i <= 12; $i++)
                             <option value="{{ $i }}">S{{ $i }}</option>
                             @endfor

                         </select>
                     </div>
                     <div class="col-md-3">
                     <label  for="noteLog">LORA Joint:</label>
                         <select class="form-control mb-1 custom-select browser-default" id="lora_joint" required>
                             <option selected disabled>Select</option>
                             @if(!empty($lora_joint))
                             @foreach ($lora_joint as $item)
                                 <option value="{{ $item->id }}">{{ $item->name }} </option>
                             @endforeach
                             @endif
                         </select>
                     </div>
                     <div class="col-md-3">
                     <label  for="noteLog">Master:</label>
                         <select class="form-control mb-1 custom-select browser-default" id="master" required>
                             <option selected disabled>Select</option>
                             @if(!empty($master))
                             @foreach ($master as $item)
                                 <option value="{{ $item->id }}">{{ $item->name }} </option>
                             @endforeach
                             @endif
                         </select>
                     </div>

                     <!-- <label class="col-md-5 mb-1" for="noteLog">Master Serial No:</label>
                     <div class="col-md-7">
                     <input type="text" class="form-control mb-1" id="master_serial_no">
                     </div> -->
                     <!-- <div class="col-md-3">
                     <label  for="noteLog">Master IMEI No:</label> -->
                     <input type="hidden" class="form-control mb-1" id="master_imei_no">
                     <!-- </div> -->
                     <div class="col-md-3">
                     <label  for="noteLog">Master OS Version:</label>
                     <input type="text" class="form-control mb-1" id="master_os">
                     </div>
                     <div class="col-md-3 d-flex align-items-center mt-4">
                         <div class="form-check">
                             <input class="form-check-input" type="checkbox" id="repeter" value="yes">
                             <label class="form-check-label ms-1 fw-bold text-dark" for="repeter">Repeter</label>
                         </div>
                     </div>
                     <div class="col-md-3">
                     <label  for="noteLog">Valve Type:</label>
                         <select class="form-control mb-1 custom-select browser-default" id="valve_type" required>
                             <option selected disabled>Select</option>
                             @if(!empty($valve_type))
                             @foreach ($valve_type as $item)
                                 <option value="{{ $item->id }}">{{ $item->name }} </option>
                             @endforeach
                             @endif
                         </select>
                     </div>
                     <div class="col-md-3">
                     <label  for="noteLog">Valve Qty:</label>
                     <input type="text" class="form-control mb-1 numberonly" id="valve_qty">
                     </div>
                     <div class="col-md-3">
                     <label  for="noteLog">Butterfly Valve Type:</label>
                         <select class="form-control mb-1 custom-select browser-default" id="butterfly_valve" required>
                             <option selected disabled>Select</option>
                             @if(!empty($butterfly_valve))
                             @foreach ($butterfly_valve as $item)
                                 <option value="{{ $item->id }}">{{ $item->name }} </option>
                             @endforeach
                             @endif
                         </select>
                     </div>
                     <div class="col-md-3">
                     <label  for="noteLog">Valve Fitting:</label>
                     <input type="text" class="form-control mb-1" id="valve_fitting">
                     </div>
                     <!-- <div class="col-md-12">
                     <input type="checkbox"  name="option" value="value1">
                     <label for="option1">Option 1</label>
                     </div> -->
                     <!-- <div class="col-md-3">
                     <label  for="noteLog">2.5" Valve Qty:</label>
                     <input type="text" class="form-control mb-1 numberonly" id="2_5_inch_valve">
                     </div>
                     <div class="col-md-3">
                     <label  for="noteLog">3" Valve Qty:</label>
                     <input type="text" class="form-control mb-1 numberonly" id="3_inch_valve">
                     </div>

                     <label  for="noteLog">4" Valve Qty:</label>
                     <div class="col-md-7">
                     <input type="text" class="form-control mb-1 numberonly" id="4_inch_valve">
                     </div>

                     <label class="col-md-5 mb-1" for="noteLog">6" Valve Qty:</label>
                     <div class="col-md-7">
                     <input type="text" class="form-control mb-1 numberonly" id="6_inch_valve">
                     </div>

                     <label class="col-md-5 mb-1" for="noteLog">8" Valve Qty:</label>
                     <div class="col-md-7">
                     <input type="text" class="form-control mb-1 numberonly" id="8_inch_valve">
                     </div>

                     <label class="col-md-5 mb-1" for="noteLog">10" Valve Qty:</label>
                     <div class="col-md-7">
                     <input type="text" class="form-control mb-1 numberonly" id="10_inch_valve">
                     </div> -->
                     <div class="col-md-12 mt-2">
                     <div class="table-responsive ">
                     <table class="table table-striped table-bordered">
                         <thead>
                         <tr style="background-color: aliceblue;">
                             <th width="60%">Valve Type (Inch)</th>
                             <th width="30%">Valve Qty</th>
                             <th width="10%">Action</th>
                         </tr>
                         </thead>
                         <tbody id="TextBoxContainer">
                         <!-- dynamic rows will be appended here -->
                         </tbody>
                     </table>
                     </div>
                     </div>
                     <div class="col-md-12">
                     <button id="btnAdd" type="button" class="btn btn-success btn-sm float-right">Add Row</button>
                     </div>
             <script>
             $(function () {
                 $("#btnAdd").on("click", function () {
                     var row = GetDynamicTextBox();
                     $("#TextBoxContainer").append(row);
                     $('.teamSubBtn').removeAttr("hidden");
                 });

                 $("body").on("click", ".remove", function () {
                     $(this).closest("tr").remove();
                 });
             });

             var valType = ['1','2','2.5','3','4','6','8','10'];
                 var i = 0;

                 function GetDynamicTextBox() {
                     i++;

                     // Build options dynamically from valType array
                     var options = '';
                     for (var j = 0; j < valType.length; j++) {
                         options += '<option value="'+valType[j]+'">'+valType[j]+'" Inch</option>';
                     }

                     return '<tr>' +
                         '<td>' +
                             
                                 '<select id="valve_type_'+i+'" name="valve_type[]" class="form-control mb-1"><option value="">--Select--</option>' +
                                     options +
                                 '</select>' +
                           
                         '</td>' +
                         '<td>' +
                             '<input type="text" class="form-control mb-1 numberonly" id="valve_qty_'+i+'" name="valve_qty[]">' +
                         '</td>' +
                         '<td>' +
                             '<button type="button" class="btn btn-danger remove">' +
                                 '<i class="fa fa-remove"></i>' +
                             '</button>' +
                         '</td>' +
                     '</tr>';
                 }

             </script>

                     <div class="col-md-3">
                     <label  for="noteLog">Cable - Meter:</label>
                     <input type="text" class="form-control mb-1" id="cable_mtr">
                     </div>
                     <div class="col-md-3">
                     <label  for="noteLog">Cable Brand:</label>
                     <input type="text" class="form-control mb-1" id="cable_brand">
                     </div>

                     <div class="col-md-3">
                     <label  for="noteLog">High Gain:</label>
                     <input type="text" class="form-control mb-1" id="high_gain">
                     </div>
                     <!-- <div class="col-md-3">
                     <label  for="noteLog">EB Power 3P:</label>
                     <input type="text" class="form-control mb-1" id="eb_power_3p">
                     </div>
                     <div class="col-md-3">
                     <label  for="noteLog">EB Power 1P:</label>
                     <input type="text" class="form-control mb-1" id="eb_power_1p">
                     </div> -->
                     <div class="col-md-12 mt-2">
                     <div class="table-responsive ">
                     <table class="table table-striped table-bordered">
                         <thead>
                         <tr style="background-color: aliceblue;">
                             <th width="60%">EB Power</th>
                             <th width="30%">Value</th>
                             <th width="10%">Action</th>
                         </tr>
                         </thead>
                         <tbody id="ebContainer">
                         <!-- dynamic rows will be appended here -->
                         </tbody>
                     </table>
                     </div>
                     </div>
                     <div class="col-md-12">
                     <button id="ebAdd" type="button" class="btn btn-success btn-sm float-right">Add Row</button>
                     </div>
             <script>
             $(function () {
                 $("#ebAdd").on("click", function () {
                     var row = GetDynamicTextBox1();
                     $("#ebContainer").append(row);
                 });

                 $("body").on("click", ".remove", function () {
                     $(this).closest("tr").remove();
                 });
             });

             var valType1 = ['1P','3P'];
                 var i = 0;

                 function GetDynamicTextBox1() {
                     i++;

                     // Build options dynamically from valType array
                     var options = '';
                     for (var j = 0; j < valType1.length; j++) {
                         options += '<option value="'+valType1[j]+'">'+valType1[j]+'</option>';
                     }

                     return '<tr>' +
                         '<td>' +
                             
                                 '<select id="eb_type_'+i+'" name="eb_type[]" class="form-control mb-1"><option value="">--Select--</option>' +
                                     options +
                                 '</select>' +
                           
                         '</td>' +
                         '<td>' +
                             '<input type="text" class="form-control mb-1 numberonly" id="eb_qty_'+i+'" name="eb_qty[]">' +
                         '</td>' +
                         '<td>' +
                             '<button type="button" class="btn btn-danger remove">' +
                                 '<i class="fa fa-remove"></i>' +
                             '</button>' +
                         '</td>' +
                     '</tr>';
                 }

             </script>
                     <div class="col-md-3">
                     <label  for="noteLog">Solar Power:</label>
                     <input type="text" class="form-control mb-1" id="solar_power">
                     </div>
                     <div class="col-md-3">
                     <label  for="noteLog">Level Sensor:</label>
                         <select class="form-control mb-1 custom-select browser-default" id="level_sensor" required>
                             <option selected disabled>Select</option>
                             @if(!empty($level_sensor))
                             @foreach ($level_sensor as $item)
                                 <option value="{{ $item->id }}">{{ $item->name }} </option>
                             @endforeach
                             @endif
                         </select>
                     </div>
                     <div class="col-md-3">
                     <label  for="noteLog">Presere Sensor Qty:</label>
                     <input type="text" class="form-control mb-1 numberonly" id="presere_sensor">
                     </div>
                     <div class="col-md-3">
                         <label for="noteLog">Sensor Moistoreter:</label>
                         <input type="text" class="form-control mb-1" id="mois_sensor">
                     </div>
                     <div class="col-md-3 d-flex align-items-center mt-4">
                         <div class="form-check">
                             <input class="form-check-input" type="checkbox" id="filter" value="yes">
                             <label class="form-check-label ms-1 fw-bold text-dark" for="filter">Filter</label>
                         </div>
                     </div>
                     <div class="col-md-3 d-flex align-items-center mt-4">
                         <div class="form-check">
                             <input class="form-check-input" type="checkbox" id="flow_sensor" value="yes">
                             <label class="form-check-label ms-1 fw-bold text-dark" for="flow_sensor">Flow Sensor</label>
                         </div>
                     </div>
                     <div class="col-md-3 d-flex align-items-center mt-4">
                         <div class="form-check">
                             <input class="form-check-input" type="checkbox" id="weather_monitor" value="yes">
                             <label class="form-check-label ms-1 fw-bold text-dark" for="weather_monitor">Weather Monitor</label>
                         </div>
                     </div>
                     
                     <div class="col-md-3 d-flex align-items-center mt-4">
                         <div class="form-check">
                             <input class="form-check-input" type="checkbox" id="fert_sys" value="yes">
                             <label class="form-check-label ms-1 fw-bold text-dark" for="fert_sys">Fertilizer System</label>
                         </div>
                     </div>
                     <script>
                         $(document).ready(function() {
                             $('.fertHide').hide();
                             $('#fert_sys').on('change', function() {
                                if ($(this).is(':checked')) {
                                    $('.fertDiv').addClass('d-flex');
                                    $('.fertHide').show();
                                } else {
                                    $('.fertHide').hide();
                                    $('.fertDiv').removeClass('d-flex');
                                }
                             });
                         });
                     </script>
                                          <!-- Fertilizer System Section Card -->
                      <div class="col-md-12 fertHide mb-3">
                          <div class="card border border-indigo-subtle shadow-sm bg-light-subtle rounded-3">
                              <div class="card-header bg-indigo text-white py-2 px-3 d-flex align-items-center rounded-top-3">
                                  <h6 class="card-title mb-0 fs-6 fw-semibold"><i class="fa fa-flask me-2"></i> Fertilizer System Specifications</h6>
                              </div>
                              <div class="card-body p-3">
                                  <div class="row g-3">
                                      <div class="col-md-2">
                                          <label class="form-label fw-semibold text-muted mb-1" for="npk">NPK Qty:</label>
                                          <input type="text" class="form-control numberonly" id="npk">
                                      </div>
                                      <div class="col-md-2">
                                          <label class="form-label fw-semibold text-muted mb-1" for="ec">EC Qty:</label>
                                          <input type="text" class="form-control numberonly" id="ec">
                                      </div>
                                      <div class="col-md-2">
                                          <label class="form-label fw-semibold text-muted mb-1" for="ph">PH Qty:</label>
                                          <input type="text" class="form-control numberonly" id="ph">
                                      </div>
                                      <div class="col-md-2">
                                          <label class="form-label fw-semibold text-muted mb-1" for="all_in">All in Qty:</label>
                                          <input type="text" class="form-control numberonly" id="all_in">
                                      </div>
                                      <div class="col-md-2">
                                          <label class="form-label fw-semibold text-muted mb-1" for="agitator_tape">Agitator Tape:</label>
                                          <select class="form-control mb-1 custom-select browser-default" id="agitator_tape" required>
                                              <option value="NA" selected>NA</option>
                                              <option value="air">Air</option>
                                              <option value="motor">Motor</option>
                                          </select>
                                      </div>
                                      <div class="col-md-2">
                                          <label class="form-label fw-semibold text-muted mb-1" for="injectTape">Injector Tape:</label>
                                          <select class="form-control mb-1 custom-select browser-default" id="injectTape" required>
                                              <option value="NA" selected>NA</option>
                                              <option value="vento">Ventori</option>
                                              <option value="normal">Normal</option>
                                          </select>
                                      </div>
                                  </div>
                              </div>
                          </div>
                      </div>
<!-- //////////////////////// -->
                    <div class="col-md-3">
                         <label  for="noteLog">Install Date:</label>
                         <input type="date" class="form-control mb-1" id="install_date">
                     </div>
                     <div class="col-md-3">
                         <label  for="noteLog">Program Folder:</label>
                         <input type="text" class="form-control mb-1" id="prog_folder">
                     </div>
                     <div class="col-md-3">
                         <label  for="noteLog">Install Actuator:</label>
                         <input type="text" class="form-control mb-1" id="install_actuator">
                     </div>
                     <div class="col-md-3">
                         <label  for="noteLog">Panel:</label>
                         <input type="text" class="form-control mb-1" id="panel">
                     </div>
                     <div class="col-md-3">
                         <label  for="noteLog">Install Done by:</label>
                         <input type="text" class="form-control mb-1" id="install_done">
                     </div>
                     <div class="col-md-3">
                         <label  for="noteLog">Sales Person:</label>
                         <input type="text" class="form-control mb-1" id="sales_person">
                     </div>
                     <div class="col-md-3">
                         <label  for="noteLog">Location:</label>
                         <input type="text" class="form-control mb-1" id="location">
                     </div>
                     <div class="col-md-3">
                         <label  for="noteLog">Customized:</label>
                         <input type="text" class="form-control mb-1" id="customized">
                     </div>
                 </div>
                 <div class="mb-1">
                         <label for="note">Note</label>
                         <textarea class="form-control mb-1" id="note" rows="4" placeholder="Enter Note"></textarea>
                         <label for="note">Image</label>
                         <!-- File input element, accepts only image files -->
     <input type="file" id="filePhoto" class="form-control" accept="image/*">
     <input type="hidden" id="image_exist">
     
     <br>
     

     <!-- Image preview -->
     <a href="large_image.jpg" id="imgAppend" target="_blank" rel="noopener noreferrer">
        <img id="image-preview" src="" alt="Image Preview" style="display:none;" class="popup_trigger">
     </a>
     <button id="clearBtn" class="image-btn btn-sm btn-danger" style="display:none;">Clear</button>
                         </div>
                 </fieldset>
             </div>
                 <div class="modal-footer">
                 
                     <input type="hidden" id="txt_imeiid" value="0">
                     <input type="hidden" id="mode">
                     <button type="button" class="btn btn-success btn-sm  btnSave btn_save"
                             >Save</button>
                         <button type="button" class="btn btn-success btn-sm btnUpdate btn_save"
                         >Update</button>
                     <button type="button" class="btn btn-warning btn-sm"
                         data-bs-dismiss="modal">Close</button>
                 </div>
             </div>
             </div>
         </div>
