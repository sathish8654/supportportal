<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\users_deleted;
use App\Models\imei_registration;
use App\Models\LRcase;
use App\Models\LRcategory;
use App\Models\LRproductsBatch;
use App\Models\LRproducts;
use App\Models\LRproductsLog;
use DataTables;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Carbon;

class ProductController  extends Controller
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

        public function index(Request $request)
    {
        $categoris = LRcategory::where('parent_id', 0)->where('status', 1)->get();
        $users = User::whereIn('role_id', ['2', '3'])->orderBy("name", "asc")->get();

        return view('productCreate', [
            "users" => $users,
            "categoris" => $categoris,
        ]);
    }


     // Create Ticket
    public function createProducts(Request $request)
    {

        $request->validate([
            'assignUserId'  => 'required'
        ]);

        $rules = [];
        foreach ($request->input('pr_item') as $key => $value) {
            $rules["pr_item.{$key}"] = 'required';
            $rules["product.{$key}"] = 'required';
            $rules["category.{$key}"] = 'required';
            $rules["qty.{$key}"] = 'required';
        }
        
        $validator = Validator::make($request->all(), $rules);

        if ($validator->passes()) {
            foreach ($request->input('pr_item') as $key => $value) {

                $categoryid = $request->get('product')[$key];

                $categories = LRcategory::from('LRcategory as c1')
                    ->select('c1.name as model', 'c1.id as model_id', 'c2.name as category')
                    ->leftJoin('LRcategory as c2', 'c1.parent_id', '=', 'c2.id')
                    ->where('c1.id', $categoryid)
                    ->get();

                $category_name = $categories[0]->category;
                $model_name = $categories[0]->model;
                $model_id = $categories[0]->model_id;

                $productsBatch = new LRproductsBatch;
                $productsBatch->category = $category_name;
                $productsBatch->model = $model_name;
                $productsBatch->model_id = $model_id;
                $productsBatch->qty = $request->get('qty')[$key];
                $productsBatch->note = $request->get('pr_desc')[$key];
                $productsBatch->assigned = $request->get('assignUserId')[$key];
                $productsBatch->created_by = Auth::user()->id;
                $productsBatch->created_at = date('Y-m-d H:i:s');
                $productsBatch->status = 1;
                $productsBatch->save();


                $qty = (int) $request->get('qty')[$key];

                    for ($i = 0; $i < $qty; $i++) {

                        $product = new LRproducts;
                        $product->batch_id = $productsBatch->id;
                        $product->status = 1;
                        $product->category = $category_name;
                        $product->model = $model_name;
                        $product->model_id = $model_id;
                        $product->note = $request->get('pr_desc')[$key];
                        $product->created_by = Auth::user()->id;
                        $product->created_at = date('Y-m-d H:i:s');
                        $product->save();

                        $productLog = new LRproductsLog;
                        $productLog->batch_id = $productsBatch->id;
                        $productLog->product_id = $product->id;
                        $productLog->assigned = $request->get('assignUserId')[$key];
                        $productLog->note = $request->get('pr_desc')[$key];
                        $productLog->active = 1;
                        $productLog->status = 1;
                        $productLog->created_by = Auth::user()->id;
                        $productLog->created_at = date('Y-m-d H:i:s');
                        $productLog->save();

                    }
            }
            return response()->json(['success' => 'done']);
        } else {
            return response()->json(['error' => $validator->errors()->all()]);
        }
    }


    public function batch(Request $request)
    {
        $result = DB::table('LRproductsBatch as b')
                                            ->select(
                                                'b.*',
                                                'createdUser.name as created_by_user',
                                                'updatedUser.name as updated_by_user',
                                                'assignedUser.name as assigned_user'
                                            )
                                            ->leftJoin('users as assignedUser', 'b.assigned', '=', 'assignedUser.id')
                                            ->leftJoin('users as createdUser', 'b.created_by', '=', 'createdUser.id')
                                            ->leftJoin('users as updatedUser', 'b.updated_by', '=', 'updatedUser.id');

   
         $filters['from_date']=$filters['to_date']=[];

        if (!empty($request->from_date)) {
            $fromDate = $request->from_date;
            $toDate = $request->to_date;
            $result = $result->whereBetween(DB::raw('DATE(LRproductsBatch.created_at)'), array($fromDate, $toDate));
            $filters['from_date']=$fromDate;
            $filters['to_date']=$toDate;
        }
       
              
        // dd($result->toSql(), $result->getBindings());
    
        $result = $result->get();
        // echo '<pre>';  print_r($result);exit;

        // Loop through the results
        $arrData=[];

    foreach ($result as $key=> $product) {
                            
                if (!empty($product->created_at) ) {

                $from = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', now());
                $to = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $product->created_at);
                $diff_in_days = $to->diffInDays($from);
                $daysCount = round($diff_in_days);
                } else {
                   $daysCount = 'NA';
                }


         //////////////////////////////////switch statement
             switch ($product->status) {
                 case '1':
                 $status= "Production";
                 break;
                 case '2':
                 $status= "Completed";
                 break;
                                  
                 default:
                 $status= "NA";
                     break;
             }

             $created_at = \Carbon\Carbon::parse($product->created_at);
             $date = $created_at->format('d-m-Y');
             $dateCreated = $created_at->format('d-m-Y H:i:s');
             $created_by_user = $product->created_by_user .' - '. $dateCreated;

            $arrData[] = [
                    'id' => $product->id,
                    'date' => $date,
                    'category' => $product->category,
                    'model' => $product->model,
                    'note' => $product->note,
                    'qty' => $product->qty,
                    'assigned_user' => $product->assigned_user,
                    'created_by_user' => $created_by_user,
                    'daysCount' => $daysCount,
                    'status' => $status,
                    ];
            }
        // echo '<pre>';print_r($arrData);exit;
        return view('productBatch', compact('arrData','filters'));
    }

    public function process(Request $request)
    {

     $result = DB::table('LRproductsLog as b')
                                            ->select(
                                                'b.*',
                                                'LRproducts.id as productId',
                                                'LRproducts.category as category',
                                                'LRproducts.model as model',
                                                'LRproductsBatch.created_at as date',
                                                'createdUser.name as created_by_user',
                                                'updatedUser.name as updated_by_user',
                                                'assignedUser.name as assigned_user'
                                            )
                                            ->leftJoin('LRproducts', 'b.product_id', '=', 'LRproducts.id')
                                            ->leftJoin('LRproductsBatch', 'b.batch_id', '=', 'LRproductsBatch.id')
                                            ->leftJoin('users as assignedUser', 'b.assigned', '=', 'assignedUser.id')
                                            ->leftJoin('users as createdUser', 'b.created_by', '=', 'createdUser.id')
                                            ->leftJoin('users as updatedUser', 'b.updated_by', '=', 'updatedUser.id')
                                            ->where('b.active', 1)
                                            ->whereIn('b.status', [1, 3]);

              
        // dd($result->toSql(), $result->getBindings());
    
        $result = $result->get();
        // echo '<pre>';  print_r($result);exit;

        // Loop through the results
        $arrData=[];

    foreach ($result as $key=> $product) {
        
             switch ($product->status) {
                 case '1':
                 $status= "Production";
                 break;
                 case '2':
                 $status= "Completed";
                 break;
                                  
                 default:
                 $status= "NA";
                     break;
             }

             $created_date = \Carbon\Carbon::parse($product->date);
             $date = $created_date->format('d-m-Y');

             $created_at = \Carbon\Carbon::parse($product->created_at);
             $created_at = $created_at->format('d-m-Y H:i:s');
             $created_by_user = $product->created_by_user .' - '. $created_at;


                if (!empty($product->date) ) {

                $from = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', now());
                $to = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $product->date);
                $diff_in_days = $to->diffInDays($from);
                $daysCount = round($diff_in_days);
                } else {
                   $daysCount = 'NA';
                }

            // Update Button data-bs-target='#updateModal'
            $updateButton = "<button  class='fa fa-edit updateProduct' data-id='" . $product->id . "' data-bs-toggle='modal' data-bs-target='#productUpdateModal' style='font-size:24px;color:green'></button>";
            // Delete Button
            $deleteButton = "<button class='fa fa-trash-o deleteProduct' data-id='" . $product->id . "' style='font-size:24px;color:red'></button>";

            if (Auth::check() && Auth::user()->role_id == 2 && Auth::user()->current_team_id == 1) {
                $actionButtons =  $updateButton . " " . $deleteButton;
            } else {
                $actionButtons =  $updateButton;
            }


            $arrData[] = [
                    'id' => $product->id,
                    'productId' => $product->productId,
                    'date' => $date,
                    'batch' => $product->batch_id,
                    'category' => $product->category,
                    'model' => $product->model,
                    'note' => $product->note,
                    'assigned_user' => $product->assigned_user,
                    'created_by_user' => $created_by_user,
                    'daysCount' => $daysCount,
                    'action' => $actionButtons,
                    ];
            }

        $users = User::whereIn('role_id', ['2', '3'])->orderBy("name", "asc")->get();

        // echo '<pre>';print_r($arrData);exit;
        return view('productProcess', compact('arrData', 'users'));
    }



     // Read Employee record by ID
    public function getAssignProduct(Request $request)
    {
        ## Read POST data
        $id        = $request->post('id');
        $productLog   = DB::table('LRproductsLog')->find($id);
        $products = DB::table('LRproducts')->find($productLog->product_id);

        $response = [];
        if (! empty($productLog)) {

            $response['id']         = $productLog->id;
            $response['productId']  = $products->id;
            $response['batch']      = $productLog->batch_id;
            $response['note']       = $products->note;
            $response['category']   = $products->category;
            $response['model']      = $products->model;
            $response['noteLog']   = $productLog->note;
            $response['assigned']     = $productLog->assigned;
            $response['status']     = $productLog->status;

            $response['success'] = 1;
        } else {
            $response['success'] = 0;
        }
        ///////////
       return response()->json([$response]);
    }


    
    public function submitAssignProduct(Request $request)
    {
        $id         = $request->input('id');
        $productLog = LRProductsLog::find($id);

        $assigned     = $request->input('assigned');
        $noteLog       = $request->input('noteLog');
        $status     = $request->input('status');
        $updated_by = Auth::user()->id;
        $updated_at = date('Y-m-d H:i:s');
        
        if ($productLog) {

            if ($productLog->status == $status && $productLog->assigned == $assigned)
            {
                $data = [
                        "note"       => $noteLog,
                        "updated_by"    => $updated_by,
                        "updated_at"    => $updated_at
                    ];
                $productLog->update($data);

            } else {
                $data = [
                        "assigned"      => $assigned,
                        "note"          => $noteLog,
                        "status"        => $status,
                        "updated_by"    => $updated_by,
                        "updated_at"    => $updated_at,
                        "active"        => 0,
                    ];
                $productLog->update($data);

                $dataInsert = [
                        "batch_id"      => $productLog->batch_id,
                        "product_id"    => $productLog->product_id,
                        "assigned"      => $assigned,
                        "status"        => $status,
                        "note"          => $noteLog,
                        "created_by"    => $updated_by,
                        "created_at"    => $updated_at,
                        "active"        => 1,
                    ];

                DB::table('LRproductsLog')->insert($dataInsert);
                }
                $response['success'] = 1;
                $response['msg']     = 'Update successfully';
            } else {
                $response['success'] = 0;
                $response['msg']     = 'Record not updated';
            }
        return response()->json($response);
    }


    public function qc(Request $request)
    {
        
         $result = DB::table('LRproductsLog as b')
                                            ->select(
                                                'b.*',
                                                'LRproducts.id as productId',
                                                'LRproducts.category as category',
                                                'LRproducts.model as model',
                                                'LRproductsBatch.created_at as date',
                                                'createdUser.name as created_by_user',
                                                'updatedUser.name as updated_by_user',
                                                'assignedUser.name as assigned_user'
                                            )
                                            ->leftJoin('LRproducts', 'b.product_id', '=', 'LRproducts.id')
                                            ->leftJoin('LRproductsBatch', 'b.batch_id', '=', 'LRproductsBatch.id')
                                            ->leftJoin('users as assignedUser', 'b.assigned', '=', 'assignedUser.id')
                                            ->leftJoin('users as createdUser', 'b.created_by', '=', 'createdUser.id')
                                            ->leftJoin('users as updatedUser', 'b.updated_by', '=', 'updatedUser.id')
                                            ->where('b.active', 1)
                                            ->where('b.status', 2);

        // dd($result->toSql(), $result->getBindings());
    
        $result = $result->get();
        // echo '<pre>';  print_r($result);exit;

        // Loop through the results
        $arrData=[];

    foreach ($result as $key=> $product) {
                        
         //////////////////////////////////switch statement
             switch ($product->status) {
                 case '1':
                 $status= "Production";
                 break;
                 case '2':
                 $status= "Completed";
                 break;
                                  
                 default:
                 $status= "NA";
                     break;
             }

             $created_date = \Carbon\Carbon::parse($product->date);
             $date = $created_date->format('d-m-Y');

             $created_at = \Carbon\Carbon::parse($product->created_at);
             $created_at = $created_at->format('d-m-Y H:i:s');
             $created_by_user = $product->created_by_user .' - '. $created_at;


                if (!empty($product->date) ) {

                $from = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', now());
                $to = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $product->date);
                $diff_in_days = $to->diffInDays($from);
                $daysCount = round($diff_in_days);
                } else {
                   $daysCount = 'NA';
                }

            // Update Button data-bs-target='#updateModal'
            $updateButton = "<button  class='fa fa-edit updateProduct' data-id='" . $product->id . "' data-bs-toggle='modal' data-bs-target='#productUpdateModal' style='font-size:24px;color:green'></button>";
            // Delete Button
            $deleteButton = "<button class='fa fa-trash-o deleteProduct' data-id='" . $product->id . "' style='font-size:24px;color:red'></button>";

            if (Auth::check() && Auth::user()->role_id == 2 && Auth::user()->current_team_id == 1) {
                $actionButtons =  $updateButton . " " . $deleteButton;
            } else {
                $actionButtons =  $updateButton;
            }


            $arrData[] = [
                    'id' => $product->id,
                    'productId' => $product->productId,
                    'date' => $date,
                    'batch' => $product->batch_id,
                    'category' => $product->category,
                    'model' => $product->model,
                    'note' => $product->note,
                    'assigned_user' => $product->assigned_user,
                    'created_by_user' => $created_by_user,
                    'daysCount' => $daysCount,
                    'action' => $actionButtons,
                    ];
            }

        $users = User::whereIn('role_id', ['2', '3'])->orderBy("name", "asc")->get();

        // echo '<pre>';print_r($arrData);exit;
        return view('productQc', compact('arrData', 'users'));
    }

    public function productGenSerial(Request $request)
    {
        

                $result = DB::table('LRproductsBatch as b')
                        ->select(
                            'b.*',
                            'createdUser.name as created_by_user',
                            'updatedUser.name as updated_by_user',
                            'assignedUser.name as assigned_user'
                        )
                        ->leftJoin('users as assignedUser', 'b.assigned', '=', 'assignedUser.id')
                        ->leftJoin('users as createdUser', 'b.created_by', '=', 'createdUser.id')
                        ->leftJoin('users as updatedUser', 'b.updated_by', '=', 'updatedUser.id')
                        ->leftJoin('LRproducts as products', 'products.batch_id', '=', 'b.id')
                        ->whereNull('products.serial')
                        ->groupBy('b.id');

                $filters['from_date']=$filters['to_date']=[];

                if (!empty($request->from_date)) {
                $fromDate = $request->from_date;
                $toDate = $request->to_date;
                $result = $result->whereBetween(DB::raw('DATE(LRproductsBatch.created_at)'), array($fromDate, $toDate));
                $filters['from_date']=$fromDate;
                $filters['to_date']=$toDate;
                }


                // dd($result->toSql(), $result->getBindings());

                $result = $result->get();
                // echo '<pre>';  print_r($result);exit;

                // Loop through the results
                $arrData=[];

                foreach ($result as $key=> $product) {

                if (!empty($product->created_at) ) {

                $from = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', now());
                $to = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $product->created_at);
                $diff_in_days = $to->diffInDays($from);
                $daysCount = round($diff_in_days);
                } else {
                $daysCount = 'NA';
                }


                //////////////////////////////////switch statement
                switch ($product->status) {
                case '1':
                $status= "Production";
                break;
                case '2':
                $status= "Completed";
                break;

                default:
                $status= "NA";
                break;
                }

                $created_at = \Carbon\Carbon::parse($product->created_at);
                $date = $created_at->format('d-m-Y');
                $dateCreated = $created_at->format('d-m-Y H:i:s');
                $created_by_user = $product->created_by_user .' - '. $dateCreated;
                 //////////////////////
                 $serialCount = LRProducts::where('batch_id', $product->id)->whereNotNull('serial')->count();
                 ////////////////////////
                $arrData[] = [
                'id' => $product->id,
                'date' => $date,
                'category' => $product->category,
                'model' => $product->model,
                'note' => $product->note,
                'qty' => $product->qty,
                'serialCount' => $serialCount,
                'assigned_user' => $product->assigned_user,
                'created_by_user' => $created_by_user,
                'daysCount' => $daysCount,
                'status' => $status,
                ];
                }


        $users = User::whereIn('role_id', ['2', '3'])->orderBy("name", "asc")->get();

        // echo '<pre>';print_r($arrData);exit;
        return view('productGenSerial', compact('arrData', 'users'));
    }

    public function appendProductModel(Request $request)
    {
        $id = $request->post('id');
        $limit = $request->post('count');

            $result = DB::table('LRproducts as products')
                ->select([
                    'products.*',
                    'createdUser.name as created_by_user',
                    'updatedUser.name as updated_by_user',
                ])
            
                ->leftJoin('users as createdUser', 'products.created_by', '=', 'createdUser.id')
                ->leftJoin('users as updatedUser', 'products.updated_by', '=', 'updatedUser.id')
                ->where('products.batch_id', $id)
                ->whereNull('serial')
                // ->where('products.created_at', '>=', '2026-05-01 00:00:00')
                ->limit($limit);
                // ->get(); 

                $result = $result->get();

                $arrData=[];
                $i=1;$k=1;$j=1;
                foreach ($result as $key=> $product) {

                if (!empty($product->created_at) ) {

                    $from = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', now());
                    $to = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $product->created_at);
                    $diff_in_days = $to->diffInDays($from);
                    $daysCount = round($diff_in_days);

                    $getDate = \Carbon\Carbon::parse($product->created_at)->format('Y-m-d');
                } else {
                    $daysCount = 'NA';
                }
            ////////////////////////////////////// random prifix start
            $randnum = mt_rand(1000,9999); 
            $year = date("Y"); // Get current year
            $letters = range('A', 'Z'); // Generate A-Z
            
            $startIndex = array_search('K', $letters); // Find position of 'J'
            $index = ($year - 2000 + $startIndex) % count($letters); // Adjust cycle start
            
            $autoUpdatedLetter = $letters[$index]; // Get corresponding letter
            
            // echo "Year: $year → Auto-updated letter: $autoUpdatedLetter";
            $length = 1;
            $randomletter = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz"), 0, $length);
            ///////////////////////////////////////////////  random prifix end

            $model_number = LRcategory::where('id', $product->model_id)->first()->model_number;

            $randnum = 222;
            $randomletter = 111;


            echo "<pre>";
            // print_r($autoUpdatedLetter);
            print_r($model_number);
            exit;

            $serial ='';

            //////////////////////////////////
            $today = date('Y-m-d'); // current date
            $currentMonth = date('n'); // current month number (1–12)
            
            if ($currentMonth < 4) {
                // Jan, Feb, Mar → Financial year started last year
                $startYear = date('Y') - 1;
                $endYear = date('Y');
            } else {
                // Apr to Dec → Financial year starts this year
                $startYear = date('Y');
                $endYear = date('Y') + 1;
            }
            
            $financialYearStart = $startYear . '-04-01';
            $financialYearEnd   = $endYear . '-03-31';
            
            // echo "Financial Year Start Date: $financialYearStart<br>";
            // echo "Financial Year End Date: $financialYearEnd";

            if (strtotime($getDate) >= strtotime($financialYearStart) && strtotime($getDate) <= strtotime($financialYearEnd)) {
                $serialCount = LRProducts::where('batch_id', $id)
                ->whereBetween('created_at', [
                    $financialYearStart . ' 00:00:00',
                    $financialYearEnd . ' 23:59:59'
                ])
                ->whereNotNull('serial')
                ->count();
                // echo $getDate.'W';
                if ($serialCount == 0) {
                    // dd($serialCount);
                    // $serial = strtoupper($randomletter).$randnum.$autoUpdatedLetter.$j++;
                    $serial = strtoupper($randomletter).$randnum.$autoUpdatedLetter.$j++;
                }else{
                    // $serial = strtoupper($randomletter).$randnum.$autoUpdatedLetter.$i+$serialCount;
                    $serial = strtoupper($randomletter).$randnum.$autoUpdatedLetter.$i+$serialCount;
                    $i++;
                }
            }else{
                $serialCount = LRProducts::where('batch_id', $id)
                ->where('created_at', '>=', $financialYearEnd.' 00:00:00')
                // ->where('id', $product->id)
                ->whereNotNull('serial')
                ->count();
                // echo $serialCount ;
                if ($serialCount == 0) {
                    // $serial = strtoupper($randomletter).$randnum.$autoUpdatedLetter.$k++;
                    $serial = strtoupper($randomletter).$randnum.$autoUpdatedLetter.$k++;
                }else{
                    // $serial = strtoupper($randomletter).$randnum.$autoUpdatedLetter.$i+$serialCount;
                    $serial = strtoupper($randomletter).$randnum.$autoUpdatedLetter.$i+$serialCount;
                    $i++;
                } 
            }

           
                $created_at = \Carbon\Carbon::parse($product->created_at);
                $date = $created_at->format('d-m-Y');
                $dateCreated = $created_at->format('d-m-Y H:i:s');
                $created_by_user = $product->created_by_user .' - '. $dateCreated;

                $arrData[] = [
                    'id' => $key+1,
                    'proid' => $product->id,
                    'batch_id' => $product->batch_id,
                    'date' => $date,
                    'category' => $product->category,
                    'model' => $product->model,
                    'serial_no' => $serial,
                    'note' => $product->note,
                    'created_by_user' => $created_by_user,
                    // 'status' => $status,
                    ];
                }
        $table_view = view('productModelData', compact('arrData'))->render();
        return response()->json(['succes' => true, 'table_view' => $table_view]);
    }

    public function updateSerial(Request $request)
    {
    //   $proId = $request->post('proId');
    //   dd($request->all());
   
    if (!empty($request->post('proId'))) {
        $proIds = $request->post('proId');
        $serialNos = $request->post('serialNo');
        $batchId = $request->post('batchId');
    
        for ($i = 0; $i < count($proIds); $i++) {
            $id = $proIds[$i];
            $serial = $serialNos[$i];
    
           $result= DB::table('LRproducts')
                ->where('id', $id)
                ->update(['serial' => $serial]);
                /////////////////////////////// update LRProducts log
           $logs= DB::table('LRproductsLog')->where('product_id', $id)->where('batch_id', $batchId)->where('status', 4)
           ->update(['active' => 0,'updated_by' =>Auth::user()->id,'updated_at' =>date('Y-m-d H:i:s')]); 
           //////////////////////insert the data to logs table
           if ($result) {
            $data = [
                'product_id' =>  $id, 
                'batch_id' => $batchId,
                'active' => 1,
                'status' => 5,
                'created_by' =>Auth::user()->id,
                'created_at' =>date('Y-m-d H:i:s'),
            ];
            
            DB::table('LRproductsLog')->insert($data);
           } 
        }

        if ($result) {
            return redirect()->back()->with('success', 'Serial has been updated successfully.');
        } else {
            return redirect()->back()->with('failed', 'Someting went to wrong.');
        }
    }
    }
    //////////////////////////////////////////////////////////////////
     public function ready(Request $request)
    {
        

         $result = DB::table('LRproductsLog as b')
                                            ->select(
                                                'b.*',
                                                'LRproducts.id as productId',
                                                'LRproducts.category as category',
                                                'LRproducts.model as model',
                                                'LRproducts.serial as serial_no',
                                                'LRproductsBatch.created_at as date',
                                                'createdUser.name as created_by_user',
                                                'updatedUser.name as updated_by_user',
                                                'assignedUser.name as assigned_user'
                                            )
                                            ->leftJoin('LRproducts', 'b.product_id', '=', 'LRproducts.id')
                                            ->leftJoin('LRproductsBatch', 'b.batch_id', '=', 'LRproductsBatch.id')
                                            ->leftJoin('users as assignedUser', 'b.assigned', '=', 'assignedUser.id')
                                            ->leftJoin('users as createdUser', 'b.created_by', '=', 'createdUser.id')
                                            ->leftJoin('users as updatedUser', 'b.updated_by', '=', 'updatedUser.id')
                                            ->where('b.active', 1)
                                            ->where('b.status', 5);

              
        // dd($result->toSql(), $result->getBindings());
    
        $result = $result->get();
        // echo '<pre>';  print_r($result);exit;

        // Loop through the results
        $arrData=[];

    foreach ($result as $key=> $product) {
                        
         //////////////////////////////////switch statement
             switch ($product->status) {
                 case '1':
                 $status= "Production";
                 break;
                 case '2':
                 $status= "Completed";
                 break;
                                  
                 default:
                 $status= "NA";
                     break;
             }

             $created_date = \Carbon\Carbon::parse($product->date);
             $date = $created_date->format('d-m-Y');

             $created_at = \Carbon\Carbon::parse($product->created_at);
             $created_at = $created_at->format('d-m-Y H:i:s');
             $created_by_user = $product->created_by_user .' - '. $created_at;


                if (!empty($product->date) ) {

                $from = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', now());
                $to = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $product->date);
                $diff_in_days = $to->diffInDays($from);
                $daysCount = round($diff_in_days);
                } else {
                   $daysCount = 'NA';
                }

            // Update Button data-bs-target='#updateModal'
            $updateButton = "<button  class='fa fa-edit updateProduct' data-id='" . $product->id . "' data-bs-toggle='modal' data-bs-target='#productUpdateModal' style='font-size:24px;color:green'></button>";
            // Delete Button
            $deleteButton = "<button class='fa fa-trash-o deleteProduct' data-id='" . $product->id . "' style='font-size:24px;color:red'></button>";

            if (Auth::check() && Auth::user()->role_id == 2 && Auth::user()->current_team_id == 1) {
                $actionButtons =  $updateButton . " " . $deleteButton;
            } else {
                $actionButtons =  $updateButton;
            }


            $arrData[] = [
                    'id' => $product->id,
                    'productId' => $product->productId,
                    'date' => $date,
                    'batch' => $product->batch_id,
                    'category' => $product->category,
                    'model' => $product->model,
                    'serial_no' => $product->serial_no,
                    'note' => $product->note,
                    'assigned_user' => $product->assigned_user,
                    'created_by_user' => $created_by_user,
                    'daysCount' => $daysCount,
                    'action' => $actionButtons,
                    ];
            }

        $users = User::whereIn('role_id', ['2', '3'])->orderBy("name", "asc")->get();

        // echo '<pre>';print_r($arrData);exit;
        return view('productReady', compact('arrData', 'users'));
    }

    public function sold(Request $request)
    {
         $result = DB::table('LRproductsLog as b')
                                            ->select(
                                                'b.*',
                                                'LRproducts.id as productId',
                                                'LRproducts.category as category',
                                                'LRproducts.model as model',
                                                'LRproductsBatch.created_at as date',
                                                'createdUser.name as created_by_user',
                                                'updatedUser.name as updated_by_user',
                                                'assignedUser.name as assigned_user'
                                            )
                                            ->leftJoin('LRproducts', 'b.product_id', '=', 'LRproducts.id')
                                            ->leftJoin('LRproductsBatch', 'b.batch_id', '=', 'LRproductsBatch.id')
                                            ->leftJoin('users as assignedUser', 'b.assigned', '=', 'assignedUser.id')
                                            ->leftJoin('users as createdUser', 'b.created_by', '=', 'createdUser.id')
                                            ->leftJoin('users as updatedUser', 'b.updated_by', '=', 'updatedUser.id')
                                            ->where('b.active', 1)
                                            ->where('b.status', 2);

        // dd($result->toSql(), $result->getBindings());
    
        $result = $result->get();
        // echo '<pre>';  print_r($result);exit;

        // Loop through the results
        $arrData=[];

    foreach ($result as $key=> $product) {
                        
         //////////////////////////////////switch statement
             switch ($product->status) {
                 case '1':
                 $status= "Production";
                 break;
                 case '2':
                 $status= "Completed";
                 break;
                                  
                 default:
                 $status= "NA";
                     break;
             }

             $created_date = \Carbon\Carbon::parse($product->date);
             $date = $created_date->format('d-m-Y');

             $created_at = \Carbon\Carbon::parse($product->created_at);
             $created_at = $created_at->format('d-m-Y H:i:s');
             $created_by_user = $product->created_by_user .' - '. $created_at;


                if (!empty($product->date) ) {

                $from = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', now());
                $to = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $product->date);
                $diff_in_days = $to->diffInDays($from);
                $daysCount = round($diff_in_days);
                } else {
                   $daysCount = 'NA';
                }

            // Update Button data-bs-target='#updateModal'
            $updateButton = "<button  class='fa fa-edit updateProduct' data-id='" . $product->id . "' data-bs-toggle='modal' data-bs-target='#productUpdateModal' style='font-size:24px;color:green'></button>";
            // Delete Button
            $deleteButton = "<button class='fa fa-trash-o deleteProduct' data-id='" . $product->id . "' style='font-size:24px;color:red'></button>";

            if (Auth::check() && Auth::user()->role_id == 2 && Auth::user()->current_team_id == 1) {
                $actionButtons =  $updateButton . " " . $deleteButton;
            } else {
                $actionButtons =  $updateButton;
            }


            $arrData[] = [
                    'id' => $product->id,
                    'productId' => $product->productId,
                    'date' => $date,
                    'batch' => $product->batch_id,
                    'category' => $product->category,
                    'model' => $product->model,
                    'note' => $product->note,
                    'assigned_user' => $product->assigned_user,
                    'created_by_user' => $created_by_user,
                    'daysCount' => $daysCount,
                    'action' => $actionButtons,
                    ];
            }

        $users = User::whereIn('role_id', ['2', '3'])->orderBy("name", "asc")->get();

        // echo '<pre>';print_r($arrData);exit;
        return view('productSold', compact('arrData', 'users'));
    }

    public function defective(Request $request)
    {
        
        return view('productDefective');
    }

  


}