<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\users_deleted;
use App\Models\imei_registration;
use App\Models\LRcategory;
use App\Models\LRcase;
use App\Models\LRvendor;
use App\Models\LRtickets;
use App\Models\LRticketsAssignLog;
use DataTables;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Validation\Rule;

class TicketCloseController extends Controller
{

    // session expire to redirect to login page
    public function __construct()
    {
        $this->middleware('auth');
    }

     
    public function closedTickets()
    {
        $users = User::whereIn('role_id', ['2','3'])->orderBy("name", "asc")->get();

        return view('closedTickets', ["users" => $users]);
    }


    
    public function getClosedTicketsListData()
    {
            $closedTickets  = LRtickets::select(
                            'LRtickets.*',
                            'users.name as user_name',
                            'LRvendor.name as LRvendor_name',
                            'LRvendor.companyname as LRvendor_companyname',
                            'LRcase.name as case'
                            )
                            ->leftJoin('users', 'users.id', '=', 'LRtickets.created_by')
                            ->leftJoin('LRvendor', 'LRvendor.id', '=', 'LRtickets.vendorid')
                            ->leftJoin('LRcase', 'LRcase.id', '=', 'LRtickets.case')
                            ->where('LRtickets.status', 2)
                            ->where('LRtickets.delivery_status', 1);

            $closedTickets = $closedTickets->get();

        return Datatables::of($closedTickets)

            ->addIndexColumn()

            ->editColumn('partsReplaced', function ($row) {
                $partsReplaced = DB::table('LRparts_replaced')
                        ->where('LRparts_replaced.ticket_id', $row->id)
                        ->select(DB::raw("CONCAT(part_name, ' ', part_number) AS part_info"))
                        ->pluck('part_info') // gets array of part_info
                        ->implode(', ');
                return $partsReplaced;
            })

            ->editColumn('idBtn', function ($row) {
                $ticketButton = '<button type="button" class="btn btn-sm btn-outline-primary view-data" data-id="' . $row->id . '">' . $row->id . '</button>';
                return $ticketButton;
            })

            ->editColumn('updated_at', function ($row) {
                return $row->updated_at->format('d M, Y H:i:s');
            })

            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d M, Y H:i:s');
            })

             ->addColumn('updated_by', function ($row) {
                $updateBy = $row->updated_by;

                    if($updateBy == null){
                        $username = "NA";
                    } else{
                        $username = User::where('id', $row->updated_by)->first()->name ?? "NA";
                    }
                    return $username;
            })
            
            ->addColumn('AssignedTo', function ($row) {
                $assignedTo = $row->userid;
                    if($assignedTo == null){
                        $username = "NA";
                    } else{
                        $username = User::where('id', $assignedTo)->first()->name;
                    }
                    return $username;
            }) 

            ->rawColumns(['idBtn'])

            ->make();
    }



    public function deliveredTickets()
    {
        $users = User::whereIn('role_id', ['2','3'])->orderBy("name", "asc")->get();

        return view('deliveredTickets', ["users" => $users]);
    }


     
    public function getDeliveredTicketsListData(Request $request)
    {
            $employees = DB::table('LRtickets')
                                                ->select(
                                                                'LRtickets.*',
                                                                'users.name as user_name',
                                                                'LRvendor.name as LRvendor_name',
                                                                'LRvendor.companyname as LRvendor_companyname',
                                                                // 'LRcategory.name as LRvendor_categorysub2name',
                                                                // 'LRcategory.id as LRvendor_categorysub2id',
                                                                'LRcase.name as case',
                                                                'LRtickets.note as LRtickets_note',
                                                                'LRtickets.solution as LRtickets_solution',
                                                                'LRtickets.serial as LRtickets_serial',
                                                                'LRtickets.model as LRtickets_model',
                                                                'LRtickets.category as LRtickets_category',
                                                            )
                                                ->leftJoin('users', 'users.id', '=', 'LRtickets.created_by')
                                                ->leftJoin('LRvendor', 'LRvendor.id', '=', 'LRtickets.vendorid')
                                                ->leftJoin('LRcase', 'LRcase.id', '=', 'LRtickets.case')
                                                ->where('LRtickets.status', 2)
                                                ->where('LRtickets.delivery_status', 2)
                                                ->orderBy('LRtickets.id', 'ASC');

            if (!empty($request->from_date)) {
                $fromDate = $request->from_date;
                $toDate = $request->to_date;
                $employees = $employees->whereBetween(DB::raw('DATE(LRtickets.created_at)'), array($fromDate, $toDate));
            }


            $employees =  $employees->get();   



        return Datatables::of($employees)

            ->addIndexColumn()


            ->editColumn('partsReplaced', function ($row) {
                $partsReplaced = DB::table('LRparts_replaced')
                        ->where('LRparts_replaced.ticket_id', $row->id)
                        ->select(DB::raw("CONCAT(part_name, ' ', part_number) AS part_info"))
                        ->pluck('part_info') // gets array of part_info
                        ->implode(', ');
                return $partsReplaced;
            })

            ->editColumn('id', function ($row) {
                $ticketButton = '<button type="button" class="btn btn-sm btn-outline-primary view-data" data-id="' . $row->id . '">' . $row->id . '</button>';
                return $ticketButton;
            })

            ->editColumn('delivery_status', function ($row) {
   
                $action = '';
                if (Auth::user()->hasMenuPermission('Manage', 'userAdmins', 'edit')) {
                return '<button type="button" class="btn btn-sm btn-outline-danger resetDelivery" data-id="' . $row->id . '">RESET</button>';
                } else {
                return '<span class="badge bg-success" >Delivered</span>';
                }
            
            })

            ->addColumn('updated_by', function ($row) {

                $updateBy = $row->updated_by;

                    if($updateBy == null){
                        $username = "NA";
                    } else{
                        $username = User::where('id', $row->updated_by)->first()->name ?? "NA";
                    }
                    return $username;
                   
            })

            ->editColumn('daysCount', function ($row) {
                if (!empty($row->created_at) && !empty($row->updated_at)) {
                $to = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $row->created_at);
                $from = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $row->updated_at);
                $diff_in_days = $to->diffInDays($from);
                return round($diff_in_days);
            } else {
                return 'NA';
            }
            })
            
            ->rawColumns(['id', 'delivery_status'])

            ->make();
    }


    
              // resetDeliverySubmit 
              public function resetDeliverySubmit(Request $request)
              {
                //    dd($request->id);
                   $logNote = "Delivery cancelled or reset";
                   $created_by = Auth::user()->id;
                   $created_at = date('Y-m-d H:i:s');
           
                   $data = array(  
                       "delivery_status" => 1,
                       "updated_by"=>$created_by,
                       "updated_at"=>$created_at,
                   );    
                   $updateData = LRtickets::where('id',$request->id)->update($data); 
           
           
                   $dataLog = array(  
                       "userid"=> $created_by,
                       "ticketid"=>$request->id,
                       "priority"=>1,
                       "created_by"=>$created_by,
                       "created_at"=>$created_at,
                       "solution"=>$logNote,
                       "status"=>6,
                       "active"=>1,
                   );    
                   DB::table('LRticketsAssignLog')->insert($dataLog);
           
                   if ($updateData) {
                       return response()->json(['message' => 'Deleted Successfully!']);
                   } else {
                       return response()->json(['message' => 'Failed! Please check!']);
                   }
               }
}