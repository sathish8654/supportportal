<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\LRvendor;
use App\Models\LRtickets;
use App\Models\LRstatusTicketLog;
use App\Models\LRparts_replaced;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
Use Response;


// 1-Airtel,2-BSNL,3-JIO


class SearchController extends Controller
{
    // session expire to redirect to login page
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
    // dd($request->all());
    $result=$filter=[];
    
    if (!empty($request->search_param) OR !empty(request()->query('serialNo'))) {
         $result = DB::table('LRproductsLog as b')
                                            ->select(
                                                'b.*',
                                                'LRproducts.id as productId',
                                                'LRproducts.category as category',
                                                'LRproducts.model as model',
                                                'LRproducts.serial as serial_no',
                                                'LRproductsBatch.created_at as date',
                                                'createdUser.name as created_by_user',
                                                'updatedUser.name as updated_by_user',
                                                'assignedUser.name as assigned_user'
                                            )
                                            ->leftJoin('LRproducts', 'b.product_id', '=', 'LRproducts.id')
                                            ->leftJoin('LRproductsBatch', 'b.batch_id', '=', 'LRproductsBatch.id')
                                            ->leftJoin('users as assignedUser', 'b.assigned', '=', 'assignedUser.id')
                                            ->leftJoin('users as createdUser', 'b.created_by', '=', 'createdUser.id')
                                            ->leftJoin('users as updatedUser', 'b.updated_by', '=', 'updatedUser.id')
                                            ->where('b.active', 1);
                                            // ->whereNotNull('LRproducts.serial')
                                            // ->where('b.status', 4);

       
                    // Serial filter
                    if ($request->search_param === 'serial') {
                    // if (!empty($request->serial)) {
                        $result->where('LRproducts.serial', $request->serial);   // change column if needed
                        $filter['serial']=$request->search_param;
                        $filter['serial_val']=$request->serial;
                    }
                    if (!empty(request()->query('serialNo'))) {// get query 
                            $result->where('LRproducts.serial', request()->query('serialNo'));   // change column if needed
                            $filter['serial']='serial';
                            $filter['serial_val']=request()->query('serialNo');
                        }
                    // Address filter
                    if ($request->search_param === 'address') {
                    // if (!empty($request->address)) {
                        $result->where('LRproducts.vendor_id', $request->address);     // change column if needed
                        $filter['address']=$request->search_param;
                        $filter['address_val']=$request->address;
                    }


        $result = $result->get();
                }else{
                    $result=[];    
                }
        // echo '<pre>';  print($result);exit;
        // echo '<pre>';  print_r($request->address);exit;

        // Loop through the results
        $arrData=[];

    foreach ($result as $key=> $product) {
                        
         //////////////////////////////////switch statement
             switch ($product->status) {
                 case '1':
                 $status= "Production";
                 break;
                 case '2':
                 $status= "Completed";
                 break;
                                  
                 default:
                 $status= "NA";
                     break;
             }

             $date = !empty($product->date) ? \Carbon\Carbon::parse($product->date)->format('d-m-Y') : 'NA';
             $created_at = !empty($product->created_at) ? \Carbon\Carbon::parse($product->created_at)->format('d-m-Y H:i:s') : '';
             $created_by_user = (!empty($product->created_by_user) ? $product->created_by_user : 'NA') . ($created_at ? ' - ' . $created_at : '');


                if (!empty($product->date) ) {

                $from = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', now());
                $to = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $product->date);
                $diff_in_days = $to->diffInDays($from);
                $daysCount = round($diff_in_days);
                } else {
                   $daysCount = 'NA';
                }

            // Update Button data-bs-target='#updateModal'
            $updateButton = "<button  class='btn btn-sm btn-outline-primary' data-id='" . $product->id . "' data-bs-toggle='modal' data-bs-target='#productUpdateModal' ></button>";
            // Delete Button
            $deleteButton = "<button class='btn btn-sm btn-outline-danger' data-id='" . $product->id . "' ></button>";

            if (Auth::check() && Auth::user()->role_id == 2 && Auth::user()->current_team_id == 1) {
                $actionButtons =  $updateButton . " " . $deleteButton;
            } else {
                $actionButtons =  $updateButton;
            }


            $arrData[] = [
                    'id' => $product->id,
                    'productId' => $product->productId,
                    'date' => $date,
                    'batch' => $product->batch_id,
                    'category' => $product->category,
                    'model' => $product->model,
                    'serial_no' => $product->serial_no,
                    'note' => $product->note,
                    'assigned_user' => $product->assigned_user,
                    'created_by_user' => $created_by_user,
                    'daysCount' => $daysCount,
                    'action' => $actionButtons,
                    ];
            }

        $users = User::whereIn('role_id', ['2', '3'])->orderBy("name", "asc")->get();

        $mobileNumber = LRvendor::select('mobile1', 'name', 'companyname', 'id')->where('status', 1)->get();


//////////////////////// for ticket history /////////////////////////////
$tickets = [];  
if (!empty($request->search_param) OR !empty(request()->query('serialNo'))) {
        $tickets = DB::table('LRtickets')->select(
                                                'LRtickets.*',
                                                'LRvendor.name as LRvendor_name',
                                                'LRvendor.companyname as LRvendor_companyname',
                                                'LRcase.name as case',
                                                'LRtickets.serial as LRtickets_serial',
                                                'LRtickets.note as LRtickets_note',
                                                'LRtickets.delivery_status as delivery_status',
                                                'LRtickets.created_by as LRtickets_created_by',
                                                'LRtickets.created_at as LRtickets_created_at',
                                                'LRtickets.updated_by as LRtickets_updated_by',
                                                'LRtickets.updated_at as LRtickets_updated_at',

                                            )
        ->leftJoin('LRvendor', 'LRvendor.id', '=', 'LRtickets.vendorid')
        ->leftJoin('LRcase', 'LRcase.id', '=', 'LRtickets.case');

            // Serial filter
            if ($request->search_param === 'serial') {
            // if (!empty($request->serial)) {
                $tickets->where('LRtickets.serial', $request->serial);   // change column if needed
                // $filter['serial']=$request->search_param;
            }
            if (!empty(request()->query('serialNo'))) {// get query 
                $tickets->where('LRtickets.serial', request()->query('serialNo'));   // change column if needed
            }
            // Address filter
            if ($request->search_param === 'address') {
            // if (!empty($request->address)) {
                $tickets->where('LRtickets.vendorid', $request->address);     // change column if needed
                // $filter['address']=$request->search_param;
            }
            // $sql = $query->toSql();
            // dd($tickets);
        $tickets = $tickets->get();
        }else{
            $tickets = [];   
        }
        
        // echo '<pre>';  print_r($tickets);exit;
            // Loop through the results
        $arrData2=[];

    foreach ($tickets as $key=> $employee) {
                

            
                if (empty($employee->LRtickets_created_by)) {
                    $username = "NA";
                } else {
                    $createdUser = User::find($employee->LRtickets_created_by);
                    $username = $createdUser ? ($createdUser->name . " - " . $employee->LRtickets_created_at) : "NA";
                }
      
                if (empty($employee->LRtickets_updated_by)) {
                    $updated_by = "NA";
                } else {
                    $updatedUser = User::find($employee->LRtickets_updated_by);
                    $updated_by = $updatedUser ? ($updatedUser->name . " - " . $employee->LRtickets_updated_at) : "NA";
                }
     
            
                if (!empty($employee->created_at) && !empty($employee->updated_at)) {

                $to = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $employee->created_at);
                $from = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $employee->updated_at);
                $diff_in_days = $to->diffInDays($from);
                $daysCount = round($diff_in_days);
                } else {
                   $daysCount = 'NA';
                }
   
            
         //////////////////////////////////switch statement
             switch ($employee->status) {
                 case '1':
                 $status= "Open";
                 break;
                 case '2':
                 $status= "Closed";
                 break;
                 case '3':
                 $status= "Assigned";
                 break;
                 case '4':
                 $status= "QC";
                    break;
                 
                 default:
                 $status= "None";
                     break;
             }
    
            
            if ($employee->status == 2) {

                if ($employee->delivery_status == 1) {
                    $delivery_status= "Pending";
                } elseif ($employee->delivery_status == 2) {
                    $delivery_status= "Delivered";
                } else {
                    $delivery_status= "None";
                }
            } else {
                $delivery_status= "NA";
            }
    

            $partsReplaced = DB::table('LRparts_replaced')
                        ->where('LRparts_replaced.ticket_id', $employee->id)
                        ->select(DB::raw("CONCAT(part_name, ' ', part_number) AS part_info"))
                        ->pluck('part_info') // gets array of part_info
                        ->implode(', ');

            $arrData2[] = [
                //////////////////////////////////////////
                    'ticketId'=>$employee->id,
                    'LRvendor_name'=>$employee->LRvendor_name,
                    'LRvendor_companyname'=>$employee->LRvendor_companyname,
                    'case'=>$employee->case,
                    'category'=>$employee->category,
                    'model'=>$employee->model,
                    'LRtickets_serial'=>$employee->LRtickets_serial,
                    'LRtickets_note'=>$employee->LRtickets_note,
                    'cost'=>$employee->cost,
                    'solution'=>$employee->solution,
                    'partsReplaced'=>$partsReplaced,
                    'created_by' => $username,
                    'updated_by' => $updated_by,
                    'daysCount' => $daysCount,
                    'status' => $status,
                    'delivery_status' => $delivery_status,
                    ];
              //////////////////////////////////////////////        
            }

            
        // echo '<pre>';print_r($filter);exit;
        return view('search', compact('arrData', 'arrData2', 'users', 'mobileNumber','filter'));


    }


}