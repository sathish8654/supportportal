<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LRvendor;
use App\Models\LRtickets;
use App\Models\LRparts_replaced;
use App\Models\LRparts;
use App\Models\imei_registration;
use DataTables;
use Illuminate\Support\Facades\Auth;
use Validator;

class PartsUsageController extends Controller
{

    // session expire to redirect to login page
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {

        
         $filters['from_date']=$filters['to_date']=[];


           $LRpartsReplacedCount = DB::table('LRparts_replaced')
            ->select(
                'LRparts_replaced.part_name',
                'LRparts_replaced.part_number',
                'LRparts_replaced.part_value',
                DB::raw("CONCAT(part_name, ' ', part_number) AS name"),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy(DB::raw("CONCAT(part_name, '-', part_number)"))
            ->orderBy('count', 'desc');
   

            $LRticketsFaultDeviceCount = DB::table('LRtickets')
                        ->select('category', 'model', DB::raw("CONCAT(category, '-', model) as name", ), DB::raw('count(*) as count'))
                        ->groupBy('name')
                        ->orderBy('count', 'desc');
                        


            $result = DB::table('LRtickets')->select(
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

        //  when filter date wise

        if (!empty($request->from_date)) {
            $fromDate = $request->from_date;
            $toDate = $request->to_date;
            $employees = $result->whereBetween(DB::raw('DATE(LRtickets.created_at)'), array($fromDate, $toDate));
            $LRpartsReplacedCount = $LRpartsReplacedCount->whereBetween(DB::raw('DATE(LRparts_replaced.created_at)'), array($fromDate, $toDate));
            $LRticketsFaultDeviceCount = $LRticketsFaultDeviceCount->whereBetween(DB::raw('DATE(LRtickets.created_at)'), array($fromDate, $toDate));
            $filters['from_date']=$fromDate;
            $filters['to_date']=$toDate;
        }

        //  when filter Parts wise

        if (!empty($request->part_name)) { // this help to filter the table, when click the "Fault Device Counts - Overall " at dashboard.
            $result = $result->leftJoin('LRparts_replaced', 'LRparts_replaced.ticket_id', '=', 'LRtickets.id');
            $result = $result->where('LRparts_replaced.part_name', $request->part_name)->where('LRparts_replaced.part_number', $request->part_number);
       
            $LRticketsFaultDeviceCount = $LRticketsFaultDeviceCount->leftJoin('LRparts_replaced', 'LRtickets.id', '=', 'LRparts_replaced.ticket_id');
            $LRticketsFaultDeviceCount = $LRticketsFaultDeviceCount->where('LRparts_replaced.part_name', $request->part_name)->where('LRparts_replaced.part_number', $request->part_number);
        
            $LRpartsReplacedCount = $LRpartsReplacedCount->where('LRparts_replaced.part_name', $request->part_name)->where('LRparts_replaced.part_number', $request->part_number);
        }
       
        //  when filter Deive wise
      
        if (!empty($request->category)) { // this help to filter the table, when click the "Fault Device Counts - Overall " at dashboard.
            $result = $result->where('LRtickets.category', $request->category)->where('LRtickets.model', $request->model);

            $LRpartsReplacedCount = $LRpartsReplacedCount->leftJoin('LRtickets', 'LRtickets.id', '=', 'LRparts_replaced.ticket_id');
            $LRpartsReplacedCount = $LRpartsReplacedCount->where('LRtickets.category', $request->category)->where('LRtickets.model', $request->model);
        
            $LRticketsFaultDeviceCount = $LRticketsFaultDeviceCount->where('LRtickets.category', $request->category)->where('LRtickets.model', $request->model);
        }



        // dd($result->toSql(), $result->getBindings());
    

        $LRpartsReplacedCount = $LRpartsReplacedCount->get();
        $LRticketsFaultDeviceCount = $LRticketsFaultDeviceCount->get();

        if($request->all()){
        $result = $result->get();
        } else {
            $result = collect();
        }

        //         echo '<pre>';
        // print_r($result);exit;


            // Loop through the results
        $arrData=[];

    foreach ($result as $key=> $employee) {
                

            
                if ($employee->LRtickets_created_by == null) {
                    $username = "NA";
                } else {
                    $username = User::where('id', $employee->LRtickets_created_by)->first()->name;

                    $username = "$username - $employee->LRtickets_created_at";
                }
      
            
                if ($employee->LRtickets_updated_by == null) {
                    $updated_by = "NA";
                } else {
                    $updated_by = User::where('id', $employee->LRtickets_updated_by)->first()->name;
                    $updated_by = "$updated_by - $employee->LRtickets_updated_at";

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

            $arrData[] = [
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

        return view('partsUsage', compact('LRpartsReplacedCount', 'LRticketsFaultDeviceCount', 'arrData', 'filters'));
    }




//     // get table data

//     public function getpartsListData(Request $request)
//     {
//         $filterId        = $request->post('filterId');
//         // dd($filterId);
//         if ($filterId==2) {
//             $parts = LRparts::select('*')->where('parent_id','!=',0)->orderBy('name', 'ASC');

//             $datatable = Datatables::of($parts)
//             ->addIndexColumn()
           
//             ->addColumn('status', function ($row) {
//                 if ($row->status == '1') {
//                     return "Active";
//                 } else {
//                     return "Inactive";
//                 }
//             })
//             ->addColumn('action', function ($row) {

//                 $iconClass = $row->status == 1 ? 'fa fa-trash' : 'fa fa-undo';
//                 $iconNote = $row->status == 1 ? 'To Disable' : 'To Enable';

//                 // Update Button
//                 $updateButton = "<button class='btn btn-sm btn-outline-primary' data-id='" . $row->id . "' data-parent_id='" . $row->parent_id. "' data-name='" . $row->name . "' data-part_value='" . $row->part_value . "'  data-bs-toggle='modal' data-bs-target='#addNewPartSubModal'  title='Edit'></button>";
//                 // Delete Button
//                 $deleteButton = "<button class='$iconClass disablePartSub' data-id='" . $row->id . "'  title='" . $iconNote . "'></button>";

//                 if (Auth::check() && Auth::user()->role_id == 2 && Auth::user()->current_team_id == 1) {
//                     return $updateButton . " " . $deleteButton;
//                 } else {
//                     return $updateButton;
//                 }
//             });
//             // Conditionally add 'partSubname' column if $filterId is 2
//             if ($filterId == 2) {
//                 $partsData = LRparts::select('*')->orderBy('name', 'ASC')->get(); // Fetch the parts list
            
//                 $datatable->addColumn('name', function ($row) use ($partsData) { 
//                     // Search in partsData for matching parent_id
//                     $matchingPart = $partsData->where('id', $row->parent_id)->first(); 
            
//                     return $matchingPart ? $matchingPart->name : '-';
//                 });
            
//                 $datatable->addColumn('partSubname', function ($row) {
//                     if ($row->parent_id != 0) {
//                         return $row->name;
//                     }
//                 });
//             }
//         return $datatable->make();
//         }else{
//             $parts = LRparts::select('*')->where('parent_id',0)->orderBy('name', 'ASC');
        
//         return Datatables::of($parts)
//             ->addIndexColumn()
           
//             ->addColumn('status', function ($row) {
//                 if ($row->status == '1') {
//                     return "Active";
//                 } else {
//                     return "Inactive";
//                 }
//             })
//             ->addColumn('action', function ($row) {

//                 $iconClass = $row->status == 1 ? 'fa fa-trash' : 'fa fa-undo';
//                 $iconNote = $row->status == 1 ? 'To Disable' : 'To Enable';

//                 // Update Button
//                 $updateButton = "<button class='btn btn-sm btn-outline-primary' data-id='" . $row->id . "' data-bs-toggle='modal' data-bs-target='#addNewPartModal'  title='Edit'></button>";
//                 // Delete Button
//                 $deleteButton = "<button class='$iconClass disablePart' data-id='" . $row->id . "'  title='" . $iconNote . "'></button>";

//                 if (Auth::check() && Auth::user()->role_id == 2 && Auth::user()->current_team_id == 1) {
//                     return $updateButton . " " . $deleteButton;
//                 } else {
//                     return $updateButton;
//                 }
//             })
            
//    ->make();
//     }
//     }



    // Add New Parent function
    public function addNewPart(Request $request)
    {
        $request->validate([
            'name' => 'unique:LRparts,name',
        ]);

        $contact = new LRparts;
        $contact->name = $request->name;
        $contact->created_by = Auth::user()->id;
        $contact->created_at = date('Y-m-d H:i:s');

        if ($contact->save()) {
            return response()->json(['message' => 'Updated Successfully!']);
        } else {
            return response()->json(['message' => 'Failed! Please check!']);
        }
    }

        // Add New Parent function
        public function addNewSubPart(Request $request)
        {
            $request->validate([
                'name' => 'unique:LRparts,name',
            ]);
            // dd($request);
            if (empty($request->subId)) {
                $contact = new LRparts;
                $contact->parent_id = $request->partIds;
                $contact->name = $request->Subname;
                $contact->part_value = $request->partValue;
                $contact->created_by = Auth::user()->id;
                $contact->created_at = date('Y-m-d H:i:s');
        
                if ($contact->save()) {
                    return response()->json(['message' => 'Added Successfully!']);
                } else {
                    return response()->json(['message' => 'Failed! Please check!']);
                }
            }else{
                $id = $request->subId;
                $result = LRparts::find($id);
                
                if (!empty($result)) {
                    $updata['parent_id'] =  $request->partIds;
                    $updata['name'] =  $request->Subname;
                    $updata['part_value'] =  $request->partValue;
                    $updata['updated_by'] = Auth::user()->id;
                    $updata['updated_at'] = date('Y-m-d H:i:s');
        
                    if ($result->update($updata)) {
                        return response()->json(['message' => 'Updated Successfully!']);
                    }  
                  }
                }
        }

    // Read Vendor address record by ID
    public function getPartInfo(Request $request)
    {

        ## Read POST data 
        $id = $request->post('id');

        $empdata = LRparts::find($id);
        $response = array();
        if (!empty($empdata)) {

            $response['id'] = $empdata->id;
            $response['name'] = $empdata->name;
          
            $response['success'] = 1;
        } else {
            $response['success'] = 0;
        }

        return response()->json($response);
    }



    // Update Employee record
    public function updatePartInfo(Request $request)
    {
        ## Read POST data
        $id = $request->post('id');
        $empdata = LRparts::find($id);
        $response = array();
        if (!empty($empdata)) {
            $updata['name'] = $request->post('name');
            $updata['status'] = 1;
            $updata['updated_by'] = Auth::user()->id;
            $updata['updated_at'] = date('Y-m-d H:i:s');

            if ($empdata->update($updata)) {
                $response['success'] = 1;
                $response['msg'] = 'Update successfully';
            } else {
                $response['success'] = 0;
                $response['msg'] = 'Record not updated';
            }
        } else {
            $response['success'] = 0;
            $response['msg'] = 'Invalid ID.';
        }

        return response()->json($response);
    }


    // Delete vendor address
    public function disablePart(Request $request)
    {
        ## Read POST data
        $id = $request->post('id');
        // Retrieve the current status
        $currentStatus = DB::table('LRparts')
            ->where('id', $id)
            ->value('status');

        // Determine the new status
        $newStatus = $currentStatus == 1 ? 2 : 1;

        // Update the status
        $result = DB::table('LRparts')
            ->where('id', $id)
            ->update(['status' => $newStatus]);
            ///////////part sub update//////////
        $subStatus = DB::table('LRparts')
            ->where('parent_id', $id)
            ->value('status');    
        // Determine the new status
        $sub = $subStatus == 1 ? 2 : 1;
        // Update the status
        $subUpdate = DB::table('LRparts')
            ->where('parent_id', $id)
            ->update(['status' => $sub]);
        if ($result) {
            return response()->json(['message' => 'Updated Successfully!']);
        } else {
            return response()->json(['message' => 'Failed! Please check!']);
        }
    }

  
}