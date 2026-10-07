@extends('layouts.app')

@section('content')

@include('modals/viewTicketsHistory')

<div class="container-flux m-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Closed Tickets') }}</div>

                <div class="card-body">
        <!-- Table -->
        <form id="add_item">
            <table id='ticketsList' class='datatable table table-striped table-bordered text-center center table-hover' style="width:100%">
                <thead>
                    <tr>
                        <!-- <td>Sl.No</td> -->
                        <th>Select <input type="checkbox" name="select_all" value="1" id="checkall">
                        </th>
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
                    </tr>
                </thead>
            </table>

            <br>

            <div class="card openDiv" style="padding: 1em;">
                <div class="row col-md-12 ">
                    <div class="col-md-2">
                        <label for="delivery_mode">Mode of deliver</label>
                        <select name="delivery_mode" id="delivery_mode" class="form-control" required>
                            <option value="">Delivery Mode</option>
                            <option value="1">Other</option>
                            <option value="2">ByHand</option>
                            <option value="3">ByBus</option>
                            <option value="4">Postal Courier</option>
                            <option value="5">ByTransport</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="fname">Receiver Name</label>
                        <input class="form-control" type="text" id="delivery_handover" name="delivery_handover"
                            placeholder="Receiver Name">
                    </div>

                    <div class="col-md-2">
                        <label for="fname">Receiver Contact</label>
                        <input class="form-control" type="text" id="delivery_contact" name="delivery_contact"
                            placeholder="Receiver Contact">
                    </div>

                    <div class="col-md-4">
                        <label for="delivery_note">Note</label>
                        <textarea class="form-control" id="delivery_note" name="delivery_note" rows="4"></textarea>
                    </div>
                    <div class="col-md-2 d-flex flex-column ml-auto mt-2 ">
                        <button type="button" class=" btn btn-lg btn-success" style="margin-top: auto;" id="submit"
                            name="submit">Submit</button>
                        <button type="button" class="mt-2 btn btn-lg btn-warning" style="margin-top: auto;"
                            onclick="javascript:window.location.reload()">Reset</button>
                    </div>
                </div>
            </div>
            <br>

        </form>

    </div>
</div>


        <!-- Script -->
        <script type="text/javascript">
            // CSRF Token
            var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
            $(document).ready(function() {


                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                //check box check and uncheck
                $('#checkall').click(function() {
                    var checked = $(this).prop('checked');
                    $('tbody').find('input:checkbox').prop('checked', checked);
                });

                // Delivery submit function
                $(document).on('click', '#submit', function() {
                    // Ensure at least one checkbox is selected
                    if ($('input[name="ticketid[]"]:checked').length === 0) {
                        alert("Please select at least one ticket first.");
                        return false;
                    }

                    $.ajax({
                        url: "{{ route('manageDelivery') }}",
                        method: "POST",
                        data: $('#add_item').serialize(),
                        type: 'json',
                        success: function(response) {
                            if (response.success == 'done') {
                                alert("Delivery updated successfully!");
                                window.location.reload();
                            }
                            if (response.error) {
                                alert(JSON.stringify(response.error));
                            }
                        },
                        error: function(response) {
                            try {
                                let res = JSON.parse(response.responseText);
                                alert(res.message || "An error occurred on the server.");
                            } catch (e) {
                                alert("Internal Server Error (500). Please check Laravel logs.");
                            }
                        },
                    }, );
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
                    ajax: "{{ route('getClosedTicketsListData') }}",
                    columns: [
                        //  { data: 'DT_RowIndex', searchable: false, orderable: false },
                        //  { data: 'id'},
                        {
                            'targets': 0,
                            'searchable': false,
                            'orderable': false,
                            'className': 'dt-body-center',
                            data: 'id', //get id value
                            'render': function(data, type, full, meta) {
                                return '<input type="checkbox" name="ticketid[]" value="' + data +
                                    '"></input>';
                            }
                        },
                        {
                            data: 'idBtn'
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
                        // {
                            // data: 'category'
                        // },
                        // {
                            // data: 'model'
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
                            data: 'serial'
                        },
                        {
                            data: 'note'
                        },
                        {
                            data: 'solution'
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
                    ]
                });
            });
        </script>
    @endsection
