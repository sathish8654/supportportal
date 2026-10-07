@extends('layouts.app')

@section('content')

@php
    $from_date = !empty($filters['from_date']) ? date('Y-m-d', strtotime($filters['from_date'])) : '';
    $to_date = !empty($filters['to_date']) ? date('Y-m-d', strtotime($filters['to_date'])) : '';
@endphp

<style>
		.table-container {
			height: 300px;
			/* Adjust the height as needed */
			overflow-y: auto;
		}

		.table-container thead th {
			position: sticky;
			top: 0;
			background: white;
			z-index: 1;
		}
	</style>

<svg id="loader" width="15%" viewBox="0 0 340 340" style="display: none;">
    <circle cx="170" cy="170" r="160" stroke="#E2007C"/>
    <circle cx="170" cy="170" r="135" stroke="#404041"/>
    <circle cx="170" cy="170" r="110" stroke="#E2007C"/>
    <circle cx="170" cy="170" r="85" stroke="#404041"/>
</svg>
<span class="sr-only">Loading...</span>


    <div class="container-flux m-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center py-2">
                <span class="mb-0">{{ __('Analyze Parts Usage') }}</span>
                <form action="{{ url('partsUsage') }}" method="POST" class="mb-0">
                    @csrf
                    <div class="d-flex align-items-center" style="gap: 8px;">
                        <input type="date" name="from_date" id="from_date" class="form-control form-control-sm text-dark bg-white"
                            placeholder="From Date" value="{{ $from_date }}" style="width: 170px; font-weight: normal;" />
                        
                        <span class="text-white-50 mx-1" style="font-size: 0.85rem;">to</span>
                        
                        <input type="date" name="to_date" id="to_date" class="form-control form-control-sm text-dark bg-white"
                            placeholder="To Date" value="{{ $to_date }}" style="width: 170px; font-weight: normal;" />
                        
                        <button type="submit" name="filter" id="filter" class="btn btn-primary btn-sm px-3 ms-2">FILTER</button>
                        <a href="{{route('partsUsage')}}" class="btn btn-info btn-sm px-3 text-white">RESET</a>
                    </div>
                </form>
            </div>
            <div class="card-body">



                            <div class="row">

                            <div class="col-md-6">
			<div class="table-container">
				<table id="myTableFaultComp" class="table table-striped table-sm  small table-bordered">
					<thead class="thead-light">
						<tr style="text-align: center">
							<th colspan="4">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
								<span>Fault Components</span>
                                <div class="position-relative d-inline-block">
                                    <input type="text" id="faultComp" class="table-search-input" onkeyup="faultComp()" placeholder="Search for names.." title="Type in a name" style="padding-right: 2rem !important;">
                                    <button type="button" class="search-clear-btn" id="clearFaultComp">&times;</button>
                                </div>
    </div>
							</th>
						</tr>

						<tr>
							<th style="text-align: center">Name</th>
							<th style="width:5%;text-align: center">Count</th>
						</tr>
					</thead>
					@foreach($LRpartsReplacedCount as $value)
						<tbody>
							<tr>
								<td style="text-align: center">
                                    <a href="<?php echo route('partsUsage', ['part_name' => $value->part_name, 'part_number' => $value->part_number]) ?>">
                                        {{ $value->name }}</a> </td>
								<td style="width:5%;text-align: center">{{ $value->count }}</td>
							</tr>
						</tbody>
					@endforeach
				</table>

<script>
function faultComp() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("faultComp");
  var clearBtn = document.getElementById("clearFaultComp");
  if (input.value) {
    clearBtn.style.display = "flex";
  } else {
    clearBtn.style.display = "none";
  }
  filter = input.value.toUpperCase();
  table = document.getElementById("myTableFaultComp");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[0];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
}

$(document).ready(function() {
  $('#clearFaultComp').on('click', function() {
    $('#faultComp').val('');
    faultComp();
  });
});
</script>

			</div>
		</div>

<div class="col-md-6">
			<div class="table-container">
				<table id="myTableFaultDevice" class="table table-striped table-sm small table-bordered">
					<thead class="thead-light">
						<tr style="text-align: center">
							<th colspan="4">
                                <div style="display: flex; justify-content: space-between; align-items: center;">

                                <span >Fault Device</span>
                                <div class="position-relative d-inline-block">
                                    <input type="text" id="faultDevice" class="table-search-input" onkeyup="faultDevice()" placeholder="Search for names.." title="Type in a name" style="padding-right: 2rem !important;">
                                    <button type="button" class="search-clear-btn" id="clearFaultDevice">&times;</button>
                                </div>
</div>
                            </th>
						</tr>
						<tr>
							<th style="text-align: center">Name</th>
							<th style="width:5%;text-align: center">Count</th>
						</tr>
					</thead>
					@foreach($LRticketsFaultDeviceCount as $value)
						<tbody>
							<tr>
								<td style="text-align: center">
                                    <a href="<?php echo route('partsUsage', ['category' => $value->category, 'model' => $value->model]) ?>">
                                        {{ $value->name }}</a></td>
								<!-- <th style="text-align: center"><a href="< echo route('logTickets', ['category' => $value->category, 'model' => $value->model, ])?>">{{ $value->name }}</a></th> -->
								<td style="width:5%;text-align: center">{{ $value->count }}</td>
							</tr>
						</tbody>
					@endforeach
				</table>

                <script>
                    function faultDevice() {
                      var input, filter, table, tr, td, i, txtValue;
                      input = document.getElementById("faultDevice");
                      var clearBtn = document.getElementById("clearFaultDevice");
                      if (input.value) {
                        clearBtn.style.display = "flex";
                      } else {
                        clearBtn.style.display = "none";
                      }
                      filter = input.value.toUpperCase();
                      table = document.getElementById("myTableFaultDevice");
                      tr = table.getElementsByTagName("tr");
                      for (i = 0; i < tr.length; i++) {
                        td = tr[i].getElementsByTagName("td")[0];
                        if (td) {
                          txtValue = td.textContent || td.innerText;
                          if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            tr[i].style.display = "";
                          } else {
                            tr[i].style.display = "none";
                          }
                        }
                      }
                    }

                    $(document).ready(function() {
                      $('#clearFaultDevice').on('click', function() {
                        $('#faultDevice').val('');
                        faultDevice();
                      });
                    });
                </script>
			</div>
			</div>
			</div>


                        @if (sizeof($arrData))

<hr>
   <div class="container-flux m-4 ">

              <!-- Table -->
            <table id='ticketsList' class='table table-striped table-bordered text-center table-hover' width="100%">
                    <thead>
                        <tr>
                            <td>Ticket</td>
                            <td>Name</td>
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
                        @if (!empty($arrData)) <!-- //////// not empty check -->
                        @foreach ($arrData as $row)
                            <tr>
                                <td><button type="button" class="btn btn-primary btn-sm view-data" data-id="{{ $row['ticketId'] }}">{{ $row['ticketId'] ?? '' }}</button></td>
                                <td>{{ $row['LRvendor_name'] ?? '' }} {{ $row['LRvendor_companyname'] ?? '' }}</td>
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
<!-- /////////////////////////////////////////// -->
<script type="text/javascript">
                        $(document).ready(function () {

                            // Show loader before DataTable initializes
                            $('#loader').show();
                            $('.container-flux').addClass('spinners');
                            setTimeout(function () {
                                let table = $('#ticketsList').DataTable({
                                    deferRender: true,
                                    responsive: true,
                                    stateSave: true,
                                    bDestroy: true,
                                    bprocessing: true,
                                    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                                    aaSorting: [[0, 'desc']],
                                    dom: 'Bfrtip',
                                    buttons: ['excel', 'pageLength'],
                                    processing: true, // Enables DataTables' built-in processing indicator

                                    initComplete: function () {
                                        // Hide loader after DataTable is fully loaded
                                        $('#loader').hide();
                                        $('.container-flux').removeClass('spinners')
                                    }
                                });
                            }, 1000); // Delay of 2 seconds before initializing DataTable
                        });

         </script>
                 @else
                    <script> $('#loader').hide(); </script>
                @endif
@endsection
