@extends('layouts.app')
@section('title', 'Add Lead')

@section('content')
<div class="container-fluid mt-3" style="max-width: 800px;">
    <h1 class="mb-4">Add Lead</h1>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form method="POST" action="{{ route('leads.store') }}">
                @csrf
                <div class="mb-3">
                    <label for="customer_id" class="form-label">Customer *</label>
                    <select name="customer_id" id="customer_id" required class="form-select select2" style="width: 100%">
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">
                            {{ $customer->name }}{{ $customer->mobile1 ? ' / ' . $customer->mobile1 : '' }}{{ $customer->mobile2 ? ' / ' . $customer->mobile2 : '' }}{{ $customer->companyname ? ' (' . $customer->companyname . ')' : '' }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="lead_source" class="form-label">Lead Source</label>
                    <input type="text" name="lead_source" id="lead_source" class="form-control" placeholder="e.g. Website, Referral">
                </div>
                <div class="mb-3">
                    <label for="interested_products" class="form-label">Interested Products</label>
                    <textarea name="interested_products" id="interested_products" class="form-control" rows="3" placeholder="List products the customer is interested in"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Save Lead</button>
                <a href="{{ route('leads.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#customer_id').select2({
            width: '100%'
        });
    });
</script>
@endsection
