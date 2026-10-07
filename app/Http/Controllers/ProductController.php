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
use App\Models\LRproductsSpec;
use App\Models\LRvendor;
use App\Models\LRproductsLog;
use App\Models\LRproductsImages;
use DataTables;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Intervention\Image\Laravel\Facades\Image;

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

        ///////////01-07-2025
        $assigned_by = Auth::user()->id;
        if($assigned_by){
            $assigned_by = User::where('id', $assigned_by)->first()->name ?? 'NA';
        }else{
            $assigned_by = "NA";
        }
        //////////
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
                $productsBatch->assigned = $request->get('assignUserId');
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
                        $productLog->assigned = $request->get('assignUserId');
                        $productLog->note = $request->get('pr_desc')[$key];
                        $productLog->active = 1;
                        $productLog->status = 1;
                        $productLog->created_by = Auth::user()->id;
                        $productLog->created_at = date('Y-m-d H:i:s');
                        $productLog->assigned_by = $assigned_by;
                        $productLog->assigned_at = date('Y-m-d H:i:s');
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
            $updateButton = "<button class='btn btn-sm btn-outline-warning updateProduct' data-id='" . $product->id . "' data-bs-toggle='modal' data-bs-target='#productUpdateModal' title='Edit'><i class='fa fa-edit'></i></button>";
            // Delete Button
            $deleteButton = "<button class='btn btn-sm btn-outline-danger deleteProduct' data-id='" . $product->id . "' title='Delete'><i class='fa fa-trash'></i></button>";

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
        $LRproductSpec = LRproductsSpec::where('status', 1)->orWhere('status',2)->orderBy('name', 'asc')->get();
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
            $response['vendor_id']     = $productLog->vendor_id;

            $response['success'] = 1;

        ///////////
        $getSpec = [];
        $html     = '';
        $getSpec = DB::table('LRproductsSpecAssigned')
            ->select(
                'LRproductsSpecAssigned.*',
                'LRproductsSpec.name as spec_name',
                'LRproductsSpec.parent_id as parent_id',

            )
            ->leftJoin('LRproductsSpec', 'LRproductsSpec.id', '=', 'LRproductsSpecAssigned.spec_id')

            ->where('LRproductsSpecAssigned.product_id', $productLog->product_id)
            ->get();
        $last_key = count($getSpec);
    //    dd($getSpec);
        $i = 1;
        foreach ($getSpec as $key => $value) {

            $html .= '<tr>';
            $html .= '<td><select class="form-control browser-default custom-select partId partCategory" data-id='.$i.' name="spec_name[]" id="part" style="width: 100% !important" required>';
            $html .= '<option value="" selected>Select</option>';

            foreach ($LRproductSpec as $item) {
                $selected = ($item->id == $value->spec_id) ? 'selected' : ''; // Correct PHP comparison
                if ($item->parent_id==0) {
                $html .= '<option value="' . $item->id . '" ' . $selected . '>' . htmlspecialchars($item->name) . '</option>';
                }
            }

            $html .= '</select>';
            $html .= '</td>';
            $html .= '<td><select class="form-control browser-default custom-select partSub partSubAppend'.$i.'" name="spec_number[]" style="width: 100% !important" required>';
            $html .= '<option value="" selected>Select</option>';

            foreach ($LRproductSpec as $item) {
                $selected = ($item->id == $value->spec_number_id) ? 'selected' : ''; // Correct PHP comparison
                if ($item->parent_id == $value->spec_id) {
                    $html .= '<option value="' . $item->id . '" ' . $selected . '>' . htmlspecialchars($item->name) . '</option>';
                }
                // $html .= '<option value="' . $item->id . '" ' . $selected . '>' . htmlspecialchars($item->name) . '</option>';
            }

            $html .= '</select>';
            // $html .= '<input type="text" class="form-control partSub" name="partSub[]" onkeypress="return event.charCode >= 48 && event.charCode <= 57" value=' . $value->part_value . ' required/>';
            $html .= '</td>';
            $html .= '<td><button type="button" class="btn btn-danger remove"><i class="fa fa-remove"></i></button></td>';
            $html .= '</tr>';

            $i++;
        }
        } else {
            $response['success'] = 0;
        }
        ///////////
        return response()->json([$response, $html]);
    }


    
    public function submitAssignProduct(Request $request)
    {
        // dd($request->all());
        $id         = $request->input('id');
        $productIDs   = $request->input('productId');
        $vendor     = $request->input('vendor');
        $assigned     = $request->input('assigned');
        $noteLog       = $request->input('noteLog');
        $status     = $request->input('status');
        $updated_by = Auth::user()->id;
        $updated_at = date('Y-m-d H:i:s');
                ///////////01-07-2025
        if($updated_by){
            $assigned_by = User::where('id', $updated_by)->first()->name ?? 'NA';
        }else{
            $assigned_by = "NA";
        }
        //////////
        $productLog = LRProductsLog::find($id);
        if ($productLog) {

            // if ($productLog->status == $status && $productLog->assigned == $assigned)
            if ($productLog->status == $status && $productLog->assigned == $updated_by)
            {
                $data = [
                        "note"          => $noteLog,
                        "vendor_id"     => $vendor,
                        "updated_by"    => $updated_by,
                        "updated_at"    => $updated_at
                    ];
                $productLog->update($data);

            } else {

                $data = [
                        // "assigned"      => $assigned,
                        "assigned"      => $updated_by,
                        "note"          => $noteLog,
                        "vendor_id"     => $vendor,
                        "updated_by"    => $updated_by,
                        "updated_at"    => $updated_at,
                        "active"        => 0,
                    ];
                $productLog->update($data);

                $dataInsert = [
                        "batch_id"      => $productLog->batch_id,
                        "product_id"    => $productLog->product_id,
                        "vendor_id"     => $vendor,
                        // "assigned"      => $assigned,
                        "assigned"      => $updated_by,
                        "status"        => $status,
                        "note"          => $noteLog,
                        "vendor_id"     => $vendor,
                        "created_by"    => $updated_by,
                        "created_at"    => $updated_at,
                        "assigned_by"   => $assigned_by,
                        "assigned_at"   => $updated_at,
                        "active"        => 1,
                    ];

                DB::table('LRproductsLog')->insert($dataInsert);
            }

                DB::table('LRproducts')->where('id', $productLog->product_id)
                            ->update([
                                'note'          => $noteLog,
                                'vendor_id'     => $vendor,
                                'status'        => $status,
                                'updated_by'    => $updated_by,
                                'updated_at'    => $updated_at
                            ]);

                //////////////////////////////////////23-06-2025
                $specData = [];
                if ($request->input('specId') != null) { /// Yes: data insertion

                    DB::table('LRproductsSpecAssigned')->where('product_id', $productIDs)->delete();
                    // exit;
                    for ($i = 0; $i < count($request->input('specId')); $i++) {
                        $matchingSpec = LRproductsSpec::where('id', $request->input('specId')[$i])->first(['id', 'name']); // Fetch only necessary fields
                        $matchingSpecSub = LRproductsSpec::where('id', $request->input('specSub')[$i])->first(['id', 'name']); // Fetch only necessary fields
                        $specData[] = [
                            'spec_id'    => $request->input('specId')[$i],
                            'spec_name' => $matchingSpec ? $matchingSpec->name : null, // Avoid error if no record is found
                            'spec_number_id' => $request->input('specSub')[$i],
                            'spec_number' => $matchingSpecSub ? $matchingSpecSub->name : null, // Avoid error if no record is found
                            "product_id"  => $productIDs,
                            "created_by" => $updated_by,
                            "created_at" => $updated_at,
                        ];
                    }
                    // dd($specData);exit;
                    DB::table('LRproductsSpecAssigned')->insert($specData);
                } else { // No: data deletion
                    DB::table('LRproductsSpecAssigned')->where('product_id', $productIDs)->delete();
                }
                ////////////////////23-06-2025
                
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
            $updateButton = "<button class='btn btn-sm btn-outline-warning updateProduct' data-id='" . $product->id . "' data-bs-toggle='modal' data-bs-target='#productUpdateModal' title='Edit'><i class='fa fa-edit'></i></button>";
            // Delete Button
            $deleteButton = "<button class='btn btn-sm btn-outline-danger deleteProduct' data-id='" . $product->id . "' title='Delete'><i class='fa fa-trash'></i></button>";

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
                            'products.batch_id as batch_id',
                            'createdUser.name as created_by_user',
                            'updatedUser.name as updated_by_user',
                            'assignedUser.name as assigned_user'
                        )
                        ->leftJoin('users as assignedUser', 'b.assigned', '=', 'assignedUser.id')
                        ->leftJoin('users as createdUser', 'b.created_by', '=', 'createdUser.id')
                        ->leftJoin('users as updatedUser', 'b.updated_by', '=', 'updatedUser.id')
                        ->leftJoin('LRproducts as products', 'products.batch_id', '=', 'b.id')
                        ->whereNull('products.serial')
                        ->where('products.status', 4)
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
                 $serialCount = LRProducts::where('batch_id', $product->batch_id)->whereNotNull('serial')->count();
                 $ready = LRProducts::where('batch_id', $product->batch_id)->whereNull('serial')->where('status',4)->count();
                 ////////////////////////

                    // Update Button data-bs-target='#updateModal'
                // $updateButton = "<button  class='btn btn-sm btn-outline-primary' data-id='" . $product->id . "' data-bs-toggle='modal' data-bs-target='#productUpdateModal' ></button>";
                // // Delete Button
                // $deleteButton = "<button class='btn btn-sm btn-outline-danger' data-id='" . $product->id . "' ></button>";

                // if (Auth::check() && Auth::user()->role_id == 2 && Auth::user()->current_team_id == 1) {
                //     $actionButtons =  $updateButton . " " . $deleteButton;
                // } else {
                //     $actionButtons =  $updateButton;
                // }

                if ($ready == 0)
                {
                $actionButtons= 'NA';
                } else {
                $actionButtons= '<span style="display:flex"><input type="number" min="0" max="'.$ready.'"  class="form-control scroll toSerial'.$product->id.' col-md-9"/><button type="button" data-toggle="tooltip" title="Click to generate serial no" class="btn btn-primary btn-sm" onclick="return appendProductModel('.$product->id.','.$ready.','.$serialCount.')"><i class="fa fa-share"></i></button></span>';
            }

                $arrData[] = [
                'id' => $product->id,
                'date' => $date,
                'category' => $product->category,
                'model' => $product->model,
                'note' => $product->note,
                'qty' => $product->qty,
                'ready' => $ready,
                'serialCount' => $serialCount,
                'created_by_user' => $created_by_user,
                'daysCount' => $daysCount,
                'status' => $status,
                'action' => $actionButtons,
                ];
                }


        $users = User::whereIn('role_id', ['2', '3'])->orderBy("name", "asc")->get();

        // echo '<pre>';print_r($arrData);exit;
        return view('productGenSerial', compact('arrData', 'users'));
    }

    public function appendProductModel(Request $request)
    {
        $batch_id = $request->post('id');
        $limit = $request->post('count');

            $result = DB::table('LRproducts as products')
                ->select([
                    'products.*',
                    'createdUser.name as created_by_user',
                    'updatedUser.name as updated_by_user',
                ])
            
                ->leftJoin('users as createdUser', 'products.created_by', '=', 'createdUser.id')
                ->leftJoin('users as updatedUser', 'products.updated_by', '=', 'updatedUser.id')
                ->where('products.batch_id', $batch_id)
                ->whereNull('serial')
                ->where('products.status',4)
                // ->where('products.created_at', '>=', '2026-05-01 00:00:00')
                ->limit($limit);
                // ->get(); 

                $result = $result->get();

                $arrData=[];
                $i=1;
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

                $model_number = LRcategory::where('id', $product->model_id)->first()->model_number;
                $created_at = \Carbon\Carbon::parse($product->created_at);
                $date = $created_at->format('d-m-Y');
                $dateCreated = $created_at->format('d-m-Y H:i:s');
                $created_by_user = $product->created_by_user .' - '. $dateCreated;



                ////////////////////////////////////// year letter change every year - start ///////////////

                    $date ??= now();
                    $date = Carbon::createFromFormat('d-m-Y', $date);

                    // 1 Apr is the cut‑over: months 1‑3 belong to last year’s FY
                    $fyStartYear = $date->month < 4 ? $date->year - 1 : $date->year;

                    $baseYear    = 2016;   // 2016‑17 fiscal year
                    $baseLetter  = 'A';

                    $letterIndex = $fyStartYear - $baseYear;      // 0‑based
                    $yearLetter = chr(ord($baseLetter) + $letterIndex);  // rolls to J in 2025‑26

                    ////////////////////////////////////// year letter change every year - end ///////////////

                $prefix = $model_number . $yearLetter;   // e.g., "LR123J"

                // Find the current max number after the prefix
                $currentMax = LRProducts::where('batch_id', $batch_id)
                    ->where('model_id', $product->model_id)
                    ->where('serial', 'like', $prefix . '%')
                    ->select(DB::raw("
                        MAX(CAST(SUBSTRING(serial, " . (strlen($prefix) + 1) . ") AS UNSIGNED)) AS max_counter
                    "))
                    ->value('max_counter');  // null if no previous serial

                $next   = ($currentMax ?? 0) + 1;
                $serial = $prefix . $next;  // e.g., "LR123J4"
          
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
        $table_view = view('modals.productModelData', compact('arrData'))->render();
        return response()->json(['succes' => true, 'table_view' => $table_view]);
    }

    public function updateSerial(Request $request)
    {
        // echo '<pre>';  print_r($request->all());exit;

    if (!empty($request->post('proId'))) {
        $proIds = $request->post('proId');
        $serialNos = $request->post('serialNo');
        $batchId = $request->post('batchId');
    
        for ($i = 0; $i < count($proIds); $i++) {
            $id = $proIds[$i];
            $serial = $serialNos[$i];
    
           $result= DB::table('LRproducts')
                        ->where('id', $id)
                        ->update([
                            'serial' => $serial,
                            'status' => 4,
                            'updated_by'    => Auth::user()->id,
                            'updated_at'    => date('Y-m-d H:i:s')
                        ]);
                /////////////////////////////// update LRProducts log
           $logs= DB::table('LRproductsLog')
                        ->where('product_id', $id)
                        ->where('batch_id', $batchId)
                        ->where('status', 4)
                        ->update([
                                'updated_by' =>Auth::user()->id,
                                'updated_at' =>date('Y-m-d H:i:s')
                                ]); 
           //////////////////////insert the data to logs table
           } 
        }

        if ($result) {
            return redirect()->back()->with('success', 'Serial has been updated successfully.');
        } else {
            return redirect()->back()->with('failed', 'Someting went to wrong.');
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
                                            ->whereNotNull('LRproducts.serial')
                                            ->where('b.status', 4);

              
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
            $updateButton = "<button class='btn btn-sm btn-outline-warning updateProduct' data-id='" . $product->id . "' data-bs-toggle='modal' data-bs-target='#productUpdateModal' title='Edit'><i class='fa fa-edit'></i></button>";
            // Delete Button
            $deleteButton = "<button class='btn btn-sm btn-outline-danger deleteProduct' data-id='" . $product->id . "' title='Delete'><i class='fa fa-trash'></i></button>";

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
        $mobileNumber = LRvendor::select('mobile1', 'name', 'companyname', 'id')->where('status', 1)->get();
        $spec = LRproductsSpec::where('status', 1)->where('parent_id', 0)->orderBy('name', 'asc')->get();
        // echo '<pre>';print_r($arrData);exit;
        return view('productReady', compact('arrData', 'users','mobileNumber','spec'));
    }

    public function sold(Request $request)
    {
        $spec=[];
         $result = DB::table('LRproductsLog as b')
                                            ->select(
                                                'b.*',
                                                'LRproducts.id as productId',
                                                'LRproducts.category as category',
                                                'LRproducts.serial as serial_no',
                                                'LRproducts.model as model',
                                                'LRproductsBatch.created_at as date',
                                                'createdUser.name as created_by_user',
                                                'updatedUser.name as updated_by_user',
                                                'assignedUser.name as assigned_user',
                                                'LRvendor.name as vendor_name',
                                                'LRvendor.company as vendor_company',
                                            )
                                            ->leftJoin('LRproducts', 'b.product_id', '=', 'LRproducts.id')
                                            ->leftJoin('LRproductsBatch', 'b.batch_id', '=', 'LRproductsBatch.id')
                                            ->leftJoin('users as assignedUser', 'b.assigned', '=', 'assignedUser.id')
                                            ->leftJoin('users as createdUser', 'b.created_by', '=', 'createdUser.id')
                                            ->leftJoin('users as updatedUser', 'b.updated_by', '=', 'updatedUser.id')
                                            ->leftJoin('LRvendor', 'b.vendor_id', '=', 'LRvendor.id')
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

            // data-bs-toggle='modal' data-bs-target='#productInfoModal'
            $proInfo = "<button class='btn btn-sm btn-outline-info proInfo' onclick='proInfo(" . $product->productId . ")' data-bs-target='#proInfoModal' title='Info'><i class='fa fa-info'></i></button>";
            $infoButton = "<button class='btn btn-sm btn-outline-success productInfo' data-id='" . $product->productId . "' data-bs-toggle='modal' data-bs-target='#productInfoModal' title='View'><i class='fa fa-eye'></i></button>";
            $updateButton = "<button class='btn btn-sm btn-outline-warning updateProduct' data-id='" . $product->id . "' data-bs-toggle='modal' data-bs-target='#productUpdateModal' title='Edit'><i class='fa fa-edit'></i></button>";
            // Delete Button
            $deleteButton = "<button class='btn btn-sm btn-outline-danger deleteProduct' data-id='" . $product->id . "' title='Delete'><i class='fa fa-trash'></i></button>";

            if (Auth::check() && Auth::user()->role_id == 2 && Auth::user()->current_team_id == 1) {
                $actionButtons =  $proInfo . " " . $infoButton . " " . $updateButton . " " . $deleteButton;
            } else {
                $actionButtons = $proInfo . " " . $infoButton . " " . $updateButton;
            }


            $arrData[] = [
                    'id' => $product->id,
                    'productId' => $product->productId,
                    'date' => $date,
                    'batch' => $product->batch_id,
                    'category' => $product->category,
                    'model' => $product->model,
                    'vendor_id' => $product->vendor_id,
                    'serial_no' => $product->serial_no,
                    'note' => $product->note,
                    'vendor_name' => $product->vendor_name,
                    'vendor_company' => $product->vendor_company,
                    'assigned_user' => $product->assigned_user,
                    'created_by_user' => $created_by_user,
                    'daysCount' => $daysCount,
                    'action' => $actionButtons,
                    ];
            }

        $users = User::whereIn('role_id', ['2', '3'])->orderBy("name", "asc")->get();
        $mobileNumber = LRvendor::select('mobile1', 'name', 'companyname', 'id')->where('status', 1)->get();
        $spec = LRproductsSpec::where('status', 1)->where('parent_id', 0)->orderBy('name', 'asc')->get();
        // echo '<pre>';print_r($arrData);exit;
        return view('productSold', compact('arrData', 'users','spec','mobileNumber'));
    }

    public function defective(Request $request)
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
                                            ->where('b.status', 6);

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


            // Update Button data-bs-target='#updateModal'
            $updateButton = "<button class='btn btn-sm btn-outline-warning updateProduct' data-id='" . $product->id . "' data-bs-toggle='modal' data-bs-target='#productUpdateModal' title='Edit'><i class='fa fa-edit'></i></button>";
            // Delete Button
            $deleteButton = "<button class='btn btn-sm btn-outline-danger deleteProduct' data-id='" . $product->id . "' title='Delete'><i class='fa fa-trash'></i></button>";

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
                    'action' => $actionButtons,
                    ];
            }

        $users = User::whereIn('role_id', ['2', '3'])->orderBy("name", "asc")->get();

        // echo '<pre>';print_r($arrData);exit;
        return view('productDefective', compact('arrData', 'users'));
    }

        public function logProductHandlerDetails(Request $request)
    {
        $logs = DB::table('LRproductsLog')
                    ->select('LRproductsLog.*', 'createdUser.name as createdUserName', 'updatedUser.name as updatedUserName')
                    ->leftJoin('users as createdUser', 'createdUser.id', '=', 'LRproductsLog.created_by')
                    ->leftJoin('users as updatedUser', 'updatedUser.id', '=', 'LRproductsLog.updated_by')
                    ->where('product_id', $request->get('id'))
                    ->orderBy('LRproductsLog.created_at', 'asc')
                    ->get();

        $product = DB::table('LRproducts')
                    ->select('LRproducts.*', 'createdUser.name as createdUserName', 'updatedUser.name as updatedUserName', 'LRvendor.name', 'LRvendor.companyname')
                    ->leftJoin('users as createdUser', 'createdUser.id', '=', 'LRproducts.created_by')
                    ->leftJoin('users as updatedUser', 'updatedUser.id', '=', 'LRproducts.updated_by')
                    ->leftJoin('LRvendor', 'LRvendor.id', '=', 'LRproducts.vendor_id')
                    ->where('LRproducts.id', $request->get('id'))
                    ->get();

        $parts = DB::table('LRproductsSpecAssigned')
                    ->leftJoin('LRproductsSpec', 'LRproductsSpecAssigned.spec_id', '=', 'LRproductsSpec.id')
                    ->where('LRproductsSpecAssigned.product_id', $request->get('id'))
                    ->select(DB::raw("CONCAT(LRproductsSpec.name, ' ', LRproductsSpec.name) AS name"))
                    ->pluck('name') // gets array of part_info
                    ->implode(', ');
                    // ->get();
        // dd($logs);
        return response()->json(['logs' => $logs, 'product' => $product, 'parts' => $parts]);
    }
  
    public function productInfo(Request $request)
    {
        
        $id = $request->get('id');
        $prodcutInfo = DB::table('LRproductsLog as b')
                                ->select(
                                    'b.*',
                                    'LRproducts.id as productId',
                                    'LRproducts.id as productId',
                                    'LRproducts.category as category',
                                    'LRproducts.created_at as created_at',
                                    'LRproducts.model as model',
                                    'LRproducts.serial as serial_no',
                                    'LRproductsBatch.created_at as date',
                                    'createdUser.name as created_by_user',
                                    'updatedUser.name as updated_by_user',
                                    'assignedUser.name as assigned_user',
                                    'LRvendor.name as vendor_name',
                                    'LRvendor.goe_location as goe_location',
                                    'LRvendor.id as vendor_id',
                                    'LRvendor.company as vendor_company',
                                    'LRvendor.companyname as vendor_company2',
                                    'LRvendor.address as vendor_address',
                                    'LRvendor.mobile1 as vendor_mobile1',
                                    'LRvendor.mobile2 as vendor_mobile2',
                                    'LRvendor.city as vendor_city',
                                    'LRvendor.district as vendor_district',
                                    'LRvendor.state as vendor_state',
                                    'LRvendor.pincode as vendor_pincode',
                                )
                                ->leftJoin('LRproducts', 'b.product_id', '=', 'LRproducts.id')
                                ->leftJoin('LRvendor', 'LRproducts.vendor_id', '=', 'LRvendor.id')
                                ->leftJoin('LRproductsBatch', 'b.batch_id', '=', 'LRproductsBatch.id')
                                ->leftJoin('users as assignedUser', 'b.assigned', '=', 'assignedUser.id')
                                ->leftJoin('users as createdUser', 'b.created_by', '=', 'createdUser.id')
                                ->leftJoin('users as updatedUser', 'b.updated_by', '=', 'updatedUser.id')
                                ->where('b.id', $id);

        $prodcutInfo = $prodcutInfo->get();
        // dd();
        $goe_location=[];
        //////////////////map show in html blade method
        if(!empty($prodcutInfo[0]->goe_location)){
            $shortUrl=$prodcutInfo[0]->goe_location;
            $fullUrl = $this->expandUrl($shortUrl);
            $latitude = $longitude = null;
            // Extract coordinates from expanded URL (e.g., @11.0186117,77.0010533)
            if (preg_match("~@([-0-9.]+),([-0-9.]+)~", $fullUrl, $matches)) {
                $latitude  = $matches[1];
                $longitude = $matches[2];
            }
            $goe_location=[
                'latitude'=>$latitude,
                'longitude'=>$longitude,
            ];
        }
        $images = LRproductsImages::where('product_id', $id)->orderBy("id", "asc")->get();
    
        $response = array();
        if (!empty($prodcutInfo)) {
            $response['success'] = 1;
            $response['prodcutInfo'] = $prodcutInfo;
            $response['goe_location'] = $goe_location;
            $response['images'] = $images;
    
        } else {
            $response['success'] = 0;
        }
        
        ////////////////////////////// append product specification data
                $getSpec = [];
                $html     = '';
                $getSpec = DB::table('LRproductsSpecAssigned')
                    ->select(
                        'LRproductsSpecAssigned.*',
                        'LRproductsSpec.name as spec_name',
                        'LRproductsSpec.parent_id as parent_id',
                    )
                    ->leftJoin('LRproductsSpec', 'LRproductsSpec.id', '=', 'LRproductsSpecAssigned.spec_id')
                    ->where('LRproductsSpecAssigned.product_id', $id)
                    ->get();
            //    dd($getSpec);
                $i = 1;
            if ($getSpec->isNotEmpty()) {
                $html .= '<div id="table-scroll">';
                $html .= '<table class="table table-striped table-bordered text-center table-hover">';
                $html .= '<tr>';
                $html .= '<th>SNo</th>';
                $html .= '<th>Product Specs</th>';
                $html .= '<th>Specs Sub</th>';
                $html .= '</tr>';
                // if (!empty($getSpec)) {
                foreach ($getSpec as $key => $value) {
        
                    $html .= '<tr>';
                    $html .= '<td>'.$i.'</td>';
                    $html .= '<td>'.$value->spec_name.'</td>';
                    $html .= '<td>'.$value->spec_number.'</td>';
                   
                    $html .= '</tr>';
        
                    $i++;
                }
                $html .= '</table>';
                $html .= '</div>';
            }else{
                
                $html .= '<td>No records found</td>';
            } 
                
        return response()->json([$response,$html]);    
    }

     public function storeImages(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,svg,gif', 
        ]);
        $id = $request->id;

        try {
            $current_timestamp = Carbon::now()->timestamp;
            $file     = $request->file('image');
            $filename = $id.'_'.$current_timestamp.'.'.$file->getClientOriginalExtension();
            $insert = LRproductsImages::updateOrCreate(
                ['name' => $filename],
                [
                'product_id'=>$id,
                'name'=>$filename,
                'created_by' => Auth::user()->id,
                'created_at' => date('Y-m-d H:i:s'),
                ]);

            // ↓ Resize & compress

                $path = public_path('productImages');
                $img = Image::read($file->path());
                $img->resize(800, 600, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($path.'/'.$filename);

            return response()->json([
                'success' => true,
                'message' => 'Upload success!',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload failed!',
            ], 500);
        }
    }
  // get specification sub table 
    public function specSub(Request $request)
    {
        // $parts = LRparts::where('status', 1)->orderBy('name', 'asc')->get();
        $parent_id = $request->cat_id;
       //   dd($parent_id);
        $subcategories = LRproductsSpec::where('parent_id',$parent_id)->get();

        return response()->json([
            'subcategories' => $subcategories
        ]);
    }

      // Update geo location in LRvendor
      public function updateLocation(Request $request)
      {
        //   dd($request->all());
          ## Read POST data
          $vendor_id = $request->post('vendor_id');
          $type = $request->post('type');
        if (!empty($vendor_id)) {
           
          $location = LRvendor::find($vendor_id);
          $response = array();
          if (!empty($location)) {// need to check id available

            if ($type!=2) {// update for location
               $updata['goe_location'] = $request->post('goe_location');
              if ($location->update($updata)) {
                $shortUrl = $request->post('goe_location');
                $fullUrl = $this->expandUrl($shortUrl);
            
                $latitude = $longitude = null;
            
                // Extract coordinates from expanded URL (e.g., @11.0186117,77.0010533)
                if (preg_match("~@([-0-9.]+),([-0-9.]+)~", $fullUrl, $matches)) {
                    $latitude  = $matches[1];
                    $longitude = $matches[2];
                }
                
                  $response['success'] =1;
                  $response['latitude'] = $latitude;
                  $response['longitude'] = $longitude;
                //   $response['msg'] = $latitude .$longitude;
              } else {
                  $response['success'] = 0;
                  $response['msg'] = 'Record not updated';
              }
            }else{ // remove for location mean deleted
                $updata['goe_location'] = null;
                $updata['remark'] =  $request->post('reason');
                $location->update($updata);
                $response['latitude'] = '';  
            }
          } else {
              $response['success'] = 0;
              $response['msg'] = 'Invalid ID.';
          }
        }else {
            $response['success'] = 0;
            $response['msg'] = 'Invalid ID.';
        }
          return response()->json($response);
      }

   // for map latitude ,langititude conversion   
      private function expandUrl($url)
      {
          $ch = curl_init($url);
          curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLOPT_HEADER, true); // Needed to trigger redirect
          curl_exec($ch);
          $expanded = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
          curl_close($ch);
      
          return $expanded;
      }


                                       
                                    
}