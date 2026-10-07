<?php

namespace App\Http\Controllers;

use App\Models\device_assign_users;
use App\Models\device_logs;
use App\Models\device_parts;
use App\Models\device_valve_types;
use App\Models\imei_registration;
use App\Models\LRiotSim;
use App\Models\User;
use App\Models\user_imei;
use App\Models\user_imei_deleted;
use App\Models\users_deleted;
use App\Models\v2_connectivity_logs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DataTables;
Use Response;

// 1-Airtel,2-BSNL,3-JIO

class HomeController extends Controller
{
    // session expire to redirect to login page
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('home');
    }

    public function userlist()
    {
        if (Auth::check() && Auth::user()->role_id == 2) {
            return view('userlist');
        } else {
            return view('dashboard');
        }
    }

    public function userDevice()
    {
        if (Auth::check() && Auth::user()->role_id == 2) {
            $gsm_module = device_parts::where('parent_id', 1)->where('status', 1)->get();
            $sim_scope = device_parts::where('parent_id', 2)->where('status', 1)->get();
            $lora_module = device_parts::where('parent_id', 3)->where('status', 1)->get();
            $lora_joint = device_parts::where('parent_id', 4)->where('status', 1)->get();
            $master = device_parts::where('parent_id', 5)->where('status', 1)->get();
            $valve_type = device_parts::where('parent_id', 6)->where('status', 1)->get();
            $butterfly_valve = device_parts::where('parent_id', 7)->where('status', 1)->get();
            $level_sensor = device_parts::where('parent_id', 8)->where('status', 1)->get();
            return view('userDevice', [
                'gsm_module' => $gsm_module,
                'sim_scope' => $sim_scope,
                'lora_module' => $lora_module,
                'lora_joint' => $lora_joint,
                'master' => $master,
                'valve_type' => $valve_type,
                'butterfly_valve' => $butterfly_valve,
                'level_sensor' => $level_sensor,
            ]);
            // return view('userDevice');
        } else {
            return view('dashboard');
        }
    }

    // DataTable data
    public function getUserListData()
    {
        $employees = DB::select('SELECT users.*, login.last_connect, imei_registration.log_days 
                                 FROM users 
                                 LEFT JOIN (SELECT imei, max(id), `time` as last_connect 
                                            FROM v2_asetss  group by imei) AS login ON login.imei = users.imei
                                 LEFT JOIN imei_registration ON imei_registration.imei = users.imei');

        return Datatables::of($employees)
            ->addIndexColumn()
            ->addColumn('role_id', function ($row) {
                if ($row->role_id == 1) {
                    return 'Disabled';
                } elseif ($row->role_id == 2) {
                    return 'Enabled';
                } else {
                    return 'NA';
                }
            })
            ->addColumn('log_days', function ($row) {
                return $row->log_days ?? 'NA';
            })
            // ->addColumn('status', function ($row) {
            //     if ($row->status == 1) {
            //         return "Active";
            //     } else {
            //         return "Inactive";
            //     }
            // })
            ->addColumn('action', function ($row) {
                $user = Auth::user();
                $action = '';

                // Check Edit Permission
                if ($user->hasMenuPermission('Users', 'userlist', 'edit')) {
                    $action .= "<button class='btn btn-sm btn-outline-success updateUser me-1' data-id='" . $row->id . "' data-bs-toggle='modal' data-bs-target='#updateModal' title='Edit'><i class='fa fa-edit'></i></button>";
                }
                // Check View Permission (if no Edit)
                elseif ($user->hasMenuPermission('Users', 'userlist', 'view')) {
                    $action .= "<button class='btn btn-sm btn-outline-primary viewUser me-1' data-id='" . $row->id . "' data-bs-toggle='modal' data-bs-target='#updateModal' data-view-only='1' title='View'><i class='fa fa-eye'></i></button>";
                }

                // Check Delete Permission
                if ($user->hasMenuPermission('Users', 'userlist', 'delete')) {
                    $action .= "<button class='btn btn-sm btn-outline-danger deleteUser' data-id='" . $row->id . "' title='Delete'><i class='fa fa-trash'></i></button>";
                }

                return $action;
            })
            ->rawColumns(['action'])
            ->make();
    }

    // DataTable data
    public function getUserListDevice()
    {
        // $employees = DB::select('SELECT users.*,
        //                                 login.last_connect FROM users
        //                         LEFT JOIN (SELECT imei, max(id), `time` as last_connect
        //                         FROM v2_asetss  group by imei) AS login ON login.imei = users.imei');
        $employees = DB::select('SELECT device_assign_users.*, users.name as creator_name 
                                     FROM device_assign_users 
                                     LEFT JOIN users ON users.id = device_assign_users.created_by 
                                     WHERE device_assign_users.status = 1');
        // dd($employees);
        return Datatables::of($employees)
            ->addIndexColumn()
            // ->addColumn('role_id', function ($row) {
            //         return 'NA';
            // })
            // ->addColumn('log_days', function ($row) {
            //     $imei = $row->imei;
            //     if (empty($imei))
            //         {
            //             $log_days= "NA";
            //         }else{
            //             $log_days = imei_registration::where('imei', $row->imei)->value('log_days');
            //         }
            //         return $log_days;
            // })
            ->addColumn('created_by', function ($row) {
                return $row->creator_name ?? 'None';
            })
            ->addColumn('action', function ($row) {
                $user = Auth::user();
                if (!$user) {
                    return '';
                }
                $buttons = [];

                // Check View Permission
                if ($user->hasMenuPermission('Users', 'userDevice', 'view')) {
                    $buttons[] = "<button type='button' title='View unit info' class='btn btn-sm btn-outline-success viewUnitInfo' data-id='" . $row->id . "' data-imei='" . $row->imei . "' data-username='" . $row->username . "' data-mode='2' data-bs-toggle='modal' data-bs-target='#printImei'><i class='fa fa-eye'></i></button>";
                }
                // Check Edit Permission
                if ($user->hasMenuPermission('Users', 'userDevice', 'edit')) {
                    $buttons[] = "<button type='button' title='Edit unit info' class='btn btn-sm btn-outline-warning editUnitInfo' data-id='" . $row->id . "' data-imei='" . $row->imei . "' data-username='" . $row->username . "' data-mode='1' data-bs-toggle='modal' data-bs-target='#updateModalImei'><i class='fa fa-edit'></i></button>";
                    $buttons[] = "<button type='button' title='Edit user info' class='btn btn-sm btn-outline-info editUserInfo' data-id='" . $row->id . "' data-username='" . $row->username . "' data-email='" . ($row->email ?? '') . "' data-mobile='" . $row->mobile . "' data-imei='" . $row->imei . "' data-village='" . $row->village . "' data-city='" . $row->city . "' data-bs-toggle='modal' data-bs-target='#updateUserModal'><i class='fa fa-user'></i></button>";
                }

                // Check Delete Permission
                if ($user->hasMenuPermission('Users', 'userDevice', 'delete')) {
                    $buttons[] = "<button type='button' title='Delete user device' class='btn btn-sm btn-outline-danger deleteUnit' data-id='" . $row->id . "'><i class='fa fa-trash'></i></button>";
                }

                if (empty($buttons)) {
                    return '';
                }

                return "<div class='d-inline-flex gap-1 align-items-center'>" . implode('', $buttons) . '</div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    // Read Employee record by ID
    public function getUserList(Request $request)
    {
        // # Read POST data
        $id = $request->post('id');

        $empdata = User::find($id);
        $imeidata = DB::table('imei_registration')->where('imei', $empdata->imei)->value('log_days');

        $response = [];
        if (!empty($empdata)) {
            $response['name'] = $empdata->name;
            $response['email'] = $empdata->email;
            $response['mobile'] = $empdata->mobile_no;
            $response['imei'] = $empdata->imei;
            $response['user_type'] = $empdata->user_type;
            $response['role_id'] = $empdata->role_id;

            $response['log_days'] = $imeidata;

            $response['success'] = 1;
        } else {
            $response['success'] = 0;
        }

        return response()->json($response);
    }

    // Update Employee record
    public function updateUserList(Request $request)
    {
        // # Read POST data
        $id = $request->post('id');
        $imei = $request->post('imei');
        $log_days = $request->post('log_days');

        $empdata = User::find($id);

        $response = [];
        if (!empty($empdata)) {
            $updata['name'] = $request->post('name');
            $updata['email'] = $request->post('email');
            $updata['mobile_no'] = $request->post('mobile');
            $updata['imei'] = $request->post('imei');
            $updata['user_type'] = $request->post('user_type');
            $updata['role_id'] = $request->post('role_id');
            // $updata['log_days'] = $request->post('log_days');

            if ($empdata->update($updata)) {
                $imeidata = DB::table('imei_registration')
                    ->where('imei', $imei)
                    ->update(['log_days' => $log_days,
                        'updated_by' => Auth::user()->id,
                        'updated_at' => date('Y-m-d H:i:s')]);

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

    // delete dvice aggigned user list
    public function deleteDeviceUser(Request $request)
    {
        // # Read POST data
        $id = $request->post('id');
        // dd($id);
        // $empdata = User::find($id);
        $users = device_assign_users::find($id);
        if (!empty($users)) {
            $updata['status'] = 2;
            $users->update($updata);
            return response()->json(['message' => 'Deleted Successfully!']);
        } else {
            return response()->json(['message' => 'Failed! Please check!']);
        }
    }

    // Delete Employee
    public function deleteUserList(Request $request)
    {
        // # Read POST data
        $id = $request->post('id');

        // $empdata = User::find($id);
        $users = User::findOrFail($id);
        if ($users->delete()) {
            $checkData = DB::table('user_imei')->where('user_id', $id)->get();
            if ($checkData) {
                DB::table('user_imei')->where('user_id', $id)->delete();
            }
            return response()->json(['message' => 'Deleted Successfully!']);
        } else {
            return response()->json(['message' => 'Failed! Please check!']);
        }
    }

    // User IMEI blade

    public function userimei()
    {
        if (Auth::check() && Auth::user()->role_id == 2) {
            return view('userimei');
        } else {
            return view('dashboard');
        }
    }

    // DataTable data
    public function getUserImeiData()
    {
        $employees = DB::select('SELECT user_imei.id, 
                                        user_imei.imei, 
                                        user_imei.device_name, 
                                        login.last_connect, 
                                        users.name as user_name, 
                                        users.mobile_no as user_mobile,
                                        users.email as user_email,
                                        users.user_type as user_types,
                                        users.role_id as role_id,
                                        users.imei as user_imei,
                                        imei_registration.log_days
                                FROM user_imei 
                                LEFT JOIN (SELECT imei, max(id), `time` as last_connect FROM v2_asetss  group by imei) AS login ON login.imei = user_imei.imei 
                                LEFT JOIN users on users.id = user_imei.user_id
                                LEFT JOIN imei_registration ON imei_registration.imei = user_imei.imei');
        // WHERE user_imei.imei = 860537061235941'

        return Datatables::of($employees)
            ->addIndexColumn()
            // ->addColumn('status', function ($row) {
            //     if ($row->status == 1) {
            //         return "Active";
            //     } else {
            //         return "Inactive";
            //     }
            // })
            ->addColumn('log_days', function ($row) {
                return $row->log_days ?? 'NA';
            })
            ->addColumn('action', function ($row) {
                $user = Auth::user();
                $action = '';

                // Check Edit Permission
                if ($user->hasMenuPermission('Users', 'userimei', 'edit')) {
                    $action .= "<button class='fa fa-edit updateUser me-1' data-id='" . $row->id . "' data-bs-toggle='modal' data-bs-target='#updateModal' title='Edit'></button>";
                }
                // Check View Permission (if no Edit)
                elseif ($user->hasMenuPermission('Users', 'userimei', 'view')) {
                    $action .= "<button class='fa fa-eye viewUser me-1' data-id='" . $row->id . "' data-bs-toggle='modal' data-bs-target='#updateModal' data-view-only='1' title='View'></button>";
                }

                // Check Delete Permission
                if ($user->hasMenuPermission('Users', 'userimei', 'delete')) {
                    $action .= "<button class='fa fa-trash-o deleteUser' data-id='" . $row->id . "' title='Delete'></button>";
                }

                return $action;
            })
            ->make();
    }

    // Read Employee record by ID
    public function getUserImei(Request $request)
    {
        // # Read POST data
        $id = $request->post('id');

        $empdata1 = DB::table('user_imei')->find($id);

        $empdata = DB::table('user_imei')
            ->select(
                'user_imei.id',
                'user_imei.user_id',
                'user_imei.imei',
                'user_imei.device_name',
                'users.name as user_name',
                'users.mobile_no as user_mobile',
                'users.email as user_email',
                'users.imei as user_imei',
                'users.user_type as user_types',
                'users.role_id as role_id',
                'imei_registration.log_days as log_days',
            )
            ->leftJoin('users', 'users.id', '=', 'user_imei.user_id')
            ->leftJoin('imei_registration', 'imei_registration.imei', '=', 'user_imei.imei')
            ->where('user_imei.id', '=', $id)
            ->get();

        $response = [];
        if (!empty($empdata)) {
            $response['device_name'] = $empdata[0]->device_name;
            $response['user_name'] = $empdata[0]->user_name;
            $response['user_email'] = $empdata[0]->user_email;
            $response['user_mobile'] = $empdata[0]->user_mobile;
            $response['user_imei'] = $empdata[0]->user_imei;
            $response['user_types'] = $empdata[0]->user_types;
            $response['role_id'] = $empdata[0]->role_id;
            $response['imei'] = $empdata[0]->imei;
            $response['device_name'] = $empdata[0]->device_name;
            $response['log_days'] = $empdata[0]->log_days;
            $response['success'] = 1;
        } else {
            $response['success'] = 0;
        }

        return response()->json($response);
    }

    // Update Employee record
    public function updateUserImei(Request $request)
    {
        // # Read POST data
        $id = $request->post('id');
        $imei = $request->post('imei');
        $log_days = $request->post('log_days');

        // $empdata = User::find($id);
        $empdata = user_imei::find($id);
        // $empdata = DB::table('user_imei')->find($id);

        $response = [];
        if (!empty($empdata)) {
            $updata['imei'] = $request->post('imei');
            $updata['device_name'] = $request->post('device_name');

            if ($empdata->update($updata)) {
                $imeidata = DB::table('imei_registration')
                    ->where('imei', $imei)
                    ->update(['log_days' => $log_days,
                        'updated_by' => Auth::user()->id,
                        'updated_at' => date('Y-m-d H:i:s')]);

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

    // Delete Employee
    public function deleteUserImei(Request $request)
    {
        // # Read POST data
        $id = $request->post('id');
        $user_imei = user_imei::findOrFail($id);

        if ($user_imei->delete()) {
            return response()->json(['message' => 'Deleted Successfully!']);
        } else {
            return response()->json(['message' => 'Failed! Please check!']);
        }
    }
}

// $datacount1 = DB::table('user_imei')->count();
// $data = DB::table('user_imei')->latest()->get();

// $data = DB::table('user_imei')
//             // ->select('user_imei.user_id', 'user_imei.imei','user_imei.imei_id','user_imei.device_name','users.name as user_name')
//             // ->select('user_imei.user_id', 'user_imei.imei','user_imei.imei_id','user_imei.device_name','users.name as user_name', 'v2_connectivity_logs.server_time as v2_connectivity_logs_server_time','v2_connectivity_logs.imei')
//             ->select('user_imei.user_id', 'user_imei.imei','user_imei.imei_id','user_imei.device_name', 'v2_connectivity_logs.server_time as v2_connectivity_logs_server_time','v2_connectivity_logs.imei')
//             // ->leftJoin('users', 'users.id', '=', 'user_imei.user_id')
//             ->leftJoin('v2_connectivity_logs', 'v2_connectivity_logs.imei', '=', 'user_imei.imei')
//             ->orderBy('v2_connectivity_logs.id', 'DESC')
//             ->last('v2_connectivity_logs_server_time')
//             // ->groupBy('v2_connectivity_logs.imei')
//             // ->take('1')
//             // ->where('v2_connectivity_logs.id', DB::raw("(select max('v2_connectivity_logs.id') from v2_connectivity_logs)"))

//             ->get();
// $data = DB::table('user_imei')
// ->select('user_imei.user_id', 'user_imei.imei','user_imei.imei_id','user_imei.device_name','users.name as user_name')
// ->leftJoin('users', 'users.id', '=', 'user_imei.user_id')
// ->take('1')
// ->get();

// $datacount1 = count($data);

// echo $datacount1;
// echo "<Br>";
// echo $datacount;
// echo '<pre>';
// print_r ($data);
// exit;
