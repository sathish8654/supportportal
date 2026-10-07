   <!-- Modal -->
   <div id="ticketUpdateModal" class="modal" role="dialog">
           <div class="modal-dialog">

               <!-- Modal content-->
              <div class="modal-content">
                  <div class="modal-header">
                      <h4 class="modal-title">Update</h4>
                      <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                  </div>
                  <input type="hide" class="form-control" id="id" placeholder="Enter Employee name" hidden>
                  <div class="modal-body">
                      <div class="form-group">
                          <label for="ticketid" >Ticket ID</label>
                          <input type="hide" class="form-control" id="ticketid" placeholder="Enter Employee name" readonly>
                      </div>
                      <div class="form-group">
                          <label for="LRtickets_serial" >Serial</label>
                          <input type="hide" class="form-control" id="LRtickets_serial" placeholder="Device serial number" readonly>
                      </div>
                      <div class="form-group">
                          <label for="note" >Assign Name</label>
                      <select class="browser-default custom-select" name="userid" id="userid">
                      <option selected>Select category</option>
                      @foreach ($users as $item)
                      <option value='{{ $item->id }}'>{{ $item->name }}</option>
                      @endforeach
                    </select></td>
                    </div>
                      <!-- Department Dropdown -->
                     <div class="form-group">
                          <label for="priority" >Priority</label>
                          <select id='priority' class="form-control">
                              <option value=''>--Select--</option>
                              <option value='1'>High</option>
                              <option value='2'>Low</option>
                              <option value='3'>Medium</option>
                          </select>
                      </div>
                    <!-- Department Dropdown -->
                     <div class="form-group">
                          <label for="status" >Status</label>
                          <select id='status' class="form-control">
                              <option value=''>--Select--</option>
                              <!-- <option value='1'>Open</option> -->
                              <!-- <option value='2'>Closed</option> -->
                              <option value='3'>In-Progress</option>
                              <option value='4'>QC</option>
                          </select>
                      </div>
                      <div class="form-group">
                          <label for="note" >Problem Note</label>
                          <textarea class="form-control" id="note" rows="3" placeholder="Enter problem note" readonly></textarea>
                      </div>


                      <div class="form-group">
                        <label for="name">Part replaced :</label>&nbsp;&nbsp;
                        <input type="radio" checked="checked"  name="actions" value="AssignNo"
                            class="static_class" /> No </label>&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="radio" name="actions"  value="AssignYes"
                            class="static_class" />
                        Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;
                        
                        <div id="extra" style="display:none;">
                            <div class="form-group" style="width: 100%">

                      <table class="table order-list" >
                        <thead>
                            <tr>
                                <th>Part</th>
                                <th>Number</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="TextBoxContainer">
                            <!-- <tr id="row0">
                                <td class="col-sm-4">
                                    <select class="form-control browser-default custom-select partId" name="part[]" id="part" style="width: 100% !important" >
                                        <option value="">Select</option>
                                        @foreach ($parts as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="col-sm-4">
                                    <input type="text" class="form-control partValue" name="partValue[]" onkeypress="return event.charCode >= 48 && event.charCode <= 57" placeholder="Part Value" />
                                </td>
                                <td class="col-sm-2">
                                    <span class="input-group-btn">
                                        <button id="btn0" type="button" class="btn btn-primary" onclick="addRow(this)">
                                        <i id="icon0" class="fa fa-plus"></i>
                                        </button>

                                        <button id="btn0" type="button" class="btn btn-primary remRows"  onclick="remRow(this)">
                                        <i id="icon0" class="fa fa-minus"></i>
                                        </button>
                                    </span>
                                </td>
                            </tr> -->
                            <!-- /////////////////////////////// -->

                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="5">
                            <button id="btnAdd" type="button" class="btn btn-primary btn-sm" data-toggle="tooltip" data-original-title="Add more controls"><i class="glyphicon glyphicon-plus-sign"></i>&nbsp; Add&nbsp;</button></th>
                        </tr>
                        </tfoot>
                    </table>

                    </div>
                                                </div>
                                            </div>

                        <script>
                        $(function () {
                            $("#btnAdd").bind("click", function () {
                                var div = $("<tr />");
                                div.html(GetDynamicTextBox(""));
                                $("#TextBoxContainer").append(div);
                            });
                            $("body").on("click", ".remove", function () {
                                $(this).closest("tr").remove();
                            });
                        });
                        function GetDynamicTextBox(value) {

                        return '<td><select class="form-control browser-default custom-select partId" id="part" name="part[]" style="width: 100% !important" required>'+
                                '<option value="" selected>Select</option>@foreach ($parts as $item)'+
                                '<option value="{{ $item->id }}">{{ $item->name }}</option>@endforeach'+
                                '</select></td>'+
                                '<td><input type="text" class="form-control partValue" onkeypress="return event.charCode >= 48 && event.charCode <= 57" name="partValue[]" placeholder="Part value" required/></td>'+
                                '<td><button type="button" class="btn btn-danger remove"><i class="fa fa-remove"></i></button></td>';
                                          
                        }
                              // show and hide the Part replace show/hide function menu
                            $(function() {
                                $(".static_class").click(function() {
                                    if ($(this).val() === "AssignNo") {
                                        /////////////////////////////
                                        var arr = $('.partId').map(function () {
                                        return $(this).val();
                                        }).get();
                                      console.log(arr);
                                      if (arr.length > 1 || arr.length == 0) {
                                        if(confirm('Are you sure close the request?.')){
                                            $("#extra").hide("fast");
                                            $("#assignUserStatus").val("");
                                        }else{
                                            $("input:radio[value='AssignYes']").prop( "checked", true );
                                        }
                                        }
                                        // $("#extra").hide("fast");
                                        // $("#assignUserStatus").val("");
                                    } else {
                                        $("#extra").show("fast");
                                        $("#assignUserStatus").val("AssignUserEnable");
                                    }
                                });
                            });

                            function addRow(input) {
                                    var table = document.getElementById("myTable");
                                    var i = parseInt(input.id.substring(3, input.id.length));
                                    document.getElementById('icon' + i).className = "fa fa-minus";

                                    // Create new row
                                    var row = table.insertRow(table.rows.length);
                                    row.id = "row" + (i + 1);

                                    // Insert cells
                                    var cell1 = row.insertCell(0);
                                    var cell2 = row.insertCell(1);
                                    var cell3 = row.insertCell(2);

                                    // Populate cells
                                    cell1.className = "col-sm-4";
                                    // cell1.innerHTML = '<input type="text" class="form-control" />';
                                    cell1.innerHTML = '<select class="form-control browser-default custom-select partId" id="part'+i+'" name="part[]" style="width: 100% !important" required>'+
                                                        '<option value="" selected>Select</option>@foreach ($parts as $item)'+
                                                        '<option value="{{ $item->id }}">{{ $item->name }}</option>@endforeach'+
                                                        '</select>';

                                    cell2.className = "col-sm-4";
                                    cell2.innerHTML = '<input type="text" class="form-control partValue" onkeypress="return event.charCode >= 48 && event.charCode <= 57" name="partValue[]" placeholder="Part value" required/>';

                                    cell3.className = "col-sm-2";
                                    cell3.innerHTML =
                                        '<span class="input-group-btn">' +
                                            '<button id="btn' + (i + 1) + '" type="button" class="btn btn-primary" onclick="addRow(this)">' +
                                                '<span id="icon' + (i + 1) + '" class="fa fa-plus"></span>' +
                                            '</button>' +
                                        '</span>';

                                    // Change previous button to remove
                                    $('#btn' + i).attr('onclick', 'remRow(this)');
                                }

                            function remRow(input) {
                                var table = document.getElementById("myTable");
                                var i = parseInt(input.id.substring(3, input.id.length));

                                if (i==0) {
                                    $(".remRows").removeAttr("hidden");
                                } else {
                                    $('.remRows').hide();
                                }
                                console.log(i);
                                var ind = table.rows["row" +i].rowIndex;
                                table.deleteRow(ind);
                            }
                        </script>




                      <div class="form-group">
                          <label for="solution" >Solution Note</label>
                          <textarea class="form-control" id="solution" rows="3" placeholder="Enter solution note"></textarea>
                      </div>

                      <div class="form-group">
                          <label for="cost" >Service Cost</label>
                          <input type="hide" class="form-control" id="cost" placeholder="Enter service cost">
                      </div>
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-success btn-sm" id="btn_save">Save</button>
                      <button type="button" class="btn btn-default btn-sm" data-bs-dismiss="modal">Close</button>
                  </div>
             </div>

           </div>
       </div>

   <!-- Script -->
   <script type="text/javascript">
   // CSRF Token
   var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

   $(document).ready(function(){

    // get record info
       $('#ticketsList').on('click','.updateUser',function(){

            var id = $(this).data('id');

            $('#txt_empid').val(id);

            // AJAX request
            $.ajax({
                url: "{{ route('processAssignTicket') }}",
                type: 'post',
                data: {_token: CSRF_TOKEN,id: id},

                dataType: 'json',
                success: function(response){
                    // console.log(response[1]);
                    if(response[0].success == 1){

                         $('#id').val(response[0].id);
                         $('#ticketid').val(response[0].ticketid);
                         $('#LRtickets_serial').val(response[0].serial);
                         $('#note').val(response[0].note);
                         $('#solution').val(response[0].solution);
                         $('#cost').val(response[0].cost);
                         $('#userid').val(response[0].userid);
                         $('#priority').val(response[0].priority);
                         $('#status').val(response[0].status);
                         /////////////////////////
                         if (response[1]!='') {
                            $("input:radio[value='AssignYes']").prop( "checked", true );
                            $("#extra").show("fast");
                            $("#assignUserStatus").val("AssignUserEnable");
                            $('#TextBoxContainer').html(response[1]);/// html part replacement append
                         }

                        //  ticketsList.ajax.reload();
                    }else{
                         alert("Invalid ID.");
                    }
                }
            });

       });

       // Save user
       $('#btn_save').click(function(){

            var id = $('#id').val();
            var ticketid = $('#ticketid').val();
            var userid = $('#userid').val().trim();
            var priority = $('#priority').val().trim();
            var note = $('#note').val().trim();
            var solution = $('#solution').val().trim();
            var cost = $('#cost').val().trim();
            var status = $('#status').val().trim();

            var partId = [];
            let allFilled = true;
            $('.partId').each(function() {

                partId.push($(this).val());
                if ($(this).val() === '') {
                    allFilled = false;
                    return false; // Exit the loop if any input is empty
                }
            });


            var partValue = [];
            $('.partValue').each(function() {

                partValue.push($(this).val());
                if ($(this).val() === '') {
                    allFilled = false;
                    return false; // Exit the loop if any input is empty
                }
            });

            var  selectedValue = $('input[type="radio"]:checked').val();
            // alert(selectedValue);
            if (selectedValue=="AssignYes") {
                var mode = 1;
                if (allFilled!=true) {
                alert('Please fill in all input fields.');
                return false;
               }
            }else{
                var mode = 2;
            }

//  console.log(allFilled);
            if(id !='' && userid != ''  && priority != ''  && status != '' ){

                 // AJAX request
                 $.ajax({
                     url: "{{ route('processTicketSubmit') }}",
                     type: 'post',
                     data: {_token: CSRF_TOKEN,
                            id: id,
                            ticketid: ticketid,
                            partValue: partValue,
                            partId: partId,
                            userid: userid,
                            priority: priority,
                            note: note,
                            solution: solution,
                            cost: cost,
                            status: status,
                            mode: mode,
                            },
                     dataType: 'json',
                     success: function(response){
                         if(response.success == 1){
                              alert(response.msg);

                              // Empty and reset the values
                              $('#id','#userid','#priority').val('');

                              // Close modal
                              $('#ticketUpdateModal').modal('toggle');

                                // Reload DataTable
                              $('#ticketsList').DataTable().ajax.reload();

                         }else{
                              alert(response.msg);
                         }
                     }
                 });

            }else{
                 alert('Please fill all fields.');
            }
       });
       });
   </script>
