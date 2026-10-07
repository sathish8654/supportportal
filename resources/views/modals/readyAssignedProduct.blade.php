   <!-- Modal -->
   <div id="productUpdateModal" class="modal" role="dialog">
       <div class="modal-dialog">

           <!-- Modal content-->
           <div class="modal-content">
               <div class="modal-header">
                   <h4 class="modal-title">Update</h4>
                   <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
               </div>
               <div class="modal-body">
                   <div class="row">
                       <div class="col-md-6">
                           <div class="form-group">
                               <label for="productId">Tracker ID</label>
                               <input type="hide" class="form-control" id="id" hidden>
                               <input type="hide" class="form-control" id="productId" readonly>
                           </div>
                       </div>
                       <div class="col-md-6">
                           <div class="form-group">
                               <label for="batch">Batch</label>
                               <input type="hide" class="form-control" id="batch" readonly>
                           </div>
                       </div>
                   </div>

                   <div class="row">
                       <div class="col-md-6">
                           <div class="form-group">
                               <label for="category">Category</label>
                               <input type="hide" class="form-control" id="category" readonly>
                           </div>
                       </div>
                       <div class="col-md-6">
                           <div class="form-group">
                               <label for="model">Model</label>
                               <input type="hide" class="form-control" id="model" readonly>
                           </div>
                       </div>
                   </div>

                   <!-- Department Dropdown -->

                   <div class="form-group">
                       <label for="note">Note</label>
                       <textarea class="form-control" id="note" rows="3" readonly></textarea>
                   </div>

                   <div class="form-group">
                       <label for="noteLog">Reference</label>
                       <textarea class="form-control" id="noteLog" rows="3"
                           placeholder="Enter reference note"></textarea>
                   </div>
                   
                   <div class="form-group">
                    <select class=" browser-default custom-select vendor"  id="categoryMobile1" required="">
                        <option selected disabled>Select Address</option>
                        @if(!empty($mobileNumber))
                        @foreach ($mobileNumber as $item)
                            <option value="{{ $item->id }}">{{ $item->mobile1 }} - {{ $item->name }} -
                                {{ $item->companyname }}</option>
                        @endforeach
                        @endif
                    </select>
                    </div>
                    <!-- //////////////////////////////// -->
                    <table class="table order-list" >
                        <thead>
                            <tr>
                                <th>Product Spec</th>
                                <th>Sub</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="TextBoxContainer">
                         <!-- /////// -->
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="5">
                            <button id="btnAdd" type="button" class="btn btn-primary btn-sm" data-toggle="tooltip" data-original-title="Add more controls"><i class="glyphicon glyphicon-plus-sign"></i>&nbsp; Add&nbsp;</button></th>
                        </tr>
                        </tfoot>
                    </table>
                    <!-- ///////////////////////////// -->
                   <div class="row">
                        <input type="hide" class="form-control" name="assigned" value="{{ Auth::user()->id }}"  hidden>

                       <!-- <div class="col-md-6">
                           <div class="form-group">
                               <label for="note">Assign Name</label>
                               <select class="browser-default custom-select" name="assigned" id="assigned">
                                   <option selected>Select category</option>
                                   @foreach ($users as $item)
                                   <option value='{{ $item->id }}'>{{ $item->name }}</option>
                                   @endforeach
                               </select></td>
                           </div>
                       </div> -->
                       <div class="col-md-6">
                           <div class="form-group">
                               <label for="status">Status</label>
                               <select id='status' class="form-control" required>
                                   <option value=''>--Select--</option>
                                   <!-- <option value='1'>In-Progress</option> -->
                                   <option value='2'>Qc/Testing</option>
                                   <option value='4'>Ready</option>
                                   <option value='5'>Sold</option>
                                   <option value='6'>Defective</option>
                               </select>
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

                        var i = 1;
                        function GetDynamicTextBox(value) {
                            var rowCount = $("#TextBoxContainer tr").length;
                                var i = rowCount;
// console.log("Row count is: " + rowCount);
                            i++;
                        return '<td><select class="form-control browser-default custom-select partId partCategory" data-id='+ i + '  id="part" name="spec_name[]" style="width: 100% !important" required>'+
                                '<option value="" selected>Select</option>@foreach ($spec as $item)'+
                                '<option value="{{ $item->id }}">{{ $item->name }}</option>@endforeach'+
                                '</select></td>'+
                                '<td><select class="form-control browser-default custom-select partSub partSubAppend'+i+'"  name="spec_number[]" style="width: 100% !important" required>'+
                                '</select></td>'+
                                // '<td><input type="text" class="form-control partSub" onkeypress="return event.charCode >= 48 && event.charCode <= 57" name="partSub[]" placeholder="Part value" required/></td>'+
                                '<td><button type="button" class="btn btn-danger remove"><i class="fa fa-remove"></i></button></td>';

                        }
                              // show and hide the Part replace show/hide function menu
                            $(function() {
                                $(".static_class").click(function() {

                                        /////////////////////////////
                                        var arr = $('.partId').map(function () {
                                        return $(this).val();
                                        }).get();
                                    //   console.log(arr);
                                      if (arr.length > 0 || arr.length == 0) {
                                        if(confirm('Are you sure, parts not replaced?.')){
                                            $("#extra").hide("fast");
                                            $("#assignUserStatus").val("");
                                        }else{
                                            $("input:radio[value='AssignYes']").prop( "checked", true );
                                        }
                                        }


                                });
                            });

                        </script>
   <!-- Script -->
   <script type="text/javascript">
// CSRF Token
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

$(document).ready(function() {
    // $('.vendor').select2({});
    $('.partCategory').select2({});
    $('.partSub').select2({});


    // get record info
    $('#productLog').on('click', '.updateProduct', function() {
        $(this).find('form').trigger('reset'); // clear modal
        var id = $(this).data('id');
        $('#txt_empid').val(id);

        // AJAX request
        $.ajax({
            url: "{{ route('processAssignProduct') }}",
            type: 'post',
            data: {
                _token: CSRF_TOKEN,
                id: id,

            },

            dataType: 'json',
            success: function(response) {
                console.log(response);
                if (response[0].success == 1) {

                    $('#id').val(response[0].id);
                    $('#productId').val(response[0].productId);
                    $('#batch').val(response[0].batch);
                    $('#note').val(response[0].note);
                    $('#category').val(response[0].category);
                    $('#model').val(response[0].model);
                    $('#noteLog').val(response[0].noteLog);
                    $('#assigned').val(response[0].assigned);
                    $('#status').val(response[0].status);
                    $('.vendor').val(response[0].vendor_id);


                    $('#TextBoxContainer').html(response[1]);/// html part replacement append
                } else {
                    alert("Invalid ID.");
                }
            }
        });

    });

    // Save user
    $('#btn_save').click(function() {
        var id = $('#id').val();
        var assigned = $('#assigned').val();
        var note = $('#note').val().trim();
        var noteLog = $('#noteLog').val().trim();
        var vendor = $('.vendor').val();/////////////
        var productId = $('#productId').val();/////////////

        if ($('#status').val()) {
            var status = $('#status').val().trim();
        } else {
            var status = 4;
        }

        ///////////////////////////
            var specId = [];
            let allFilled = true;
            ///specs datas get by array
            $('.partId').each(function() {
                specId.push($(this).val());
                if ($(this).val() === '') {
                    allFilled = false;
                    return false; // Exit the loop if any input is empty
                }
            });
           ///specs sub data get by array
            var specSub = [];
            $('.partSub').each(function() {
                specSub.push($(this).val());
                if ($(this).val() === '') {
                    allFilled = false;
                    return false; // Exit the loop if any input is empty
                }
            });
            // validate all the open input data
            if (allFilled!=true) {
                alert('Please fill in all input fields.');
                return false;
               }
        //  console.log(allFilled);
        if (id != '' && assigned != '' && status != '') {

            // AJAX request
            $.ajax({
                url: "{{ route('processProductSubmit') }}",
                type: 'post',
                data: {
                    _token: CSRF_TOKEN,
                    id: id,
                    assigned: assigned,
                    note: note,
                    noteLog: noteLog,
                    status: status,
                    specId: specId,
                    specSub: specSub,
                    vendor: vendor,
                    productId:productId,
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success == 1) {
                        alert(response.msg);

                        // Empty and reset the values
                        $('#id', '#assigned').val('');

                        // Close modal
                        window.location.reload();
                        $('#productUpdateModal').modal('toggle');
                        // Reload DataTable
                        $('#productLog').DataTable().ajax.reload();

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

// $(document).ready(function(){

$(document).on('change', ".partCategory", function(e) {

    var cat_id = $(this).val();
    var key = $(this).data('id');

    // alert(key);
    $.ajax({
        url: "{{ route('specSub') }}",
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

$(document).ready(function () {
    $('.numberonly').keyup(function () {
        $(this).val($(this).val().replace(/[^0-9]/g, ''));
    });
});
   </script>
