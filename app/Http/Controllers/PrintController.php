<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\users_deleted;
use App\Models\imei_registration;
use App\Models\LRcategory;
use App\Models\LRvendor;
use App\Models\LRtickets;
use App\Models\LRstatusTicketLog;
use App\Models\LRcase;
use App\Models\LRticketsAssignLog;
use DataTables;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Crypt;

class PrintController extends Controller
{
    // session expire to redirect to login page
    public function __construct()
    {
        $this->middleware('auth');
    }


    // public function userAddImei()
    // {

    //     if (Auth::check() && Auth::user()->role_id == 2) {
    //         return view('printAddress');
    //     } else {
    //         return view('dashboard');
    //     }
    // }


    public function index()
    {
        // $categoris = LRcategory::where('parent_id', 0)->get();
        // $mobileNumber = LRvendor::select('mobile1', 'name', 'id')->get();
        
        // return view('printAddress', ["categoris" => $categoris, 
        //                              "mobileNumber" => $mobileNumber]);
    }

    
    public function printAddress()
    {
        $categoris = LRcategory::where('parent_id', 0)->get();
        $mobileNumber = LRvendor::select('mobile1', 'name', 'id')->get();
        
        $company_name= LRvendor::where('company', 1)->get();


        return view('printAddress', ["categoris" => $categoris, 
                                     "mobileNumber" => $mobileNumber,
                                    "company_name" => $company_name
                                ]);
    }


    public function printTickets(Request $request)
    {

            $query  = LRtickets::select(
                'LRtickets.*',
                'users.name as user_name',
                'updater.name as user_name_updated',
                'LRvendor.name as LRvendor_name',
                'LRvendor.companyname as LRvendor_companyname',
                'LRcase.name as case'
                )
                ->leftJoin('users', 'users.id', '=', 'LRtickets.created_by')
                ->leftJoin('users as updater', 'updater.id', '=', 'LRtickets.updated_by')
                ->leftJoin('LRvendor', 'LRvendor.id', '=', 'LRtickets.vendorid')
                ->leftJoin('LRcase', 'LRcase.id', '=', 'LRtickets.case');


                // if($request->filter == 'YES') {
                    
                // }

            if (!empty($request->from_date)) {
                $fromDate = $request->from_date;
                $toDate = $request->to_date;
                $query = $query->whereBetween(DB::raw('DATE(LRtickets.created_at)'), array($fromDate, $toDate));
            }

                
           
            // Filter by vendor
            if (!empty($request->filter_vendor) && $request->filter_vendor != 'ALL') {
                $query = $query->where('LRtickets.vendorid', $request->filter_vendor);
            }


            // Filter by status
            if (!empty($request->filter_status) && $request->filter_status != 'ALL') {
                $query = $query->where('LRtickets.status', $request->filter_status);
            }

            // Filter by delivery status
            if (!empty($request->filter_delivery_status) && $request->filter_delivery_status != 'ALL') {
                $query = $query->where('LRtickets.delivery_status', $request->filter_delivery_status);
            }

            // Filter by Case
            if (!empty($request->filter_case) && $request->filter_case != 'ALL') {
                $query = $query->where('LRtickets.case', $request->filter_case);
            }

            // Filter by Category
            if (!empty($request->filter_category) && $request->filter_category != 'ALL') {
                $query = $query->where(function($q) use ($request) {
                    $q->where('LRtickets.category', $request->filter_category)
                      ->orWhere('LRtickets.categoryid', $request->filter_category);
                });
            }

            // Filter by Model
            if (!empty($request->filter_model) && $request->filter_model != 'ALL') {
                $query = $query->where('LRtickets.model', $request->filter_model);
            }

            // Default filter if no specific filters are provided
            if (empty($request->filter_vendor) && empty($request->filter_status) && empty($request->filter_delivery_status) && empty($request->filter_case) && empty($request->filter_category) && empty($request->filter_model) && empty($request->from_date)) {
                $query = $query->where('LRtickets.status', 2)->where('LRtickets.delivery_status', 1);
            }


            // Retrieve the results
            $data = $query->get();


            
        if(request()->ajax())
             {
                
              return datatables()->of($data)

                    ->editColumn('idBtn', function ($row) {

                        // $ticketButton = $row->id;

                        $ticketButton = '<button type="button" class="btn btn-sm btn-outline-primary view-data" data-id="' . $row->id . '">' . $row->id . '</button>';

                        return $ticketButton;
                    })
                    
                    ->editColumn('status', function ($row) {
                        if ($row->status == 1) {
                            return '<span class="badge badge-primary" >Open</span>';
                        } elseif ($row->status == 2) {
                            return '<span class="badge badge-info" >Closed</span>';
                        } elseif ($row->status == 3) {
                            return '<span class="badge badge-success" >Assigned</span>';
                        } elseif ($row->status == 4) {
                            return '<span class="badge badge-warning" >QC</span>';
                        } else {
                            return "None";
                        }
                    })

                    ->editColumn('created_at', function ($row) {
                        return $row->created_at ? $row->created_at->format('d M, Y H:i:s') : '';
                    })

                    ->editColumn('updated_at', function ($row) {
                        if (empty($row->updated_by)) {
                            return 'NA';
                        } else {
                            return $row->updated_at ? $row->updated_at->format('d M, Y H:i:s') : 'NA';
                        }
                    })

                    ->editColumn('daysCount', function ($row) {
                        if (!empty($row->created_at) && !empty($row->updated_at)) {
                        $to = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $row->created_at);
                        $from = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $row->updated_at);
                        $diff_in_days = $to->diffInDays($from);
                        return round($diff_in_days);
                    } else {
                        return 'NA';
                    }
                    })

                    ->editColumn('user_name_updated', function ($row) {
                        return !empty($row->user_name_updated) ? $row->user_name_updated : 'NA';
                    })
                    ->rawColumns(['idBtn','status','created_at','updated_at'])
                    ->make(true);
             }



            $vendor_name = LRtickets::select(
                                                'LRtickets.*',
                                                'LRtickets.vendorid as LRticketsvendorid',
                                                'LRvendor.name as vendorname',
                                                'LRvendor.mobile1 as vendormobile1',
                                                'LRvendor.companyname as LRvendor_companyname'
                                                    )
                                                        ->leftJoin('LRvendor', 'LRvendor.id', '=', 'LRtickets.vendorid')
                                                        ->groupBy('LRtickets.vendorid')
                                                        ->get();

            $company_name= LRvendor::where('company', 1)->get();
            $cases = LRcase::where('status', 1)->get();
            $categories = LRtickets::select('category')->whereNotNull('category')->where('category', '!=', '')->distinct()->orderBy('category')->get();
            $models = LRtickets::select('model')->whereNotNull('model')->where('model', '!=', '')->distinct()->orderBy('model')->get();

        return view('printTickets', [
            "vendor_name" => $vendor_name,
            "company_name" => $company_name,
            "cases" => $cases,
            "categories" => $categories,
            "models" => $models
        ]);

    }


    public function printForm(Request $request)
    {
        $filter_company = $request->input('filter_company');
        $filter_vendor = $request->input('filter_vendor');
        $filter_status = $request->input('filter_status');
        $filter_delivery_status = $request->input('filter_delivery_status');

        $company = LRvendor::find($filter_company);
        $vendor = LRvendor::find($filter_vendor);
        
        $tickets = LRtickets::select('LRtickets.*', 'LRstatusTicketLog.name as ticket_status')
                            ->where('LRtickets.vendorid', $filter_vendor)
                            ->leftJoin('LRstatusTicketLog', 'LRstatusTicketLog.id', '=', 'LRtickets.status');

        if (!empty($filter_status) && $filter_status != 'ALL') {
            $tickets = $tickets->where('LRtickets.status', $filter_status);
        }

        if (!empty($filter_delivery_status) && $filter_delivery_status != 'ALL') {
            $tickets = $tickets->where('LRtickets.delivery_status', $filter_delivery_status);
        }

        if (!empty($request->filter_case) && $request->filter_case != 'ALL') {
            $tickets = $tickets->where('LRtickets.case', $request->filter_case);
        }

        if (!empty($request->filter_category) && $request->filter_category != 'ALL') {
            $tickets = $tickets->where(function($q) use ($request) {
                $q->where('LRtickets.category', $request->filter_category)
                  ->orWhere('LRtickets.categoryid', $request->filter_category);
            });
        }

        if (!empty($request->filter_model) && $request->filter_model != 'ALL') {
            $tickets = $tickets->where('LRtickets.model', $request->filter_model);
        }

        if (!empty($request->from_date)) {
            $fromDate = $request->from_date;
            $toDate = $request->to_date;
            $tickets = $tickets->whereBetween(DB::raw('DATE(LRtickets.created_at)'), array($fromDate, $toDate));
        }

        $tickets = $tickets->get();

        return view('printAck', compact('company', 'vendor', 'tickets'));
    }

     
    public function getTicketsListDataByStatus(Request $request)
    {
      $employees = LRtickets::select(
        'LRtickets.*',
        'users.name as user_name',
        'LRvendor.name as LRvendor_name',
        'LRvendor.companyname as LRvendor_companyname',
        'LRcategory.name as LRvendor_categorysub2name',
        'LRcategory.id as LRvendor_categorysub2nameid',
        'LRcase.name as case'
    )
        ->leftJoin('users', 'users.id', '=', 'LRtickets.created_by')
        ->leftJoin('LRvendor', 'LRvendor.id', '=', 'LRtickets.vendorid')
        ->leftJoin('LRcategory', 'LRcategory.id', '=', 'LRtickets.categoryid')
        ->leftJoin('LRcase', 'LRcase.id', '=', 'LRtickets.case')
        // ->whereNot('LRtickets.status', 3)
        ->where('LRtickets.status', 1)
        ->get();

      $employees = view("ticketList")->render();
      return response()->json(["html" => $employees], 200);
            

    }

    // public function getVendorListDataByStatus(Request $request)
    // {
    //     // dd($request->all());
    // $id = $request->post('filter_status');
    // // $vendorList = LRvendor::get()->where('id',$id);
    // if ($id != 100) {// vendor condition list show
    //             $vendorList = LRtickets::select(
    //                                                 'LRtickets.*',
    //                                                 'LRvendor.name as LRvendor_name',
    //                                                 'LRvendor.id as LRvendor_id',
    //                                                 'LRvendor.companyname as LRvendor_companyname',
    //                                             )
    //                                                 ->leftJoin('LRvendor', 'LRvendor.id', '=', 'LRtickets.vendorid')
    //                                                 // ->whereNot('LRtickets.status', 3)
    //                                                 ->where('LRtickets.status', $id)
    //                                                 ->groupBy('LRvendor.name')
    //                                                 ->get();
    // } else {// vendor all show
    //             $vendorList = DB::table('LRvendor')
    //                                             ->select('name as LRvendor_name', 'id as LRvendor_id')
    //                                             ->groupBy('name')
    //                                             ->orderBy('name', 'ASC')
    //                                             ->get(); 
    // }   
    //     $html='';
    //     if(!empty($vendorList)){
    //     $html .= "<option value=''>--Select Vendor --</option>";
    //     foreach($vendorList as $v_list){ //this loop will continue for each instance of the club
    //         // $mainid = $v_list->LRvendor_name;   
    //         $html .= "<option value='" . $v_list->LRvendor_id . "'>" . $v_list->LRvendor_name . "</option>";
    //     }
    //    }else {
    //     $html .= "<option value=''> Data Empty...!</option>";
    //    }
    //    echo $html;
    // // exit;
    // }

    // https://www.positronx.io/create-autocomplete-search-in-laravel-with-typeahead-js/
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

                        $response['success'] = 1;
                    } else {
                        $response['success'] = 0;
                    }

                    return response()->json($response);
            }




             // Manage Delivery Ticket
    public function manageDelivery(Request $request)
    {

        $logNote = $request->delivery_handover.'-'.$request->delivery_contact.'-'.$request->delivery_note;
        $created_by = Auth::user()->id;
        $created_at = date('Y-m-d H:i:s');

        $request->validate([
            'delivery_mode' => 'required', 
            'ticketid' => 'required', 
            'delivery_handover' => 'required', 
            'delivery_note' => 'required', 
        ]);
        foreach ($request->ticketid as $key => $value) {
                $data = array(  
                    "delivery_mode" => $request->delivery_mode,
                    "delivery_handover" => $request->delivery_handover,
                    "delivery_contact" => $request->delivery_contact,
                    "delivery_note" => $request->delivery_note,
                    "delivery_status" => 2,
                    "updated_by"=>$created_by,
                    "updated_at"=>$created_at,
                );    
                LRtickets::where('id',$request->ticketid[$key])->update($data); 
                }

              ///////////01-07-2025
                $assigned_by = "NA";
                $assigned_by = User::where('id', $created_by)->first()->name ?? 'NA';
                //////////
        foreach ($request->ticketid as $key => $value) {
                $dataLog = array(  
                    "userid"=> $created_by,
                    "ticketid"=>$request->ticketid[$key],
                    "priority"=>1,
                    "created_by"=>$created_by,
                    "created_at"=>$created_at,
                    "updated_by"=>$created_by,
                    "updated_at"=>$created_at,
                    "assigned_by"=> $assigned_by,
                    "assigned_at"=> $created_at,
                    "solution"=>$logNote,
                    "status"=>6,
                    "active"=>1,
                );    
                DB::table('LRticketsAssignLog')->insert($dataLog);
                }

            return response()->json(['success' => 'done']);
    if ($validator->fails()):
            return response()->json(['error' => $validator->errors()->all()]);
        endif;
    }




}