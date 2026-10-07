<?php
namespace App\Http\Controllers;

use App\Models\LRcategory;
use App\Models\LRiotSim;
use App\Models\LRtickets;
use App\Models\LRticketsAssignLog;
use App\Models\LRvendor;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    // session expire to redirect to login page
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function userAddImei()
    {

        if (Auth::check() && Auth::user()->role_id == 2) {
            return view('imei');
        } else {
            return view('dashboard');
        }
    }

    public function index()
    {
        $categoris    = LRcategory::where('parent_id', 0)->count();
        $mobileNumber = LRvendor::select('mobile1', 'id')->count();

        $simCount          = LRiotSim::all()->count();
        $simStatusActive   = LRiotSim::where('simStatus', '=', 1)->count();
        $simStatusInActive = LRiotSim::where('simStatus', '=', 2)->count();
        $paymentPaid       = LRiotSim::where('payment', '=', 1)->count();
        $paymentNotPaid    = LRiotSim::where('payment', '=', 2)->count();
        $kycStatusPending  = LRiotSim::where('kycStatus', '=', 2)->where('simStatus', '=', 1)->count();
        $imeiPending       = LRiotSim::where('imei', '=', '')->orWhereNull('imei')->orWhere('imei', '=', '0')->count();

        $overDue = now()->toDateTimeString();
        $overDue = LRiotSim::where('renewalDate', '<=', $overDue)->where('simStatus', 1)->count();

        $date         = now()->addDays(31)->toDateTimeString();
        $dueDaysCount = LRiotSim::where("renewalDate", '<=', $date)->where('simStatus', 1)->count();

        $unassignTickets         = LRtickets::where('status', '=', 1)->count();
        $closedTickets           = LRtickets::where('status', '=', 2)->count();
        $assignInprogressTickets = LRtickets::where('status', '=', 3)->count();
        $qcTickets               = LRticketsAssignLog::where('status', '=', 4)->where('active', '=', 1)->count();
        $deliveryPending         = LRtickets::where('delivery_status', '=', 1)->where('status', '=', 2)->count();

        // Fetch the newest date from the records
        $newestDate = DB::table('LRtickets')
            ->select(DB::raw('MAX(GREATEST(created_at, updated_at)) as newest_date'))
            ->value('newest_date');

        // Calculate the start date (9 days before the newest date) and end date (newest date)
        $startDate = Carbon::parse($newestDate)->subDays(9)->startOfDay()->format('Y-m-d H:i:s');
        $endDate   = Carbon::parse($newestDate)->endOfDay()->format('Y-m-d H:i:s'); // Ensure today's date is included

        // HW Logs chart data generate - last 7 days day-wise line chart report
        $chartData = DB::table('LRtickets')
            ->select(
                DB::raw('SUM(status = "1") as open_count'),
                DB::raw('SUM(status = "2") - SUM(delivery_status = "2") as close_count'),
                DB::raw('SUM(status = "3") as inprogress_count'),
                DB::raw('SUM(delivery_status = "2") as delivery_status_count'),
                DB::raw('DATE(GREATEST(created_at, updated_at)) as date'),
                DB::raw('DATE(GREATEST(created_at, updated_at)) as date'),

            )
            ->whereBetween('created_at', [$startDate, $endDate])   // Filter records within the date range
            ->orWhereBetween('updated_at', [$startDate, $endDate]) // Include records updated within the date range
            ->groupBy('date')                                      // Group by date
            ->orderBy('date')                                      // Order by date
            ->get();

        // echo "<pre>";
        // print_r($chartData);
        // exit;

        $chart_log_data = [];

        foreach ($chartData as $row) {
            $chart_log_data['date'][]                  = $row->date;
            $chart_log_data['open_count'][]            = (int) $row->open_count;
            $chart_log_data['close_count'][]           = (int) $row->close_count;
            $chart_log_data['inprogress_count'][]      = (int) $row->inprogress_count;
            $chart_log_data['delivery_status_count'][] = (int) $row->delivery_status_count;
        }

        $chartDate             = json_encode($chart_log_data['date'] ?? '');
        $open_count            = json_encode($chart_log_data['open_count'] ?? '');
        $close_count           = json_encode($chart_log_data['close_count'] ?? '');
        $inprogress_count      = json_encode($chart_log_data['inprogress_count'] ?? '');
        $delivery_status_count = json_encode($chart_log_data['delivery_status_count'] ?? '');

        $LRticketsFaultDeviceCount = DB::table('LRtickets')
            ->select('category', 'model', DB::raw("CONCAT(category, '-', model) as name", ), DB::raw('count(*) as count'))
            ->groupBy('name')
            ->having('count', '>', 10)
            ->orderBy('count', 'desc')
            ->get();

        $sixtyDaysAgo = Carbon::now()->subDays(60);

        $LRticketsFaultDeviceCount60days = DB::table('LRtickets')
            ->select('category', 'model', DB::raw("CONCAT(category, '-', model) as name"), DB::raw('count(*) as count'))
            ->where('created_at', '>=', $sixtyDaysAgo)
            ->groupBy('name')
            ->having('count', '>', 10)
            ->orderBy('count', 'desc')
            ->get();

        $LRpartsReplacedCount = DB::table('LRparts_replaced')
            ->select(
                'LRparts_replaced.part_name',
                'LRparts_replaced.part_number',
                'LRparts_replaced.part_value',
                DB::raw("CONCAT(part_name, ' ', part_number) AS name"),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy(DB::raw("CONCAT(part_name, '-', part_number)"))
            // ->having('count', '>', 10)
            ->orderBy('count', 'desc')
            ->get();

        $thirtyDaysAgo = Carbon::now()->subDays(30);

        $LRpartsReplacedCount30days = DB::table('LRparts_replaced')
            ->select(
                 'LRparts_replaced.part_name',
                'LRparts_replaced.part_number',
                'LRparts_replaced.part_value',
                DB::raw("CONCAT(part_name, ' ', part_number) AS name"),
                DB::raw('COUNT(*) as count')
            )
            ->where('LRparts_replaced.created_at', '>=', $thirtyDaysAgo)
            ->groupBy(DB::raw("CONCAT(part_name, '-', part_number)"))
            // ->having('count', '>', 1)
            ->orderBy('count', 'desc')
            ->get();

        // print_r($thirtyDaysAgo->toDateTimeString());


        // echo "<pre>";
        // print_r($chart_log_data) ;
        // exit;

        // Real Sales Dashboard Metrics
        $salesTotalLeads = DB::table('LRSales_leads')->count();
        $salesTotalOrders = DB::table('LRSales_orders')->count();
        $salesTotalInstallations = DB::table('LRSales_installations')->count();
        $serviceTicketsCount = DB::table('LRtickets')->count();

        $recentLeads = \App\Models\Lead::with('vendor')
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

        $customers = LRvendor::all();

        return view('dashboard', compact(
            'chartDate',
            'open_count',
            'close_count',
            'inprogress_count',
            'delivery_status_count',
            'categoris',
            'mobileNumber',
            'simCount',
            'simStatusActive',
            'simStatusInActive',
            'paymentPaid',
            'paymentNotPaid',
            'kycStatusPending',
            'dueDaysCount',
            'unassignTickets',
            'closedTickets',
            'qcTickets',
            'assignInprogressTickets',
            'imeiPending',
            'deliveryPending',
            'LRticketsFaultDeviceCount',
            'LRticketsFaultDeviceCount60days',
            'overDue',
            'LRpartsReplacedCount',
            'LRpartsReplacedCount30days',
            'salesTotalLeads',
            'salesTotalOrders',
            'salesTotalInstallations',
            'serviceTicketsCount',
            'recentLeads',
            'customers'
        ));
    }

}
