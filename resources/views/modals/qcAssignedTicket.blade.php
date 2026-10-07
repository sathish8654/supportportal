   <!-- Modal -->
   <div id="ticketUpdateModal" class="modal " role="dialog">
       <div class="modal-dialog">

           <!-- Modal content-->
           <div class="modal-content">
               <div class="modal-header">
                   <h4 class="modal-title">Update</h4>
                   <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
               </div>
               <input type="hide" class="form-control" id="id" hidden>
               <div class="modal-body">
                   <div class="row">
                       <div class="col-md-4">
                           <div class="form-group">
                               <label for="ticketid">Ticket ID</label>
                               <input type="hide" class="form-control" id="ticketid" readonly>
                           </div>
                       </div>
                       <div class="col-md-4">
                           <div class="form-group">
                               <label for="LRtickets_serial">Serial</label>
                               <input type="hide" class="form-control" id="LRtickets_serial"
                                   placeholder="Device serial number" readonly>
                           </div>
                       </div>
                        <div class="col-md-4">
                           <!-- Department Dropdown -->
                           <div class="form-group">
                               <label for="priority">Priority</label>
                               <select id='priority' class="form-control" readonly>
                                   <option value=''>--Select--</option>
                                   <option value='1'>High</option>
                                   <option value='2'>Low</option>
                                   <option value='3'>Medium</option>
                               </select>
                           </div>
                       </div>
                   </div>
                   <div class="form-group">
                       <label for="note">Problem Note</label>
                       <textarea class="form-control" id="note" rows="3" placeholder="Enter problem note"
                           readonly></textarea>
                   </div>

                   <div class="form-group">
                       <label for="solution">Solution Note</label>
                       <textarea class="form-control" id="solution" rows="3"
                           placeholder="Enter solution note"></textarea>
                   </div>

                   <div class="form-group">
                       <label for="name">Part replaced :</label>&nbsp;&nbsp;
                       <input type="radio" checked="checked" name="actions" value="AssignNo" class="static_class" /> No
                       </label>&nbsp;&nbsp;&nbsp;&nbsp;
                       <input type="radio" name="actions" value="AssignYes" class="static_class" />
                       Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;

                       <div id="extra" style="display:none;">
                           <div class="form-group" style="width: 100%">

                               <table class="table order-list">
                                   <thead>
                                       <tr>
                                           <th>Part</th>
                                           <th>Sub</th>
                                           <th>Action</th>
                                       </tr>
                                   </thead>
                                   <tbody id="TextBoxContainer">
                                       <!-- ////////////////////////////////////////// -->
                                   </tbody>
                                   <tfoot>
                                       <tr>
                                           <th colspan="5">
                                               <button id="btnAdd" type="button" class="btn btn-primary btn-sm"
                                                   data-toggle="tooltip" data-original-title="Add more controls"><i
                                                       class="glyphicon glyphicon-plus-sign"></i>&nbsp;
                                                   Add&nbsp;</button>
                                           </th>
                                       </tr>
                                   </tfoot>
                               </table>

                           </div>
                       </div>
                   </div>

                   <script>
                   $(function() {
                       $("#btnAdd").bind("click", function() {
                           var div = $("<tr />");
                           
                           div.html(GetDynamicTextBox(""));
                           $("#TextBoxContainer").append(div);
                       });
                       $("body").on("click", ".remove", function() {
                           $(this).closest("tr").remove();
                       });
                   });

                   var i = 1;

                   function GetDynamicTextBox(value) {
                       i++;
                       return '<td><select class="form-control browser-default custom-select partId partCategory" data-id=' +
                           i + ' id="part" name="part[]" style="width: 100% !important" required>' +
                           '<option value="" selected>Select</option>@foreach ($parts as $item)' +
                           '<option value="{{ $item->id }}">{{ $item->name }}</option>@endforeach' +
                           '</select></td>' +
                           '<td><select class="form-control browser-default custom-select partSub partSubAppend' + i +
                           '"  name="partSub[]" style="width: 100% !important" required>' +
                           '</select></td>' +
                           // '<td><input type="text" class="form-control partSub" onkeypress="return event.charCode >= 48 && event.charCode <= 57" name="partSub[]" placeholder="Part value" required/></td>'+
                           '<td><button type="button" class="btn btn-danger remove"><i class="fa fa-remove"></i></button></td>';

                   }
                   // show and hide the Part replace show/hide function menu
                   $(function() {
                       $(".static_class").click(function() {
                           if ($(this).val() === "AssignNo") {
                               /////////////////////////////
                               var arr = $('.partId').map(function() {
                                   return $(this).val();
                               }).get();
                               //   console.log(arr);
                               if (arr.length > 0 || arr.length == 0) {
                                   if (confirm('Are you sure, parts not replaced?.')) {
                                       $("#extra").hide("fast");
                                       $("#assignUserStatus").val("");
                                   } else {
                                       $("input:radio[value='AssignYes']").prop("checked", true);
                                   }
                               }

                           } else {
                               $("#extra").show("fast");
                               $("#assignUserStatus").val("AssignUserEnable");
                           }
                       });
                   });
                   </script>

                   <div class="row">
                        <input type="hide" class="form-control userid" name="userid" value="{{ Auth::user()->id }}"  hidden>

                       <!-- <div class="col-md-6">
                           <div class="form-group">
                               <label for="note">Assign Name</label>
                               <select class="browser-default custom-select" name="userid" id="userid">
                                   <option selected>Select category</option>
                                   @foreach ($users as $item)
                                   <option value='{{ $item->id }}'>{{ $item->name }}</option>
                                   @endforeach
                               </select></td>
                           </div>
                       </div> -->
                      
                       <div class="col-md-6">
                           <!-- Department Dropdown -->
                           <div class="form-group">
                               <label for="status">Status</label>
                               <select id='status' class="form-control">
                                   <option value=''>--Select--</option>
                                   <!-- <option value='1'>Open</option> -->
                                   <option value='2'>Closed</option>
                                   <!-- <option value='3'>In-Progress</option> -->
                                   <option value='4'>QC</option>
                                   <option value='5'>ReWork</option>
                               </select>
                           </div>
                       </div>
                       <div class="col-md-6">
                           <div class="form-group">
                               <label for="solution">Service Cost</label>
                               <input class="form-control" id="cost" placeholder="Enter service cost">
                           </div>
                       </div>
                   </div>
               </div>
               <div class="modal-footer">
                   <button type="button" class="btn btn-success btn-sm" id="btn_save">Save</button>
                   <button type="button" class="btn btn-warning btn-sm" data-bs-dismiss="modal">Close</button>
               </div>
           </div>

       </div>
   </div>

   <!-- Script -->
   <script type="text/javascript">
// CSRF Token
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

$(document).ready(function() {
    // get record info
    $('#ticketsList').on('click', '.updateUser, .viewTicket', function() {
        var isViewOnly = $(this).hasClass('viewTicket') || $(this).data('view-only') == 1;

        if (isViewOnly) {
            $('#ticketUpdateModal .modal-title').text('View Details');
            $('#ticketUpdateModal input, #ticketUpdateModal select, #ticketUpdateModal textarea').prop('disabled', true);
            $('#btn_save').hide();
        } else {
            $('#ticketUpdateModal .modal-title').text('Update');
            $('#ticketUpdateModal input, #ticketUpdateModal select, #ticketUpdateModal textarea').prop('disabled', false);
            $('#btn_save').show();
        }

        var id = $(this).data('id');

        $('#txt_empid').val(id);

        // AJAX request
        $.ajax({
            url: "{{ route('qcAssignTicket') }}",
            type: 'post',
            data: {
                _token: CSRF_TOKEN,
                id: id
            },

            dataType: 'json',
            success: function(response) {
                // console.log(response);
                if (response[0].success == 1) {

                    $('#id').val(response[0].id);
                    $('#ticketid').val(response[0].ticketid);
                    $('#LRtickets_serial').val(response[0].serial);
                    $('#note').val(response[0].note);
                    $('#solution').val(response[0].solution);
                    $('#cost').val(response[0].cost);
                    // $('#userid').val(response[0].userid);
                    $('#priority').val(response[0].priority);
                    $('#status').val(response[0].status);
                    //////////////////////////////////////
                    if (response[1] != '') {
                        $("input:radio[value='AssignYes']").prop("checked", true);
                        $("#extra").show("fast");
                        $("#assignUserStatus").val("AssignUserEnable");
                        $('#TextBoxContainer').html(response[
                        1]); /// html part replacement append
                    }
                    // ticketsList.ajax.reload();
                } else {
                    alert("Invalid ID.");
                }
            }
        });

    });

    // Save user 
    $('#btn_save').click(function() {

        var id = $('#id').val();
        var ticketid = $('#ticketid').val();
        var userid = $('.userid').val().trim();
        var priority = $('#priority').val().trim();
        var note = $('#note').val().trim();
        var solution = $('#solution').val().trim();
        var cost = $('#cost').val().trim();
        var status = $('#status').val().trim();
        ///////////////////////
        var partId = [];
        let allFilled = true;
        ///part part name get by array
        $('.partId').each(function() {

            partId.push($(this).val());
            if ($(this).val() === '') {
                allFilled = false;
                return false; // Exit the loop if any input is empty
            }
        });
        ///part value get by array
        var partSub = [];
        $('.partSub').each(function() {

            partSub.push($(this).val());
            if ($(this).val() === '') {
                allFilled = false;
                return false; // Exit the loop if any input is empty
            }
        });

        var selectedValue = $('input[type="radio"]:checked').val(); // radio button value get
        // alert(selectedValue);
        if (selectedValue == "AssignYes") { //yes
            var mode = 1;
            if (allFilled != true) {
                alert('Please fill in all input fields.');
                return false;
            }
        } else { //No
            var mode = 2;
        }
        ///////////////////////////////////////////
        if (id != '' && userid != '' && priority != '' && status != '') {

            // AJAX request
            $.ajax({
                url: "{{ route('qcTicketSubmit') }}",

                type: 'post',
                data: {
                    _token: CSRF_TOKEN,
                    id: id,
                    ticketid: ticketid,
                    partSub: partSub,
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
                success: function(response) {
                    if (response.success == 1) {
                        alert(response.msg);

                        // Empty and reset the values
                        $('#id', '.userid', '#priority').val('');

                        // Close modal
                        window.location.reload();
                        $('#ticketUpdateModal').modal('toggle');

                        // Reload DataTable
                        $('#ticketsList').DataTable().ajax.reload();

                    } else {
                        alert(response.msg);
                    }
                }
            });

        } else {
            alert('Please fill all fields.');
        }
    });
});

//////////////////////////////
$(document).on('change', ".partCategory", function(e) {

    var cat_id = $(this).val();
    var key = $(this).data('id');

    // alert(key);
    $.ajax({
        url: "{{ route('partsSub') }}",
        type: "POST",
        data: {
            _token: CSRF_TOKEN,
            cat_id: cat_id
        },
        success: function(data) {
            // console.log(data);
            $('.partSubAppend' + key).empty().append($("<option></option>").attr("value", '').text(
                'Please Select'));

            if (data.subcategories && Array.isArray(data.subcategories)) {
                $.each(data.subcategories, function(index, product) {
                    $('.partSubAppend' + key).append('<option value="' + product.id + '">' +
                        product.name + '</option>');
                });
            }
        }
    })

});
   </script>