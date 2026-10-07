
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Product Serial no Update') }}</div>
                <div class="card-body">
                    <!-- Table -->
                    <!-- Table -->
            <form action="{{ url('updateSerial') }}" method="POST">
            @csrf
                    <table id='ticketsList' class='table table-striped table-bordered text-center table-hover'
                        width="100%">
                        <thead>
                            <tr>
                                <td>ID</td>
                                <td>Date</td>
                                <td>Category</td>
                                <td>Model</td>
                                <td>Serial No</td>
                                <td>Note</td>
                                <td>CreateBy</td>
                                <!-- <td>Status</td> -->
                            </tr>
                        </thead>
                        <tbody>
                            @if (!empty($arrData))
                            <!-- //////// not empty check -->
                            @foreach ($arrData as $row)
                            <tr>
                               
                             <td>{{ $row['id'] ?? '' }}
                             <input type="hidden" name="proId[]" value="{{ $row['proid'] ?? '' }}"/> 
                             <input type="hidden" name="batchId" value="{{ $row['batch_id'] ?? '' }}"/> 
                             </td>
                             <td>{{ $row['date'] ?? '' }}</td>
                             <td>{{ $row['category'] ?? '' }}</td>
                             <td>{{ $row['model'] ?? '' }}</td>
                             <td>{{ $row['serial_no'] ?? '' }}
                                <input type="hidden" name="serialNo[]" value="{{ $row['serial_no'] ?? '' }}"/>
                             </td>
                             
                             <td>{{ $row['note'] ?? '' }}</td>
                             <td>{{ $row['created_by_user'] ?? '' }}</td>
                             <!-- <td>{{ $row['status'] ?? '' }}</td> -->
                            </tr>
                            @endforeach
                            @endif
                        </tbody>
                        
                    </table>
                    <button type="submit" class="btn btn-warning float-right">UpdateSerial</button>
</form>
                </div>
            </div>
        </div>
    </div>
