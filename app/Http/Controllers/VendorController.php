<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LRvendor;
use App\Models\imei_registration;
use DataTables;
use Illuminate\Support\Facades\Auth;
use Validator;

class VendorController extends Controller
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
        return view('vendor');
    }




    // get table data

    public function getVendorsListData()
    {
        $vendors = LRvendor::select('*')->orderBy('name', 'ASC');;

        return Datatables::of($vendors)
            ->addIndexColumn()
            ->addColumn('status', function ($row) {

                if ($row->status == '1') {
                    return "Active";
                } else {
                    return "Inactive";
                }
            })
            ->addColumn('action', function ($row) {

                $iconClass = $row->status == 1 ? 'fa fa-times' : 'fa fa-undo';
                $iconNote = $row->status == 1 ? 'To Disable' : 'To Enable';
                $btnColor = $row->status == 1 ? 'btn-outline-danger' : 'btn-outline-warning';

                $user = Auth::user();
                $action = '';

                // View Permission
                if ($user->hasMenuPermission('Manage', 'vendor', 'view') || $user->hasMenuPermission('Manage', 'vendor', 'edit')) {
                    $action .= "<button class='btn btn-sm btn-outline-info viewVendor me-1' data-id='" . $row->id . "' data-bs-toggle='modal' data-bs-target='#addNewAddressModal' data-view-only='1' title='View'><i class='fa fa-eye'></i></button>";
                }

                // Edit Permission
                if ($user->hasMenuPermission('Manage', 'vendor', 'edit')) {
                    $action .= "<button class='btn btn-sm btn-outline-primary getAddress me-1' data-id='" . $row->id . "' data-bs-toggle='modal' data-bs-target='#addNewAddressModal' title='Edit'><i class='fa fa-edit'></i></button>";
                }

                // Delete/Disable Permission
                if ($user->hasMenuPermission('Manage', 'vendor', 'delete')) {
                    $action .= "<button class='btn btn-sm " . $btnColor . " deleteVendorAdd' data-id='" . $row->id . "' title='" . $iconNote . "'><i class='" . $iconClass . "'></i></button>";
                }

                return $action;
            })
            ->make();
    }



    // Add New Parent function
    public function addNewVendor(Request $request)
    {
        $request->validate([
            'mobile1' => 'unique:LRvendor,mobile1',
        ]);

        $contact = new LRvendor;
        $contact->name = $request->name;
        $contact->companyname = $request->companyname;
        $contact->mobile1 = $request->mobile1;
        $contact->mobile2 = $request->mobile2;
        $contact->address = $request->address;
        $contact->city = $request->city;
        $contact->district = $request->district;
        $contact->state = $request->state;
        $contact->pincode = $request->pincode;
        $contact->gstno = $request->gstno;
        $contact->created_by = Auth::user()->id;
        $contact->created_at = date('Y-m-d H:i:s');

        $contact->save();

        return response()->json(['message' => 'Address created successfully!']);
    }

    // Read Vendor address record by ID
    public function getVendorAddressInfo(Request $request)
    {

        ## Read POST data 
        $id = $request->post('id');

        $empdata = LRvendor::find($id);
        $response = array();
        if (!empty($empdata)) {
            $created_user = !empty($empdata->created_by) ? User::find($empdata->created_by) : null;
            $updated_user = !empty($empdata->updated_by) ? User::find($empdata->updated_by) : null;

            $created_by_name = $created_user ? $created_user->name : 'NA';
            $created_at_ist = !empty($empdata->created_at) ? \Carbon\Carbon::parse($empdata->created_at)->setTimezone('Asia/Kolkata')->format('d M Y, h:i A') : 'NA';

            $updated_by_name = $updated_user ? $updated_user->name : 'NA';
            $updated_at_ist = !empty($empdata->updated_at) ? \Carbon\Carbon::parse($empdata->updated_at)->setTimezone('Asia/Kolkata')->format('d M Y, h:i A') : 'NA';

            $response['id'] = $empdata->id;
            $response['name'] = $empdata->name;
            $response['companyname'] = $empdata->companyname;
            $response['mobile1'] = $empdata->mobile1;
            $response['mobile2'] = $empdata->mobile2;
            $response['address'] = $empdata->address;
            $response['district'] = $empdata->district;
            $response['city'] = $empdata->city;
            $response['state'] = $empdata->state;
            $response['gstno'] = $empdata->gstno;
            $response['pincode'] = $empdata->pincode;

            $response['created_by_info'] = $created_by_name . ' - ' . $created_at_ist;
            $response['updated_by_info'] = ($updated_user || !empty($empdata->updated_at)) ? ($updated_by_name . ' - ' . $updated_at_ist) : 'NA';

            $response['success'] = 1;
        } else {
            $response['success'] = 0;
        }

        return response()->json($response);
    }



    // Update Employee record
    public function updateVendorAddress(Request $request)
    {
        ## Read POST data
        $id = $request->post('id');

        $empdata = LRvendor::find($id);

        $response = array();
        if (!empty($empdata)) {
            $updata['name'] = $request->post('name');
            $updata['companyname'] = $request->post('companyname');
            $updata['address'] = $request->post('address');
            $updata['district'] = $request->post('district');
            $updata['city'] = $request->post('city');
            $updata['state'] = $request->post('state');
            $updata['pincode'] = $request->post('pincode');
            $updata['gstno'] = $request->post('gstno');
            $updata['mobile1'] = $request->post('mobile1');
            $updata['mobile2'] = $request->post('mobile2');
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
    public function deleteVendorAddress(Request $request)
    {
        ## Read POST data
        $id = $request->post('id');
        // Retrieve the current status
        $currentStatus = DB::table('LRvendor')
            ->where('id', $id)
            ->value('status');

        // Determine the new status
        $newStatus = $currentStatus == 1 ? 2 : 1;

        // Update the status
        $result = DB::table('LRvendor')
            ->where('id', $id)
            ->update(['status' => $newStatus]);

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