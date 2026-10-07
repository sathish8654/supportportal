@php
    $rowCount = 4; // Total, Active, Paid, Due
    if ($simStatusInActive !== 0) $rowCount++;
    if ($paymentNotPaid !== 0) $rowCount++;
    if ($kycStatusPending !== 0) $rowCount++;
    if ($imeiPending !== 0) $rowCount++;
    if ($overDue !== 0) $rowCount++;
    
    $isLong = $rowCount > 6;

    // Calculate sizing dynamically based on row count
    if ($rowCount <= 4) {
        $padding = '10px 12px';
        $fontSize = '13.5px';
        $lineHeight = '1.4';
        $btnPadding = '2px 8px';
        $btnHeight = '24px';
    } elseif ($rowCount <= 6) {
        $padding = '6px 10px';
        $fontSize = '12px';
        $lineHeight = '1.2';
        $btnPadding = '1px 6px';
        $btnHeight = '21px';
    } else {
        $padding = '3px 6px';
        $fontSize = '11px';
        $lineHeight = '1.0';
        $btnPadding = '0px 4px';
        $btnHeight = '18px';
    }

    // Calculate visible labels for Pie Chart
    $visibleLabelsCount = 0;
    if ($unassignTickets > 0) $visibleLabelsCount++;
    if ($assignInprogressTickets > 0) $visibleLabelsCount++;
    if ($qcTickets > 0) $visibleLabelsCount++;
    if ($deliveryPending > 0) $visibleLabelsCount++;

    $hasBlinkingBanner = $deliveryPending > 0;
    
    if ($visibleLabelsCount <= 2) {
        $chartContainerHeight = $hasBlinkingBanner ? '160px' : '190px';
        $chartCanvasHeight = $hasBlinkingBanner ? '150' : '180';
    } else {
        $chartContainerHeight = $hasBlinkingBanner ? '140px' : '155px';
        $chartCanvasHeight = $hasBlinkingBanner ? '130' : '145';
    }
@endphp
<div class="container-fluid ">	
	<div class="row ">
		<div class="col-md-6">
			<div class="card" style="height: 300px; display: flex; flex-direction: column; justify-content: space-between; overflow: hidden;">
				<div style="text-align: center; font-weight: 700; color: #006192; font-family: 'Allianz-Neo', sans-serif; font-size: 13.5px; padding: 8px 10px; background-color: #cfe2ff; border-bottom: 1px solid #b6d4fe;">
					Last 7 days Tickets Report
				</div>
				<div class="card-body" style="flex: 1; padding: 10px !important; display: flex; align-items: center; justify-content: center; height: calc(100% - 38px);">
					<canvas id="canvas1" width="600" height="200" class="chartjs-render-monitor"></canvas>
				</div>
			</div>
		</div>

		<script src="{{ asset('js/charts/Chart.js') }}"></script>
		<script src="{{ asset('js/charts/utils.js') }}"></script>

		<script>
			var c_date = <?= $chartDate ?>;
			var open_count = <?= $open_count ?>;
			var close_count = <?= $close_count ?>;
			var inprogress_count = <?= $inprogress_count ?>;
			var delivery_status_count = <?= $delivery_status_count ?>;
			var config = {
				type: 'line',
				data: {
					labels: c_date,//close date show
					datasets: [{
						label: 'Open',
						backgroundColor: window.chartColors.red,
						borderColor: window.chartColors.red,
						fill: false,
						data: open_count,

					}, {
						label: 'InProgress',
						backgroundColor: window.chartColors.yellow,
						borderColor: window.chartColors.yellow,
						fill: false,
						data: inprogress_count,

					}, {
						label: 'Closed',
						backgroundColor: window.chartColors.green,
						borderColor: window.chartColors.green,
						fill: false,
						data: close_count, //close count show
					}, {
						label: 'Delivered',
						backgroundColor: window.chartColors.orange,
						borderColor: window.chartColors.orange,
						fill: false,
						data: delivery_status_count,

					}]
				},
				options: {

					onClick: function (c, i) {
						e = i[0];
						// console.log(e._index)
						var clickedDate = this.data.labels[e._index];
						var y_value = this.data.datasets[0].data[e._index];
						console.log(clickedDate);
						sendPostRequest(clickedDate);
						// console.log(y_value);
					},

					responsive: true,
					maintainAspectRatio: false,
					title: {
						display: false,
						text: 'Last 7 days Tickets Report'
					},
					scales: {
						// xAxes: [{
						// 	display: true,
						// 	scaleLabel: {
						// 		display: true,
						// 		labelString: 'Date'
						// 	},

						// }],
						yAxes: [{
							display: true,
							//type: 'logarithmic',
							scaleLabel: {
								display: true,
								labelString: 'Tickets Counts'
							},
							// ticks: {
							// 	min: 0,
							// 	max: 10,

							// 	// forces step size to be 5 units
							// 	stepSize: 2
							// }
						}]
					}
				}
			};


			function sendPostRequest(clickedDate) {
				var form = document.createElement('form');
				form.method = 'POST';
				form.action = '/logTickets'; // Replace with your target URL

				var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
				var csrfInput = document.createElement('input');
				csrfInput.type = 'hidden';
				csrfInput.name = '_token';
				csrfInput.value = csrfToken;
				form.appendChild(csrfInput);

				var dateInput = document.createElement('input');
				dateInput.type = 'hidden';
				dateInput.name = 'from_date';
				dateInput.value = clickedDate;
				form.appendChild(dateInput);

				var dateInput = document.createElement('input');
				dateInput.type = 'hidden';
				dateInput.name = 'to_date';
				dateInput.value = clickedDate;
				form.appendChild(dateInput);

				var chartInput = document.createElement('input');
				chartInput.type = 'hidden';
				chartInput.name = 'filter';
				chartInput.value = 'YES';
				form.appendChild(chartInput);

				document.body.appendChild(form);
				form.submit();
			};

			window.onload = function () {
				var ctx = document.getElementById('canvas1').getContext('2d');
				window.myLine = new Chart(ctx, config);

			};

			// document.getElementById('randomizeData').addEventListener('click', function () {
			// 	config.data.datasets.forEach(function (dataset) {
			// 		dataset.data = dataset.data.map(function () {
			// 			return randomScalingFactor();
			// 		});
			// 	});
			// 	window.myLine.update();
			// });

		</script>






		<div class="col-md-3">
			<div class="card" style="height: 300px; padding: 10px; display: flex; flex-direction: column; justify-content: space-between; overflow: hidden;">
				<div style="text-align: center; font-weight: 700; color: #006192; font-family: 'Allianz-Neo', sans-serif; font-size: 13.5px; margin: -10px -10px 0 -10px; padding: 8px 10px; background-color: #cfe2ff; border-bottom: 1px solid #b6d4fe;">
					Open Tickets
				</div>

				<!-- Canvas (centered in upper area) -->
				<div class="d-flex justify-content-center align-items-center flex-grow-1" style="min-height: 0; padding: 4px 0;">
					<canvas id="pieChart" style="max-height: 150px;"></canvas>
				</div>

				<!-- Status items row: 3 items (Open, Inprogress, QC) -->
				<div class="d-flex justify-content-between align-items-center px-2 pb-1" style="font-family: 'Allianz-Neo', sans-serif;">
					@if ($unassignTickets > 0)
						<div>
							<a href="viewTickets" class="btn btn-sm p-0" style="color: #006192; font-size: 10px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; white-space: nowrap; transition: all 0.2s; line-height: 1.2;">
								<span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background-color: aqua; margin-right: 6px;"></span>
								Open - {{ $unassignTickets }}
							</a>
						</div>
					@endif

					@if ($assignInprogressTickets > 0)
						<div>
							<a href="processTickets" class="btn btn-sm p-0" style="color: #006192; font-size: 10px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; white-space: nowrap; transition: all 0.2s; line-height: 1.2;">
								<span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background-color: #ffd700; margin-right: 6px;"></span>
								Inprogress - {{ $assignInprogressTickets }}
							</a>
						</div>
					@endif

					@if ($qcTickets > 0)
						<div>
							<a href="qcTickets" class="btn btn-sm p-0" style="color: #006192; font-size: 10px; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; white-space: nowrap; transition: all 0.2s; line-height: 1.2;">
								<span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background-color: pink; margin-right: 6px;"></span>
								QC - {{ $qcTickets }}
							</a>
						</div>
					@endif
				</div>

				<script>
					var unassignTickets = <?= $unassignTickets ?>;
					var assignInprogressTickets = <?= $assignInprogressTickets ?>;
					var qcTickets = <?= $qcTickets ?>;
					var closedTickets = <?= $closedTickets ?>;
					var deliveryPending = <?= $deliveryPending ?>;
					var canvasP = document.getElementById("pieChart");
					var ctxP = canvasP.getContext('2d');

					// Function to filter out labels with zero values
					function getLabels() {
						const labels = [
							{ label: `Open - ${unassignTickets}`, value: unassignTickets },
							{ label: `Inprogress - ${assignInprogressTickets}`, value: assignInprogressTickets },
							{ label: `QC - ${qcTickets}`, value: qcTickets },
							// { label: `DeliveryPending - ${deliveryPending}`, value: deliveryPending }
						];

						// Filter out labels where the value is zero
						return labels.filter(ticket => ticket.value > 0).map(ticket => ticket.label);
					}


					var myPieChart = new Chart(ctxP, {
						type: 'pie',
						data: {
							// "Closed - {{$closedTickets}}",
							labels: ["Open - {{$unassignTickets}}", "Inprogress - {{$assignInprogressTickets}}", "QC - {{$qcTickets}}", "DeliveryPending - {{$deliveryPending}}"],
							// labels: getLabels(),
							datasets: [{
								// closedTickets,
								data: [unassignTickets, assignInprogressTickets, qcTickets, deliveryPending],
								backgroundColor: ['aqua', 'yellow', 'pink', 'lightgreen', 'lightblue', 'orange'],
								hoverOffset: 10,
							}]
						},

						options: {
							responsive: true,
							maintainAspectRatio: false,
							title: {
								display: false
							},
							legend: {
								display: false
							}
						}
					});

					canvasP.onclick = function (e) {
						var slice = myPieChart.getElementAtEvent(e);
						if (!slice.length) return; // return if not clicked on slice
						var label = slice[0]._model.label;
						switch (label) {
							// add case for each label/slice
							case 'Open - {{$unassignTickets}}':
								//  alert('clicked on Open - {{$unassignTickets}}');
								window.location.replace('viewTickets');
								break;
							case 'Inprogress - {{$assignInprogressTickets}}':
								window.location.replace('processTickets');
								break;
							case 'QC - {{$qcTickets}}':
								window.location.replace('qcTickets');
								break;
							case 'DeliveryPending - {{$deliveryPending}}':
								window.location.replace('closedTickets');
								break;
						}
					}
				</script>
				@if ($deliveryPending !== 0)
					<div class="d-flex justify-content-center invalid" style="margin: 0 -10px -10px -10px; padding: 4px !important;">
						<a href="{{route('closedTickets')}}"><span style="color: white;">Delivery Pending  - </span>&nbsp;<span style="color: white;">{{ $deliveryPending }}</span></a>
					</div>
				@endif
			</div>
		</div>



		<div class="col-md-3">
			<div class="card" style="height: 300px; overflow: hidden;">
				<div style="height: 100%; overflow-y: auto;">
					<table class="table table-striped table-sm small table-bordered mb-0 compact-table h-100">
						<thead class="thead-dark">
							<tr class="table-primary" style="font-family: 'Allianz-Neo', sans-serif; font-weight: 700; color: #006192;">
								<td style="position: sticky; top: 0; background-color: #cfe2ff !important; z-index: 2; border-bottom: 1px solid #b6d4fe !important; font-weight: 700; color: #006192;">SIM Status</td>
								<td class="text-center" style="position: sticky; top: 0; background-color: #cfe2ff !important; z-index: 2; border-bottom: 1px solid #b6d4fe !important; font-weight: 700; color: #006192;">Count</td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>Total</td>
								<td class="text-center">{{ $simCount }}</td>
							</tr>
							<tr>
								<td>Active</td>
								<td class="text-center">{{ $simStatusActive }}</td>
							</tr>
							@if ($simStatusInActive !== 0)
								<tr>
									<td>InActive</td>
									<td class="text-center">{{ $simStatusInActive }}</td>
								</tr>
							@endif
							<tr>
								<td>Paid</td>
								<td class="text-center">{{ $paymentPaid }}</td>
							</tr>
							@if ($paymentNotPaid !== 0)
								<tr>
									<td>UnPaid</td>
									<td class="text-center">
									<button type="button" class="btn btn-sm btn-info filter-payment" data-status="2" onclick="window.location.href='/usersim?trigger=filter-payment&status=2'">
										{{ $paymentNotPaid }}
									</button>
									</td>
								</tr>
							@endif
							@if ($kycStatusPending !== 0)
								<tr>
									<td>KYC Pending</td>
									<td class="text-center">
									<button type="button" class="btn btn-sm btn-info filter-kycStatusPending" data-status="2" onclick="window.location.href='/usersim?trigger=filter-kycStatusPending&status=2'">
									{{ $kycStatusPending }}
										</button>
									</td>
								</tr>
							@endif
							@if ($imeiPending !== 0)
								<tr>
									<td>IMEI Pending</td>
									<td class="text-center">{{ $imeiPending }}</td>
								</tr>
							@endif
							<tr class="table-warning">
								<td>Due(<30 days)</td>
								<td class="text-center">{{ $dueDaysCount }}</td>
							</tr>
							@if ($overDue !== 0)
								<tr>
									<td class="invalid"><a href="#" class="filter-payment" data-status="2" onclick="window.location.href='/usersim?trigger=filter-payment&status=2'"><span style="color: white;">Over Due </span></a></td>
									<td class="invalid text-center"><a href="#" class="filter-payment" data-status="2" onclick="window.location.href='/usersim?trigger=filter-payment&status=2'"><span style="color: white;">{{ $overDue }}</span></a></td>
								</tr>
							@endif
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<hr>

<!-- flashing background CSS -->
<style>
	table.table.compact-table tbody tr td,
	table.table.compact-table tbody td,
	table.table.compact-table td,
	table.table.compact-table th,
	table.table.compact-table td.invalid {
		padding: {{ $padding }} !important;
		line-height: {{ $lineHeight }} !important;
		font-size: {{ $fontSize }} !important;
		vertical-align: middle !important;
	}
	.compact-table .btn-sm {
		padding: {{ $btnPadding }} !important;
		font-size: calc({{ $fontSize }} - 1px) !important;
		line-height: {{ $lineHeight }} !important;
		height: {{ $btnHeight }} !important;
		margin: 0 !important;
	}
	.invalid{
		color: #fff;
		padding: 10px;
		/* display: inline-block; */
		/* border-radius: 5px; */
		animation: blinkingBackground 2s infinite;
	}
	@keyframes blinkingBackground{
		0%		{ background-color: #10c018;}
		25%		{ background-color: #1056c0;}
		50%		{ background-color: #ef0a1a;}
		75%		{ background-color: #254878;}
		100%	{ background-color: #04a1d5;}
	}
</style>


	<style>
		.table-container {
			height: 100%;
			overflow-y: auto;
			container-type: inline-size;
		}

		.table-container thead th {
			position: sticky;
			top: 0;
			background: white;
			z-index: 1;
			padding: 8px 12px !important;
		}

		.table-container thead tr:first-child th {
			white-space: nowrap !important;
			text-transform: none !important;
			font-size: clamp(10px, 4.8cqw, 14px) !important;
			letter-spacing: normal !important;
			background-color: #cfe2ff !important;
			font-family: 'Allianz-Neo', sans-serif !important;
			font-weight: 700 !important;
			color: #006192 !important;
			border-bottom: 1px solid #b6d4fe !important;
			padding: 3px 12px !important;
			line-height: 16px !important;
			top: 0 !important;
			z-index: 3 !important;
		}

		.table-container thead tr:last-child th {
			padding: 3px 12px !important;
			top: 22px !important;
			z-index: 2 !important;
		}

		.table-container table.table tbody tr td,
		.table-container table.table tbody td,
		.table-container table.table td {
			padding: 8px 12px !important;
			font-size: 11px !important;
			line-height: 1.2 !important;
			white-space: nowrap !important;
			overflow: hidden !important;
			text-overflow: ellipsis !important;
			max-width: 130px !important;
			vertical-align: middle !important;
		}
	</style>

	<div class="row mb-4">
		<div class="col-md-3">
			<div class="card" style="height: 300px; overflow: hidden;">
				<div class="table-container">
					<table class="table table-striped table-sm small table-bordered mb-0">
						<thead class="thead-light">
							<tr style="text-align: center">
								<th colspan="4">
									Fault Device - Overall
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
									<!-- <td style="text-align: center"><a href="< echo route('logTickets', ['category' => $value->category, 'model' => $value->model, ])?>">{{ $value->name }}</a></td> -->
									<td style="width:5%;text-align: center">{{ $value->count }}</td>
								</tr>
							</tbody>
						@endforeach
					</table>
				</div>
			</div>
		</div>

		<div class="col-md-3">
			<div class="card" style="height: 300px; overflow: hidden;">
				<div class="table-container">
					<table class="table table-striped table-sm  small table-bordered mb-0">
						<thead class="thead-light">
							<tr style="text-align: center">
								<th colspan="4">
									Fault Device - Last 60 days
								</th>
							</tr>

							<tr>
								<th style="text-align: center">Name</th>
								<th style="width:5%;text-align: center">Count</th>
							</tr>
						</thead>
						@foreach($LRticketsFaultDeviceCount60days as $value) 
							<tbody>
								<tr>
									<td style="text-align: center">
										<a href="<?php echo route('partsUsage', ['category' => $value->category, 'model' => $value->model]) ?>">
										{{ $value->name }}</a> </td>
									<td style="width:5%;text-align: center">{{ $value->count }}</td>
								</tr>
							</tbody>
						@endforeach
					</table>
				</div>
			</div>
		</div>
		
		<div class="col-md-3">
			<div class="card" style="height: 300px; overflow: hidden;">
				<div class="table-container">
					<table class="table table-striped table-sm  small table-bordered mb-0">
						<thead class="thead-light">
							<tr style="text-align: center">
								<th colspan="4">
									Fault Components - Overall
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
										{{ $value->name }} </a> </td>
									<td style="width:5%;text-align: center">{{ $value->count }}</td>
								</tr>
							</tbody>
						@endforeach
					</table>
				</div>
			</div>
		</div>

		
		<div class="col-md-3">
			<div class="card" style="height: 300px; overflow: hidden;">
				<div class="table-container">
					<table class="table table-striped table-sm  small table-bordered mb-0">
						<thead class="thead-light">
							<tr style="text-align: center">
								<th colspan="4">
									Fault Components - Last 30 days
								</th>
							</tr>

							<tr>
								<th style="text-align: center">Name</th>
								<th style="width:5%;text-align: center">Count</th>
							</tr>
						</thead>
						@foreach($LRpartsReplacedCount30days as $value) 
							<tbody>
								<tr>
									<td style="text-align: center">
										<a href="<?php echo route('partsUsage', ['part_name' => $value->part_name, 'part_number' => $value->part_number]) ?>">
										{{ $value->name }} </a> </td>
									<td style="width:5%;text-align: center">{{ $value->count }}</td>
								</tr>
							</tbody>
						@endforeach
					</table>
				</div>
			</div>
		</div>
	</div>