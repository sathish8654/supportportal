<?php

namespace App\Http\Controllers;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LRvendor;
use App\Models\LRparts;
use App\Models\imei_registration;
use DataTables;
use Illuminate\Support\Facades\Auth;
use Validator;

class PartsController extends Controller
{

    // session expire to redirect to login page
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $parts = LRparts::select('*')->where('parent_id',0)->orderBy('name', 'ASC')->get(); 
        return view('parts',["parts" => $parts]);
    }




    // get table data

    public function getpartsListData()
    {
        
    $parts = LRparts::select('*')->where('parent_id',0);
        
        return Datatables::of($parts)
            ->addIndexColumn()
           
            ->addColumn('status', function ($row) {
                if ($row->status == '1') {
                    return "Active";
                } else {
                    return "Inactive";
                }
            })
            ->addColumn('action', function ($row) {

                $iconClass = $row->status == 1 ? 'fa fa-eye' : 'fa fa-eye-slash';
                $iconNote = $row->status == 1 ? 'To Disable' : 'To Enable';

                $user = Auth::user();
                $action = '';

                // Check Edit Permission
                if ($user->hasMenuPermission('Parts', 'parts', 'edit')) {
                    $action .= "<button class='fa fa-edit getPartInfo me-1' data-id='" . $row->id . "' data-bs-toggle='modal' data-bs-target='#addNewPartModal' title='Edit'></button>";
                } 
                // Check View Permission (if no Edit)
                elseif ($user->hasMenuPermission('Parts', 'parts', 'view')) {
                    $action .= "<button class='fa fa-eye viewPart me-1' data-id='" . $row->id . "' data-bs-toggle='modal' data-bs-target='#addNewPartModal' data-view-only='1' title='View'></button>";
                }

                // Check Delete Permission
                if ($user->hasMenuPermission('Parts', 'parts', 'delete')) {
                    $action .= "<button class='$iconClass disablePart' data-id='" . $row->id . "' title='" . $iconNote . "'></button>";
                }

                return $action;
            })
            
   ->make();
    
    }
/////////////////////// for parts sub list data
    public function getpartsSubListData()
    {
        $partsQuery = LRparts::where('parent_id', '!=', 0);
    
        $datatable = Datatables::of($partsQuery)
            ->addIndexColumn()
    
            // statuc column
            ->addColumn('status', function ($row) {
                return $row->status == '1' ? 'Active' : 'Inactive';
            })
            ->filterColumn('status', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('status', 'like', $keyword == 'Active' ? '1' : '0');
                });
            })
    
            // colunm name
            ->addColumn('action', function ($row) {
                $iconClass = $row->status == 1 ? 'fa fa-eye' : 'fa fa-eye-slash';
                $iconNote = $row->status == 1 ? 'To Disable' : 'To Enable';

                $user = Auth::user();
                $action = '';

                // Check Edit Permission
                if ($user->hasMenuPermission('Parts', 'parts', 'edit')) {
                    $action .= "<button class='fa fa-edit getPartSubInfo me-1' data-id='" . $row->id . "' data-parent_id='" . $row->parent_id. "' data-name='" . $row->name . "' data-part_value='" . $row->part_value . "' data-bs-toggle='modal' data-bs-target='#addNewPartSubModal' title='Edit'></button>";
                } 
                // Check View Permission (if no Edit)
                elseif ($user->hasMenuPermission('Parts', 'parts', 'view')) {
                    $action .= "<button class='fa fa-eye viewPartSub me-1' data-id='" . $row->id . "' data-parent_id='" . $row->parent_id. "' data-name='" . $row->name . "' data-part_value='" . $row->part_value . "' data-bs-toggle='modal' data-bs-target='#addNewPartSubModal' data-view-only='1' title='View'></button>";
                }

                // Check Delete/Disable Permission
                if ($user->hasMenuPermission('Parts', 'parts', 'delete')) {
                    $action .= "<button class='$iconClass disablePartSub' data-id='" . $row->id . "' title='" . $iconNote . "'></button>";
                }

                return $action;
            });
    
          /////////////////////////////////////////////////////////////
            $partsData = LRparts::all(); // Only fetched once

            // parent sub name
            $datatable->addColumn('name', function ($row) use ($partsData) {
                $matchingPart = $partsData->where('id', $row->parent_id)->first();
                return $matchingPart ? $matchingPart->name : '-';
            });

            $datatable->filterColumn('name', function ($query, $keyword) use ($partsData) {
                $matchingIds = $partsData->filter(function ($item) use ($keyword) {
                    return stripos($item->name, $keyword) !== false;
                })->pluck('id')->toArray();

                $query->whereIn('parent_id', $matchingIds);
            });

            // part sub name
            $datatable->addColumn('partSubname', function ($row) {
                return $row->parent_id != 0 ? $row->name : '';
            });

            ///////////////////// sorting for custom columns
            $datatable->orderColumn('status', function ($query, $order) {
                $query->orderBy('status', $order);
            });

            $datatable->orderColumn('name', function ($query, $order) {
                $query->orderBy('parent_id', $order); // sorting by parent_id
            });

            $datatable->orderColumn('partSubname', function ($query, $order) {
                $query->orderBy('name', $order); // sorting by part name
            });

            // filter for sub part name
            $datatable->filterColumn('partSubname', function ($query, $keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            });

            
            return $datatable->rawColumns(['action'])->make(true);
    }
    

    // Add New Parent function
    public function addNewPart(Request $request)
    {
        $request->validate([
            'name' => ['required', Rule::unique('LRparts')->where(function ($query) {
                            return $query->where('parent_id', 0);
                        })],
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
                'Subname' => [
                    'required',
                    Rule::unique('LRparts', 'name')
                        ->where(function ($query) use ($request) {
                            return $query->where('parent_id', $request->partIds);
                        }),
                ],
               ]);

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

        $request->validate([
            'name' => [
                'required',
                Rule::unique('LRparts', 'name')
                    ->ignore($id) // exclude current record
                    ->where(function ($query) use ($request) {
                        return $query->where('parent_id',0);
                    }),
            ],
           ]);


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


    
    // Update Employee record
    public function updatePartInfoSub(Request $request)
    {
        
        ## Read POST data
        $id = $request->post('id');

        $request->validate([
            'name' => [
                'required',
                Rule::unique('LRparts', 'name')
                    ->ignore($id) // exclude current record
                    ->where(function ($query) use ($request) {
                        return $query->where('parent_id',$request->parent_id);
                    }),
            ],
           ]);


        $empdata = LRparts::find($id);
        $response = array();
        if (!empty($empdata)) {
            $updata['name'] = $request->post('name');
            $updata['part_value'] = $request->post('partValue');
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

    #Search autocomplete
    public function autocompleteCity(Request $request)
    {
        $data = LRvendor::select("city as value", "id")
            ->where('city', 'LIKE', '%' . $request->get('search') . '%')
            ->groupBy('city')
            ->get();
        return response()->json($data);
    }

    public function autocompleteDistrict(Request $request)
    {
        $data = LRvendor::select("district as value", "id")
            ->where('district', 'LIKE', '%' . $request->get('search') . '%')
            ->groupBy('district')
            ->get();
        return response()->json($data);
    }

    public function autocompleteState(Request $request)
    {
        $data = LRvendor::select("state as value", "id")
            ->where('state', 'LIKE', '%' . $request->get('search') . '%')
            ->groupBy('state')
            ->get();
        return response()->json($data);
    }
}