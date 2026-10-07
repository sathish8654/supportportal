@extends('layouts.app')

@section('content')
@include('modals/viewTicketsAssign')
    <div class="container-flux m-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('Open Tickets') }}</div>
                    <div class="card-body">
                        <!-- Table -->
                        <table id='ticketsList'
                            class='datatable table table-striped table-bordered text-center center table-hover'
                            width="100%">
                            <thead>
                                <tr>
                                    <!-- <td>Sl.No</td> -->
                                    <td>Ticket</td>
                                    <td>Name</td>
                                    <!-- <td>Company</td> -->
                                    <td>Case</td>
                                    <!-- <td>Category</td> -->
                                    <td>Model</td>
                                    <td>Serial</td>
                                    <td>Note</td>
                                    <td>CreateBy</td>
                                    <!-- <td>CreateOn</td> -->
                                    <td>Status</td>
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
                language: { processing: "<svg class='loader' id='loader' viewBox='0 0 340 340'><circle cx='170' cy='170' r='160' stroke='#E2007C'/><circle cx='170' cy='170' r='135' stroke='#404041'/><circle cx='170' cy='170' r='110' stroke='#E2007C'/><circle cx='170' cy='170' r='85' stroke='#404041'/></svg>"},
                ajax: "{{ route('getTicketsListData') }}",
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
                    {
    data: null,
    render: function (data, type, row) {
        const category = row.category || '';
        const model = row.model || '';
        return [category, model].filter(Boolean).join(' - ');
    }
},
                    //  { data: 'LRvendor_categoryname' },
                    // {
                    //     data: 'category'
                    // },
                    // {
                    //     data: 'model'
                    // },
                    {
                        data: 'serial'
                    },
                    {
                        data: 'note'
                    },

                    {
    data: null,
    render: function (data, type, row) {
        const user_name = row.user_name || '';
        const created_at = row.created_at || '';
        return [user_name, created_at].filter(Boolean).join(' - ');
    }
},

                    // {
                    //     data: 'user_name'
                    // },
                    // {
                    //     data: 'created_at'
                    // },
                    {
                        data: 'status'
                    },
                    {
                        data: 'action',
                        visible: {{ (Auth::user()->hasMenuPermission('Tickets', 'viewTickets', 'edit') || Auth::user()->hasMenuPermission('Tickets', 'viewTickets', 'view') || Auth::user()->hasMenuPermission('Tickets', 'viewTickets', 'delete')) ? 'true' : 'false' }}
                    },
                ]
            });
        });
    </script>
    </body>




    <!-- Script -->
    <script type="text/javascript">
        // CSRF Token
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {

            // Delete record
            var mytable = $('#ticketsList').DataTable();

            $('#ticketsList').on('click', '.deleteUser', function() {
                var id = $(this).data('id');

                var deleteConfirm = confirm("Are you sure?");
                if (deleteConfirm == true) {
                    // AJAX request
                    $.ajax({
                        url: "{{ route('assignTicketDelete') }}",
                        type: 'post',
                        data: {
                            _token: CSRF_TOKEN,
                            id: id
                        },
                        success: function(response) {
                            alert(response.message);
                            mytable.row(this).remove().draw(false);
                        },

                        error: function(response) {
                            alert(JSON.parse(response.responseText).message);
                        }
                    });
                }
            });
        });
    </script>
@endsection
