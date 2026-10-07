<?php
namespace App\Http\Controllers;

use App\Imports\ImeiImport;
use App\Models\device_assign_users;
use App\Models\device_logs;
use App\Models\device_parts;
use App\Models\device_valve_types;
use App\Models\imei_registration;
use App\Models\User;
use App\Models\user_imei;
use App\Models\users_deleted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use DataTables;
use Validator;

class ImeiController extends Controller
{
    // session expire to redirect to login page
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function userAddImei()
    {
        if (Auth::check() && Auth::user()->role_id == 2) {
            $result = device_parts::where('status', 1)->get();

            $gsm_module = device_parts::where('parent_id', 1)->where('status', 1)->get();
            $sim_scope = device_parts::where('parent_id', 2)->where('status', 1)->get();
            $lora_module = device_parts::where('parent_id', 3)->where('status', 1)->get();
            $lora_joint = device_parts::where('parent_id', 4)->where('status', 1)->get();
            $master = device_parts::where('parent_id', 5)->where('status', 1)->get();
            $valve_type = device_parts::where('parent_id', 6)->where('status', 1)->get();
            $butterfly_valve = device_parts::where('parent_id', 7)->where('status', 1)->get();
            $level_sensor = device_parts::where('parent_id', 8)->where('status', 1)->get();

            // print_r($result);
            return view('imei', [
                'gsm_module' => $gsm_module,
                'sim_scope' => $sim_scope,
                'lora_module' => $lora_module,
                'lora_joint' => $lora_joint,
                'master' => $master,
                'valve_type' => $valve_type,
                'butterfly_valve' => $butterfly_valve,
                'level_sensor' => $level_sensor,
            ]);
        } else {
            return view('dashboard');
        }
    }

    // DataTable data
    public function getImeiListData()
    {
        $employees = DB::table('imei_registration')
            ->select([
                'imei_registration.*',
                'last_connect' => DB::table('v2_asetss')
                    ->select('time')
                    ->whereColumn('v2_asetss.imei', 'imei_registration.imei')
                    ->orderBy('id', 'desc')
                    ->limit(1)
            ]);

        return Datatables::of($employees)
            ->addIndexColumn()
            ->filterColumn('owner', function ($query, $keyword) {
                $matchingImeis = User::where('status', 1)
                    ->where('name', 'like', "%{$keyword}%")
                    ->pluck('imei')
                    ->toArray();
                $matchingUserImeis = user_imei::join('users', 'user_imei.user_id', '=', 'users.id')
                    ->where('users.name', 'like', "%{$keyword}%")
                    ->pluck('user_imei.imei')
                    ->toArray();
                $allImeis = array_unique(array_filter(array_merge($matchingImeis, $matchingUserImeis)));
                $query->whereIn('imei_registration.imei', $allImeis);
            })
            ->filterColumn('updated_byname', function ($query, $keyword) {
                $userIds = User::where('name', 'like', "%{$keyword}%")->pluck('id')->toArray();
                $query->whereIn('imei_registration.updated_by', $userIds);
            })
            ->filterColumn('status', function ($query, $keyword) {
                $query->where('imei_registration.status', 'like', "%{$keyword}%");
            })
            ->filterColumn('last_connect', function ($query, $keyword) {
                $matchingImeis = DB::table('v2_asetss')
                    ->where('time', 'like', "%{$keyword}%")
                    ->pluck('imei')
                    ->toArray();
                $query->whereIn('imei_registration.imei', $matchingImeis);
            })
            ->orderColumn('last_connect', function ($query, $order) {
                $query->orderBy('last_connect', $order);
            })
            ->orderColumn('updated_byname', function ($query, $order) {
                $query->orderBy('updated_by', $order);
            })
            ->addColumn('owner', function ($row) {
                // 1. Get names directly from users table (imei + status = 1)
                $namesFromUser = User::where('imei', $row->imei)
                    ->where('status', 1)
                    ->pluck('name')  // returns collection of names
                    ->toArray();

                // 2. If not found or empty, get names from user_imei join
                if (empty($namesFromUser)) {
                    $namesFromUser = user_imei::join('users', 'user_imei.user_id', '=', 'users.id')
                        ->where('user_imei.imei', $row->imei)
                        ->pluck('users.name')  // returns collection of names
                        ->toArray();
                }

                // Convert to comma-separated string
                $owner = !empty($namesFromUser)
                    ? implode(', ', $namesFromUser)
                    : 'None';

                return $owner;
            })
            ->addColumn('status', function ($row) {
                if ($row->status == 'active') {
                    return 'Active';
                } else {
                    return 'Inactive';
                }
            })
            ->addColumn('updated_byname', function ($row) {
                return User::where('id', $row->updated_by)->first()->name ?? 'None';
            })
            ->addColumn('action', function ($row) {
                $user = Auth::user();
                if (!$user) {
                    return '';
                }
                $buttons = [];

                // Check Edit Permission
                if ($user->hasMenuPermission('Manage', 'userAddImei', 'edit')) {
                    $buttons[] = "<button class='btn btn-sm btn-outline-warning updateUser' data-id='" . $row->id . "' data-mode='1' data-bs-toggle='modal' data-bs-target='#updateModalImei' title='Edit'><i class='fa fa-edit'></i></button>";
                }
                // Check View Permission (if no Edit)
                elseif ($user->hasMenuPermission('Manage', 'userAddImei', 'view')) {
                    $buttons[] = "<button class='btn btn-sm btn-outline-success updateUser1' data-id='" . $row->id . "' data-mode='2' data-bs-toggle='modal' data-bs-target='#printImei' title='View'><i class='fa fa-eye'></i></button>";
                }

                if (empty($buttons)) {
                    return '';
                }

                return "<div class='btn-group'>" . implode('', $buttons) . '</div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    // Excel / CSV bulk upload
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls,txt|max:4096',
        ]);

        $file = $request->file('file');

        try {
            $import = new ImeiImport();
            Excel::import($import, $file);

            $importedCount = $import->getRowCount();
            $skippedCount = $import->getSkippedCount();

            $msg = "Successfully imported {$importedCount} IMEI(s).";
            if ($skippedCount > 0) {
                $msg .= " ({$skippedCount} duplicate/empty records skipped)";
            }

            return redirect()->back()->with('success', $msg);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error during upload: ' . $e->getMessage());
        }
    }

    // Download Sample Template
    public function downloadSample()
    {
        $filePath = public_path('download/imei_sample.xlsx');
        if (!file_exists($filePath)) {
            $csvPath = public_path('download/imei_sample.csv');
            if (file_exists($csvPath)) {
                return response()->download($csvPath, 'imei_sample.csv', [
                    'Content-Type' => 'text/csv',
                ]);
            }
            return redirect()->back()->with('error', 'Sample file not found.');
        }

        return response()->download($filePath, 'imei_sample.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function getImeiList(Request $request)
    {
        $id = $request->post('id');
        $imei = $request->post('imei') ?? imei_registration::where('id', $id)->value('imei');
        $mode = $request->post('mode');

        $log_record = device_logs::where('master_imei_no', $imei)->first();
        $exists = !empty($log_record);

        if ($exists) {
            $log_id = $log_record->user_id;
        } else {
            $assign_user_id = DB::table('device_assign_users')->where('imei', $imei)->value('id');
            $log_id = $assign_user_id ?? $id;
        }

        if ($exists) {  // if exist value iss selected
            $categories = device_logs::from('device_logs as c1')
                ->select(
                    'c1.*',
                    'c2.*',
                )
                ->leftJoin('device_valve_types as c2', 'c1.user_id', '=', 'c2.user_id')
                ->where('c1.user_id', $log_id)
                ->groupBy('c1.user_id')  // ✅ group by the parent key
                ->first();

            if (!empty($categories)) {
                $response['mode'] = 2;
                $response['imei'] = $imei;
                $response['note'] = imei_registration::where('imei', $imei)->value('note') ?? '';
                $response['log_days'] = imei_registration::where('imei', $imei)->value('log_days') ?? '';

                $response['status'] = imei_registration::where('imei', $imei)->value('status') ?? 'active';
                $response['updated_by'] = $categories->updated_by;
                $response['updated_at'] = $categories->updated_at;
                $response['created_at'] = $categories->created_at;
                $response['imei_id'] = $categories->imei_id;
                $response['gsm_module'] = $categories->gsm_module;
                $response['sim_scope'] = $categories->sim_scope;
                $response['lora_module'] = $categories->lora_module;
                $response['lora_freq'] = $categories->lora_freq;
                $response['lora_channel'] = $categories->lora_channel;
                $response['lora_SF'] = $categories->lora_SF;
                $response['lora_joint'] = $categories->lora_joint;
                $response['master'] = $categories->master;
                $response['master_serial_no'] = $categories->master_serial_no;
                $response['master_imei_no'] = $categories->master_imei_no;
                $response['master_os'] = $categories->master_os;
                $response['repeter'] = $categories->repeter;
                $response['valve_type'] = $categories->valve_type;
                $response['valve_qty'] = $categories->valve_qty;
                $response['butterfly_valve'] = $categories->butterfly_valve;
                $response['cable_mtr'] = $categories->cable_mtr;
                $response['cable_brand'] = $categories->cable_brand;
                $response['high_gain'] = $categories->high_gain;
                $response['solar_power'] = $categories->solar_power;
                $response['level_sensor'] = $categories->level_sensor;
                $response['presere_sensor'] = $categories->presere_sensor;
                $response['mois_sensor'] = $categories->mois_sensor;
                $response['npk'] = $categories->npk;
                $response['ec'] = $categories->ec;
                $response['ph'] = $categories->ph;
                $response['all_in'] = $categories->all_in;
                $response['filter'] = $categories->filter;
                $response['flow_sensor'] = $categories->flow_sensor;
                $response['weather_monitor'] = $categories->weather_monitor;
                $response['fert_sys'] = $categories->fert_sys;
                $response['tank_count'] = $categories->tank_count;
                $response['agitator_tape'] = $categories->agitator_tape;
                $response['injectTape'] = $categories->injectTape;
                $response['valve_fitting'] = $categories->valve_fitting;
                $response['install_date'] = $categories->install_date;
                $response['prog_folder'] = $categories->prog_folder;
                $response['install_actuator'] = $categories->install_actuator;
                $response['panel'] = $categories->panel;
                $response['install_done'] = $categories->install_done;
                $response['sales_person'] = $categories->sales_person;
                $response['location'] = $categories->location;
                $response['customized'] = $categories->customized;
                $response['created_by'] = $categories->created_by;

                $response['device_valve_types'] = device_valve_types::where('user_id', $log_id)->get();

                if (!empty($categories->image)) {
                    $path = public_path('images/' . $categories->image);

                    if (file_exists($path)) {
                        // File exists
                        $response['image_url'] = 'https://' . $_SERVER['HTTP_HOST'] . '/images/' . $categories->image;
                        $response['image_exist'] = $categories->image;
                    } else {
                        // File missing, return placeholder or null
                        $response['image_url'] = '';  // optional fallback
                        $response['image_exist'] = '';
                    }
                } else {
                    $response['image_url'] = '';  // optional fallback
                    $response['image_exist'] = '';
                }

                // dd($response);

                $response['success'] = 1;
            } else {
                $response['success'] = 0;
            }
        } else {  // not exist append value to modal
            // echo$imei;
            // exit;
            $empdata = imei_registration::where('imei', $imei)->first();
            $response = [];
            if (!empty($empdata)) {
                $response['mode'] = 1;
                $response['imei'] = $empdata->imei;
                $response['note'] = $empdata->note;
                $response['log_days'] = $empdata->log_days;

                $response['status'] = $empdata->status ?? 'active';
                $response['updated_by'] = '';
                $response['updated_at'] = '';
                $response['created_at'] = '';
                $response['imei_id'] = '';
                $response['gsm_module'] = '';
                $response['sim_scope'] = '';
                $response['lora_module'] = '';
                $response['lora_freq'] = '';
                $response['lora_channel'] = '';
                $response['lora_SF'] = '';
                $response['lora_joint'] = '';
                $response['master'] = '';
                $response['master_serial_no'] = '';
                $response['master_imei_no'] = '';
                $response['master_os'] = '';
                $response['repeter'] = '';
                $response['valve_type'] = '';
                $response['valve_qty'] = '';
                $response['butterfly_valve'] = '';
                $response['cable_mtr'] = '';
                $response['cable_brand'] = '';
                $response['high_gain'] = '';
                $response['solar_power'] = '';
                $response['level_sensor'] = '';
                $response['presere_sensor'] = '';
                $response['mois_sensor'] = '';
                $response['npk'] = '';
                $response['ec'] = '';
                $response['ph'] = '';
                $response['all_in'] = '';
                $response['filter'] = 'no';
                $response['flow_sensor'] = 'no';
                $response['weather_monitor'] = 'no';
                $response['fert_sys'] = 'no';
                $response['tank_count'] = '';
                $response['agitator_tape'] = '';
                $response['injectTape'] = '';
                $response['valve_fitting'] = '';
                $response['install_date'] = '';
                $response['prog_folder'] = '';
                $response['install_actuator'] = '';
                $response['panel'] = '';
                $response['install_done'] = '';
                $response['sales_person'] = '';
                $response['location'] = '';
                $response['customized'] = '';
                $response['created_by'] = '';
                $response['device_valve_types'] = [];
                $response['image_url'] = '';
                $response['image_exist'] = '';

                $response['success'] = 1;
            } else {
                $response['success'] = 0;
            }
        }

        return response()->json($response);
    }

    // for print page append
    public function appendImeiPrint(Request $request)
    {
        // # Read POST data
        $user_id = $request->post('id');
        $mode = $request->post('mode');
        $imei = $request->post('imei') ?? imei_registration::where('id', $user_id)->value('imei');

        $log_record = device_logs::where('master_imei_no', $imei)->first();
        if (!empty($log_record)) {
            $log_id = $log_record->user_id;
        } else {
            $assign_user_id = DB::table('device_assign_users')->where('imei', $imei)->value('id');
            $log_id = $assign_user_id ?? $user_id;
        }

        $categories = device_logs::from('device_logs as c1')
            ->select(
                'c1.*',
                'c2.*',
                'c1.id as i_id',
            )
            ->leftJoin('device_valve_types as c2', 'c1.user_id', '=', 'c2.user_id')
            ->where('c1.user_id', $log_id)
            ->groupBy('c1.id')
            ->first();

        $response = [];
        $response['imei'] = $imei;
        $response['note'] = imei_registration::where('imei', $imei)->value('note') ?? '';
        $response['log_days'] = imei_registration::where('imei', $imei)->value('log_days') ?? '';

        if (!empty($categories)) {
            $response['status'] = $categories->status;
            $response['updated_by'] = $categories->updated_by;
            $response['updated_at'] = $categories->updated_at;
            $response['created_at'] = $categories->created_at;
            $response['imei_id'] = $categories->imei_id;
            $response['gsm_module'] = device_parts::where('id', $categories->gsm_module)->value('name') ?? '';
            $response['sim_scope'] = device_parts::where('id', $categories->sim_scope)->value('name') ?? '';
            $response['lora_module'] = device_parts::where('id', $categories->lora_module)->value('name') ?? '';
            $response['lora_freq'] = $categories->lora_freq;
            $response['lora_channel'] = $categories->lora_channel;
            $response['lora_SF'] = $categories->lora_SF;
            $response['lora_joint'] = device_parts::where('id', $categories->lora_joint)->value('name') ?? '';
            $response['master'] = device_parts::where('id', $categories->master)->value('name') ?? '';
            $response['master_imei_no'] = $categories->imei;
            $response['master_os'] = $categories->master_os;
            $response['repeter'] = $categories->repeter;
            $response['valve_type'] = device_parts::where('id', $categories->valve_type)->value('name') ?? '';
            $response['valve_qty'] = $categories->valve_qty;
            $response['butterfly_valve'] = device_parts::where('id', $categories->butterfly_valve)->value('name') ?? '';
            $response['cable_mtr'] = $categories->cable_mtr;
            $response['cable_brand'] = $categories->cable_brand;
            $response['high_gain'] = $categories->high_gain;
            $response['eb_power_3p'] = $categories->eb_power_3p;
            $response['eb_power_1p'] = $categories->eb_power_1p;
            $response['solar_power'] = $categories->solar_power;
            $response['level_sensor'] = device_parts::where('id', $categories->level_sensor)->value('name') ?? '';
            $response['presere_sensor'] = $categories->presere_sensor;
            $response['mois_sensor'] = $categories->mois_sensor;
            $response['npk'] = $categories->npk;
            $response['ec'] = $categories->ec;
            $response['ph'] = $categories->ph;
            $response['all_in'] = $categories->all_in;
            $response['filter'] = $categories->filter;
            $response['flow_sensor'] = $categories->flow_sensor;
            $response['weather_monitor'] = $categories->weather_monitor;
            $response['fert_sys'] = $categories->fert_sys;
            $response['tank_count'] = $categories->tank_count;
            $response['agitator_tape'] = $categories->agitator_tape;
            $response['injectTape'] = $categories->injectTape;
            $response['valve_fitting'] = $categories->valve_fitting;
            $response['install_date'] = $categories->install_date;
            $response['prog_folder'] = $categories->prog_folder;
            $response['install_actuator'] = $categories->install_actuator;
            $response['panel'] = $categories->panel;
            $response['install_done'] = $categories->install_done;
            $response['sales_person'] = $categories->sales_person;
            $response['location'] = $categories->location;
            $response['customized'] = $categories->customized;
            $response['created_by'] = $categories->created_by;
        } else {
            $response['status'] = '';
            $response['updated_by'] = '';
            $response['updated_at'] = '';
            $response['created_at'] = '';
            $response['imei_id'] = '';
            $response['gsm_module'] = '';
            $response['sim_scope'] = '';
            $response['lora_module'] = '';
            $response['lora_freq'] = '';
            $response['lora_channel'] = '';
            $response['lora_SF'] = '';
            $response['lora_joint'] = '';
            $response['master'] = '';
            $response['master_imei_no'] = '';
            $response['master_os'] = '';
            $response['repeter'] = '';
            $response['valve_type'] = '';
            $response['valve_qty'] = '';
            $response['butterfly_valve'] = '';
            $response['cable_mtr'] = '';
            $response['cable_brand'] = '';
            $response['high_gain'] = '';
            $response['eb_power_3p'] = '';
            $response['eb_power_1p'] = '';
            $response['solar_power'] = '';
            $response['level_sensor'] = '';
            $response['presere_sensor'] = '';
            $response['mois_sensor'] = '';
            $response['npk'] = '';
            $response['ec'] = '';
            $response['ph'] = '';
            $response['all_in'] = '';
            $response['filter'] = '';
            $response['flow_sensor'] = '';
            $response['weather_monitor'] = '';
            $response['fert_sys'] = '';
            $response['tank_count'] = '';
            $response['agitator_tape'] = '';
            $response['injectTape'] = '';
            $response['valve_fitting'] = '';
            $response['install_date'] = '';
            $response['prog_folder'] = '';
            $response['install_actuator'] = '';
            $response['panel'] = '';
            $response['install_done'] = '';
            $response['sales_person'] = '';
            $response['location'] = '';
            $response['customized'] = '';
            $response['created_by'] = '';
        }

        // ///customer details
        $response['customer_name'] = device_assign_users::where('id', $user_id)->value('username') ?? '';
        $response['customer_mobile'] = device_assign_users::where('id', $user_id)->value('mobile') ?? '';
        $response['customer_village'] = device_assign_users::where('id', $user_id)->value('village') ?? '';
        $response['customer_city'] = device_assign_users::where('id', $user_id)->value('city') ?? '';

        // Fetch all valve types for this imei_id
        $valveTypes = device_valve_types::where('user_id', $log_id)->where('status', 1)->get();

        // Build structured array
        $valves = [];
        foreach ($valveTypes as $valve) {
            $valves[] = [
                'type' => $valve->name_type,
                'qty' => $valve->value_qty,
                'status' => $valve->status ?? null,
            ];
        }
        $response['valves'] = $valves;

        $ebPower = device_valve_types::where('user_id', $log_id)->where('status', 2)->get();

        // Build structured array
        $ebeb = [];
        foreach ($ebPower as $valve) {
            $ebeb[] = [
                'type' => $valve->name_type,
                'qty' => $valve->value_qty,
                'status' => $valve->status ?? null,
            ];
        }
        $response['ebPower'] = $ebeb;

        $response['success'] = 1;
        $table_view = view('modals.imeiPrintModal', compact('response'))->render();
        return response()->json(['succes' => true, 'table_view' => $table_view]);
    }

    // Update Employee record
    public function updateImeiList(Request $request)
    {
        // # Read POST data
        $id = $request->post('id');
        $imei = $request->post('imei');
        $mode = $request->post('mode');  // check insert or update query

        $imei_reg_id = imei_registration::where('imei', $imei)->value('id');
        $log_record = device_logs::where('master_imei_no', $imei)->first();
        if (!empty($log_record)) {
            $log_id = $log_record->user_id;
        } else {
            $assign_user_id = DB::table('device_assign_users')->where('imei', $imei)->value('id');
            $log_id = $assign_user_id ?? ($imei_reg_id ?? $id);
        }

        $empdata = imei_registration::where('imei', $imei);
        // $valve             = new device_valve_types;
        // dd($request);

        $response = [];
        if (!empty($empdata)) {
            $updata['imei'] = $request->post('imei');
            $updata['note'] = $request->post('note');
            if ($request->has('status')) {
                $updata['status'] = $request->post('status');
            }
            if ($request->has('log_days')) {
                $updata['log_days'] = $request->post('log_days');
            }
            $updata['updated_by'] = Auth::user()->id;

            if ($empdata->update($updata)) {
                // //////////////////for imei log insert part
                $device_logs = new device_logs;
                $device_logs->user_id = $log_id ?? 1;
                $device_logs->imei_id = $log_id ?? 1;
                $device_logs->gsm_module = $request->post('gsm_module') ?? '';
                $device_logs->sim_scope = $request->post('sim_scope') ?? '';
                $device_logs->lora_module = $request->post('lora_module') ?? '';
                $device_logs->lora_freq = $request->post('lora_freq') ?? '';
                $device_logs->lora_channel = $request->post('lora_channel') ?? '';
                $device_logs->lora_SF = $request->post('lora_SF') ?? '';
                $device_logs->lora_joint = $request->post('lora_joint') ?? '';
                $device_logs->master = $request->post('master') ?? '';
                $device_logs->master_serial_no = $request->post('master_imei_no') ?? '';
                $device_logs->master_imei_no = $request->post('imei') ?? '';
                $device_logs->master_os = $request->post('master_os') ?? '';
                $device_logs->repeter = $request->post('repeter') ?? '';
                $device_logs->valve_type = $request->post('valve_type') ?? '';
                $device_logs->valve_qty = $request->post('valve_qty') ?? '';
                // $device_logs->valve_qty        = $request->post('valve_qty') ?? '';
                $device_logs->butterfly_valve = $request->post('butterfly_valve') ?? '';

                $device_logs->cable_mtr = $request->post('cable_mtr') ?? '';
                $device_logs->cable_brand = $request->post('cable_brand') ?? '';
                $device_logs->high_gain = $request->post('high_gain') ?? '';
                // $device_logs->eb_power_3p = $request->post('eb_power_3p') ?? '';
                // $device_logs->eb_power_1p = $request->post('eb_power_1p') ?? '';
                $device_logs->solar_power = $request->post('solar_power') ?? '';
                $device_logs->level_sensor = $request->post('level_sensor') ?? '';
                $device_logs->presere_sensor = $request->post('presere_sensor') ?? '';
                $device_logs->mois_sensor = $request->post('mois_sensor') ?? '';
                $device_logs->npk = $request->post('npk') ?? '';
                $device_logs->ec = $request->post('ec') ?? '';
                $device_logs->ph = $request->post('ph') ?? '';
                $device_logs->all_in = $request->post('all_in') ?? '';
                $device_logs->filter = $request->post('filter') ?? '';
                $device_logs->flow_sensor = $request->post('flow_sensor') ?? '';
                $device_logs->weather_monitor = $request->post('weather_monitor') ?? '';
                $device_logs->fert_sys = $request->post('fert_sys') ?? '';
                $device_logs->tank_count = $request->post('tank_count') ?? '';
                $device_logs->agitator_tape = $request->post('agitator_tape') ?? '';
                $device_logs->injectTape = $request->post('injectTape') ?? '';

                $device_logs->valve_fitting = $request->post('valve_fitting') ?? '';
                $device_logs->install_date = $request->post('install_date') ?? '';
                $device_logs->prog_folder = $request->post('prog_folder') ?? '';
                $device_logs->install_actuator = $request->post('install_actuator') ?? '';
                $device_logs->panel = $request->post('panel') ?? '';
                $device_logs->install_done = $request->post('install_done') ?? '';
                $device_logs->sales_person = $request->post('sales_person') ?? '';
                $device_logs->location = $request->post('location') ?? '';
                $device_logs->customized = $request->post('customized') ?? '';
                $device_logs->note = $request->post('note') ?? '';

                $device_logs->created_by = $request->post('created_by') ?? '';
                $device_logs->created_at = $request->post('created_at') ?? '';

                $device_logs->created_by = Auth::user()->id;
                $device_logs->created_at = date('Y-m-d H:i:s');

                // //////////////////////////////
                // $device_logs->save();

                if ($mode != 2) {  // check save only
                    // //////////////////////////////image save database and local folder
                    if (!empty($request->file('upload'))) {  // check file input empty
                        $validation = Validator::make($request->all(), [
                            'upload' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
                        ]);
                        if ($validation->passes()) {
                            $image = $request->file('upload');
                            $new_name = rand() . '_device_logs.' . $image->getClientOriginalExtension();
                            // dd($new_name);
                            $image->move(public_path('images'), $new_name);
                        } else {
                            return response()->json([
                                'message' => $validation->errors()->all(),
                                'uploaded_image' => '',
                                'class_name' => 'alert-danger'
                            ]);
                        }

                        $device_logs->image = $new_name ?? '';
                    }
                    // ////////////////////////////////////
                    $result = $device_logs->save();
                } else {  // for update query

                    if (!empty($request->file('upload'))) {  // check file input empty
                        $fileName = $request->post('image_exist');
                        if (!empty($fileName)) {
                            $imagePath = public_path('images/' . $fileName);

                            if (file_exists($imagePath)) {
                                unlink($imagePath);  // deletes the file
                            }
                        }

                        // /////////////////////
                        $validation = Validator::make($request->all(), [
                            'upload' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
                        ]);
                        if ($validation->passes()) {
                            $image = $request->file('upload');
                            $new_name = rand() . '_device_logs.' . $image->getClientOriginalExtension();
                            // dd($new_name);
                            $image->move(public_path('images'), $new_name);
                            // return response()->json([
                            //  'message'   => 'Image Upload Successfully',
                            // //  'uploaded_image' => '<img src="/images/'.$new_name.'" class="img-thumbnail" width="300" />',
                            //  'class_name'  => 'alert-success'
                            // ]);
                        } else {
                            return response()->json([
                                'message' => $validation->errors()->all(),
                                'uploaded_image' => '',
                                'class_name' => 'alert-danger'
                            ]);
                        }
                    } else {
                        $new_name = $request->post('image_exist') ?? '';
                    }
                    $result = device_logs::where('user_id', $log_id)->update([
                        'imei_id' => $log_id ?? 1,
                        'image' => $new_name ?? '',
                        'gsm_module' => $request->post('gsm_module') ?? '',
                        'gsm_module' => $request->post('gsm_module') ?? '',
                        'sim_scope' => $request->post('sim_scope') ?? '',
                        'lora_module' => $request->post('lora_module') ?? '',
                        'lora_freq' => $request->post('lora_freq') ?? '',
                        'lora_channel' => $request->post('lora_channel') ?? '',
                        'lora_SF' => $request->post('lora_SF') ?? '',
                        'lora_joint' => $request->post('lora_joint') ?? '',
                        'master' => $request->post('master') ?? '',
                        'master_serial_no' => $request->post('master_imei_no') ?? '',
                        'master_imei_no' => $request->post('imei') ?? '',
                        'master_os' => $request->post('master_os') ?? '',
                        'repeter' => $request->post('repeter') ?? '',
                        'valve_type' => $request->post('valve_type') ?? '',
                        'valve_qty' => $request->post('valve_qty') ?? '',
                        'butterfly_valve' => $request->post('butterfly_valve') ?? '',
                        'cable_mtr' => $request->post('cable_mtr') ?? '',
                        'cable_brand' => $request->post('cable_brand') ?? '',
                        'high_gain' => $request->post('high_gain') ?? '',
                        'solar_power' => $request->post('solar_power') ?? '',
                        'level_sensor' => $request->post('level_sensor') ?? '',
                        'presere_sensor' => $request->post('presere_sensor') ?? '',
                        'mois_sensor' => $request->post('mois_sensor') ?? '',
                        'npk' => $request->post('npk') ?? '',
                        'ec' => $request->post('ec') ?? '',
                        'ph' => $request->post('ph') ?? '',
                        'all_in' => $request->post('all_in') ?? '',
                        'filter' => $request->post('filter') ?? '',
                        'flow_sensor' => $request->post('flow_sensor') ?? '',
                        'weather_monitor' => $request->post('weather_monitor') ?? '',
                        'fert_sys' => $request->post('fert_sys') ?? '',
                        'tank_count' => $request->post('tank_count') ?? '',
                        'agitator_tape' => $request->post('agitator_tape') ?? '',
                        'injectTape' => $request->post('injectTape') ?? '',
                        'valve_fitting' => $request->post('valve_fitting') ?? '',
                        'install_date' => $request->post('install_date') ?? '',
                        'prog_folder' => $request->post('prog_folder') ?? '',
                        'install_actuator' => $request->post('install_actuator') ?? '',
                        'panel' => $request->post('panel') ?? '',
                        'install_done' => $request->post('install_done') ?? '',
                        'sales_person' => $request->post('sales_person') ?? '',
                        'location' => $request->post('location') ?? '',
                        'customized' => $request->post('customized') ?? '',
                        'note' => $request->post('note') ?? '',
                        'created_by' => $request->post('created_by') ?? '',
                        'created_at' => $request->post('created_at') ?? '',
                        'created_by' => Auth::user()->id,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                }
                if ($result) {  // checking valve type adding table
                    $valveTypes = $request->post('valveTypes');  // array or comma-separated string of types
                    $valveQtys = $request->post('valveQtys');    // array or comma-separated string of qtys
                    $ebTypes = $request->post('ebTypes');        // array or comma-separated string of types
                    $ebQtys = $request->post('ebQtys');          // array or comma-separated string of qtys

                    // Always delete old records for this device user to avoid duplicates
                    DB::table('device_valve_types')->where('user_id', $log_id)->delete();

                    // Insert inch valve rows
                    if (!empty($valveTypes)) {
                        $vTypes = is_array($valveTypes) ? $valveTypes : explode(',', $valveTypes);
                        $vQtys  = !empty($valveQtys) ? (is_array($valveQtys) ? $valveQtys : explode(',', $valveQtys)) : [];
                        $data = [];
                        for ($i = 0; $i < count($vTypes); $i++) {
                            $name = trim($vTypes[$i] ?? '');
                            if ($name !== '' && $name !== '--Select--') {
                                $data[] = [
                                    'user_id' => $log_id,
                                    'imei_id' => $log_id,
                                    'name_type' => $name,
                                    'value_qty' => isset($vQtys[$i]) && trim($vQtys[$i]) !== '' ? trim($vQtys[$i]) : null,
                                    'status' => 1,
                                ];
                            }
                        }
                        if (!empty($data)) {
                            DB::table('device_valve_types')->insert($data);
                        }
                    }

                    // Insert EB power rows
                    if (!empty($ebTypes)) {
                        $eTypes = is_array($ebTypes) ? $ebTypes : explode(',', $ebTypes);
                        $eQtys  = !empty($ebQtys) ? (is_array($ebQtys) ? $ebQtys : explode(',', $ebQtys)) : [];
                        $data = [];
                        for ($i = 0; $i < count($eTypes); $i++) {
                            $name = trim($eTypes[$i] ?? '');
                            if ($name !== '' && $name !== '--Select--') {
                                $data[] = [
                                    'user_id' => $log_id,
                                    'imei_id' => $log_id,
                                    'name_type' => $name,
                                    'value_qty' => isset($eQtys[$i]) && trim($eQtys[$i]) !== '' ? trim($eQtys[$i]) : null,
                                    'status' => 2,
                                ];
                            }
                        }
                        if (!empty($data)) {
                            DB::table('device_valve_types')->insert($data);
                        }
                    }
                }

                // ///////////////////////
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
    public function deleteImeiList(Request $request)
    {
        // # Read POST data
        $id = $request->post('id');

        // $empdata = User::find($id);
        $users = User::findOrFail($id);

        // replicate (duplicate) the data
        $users_deleted = $users->replicate();
        $users_deleted->pid = $users->id;

        // make into array for mass assign.
        // make sure you activate $guarded in your Staff model
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

    // Modal function
    public function submitAddImei(Request $request)
    {
        $request->validate([
            'imei' => 'required',
            'note' => 'required',
        ]);

        $contact = new imei_registration;
        $contact->imei = $request->imei;
        $contact->note = $request->note;
        $contact->created_by = Auth::user()->id;
        $contact->created_at = date('Y-m-d H:i:s');

        $contact->save();

        return response()->json(['success' => 'Successfully']);
    }

    // https://stackoverflow.com/questions/52941826/how-to-save-multiple-row-forms-in-laravel-using-ajax

    public function submitAddImeiMany(Request $request)
    {
        //
        $rules = [];
        foreach ($request->input('pr_item') as $key => $value) {
            $rules["pr_item.{$key}"] = 'required';
            $rules["pr_unit.{$key}"] = 'required|unique:imei_registration,imei';
            $rules["pr_desc.{$key}"] = 'required';
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->passes()) {
            foreach ($request->input('pr_item') as $key => $value) {
                $Record = new imei_registration;
                $Record->imei = $request->get('pr_unit')[$key];
                $Record->note = $request->get('pr_desc')[$key];
                $Record->created_by = Auth::user()->id;
                $Record->created_at = date('Y-m-d H:i:s');
                $Record->updated_by = Auth::user()->id;
                $Record->updated_at = date('Y-m-d H:i:s');
                $Record->save();
            }
            return response()->json(['success' => 'done']);
        } else {
            // return response()->json(['error'=>$validator->errors()->all()]);}
            return response()->json(['error' => 'error']);
        }
    }

    // Modal function
    public function addUpdateUserDevice(Request $request)
    {
        $request->validate([
            'device_imei' => 'required',
            'device_firstname' => 'required',
        ]);

        $imei = trim($request->device_imei);
        $confirmAddImei = $request->post('confirm_add_imei', 0);

        // Check if IMEI exists in imei_registration
        $existsInRegistration = imei_registration::where('imei', $imei)->exists();

        if (!$existsInRegistration) {
            if (!$confirmAddImei) {
                return response()->json([
                    'status' => 'imei_not_found',
                    'message' => 'The imei not available in our database, and it looks like new unit, shall we add it?',
                ]);
            } else {
                // User confirmed: insert new IMEI in imei_registration
                $newImei = new imei_registration;
                $newImei->imei = $imei;
                $newImei->status = 'active';
                $newImei->log_days = 10;
                $newImei->note = 'Auto registered from User Device for ' . $request->device_firstname;
                $newImei->created_by = Auth::user()->id;
                $newImei->created_at = date('Y-m-d H:i:s');
                $newImei->save();
            }
        }

        if ($request->modes != 2) {
            $device = new device_assign_users;
            $device->username = $request->device_firstname;
            $device->imei = $imei;
            $device->mobile = $request->device_mobile;
            $device->city = $request->device_city;
            $device->village = $request->device_village;
            $device->status = 1;
            $device->created_by = Auth::user()->id;
            $device->created_at = date('Y-m-d H:i:s');

            $device->save();

            return response()->json(['success' => 'Successfully']);
        } else {
            $empdata = device_assign_users::find($request->dev_usr_id);

            $response = [];
            if (!empty($empdata)) {
                $updata['username'] = $request->device_firstname;
                $updata['imei'] = $imei;
                $updata['mobile'] = $request->device_mobile;
                $updata['city'] = $request->device_city;
                $updata['village'] = $request->device_village;
                $updata['updated_by'] = Auth::user()->id;
                $updata['updated_at'] = date('Y-m-d H:i:s');

                if ($empdata->update($updata)) {
                    $response['msg'] = 1;
                    $response['success'] = 'Update successfully';
                } else {
                    $response['msg'] = 0;
                    $response['success'] = 'Record not updated';
                }
            } else {
                $response['msg'] = 0;
                $response['success'] = 'Invalid ID.';
            }

            return response()->json($response);
        }
    }
}
