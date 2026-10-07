@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Firmware Management</div>

                <div class="card-body">
                    @if (Auth::user()->hasMenuPermission('Manage', 'firmware.index', 'create'))
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center" id="toggleUploadHeader" style="cursor: pointer;">
                            <span>Upload New Firmware</span>
                            <i class="fa fa-plus" id="toggleIcon"></i>
                        </div>
                        <div class="card-body" id="uploadFormBody" style="display: none;">
                            <form action="{{ route('firmware.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Select IMEI</label>
                                            <select name="imei" id="imei_select" class="form-control select2" required>
                                                <option value="">-- Select IMEI --</option>
                                                @foreach($imeis as $imei => $deviceName)
                                                    <option value="{{ $imei }}">{{ $imei }} - {{ $deviceName }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Firmware File (.bin)</label>
                                            <input type="file" name="firmware_file" id="firmware_file" class="form-control" accept=".bin" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Firmware Notes</label>
                                            <textarea name="notes" id="firmware_notes" class="form-control" maxlength="100" rows="2"></textarea>
                                            <small class="text-muted">Remaining characters: <span id="char_count">100</span></small>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <button type="submit" class="btn btn-success">Upload</button>
                                    <button type="reset" class="btn btn-secondary">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif

                    <div class="alert alert-info d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Latest Available Firmware (Upload Folder):</strong> 
                            @forelse($uploadedFiles as $file)
                                <div class="d-inline-block mr-2">
                                    <span class="badge badge-secondary">{{ $file->getFilename() }}</span>
                                    <a href="javascript:void(0)" class="copy-firmware-url text-primary ml-1" data-url="{{ asset('firmware/upload/' . $file->getFilename()) }}" title="Copy Public URL"><i class="fa fa-copy"></i></a>
                                </div>
                            @empty
                                No firmware files available in upload folder.
                            @endforelse
                        </div>
                        @if(count($uploadedFiles) > 0 && Auth::user()->hasMenuPermission('Manage', 'firmware.index', 'delete'))
                            <div class="d-flex align-items-center">
                                <span class="mr-3 text-muted small"><strong>Last Uploaded:</strong> {{ date('Y-m-d H:i:s', $uploadedFiles[0]->getMTime()) }}</span>
                                <form action="{{ route('firmware.clearUpload') }}" method="POST" onsubmit="return confirm('Are you sure you want to clear the upload folder?')">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger">Clear Upload Folder</button>
                                </form>
                            </div>
                        @endif
                    </div>

                    <div class="table-responsive mt-4">
                        <table class="table table-bordered table-striped" id="firmwareTable">
                            <thead>
                                <tr>
                                    <th>Sl. No</th>
                                    <th>IMEI</th>
                                    <th>Files</th>
                                    <th>Date</th>
                                    <th>Version</th>
                                    <th>Size</th>
                                    <th>CRC</th>
                                    <th>Note</th>
                                    @php
                                        $hasEdit = Auth::user()->hasMenuPermission('Manage', 'firmware.index', 'edit');
                                        $hasDelete = Auth::user()->hasMenuPermission('Manage', 'firmware.index', 'delete');
                                    @endphp
                                    @if ($hasEdit || $hasDelete)
                                    <th>Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($firmwareList as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item['imei'] }}</td>
                                    <td>
                                        <a href="{{ asset('firmware/' . $item['baseName'] . '.bin') }}" class="badge badge-info" download>BIN</a>
                                        <a href="{{ asset('firmware/' . $item['baseName'] . '.txt') }}" class="badge badge-info" download>TXT</a>
                                        <a href="{{ asset('firmware/' . $item['baseName'] . '.json') }}" class="badge badge-info" download>JSON</a>
                                    </td>
                                    <td>{{ $item['date'] }}</td>
                                    <td>{{ $item['version'] }}</td>
                                    <td>{{ $item['size'] }}</td>
                                    <td><code class="text-dark">{{ $item['crc'] }}</code></td>
                                    <td>{{ $item['note'] }}</td>
                                    @if ($hasEdit || $hasDelete)
                                    <td>
                                        @if ($hasEdit)
                                        <form action="{{ route('firmware.deploy', $item['baseName']) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to deploy this firmware to the upload folder? This will clear existing files in the upload directory.')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-primary">Upload</button>
                                        </form>
                                        @endif
                                        
                                        @if ($hasDelete)
                                        <form action="{{ route('firmware.destroy', $item['baseName']) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this firmware and its associated files?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                        @endif
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#firmwareTable').DataTable({
            "order": [[3, "desc"]]
        });

        // Initialize Select2 for searchable dropdown
        $('.select2').select2({
            width: '100%'
        });

        // Filter table on IMEI selection
        $('#imei_select').on('change', function() {
            var selectedImei = $(this).val();
            if (selectedImei) {
                table.column(1).search('^' + selectedImei + '$', true, false).draw();
            } else {
                table.column(1).search('').draw();
            }
        });

        // Show popup alert if duplicate is found
        @if(session('error'))
            alert("{{ session('error') }}");
        @endif
    });

    $('#toggleUploadHeader').on('click', function() {
        $('#uploadFormBody').slideToggle();
        $('#toggleIcon').toggleClass('fa-plus fa-minus');
    });

    $('#firmware_notes').on('input', function() {
        $('#char_count').text(100 - $(this).val().length);
    });

    $('.copy-firmware-url').on('click', function() {
        var url = $(this).data('url');
        navigator.clipboard.writeText(url).then(function() {
            alert('Copied URL: ' + url);
        });
    });
</script>
@endsection