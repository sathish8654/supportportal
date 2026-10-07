<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\LRvendor;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Order;
use App\Models\WorkOrder;
use App\Models\Installation;
use App\Models\User;

try {
    // 1. Get or create sample vendor
    $vendor = LRvendor::first();
    if (!$vendor) {
        $vendor = LRvendor::create([
            'name' => 'Demo Customer Contact',
            'companyname' => 'Acme Technologies Pvt Ltd',
            'mobile1' => '9876543210',
            'city' => 'Chennai',
            'state' => 'Tamil Nadu',
            'status' => 1,
        ]);
        echo "Created sample LRvendor ID: {$vendor->id}\n";
    } else {
        echo "Using existing LRvendor ID: {$vendor->id} ({$vendor->companyname})\n";
    }

    // 2. Get sample user
    $user = User::first();
    $userId = $user ? $user->id : 1;

    // 3. Test Lead Creation
    $lead = Lead::create([
        'vendor_id' => $vendor->id,
        'sales_executive_id' => $userId,
        'lead_source' => 'Website',
        'interested_products' => 'GPS Tracker Model-X, Lora Sensor',
        'estimated_value' => 75000.00,
        'status' => 'new',
        'workflow_stage' => 'sales_executive',
        'notes' => 'Test Lead created for workflow verification',
        'created_by' => $userId,
    ]);
    echo "1. Lead created successfully! ID: {$lead->id}\n";

    // 4. Test Lead Activity Logging
    $activity = LeadActivity::create([
        'lead_id' => $lead->id,
        'user_id' => $userId,
        'action' => 'created',
        'stage_to' => 'sales_executive',
        'remarks' => 'Initial lead entry',
    ]);
    echo "2. Lead activity logged! ID: {$activity->id}\n";

    // 5. Test Quotation Creation
    $qNumber = 'QT-' . date('Ymd') . '-' . rand(100, 999);
    $quotation = Quotation::create([
        'quotation_number' => $qNumber,
        'lead_id' => $lead->id,
        'vendor_id' => $vendor->id,
        'sales_executive_id' => $userId,
        'subtotal' => 60000.00,
        'gst_amount' => 10800.00,
        'transport_charges' => 1500.00,
        'installation_charges' => 2700.00,
        'total_amount' => 75000.00,
        'status' => 'approved_by_md',
        'created_by' => $userId,
    ]);
    echo "3. Quotation created! Number: {$quotation->quotation_number}\n";

    // 6. Test Quotation Item Creation
    $item = QuotationItem::create([
        'quotation_id' => $quotation->id,
        'item_description' => 'GPS Tracker Model-X with 1 Year Subscription',
        'quantity' => 10,
        'unit_price' => 6000.00,
        'gst_rate' => 18.00,
        'total_price' => 70800.00,
    ]);
    echo "4. Quotation Item added! ID: {$item->id}\n";

    // 7. Test Sales Order Creation
    $oNumber = 'SO-' . date('Ymd') . '-' . rand(100, 999);
    $order = Order::create([
        'order_number' => $oNumber,
        'quotation_id' => $quotation->id,
        'vendor_id' => $vendor->id,
        'advance_amount' => 25000.00,
        'payment_status' => 'partial',
        'status' => 'in_production',
        'created_by' => $userId,
    ]);
    echo "5. Sales Order created! Number: {$order->order_number}\n";

    // 8. Test Work Order Creation
    $woNumber = 'WO-' . date('Ymd') . '-' . rand(100, 999);
    $workOrder = WorkOrder::create([
        'wo_number' => $woNumber,
        'order_id' => $order->id,
        'assigned_to' => $userId,
        'quantity_units' => 10,
        'status' => 'completed',
        'remarks' => 'Assembly and QC completed',
    ]);
    echo "6. Work Order created! Number: {$workOrder->wo_number}\n";

    // 9. Test Installation Creation
    $installation = Installation::create([
        'work_order_id' => $workOrder->id,
        'service_engineer_id' => $userId,
        'scheduled_date' => date('Y-m-d'),
        'status' => 'assigned',
        'customer_feedback' => 'Scheduled field installation',
    ]);
    echo "7. Installation Task created! ID: {$installation->id}\n";

    echo "\nFULL END-TO-END SALES MODULE FLOW VERIFIED SUCCESSFULLY IN DATABASE!\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
