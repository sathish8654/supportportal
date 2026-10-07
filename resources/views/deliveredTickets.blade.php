@extends('layouts.app')

@section('content')
    @include('modals/viewTicketsHistory')


    <div class="container-flux m-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center py-2">
                        <span class="mb-0">{{ __('Delivered Tickets') }}</span>
                        <div class="d-flex align-items-center" style="gap: 8px;">
                            <input type="date" name="from_date" id="from_date" class="form-control form-control-sm text-dark bg-white"
                                placeholder="From Date" style="width: 170px; font-weight: normal;" />
                            
                            <span class="text-white-50 mx-1" style="font-size: 0.85rem;">to</span>
                            
                            <input type="date" name="to_date" id="to_date" class="form-control form-control-sm text-dark bg-white"
                                placeholder="To Date" style="width: 170px; font-weight: normal;" />
                            
                            <button type="button" name="filter" id="filter" class="btn btn-primary btn-sm px-3 ms-2">FILTER</button>
                            <button type="button" name="refresh" id="refresh" class="btn btn-info btn-sm px-3 text-white">RESET</button>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Table -->

                        <table id='ticketsList'
                            class='datatable table table-striped table-bordered text-center center table-hover'
                            style="width:100%">
                            <thead>
                                <tr>
                                    <!-- <td>Sl.No</td> -->
                                    <td>Ticket</td>
                                    <td>Name</td>
                                    <td>Case</td>
                                    <!-- <td>Category</td> -->
                                    <td>Model</td>
                                    <td>Serial</td>
                                    <td>Problem</td>
                                    <td>Solution</td>
                                    <td>Parts</td>
                                    <td>Create By</td>
                                    <td>Closed By</td>
                                    <td>Days</td>
                                    <td>Action</td>
                                </tr>
                            </thead>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- $('.input-daterange').datepicker({
                todayBtn: 'linked',
                format: 'yyyy-mm-dd',
                autoclose: true
            }); -->

    <!-- Script -->
    <script type="text/javascript">
        // CSRF Token
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $(document).ready(function() {
            $('#clear-filter').click(function() {
                $('#ticketsList').DataTable().search(this.value).draw();
            });

           

            // Initialize
            var ticketsList = $('#ticketsList').DataTable({
                initComplete: function(settings) {
                    $('#' + settings.nTable.id + '_filter input').wrap(`
                          <div class="d-inline-flex position-relative"></div>
                        `).after(`
                          <button type="button" class="close position-absolute" aria-label="Close" style="right:5px">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        `).attr('required', 'required').attr('title', 'Search');

                    $(document).on('click', '#' + settings.nTable.id + '_filter button',
                        function() {
                            $('#' + settings.nTable.id).DataTable({
                                "retrieve": true,
                            }).search('').draw();
                        });
                },
                deferRender: true,
                responsive: true,
                stateSave: true,
                bDestroy: true,
                bprocessing: true,
                processing: true,
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
                language: { processing: "<svg class='loader' id='loader' viewBox='0 0 340 340'><circle cx='170' cy='170' r='160' stroke='#E2007C'/><circle cx='170' cy='170' r='135' stroke='#404041'/><circle cx='170' cy='170' r='110' stroke='#E2007C'/><circle cx='170' cy='170' r='85' stroke='#404041'/></svg>"},
                ajax: {
                    url: "{{ route('getDeliveredTicketsListData') }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: function(d) {
                        d.from_date = $('#from_date').val();
                        d.to_date = $('#to_date').val();
                        d.statusId = $('#statusId').val();
                    },
                },
                columns: [
                    //  { data: 'DT_RowIndex', searchable: false, orderable: false },
                    {
                        data: 'id'
                    },
                    {
                        data: 'LRvendor_name',
                        render: function(data, type, row) {
                            let name = data ? data.trim() : '';
                            let company = row['LRvendor_companyname'] ? row['LRvendor_companyname'].trim() : '';

                            // If both are the same (case-insensitive), return only one
                            if (name.toLowerCase() === company.toLowerCase()) {
                                return name;
                            }

                            return (name + ' ' + company).trim();
                        }
                    },
                    {
                        data: 'case'
                    },
                    //  { data: 'LRvendor_categoryname' },
                    // {
                        // data: 'LRtickets_category'
                    // },
                    // {
                        // data: 'LRtickets_model'
                    // },
                      {
                            data: null,
                            render: function (data, type, row) {
                                const category = row.LRtickets_category || '';
                                const model = row.LRtickets_model || '';
                                return [category, model].filter(Boolean).join(' - ');
                            }
                        },
                    {
                        data: 'LRtickets_serial'
                    },
                    {
                        data: 'LRtickets_note'
                    },
                    {
                        data: 'LRtickets_solution'
                    },
                    {
                        data: 'partsReplaced'
                    },
                    {
                        "data": null,
                        render: function(data, type, row) {
                            var details = row.user_name + " " + row.created_at;
                            return details;
                        }
                    },

                    {
                        "data": null,
                        render: function(data, type, row) {
                            var details = row.updated_by + " " + row.updated_at;
                            return details;
                        }
                    },
                    {
                        data: 'daysCount'
                    },
                    {
                        data: 'delivery_status'
                    },
                ]
            });

            // DataTables initialisation
            //  var table = $('#ticketsList').DataTable();

            $('#filter').click(function() {
                var from_date = $('#from_date').val();
                var to_date = $('#to_date').val();
                if (from_date != '' && to_date != '') {
                    //  $('#order_table').DataTable().destroy();
                    ticketsList.draw();
                    //  load_data(from_date, to_date);
                } else {
                    alert('Both Date is required');
                }
            });

            $('#refresh').click(function() {
                $('#from_date').val('');
                $('#to_date').val('');
                // $('#ticketsList').DataTable().destroy();
                $('#ticketsList').DataTable().ajax.reload();
            });

        });


        
            // reset delivery record
            var processtable = $('#ticketsList').DataTable();
            $('#ticketsList').on('click', '.resetDelivery', function() {

                var id = $(this).data('id');

                var deleteConfirm = confirm("Are you sure?");
                if (deleteConfirm == true) {
                    // AJAX request
                    $.ajax({
                        url: "{{ route('resetDeliverySubmit') }}",
                        type: 'post',
                        data: {
                            _token: CSRF_TOKEN,
                            id: id
                        },
                        success: function(response) {
                            alert(response.message);
                            processtable.row(this).remove().draw(false);
                        },

                        error: function(response) {
                            try {
                                let res = JSON.parse(response.responseText);
                                alert(res.message || "An error occurred on the server.");
                            } catch (e) {
                                alert("Internal Server Error (500). Please check Laravel logs.");
                            }
                        }
                    });
                }
            });

    </script>
@endsection
