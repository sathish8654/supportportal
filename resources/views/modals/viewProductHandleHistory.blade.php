<style type="text/css">
    table {
        /* table-layout: fixed;
        word-wrap: break-word; */
    }

    table th,
    table td {
        overflow: hidden;
    }

    .at-timeline {
      position: relative;
      width: 100%;
    }

    .at-timeline:after {
      content: "";
      position: absolute;
      width: 2px;
      height: 100%;
      top: 0;
      left: 50%;
      background: #2783e8;
    }
    .timeline--single {
      position: relative;
      margin: 50px 0;
    }
    .timeline--single:nth-child(odd) .timeline--details {
      margin-right: 50%;
      text-align: right;
      padding-right: 50px;
    }

    .timeline--single:nth-child(odd) .time--date{
      position: absolute;
      left: 50%;
      top: 0px;
      margin-left: 20px;
      width: 300px;
    }
    .timeline--single:nth-child(even) .time--date{
      position: absolute;
      right: 50%;
      top: 0px;
      margin-right: 20px;
      width: 300px;
      text-align: right;
    }

    .timeline--single:nth-child(even) .timeline--details {
      margin-left: 50%;
      text-align: left;
      padding-left: 50px;
    }

    .at-timeline .timeline--details:before {
      content: "";
      position: absolute;
      width: 35px;
      height: 2px;
      top: 10px;
      background: #2783e8;
    }

    .timeline--single:nth-child(odd) .timeline--details:before {
      right: 50%;
    }
    .timeline--single:nth-child(even) .timeline--details:before {
      left: 50%;
    }

    .timeline--title {
      margin-top: 5px;
    }

    .hideShow{
      display:none;
    }
</style>
<!-- Script -->


<script type="text/javascript">
    // CSRF Token
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
</script>


  <!-- Modal -->
  <div class="modal" id="dataModal" tabindex="-1" role="dialog" aria-labelledby="dataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dataModalLabel">Ticket Details</h5>
                <button type="button" class="close close-all" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <p style="margin-left: 20px;">
            Ticket ID: <span id="ticketDetails" style="font-family: monospace; padding-left: 5px;"></span><br>
            Replaced Parts: <span id="parts" style="font-family: monospace; padding-left: 5px; color: red"></span>
            </p>

            <div class="modal-body">
          
              <div class="myData"></div>
              
                <!-- Data will be displayed here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>




<script>
 $('#dataModal').on('hidden.bs.modal', function() {
  $('.myData').addClass('hideShow');
  console.log("modal closed and content cleared");
});
    // resources/js/app.js
$(document).ready(function() {
    // Open modal on view button click
   

    $('#productLog').on('click', '.view-data-history', function() {
      $('.myData').removeClass('hideShow');
        var id = $(this).data('id');
        $.ajax({
            url: "{{ route('logProductHandlerDetails') }}",
            data: { _token: CSRF_TOKEN, id: id },
            // url: '/logsTickets/' + id, // Endpoint to fetch data for a specific record
            type: 'GET',
            success: function(response) {

                const product = response.product[0];
                const parts = [];
                if (product.id) parts.push(product.id);
                if (product.companyname && product.companyname !== 'null' && String(product.companyname).trim() !== '') {
                    parts.push(product.companyname);
                }
                if (product.name && product.name !== 'null' && String(product.name).trim() !== '') {
                    parts.push(product.name);
                }
                const displayText = parts.join(' | ');
                $('#ticketDetails').text(displayText);

                console.log(response.parts);
                //   const displayText = `${response.parts[0]}`;
                $('#parts').text(response.parts);


                // Populate modal body with fetched data
                var modalBody = $('.myData');
                modalBody.empty();
                
                    // Mapping object for status names
                var statusMap = {
                    // 1: 'Open',
                    // 2: 'Close',
                    // 3: 'InProgress',
                    // 4: 'QC',
                    // 5: 'ReWork',
                    // 6: 'Delivered',
                    1:'Production', 
                    2:'Testing', 
                    3:'ReWork', 
                    4:'GenerateSerial', 
                    5:'Ready', 
                    6:'Sold', 
                    7:'NotUsable/defective'	
                    // Add more mappings as needed
                };

                // Construct table and append to modal body

                var tableHtml = '<div class="timeline at-timeline" style="padding: 100px;">';

                for (var i = 0; i < response.product.length; i++) {
                    var ticketInfo = response.product[i];
                    var siNo = i + 1;
                    var statusName = statusMap[ticketInfo.status] || 'Unknown';
                        tableHtml += '<div class="timeline--single">';
                        tableHtml += '<span class="time--date text-right">';
                        // tableHtml += '<span class="time--date"></span>';
                        tableHtml += '<span>' + ticketInfo.createdUserName + '</span><br><span style="font-size: 11px; color: gray;">' + ticketInfo.created_at + '</span>';
                        tableHtml += '</span>';

                        tableHtml += '<div class="timeline--details reverse">';
                        tableHtml += '<h4 class="timeline--title">Created</h4>';
                        if (ticketInfo.solution  != null) {
                        tableHtml += '<p>' +  ticketInfo.solution +'</p>';
                        }
                        tableHtml += '</div>';
                }

                for (var i = 0; i < response.logs.length; i++) {
                  var log = response.logs[i];
                  var siNo = i + 1;
                  var statusName = statusMap[log.status] || 'Unknown';

                  if (log.updatedUserName  == null) {
                    break;
                  } else {
                    tableHtml += '</div>';
                    tableHtml += '<div class="timeline--single">';
                    // tableHtml += '<div class="timeline--details text-right">';
                    tableHtml += '<div class="timeline--details">';
                    tableHtml += '<h4 class="timeline--title">' + statusName + '</h4>';
                    if (log.solution  != null) {
                      tableHtml += '<p>' + log.solution + '</p>';
                    }
                    tableHtml += '</div>';
                    tableHtml += '<span class="time--date">';
                    // tableHtml += '<span>' + log.createdUserName + '-' + log.created_at + '</span><br>';
                    tableHtml += '<span>' + log.updatedUserName + '</span><br><span style="font-size: 11px; color: gray;">' + log.updated_at + '</span>';
                    tableHtml += '</span>';
                  }
                }

                for (var i = 0; i < response.product.length; i++) {
                    var ticketInfo = response.product[i];
                    var siNo = i + 1;
                    var statusName = statusMap[ticketInfo.status] || 'Unknown';

                    if (ticketInfo.delivery_status  == 2) {
                      tableHtml += '</div>';
                        tableHtml += '<div class="timeline--single">';
                        tableHtml += '<span class="time--date text-right">';
                        // tableHtml += '<span class="time--date"></span>';
                        tableHtml += '<span>' + ticketInfo.updatedUserName + '</span><br><span style="font-size: 11px; color: gray;">' + ticketInfo.updated_at + '</span>';
                        tableHtml += '</span>';

                        tableHtml += '<div class="timeline--details reverse">';
                        tableHtml += '<h4 class="timeline--title">Completed</h4>';
                        if (ticketInfo.solution  != null) {
                        tableHtml += '<p>' +  ticketInfo.solution +'</p>';
                        }
                        tableHtml += '</div>';

                  } else {
                    break;
                  }
                }
                tableHtml += '</div>';

                // tableHtml += '</tbody></table>';
                modalBody.append(tableHtml);
                // Add more data fields as needed
                $('#dataModal').modal('show');
            },
            error: function(xhr, status, error) {
                // Handle errors
                console.error(error);
            }
        });
    });
});

</script>
