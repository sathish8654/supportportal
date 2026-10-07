<?php

namespace App\Http\Controllers;

use App\Models\LRvendor;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $leads = Lead::with(['vendor', 'salesExecutive', 'activities', 'quotations'])
            ->orderBy('id', 'desc')
            ->paginate(15);

        $customers = LRvendor::all();

        return view('leads.index', compact('leads', 'customers'));
    }

    public function create()
    {
        $customers = LRvendor::all();
        return view('leads.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required', // maps to vendor_id
            'lead_source' => 'required',
            'interested_products' => 'nullable|string',
            'estimated_value' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        $vendorId = $request->post('customer_id') ?: $request->post('vendor_id');

        $lead = Lead::create([
            'vendor_id' => $vendorId,
            'sales_executive_id' => Auth::id(),
            'lead_source' => $request->post('lead_source'),
            'interested_products' => $request->post('interested_products'),
            'estimated_value' => $request->post('estimated_value', 0.00),
            'status' => 'new',
            'workflow_stage' => 'sales_executive',
            'notes' => $request->post('notes'),
            'created_by' => Auth::id(),
        ]);

        // Audit Trail Activity
        LeadActivity::create([
            'lead_id' => $lead->id,
            'user_id' => Auth::id(),
            'action' => 'created',
            'stage_to' => 'sales_executive',
            'remarks' => 'Lead created in CRM',
        ]);

        return redirect()->route('leads.index')->with('success', 'Lead created successfully.');
    }

    public function show($id)
    {
        $lead = Lead::with(['vendor', 'salesExecutive', 'activities.user', 'quotations.items'])->findOrFail($id);
        return view('leads.show', compact('lead'));
    }

    public function edit($id)
    {
        $lead = Lead::with('vendor')->findOrFail($id);
        $customers = LRvendor::all();
        return view('leads.edit', compact('lead', 'customers'));
    }

    public function update(Request $request, $id)
    {
        $lead = Lead::findOrFail($id);

        $request->validate([
            'customer_id' => 'nullable',
            'lead_source' => 'nullable',
            'interested_products' => 'nullable|string',
            'estimated_value' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        $vendorId = $request->post('customer_id') ?: $request->post('vendor_id') ?: $lead->vendor_id;

        $lead->update([
            'vendor_id' => $vendorId,
            'lead_source' => $request->post('lead_source', $lead->lead_source),
            'interested_products' => $request->post('interested_products', $lead->interested_products),
            'estimated_value' => $request->post('estimated_value', $lead->estimated_value),
            'notes' => $request->post('notes', $lead->notes),
            'status' => $request->post('status', $lead->status),
        ]);

        LeadActivity::create([
            'lead_id' => $lead->id,
            'user_id' => Auth::id(),
            'action' => 'updated',
            'remarks' => 'Lead details updated',
        ]);

        return redirect()->route('leads.index')->with('success', 'Lead updated successfully.');
    }

    public function destroy($id)
    {
        $lead = Lead::findOrFail($id);
        $lead->delete();

        return redirect()->route('leads.index')->with('success', 'Lead deleted successfully.');
    }
}
