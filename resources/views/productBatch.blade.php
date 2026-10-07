@extends('layouts.app')
@section('content')
<!-- include('modals/viewTicketsHistory') -->

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
                <div class="card-header">{{ __('Batch Items') }}</div>
                <div class="card-body">
                    <form action="{{ url('allLogTickets') }}" method="POST">
                        @csrf
                        <div class="row input-daterange form-group ">
                            <div class="col-md-2 ">
                                <div class="form-group">
                                    <input type="date" name="from_date" id="from_date" class="form-control"
                                        placeholder="From Date" value="{{ $from_date }}" />
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <input type="date" name="to_date" id="to_date" class="form-control"
                                        placeholder="To Date" value="{{ $to_date }}" />
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="hidden" id="statusIdss">
                                    <button type="submit" name="filter" id="filter"
                                        class="btn btn-primary">FILTER</button>
                                    <a href="{{route('allLogTickets')}}" class="btn btn-info">RESET</a>
                                </div>
                            </div>
                        </div>
                    </form>
                    <br>

                    <!-- Table -->
                    <table id='ticketsList' class='table table-striped table-bordered text-center table-hover'
                        width="100%">
                        <thead>
                            <tr>
                                <td>ID</td>
                                <td>Date</td>
                                <td>Category</td>
                                <td>Model</td>
                                <td>Note</td>
                                <td>Qty</td>
                                <td>Assigned</td>
                                <td>CreateBy</td>
                                <td>Days</td>
                                <td>Status</td>
                            </tr>
                        </thead>
                        <tbody>
                            @if (!empty($arrData))
                            <!-- //////// not empty check -->
                            @foreach ($arrData as $row)
                            <tr>
                                <!-- <td><button type="button" class="btn btn-primary btn-sm view-data"
                                        data-id="{{ $row['id'] }}">{{ $row['id'] ?? '' }}</button></td> -->
                                <td>{{ $row['id'] ?? '' }}</td>
                                <td>{{ $row['date'] ?? '' }}</td>
                                <td>{{ $row['category'] ?? '' }}</td>
                                <td>{{ $row['model'] ?? '' }}</td>
                                <td>{{ $row['note'] ?? '' }}</td>
                                <td>{{ $row['qty'] ?? '' }}</td>
                                <td>{{ $row['assigned_user'] ?? '' }}</td>
                                <td>{{ $row['created_by_user'] ?? '' }}</td>
                                <td>{{ $row['daysCount'] ?? '' }}</td>
                                <td>{{ $row['status'] ?? '' }}</td>
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

            initComplete: function() {
                // Hide loader after DataTable is fully loaded
                $('#loader').hide();
                $('.container-flux').removeClass('spinners')
            }
        });
    }, 1000); // Delay of 2 seconds before initializing DataTable
});
</script>

@endsection