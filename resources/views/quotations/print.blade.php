<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quotation #{{ $quotation->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .section { margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .total { font-weight: bold; font-size: 1.2em; }
        .footer { margin-top: 30px; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="header">
        <h1>QUOTATION</h1>
        <p>Support Portal - Internal Operations Management System</p>
    </div>

    <div class="section">
        <h3>Customer Details</h3>
        <p><strong>Name:</strong> {{ $quotation->lead->customer->name ?? '-' }}</p>
        <p><strong>Phone:</strong> {{ $quotation->lead->customer->phone ?? '-' }}</p>
        <p><strong>Email:</strong> {{ $quotation->lead->customer->email ?? '-' }}</p>
        <p><strong>Address:</strong> {{ $quotation->lead->customer->address ?? '-' }}</p>
    </div>

    <div class="section">
        <h3>Product Details</h3>
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Model</th>
                    <th>Model No.</th>
                    <th>Item Description</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @if($quotation->items && count($quotation->items) > 0)
                    @foreach($quotation->items as $item)
                    <tr>
                        <td>{{ $item->product->parent->name ?? '-' }}</td>
                        <td>{{ $item->product->name ?? '-' }}</td>
                        <td>{{ $item->product->model_number ?? '-' }}</td>
                        <td>{{ $item->item_description ?? '-' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>₹{{ number_format($item->unit_price ?? 0, 2) }}</td>
                        <td>₹{{ number_format($item->total_price ?? 0, 2) }}</td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td>Mock Category</td>
                        <td>Mock Product Tracker</td>
                        <td>M-100</td>
                        <td>Initial GPS Tracker deployment</td>
                        <td>1</td>
                        <td>₹{{ number_format($quotation->total_amount ?? 15000, 2) }}</td>
                        <td>₹{{ number_format($quotation->total_amount ?? 15000, 2) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="section">
        <p><strong>Subtotal:</strong> ₹{{ number_format($quotation->subtotal ?? ($quotation->total_amount ?? 15000), 2) }}</p>
        <p><strong>GST:</strong> ₹{{ number_format($quotation->gst_amount ?? 0, 2) }}</p>
        <p><strong>Transport Charges:</strong> ₹{{ number_format($quotation->transport_charges ?? 0, 2) }}</p>
        <p><strong>Installation Charges:</strong> ₹{{ number_format($quotation->installation_charges ?? 0, 2) }}</p>
        <p class="total">Total Amount: ₹{{ number_format($quotation->total_amount ?? 15000, 2) }}</p>
    </div>

    <div class="section">
        <p><strong>Status:</strong> {{ $quotation->status ?? 'pending' }}</p>
        <p><strong>Created:</strong> {{ $quotation->created_at ? $quotation->created_at->format('d-m-Y') : date('d-m-Y') }}</p>
    </div>

    <div class="footer">
        <p>Terms & Conditions Apply | Validity: 30 days</p>
    </div>
    
    <script>window.print();</script>
</body>
</html>
