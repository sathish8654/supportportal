<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\users_deleted;
use App\Models\imei_registration;
use App\Models\LRcase;
use App\Models\LRcategory;
use App\Models\LRproductsSpec;
use DataTables;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Validation\Rule;

class CategoryController  extends Controller
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


    // DataTable data
    public function index(Request $request)
    {
    $categoris = LRcategory::where('parent_id',0)->get();
    //   $category = DB::table('LRcategory as c1')
    //                 ->leftJoin('LRcategory as c2', 'c1.parent_id', '=', 'c2.id')
    //                 ->select('c1.id', 'c1.name as child_name', 'c2.name as parent_name', 'c1.status', 'c1.updated_by', 'c1.updated_at', 'c1.created_by', 'c1.created_at')
    //                 // ->where('c1.parent_id', '!=', 0)
    //                 ->orderBy('c1.status', 'desc') 
    //                 ->get();
      $category = LRcategory::select('*')->where('parent_id',0)->orderBy('id', 'ASC')->get(); 
      return view('category',["categoris" => $categoris, "category" =>$category]);
     }    
        // get table data

    public function getCategoryData()
    {
            
        $category = LRcategory::select('*')->where('parent_id',0);
            
            return Datatables::of($category)
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
                    $iconColor = $row->status == 1 ? 'green' : 'red';
    
                    $btnClass = $row->status == 1 ? 'btn-outline-success' : 'btn-outline-danger';
                    $deleteButton = "<button class='btn btn-sm $btnClass disablePart btn-toggle-status' data-id='" . $row->id . "' data-status=" . $row->status . " title='" . $iconNote . "'><i class='$iconClass'></i></button>";
                    $updateButton = "<button class='btn btn-sm btn-outline-primary addSubCat1Modal' data-id='" . $row->id . "'><i class='fa fa-plus'></i></button>";
    
                    if (Auth::check() && Auth::user()->role_id == 2 && Auth::user()->current_team_id == 1) {
                             if($row->status == 1 ){
                                return  $deleteButton.'  '. $updateButton ;
                                } else {
                                return  $deleteButton ;
                                }
                    }
                })
                
       ->make();
        
        }
  
/////////////////////// for parts sub list data
public function getSubCatData()
{
    // LRcategory::select('*')->where('parent_id',0);
    $CatQuery =LRcategory::where('parent_id', '!=', 0);

    $datatable = Datatables::of($CatQuery)
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
            $iconColor = $row->status == 1 ? 'green' : 'red';

            $btnClass = $row->status == 1 ? 'btn-outline-success' : 'btn-outline-danger';
            $deleteButton = "<button class='btn btn-sm $btnClass disableSubCat' data-id='" . $row->id . "' title='" . $iconNote . "'><i class='$iconClass'></i></button>";
            $updateButton = "<button class='btn btn-sm btn-outline-warning updateSubCat1Modal' data-id='" . $row->id . "' type='button'><i class='fa fa-edit'></i></button>";

            if (Auth::check() && Auth::user()->role_id == 2 && Auth::user()->current_team_id == 1) {
                if($row->status == 1 ){
                return  $deleteButton.'  '. $updateButton ;
                } else {
                return  $deleteButton ;
                }
            } 
        });

      /////////////////////////////////////////////////////////////
        $categoryData = LRcategory::all(); // Only fetched once

        // parent sub name
        $datatable->addColumn('name', function ($row) use ($categoryData) {
            $matchingPart = $categoryData->where('id', $row->parent_id)->first();
            return $matchingPart ? $matchingPart->name : 'sd-';
        });

        $datatable->filterColumn('name', function ($query, $keyword) use ($categoryData) {
            $matchingIds = $categoryData->filter(function ($item) use ($keyword) {
                return stripos($item->name, $keyword) !== false;
            })->pluck('id')->toArray();

            $query->whereIn('parent_id', $matchingIds);
        });

        // part sub name
        $datatable->addColumn('Subname', function ($row) {
            return $row->parent_id != 0 ? $row->name : '';
        });

        ///////////////////// sorting for custom columns
        $datatable->orderColumn('status', function ($query, $order) {
            $query->orderBy('status', $order);
        });

        $datatable->orderColumn('name', function ($query, $order) {
            $query->orderBy('parent_id', $order); // sorting by parent_id
        });

        $datatable->orderColumn('Subname', function ($query, $order) {
            $query->orderBy('name', $order); // sorting by part name
        });

        // filter for sub part name
        $datatable->filterColumn('Subname', function ($query, $keyword) {
            $query->where('name', 'like', "%{$keyword}%");
        });

        
        return $datatable->rawColumns(['action'])->make(true);
}

    ////////////// Disable to sub category item
    public function disableSubCategory(Request $request)
    {
        ## Read POST data
        $id = $request->post('id');
        // Retrieve the current status
        $currentStatus = DB::table('LRcategory')
            ->where('id', $id)
            ->value('status');

        // Determine the new status
        $newStatus = $currentStatus == 1 ? 2 : 1;

        // Update the status
        $result = DB::table('LRcategory')
            ->where('id', $id)
            ->update(['status' => $newStatus]);
            ///////////part sub update//////////
        $subStatus = DB::table('LRcategory')
            ->where('parent_id', $id)
            ->value('status');    
        // Determine the new status
        $sub = $subStatus == 1 ? 2 : 1;
        // Update the status
        $subUpdate = DB::table('LRcategory')
            ->where('parent_id', $id)
            ->update(['status' => $sub]);
        if ($result) {
            return response()->json(['message' => 'Updated Successfully!']);
        } else {
            return response()->json(['message' => 'Failed! Please check!']);
        }
    }
    /////////////////////////////////////////////
     public function categoryStatus(Request $request, $id)
     {
        //  $item = LRcategory::find($id);
        //  if ($item) {
        //      $item->status = $request->status;
        //      $item->save();
 
        //      return response()->json(['success' => true]);
        //  }
 
        //  return response()->json(['success' => false]);
                ## Read POST data
            
                // Retrieve the current status
                $currentStatus = DB::table('LRcategory')
                    ->where('id', $id)
                    ->value('status');
        
                // Determine the new status
                $newStatus = $currentStatus == 1 ? 2 : 1;
        
                // Update the status
                $result = DB::table('LRcategory')
                    ->where('id', $id)
                    ->update(['status' => $newStatus]);
                    ///////////part sub update//////////
                $subStatus = DB::table('LRcategory')
                    ->where('parent_id', $id)
                    ->value('status');    
                // Determine the new status
                $sub = $subStatus == 1 ? 2 : 1;
                // Update the status
                $subUpdate = DB::table('LRcategory')
                    ->where('parent_id', $id)
                    ->update(['status' => $sub]);
                if ($result) {
                    return response()->json(['success' => true]);
                } else {
                    return response()->json(['success' => false]);
                }
     }

     

     
     public function subCat(Request $request)
     {
         $parent_id = $request->cat_id;
        //   dd($parent_id);
         $subcategories = LRcategory::where('id',$parent_id)
                            //    ->with('subcategories')
                                ->with(['subcategories' => function ($query) {
                                    $query->where('status', 1);
                                }])
                               ->get();

         return response()->json([
             'subcategories' => $subcategories
         ]);
     }

     public function createCategory(Request $request)
    {
        $categories = LRcategory::where('parent_id', 0)->orderby('name', 'asc')->get();

        if($request->method()=='GET')
        {
            return view('create-category', compact('categories'));
        }
        if($request->method()=='POST')
        {
            $validator = $request->validate([
                'name'      => 'required|unique:LRcategory',
                'parent_id' => 'required|numeric'
            ]);

            LRcategory::create([
                'name' => $request->name,
                'parent_id' =>$request->parent_id
            ]);

            return redirect()->back()->with('success', 'Category has been created successfully.');
        }
    }


       // Add New case name function
       public function submitAddCase(Request $request)
       {
           $request->validate([
               'name' => [
                   'required',
                   Rule::unique('LRcase')->where('name', request('name'))
               ],
           ]);
   
           $contact = new LRcase;
           $contact->name = $request->name;
           $contact->created_by = Auth::user()->id;
           $contact->created_at = date('Y-m-d H:i:s');
           $contact->save();
           return response()->json(['success' => 'Successfully']);
       }

        // Add New Parent function
        public function submitAddCat(Request $request)
        {

            if(!(request('parent_id'))){
               
                 $request->validate([

                'name' => [
                    'required',
                    Rule::unique('LRcategory')->where('parent_id', request('parent_id'))
                ],

                'parent_id' => [
                            'required',
                            Rule::unique('LRcategory')->where('name', request('name'))
                        ],
        
                ]);
                
            } else {
                 $request->validate([
                    'name' => [
                                'required',
                                Rule::unique('LRcategory', 'name')->where(function ($query) {
                                    return $query->where('parent_id', request('parent_id'));
                                }),
                            ],

                    'modelNumber' => [
                                'required',
                                Rule::unique('LRcategory', 'model_number')->where(function ($query) {
                                    return $query->where('parent_id', request('parent_id'));
                                }),
                            ],
                    'parent_id' => [
                                'required',
                                Rule::unique('LRcategory')->where('name', request('name'))
                            ],
            ]);
        }
           
    
            $contact = new LRcategory;
            $contact->name = $request->name;
            $contact->parent_id = $request->parent_id;
            $contact->model_number = $request->modelNumber;
            $contact->created_by = Auth::user()->id;
            $contact->created_at = date('Y-m-d H:i:s');
    
            $contact->save();
    
            return response()->json(['success' => 'Successfully']);
            
        }


            // Read Employee record by ID
    public function getSubCat1(Request $request)
    {

        ## Read POST data 
        $id = $request->post('cat_id');
        $empdata = LRcategory::find($id);

        $response = array();
        if (!empty($empdata)) {

            $response['id'] = $empdata->id;
            $response['name'] = $empdata->name;
            $response['modelNumber'] = $empdata->model_number;

            $response['success'] = 1;
        } else {
            $response['success'] = 0;
        }

        return response()->json($response);
    }
          // Read Employee record by ID
    public function getSubCat2(Request $request)
    {

        ## Read POST data 
        $id = $request->post('cat_id');
        $empdata = LRcategory::find($id)->where('status', 1);

        // $empdata = LRcategory::where('parent_id', '=', $id)->get();
        // echo "<pre>";
        // print_r($empdata);
        // exit;
        // dd($empdata);
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

    

    // Add New Parent function
    public function submitAddSubCat1(Request $request)
    {
        $request->validate([

            'name' => [
                'required',
                Rule::unique('LRcategory')->where('parent_id', request('parent_id'))
            ],
          'parent_id' => [
                    'required',
                    Rule::unique('LRcategory')->where('name', request('name'))
                ],

        ]);

        $contact = new LRcategory;
        $contact->name = $request->name;
        $contact->parent_id = $request->parent_id;
        $contact->created_by = Auth::user()->id;
        $contact->created_at = date('Y-m-d H:i:s');

        $contact->save();

        return response()->json(['success' => 'Successfully']);
        
    }


      // u function
    public function submitUpdate(Request $request)
    {

            $id = $request->input('id');

        // $request->validate([
        //                         'modelNumber' => 'required|unique:LRcategory,model_number',
        //                     ]);


        $request->validate([
                    'modelNumber' => [
                        'required',
                        Rule::unique('LRcategory', 'model_number')->ignore($id),
                    ],
                ]);
            $category = LRcategory::find($id);
            $data = [
                        "model_number"         => $request->input('modelNumber'),
                        "updated_by"           => Auth::user()->id,
                        "updated_at"           => date('Y-m-d H:i:s'),
                    ];

            $update = $category->update($data);
            if($update) {
                return response()->json(['success' => 'Successfully']);

            }else {
                return response()->json(['error' => 'Not Success!']);

            }        
    }

///////////////////////////////////////////
public function productSpecification(Request $request)
{
    // $spec = LRproductsSpec::where('parent_id',0)->get();
    $spec = LRproductsSpec::select('*')->where('parent_id',0)->orderBy('name', 'ASC')->get(); 
    // dd($spec);
    // return view('productSpecification',compact('spec'));
    return view('productSpecification',["spec" => $spec]);
} 
////////////////////
    // Add New Parent function
    public function addNewSpec(Request $request)
    {
        $request->validate([
            'name' => ['required', Rule::unique('LRproductsSpec')->where(function ($query) {
                            return $query->where('parent_id', 0);
                        })],
        ]);

        $contact = new LRproductsSpec;
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
            public function addNewSubSpec(Request $request)
            {
            //  dd($request->all());
                $request->validate([
                    'Subname' => [
                        'required',
                        Rule::unique('LRproductsSpec', 'name')
                            ->where(function ($query) use ($request) {
                                return $query->where('parent_id', $request->partIds);
                            }),
                    ],
                   ]);
             
                if (empty($request->subId) && $request->mode!=2) {
                    $contact = new LRproductsSpec;
                    $contact->parent_id = $request->partIds;
                    $contact->name = $request->Subname;
                    // $contact->part_value = $request->partValue;
                    $contact->created_by = Auth::user()->id;
                    $contact->created_at = date('Y-m-d H:i:s');
            
                    if ($contact->save()) {
                        return response()->json(['message' => 'Added Successfully!']);
                    } else {
                        return response()->json(['message' => 'Failed! Please check!']);
                    }
                }else{
                    $id = $request->subId;
                    $result = LRproductsSpec::find($id);
                    // dd();
                    if (!empty($result)) {
                        $updata['parent_id'] =  $request->parent_id;
                        $updata['name'] =  $request->Subname;
                        // $updata['part_value'] =  $request->partValue;
                        $updata['updated_by'] = Auth::user()->id;
                        $updata['updated_at'] = date('Y-m-d H:i:s');
            
                        if ($result->update($updata)) {
                            // $response['success'] = 1;
                            return response()->json(['success' => 1]);
                        } else {
                            return response()->json(['success' => 2]);
                        } 
                      }
                    }
                
            }
    public function getSpecificationData()
    {
            
        $category = LRproductsSpec::select('*')->where('parent_id',0);
            
            return Datatables::of($category)
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
                    $iconColor = $row->status == 1 ? 'green' : 'red';
    
                    $deleteButton = "";
                    $updateButton = "<button class='btn btn-sm btn-outline-primary getPartInfo' data-id='" . $row->id . "' data-name='" . $row->name . "' data-bs-toggle='modal' data-bs-target='#addNewSpecModal'><i class='fa fa-plus'></i></button>";
    
                    if (Auth::check() && Auth::user()->role_id == 2 && Auth::user()->current_team_id == 1) {
                             if($row->status == 1 ){
                                return  $deleteButton.'  '. $updateButton ;
                                } else {
                                return  $deleteButton ;
                                }
                    }
                })
                
       ->make();
        
        }

/////////////////////// for parts sub list data
public function getSubSpecData()
{
    // LRcategory::select('*')->where('parent_id',0);
    $CatQuery =LRproductsSpec::where('parent_id', '!=', 0);

    $datatable = Datatables::of($CatQuery)
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
            $iconColor = $row->status == 1 ? 'green' : 'red';

            $btnClass = $row->status == 1 ? 'btn-outline-success' : 'btn-outline-danger';
            $deleteButton = "<button class='btn btn-sm $btnClass disableProdSpec' data-id='" . $row->id . "' title='" . $iconNote . "'><i class='$iconClass'></i></button>";
            $updateButton = "<button class='fa fa-edit getPartSubInfo' data-id='" . $row->id . "' data-parent_id='" . $row->parent_id . "' data-name='" . $row->name . "' data-bs-toggle='modal' data-bs-target='#addNewSpecSubModal' type='button' title='Edit'></button>";

            if (Auth::check() && Auth::user()->role_id == 2 && Auth::user()->current_team_id == 1) {
                if($row->status == 1 ){
                return  $deleteButton.'  '. $updateButton ;
                } else {
                return  $deleteButton ;
                }
            } 
        });

      /////////////////////////////////////////////////////////////
        $categoryData = LRproductsSpec::all(); // Only fetched once

        // parent sub name
        $datatable->addColumn('name', function ($row) use ($categoryData) {
            $matchingPart = $categoryData->where('id', $row->parent_id)->first();
            return $matchingPart ? $matchingPart->name : 'sd-';
        });

        $datatable->filterColumn('name', function ($query, $keyword) use ($categoryData) {
            $matchingIds = $categoryData->filter(function ($item) use ($keyword) {
                return stripos($item->name, $keyword) !== false;
            })->pluck('id')->toArray();

            $query->whereIn('parent_id', $matchingIds);
        });

        // part sub name
        $datatable->addColumn('Subname', function ($row) {
            return $row->parent_id != 0 ? $row->name : '';
        });

        ///////////////////// sorting for custom columns
        $datatable->orderColumn('status', function ($query, $order) {
            $query->orderBy('status', $order);
        });

        $datatable->orderColumn('name', function ($query, $order) {
            $query->orderBy('parent_id', $order); // sorting by parent_id
        });

        $datatable->orderColumn('Subname', function ($query, $order) {
            $query->orderBy('name', $order); // sorting by part name
        });

        // filter for sub part name
        $datatable->filterColumn('Subname', function ($query, $keyword) {
            $query->where('name', 'like', "%{$keyword}%");
        });

        // dd($datatable);
        return $datatable->rawColumns(['action'])->make(true);
}
    
    // Update Employee record
    public function updateProdSpec(Request $request)
    {
        
        ## Read POST data
        $id = $request->post('id');

        $request->validate([
            'name' => [
                'required',
                Rule::unique('LRproductsSpec', 'name')
                    ->ignore($id) // exclude current record
                    ->where(function ($query) use ($request) {
                        return $query->where('parent_id',0);
                    }),
            ],
           ]);


        $empdata = LRproductsSpec::find($id);
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

    // Delete vendor address
    public function disableProdSpec(Request $request)
    {
        ## Read POST data
        $id = $request->post('id');
        // Retrieve the current status
        $currentStatus = DB::table('LRproductsSpec')
            ->where('id', $id)
            ->value('status');

        // Determine the new status
        $newStatus = $currentStatus == 1 ? 2 : 1;

        // Update the status
        $result = DB::table('LRproductsSpec')
            ->where('id', $id)
            ->update(['status' => $newStatus]);
            ///////////part sub update//////////
        $subStatus = DB::table('LRproductsSpec')
            ->where('parent_id', $id)
            ->value('status');    
        // Determine the new status
        $sub = $subStatus == 1 ? 2 : 1;
        // Update the status
        $subUpdate = DB::table('LRproductsSpec')
            ->where('parent_id', $id)
            ->update(['status' => $sub]);
        if ($result) {
            return response()->json(['message' => 'Updated Successfully!']);
        } else {
            return response()->json(['message' => 'Failed! Please check!']);
        }
    }
}