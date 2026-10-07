@extends('layouts.app')
@section('content')

<svg class="loader" id="loader" width="15%" viewBox='0 0 340 340' style="display: none;">
    <circle cx='170' cy='170' r='160' stroke='#E2007C' />
    <circle cx='170' cy='170' r='135' stroke='#404041' />
    <circle cx='170' cy='170' r='110' stroke='#E2007C' />
    <circle cx='170' cy='170' r='85' stroke='#404041' />
</svg>
<span class="sr-only">Loading...</span>


<div class="container-flux m-4 ">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Product Ready to Generate Serial') }}</div>
                <div class="card-body">
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
                                <td>Ready</td>
                                <td>Update</td>
                                <td width="10%">Define Serial</td>
                                <td>CreateBy</td>
                                <td>Days</td>
                            </tr>
                        </thead>
                        <tbody>
                            @if (!empty($arrData))
                            @foreach ($arrData as $row)
                            <tr>
                                <td>{{ $row['id'] ?? '' }}</td>
                                <td>{{ $row['date'] ?? '' }}</td>
                                <td>{{ $row['category'] ?? '' }}</td>
                                <td>{{ $row['model'] ?? '' }}</td>
                                <td>{{ $row['note'] ?? '' }}</td>
                                <td>{{ $row['qty'] ?? '' }}</td>
                                <td>{{ $row['ready'] ?? '' }}</td>
                                <td>{{ $row['serialCount'] ?? '' }}</td>
                                <td>{!! $row['action'] !!}</td>
                                <td>{{ $row['created_by_user'] ?? '' }}</td>
                                <td>{{ $row['daysCount'] ?? '' }}</td>
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="appendProductModelData"></div>
</div>

<script type="text/javascript">
var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');

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

// append product model
function appendProductModel(id, qty, upSerialCount) {

    var toSerialCount = Number($('.toSerial' + id).val());
    var countCheck = qty - upSerialCount;
    if (!toSerialCount) {
        alert('Please enter a valid count!');
        return false;
    }

    if (qty < toSerialCount) {
        alert('Entered count cannot exceed the Total Quantity');
        $('.toSerial' + id).focus();
        return false;
    } else {
        $.ajax({
            url: "{{ route('appendProductModel') }}",
            type: 'post',
            data: {
                _token: CSRF_TOKEN,
                id: id,
                count: toSerialCount,
            },
            success: function(data) {
                // alert(response.message);
                $('.appendProductModelData').html(data.table_view);
            }
        });
    }
}
</script>

@endsection