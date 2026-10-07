<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\users_deleted;
use App\Models\imei_registration;
use App\Models\LRcase;
use App\Models\LRparts;
use App\Models\LRcategory;
use App\Models\LRvendor;
use App\Models\LRtickets;
use App\Models\LRticketsAssignLog;
use DataTables;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Validation\Rule;

class TicketQcController extends Controller
{

    // session expire to redirect to login page
    public function __construct()
    {
        $this->middleware('auth');
    }

           
    public function qcTickets()
    {
        $users = User::whereIn('role_id', ['2','3'])->orderBy("name", "asc")->get();
        $parts = LRparts::where('status', 1)->where('parent_id', 0)->orderBy('name', 'asc')->get();
        return view('qcTickets', ["users" => $users,"parts" => $parts]);
    }



    
    public function getqcTicketsListData()
    {

            $employees = DB::table('LRticketsAssignLog')
                                                ->select(
                                                                'LRticketsAssignLog.*',
                                                                'users.name as user_name',
                                                                'LRvendor.name as LRvendor_name',
                                                                'LRvendor.companyname as LRvendor_companyname',
                                                                // 'LRcategory.name as LRvendor_categorysub2name',
                                                                // 'LRcategory.id as LRvendor_categorysub2id',
                                                                'LRcase.name as case',
                                                                'LRtickets.note as LRtickets_note',
                                                                'LRtickets.serial as LRtickets_serial',
                                                                'LRtickets.category as LRtickets_category',
                                                                'LRtickets.model as LRtickets_model',
                                                            )
                                                ->leftJoin('LRtickets', 'LRticketsAssignLog.ticketid', '=', 'LRtickets.id')
                                                ->leftJoin('users', 'users.id', '=', 'LRticketsAssignLog.created_by')
                                                ->leftJoin('LRvendor', 'LRvendor.id', '=', 'LRtickets.vendorid')
                                                // ->leftJoin('LRcategory', 'LRcategory.id', '=', 'LRtickets.categoryid')
                                                ->leftJoin('LRcase', 'LRcase.id', '=', 'LRtickets.case')
                                                ->where('LRticketsAssignLog.status', 4)
                                                ->orderBy('LRticketsAssignLog.id','desc') 
                                                ->where('LRticketsAssignLog.active', 1)
                                                ->get();

            // dd($employees);

            if (Auth::check() && Auth::user()->only_selfview == 1) {
                $employees =  $employees->where('userid', Auth::user()->id);
            }                                 

            
            $employeesCount =  $employees;
            $countE = $employeesCount->count();
        return Datatables::of($employees)

            ->addIndexColumn()

            // ->editColumn('created_at', function ($row) {
            //     return $row->created_at->format('d M, Y H:i:s');
            // })


            // ->addColumn('LRvendor_categoryname', function ($row) {

            //     $c2id=$row->LRvendor_categorysub2id;

            //     // dd($c2id);

            //     $parent_id = LRcategory::get()->where('id',$c2id);

            //     foreach($parent_id as $mid){ //this loop will continue for each instance of the club
            //         $mainid = $mid->parent_id;   //this will return a specific id of a club
            //         $parent_id_main = LRcategory::where('id',$mainid)->value('parent_id'); //this will return all the events of that particular club
            //         $categoryname = LRcategory::where('id',$parent_id_main)->value('name'); //this will return all the events of that particular club
            //     }
              
            //     return $categoryname;
            // })

            // ->addColumn('LRvendor_categorysub1name', function ($row) {
                
            //     $c2id=$row->LRvendor_categorysub2id;
                
            //     $parent_id = LRcategory::get()->where('id',$c2id);

            //     foreach($parent_id as $mid){ //this loop will continue for each instance of the club
            //         $mainid = $mid->parent_id;   //this will return a specific id of a club
            //         $categorysub1name = LRcategory::where('id',$mainid)->value('name'); //this will return all the events of that particular club
            //     }
              
            //     return $categorysub1name;
            // })

            ->addColumn('updated_by', function ($row) {
                $updateBy = $row->updated_by;
                    if($updateBy == null){
                        $username = "NA";
                    } else{
                        $username = User::where('id', $row->updated_by)->first()->name;
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

            ->addColumn('priority', function ($row) {

                if ($row->priority == 1) {
                    return "High";
                } elseif ($row->priority == 2) {
                    return "Low";
                } elseif ($row->priority == 3) {
                    return "Medium";
                } else {
                    return "NA";
                }
                   
            })

            // ->addColumn('case', function ($row) {

            //     if ($row->case == 1) {
            //         return "Service";
            //     } elseif ($row->case == 2) {
            //         return "Replacement";
            //     } else {
            //         return "None";
            //     }
            // })

            ->addColumn('status', function ($row) {

                if ($row->status == 1) {
                    return "Open";
                } elseif ($row->status == 2) {
                    return "Closed";
                } elseif ($row->status == 3) {
                    return "Assigned";
                } elseif ($row->status == 4) {
                    return "QC";
                }else {
                    return "None";
                }
            })


            ->addColumn('action', function ($row) {
                if ($row->status == 2) {
                    return "Closed";
                }

                $user = Auth::user();
                $action = '';

                // Check Edit Permission
                if ($user->hasMenuPermission('Tickets', 'qcTickets', 'edit')) {
                    $action .= "<button class='fa fa-edit updateUser me-1' data-id='" . $row->id . "' data-bs-toggle='modal' data-bs-target='#ticketUpdateModal' title='Edit'></button>";
                } 
                // Check View Permission (if no Edit)
                elseif ($user->hasMenuPermission('Tickets', 'qcTickets', 'view')) {
                    $action .= "<button class='fa fa-eye viewTicket me-1' data-id='" . $row->id . "' data-bs-toggle='modal' data-bs-target='#ticketUpdateModal' data-view-only='1' title='View'></button>";
                }

                // Check Delete Permission
                if ($user->hasMenuPermission('Tickets', 'qcTickets', 'delete')) {
                    if ($row->status == 1 && $row->created_by == $user->id) {
                        $action .= " <button class='fa fa-trash-o deleteUser' data-id='" . $row->id . "' title='Delete'></button>";
                    }
                }

                return $action;
            })
            ->make();
    }

    
          // Read Employee record by ID
          public function qcAssignTicket(Request $request)
          {
              ## Read POST data 
              $id = $request->post('id');
            $empdata = DB::table('LRticketsAssignLog')->find($id);
            $LRtickets = DB::table('LRtickets')->find($empdata->ticketid);
            // ->where('active', 1)
            $parts = LRparts::where('status', 1)->orWhere('status',2)->orderBy('name', 'asc')->get();
              $response = array();
              if (!empty($empdata)) {
      
                  $response['id'] = $empdata->id;
                  $response['ticketid'] = $empdata->ticketid;
                  $response['note'] = $LRtickets->note;
                  $response['solution'] = $empdata->solution;
                  $response['cost'] = $empdata->cost;
                  $response['userid'] = $empdata->userid;
                  $response['serial'] = $LRtickets->serial;
                  $response['priority'] = $empdata->priority;
                  $response['status'] = $empdata->status;
      
                  $response['success'] = 1;
              } else {
                  $response['success'] = 0;
              }
      
              $html='';
              $partsRep = DB::table('LRparts_replaced')
              ->select(
                              'LRparts_replaced.*',
                              'LRparts.name as part_name',
                              
                          )
              ->leftJoin('LRparts', 'LRparts.id', '=', 'LRparts_replaced.part_id')
              
              ->where('LRparts_replaced.ticket_id', $empdata->ticketid)
              ->get();

              $i = 1;
                foreach ($partsRep as $key => $value) {

                $html .= '<tr>';
                $html .= '<td><select class="form-control browser-default custom-select partId partCategory" data-id='.$i.' name="part[]" id="part" style="width: 100% !important" required>';
                $html .= '<option value="" selected>Select</option>';
            
                foreach ($parts as $item) {
                    $selected = ($item->id == $value->part_id) ? 'selected' : ''; // Correct PHP comparison
                    if ($item->parent_id==0) {
                    $html .= '<option value="' . $item->id . '" ' . $selected . '>' . htmlspecialchars($item->name) . '</option>';
                    }
                }
            
                $html .= '</select>';
                $html .= '</td>';
                $html .= '<td><select class="form-control browser-default custom-select partSub partSubAppend'.$i.'" name="partSub[]" style="width: 100% !important" required>';
                $html .= '<option value="" selected>Select</option>';

                foreach ($parts as $item) {
                    $selected = ($item->id == $value->part_number_id) ? 'selected' : ''; // Correct PHP comparison
                    if ($item->parent_id == $value->part_id) {
                        $html .= '<option value="' . $item->id . '" ' . $selected . '>' . htmlspecialchars($item->name) . '</option>';
                    }
                    // $html .= '<option value="' . $item->id . '" ' . $selected . '>' . htmlspecialchars($item->name) . '</option>';
                }

                $html .= '</select>';

                // $html .= '<input type="text" class="form-control partSub" name="partSub[]" onkeypress="return event.charCode >= 48 && event.charCode <= 57" value='.$value->part_value.' required/>';
                $html .= '</td>';
                $html .= '<td><button type="button" class="btn btn-danger remove"><i class="fa fa-remove"></i></button></td>';
                $html .= '</tr>';

                $i++;
            }
            
            
        //  dd($html);
              return response()->json([$response,$html]);
          }


   // Update Employee record
   public function qcTicketSubmit(Request $request)
   {


    $id = $request->input('id');
    $ticketId = $request->input('ticketid');
    $userid = $request->input('userid');
    $priority = $request->input('priority');
    $mode = $request->input('mode');
    $solution = $request->input('solution');
    $cost = $request->input('cost');
    $status = $request->input('status');
    $updated_by = Auth::user()->id;
    $updated_at = date('Y-m-d H:i:s');



    $dataValidate = LRticketsAssignLog::where('id', $id)->where('ticketid', $ticketId)->where('status', $status)->where('active', 1)->count();
    $empdata = LRticketsAssignLog::find($id);

    if ($dataValidate) {
            $data=array('userid'=>$userid,
                        "id"=>$id,
                        "priority"=>$priority,
                        "updated_by"=>$updated_by,
                        "updated_at"=>$updated_at,
                        "solution"=>$solution,
                        "cost"=>$cost,
                        "active"=>1,
                    );

            $empdata->update($data);

        }else {

            $data=array('userid'=>$userid,
                        "id"=>$id,
                        "priority"=>$priority,
                        "updated_by"=>$updated_by,
                        "updated_at"=>$updated_at,
                        "solution"=>$solution,
                        "cost"=>$cost,
                        "active"=>0,
                    );

            $empdata->update($data);
            ///////////01-07-2025
            $assigned_by = "NA";
            if($userid){
                $assigned_by = User::where('id', $userid)->first()->name ?? 'NA';
            } 
            //////////
            $dataInsert=array('userid'=>$userid,
            "ticketid"=>$ticketId,
            "priority"=>$priority,
            "created_by"=>$updated_by,
            "created_at"=>$updated_at,
            "updated_by"=>$updated_by,
            "updated_at"=>$updated_at,
            "assigned_by"=> $assigned_by,
            "assigned_at"=> $updated_at,
            "solution"=>$solution,
            "cost"=>$cost,
            "status"=>$status,
            "active"=>1,
        );
        
        DB::table('LRticketsAssignLog')->insert($dataInsert);
        }


        if($status == "5"){
            $status = 3;
        }
        
        ## Read and update ticket data 
        $updateTicketStatus = LRtickets::find($ticketId);

        $updateTicketData=array(
            "status"=>$status,
            "solution"=>$solution,
            "cost"=>$cost,
            "updated_by"=>$updated_by,
            "updated_at"=>$updated_at,
        );
        
        $updateTicket = $updateTicketStatus->update($updateTicketData);

        if ($updateTicket) {
            ////////////////////////////////////23-04-2025
    
            $partReplace = [];
                    if ($request->mode!=2) {/////////////////// Yes: data insertion
                    
                    DB::table('LRparts_replaced')->where('ticket_id', $ticketId)->delete();
                    // exit;
                    for ($i=0; $i <count($request->input('partId')) ; $i++){
                        $matchingPart = LRparts::where('id', $request->input('partId')[$i])->first(['id', 'name']); // Fetch only necessary fields
                        $matchingPartSub = LRparts::where('id', $request->input('partSub')[$i])->first(['id', 'name','part_value']); // Fetch only necessary fields
                        $partReplace[] = array(
                            'part_id' => $request->input('partId')[$i],
                            'part_name' => $matchingPart ? $matchingPart->name : null, // Avoid error if no record is found
                            'part_number_id' => $request->input('partSub')[$i],
                            'part_number' => $matchingPartSub ? $matchingPartSub->name : null, // Avoid error if no record is found
                            'part_value' => $matchingPartSub ? $matchingPartSub->part_value : null, // Avoid error if no record is found
                            "ticket_id"=>$ticketId,
                            "mode"=>$mode,
                            "created_by"=>$updated_by,
                            "created_at"=>$updated_at,
                        );
                    }
                    // dd($partReplace);exit;
                    DB::table('LRparts_replaced')->insert($partReplace);
                }else{  // No: data deletion
                    DB::table('LRparts_replaced')->where('ticket_id', $ticketId)->delete();  
                }
            //////////////////////////////////////////////////

            
            $response['success'] = 1;
            $response['msg'] = 'Update successfully';

        } else {
            $response['success'] = 0;
            $response['msg'] = 'Record not updated';
        }
  
    return response()->json($response);
}


  // Delete Employee
  public function deleteTicketSubmit(Request $request)
  {

    // dd($request);

    //# Read POST data
    $id = $request->post('id');
    $deleteTicketAssignLog = LRticketsAssignLog::findOrFail($id);

    $findTicketId = DB::table('LRticketsAssignLog')->find($id);
    $ticketId = $findTicketId->ticketid;

    $updateTicketStatus = LRtickets::find($ticketId);
    $updated_by = Auth::user()->id;
    $updated_at = date('Y-m-d H:i:s');

    $updateTicketData=array(
        "status"=>1,
        "updated_by"=>$updated_by,
        "updated_at"=>$updated_at,
    );

      if ($deleteTicketAssignLog->delete()) {
            $updateTicketStatus->update($updateTicketData); 
          return response()->json(['message' => 'Deleted Successfully!']);
      } else {
          return response()->json(['message' => 'Failed! Please check!']);
      }
  }
}
