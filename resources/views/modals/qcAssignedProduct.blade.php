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
                                   <option value='3'>ReWork</option>
                                   <option value='4'>Ready</option>
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

   <!-- Script -->
   <script type="text/javascript">
// CSRF Token
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

$(document).ready(function() {
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
                id: id
            },

            dataType: 'json',
            success: function(response) {
                // console.log(response[1]);
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

        if ($('#status').val()) {
            var status = $('#status').val().trim();
        } else {
            var status = 2;
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