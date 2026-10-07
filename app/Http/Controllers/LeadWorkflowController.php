<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadWorkflowController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function requestDelete(Request $request, $id)
    {
        $lead = Lead::findOrFail($id);
        $oldStage = $lead->workflow_stage;
        $lead->status = 'hold';
        $lead->save();

        LeadActivity::create([
            'lead_id' => $lead->id,
            'user_id' => Auth::id(),
            'action' => 'delete_requested',
            'stage_from' => $oldStage,
            'stage_to' => $oldStage,
            'remarks' => $request->post('remarks', 'Delete request submitted'),
        ]);

        return back()->with('success', 'Lead delete request submitted successfully.');
    }

    public function approveToAccounts(Request $request, $id)
    {
        $lead = Lead::findOrFail($id);
        $oldStage = $lead->workflow_stage;
        $lead->workflow_stage = 'accounts';
        $lead->status = 'in_discussion';
        $lead->save();

        LeadActivity::create([
            'lead_id' => $lead->id,
            'user_id' => Auth::id(),
            'action' => 'approved_to_accounts',
            'stage_from' => $oldStage,
            'stage_to' => 'accounts',
            'remarks' => $request->post('remarks', 'Lead approved to Accounts department'),
        ]);

        return back()->with('success', 'Lead approved to Accounts successfully.');
    }

    public function approveQuotation(Request $request, $id)
    {
        $quotation = Quotation::findOrFail($id);
        
        // Update quotation status to approved by head
        $quotation->status = 'approved_by_head';
        $quotation->save();

        $lead = $quotation->lead;
        if ($lead) {
            $oldStage = $lead->workflow_stage;
            $lead->workflow_stage = 'md_approval';
            $lead->status = 'quotation_sent';
            $lead->save();

            LeadActivity::create([
                'lead_id' => $lead->id,
                'user_id' => Auth::id(),
                'action' => 'quotation_approved',
                'stage_from' => $oldStage,
                'stage_to' => 'md_approval',
                'remarks' => $request->post('remarks', 'Quotation approved by Sales Head'),
            ]);
        }

        return back()->with('success', 'Quotation approved successfully.');
    }

    public function customerConfirmed(Request $request, $id)
    {
        $quotation = Quotation::findOrFail($id);

        // Update quotation status to accepted by customer
        $quotation->status = 'accepted_by_customer';
        $quotation->save();

        $lead = $quotation->lead;
        if ($lead) {
            $oldStage = $lead->workflow_stage;
            $lead->workflow_stage = 'completed';
            $lead->status = 'confirmed';
            $lead->save();

            LeadActivity::create([
                'lead_id' => $lead->id,
                'user_id' => Auth::id(),
                'action' => 'customer_confirmed',
                'stage_from' => $oldStage,
                'stage_to' => 'completed',
                'remarks' => $request->post('remarks', 'Customer confirmed quotation'),
            ]);
        }

        return back()->with('success', 'Quotation confirmed by customer.');
    }
}
