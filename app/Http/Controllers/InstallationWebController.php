<?php

namespace App\Http\Controllers;

use App\Models\Installation;
use App\Models\WorkOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InstallationWebController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $installations = Installation::with(['workOrder.order.vendor', 'serviceEngineer'])
            ->orderBy('id', 'desc')
            ->paginate(15);

        $workOrders = WorkOrder::with('order.vendor')
            ->where('status', 'completed')
            ->get();

        // Engineers with role_id = 15 or role_id = 2
        $engineers = User::whereIn('role_id', [15, 2])->get();

        return view('installations.index', compact('installations', 'workOrders', 'engineers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'work_order_id' => 'required',
            'service_engineer_id' => 'nullable',
            'scheduled_date' => 'nullable|date',
        ]);

        $installation = Installation::create([
            'work_order_id' => $request->post('work_order_id'),
            'service_engineer_id' => $request->post('service_engineer_id'),
            'scheduled_date' => $request->post('scheduled_date'),
            'status' => 'assigned',
        ]);

        return redirect()->route('installations.index')->with('success', 'Installation task assigned successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $installation = Installation::findOrFail($id);
        $status = $request->post('status', 'completed');

        $installation->status = $status;
        if ($status == 'completed') {
            $installation->completion_date = date('Y-m-d');
        }
        $installation->customer_feedback = $request->post('customer_feedback', $installation->customer_feedback);
        $installation->save();

        return back()->with('success', 'Installation status updated successfully.');
    }

    public function assign(Request $request, $id)
    {
        $installation = Installation::findOrFail($id);
        $installation->service_engineer_id = $request->post('service_engineer_id');
        $installation->save();

        return back()->with('success', 'Service Engineer reassigned successfully.');
    }
}
