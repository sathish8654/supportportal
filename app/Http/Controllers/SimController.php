<?php
namespace App\Http\Controllers;

use App\Imports\RiotSimImport;
use App\Models\imei_registration;
use App\Models\LRiotSim;
use App\Models\LRiotSim_payment_history;
use App\Models\LRstatusSim;
use App\Models\User;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Response;
use Carbon\Carbon;

// 1-Airtel,2-BSNL,3-JIO

class SimController extends Controller
{
    // session expire to redirect to login page
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function usersim(Request $request)
    {
        if (Auth::check() && Auth::user()->role_id == 2) {

            $simCount          = LRiotSim::all()->count();
            $simStatusActive   = LRiotSim::where('simStatus', '=', 1)->count();
            $simStatusInActive = LRiotSim::where('simStatus', '=', 2)->count();
            $paymentPaid       = LRiotSim::where('payment', '=', 1)->count();
            $paymentNotPaid    = LRiotSim::where('payment', '=', 2)->count();
            $kycStatusPending  = LRiotSim::where('kycStatus', '=', 2)->where('simStatus', '=', 1)->count();
            $imeiPending       = LRiotSim::where('imei', '=', '')->orWhereNull('imei')->orWhere('imei', '=', '0')->count();

            $overDue = now()->toDateTimeString();
            $overDue = LRiotSim::where('renewalDate', '<=', $overDue)->where('simStatus', 1)->count();

            $simStatus    = LRstatusSim::where('status', 1)->get();
            $date         = now()->addDays(31)->toDateTimeString();
            $dueDaysCount = LRiotSim::where("renewalDate", '<=', $date)->where('simStatus', 1)->count();

            return view('usersim', compact('simCount',
                'simStatusActive',
                'simStatusInActive',
                'paymentPaid',
                'paymentNotPaid',
                'kycStatusPending',
                'dueDaysCount',
                'imeiPending',
                'simStatus',
                'overDue'));
        } else {
            return view('dashboard');
        }
    }

    // DataTable data
    public function getUserSimData(Request $request)
    {
        $overDue = now()->toDateTimeString();

        $employees = DB::table('LRiotSim')
            ->select('LRiotSim.*', 'login.last_connect', 'LRstatusSim.name as simStatus')
            ->leftJoin(DB::raw('(SELECT imei, MAX(id) AS max_id, `time` AS last_connect FROM v2_asetss GROUP BY imei) AS login'), 'login.imei', '=', 'LRiotSim.imei')
            ->leftJoin('LRstatusSim', 'LRstatusSim.id', '=', 'LRiotSim.simStatus');
        // ->where('LRiotSim.renewalDate', '<=', $overDue) // To check / compare the over due

        if (! empty($request->payment)) {
            // $employees = $employees->where('LRiotSim.payment', $request->payment);
            $employees = $employees->where('LRiotSim.renewalDate', '<=', $overDue)->where('simStatus', 1);
        }

        if (! empty($request->kycStatusPending)) {
            $employees = $employees->where('LRiotSim.kycStatus', $request->kycStatusPending)->where('simStatus', '=', 1);
        }

        $employees = $employees->get();

        return Datatables::of($employees)
            ->addIndexColumn()

            ->editColumn('activateDate', function ($row) {
                if ($row->activateDate === null) {
                    return 'NA';
                }
                return date('d-m-Y', strtotime($row->activateDate));
            })

            ->editColumn('renewalDate', function ($row) {
                if ($row->renewalDate === null) {
                    return 'NA';
                }
                return date('d-m-Y', strtotime($row->renewalDate));
            })

            ->editColumn('last_connect', function ($row) {
                if ($row->last_connect) {
                    return date('d-m-Y H:i:s', strtotime($row->last_connect));
                } else {
                    return "NOT CONNECTED";
                }

            })

            ->addColumn('renew', function ($row) {

                if ($row->simStatus == 'Suspend') {
                    return 'NA';
                } else {
                    if ($row->renew == 1) {
                    return 'Renew';
                } elseif ($row->renew == 2) {
                    return '<span style="color: red;">Not-renew</span>';
                } else {
                    return 'NA';
                }
                }

                
            })

            ->addColumn('simOperatorName', function ($row) {

                if ($row->simOperator == 1) {
                    return 'AirTel';
                } elseif ($row->simOperator == 2) {
                    return 'BSNL';
                } elseif ($row->simOperator == 3) {
                    return 'JIO';
                } else {
                    return 'Other';
                }
            })

            ->addColumn('kycStatus', function ($row) {

                if ($row->kycStatus == 1) {
                    return "Updated";
                } elseif ($row->kycStatus == 2) {
                    return "Pending";
                }
            })
            ->addColumn('payment', function ($row) {

                if ($row->payment == 1) {
                    return "Paid";
                } elseif ($row->payment == 2) {
                    return "Pending";
                }
            })
            ->addColumn('dueDays', function ($row) {

                if ($row->simStatus == 'Suspend') {
                    return 'NA';
                } else {

                if ($row->renewalDate === null) {
                    return 'NA';
                } elseif ($row->simStatus === 'Terminated') {
                    return 'NA';
                } else {
                    $date        = today()->format('Y-m-d');
                    $renewalDate = $row->renewalDate;

                    if ($date >= $renewalDate) {
                        $dueDays = '<span style="color: red;">Over Due ' . round(now()->diffInDays($row->renewalDate)) . ' days</span>';
                    } else {
                        $dueDays = round(now()->diffInDays($row->renewalDate));
                    }
                    return $dueDays;
                }
                 }
            })

            ->addColumn('action', function ($row) {
                $user = Auth::user();
                $action = '';

                // Check Edit Permission
                if ($user->hasMenuPermission('Users', 'usersim', 'edit')) {
                    $action .= "<button class='btn btn-sm btn-outline-success updateUser me-1' data-id='" . $row->id . "' data-bs-toggle='modal' data-bs-target='#updateModal' title='Edit'><i class='fa fa-edit'></i></button>";
                } 
                // Check View Permission (if no Edit)
                elseif ($user->hasMenuPermission('Users', 'usersim', 'view')) {
                    $action .= "<button class='btn btn-sm btn-outline-primary viewUser me-1' data-id='" . $row->id . "' data-bs-toggle='modal' data-bs-target='#updateModal' data-view-only='1' title='View'><i class='fa fa-eye'></i></button>";
                }

                // Check Delete Permission
                if ($user->hasMenuPermission('Users', 'usersim', 'delete')) {
                    $action .= "<button class='btn btn-sm btn-outline-danger deleteUserSim' data-id='" . $row->id . "' title='Delete'><i class='fa fa-trash'></i></button>";
                }

                return $action;
            })

            ->rawColumns(['dueDays', 'action', 'renew'])

            ->make();
    }

// excel file upload
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls|max:2048', // Validate file type and size
        ]);

        $file = $request->file('file');

        try {
            // Excel::import(new RiotSimImport(), $file); // Handle import using Excel import
            $import = new RiotSimImport();
            Excel::import($import, $file);
            $importedRowCount = $import->getRowCount();

            return redirect()->back()->with('success', 'Successfully imported ' . $importedRowCount . ' records.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // Read User SIM record by ID for modal
    public function getUserSimList(Request $request)
    {

        //# Read POST data
        $id = $request->post('id');

        $empdata = LRiotSim::find($id);

        $created_by_name = User::where('id', $empdata->created_by)->first()->name ?? $empdata->created_by;
        $updated_by_name = User::where('id', $empdata->updated_by)->first()->name ?? $empdata->updated_by;

        $response = [];
        if (! empty($empdata)) {

            $response['simStatus']         = $empdata->simStatus;
            $response['renew']             = $empdata->renew;
            $response['cost']              = $empdata->cost;
            $response['simNo']             = $empdata->simNo;
            $response['simPhoneNumber']    = $empdata->simPhoneNumber;
            $response['simOperator']       = $empdata->simOperator;
            $response['activateDate']      = $empdata->activateDate;
            $response['custName']          = $empdata->custName;
            $response['custAadharName']    = $empdata->custAadharName;
            $response['custContactNumber'] = $empdata->custContactNumber;
            $response['custAadharNumber']  = $empdata->custAadharNumber;
            $response['custAddress']       = $empdata->custAddress;
            $response['imei']              = $empdata->imei;
            $response['unitSerialNumber']  = $empdata->unitSerialNumber;
            $response['kycStatus']         = $empdata->kycStatus;
            $response['payment']           = $empdata->payment;
            $response['careOff']           = $empdata->careOff;
            $response['note']              = $empdata->note;
            $response['renewalDate']       = $empdata->renewalDate;
            $response['created_by']        = $created_by_name;
            $response['created_at']        = $empdata->created_at->format('d-m-Y H:i:s');
            $response['updated_by']        = $updated_by_name;
            $response['updated_at']        = $empdata->updated_at->format('d-m-Y H:i:s');

            $response['success'] = 1;
        } else {
            $response['success'] = 0;
        }

        return response()->json($response);
    }

    // Add New record
    public function addNewSim(Request $request)
    {

        $imeiNo  = $request->post('imeiNo');
        $imeiYes = $request->post('imeiYes');

        if ($imeiYes == 'Yes') {

            $imei         = $request->post('imei');
            $validateIMEI = imei_registration::where('imei', '=', $imei)->count();

            if ($validateIMEI == 1) {
                $request->validate([
                    'simNo'          => 'required|unique:LRiotSim,simNo',
                    'simPhoneNumber' => 'required|unique:LRiotSim,simPhoneNumber',
                    'simOperator'    => 'required',
                    'imei'           => 'required',
                    'renew'          => 'required',
                    'cost'           => 'required',
                ], [
                    'simNo.unique'          => 'SIM Number already exists!',
                    'simPhoneNumber.unique' => 'SIM Phone Number already exists!',
                ]);

                $contact = new LRiotSim;

                $contact->simStatus         = $request->simStatus;
                $contact->renew             = $request->renew;
                $contact->cost              = $request->cost;
                $contact->simNo             = $request->simNo;
                $contact->simPhoneNumber    = $request->simPhoneNumber;
                $contact->simOperator       = $request->simOperator;
                $contact->activateDate      = $request->activateDate;
                $contact->custName          = $request->custName;
                $contact->custAadharName    = $request->custAadharName;
                $contact->custContactNumber = $request->custContactNumber;
                $contact->custAadharNumber  = $request->custAadharNumber;
                $contact->custAddress       = $request->custAddress;
                $contact->imei              = $request->imei;
                $contact->unitSerialNumber  = $request->unitSerialNumber;
                $contact->kycStatus         = $request->kycStatus;
                $contact->payment           = $request->payment;
                $contact->careOff           = $request->careOff;
                $contact->note              = $request->note;
                $contact->renewalDate       = $request->renewalDate;
                $contact->created_by        = Auth::user()->id;
                $contact->created_at        = date('Y-m-d H:i:s');

                $contact->save();
                return response()->json(['message' => 'Record created successfully']);
            } else {
                $returnData = ['error' => 'Invalid IMEI'];
                return Response::json($returnData, 500);
            }
        }

        if ($imeiNo == 'No') {
            $request->validate([
                'simNo'          => 'required|unique:LRiotSim,simNo',
                'simPhoneNumber' => 'required|unique:LRiotSim,simPhoneNumber',
                'simOperator'    => 'required',
                'renew'          => 'required',
                'cost'           => 'required',
            ], [
                'simNo.unique'          => 'SIM Number already exists!',
                'simPhoneNumber.unique' => 'SIM Phone Number already exists!',
            ]);

            $contact = new LRiotSim;

            $contact->simStatus         = $request->simStatus;
            $contact->renew             = $request->renew;
            $contact->cost              = $request->cost;
            $contact->simNo             = $request->simNo;
            $contact->simPhoneNumber    = $request->simPhoneNumber;
            $contact->simOperator       = $request->simOperator;
            $contact->activateDate      = $request->activateDate;
            $contact->custName          = $request->custName;
            $contact->custAadharName    = $request->custAadharName;
            $contact->custContactNumber = $request->custContactNumber;
            $contact->custAadharNumber  = $request->custAadharNumber;
            $contact->custAddress       = $request->custAddress;
            $contact->imei              = $request->imei;
            $contact->unitSerialNumber  = $request->unitSerialNumber;
            $contact->kycStatus         = $request->kycStatus;
            $contact->payment           = $request->payment;
            $contact->careOff           = $request->careOff;
            $contact->note              = $request->note;
            $contact->renewalDate       = $request->renewalDate;
            $contact->created_by        = Auth::user()->id;
            $contact->created_at        = date('Y-m-d H:i:s');

            $contact->save();
            return response()->json(['message' => 'Record created successfully']);
        }

    }

    // Update Employee record
    public function updateSimInfo(Request $request)
    {
        $imei = $request->post('imei');
        if ($imei == '') {
            $validateIMEI = 1;

            $id = $request->post('id');

            $request->validate([
                "simNo"          => 'required|unique:LRiotSim,simNo,' . $id . ',id',
                "simPhoneNumber" => 'required|unique:LRiotSim,simPhoneNumber,' . $id . ',id',
                'simOperator'    => 'required',
            ]);
        } else {
            $validateIMEI = imei_registration::where('imei', '=', $imei)->count();

            $id = $request->post('id');

            $request->validate([
                "simNo"          => 'required|unique:LRiotSim,simNo,' . $id . ',id',
                "simPhoneNumber" => 'required|unique:LRiotSim,simPhoneNumber,' . $id . ',id',
                'simOperator'    => 'required',
                'imei'           => 'required',
            ]);
        }

        if ($validateIMEI == 1) {

            ## Read POST data

            $empdata  = LRiotSim::find($id);
            $response = [];
            if (! empty($empdata)) {

                $updata['simStatus']         = $request->post('simStatus');
                $updata['renew']             = $request->post('renew');
                $updata['cost']              = $request->post('cost');
                $updata['simNo']             = $request->post('simNo');
                $updata['simPhoneNumber']    = $request->post('simPhoneNumber');
                $updata['simOperator']       = $request->post('simOperator');
                $updata['activateDate']      = $request->post('activateDate');
                $updata['custName']          = $request->post('custName');
                $updata['custAadharName']    = $request->post('custAadharName');
                $updata['custContactNumber'] = $request->post('custContactNumber');
                $updata['custAadharNumber']  = $request->post('custAadharNumber');
                $updata['custAddress']       = $request->post('custAddress');
                $updata['imei']              = $request->post('imei');
                $updata['unitSerialNumber']  = $request->post('unitSerialNumber');
                $updata['kycStatus']         = $request->post('kycStatus');
                $updata['payment']           = $request->post('payment');
                $updata['careOff']           = $request->post('careOff');
                $updata['note']              = $request->post('note');
                $updata['renewalDate']       = $request->post('renewalDate');
                $updata['updated_by']        = Auth::user()->id;
                $updata['updated_at']        = date('Y-m-d H:i:s');

                $empdata->update($updata);

                return response()->json(['message' => 'Updated Successfully!']);

            } else {
                $returnData = ['message' => 'Invalid Data'];
                return Response::json($returnData, 500);
            }
        } else {
            $returnData = ['message' => 'Invalid IMEI'];
            return Response::json($returnData, 500);
        }
    }

    // Delete User SIM row
    public function deleteUserSimList(Request $request)
    {
        //# Read POST data
        $id    = $request->post('id');
        $users = LRiotSim::findOrFail($id);
        if ($users->delete()) {
            return response()->json(['message' => 'Deleted Successfully!']);
        } else {
            return response()->json(['message' => 'Failed! Please check!']);
        }
    }

    public function getSiminfo(Request $request)
    {
        $simPhoneNumber          = $request->post('simPhoneNumber');
        $simNumber               = LRiotSim::select('renewalDate', 'custContactNumber')->where('simPhoneNumber', $simPhoneNumber)->get();
        $response                = [];
        $response['success']     = $simNumber->count();
        $response['renewalDate'] = $simNumber[0]->renewalDate;
        $response['contact']     = $simNumber[0]->custContactNumber;
        return response()->json($response);
    }

   
    public function getPaymentsPartial(Request $request)
    {


        $PaymentData = DB::table('LRiotSim_payment_history')
            ->select(
                'LRiotSim_payment_history.*', 
                'LRiotSim.custName as simName', 
                'LRsimOperator.name as simOperator', 
                'LRiotSim_payment_history.id as LRiotSim_payment_history_id',
                'LRiotSim_payment_history.created_by as payment_received', 
                'LRiotSim_payment_history.created_at as payment_received_on',
                'LRiotSim_payment_history.updated_by as payment_updated', 
                'LRiotSim_payment_history.updated_at as payment_updated_on',
                )
            ->leftJoin('LRiotSim', 'LRiotSim.simPhoneNumber', '=', 'LRiotSim_payment_history.sim_phone_number')
            ->leftJoin('LRsimOperator', 'LRsimOperator.id', '=', 'LRiotSim.simOperator')
            ->orderBy('paid_date', 'desc');


        $PaymentData = $PaymentData->get();
      
        $arrData=[];
    foreach ($PaymentData as $key=> $payment) {


        if ($payment->payment_received == null) {
                    $username = "NA";
                } else {
                    $username = User::where('id', $payment->payment_received)->first()->name;
                    $payment_received_on = Carbon::parse($payment->payment_received_on)->format('d-m-Y H:i:s');
                    $payment_received = "$username - $payment_received_on";
                }




         if ($payment->payment_received == null) {
                    $username = "NA";
                } else {
                    $username = User::where('id', $payment->payment_received)->first()->name;
                    $payment_received_on = Carbon::parse($payment->payment_received_on)->format('d-m-Y H:i:s');
                    $payment_received = "$username - $payment_received_on";
                }



            switch ($payment->mode) {
                 case '1':
                 $mode= "Offline";
                 break;
                 case '2':
                 $mode= "Online";
                 break;
                 case '3':
                 $mode= "MobileApp";
                 break;
                                
                 default:
                 $mode= "NA";
                     break;
             }

              switch ($payment->status) {
                 case '1':
                 $status= "Success";
                 break;
                 case '2':
                 $status= "Failed";
                 break;
                 case '3':
                 $status= "Inprogress";
                 break;
                                
                 default:
                 $status= "NA";
                     break;
             }

                             // Update Button data-bs-target='#updateModal'
                $updateButton = "<button class='btn btn-sm btn-outline-primary' data-id='" . $payment->LRiotSim_payment_history_id . "' data-bs-toggle='modal' data-bs-target='#updateModal'><i class='fa fa-edit'></i></button>";
                // Delete Button
                $deleteButton = "<button class='btn btn-sm btn-outline-danger' data-id='" . $payment->LRiotSim_payment_history_id . "' ><i class='fa fa-trash'></i></button>";

                if (Auth::check() && Auth::user()->role_id == 2 && Auth::user()->current_team_id == 1) {
                    $actionButtons =  $updateButton . " " . $deleteButton;
                } else {
                    $actionButtons =  $updateButton;
                }
                            

            $arrData[] = [
                    'paidDate'          => Carbon::parse($payment->paid_date)->format('d-m-Y'),
                    'simPhoneNumber'    => $payment->sim_phone_number,
                    'operator'          => $payment->simOperator,
                    'simOwner'          => $payment->sim_phone_number,
                    'contact'           => $payment->usercontact_number,
                    'name'              => $payment->simName,
                    'fromDate'          => Carbon::parse($payment->from_date)->format('d-m-Y'),
                    'toDate'          => Carbon::parse($payment->to_date)->format('d-m-Y'),
                    'cost'              => $payment->cost,
                    'txId'              => $payment->transaction_id,
                    'note'              => $payment->note,
                    'mode'              => $mode,
                    'status'            => $status,
                    'handledBy'         => $payment_received,
                    'Action'            => $actionButtons,
                    ];
            }

        return view('partials.user_sim_payment_rows', compact('arrData'))->render(); // Blade partial
    }




    // DataTable data
    public function usersimPayment(Request $request)
    {
         // add module dropdown
        $simNumber = LRiotSim::select('simNo', 'simPhoneNumber', 'custName', 'custContactNumber', 'simStatus', 'id');
        $simNumber =  $simNumber->get();
        return view('usersimPayment', compact('simNumber'));
    }


    public function getusersimPaymentData(Request $request)
    {
        $PaymentData = DB::table('LRiotSim_payment_history')
            ->select('LRiotSim_payment_history.*', 'LRiotSim.custName as simName', 'LRsimOperator.name as simOperator', 'LRiotSim_payment_history.created_by as payment_received', 'LRiotSim_payment_history.created_at as payment_received_on')
            ->leftJoin('LRiotSim', 'LRiotSim.simPhoneNumber', '=', 'LRiotSim_payment_history.sim_phone_number')
            ->leftJoin('LRsimOperator', 'LRsimOperator.id', '=', 'LRiotSim.simOperator')
            ->orderBy('paid_date', 'desc');

        if ($request->filled('from_date') && $request->filled('to_date')) {
             $fromDate = $request->from_date;
            $toDate = $request->to_date;
            $PaymentData = $PaymentData->whereBetween(DB::raw('DATE(LRiotSim_payment_history.paid_date)'), array($fromDate, $toDate));
            }

        $PaymentData = $PaymentData->get();

        return Datatables::of($PaymentData)
            ->addIndexColumn()

            ->editColumn('paid_date', function ($row) {
                if ($row->paid_date === null) {
                    return 'NA';
                }
                return date('d-m-Y', strtotime($row->paid_date));
            })

            ->editColumn('from_date', function ($row) {
                if ($row->paid_date === null) {
                    return 'NA';
                }
                return date('d-m-Y', strtotime($row->from_date));
            })

            ->editColumn('to_date', function ($row) {
                if ($row->paid_date === null) {
                    return 'NA';
                }
                return date('d-m-Y', strtotime($row->to_date));
            })

            ->addColumn('name', function ($row) {

                $username = User::where('mobile_no', $row->usercontact_number)->count();
                $LRiotSim = LRiotSim::where('custContactNumber', $row->usercontact_number)->count();

                if ($username) {
                    $username = User::where('mobile_no', $row->usercontact_number)->first()->name;
                } elseif ($LRiotSim) {
                    $username = LRiotSim::where('custContactNumber', $row->usercontact_number)->first()->custName;
                } else {
                    $username = "NA";
                }
                return $username;
            })

            ->addColumn('mode', function ($row) {

                if ($row->mode == 1) {
                    return "Offline";
                } elseif ($row->mode == 2) {
                    return "Online";
                } elseif ($row->mode == 3) {
                    return "MobileApp";
                } else {
                    return "NA";
                }
            })

            ->addColumn('status', function ($row) {

                if ($row->status == 1) {
                    return "Success";
                } elseif ($row->status == 2) {
                    $status = '<span style="color: red;">Failed</span>';
                    return $status;
                } elseif ($row->status == 3) {
                    $status = '<span style="color: orange;">Inprogress</span>';
                    return $status;
                } else {
                    return "NA";
                }
            })

            ->addColumn('payment_received', function ($row) {

                $created_by_name = User::where('id', $row->payment_received)->first()->name ?? $empdata->payment_received;

                if ($created_by_name) {
                    $username = $created_by_name;
                } else {
                    $username = "NA";
                }
                return $username;
            })

            ->addColumn('action', function ($row) {
                $user = Auth::user();
                $action = '';

                // Check Edit Permission
                if ($user->hasMenuPermission('Users', 'usersimPayment', 'edit')) {
                    $action .= "<button class='btn btn-sm btn-outline-success updatePayment me-1' data-id='" . $row->id . "' data-bs-toggle='modal' data-bs-target='#updateModal' title='Edit'><i class='fa fa-edit'></i></button>";
                } 
                // Check View Permission (if no Edit)
                elseif ($user->hasMenuPermission('Users', 'usersimPayment', 'view')) {
                    $action .= "<button class='btn btn-sm btn-outline-primary viewPayment me-1' data-id='" . $row->id . "' data-bs-toggle='modal' data-bs-target='#updateModal' data-view-only='1' title='View'><i class='fa fa-eye'></i></button>";
                }

                // Check Delete Permission
                if ($user->hasMenuPermission('Users', 'usersimPayment', 'delete')) {
                    $action .= "<button class='btn btn-sm btn-outline-danger deletePayment' data-id='" . $row->id . "' title='Delete'><i class='fa fa-trash'></i></button>";
                }

                return $action;
            })

            ->rawColumns(['status', 'payment_received', 'action'])

            ->make();
    }

    // Read User SIM record by ID for modal
    public function getusersimPaymentList(Request $request)
    {
        $id      = $request->post('id');
        $empdata = LRiotSim_payment_history::find($id);

        $name = User::where('mobile_no', $empdata->usercontact_number)->first()->name ?? 'Unknown';
        $imei = User::where('mobile_no', $empdata->usercontact_number)->first()->imei ?? 'NA';
        $name = $name . ' - IMEI - ' . $imei;

        $created_by_name = User::where('id', $empdata->created_by)->first()->name ?? $empdata->created_by;
        $created_at      = $empdata->created_at->format('d-m-Y H:i:s');
        $created_by_name = $created_by_name . ' - ' . $created_at;

        $updated_by_name = User::where('id', $empdata->updated_by)->first()->name ?? $empdata->updated_by;
        $updated_at      = $empdata->updated_at->format('d-m-Y H:i:s');
        $updated_by_name = $updated_by_name . ' - ' . $updated_at;

        $sim_id_name = LRiotSim::where('simPhoneNumber', $empdata->sim_phone_number)->first()->custName ?? 'Unknown';

        $response = [];
        if (! empty($empdata)) {

            $response['id']             = $empdata->id;
            $response['simPhoneNumber'] = $empdata->sim_phone_number;
            $response['contact']        = $empdata->usercontact_number;
            $response['date']           = $empdata->paid_date;
            $response['from_date']      = $empdata->from_date;
            $response['to_date']        = $empdata->to_date;
            $response['tx_number']      = $empdata->transaction_id;
            $response['mode']           = $empdata->mode;
            $response['status']         = $empdata->status;
            $response['cost']           = $empdata->cost;
            $response['note']           = $empdata->note;
            $response['created_by']     = $created_by_name;
            $response['updated_by']     = $updated_by_name;
            $response['name']           = $name;
            $response['sim_id_name']    = $sim_id_name;

            $response['success'] = 1;
        } else {
            $response['success'] = 0;
        }
        return response()->json($response);
    }

    // Add new data / insert

    public function addNewPayment(Request $request)
    {

        $request->validate([
            'simPhoneNumber' => 'required|exists:LRiotSim,simPhoneNumber',
            'contact'        => 'required',
            'cost'           => 'required',
            'from_date'      => 'required',
            'to_date'        => 'required',
            'tx_number'      => 'required',
            'date'           => 'required',
            'mode'           => 'required',
            'status'         => 'required',
        ]);

        // Check if a record already exists with the same simPhoneNumber, to_date, and status in LRiotSim_payment_history
        $existingRecord = DB::table('LRiotSim_payment_history')
            ->where('sim_phone_number', $request->simPhoneNumber)
            ->where('to_date', $request->to_date)
            ->where('status', $request->status)
            ->first();

        if ($existingRecord) {
            return response()->json(['message' => 'SIM already renewed, please check!'], 404);
        }

        $create_sim_expire = LRiotSim::where('simPhoneNumber', $request->simPhoneNumber)->first();

        if ($create_sim_expire) {
            $contact = new LRiotSim_payment_history;

            $contact->paid_date          = $request->date;
            $contact->sim_phone_number   = $request->simPhoneNumber;
            $contact->usercontact_number = $request->contact;
            $contact->from_date          = $request->from_date;
            $contact->to_date            = $request->to_date;
            $contact->cost               = $request->cost;
            $contact->transaction_id     = $request->tx_number;
            $contact->note               = $request->note;
            $contact->mode               = $request->mode;
            $contact->status             = $request->status;
            $contact->created_by         = Auth::user()->id;
            $contact->created_at         = date('Y-m-d H:i:s');
            $contact->updated_by         = Auth::user()->id;
            $contact->updated_at         = date('Y-m-d H:i:s');
            $contact->save();

            if ($request->status == 1) {
                $update_sim_expire                    = LRiotSim::where('simPhoneNumber', $request->simPhoneNumber);
                $update_expire_payment['payment']     = 1;
                $update_expire_payment['simStatus']   = 1;
                $update_expire_payment['renewalDate'] = $request->to_date;
                $update_expire_payment['updated_by']  = Auth::user()->id;
                $update_expire_payment['updated_at']  = date('Y-m-d H:i:s');
                $update_sim_expire->update($update_expire_payment);
            }

                    // echo "<pre>";
                    // print_r($request->all());
                    // exit;

            return response()->json(['message' => 'Record created successfully']);

        } else {
            return response()->json(['error' => 'SIM number not found'], 404);
        }
    }

    // Update Employee record
    public function updatePaymentInfo(Request $request)
    {

        $id = $request->post('id');
        $request->validate([
            'id'             => 'required',
            'simPhoneNumber' => 'required',
            'contact'        => 'required',
            'from_date'      => 'required',
            'to_date'        => 'required',
            'tx_number'      => 'required',
            'date'           => 'required',
            'mode'           => 'required',
            'status'         => 'required',
        ]);

        ## Read POST data

        $empdata  = LRiotSim_payment_history::find($id);
        $response = [];
        if (! empty($empdata)) {

            $update['id']                 = $request->id;
            $update['sim_phone_number']   = $request->simPhoneNumber;
            $update['usercontact_number'] = $request->contact;
            $update['paid_date']          = $request->date;
            $update['from_date']          = $request->from_date;
            $update['to_date']            = $request->to_date;
            $update['transaction_id']     = $request->tx_number;
            $update['mode']               = $request->mode;
            $update['status']             = $request->status;
            $update['cost']               = $request->cost;
            $update['note']               = $request->note;
            $update['updated_by']         = Auth::user()->id;
            $update['updated_at']         = date('Y-m-d H:i:s');

            $empdata->update($update);

            if ($request->status == 1) {
                $update_sim_expire            = LRiotSim::where('simPhoneNumber', $request->simPhoneNumber);
                $update_expire['simStatus']   = 1;
                $update_expire['payment']     = 1;
                $update_expire['renewalDate'] = $request->to_date;
                $update_expire['updated_by']  = Auth::user()->id;
                $update_expire['updated_at']  = date('Y-m-d H:i:s');
                $update_sim_expire->update($update_expire);
            }

            return response()->json(['message' => 'Updated Successfully!']);

        } else {
            $returnData = ['message' => 'Invalid Data'];
            return Response::json($returnData, 500);
        }
    }

    // Delete User SIM row
    public function deletesimPaymentList(Request $request)
    {
        //# Read POST data
        $id    = $request->post('id');
        $users = LRiotSim_payment_history::findOrFail($id);
        if ($users->delete()) {
            return response()->json(['message' => 'Deleted Successfully!']);
        } else {
            return response()->json(['message' => 'Failed! Please check!']);
        }
    }

}
