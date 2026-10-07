@extends('layouts.app')

@section('content')
@include('modals/editUserSimPayment')

<div class="container-flux m-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('IoT SIM Payment tracker') }}</span>
                    <button type="button" class="btn btn-success btn-sm addNew" data-bs-toggle="modal"
                        data-bs-target="#updateModal"><i class="fa fa-plus"></i> Add Payment</button>
                </div>

                <div class="card-body">
        
                    <div class="container-fluid px-0 mb-3">
                        <div class="row align-items-center">
                            <div class="col-md-9">
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <input type="date" id="filter_from_date" class="form-control" placeholder="From Date">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="date" id="filter_to_date" class="form-control" placeholder="To Date" >
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" id="filter" class="btn btn-primary btn-sm me-1">Filter</button>
                                        <button type="button" id="reset" class="btn btn-secondary btn-sm">Reset</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
          <!-- Table -->
          <table id='userSimPaymentTable'
            class='datatable table table-striped table-bordered text-center center table-hover' style="width:100%">
            <thead>
              <tr>
                <td>Sl.No</td>
                <td>Paid date</td>
                <td>Sim PhoneNumber</td>
                <td>Operator</td>
                <td>Sim Owner</td>
                <td>Contact</td>
                <td>Name</td>
                <td>From Date</td>
                <td>To Date</td>
                <td>Cost</td>
                <td>Tx ID</td>
                <td>Note</td>
                <td>Mode</td>
                <td>Status</td>
                <td>handled By</td>
                <td>handled On</td>
                <td>Action</td>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
</div>
</div>
</div>


        <!-- Script -->
        <script type="text/javascript">
          // CSRF Token
          var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
          $(document).ready(function () {
            // Initialize
            var userSimPaymentTable = $('#userSimPaymentTable').DataTable({

              initComplete: function(settings) {
                //settings.nTable.id --> Get table ID
                $('#'+settings.nTable.id+'_filter input').wrap(`
                  <div class="d-inline-flex position-relative"></div>
                `).after(`
                  <button type="button" class="close position-absolute" aria-label="Close" style="right:5px">
                    <span aria-hidden="true">&times;</span>
                  </button>
                `).attr('required','required').attr('title','Search');

                // Click Event on Clear button
                $(document).on('click', '#'+settings.nTable.id+'_filter button', function(){
                  $('#'+settings.nTable.id).DataTable({
                    "retrieve": true,
                    }).search('').draw(); // reDraw table
                });
              },


              deferRender: true,
              responsive: true,
              stateSave: true,
              bDestroy: true,
              processing: true,
              serverSide: true,
              lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
              dom: 'Bfrtip',
              buttons: ['excel', 'pageLength'],
              language: { processing: "<svg class='loader' id='loader' viewBox='0 0 340 340'><circle cx='170' cy='170' r='160' stroke='#E2007C'/><circle cx='170' cy='170' r='135' stroke='#404041'/><circle cx='170' cy='170' r='110' stroke='#E2007C'/><circle cx='170' cy='170' r='85' stroke='#404041'/></svg>"},
              ajax: {
                          url: "{{ route('getusersimPaymentData') }}",
                          data: function(d) {
                             console.log("From Date: ", $('#filter_from_date').val());
            console.log("To Date: ", $('#filter_to_date').val());

                                    d.from_date = $('#filter_from_date').val();
                                    d.to_date = $('#filter_to_date').val();
                                }
              },
              
              columns: [
                { data: 'DT_RowIndex', searchable: false, orderable: false },
                { data: 'paid_date' },
                { data: 'sim_phone_number' },
                { data: 'simOperator' },
                { data: 'simName' },
                { data: 'usercontact_number' },
                { data: 'name' },
                { data: 'from_date' },
                { data: 'to_date' },
                { data: 'cost' },
                { data: 'transaction_id' },
                { data: 'note' },
                { data: 'mode' },
                { data: 'status' },
                { data: 'payment_received' },
                { data: 'payment_received_on' },
                {
                    data: 'action',
                    visible: {{ (Auth::user()->hasMenuPermission('Users', 'usersimPayment', 'edit') || Auth::user()->hasMenuPermission('Users', 'usersimPayment', 'view') || Auth::user()->hasMenuPermission('Users', 'usersimPayment', 'delete')) ? 'true' : 'false' }}
                },
              ]
            });


          // Filter button
          $('#filter').click(function() {
                      console.log("From Date (Before reload): ", $('#filter_from_date').val());
            console.log("To Date (Before reload): ", $('#filter_to_date').val());
              userSimPaymentTable.draw();
          });

         // Reset button
          $('#reset').click(function() {
              $('#filter_from_date').val('');
              $('#filter_to_date').val('');
              userSimPaymentTable.draw();
          });

          });

        </script>

@endsection
