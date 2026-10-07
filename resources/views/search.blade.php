@extends('layouts.app')
@section('content')
@include('modals/viewTicketsHistory')

@php
$serial = '';
$address = '';

if (!empty($filter) && !empty($filter['serial_val'])) {
$serial = $filter['serial_val'];
} else {
$address = $filter['address_val'] ?? '';
}
@endphp

<div class="container-flux m-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Search product Information') }}</div>
                <div class="card-body">
                    <div class="row justify-content-center">
                        <form action="{{ url('search') }}" method="POST" class="mb-3">
                            @csrf

                            <div class="input-group search-premium-group">
                                {{-- ▼ Filter dropdown --}}
                                <div class="input-group-prepend">

                                    <!-- <label for="filterMenu">Filter by</label> -->
                                    <select class="form-control" id="filterMenu" name='search_param'>
                                        <option value="">Filter by</option>
                                        <option value="serial" data-target="serialDiv" @if(!empty($filter) &&
                                            !empty($filter['serial'])) selected @endif>Serial</option>
                                        <option value="address" data-target="addressDiv" @if(!empty($filter) &&
                                            !empty($filter['address'])) selected @endif>Address</option>
                                        <!-- <option value="serial" data-target="serialDiv">Serial</option> -->
                                        <!-- <option value="address" data-target="addressDiv">Address</option> -->
                                    </select>

                                </div>

                                <!-- {{-- hidden field sent with the form --}} -->
                                <!-- <input type="hidden" name="search_param" id="search_param" value=""> -->

                                {{-- ── Serial input: visible + readonly by default --}}
                                <div id="serialDiv" style="width:415px;">
                                    <input type="text" class="form-control" name="serial"
                                        placeholder="Enter device serial number…" value="{{$serial}}" readonly required>
                                </div>

                                {{-- ── Address select: hidden by default --}}
                                <div id="addressDiv" style="width:430px; display:none;">
                                    <select class="browser-default custom-select select2" name="address" id="address"
                                        disabled style="width:430px; display:none;" required>
                                        <option value="" selected disabled>Select Address</option>
                                        @foreach ($mobileNumber as $item)
                                        @php
                                        $select_address = (!empty($filter['address_val']) && $filter['address_val'] ==
                                        $item->id) ? 'selected' : '';
                                        @endphp
                                        <option value="{{ $item->id }}" {{ $select_address }}>
                                            {{ $item->mobile1 }} – {{ $item->name }} – {{ $item->companyname }}
                                        </option>
                                        @endforeach
                                    </select>

                                </div>

                                <div class="input-group-append">
                                    <button class="btn btn-outline-primary" type="submit">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </div>

                                <div class="input-group-append">
                                    <button class="btn btn-outline-danger" onClick="window.location.reload();">
                                        <i class="fa fa-refresh"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-body">
                        <label>Product Info</label>
                        <!-- Table -->
                        <table id='productLog' class='table table-striped table-bordered text-center table-hover'
                            width="100%">
                            <thead>
                                <tr>
                                    <td>ID</td>
                                    <td>Date</td>
                                    <td>Batch</td>
                                    <td>Category</td>
                                    <td>Model</td>
                                    <td>Serial No</td>
                                    <td>Note</td>
                                    <td>Assigned</td>
                                    <td>CreateBy</td>
                                    <td>Days</td>
                                    <td>Action</td>
                                </tr>
                            </thead>
                            <tbody>
                                @if (!empty($arrData))
                                <!-- //////// not empty check -->
                                @foreach ($arrData as $row)
                                <tr>
                                    <!-- <td><button type="button" class="btn btn-primary btn-sm view-data"
                                        data-id="{{ $row['id'] }}">{{ $row['id'] ?? '' }}</button></td> -->
                                    <td>{{ $row['productId'] ?? '' }}</td>
                                    <td>{{ $row['date'] ?? '' }}</td>
                                    <td>{{ $row['batch'] ?? '' }}</td>
                                    <td>{{ $row['category'] ?? '' }}</td>
                                    <td>{{ $row['model'] ?? '' }}</td>
                                    <td>{{ $row['serial_no'] ?? '' }}</td>
                                    <td>{{ $row['note'] ?? '' }}</td>
                                    <td>{{ $row['assigned_user'] ?? '' }}</td>
                                    <td>{{ $row['created_by_user'] ?? '' }}</td>
                                    <td>{{ $row['daysCount'] ?? '' }}</td>
                                    <td>{!! $row['action'] ?? '' !!}</td>
                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <br>
                    <hr>
                    <br>
                    <div class="card-body">
                        <!-- Table -->
                        <label>Tickets Info</label>
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
                                @if (!empty($arrData2))
                                <!-- //////// not empty check -->
                                @foreach ($arrData2 as $row)
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
    <script>
    $('.select2').select2({});

    /** Toggles which block is shown + editable */
    function swapSection(targetId) {

        // serial block
        const serialBlock = $("#serialDiv");
        const serialField = serialBlock.find("input");

        // address block
        const addressBlock = $("#addressDiv");
        const addressField = addressBlock.find("select");

        if (targetId === "serialDiv") {
            serialBlock.show();
            serialField.prop("readonly", false);
            addressBlock.hide();
            addressField.prop("disabled", true);
        } else if (targetId === "addressDiv") {
            addressBlock.show();
            addressField.prop("disabled", false);
            serialBlock.hide();
            serialField.prop("readonly", true).val(""); // clear stale value
        } else {
            serialField.prop("readonly", true).val(""); // clear stale value
        }
    }


    $(document).ready(function() {
        $("#filterMenu").on("change", function() {
            const selectedOption = $(this).find(":selected");

            const label = selectedOption.text(); // Serial / Address
            const targetDiv = selectedOption.data(
                "target"); // serialDiv / addressDiv
            const paramVal = selectedOption.val(); // "serial" or "address"

            $("#filter_label").text(label); // update label
            $("#search_param").val(paramVal); // hidden input for backend
            swapSection(targetDiv); // display relevant section
        });

        // 🪄 Trigger the event on page load
        $("#filterMenu").trigger("change");
    });
    </script>
    @endsection