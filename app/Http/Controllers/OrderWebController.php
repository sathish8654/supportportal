<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Quotation;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderWebController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $orders = Order::with(['quotation.lead.vendor', 'vendor', 'workOrders.assignedToUser'])
            ->orderBy('id', 'desc')
            ->paginate(15);

        $quotations = Quotation::with('vendor')
            ->whereIn('status', ['approved_by_accounts', 'approved_by_md', 'accepted_by_customer'])
            ->get();

        return view('orders.index', compact('orders', 'quotations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'quotation_id' => 'required',
            'advance_amount' => 'nullable|numeric',
        ]);

        $quotation = Quotation::findOrFail($request->post('quotation_id'));

        $orderNumber = 'SO-' . date('Ymd') . '-' . rand(100, 999);
        $advance = $request->post('advance_amount', 0.00);
        $paymentStatus = ($advance >= $quotation->total_amount) ? 'paid' : (($advance > 0) ? 'partial' : 'pending');

        $order = Order::create([
            'order_number' => $orderNumber,
            'quotation_id' => $quotation->id,
            'vendor_id' => $quotation->vendor_id,
            'advance_amount' => $advance,
            'payment_status' => $paymentStatus,
            'status' => 'confirmed',
            'created_by' => Auth::id(),
        ]);

        // Generate initial Work Order
        $woNumber = 'WO-' . date('Ymd') . '-' . rand(100, 999);
        WorkOrder::create([
            'wo_number' => $woNumber,
            'order_id' => $order->id,
            'status' => 'pending',
            'quantity_units' => 1,
            'remarks' => 'Auto-generated Work Order for Sales Order ' . $orderNumber,
        ]);

        return redirect()->route('orders.index')->with('success', 'Sales Order ' . $orderNumber . ' created successfully.');
    }

    public function submitProduction(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->status = 'in_production';
        $order->save();

        return redirect()->route('orders.index')->with('success', 'Sales Order submitted to Production successfully.');
    }
}
