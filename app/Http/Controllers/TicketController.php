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
use Symfony\Component\Mime\Encoder\QpContentEncoder;
use Validator;
use Illuminate\Validation\Rule;

class TicketController extends Controller
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



    // public function logsTickets()
    // {
    //     $categories = LRticketsAssignLog::all();
    //     return view('logsTickets', compact('categories'));
    // }


    public function logTicketsDetails(Request $request)
    {
        $logs = DB::table('LRticketsAssignLog')->select('LRticketsAssignLog.*', 'createdUser.name as createdUserName', 'updatedUser.name as updatedUserName')
                    ->leftJoin('users as createdUser', 'createdUser.id', '=', 'LRticketsAssignLog.created_by')
                    ->leftJoin('users as updatedUser', 'updatedUser.id', '=', 'LRticketsAssignLog.updated_by')
                    ->where('ticketid', $request->get('id'))
                    ->orderBy('LRticketsAssignLog.created_at', 'asc')
                    ->get();


        $ticket = DB::table('LRtickets')->select('LRtickets.*', 'createdUser.name as createdUserName', 'updatedUser.name as updatedUserName', 'LRvendor.name', 'LRvendor.companyname')
                    ->leftJoin('users as createdUser', 'createdUser.id', '=', 'LRtickets.created_by')
                    ->leftJoin('users as updatedUser', 'updatedUser.id', '=', 'LRtickets.updated_by')
                    ->leftJoin('LRvendor', 'LRvendor.id', '=', 'LRtickets.vendorid')
                    ->where('LRtickets.id', $request->get('id'))
                    ->get();

        $partsReplaced = DB::table('LRparts_replaced')
                        ->leftJoin('LRparts', 'LRparts_replaced.part_id', '=', 'LRparts.id')
                        ->where('LRparts_replaced.ticket_id', $request->get('id'))
                        ->select(DB::raw("CONCAT(LRparts.name, ' ', LRparts_replaced.part_value) AS part_info"))
                        ->pluck('part_info') // gets array of part_info
                        ->implode(', ');
        // return view('logsTickets', compact('logs'));
        return response()->json(['logs' => $logs, 'ticket' => $ticket, 'partsReplaced' => $partsReplaced]);
    }

      public function allLogTickets(Request $request)
      {
         $categories = DB::table('LRstatusTicketLog')->where('status', 1)->get();
 
         $resultQuery = DB::table('LRtickets')->select(
                                                 'LRtickets.*',
                                                 'LRvendor.name as LRvendor_name',
                                                 'LRvendor.companyname as LRvendor_companyname',
                                                 'LRcase.name as case',
                                                 'creator.name as creator_name',
                                                 'updater.name as updater_name',
                                                 'LRtickets.serial as LRtickets_serial',
                                                 'LRtickets.note as LRtickets_note',
                                                 'LRtickets.delivery_status as delivery_status',
                                                 'LRtickets.created_by as LRtickets_created_by',
                                                 'LRtickets.created_at as LRtickets_created_at',
                                                 'LRtickets.updated_by as LRtickets_updated_by',
                                                 'LRtickets.updated_at as LRtickets_updated_at',
                                             )
         ->leftJoin('LRvendor', 'LRvendor.id', '=', 'LRtickets.vendorid')
         ->leftJoin('LRcase', 'LRcase.id', '=', 'LRtickets.case')
         ->leftJoin('users as creator', 'creator.id', '=', 'LRtickets.created_by')
         ->leftJoin('users as updater', 'updater.id', '=', 'LRtickets.updated_by');
 
          $filters['from_date']=$filters['to_date']=[];
 
         if (!empty($request->from_date)) {
             $fromDate = $request->from_date;
             $toDate = $request->to_date;
             $resultQuery = $resultQuery->whereBetween(DB::raw('DATE(LRtickets.created_at)'), array($fromDate, $toDate));
             $filters['from_date']=$fromDate;
             $filters['to_date']=$toDate;
         }
        
         $statusId='';
         if (!empty($request->statusId)) {
            $resultQuery = $resultQuery->where('LRtickets.status',$request->statusId);
            $statusId=$request->statusId;
          }
 
         if ($request->chart == 'charData') {
             $resultQuery = $resultQuery->whereDate('LRtickets.created_at', '=', $request->date);
         }
 
         if (!empty($request->category)) {
             $resultQuery = $resultQuery->where('LRtickets.category', $request->category)->where('LRtickets.model', $request->model);
         }
     
         $result = $resultQuery->get();
         
         // Fetch all parts replaced in a single query to prevent N+1
         $ticketIds = $result->pluck('id')->toArray();
         $partsReplacedGrouped = DB::table('LRparts_replaced')
             ->whereIn('ticket_id', $ticketIds)
             ->select('ticket_id', DB::raw("CONCAT(part_name, ' ', part_number) AS part_info"))
             ->get()
             ->groupBy('ticket_id');

         $arrData=[];
 
         foreach ($result as $key=> $employee) {
                 if ($employee->creator_name == null) {
                     $username = "NA";
                 } else {
                     $username = "$employee->creator_name - $employee->LRtickets_created_at";
                 }
       
                 if ($employee->updater_name == null) {
                     $updated_by = "NA";
                 } else {
                     $updated_by = "$employee->updater_name - $employee->LRtickets_updated_at";
                 }
      
                 if (!empty($employee->created_at) && !empty($employee->updated_at)) {
                     $to = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $employee->created_at);
                     $from = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $employee->updated_at);
                     $diff_in_days = $to->diffInDays($from);
                     $daysCount = round($diff_in_days);
                 } else {
                    $daysCount = 'NA';
                 }
    
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
     
              $partsReplaced = isset($partsReplacedGrouped[$employee->id])
                  ? $partsReplacedGrouped[$employee->id]->pluck('part_info')->implode(', ')
                  : '';
 
             $arrData[] = [
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
             }
         return view('allLogTickets', compact('categories','arrData','statusId','filters'));
      }



  


    public function showTicketLog($id)
    {
        $showTicket = LRticketsAssignLog::where('ticketid', $id);
        return response()->json($showTicket);
    }


    public function index(Request $request)
    {
        $cases = LRcase::where('status', 1)->get();
        $categoris = LRcategory::where('parent_id', 0)->where('status', 1)->get();
        $mobileNumber = LRvendor::select('mobile1', 'name', 'companyname', 'id')->where('status', 1)->get();
        $users = User::whereIn('role_id', ['2', '3'])->orderBy("name", "asc")->get();

        return view('createTicket', [
            "users" => $users,
            "cases" => $cases,
            "categoris" => $categoris,
            "mobileNumber" => $mobileNumber
        ]);
    }

    // https://www.positronx.io/create-autocomplete-search-in-laravel-with-typeahead-js/
    public function autocompleteaddress(Request $request)
    {
        $queryname = $request->get('queryname');
        if ($queryname) {
            $data = LRvendor::where('name', 'LIKE', '%' . $queryname . '%')->get();
        }

        $querycompany = $request->get('querycompany');
        if ($querycompany) {
            $data = LRvendor::where('name', 'LIKE', '%' . $querycompany . '%')->get();
        }

        $response = array();
        if (!empty($data)) {

            $response['id'] = $data[0]->id;
            $response['name'] = $data[0]->name;
            $response['companyname'] = $data[0]->companyname;
            $response['mobile1'] = $data[0]->mobile1;
            $response['mobile2'] = $data[0]->mobile2;
            $response['address'] = $data[0]->address;
            $response['district'] = $data[0]->district;
            $response['city'] = $data[0]->city;
            $response['state'] = $data[0]->state;
            $response['gstno'] = $data[0]->gstno;
            $response['pincode'] = $data[0]->pincode;

            $response['success'] = 1;
        } else {
            $response['success'] = 0;
        }

        return response()->json($response);
    }

    public function createTicket(Request $request)
    {
        $categories = LRcategory::where('parent_id', 0)->orderby('name', 'asc')->get();

        if ($request->method() == 'GET') {
            return view('create-category', compact('categories'));
        }
        if ($request->method() == 'POST') {
            $validator = $request->validate([
                'name' => 'required|unique:LRcategory',
                'parent_id' => 'required|numeric'
            ]);

            LRcategory::create([
                'name' => $request->name,
                'parent_id' => $request->parent_id
            ]);

            return redirect()->back()->with('success', 'Ticket has been created successfully.');

        }

    }


    // Add New Parent function
    public function submitAddCat(Request $request)
    {
        $request->validate([

            'name' => [
                'required',
                Rule::unique('LRcategory')->where('parent_id', request('parent_id'))
            ],
            'parent_id' => [
                'required',
                Rule::unique('LRcategory')->where('name', request('name'))
            ],

        ]);

        $contact = new LRcategory;
        $contact->name = $request->name;
        $contact->parent_id = $request->parent_id;
        $contact->created_by = Auth::user()->id;
        $contact->created_at = date('Y-m-d H:i:s');

        $contact->save();

        return response()->json(['success' => 'Successfully']);

    }


    // Read Employee record by ID
    public function getSubCat1(Request $request)
    {

        ## Read POST data 
        $id = $request->post('cat_id');
        $data = LRcategory::find($id)->where('status', 1);

        $response = array();
        if (!empty($data)) {

            $response['id'] = $data->id;
            $response['name'] = $data->name;

            $response['success'] = 1;
        } else {
            $response['success'] = 0;
        }

        return response()->json($response);
    }
    // Read Employee record by ID
    public function getSubCat2(Request $request)
    {

        ## Read POST data 
        $id = $request->post('cat_id');
        $empdata = LRcategory::find($id)->where('status', 1);

        // $empdata = LRcategory::where('parent_id', '=', $id)->get();

        // dd($empdata);
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



    // Add New Parent function
    public function submitAddSubCat1(Request $request)
    {
        $request->validate([

            'name' => [
                'required',
                Rule::unique('LRcategory')->where('parent_id', request('parent_id'))
            ],
            'parent_id' => [
                'required',
                Rule::unique('LRcategory')->where('name', request('name'))
            ],

        ]);

        $contact = new LRcategory;
        $contact->name = $request->name;
        $contact->parent_id = $request->parent_id;
        $contact->created_by = Auth::user()->id;
        $contact->created_at = date('Y-m-d H:i:s');

        $contact->save();

        return response()->json(['success' => 'Successfully']);

    }



    // https://stackoverflow.com/questions/52941826/how-to-save-multiple-row-forms-in-laravel-using-ajax
    // Create Ticket
    public function submitCreateTickets(Request $request)
    {
        $request->validate([
            'address_id' => 'required',
        ]);



        $assigned_by = User::where('id', Auth::user()->id)->first()->name ?? 'NA';



        $rules = [];
        $messages = [];
        if ($request->has('product') && is_array($request->input('product'))) {
            foreach ($request->input('product') as $key => $value) {
                // $rules["address_id.{$key}"] = 'required';
                $rules["case.{$key}"] = 'required';
                $rules["product.{$key}"] = 'required';
                $rules["serial.{$key}"] = 'required';
                // $rules["pr_desc.{$key}"] = 'required';
                
                $row = $key + 1;
                $messages["case.{$key}.required"] = "Case is required for row {$row}.";
                $messages["product.{$key}.required"] = "Model/Product is required for row {$row}.";
                $messages["serial.{$key}.required"] = "Serial is required for row {$row}.";
            }
        }
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->passes() && $request->has('product') && is_array($request->input('product'))) {
            foreach ($request->input('product') as $key => $value) {

                $categoryid = $request->get('product')[$key];

                $categories = LRcategory::from('LRcategory as c1')
                    ->select('c1.name as model', 'c2.name as category')
                    ->leftJoin('LRcategory as c2', 'c1.parent_id', '=', 'c2.id')
                    ->where('c1.id', $categoryid)
                    ->get();

                $category_name = $categories[0]->category;
                $model_name = $categories[0]->model;

                $Record = new LRtickets;
                $Record->case = $request->get('case')[$key];
                $Record->vendorid = $request->address_id;
                $Record->categoryid = $request->get('product')[$key];
                $Record->category = $category_name;
                $Record->model = $model_name;
                $Record->created_by = Auth::user()->id;
                $Record->created_at = date('Y-m-d H:i:s');
                $Record->serial = $request->get('serial')[$key];
                $Record->note = $request->get('pr_desc')[$key];
                $Record->status = 1;
                $Record->save();

                $TicketLog = new LRticketsAssignLog;
                $TicketLog->userid = Auth::user()->id;
                $TicketLog->ticketid = $Record->id;
                $TicketLog->priority = 1;
                $TicketLog->active = 1;
                $TicketLog->status = 1;
                $TicketLog->assigned_by = $assigned_by;
                $TicketLog->assigned_at = date('Y-m-d H:i:s');
                $TicketLog->created_by = Auth::user()->id;
                $TicketLog->created_at = date('Y-m-d H:i:s');
                $TicketLog->save();

                if ($request->assignUserStatus == "AssignUserEnable") {
                    $empdata = LRticketsAssignLog::where('ticketid', $Record->id)->where('status', 1)->where('active', 1);
                    $data = [
                                "priority"             => 1,
                                "updated_by"           => Auth::user()->id,
                                "updated_at"           =>  date('Y-m-d H:i:s'),
                                "active"               => 0,
                            ];
                    $empdata->update($data);
                    

                    $TicketLog = new LRticketsAssignLog;
                    $TicketLog->userid = $request->assignUserId;
                    $TicketLog->ticketid = $Record->id;
                    $TicketLog->priority = 1;
                    $TicketLog->active = 1;
                    $TicketLog->status = 3;
                    $TicketLog->assigned_by = $assigned_by;
                    $TicketLog->assigned_at = date('Y-m-d H:i:s');
                    $TicketLog->created_by = Auth::user()->id;
                    $TicketLog->created_at = date('Y-m-d H:i:s');
                    $TicketLog->save();

                    $UpdateDetails = LRtickets::where('id', '=', $Record->id)->first();
                    $UpdateDetails->status = 3;
                    $UpdateDetails->save();

                }
            }
            return response()->json(['success' => 'done']);
        } else {
            return response()->json(['error' => $validator->errors()->all()]);
        }
    }

    // Delete Employee
    public function deleteUserList(Request $request)
    {
        ## Read POST data
        $id = $request->post('id');

        // $empdata = User::find($id);
        $users = User::findOrFail($id);

        // replicate (duplicate) the data
        $users_deleted = $users->replicate();
        $users_deleted->pid = $users->id;

        // make into array for mass assign. 
        //make sure you activate $guarded in your Staff model
        $users_deleted = $users_deleted->toArray();

        users_deleted::firstOrCreate($users_deleted);

        if ($users->delete()) {
            $response['success'] = 1;
            $response['msg'] = 'Delete successfully';
        } else {
            $response['success'] = 0;
            $response['msg'] = 'Invalid ID.';
        }
        return response()->json($response);
    }

    public function getVendorInfo(Request $request)
            {
                    ## Read POST data 
                    $id = $request->post('vendorid');

                    if($id == 100){
                        $empdata = LRvendor::all();
                    } else {
                        $empdata = LRvendor::find($id);
                    }

                    $response = array();
                    if (!empty($empdata)) {
                        $created_by_name = null;
                        if (!empty($empdata->created_by)) {
                            $created_by_name = User::where('id', $empdata->created_by)->first()->name ?? null;
                        }
                        $updated_by_name = null;
                        if (!empty($empdata->updated_by)) {
                            $updated_by_name = User::where('id', $empdata->updated_by)->first()->name ?? null;
                        }

                        $response['id'] = $empdata->id;
                        $response['name'] = $empdata->name;
                        $response['companyname'] = $empdata->companyname;
                        $response['address'] = $empdata->address;
                        $response['mobile1'] = $empdata->mobile1;
                        $response['mobile2'] = $empdata->mobile2;
                        $response['city'] = $empdata->city;
                        $response['district'] = $empdata->district;
                        $response['state'] = $empdata->state;
                        $response['pincode'] = $empdata->pincode;
                        $response['gstno'] = $empdata->gstno;
                        $response['created_by'] = $created_by_name;
                        $response['updated_by'] = $updated_by_name;
                        $response['created_at'] = $empdata->created_at ? \Carbon\Carbon::parse($empdata->created_at)->timezone('Asia/Kolkata')->format('d-M-Y h:i A') : null;
                        $response['updated_at'] = $empdata->updated_at ? \Carbon\Carbon::parse($empdata->updated_at)->timezone('Asia/Kolkata')->format('d-M-Y h:i A') : null;

                        $response['success'] = 1;
                    } else {
                        $response['success'] = 0;
                    }

                    return response()->json($response);
            }





    public function validateSerial(Request $request)
    {
        $serial = $request->input('serial');
        $modalId = $request->input('modal');

        if (is_null($modalId) || is_null($serial)) {

            $returnData = array(
                'error' => true,
                'message' => 'Record not found'
            );
            return response()->json($returnData, "404");

        } else {
            $dataQuery = DB::table('LRtickets')->where('categoryid', $modalId)->where('serial', $serial);
            $exists = $dataQuery->exists();

            if ($exists) {

                $data = $dataQuery->get();
                $ticket = $data[0]->id;
                $status = $data[0]->status;
                $delivery_status = $data[0]->delivery_status;

                if ($status == 1) {
                    return response()->json(['valid' => true, 'ticket' => 'The device already at unassigned, Ticket ID : ' . $ticket]);
                }  
                if ($status == 4) {
                    return response()->json(['valid' => true, 'ticket' => 'The device already at QC, Ticket ID : ' . $ticket]);
                }
                if ($status == 3) {
                    return response()->json(['valid' => true, 'ticket' => 'The device already at assigned. Ticket ID : ' . $ticket]);
                }
                if ($status == 2 && $delivery_status == 1) {
                    return response()->json(['valid' => true, 'ticket' => 'The device is in delivery pending state. Ticket ID : ' . $ticket]);
                }
                if ($status == 2 && $delivery_status == 2) {
                    return response()->json(['valid' => false, 'ticket' => 'The device recently delivered. Ticket ID : ' . $ticket]);
                }
            } else {
                return response()->json(['valid' => false, 'ticket' => 'The serial number is new, continue...']);
            }
        }
    }


}