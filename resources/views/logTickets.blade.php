@extends('layouts.app')

@section('content')
    @include('modals/viewTicketsHistory')

    <div class="container-flux m-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('Overall Tickets log') }}</div>

                    <div class="card-body">
                    <form  action="{{ url('logTickets') }}" method="POST"> 
                    @csrf
                        <div class="row input-daterange form-group ">
                            <div class="col-md-2 ">
                                <select name="statusId" id="statusId" class="form-control">
                                <option value="">All</option>
                                @foreach($categories as $category)
                                <option value="{{$category->id}}">{{$category->name}} </option>
                                @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 ">
                                <input type="text" name="from_date" id="from_date" class="form-control"
                                    placeholder="From Date" readonly />
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="to_date" id="to_date" class="form-control"
                                    placeholder="To Date" readonly />
                            </div>
                            <div class="col-md-4">
                                <input type="hidden" id="statusIdss">
                               
                                <button type="button" name="filter" id="filter" class="btn btn-primary">FILTER</button>
                                <a href="{{route('logTickets')}}" class="btn btn-info">RESET</a>
                            </div>
                        </div>
                        </form>
                        <br>

                        <!-- Table -->
                        <table id='ticketsList'
                            class=' table table-striped table-bordered text-center center table-hover'
                            width="100%">
                            <thead>
                                <tr>
                                    <!-- <td>Sl.No</td> -->
                                    <td>Ticket</td>
                                    <td>Name</td>
                                    <td>Company</td>
                                    <td>Case</td>
                                    <td>Category</td>
                                    <td>Product</td>
                                    <td>Serial</td>
                                    <td>Model</td>
                                    <td>Cost</td>
                                    <td>Solution</td>
                                    <td>CreateBy</td>
                                    <td>UpdateBy</td>
                                    <td>Days</td>
                                    <td>Status</td>
                                    <td>Delivery</td>
                                </tr>
                            </thead>
                        </table>
                        <br>
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
            // $('#filter').click(function() {
            //    let statusId =  $('#statusId').val();
            //    $('#statusId').val(statusId);
            //    // alert(statusId);
            // });

            $('.input-daterange').datepicker({
                todayBtn: 'linked',
                format: 'yyyy-mm-dd',
                autoclose: true
            });
            //////////////////////////////////////
            let ajaxUrl = '';

            if ($('#statusIdss').val() != '') {// when click filter button the url trigger to controller
                ajaxUrl = "{{ route('logTickets') }}";
            }
            /////////////////////////////////////////
            // Initialize
            var ticketsList = $('#ticketsList').DataTable({
                deferRender: true,
                responsive: true,
                stateSave: true,
                bDestroy: true,
                bprocessing: true,
                processing: true,
                language: { processing: "<svg class='loader' id='loader' viewBox='0 0 340 340'><circle cx='170' cy='170' r='160' stroke='#E2007C'/><circle cx='170' cy='170' r='135' stroke='#404041'/><circle cx='170' cy='170' r='110' stroke='#E2007C'/><circle cx='170' cy='170' r='85' stroke='#404041'/></svg>"},
                paging: true,
                orderClasses: false,
                serverSide: true,
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                aaSorting: [
                    [0, 'desc']
                ],
                dom: 'Bfrtip',
                buttons: ['excel', 'pageLength'],
                ajax: {

                    url: ajaxUrl,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function(d) {
                        d.from_date = $('#from_date').val();
                        d.to_date = $('#to_date').val();
                        d.statusId = $('#statusIdss').val();
                    },
                },
                columns: [
                    //  { data: 'DT_RowIndex', searchable: false, orderable: false },
                    {
                        data: 'ticketId'
                    },
                    {
                        data: 'LRvendor_name'
                    },
                    {
                        data: 'LRvendor_companyname'
                    },
                    {
                        data: 'case'
                    },
                    {
                        data: 'category'
                    },
                    {
                        data: 'model'
                    },
                    {
                        data: 'LRtickets_serial'
                    },
                    {
                        data: 'LRtickets_note'
                    },
                    {
                        data: 'cost'
                    },
                    {
                        data: 'solution'
                    },
                    {
                        data: 'created_by'
                    },
                    {
                        data: 'updated_by'
                    },
                    {
                        data: 'daysCount'
                    },
                    {
                        data: 'status'
                    },
                    {
                        data: 'delivery_status'
                    },
                ]
            });

            // DataTables initialisation
             var table = $('#ticketsList').DataTable();

            $('#filter').click(function() {
                // alert();
              window.history.pushState('obj', 'newtitle', '/logTickets');// url change method
  
              var statusId = $('#statusId').val();// append to input filed
              $('#statusIdss').val(statusId);
              ticketsList.draw();
                // var from_date = $('#from_date').val();
                // var to_date = $('#to_date').val();

                // if (from_date != '' && to_date != '') {
                //     //  $('#order_table').DataTable().destroy();
                //     ticketsList.draw();
                //     //  load_data(from_date, to_date);
                // } else {
                //     alert('Both Date is required');
                // }
            });

            // $('#refresh').click(function() {
            //     $('#from_date').val('');
            //     $('#to_date').val('');
            //     // $('#ticketsList').DataTable().destroy();
            //     $('#ticketsList').DataTable().ajax.reload();
            // });

        });
    </script>
@endsection
