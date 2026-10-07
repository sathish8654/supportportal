<?php
namespace App\Http\Controllers;

use App\Models\LRcase;
use App\Models\LRcategory;
use App\Models\LRtickets;
use App\Models\LRticketsAssignLog;
use App\Models\LRvendor;
use App\Models\User;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TicketAssignController extends Controller
{

    // session expire to redirect to login page
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function viewTickets()
    {
        $users        = User::whereIn('role_id', ['2', '3'])->orderBy("name", "asc")->get();
        $categoris    = LRcategory::where('parent_id', 0)->get();
        $cases        = LRcase::where('status', 1)->get();
        $mobileNumber = LRvendor::select('mobile1', 'id')->get();

        return view('viewTickets', ["users" => $users, "cases" => $cases, "categoris" => $categoris, "mobileNumber" => $mobileNumber]);
    }

    public function getTicketsListData()
    {

        $employees = LRtickets::select(
            'LRtickets.*',
            'users.name as user_name',
            'LRvendor.name as LRvendor_name',
            'LRvendor.companyname as LRvendor_companyname',
            // 'LRcategory.name as LRvendor_categorysub2name',
            // 'LRcategory.id as LRvendor_categorysub2nameid',
            'LRcase.name as case'
        )
            ->leftJoin('users', 'users.id', '=', 'LRtickets.created_by')
            ->leftJoin('LRvendor', 'LRvendor.id', '=', 'LRtickets.vendorid')
        // ->leftJoin('LRcategory', 'LRcategory.id', '=', 'LRtickets.categoryid')
            ->leftJoin('LRcase', 'LRcase.id', '=', 'LRtickets.case')
        // ->whereNot('LRtickets.status', 3)
            ->where('LRtickets.status', 1)
            ->get();
// dd($employees);
        return Datatables::of($employees)

            ->addIndexColumn()

            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d M, Y H:i:s');
            })

            ->addColumn('status', function ($row) {

                if ($row->status == 1) {
                    return "Open";
                } elseif ($row->status == 2) {
                    return "Closed";
                } elseif ($row->status == 3) {
                    return "Assigned";
                } else {
                    return "None";
                }
            })

            ->addColumn('action', function ($row) {

                if ($row->status == 3 || $row->status == 2) {
                    return "Assigned";
                }

                $user = Auth::user();
                $action = '';

                // Check Edit Permission
                if ($user->hasMenuPermission('Tickets', 'viewTickets', 'edit')) {
                    $action .= "<button class='fa fa-edit updateUser me-1' data-id='" . $row->id . "' data-bs-toggle='modal' data-bs-target='#ticketUpdateModal' title='Edit'></button>";
                } 
                // Check View Permission (if no Edit)
                elseif ($user->hasMenuPermission('Tickets', 'viewTickets', 'view')) {
                    $action .= "<button class='fa fa-eye viewTicket me-1' data-id='" . $row->id . "' data-bs-toggle='modal' data-bs-target='#ticketUpdateModal' data-view-only='1' title='View'></button>";
                }

                // Check Delete Permission
                if ($user->hasMenuPermission('Tickets', 'viewTickets', 'delete')) {
                    $userPermission = DB::table('user_permission')->where('user_id', $user->id)->first();
                    $isCreator = ($row->created_by == $user->id);
                    $isSuper = ($user->current_team_id == 1);
                    $hasSpecialDelete = ($userPermission && $userPermission->all_special_delete == 1);

                    if ($row->status == 1 && ($isCreator || $isSuper || $hasSpecialDelete)) {
                        $action .= " <button class='fa fa-trash-o deleteUser' data-id='" . $row->id . "' title='Delete'></button>";
                    }
                }

                return $action;
            })
            ->make();
    }

    // Read Employee record by ID
    public function assignTicket(Request $request)
    {
        ## Read POST data
        $id = $request->post('id');

        $empdata      = LRtickets::find($id);
        $vendordata   = LRvendor::find($empdata->vendorid);
        $categorydata = LRcategory::find($empdata->categoryid);
        $productdata  = LRcategory::find($categorydata->parent_id);
        $casedata     = LRcase::find($empdata->case);

        $response = [];
        if (! empty($empdata)) {

            $response['id']               = $empdata->id;
            $response['vendorid']         = $empdata->vendorid;
            $response['case']             = $casedata->id;
            $response['categoryid']       = $empdata->categoryid;
            $response['serial']           = $empdata->serial;
            $response['note']             = $empdata->note;
            $response['created_by']       = $empdata->created_by;
            $response['created_at']       = $empdata->created_at;
            $response['vendorName']       = $vendordata->name;
            $response['vendorCompany']    = $vendordata->companyname;
            $response['categoryParentId'] = $categorydata->parent_id;
            $response['productName']      = $productdata->name;

            $response['success'] = 1;
        } else {
            $response['success'] = 0;
        }

        return response()->json($response);
    }

    // Update Employee record
    public function assignTicketSubmit(Request $request)
    {

        $userid     = $request->input('userid');
        $note       = $request->input('note');
        $ticketid   = $request->input('ticketid');
        $priority   = $request->input('priority');
        $categoryid = $request->input('categoryid');
        $serial     = $request->input('serial');
        $case       = $request->input('case');

        $created_by = Auth::user()->id;
        $created_at = date('Y-m-d H:i:s');
        ///////////01-07-2025
        $assigned_by = "NA";
        if($userid){
            $assigned_by = User::where('id', $userid)->first()->name ?? 'NA';
        } 
        //////////
        $data = [
            'userid'               => $userid,
            "ticketid"             => $ticketid,
            "priority"             => $priority,
            "created_by"           => $created_by,
            "created_at"           => $created_at,
            "assigned_by"          => $assigned_by,
            "assigned_at"          => $created_at,
            "active"               => 1,
            "status"               => 3
        ];

        ## Read and update ticket data
        $ticketId           = $request->input('ticketid');
        $updateTicketStatus = LRtickets::find($ticketId);

        $updateTicketData = [
            "categoryid" => $categoryid,
            "serial"     => $serial,
            "case"       => $case,
            "note"       => $note,
            "updated_by" => $created_by,
            "updated_at" => $created_at,
        ];
        $updateTicketStatus->update($updateTicketData);

        if ($data) {

            $ticketDataUpdate = LRticketsAssignLog::where('ticketid', $ticketId)->where('status', 1)->where('active', 1);
            $dataUpdate = [
                        "priority"             => 1,
                        "updated_by"           => Auth::user()->id,
                        "updated_at"           => date('Y-m-d H:i:s'),
                        "active"               => 0,
                    ];
            $ticketDataUpdate->update($dataUpdate);

            DB::table('LRticketsAssignLog')->insert($data);
            LRtickets::where('id', $ticketid)
                ->update([
                    'status' => 3,
                    'note'   => $note,
                ]);
            return response()->json(['success' => 'done']);
        } else {
            return response()->json(['error' => 'error']);
        }
    }

    // Delete Employee
    public function assignTicketDelete(Request $request)
    {
        //# Read POST data
        $id           = $request->post('id');
        $deleteTicket = LRtickets::findOrFail($id);
        $deleteTicketlog = LRticketsAssignLog::where('ticketid', $id)->delete();

        if ($deleteTicket->delete()) {
            return response()->json(['message' => 'Deleted Successfully!']);
        } else {
            return response()->json(['message' => 'Failed! Please check!']);
        }
    }

}
