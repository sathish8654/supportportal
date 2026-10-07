<!-- Modal -->
    <div id="ticketUpdateModal" class="modal fade" role="dialog" tabindex="-1" aria-labelledby="ticketUpdateModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
              <!-- Modal content-->
                 <form id='assignTicketForm' name='assignTicketForm' autocomplete="off">
               <div class="modal-content">

                   <div class="modal-header">
                       <h5 class="modal-title" id="ticketUpdateModalLabel">Assign Ticket</h5>
                       <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                   </div>
                   <div class="modal-body">
                     <div class="row">
                     <div class="col-md-6">
                       <div class="mb-3">
                           <label for="id" class="form-label">Ticket ID</label>
                           <input type="text" class="form-control" id="id" readonly>
                       </div>
                       <div class="mb-3">
                           <label for="vendorName" class="form-label">Customer Name</label>
                           <input type="text" class="form-control" id="vendorName" readonly>
                       </div>
                       <div class="mb-3">
                           <label for="vendorCompany" class="form-label">Company</label>
                           <input type="text" class="form-control" id="vendorCompany" readonly>
                       </div>
                      <div class="mb-3">
                           <label for="priority" class="form-label">Priority</label>
                           <select id='priority' class="form-select">
                               <option value=''>--Select--</option>
                               <option value='1'>High</option>
                               <option value='2'>Low</option>
                               <option value='3'>Medium</option>
                           </select>
                       </div>
                       </div>
                     <div class="col-md-6">

                       <div class="mb-3">
                           <label for="case" class="form-label">Case</label>
                         <select class="form-select case" data-id="1"  name="case" style='width:100%;' disabled>
                           <option selected>Select category</option>
                           @foreach ($cases as $case)
                           <option value='{{ $case->id }}'>{{ $case->name }}</option>
                           @endforeach
                           </select>
                       </div>

                       <div class="mb-3">
                           <label for="category" class="form-label">Category</label>
                         <select class="form-select categorym categorym1" data-id="1"  name="category" style='width:100%;' disabled>
                           <option selected>Select category</option>
                           @foreach ($categoris as $item)
                           <option value='{{ $item->id }}'>{{ $item->name }}</option>
                           @endforeach
                         </select></div>
                         <div class="mb-3">
                           <label for="product1" class="form-label">Model</label>
                         <select class='form-select product1' style='width:100%;' type="text"   id="product1" name="product" disabled>
                         </select>
                       </div>
                       <div class="mb-3">
                         <label for="serial" class="form-label">Serial</label>
                           <input type="text" class="form-control" id="serial" readonly>
                       </div>

                       </div>
                        </div>
                       <div class="row">
                       <div class="col-12">


                       <div class="mb-3">
                           <label for="note" class="form-label">Note</label>
                           <textarea class="form-control" id="note" rows="3" placeholder="Enter problem note"></textarea>
                       </div>

                       <div class="mb-3">
                           <label for="userid" class="form-label">Assign To</label>
                       <select class="form-select" name="userid" id="userid">
                       <option value='' selected>Select Technician</option>
                       @foreach ($users as $item)
                       <option value='{{ $item->id }}'>{{ $item->name }}</option>
                       @endforeach
                     </select>
                     </div>

                       </div>
                       </div>
                   </div>
                   <div class="modal-footer">
                       <button type="button" class="btn btn-success btn-sm" id="btn_save">Save</button>
                       <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                   </div>
              </div>
             </form>
            </div>
        </div>

   <!-- Script -->
   <script type="text/javascript">



   // CSRF Token
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function categoryid (w,c){
    var selected = '';
    // alert(w);
    $.ajax({
     url: "{{ route('subcat') }}",
     type: "POST",
     data: {
       cat_id: w
     },

     success: function(data) {
       $('.product1').empty().append($("<option></option>").attr("value", '').text('Please Select'));
       $.each(data.subcategories[0].subcategories, function(index, product) {
        if(c == product.id){
        selected = 'selected';
       } else {
        selected = '';
       }
         $('.product1').append('<option ' + selected +' value="' + product.id + '">' + product.name + '</option>');
       })
     }
   })
}


   $(document).on('change', ".categorym", function(e) {
   var cat_id = $(this).val();
   $.ajax({
     url: "{{ route('subcat') }}",
     type: "POST",
     data: {
       cat_id: cat_id
     },
     success: function(data) {
       $('.product1').empty().append($("<option></option>").attr("value", '').text('Please Select'));

       $.each(data.subcategories[0].subcategories, function(index, product) {
         $('.product1').append('<option value="' + product.id + '">' + product.name + '</option>');
       })
     }
   })

});

   $(document).ready(function(){

    // get record info
       $('#ticketsList').on('click','.updateUser, .viewTicket',function(){
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
                url: "{{ route('assignTicket') }}",
                type: 'post',
                data: {_token: CSRF_TOKEN,id: id},
                dataType: 'json',
                success: function(response){

                    if(response.success == 1){
                         $('#assignTicketForm')[0].reset();
                         $('#id').val(response.id);
                         $('#vendorid').val(response.vendorid);
                         $('#vendorName').val(response.vendorName);
                         $('#vendorCompany').val(response.vendorCompany);
                         $('.case').val(response.case);
                         $('#serial').val(response.serial);
                         $('#note').val(response.note);
                         $('.categorym1').val(response.categoryParentId);
                        //  $('#product1').val(response.productName);
                         $('.categoryid').val(response.categoryid);
                         categoryid(response.categoryParentId,response.categoryid)

                        //  ticketsList.ajax.reload();
                    }else{
                         alert("Invalid ID.");
                    }
                }
            });

       });

       // Save user
       $('#btn_save').click(function(){

            var ticketid = $('#id').val();
            var note = $('#note').val().trim();
            var userid = $('#userid').val().trim();
            var priority = $('#priority').val().trim();
            var categoryid = $('#product1').val().trim();
            var serial = $('#serial').val().trim();
            var casename = $('.case').val().trim();

            if(ticketid !='' && userid != '' && priority != '' ){

                 // AJAX request
                 $.ajax({
                     url: "{{ route('assignTicketSubmit') }}",
                     type: 'post',
                     data: {_token: CSRF_TOKEN,
                            ticketid: ticketid,
                            note: note,
                            userid: userid,
                            priority: priority,
                            categoryid: categoryid,
                            serial: serial,
                            case: casename,
                            },
                     dataType: 'json',
                     success: function(response){
                         if(response.success == "done"){
                              alert("Assigned Successfully!");


                                // Empty and reset the values
                                    $('#id').val('');
                                    $('#userid').val('');
                                    $('#priority').val('');

                                // Reload DataTable


                              // Close modal
                              $('#ticketUpdateModal').modal('hide');
                            //  mytable.ajax.reload();

                            $('#ticketsList').DataTable().ajax.reload();
                              // location="/viewTickets";

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
