<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LRcategory;
use App\Models\LRvendor;
use App\Models\Quotation;
use App\Models\QuotationItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuotationWebController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $quotations = Quotation::with(['lead.vendor', 'vendor', 'salesExecutive', 'items.product.parent'])
            ->orderBy('id', 'desc')
            ->paginate(15);

        $leads = Lead::with('vendor')->get();
        $categories = LRcategory::where('status', 1)->orderBy('name', 'asc')->get();

        return view('quotations.index', compact('quotations', 'leads', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'lead_id' => 'required',
            'subtotal' => 'nullable|numeric',
            'gst_amount' => 'nullable|numeric',
            'transport_charges' => 'nullable|numeric',
            'installation_charges' => 'nullable|numeric',
            'total_amount' => 'nullable|numeric',
        ]);

        $lead = Lead::findOrFail($request->post('lead_id'));

        $quotationNumber = 'QT-' . date('Ymd') . '-' . rand(100, 999);

        $subtotal = $request->post('subtotal', 0.0);
        $gstAmount = $request->post('gst_amount', $subtotal * 0.18);
        $transport = $request->post('transport_charges', 0.0);
        $installation = $request->post('installation_charges', 0.0);
        $totalAmount = $request->post('total_amount', ($subtotal + $gstAmount + $transport + $installation));

        $quotation = Quotation::create([
            'quotation_number' => $quotationNumber,
            'lead_id' => $lead->id,
            'vendor_id' => $lead->vendor_id,
            'sales_executive_id' => Auth::id(),
            'subtotal' => $subtotal,
            'gst_amount' => $gstAmount,
            'transport_charges' => $transport,
            'installation_charges' => $installation,
            'total_amount' => $totalAmount,
            'status' => 'draft',
            'created_by' => Auth::id(),
        ]);

        // Add Quotation Items if submitted
        if ($request->has('products') && is_array($request->post('products'))) {
            foreach ($request->post('products') as $item) {
                if (!empty($item['product_id']) || !empty($item['description'])) {
                    QuotationItem::create([
                        'quotation_id' => $quotation->id,
                        'product_id' => $item['product_id'] ?? null,
                        'item_description' => $item['description'] ?? 'Product Item',
                        'quantity' => $item['quantity'] ?? 1,
                        'unit_price' => $item['unit_price'] ?? 0.0,
                        'gst_rate' => $item['gst_rate'] ?? 18.0,
                        'total_price' => $item['total_price'] ?? 0.0,
                    ]);
                }
            }
        }

        // Update Lead stage
        $lead->status = 'quotation_sent';
        $lead->workflow_stage = 'sales_head_review';
        $lead->save();

        return redirect()->route('quotations.index')->with('success', 'Quotation ' . $quotationNumber . ' created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'lead_id' => 'required',
            'subtotal' => 'nullable|numeric',
            'gst_amount' => 'nullable|numeric',
            'transport_charges' => 'nullable|numeric',
            'installation_charges' => 'nullable|numeric',
            'total_amount' => 'nullable|numeric',
        ]);

        $quotation = Quotation::findOrFail($id);

        // Only allow editing if status is draft
        if ($quotation->status !== 'draft') {
            return back()->with('error', 'Only draft quotations can be edited.');
        }

        $lead = Lead::findOrFail($request->post('lead_id'));
        $subtotal = $request->post('subtotal', 0.0);
        $gstAmount = $request->post('gst_amount', $subtotal * 0.18);
        $transport = $request->post('transport_charges', 0.0);
        $installation = $request->post('installation_charges', 0.0);
        $totalAmount = $request->post('total_amount', ($subtotal + $gstAmount + $transport + $installation));

        $quotation->update([
            'lead_id' => $lead->id,
            'vendor_id' => $lead->vendor_id,
            'subtotal' => $subtotal,
            'gst_amount' => $gstAmount,
            'transport_charges' => $transport,
            'installation_charges' => $installation,
            'total_amount' => $totalAmount,
            'status' => $request->post('status', 'draft'),
        ]);

        // Delete existing items and recreate
        $quotation->items()->delete();

        if ($request->has('products') && is_array($request->post('products'))) {
            foreach ($request->post('products') as $item) {
                if (!empty($item['product_id']) || !empty($item['description'])) {
                    QuotationItem::create([
                        'quotation_id' => $quotation->id,
                        'product_id' => $item['product_id'] ?? null,
                        'item_description' => $item['description'] ?? 'Product Item',
                        'quantity' => $item['quantity'] ?? 1,
                        'unit_price' => $item['unit_price'] ?? 0.0,
                        'gst_rate' => $item['gst_rate'] ?? 18.0,
                        'total_price' => $item['total_price'] ?? 0.0,
                    ]);
                }
            }
        }

        return redirect()->route('quotations.index')->with('success', 'Quotation ' . $quotation->quotation_number . ' updated successfully.');
    }

    public function destroy($id)
    {
        $quotation = Quotation::findOrFail($id);

        // Check if the quotation is already approved in any stage
        $approvedStatuses = ['approved_by_head', 'approved_by_accounts', 'approved_by_md', 'accepted_by_customer'];
        if (in_array($quotation->status, $approvedStatuses)) {
            return back()->with('error', 'Approved quotations cannot be deleted.');
        }

        // Delete line items first
        $quotation->items()->delete();
        $quotation->delete();

        return redirect()->route('quotations.index')->with('success', 'Quotation deleted successfully.');
    }
}
