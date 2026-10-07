
<div class="table-responsive">
    <table class="table table-striped table-grid table-bordered text-center center">
        <thead class="thead-dark">
            <tr>
                <th scope="col">Total</th>
                <th scope="col">Active</th>
                @if ($simStatusInActive !== 0)
                    <th scope="col">InActive</th>
                @endif
                <th scope="col">Paid</th>
                @if ($paymentNotPaid !== 0)
                    <th scope="col">UnPaid</th>
                @endif
                @if ($kycStatusPending !== 0)
                    <th scope="col">KYC Pending</th>
                @endif
                @if ($imeiPending !== 0)
                    <th scope="col">IMEI Pending</th>
                @endif
                @if ($dueDaysCount !== 0)
                    <th scope="col">Due(<30 days)</th>
                @endif
                @if ($overDue !== 0)
                    <th scope="col">OverDue</th>
                @endif
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="table-secondary">{{ $simCount }}</td>
                <td class="table-success">{{ $simStatusActive }}</td>
                @if ($simStatusInActive !== 0)
                    <td class="table-warning">{{ $simStatusInActive }}</td>
                @endif
                <td class="table-info">{{ $paymentPaid }}</td>
                @if ($paymentNotPaid !== 0)
                    <td class="table-warning">
                        <button type="button" class="btn btn-warning filter-payment" data-status="2">
                            {{ $paymentNotPaid }}
                        </button>
                    </td>
                @endif
                @if ($kycStatusPending !== 0)
                    <td class="table-info">
                        <button type="button" class="btn btn-info filter-kycStatusPending" data-status="2">
                            {{ $kycStatusPending }}
                        </button>
                    </td>
                @endif
                @if ($imeiPending !== 0)
                    <td class="table-info">{{ $imeiPending }}</td>
                @endif
                @if ($dueDaysCount !== 0)
                    <td class="table-success">{{ $dueDaysCount }}</td>
                @endif
                @if ($overDue !== 0)
                    <td class="table-warning">
                        <button type="button" class="btn btn-warning filter-payment" data-status="2">
                            {{ $overDue }}
                        </button>
                    </td>
                @endif
            </tr>

        </tbody>
    </table>
</div>
