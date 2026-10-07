@extends('layouts.app')

@section('content')
@include('modals/viewTicketsHistory')



<svg class="loader" id="loader" width="15%" viewBox='0 0 340 340' style="display: none;">
    <circle cx='170' cy='170' r='160' stroke='#E2007C' />
    <circle cx='170' cy='170' r='135' stroke='#404041' />
    <circle cx='170' cy='170' r='110' stroke='#E2007C' />
    <circle cx='170' cy='170' r='85' stroke='#404041' />
</svg>
<span class="sr-only">Loading...</span>


@php
$from_date = !empty($filters['from_date']) ? date('Y-m-d', strtotime($filters['from_date'])) : '';
$to_date = !empty($filters['to_date']) ? date('Y-m-d', strtotime($filters['to_date'])) : '';
@endphp

<div class="container-flux m-4 ">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center py-2">
                    <span class="mb-0">{{ __('All Tickets log') }}</span>
                    <form action="{{ url('allLogTickets') }}" method="POST" class="mb-0">
                        @csrf
                        <div class="d-flex align-items-center" style="gap: 8px;">
                            <select name="statusId" id="statusId" class="form-control form-control-sm text-dark bg-white" style="width: 130px;">
                                <option value="">All</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}" <?php if ($statusId == $category->id): ?>
                                    selected="selected" <?php endif; ?>>{{ $category->name }}</option>
                                @endforeach
                            </select>

                            <input type="date" name="from_date" id="from_date" class="form-control form-control-sm text-dark bg-white"
                                placeholder="From Date" value="{{ $from_date }}" style="width: 170px; font-weight: normal;" />
                            
                            <span class="text-white-50 mx-1" style="font-size: 0.85rem;">to</span>
                            
                            <input type="date" name="to_date" id="to_date" class="form-control form-control-sm text-dark bg-white"
                                placeholder="To Date" value="{{ $to_date }}" style="width: 170px; font-weight: normal;" />
                            
                            <input type="hidden" id="statusIdss">
                            <button type="submit" name="filter" id="filter" class="btn btn-primary btn-sm px-3 ms-2">FILTER</button>
                            <a href="{{route('allLogTickets')}}" class="btn btn-info btn-sm px-3 text-white">RESET</a>
                        </div>
                    </form>
                </div>
                <div class="card-body">

                    <!-- Table -->
                    <table id='ticketsList' class='table table-striped table-bordered text-center table-hover'
                        width="100%">
                        <thead>
                            <tr>
                                <td>Ticket</td>
                                <td>Name</td>
                                <!-- <td>Company</td> -->
                                <td>Case</td>
                                <td>Category</td>
                                <td>Product</td>
                                <td>Serial</td>
                                <td>Model</td>
                                <td>Cost</td>
                                <td>Solution</td>
                                <td>Parts</td>
                                <td>CreateBy</td>
                                <td>UpdateBy</td>
                                <td>Days</td>
                                <td>Status</td>
                                <td>Delivery</td>
                            </tr>
                        </thead>
                        <tbody>
                            @if (!empty($arrData))
                            <!-- //////// not empty check -->
                            @foreach ($arrData as $row)
                            <tr>
                                <td><button type="button" class="btn btn-primary btn-sm view-data"
                                        data-id="{{ $row['ticketId'] }}">{{ $row['ticketId'] ?? '' }}</button></td>
                                <td>
                                    @php
                                    $name = trim($row['LRvendor_name'] ?? '');
                                    $company = trim($row['LRvendor_companyname'] ?? '');
                                    @endphp

                                    {{ strcasecmp($name, $company) === 0 ? $name : trim($name . ' ' . $company) }}
                                </td>
                                <td>{{ $row['case'] ?? '' }}</td>
                                <td>{{ $row['category'] ?? '' }}</td>
                                <td>{{ $row['model'] ?? '' }}</td>
                                <td>{{ $row['LRtickets_serial'] ?? '' }}</td>
                                <td>{{ $row['LRtickets_note'] ?? '' }}</td>
                                <td>{{ $row['cost'] ?? '' }}</td>
                                <td>{{ $row['solution'] ?? '' }}</td>
                                <td>{{ $row['partsReplaced'] ?? '' }}</td>
                                <td>{{ $row['created_by'] ?? '' }}</td>
                                <td>{{ $row['updated_by'] ?? '' }}</td>
                                <td>{{ $row['daysCount'] ?? '' }}</td>
                                <td>{{ $row['status'] ?? '' }}</td>
                                <td>{{ $row['delivery_status'] ?? '' }}</td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
    // Show loader before DataTable initializes
    $('#loader').show();
    $('.container-flux').addClass('spinners');
    setTimeout(function() {
        let table = $('#ticketsList').DataTable({
            deferRender: true,
            responsive: true,
            stateSave: true,
            bDestroy: true,
            bprocessing: true,

            lengthMenu: [
                [10, 25, 50, -1],
                [10, 25, 50, "All"]
            ],
            aaSorting: [
                [0, 'desc']
            ],
            dom: 'Bfrtip',
            buttons: ['excel', 'pageLength'],
            processing: true, // Enables DataTables' built-in processing indicator

            initComplete: function(settings) {
                // Hide loader after DataTable is fully loaded
                $('#loader').hide();
                $('.container-flux').removeClass('spinners');

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
            }
        });
    }, 1000); // Delay of 2 seconds before initializing DataTable
});
</script>

@endsection