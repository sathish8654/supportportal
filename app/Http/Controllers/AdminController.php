<?php

namespace App\Http\Controllers;

use App\Models\imei_registration;
use App\Models\LRiotSim;
use App\Models\User;
use App\Models\user_imei;
use App\Models\user_imei_deleted;
use App\Models\UserPermission;
use App\Models\users_deleted;
use App\Models\v2_connectivity_logs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;  
use Illuminate\Validation\ValidationException;
use DataTables;
Use Response;

// 1-Airtel,2-BSNL,3-JIO

class AdminController extends Controller
{
    // session expire to redirect to login page
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function home()
    {
        return view('home');
    }

    public function Adminslist()
    {
        if (Auth::check() && Auth::user()->role_id == 2) {
            return view('userAdmins');
        } else {
            return view('dashboard');
        }
    }

    // DataTable data
    public function getAdminListData()
    {
        $employees = DB::table('users')->where('role_id', '>', 1)->get();

        return Datatables::of($employees)
            ->addIndexColumn()
            ->addColumn('role_id', function ($row) {
                $roleMap = [
                    1 => 'Disabled',
                    2 => 'Support Portal Admin',
                    10 => 'Sales Executive',
                    11 => 'Sales Head',
                    12 => 'Accounts / Finance',
                    13 => 'Managing Director (MD)',
                    14 => 'Production Head',
                    15 => 'Service Engineer',
                ];
                return $roleMap[$row->role_id] ?? ('Role ' . $row->role_id);
            })
            ->addColumn('status', function ($row) {
                if ($row->status == 1) {
                    return 'Active';
                } else {
                    return 'Inactive';
                }
            })
            ->addColumn('action', function ($row) {
                $action = '<div class="d-inline-flex align-items-center gap-1">';
                if (Auth::user()->hasMenuPermission('Manage', 'userAdmins', 'edit') && Auth::user()->current_team_id == 1) {
                    // Update Button
                    $action .= "<button title='Edit Access' class='btn btn-sm btn-outline-primary updateUser d-inline-flex align-items-center justify-content-center p-1' style='width:28px; height:28px;' data-id='" . $row->id . "' data-bs-toggle='modal' data-bs-target='#updateModal'><i class='fa fa-edit'></i></button>";
                } 
                if (Auth::user()->hasMenuPermission('Manage', 'userAdmins', 'delete') && Auth::user()->current_team_id == 1) {
                    // Delete Button
                    $action .= "<button title='Remove Access' class='btn btn-sm btn-outline-danger deleteUser d-inline-flex align-items-center justify-content-center p-1' style='width:28px; height:28px;' data-id='" . $row->id . "'><i class='fa fa-trash-o'></i></button>";
                }
                if (Auth::user()->hasMenuPermission('Manage', 'userAdmins', 'edit') && Auth::user()->current_team_id == 1) {
                    // Update Password Button
                    $action .= "<button title='Update User Password' class='btn btn-sm btn-outline-primary updatePassword d-inline-flex align-items-center justify-content-center p-1' style='width:28px; height:28px;' data-id='" . $row->id . "'><i class='fa fa-key'></i></button>";
                }
                $action .= '</div>';

                return $action;

            })
            ->make();
    }

      // App DataTable data
      public function getAppAdminListData()
      {
          $employees = DB::table('users')->where('user_type', '=', 'admin_user')->get();
  
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
              ->addColumn('status', function ($row) {
                  if ($row->status == 1) {
                      return 'Active';
                  } else {
                      return 'Inactive';
                  }
              })
              ->addColumn('action', function ($row) {
                  $action = '<div class="d-inline-flex align-items-center gap-1">';
                  if (Auth::user()->hasMenuPermission('Manage', 'userAdmins', 'delete') && Auth::user()->current_team_id == 1) {
                      // Delete Button
                      $action .= "<button title='Remove Access' class='btn btn-sm btn-outline-danger removeAppAdminAccess d-inline-flex align-items-center justify-content-center p-1' style='width:28px; height:28px;' data-id='" . $row->id . "'><i class='fa fa-trash-o'></i></button>";
                  }
                  $action .= '</div>';
  
                  return $action;
  
              })
              ->make();
      }

    // Autocomplete user search by name, email, or mobile number
    public function searchAdminUsers(Request $request)
    {
        $query = trim($request->get('query', ''));
        if (strlen($query) < 1) {
            return response()->json([]);
        }

        $users = DB::table('users')
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%")
                  ->orWhere('mobile_no', 'LIKE', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'name', 'email', 'mobile_no', 'user_type', 'role_id', 'status']);

        return response()->json($users);
    }

    // Read Employee record by ID
    public function getAdminList(Request $request)
    {
        // # Read POST data
        $id = $request->post('id');

        $empdata = User::find($id);
// dd($userPermission[0]->all_special_delete);
        $response = [];
        if (!empty($empdata)) {
            $response['name'] = $empdata->name;
            $response['email'] = $empdata->email;
            $response['mobile'] = $empdata->mobile_no;
            $response['imei'] = $empdata->imei;
            $response['user_type'] = $empdata->user_type;
            $response['role_id'] = $empdata->role_id;
            $response['only_selfview'] = $empdata->only_selfview;
            $response['current_team_id'] = $empdata->current_team_id;

            $response['navbar_menus'] = self::parseNavbarMenus();

           
            $userPermission = DB::table('user_permission')->where('user_id', $id)->exists();

            if ($userPermission){
                // Fetch filtered rows where 'user_id' matches the given $id
                $userData = DB::table('user_permission')
                                ->where('user_id', $id) // Add the filter condition
                                ->first();
                $response['userData'] = $userData;

                $userPermissionsData = DB::table('user_permission')
                                        ->where('user_id', $id)
                                        ->get();  // Get the row for the authenticated user

                $response['all_select'] = $userPermissionsData[0]->all_select;
                $response['all_special_delete'] = $userPermissionsData[0]->all_special_delete;
            } else {
          
            }
    


            $response['success'] = 1;
        } else {
            $response['success'] = 0;
        }

        return response()->json($response);
    }

    // Update Employee record
    public function updateAdminList(Request $request)
    {

        // # Read POST data
        $id = $request->post('id');

        if ($id != 0 && $request->post('createNew') == 3){ // update user account password
            // dd($request->all());
            try {
            $request->validate([
                'id' => 'required', 
                'password' => 'min:6|required_with:password_confirmation|same:password_confirmation',
                'password_confirmation' => 'min:6'
            ]);

            $userPasswordUpdate['password'] = Hash::make($request->post('password'));
            $userPasswordUpdate['email_verified_at'] = date('Y-m-d H:i:s');
            $userPasswordUpdate['updated_at'] = date('Y-m-d H:i:s');
            $userPasswordUpdate['updated_by'] = Auth::user()->id;

            $updatePassword = User::find($id);
            $updatePassword->update($userPasswordUpdate);
        
            session()->put('success','Updated successfully.');

            $response = [];
            $response['success'] = 1;
            $response['msg'] = 'Updated successfully';
            $response['message'] = 'Updated successfully';
            return response()->json($response);

            // return redirect()->back()->with('success', 'Form submitted successfully!');
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
        }

        if ($id == 0 && $request->post('createNew') == 2){ // create new admin account
            // dd($request->all());
            try {
            $request->validate([
                'name' => 'required', 
                'mobile' => 'unique:users,mobile_no,' . $request->post('mobile'),
                'email' => 'unique:users,email,' . $request->post('email'),
                'password' => 'min:6|required_with:password_confirmation|same:password_confirmation',
                'password_confirmation' => 'min:6'
            ]);

            $userDataUpdate['name'] = $request->post('name');
            $userDataUpdate['email'] = $request->post('email');
            $userDataUpdate['mobile_no'] = $request->post('mobile');
            $userDataUpdate['user_type'] = 'user';
            $userDataUpdate['password'] = Hash::make($request->post('password'));
            $userDataUpdate['email_verified'] = 1;
            $userDataUpdate['email_verified_at'] = date('Y-m-d H:i:s');
            $userDataUpdate['role_id'] = 2;
            $userDataUpdate['status'] = 1;
            $userDataUpdate['updated_at'] = date('Y-m-d H:i:s');
            $userDataUpdate['updated_by'] = Auth::user()->id;
            // dd($userDataUpdate);
            $user = User::create($userDataUpdate);
            // $id = $user->id;

            $response = [];
            $response['success'] = 1;
            $response['msg'] = 'Created successfully';
            $response['message'] = 'Created successfully';
            return response()->json($response);

            // return redirect()->back()->with('success', 'Form submitted successfully!');
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
        }

        $response = [];
        if ($request->post('role_id') == 1) { // 1 - Support Portal Access is disable
                    DB::table('user_permission')->where('user_id', $id)->delete();
                            } 
                
        if ($request->post('role_id') > 1) { // Active role (2 or 10-15)

          if ($request->isMethod('post')) {
                    $conditions = ['user_id' => $id];
                    $values = [
                        'all_select' => $request->post('all_select') === '1' ? 1 : null,
                        'all_special_delete' => $request->post('all_special_delete') === '1' ? 1 : null,
                        'allowed_menus' => $request->post('allowed_menus'),
                    ];

                    // Check if the record exists
                    $exists = DB::table('user_permission')->where($conditions)->exists();

                    if ($exists) {
                        // Update the record without changing the created_by field
                        $values['updated_at'] = date('Y-m-d H:i:s');
                        $values['updated_by'] = Auth::user()->id;
                        DB::table('user_permission')->where($conditions)->update($values);

                    } else {
                        // Insert the record and set the created_by field
                        $values['user_id'] = $id;
                        $values['created_by'] = Auth::user()->id;
                        $values['created_at'] = date('Y-m-d H:i:s');
                        DB::table('user_permission')->insert($values);
                    }
                }

                if ($request->filled('name')) {
                    $userDataUpdate['name'] = $request->post('name');
                }
                if ($request->filled('email')) {
                    $userDataUpdate['email'] = $request->post('email');
                }
                if ($request->filled('mobile')) {
                    $userDataUpdate['mobile_no'] = $request->post('mobile');
                }
                $userDataUpdate['current_team_id'] = $request->post('current_team_id');
                $userDataUpdate['user_type'] = $request->post('user_type');
                $userDataUpdate['role_id'] = $request->post('role_id');
                $userDataUpdate['only_selfview'] = $request->post('only_selfview');
                $userDataUpdate['updated_at'] = date('Y-m-d H:i:s');
                $userDataUpdate['updated_by'] = Auth::user()->id;
                
                $empdata = User::find($id);
                $empdata->update($userDataUpdate);

                session()->put('success','Updated successfully.');

            $response['success'] = 1;
            $response['msg'] = 'Update successfully';
            $response['message'] = 'Update successfully';
            return response()->json($response);

        } else {
            $response['success'] = 0;
            $response['msg'] = 'Record not updated';
            return redirect()->back()->with('error', 'Your operation was not successful!');
        }
        return redirect()->back()->with('success', 'Your operation was successful!');
    }

    // Delete Employee
    public function deleteAdminList(Request $request)
    {
        // # Read POST data
        $id = $request->post('id');

        DB::table('user_permission')->where('user_id', $id)->delete();
        $checkData = User::find($id) ->update(array('role_id' => 1)); 

        if ($checkData) {
            return response()->json(['message' => 'Deleted Successfully!']);
        } else {
            return response()->json(['message' => 'Failed! Please check!']);
        }
    }

        // removeAppAdminAccess
        public function removeAppAdminAccess(Request $request)
        {
            // # Read POST data
            $id = $request->post('id');
            $checkData = User::find($id) ->update(array('user_type' => 'user')); 
    
            if ($checkData) {
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
                                        device_name, 
                                        login.last_connect, 
                                        users.name as user_name, 
                                        users.mobile_no as user_mobile,
                                        users.email as user_email,
                                        users.user_type as user_types,
                                        users.role_id as role_id,
                                        users.imei as user_imei
                                FROM user_imei 
                                LEFT JOIN (SELECT imei, max(id), `time` as last_connect FROM v2_asetss  group by imei) AS login ON login.imei = user_imei.imei 
                                LEFT JOIN users on users.id = user_imei.user_id');
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
                $imei = $row->imei;
                if (empty($imei)) {
                    $log_days = 'NA';
                } else {
                    $log_days = imei_registration::where('imei', $row->imei)->value('log_days');
                }
                return $log_days;
            })
            ->addColumn('action', function ($row) {
                // Update Button
                $updateButton = "<button class='btn btn-sm btn-outline-primary updateUser' data-id='" . $row->id . "' data-bs-toggle='modal' data-bs-target='#updateModal'><i class='btn btn-sm btn-outline-primary'></i></button>";
                // Delete Button
                $deleteButton = "<button class='btn btn-sm btn-outline-danger deleteUser' data-id='" . $row->id . "'><i class='btn btn-sm btn-outline-danger'></i></button>";

                if (Auth::check() && Auth::user()->role_id == 2 && Auth::user()->current_team_id == 1) {
                    return $updateButton . ' ' . $deleteButton;
                } else {
                    return $updateButton;
                }
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

        // $empdata = user_imei::find($id);

        $user_imei = user_imei::findOrFail($id);

        // // replicate (duplicate) the data
        // $user_imei_deleted = $user_imei->replicate();
        // $user_imei_deleted->pid = $user_imei->id;

        // // make into array for mass assign.
        // //make sure you activate $guarded in your Staff model
        // $user_imei_deleted = $user_imei_deleted->toArray();

        // user_imei_deleted::firstOrCreate($user_imei_deleted);

        if ($user_imei->delete()) {
            return response()->json(['message' => 'Deleted Successfully!']);
        } else {
            return response()->json(['message' => 'Failed! Please check!']);
        }
    }

    public static function parseNavbarMenus()
    {
        $path = resource_path('views/layouts/navbarmenus.blade.php');
        if (!file_exists($path)) {
            return [];
        }
        $content = file_get_contents($path);

        $menus = [];

        // Split by <li class="nav-item dropdown">
        $sections = explode('<li class="nav-item dropdown">', $content);
        array_shift($sections); // Remove first part before the first dropdown

        foreach ($sections as $section) {
            if (preg_match('/<a[^>]*class="[^"]*dropdown-toggle[^"]*"[^>]*>\s*(.*?)\s*<\/a>/s', $section, $titleMatch)) {
                $title = trim($titleMatch[1]);
                // Clean title if it contains Blade translation {{ __('Users') }} or similar
                $title = preg_replace('/\{\{\s*__\([\'"](.*?)[\'"]\)\s*\}\}/', '$1', $title);
                $title = trim($title);

                $submenus = [];
                // Look for <a class="dropdown-item" href="{{ route('...') }}">...</a>
                preg_match_all('/<a[^>]*class="[^"]*dropdown-item[^"]*"[^>]*href="\s*\{\{\s*route\(\s*[\'"](.*?)[\'"]\s*\)\s*\}\}\s*"[^>]*>\s*(.*?)\s*<\/a>/s', $section, $submenuMatches, PREG_SET_ORDER);

                foreach ($submenuMatches as $match) {
                    $route = trim($match[1]);
                    $label = trim($match[2]);
                    $submenus[] = [
                        'route' => $route,
                        'label' => $label
                    ];
                }

                if (!empty($submenus)) {
                    $menus[] = [
                        'title' => $title,
                        'submenus' => $submenus
                    ];
                }
            }
        }

        return $menus;
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
