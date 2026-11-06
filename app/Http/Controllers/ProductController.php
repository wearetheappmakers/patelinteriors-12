<?php
namespace App\Http\Controllers;
use App\User;
use DataTables;
use App\Holiday;
use App\ProductCategory;
use App\Warehouse;
use App\WarehouseInventory;
use App\Product;
use App\Employee;
use App\Company;
use App\Download;
use Auth;
use File;
use Illuminate\Http\Request;
use App\Imports\ProductImport;
use App\Exports\ProductExport;
use App\Imports\ProductImportMrp;
use App\Exports\ProductExportMrp;
use Maatwebsite\Excel\Facades\Excel;
class ProductController extends Controller
{
    public function __construct(Product $s)
    {
        $this->view = 'product';
        $this->route = 'product';
        $this->viewName = 'Product';
    }   
    public function productexportmrp()
    {
        \Artisan::call('Export:check');
    }
    public function productdownload(Request $request)
    {
        if ($request->ajax())
        {
            $query = Download::latest();
            
            return Datatables::of($query)
            ->addColumn('dates', function ($row) {
                $bank = date('d-m-Y H:i a', strtotime($row->created_at));
                return $bank;
            })
            ->addColumn('files', function ($row) {
                $path = env('APP_URL').'storage/exports/'.$row->file_name;
                // dd($path);
                if(File::exists(storage_path('storage/exports/'.$row->file_name)))
                {
                    $bank = '<a href='.$path.' download target="_blank">'.$row->file_name.'</a>';
                }else{
                    $bank = '<p style="color: red;">Still in process, wait until generated.<p>';
                }
                
                return $bank;
            })
            ->addColumn('action', function ($row) {
               $btn = '<a style="background: white; " href="'.route('user.product.download.delete',$row->id).'" onclick="return confirm(`Are you sure?`)" title="Delete " class="btn btn-sm btn-clean btn-icon btn-icon-md delete-record">
               <i style="color: red;" class="la la-trash"></i>
               </a>';
               return $btn;
           })
            ->rawColumns(['dates','files','action'])
            ->make(true);
        }
        return view('product.download');
    }
    public function downloaddelete($id)
    {
        $download = Download::where('id',$id)->first();
        
        if(File::exists(public_path('storage/exports/'.$download->file_name)))
        {
            unlink(public_path('storage/exports/'.$download->file_name));
        }
        $download->delete();
        return redirect()->back();
    }
    public function index(Request $request)
    {
        //      if(Auth::guard('employee')->check())
        //     {
        //     if ($request->ajax()) {
        //          $query = Product::where('employee_id',Auth::user()->id)->latest()->get();
        //         if($request->status == 1)
        //         {
        //             // dd($request->status);
        //             $query = $query->where('status',1);
        //         }
        //         else if($request->status == 0)
        //         {
        //              $query = $query->where('status',0);
        //         }
        //         elseif($request->status == 2) 
        //         {
        //             $query = $query;
        //         }
        //         else
        //         {
        //             $query = Product::latest();
        //         }
        //         if($request->productcategory_id)
        //         {
        //              $query = $query->where('productcategory_id',$request->productcategory_id);
        //         }
        //         return Datatables::of($query)
        //         ->addColumn('action', function ($row) {
        //             // $btn = '<a href="'.route('user.edit', $row->id).'">Edit</a>&nbsp&nbsp&nbsp<a href="'.route('user.delete', $row->id).'">Delete</a>'; 
        //                  $btn = view('layouts.actionbtnpermission')->with(['id'=>$row->id,'route'=>'product','per_value'=>'product'])->render();      
        //             return $btn;
        //         })
        //         ->addColumn('singlecheckbox', function ($row) {
        //                 $schk = view('layouts.singlecheckbox')->with(['id' => $row->id , 'status'=>$row->status])->render();
        //             return $schk;
        //         })
        //         ->addColumn('productcategory_id', function ($row) {
        //             $btn = $row->productcategory->name;
        //             return $btn;
        //         })
        //         ->rawColumns(['numbers','action','singlecheckbox','productcategory_id'])
        //         ->make(true);
        //     } 
        //     $data['productcategory'] = ProductCategory::get();
        // }
        // else
        // {
        //      if ($request->ajax()) {
        //         $query = Product::latest();
        //         if(Auth::user()->role == 2)
        //         {
        //             $query = Product::where('branch_id',Auth::user()->id)->latest()->get();
        //             // dd(Auth::user());
        //         }
        //         if($request->status == 1)
        //         {
        //             // dd($request->status);
        //             $query = $query->where('status',1);
        //         }
        //         else if($request->status == 0)
        //         {
        //              $query = $query->where('status',0);
        //         }
        //         elseif($request->status == 2) 
        //         {
        //             $query = $query;
        //         }
        //         else
        //         {
        //             $query = Product::latest();
        //         }
        //         if($request->productcategory_id)
        //         {
        //              $query = $query->where('productcategory_id',$request->productcategory_id);
        //         }
        //         return Datatables::of($query)
        //         ->addColumn('action', function ($row) {
        //             // $btn = '<a href="'.route('user.edit', $row->id).'">Edit</a>&nbsp&nbsp&nbsp<a href="'.route('user.delete', $row->id).'">Delete</a>'; 
        //                  $btn = view('layouts.actionbtnpermission')->with(['id'=>$row->id,'route'=>'product','per_value'=>'product'])->render();      
        //             return $btn;
        //         })
        //         ->addColumn('singlecheckbox', function ($row) {
        //                 $schk = view('layouts.singlecheckbox')->with(['id' => $row->id , 'status'=>$row->status])->render();
        //             return $schk;
        //         })
        //         ->addColumn('productcategory_id', function ($row) {
        //             $btn = $row->productcategory->name;
        //             return $btn;
        //         })
        //         ->rawColumns(['numbers','action','singlecheckbox','productcategory_id'])
        //         ->make(true);
        //     } 
        //     $data['productcategory'] = ProductCategory::get();
        // }
        if ($request->ajax()) {

            $query = Product::orderBy('id','DESC');
            if(Auth::guard('user')->check())
            {
                if(Auth::user()->role == 1)
                {
                    $query->latest();
                } else {
                    $query->where('branch_id',Auth::user()->id)->latest();
                }
            }
            
            if(Auth::guard('employee')->check())
            {
                $query->where('branch_id',Auth::user()->branch_id)->latest();
            }

            // if($request->status == 1)
            // {
            //     $query = $query->where('status',1);
            // }
            // else if($request->status == 0)
            // {
            //     $query = $query->where('status',0);
            // }
            // elseif($request->status == 2) 
            // {
            //     $query = $query;
            // }
            // else
            // {
            //     $query = Product::latest();
            // }
            if($request->company_id)
            {
                $query = $query->where('company_id',$request->company_id);
            }
            if($request->productcategory_id)
            {
                $query = $query->where('productcategory_id',$request->productcategory_id);
            }
            return Datatables::of($query)
            ->addColumn('action', function ($row) {
                $btn = '<a style="background: transparent;" href="'.route('user.product.edit',$row->id).'" title="Edit details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: green;" class="la la-edit"></i>
                </a>&nbsp;&nbsp;&nbsp;';

                $btn .= '&nbsp;&nbsp;<a style="background: white;" href="'.route('user.product.show',$row->id).'"  title="View Details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-eye"></i></a>&nbsp;&nbsp;&nbsp;';

                if(Auth::user()->role == 1)
                {
                    $btn .= '<a href="'.route('user.deletes.product',$row->id).'" onclick="return confirm(`Are you sure?`)" style="background: white;" title="Delete details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: red;" class="la la-trash"></i></a>';
                    
                }
                return $btn;
            })
            ->addColumn('singlecheckbox', function ($row) {
                $schk = view('layouts.singlecheckbox')->with(['id' => $row->id , 'status'=>$row->status])->render();
                return $schk;
            })
            ->addColumn('company_id', function ($row) {
                $btn = $row->company->name;
                return $btn;
            })
            ->addColumn('productcategory_id', function ($row) {
                if($row->productcategory_id)
                {
                    $btn = $row->productcategory->name;
                }
                else
                {
                    $btn = '';
                }
                return $btn;
            })
            ->rawColumns(['numbers','action','singlecheckbox','productcategory_id','company_id'])
            ->make(true);
        } 

        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $data['productcategory'] = ProductCategory::where('status',1)->get();
                $data['company'] = Company::where('status',1)->get();
            } else {
                $data['productcategory'] = ProductCategory::where('status',1)->where('branch_id',Auth::user()->id)->get();
                $data['company'] = Company::where('status',1)->where('branch_id',Auth::user()->id)->get();
            }
        }
        
        if(Auth::guard('employee')->check())
        {
            $data['productcategory'] = ProductCategory::where('status',1)->where('branch_id',Auth::user()->branch_id)->get();
            $data['company'] = Company::where('status',1)->where('branch_id',Auth::user()->branch_id)->get();
        }
        return view('product.index')->with($data);
    }
    public function create()
    {
        $data['title'] = 'Add ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;

        $data['branch'] = User::where('role',2)->where('status',1)->get();
        
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $data['productcategory'] = ProductCategory::where('status',1)->get();
                $data['company'] = Company::where('status',1)->get();
                $data['employee'] = Employee::get();
            }else {
                $data['productcategory'] = ProductCategory::where('status',1)->where('branch_id',Auth::user()->id)->get();
                $data['company'] = Company::where('status',1)->where('branch_id',Auth::user()->id)->get();
                $data['employee'] = Employee::where('branch_id',Auth::user()->id)->get();
            }
        }

        if(Auth::guard('employee')->check())
        {
            $data['productcategory'] = ProductCategory::where('status',1)->where('branch_id',Auth::user()->branch_id)->get();
            $data['company'] = Company::where('status',1)->where('branch_id',Auth::user()->branch_id)->get();
            $data['employee'] = Employee::where('branch_id',Auth::user()->branch_id)->get();
        }
        return view('general.add_form')->with($data);
    }
    public function store(Request $request)
    {
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $branch_id = $request->branch_id;
            }
            else
            {
                $branch_id = Auth::user()->id;
            }
        }
        else
        {
            $branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
        }

        $chk_product = Product::where('branch_id',$branch_id)->where('code',$request->code)->first();
        if(isset($chk_product))
        {
            return response()->json(['status' => 'required', 'message' => 'Duplicate SKU Code Entered !!!']);
        }
        
        $product = new Product();
        $product->product = $request->product;
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $product->branch_id = $request->branch_id;
                $product->employee_id = $request->employee_id;
            }
            else
            {
                $product->branch_id = Auth::user()->id;
                $product->employee_id = $request->employee_id;
            }
        }
        else
        {
            $product->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
            $product->employee_id = Auth::user()->id;
        }
        $product->company_id = $request->company_id;
        $product->code = $request->code;
        $product->price = $request->price;
        $product->productcategory_id = $request->productcategory_id;
        $product->unit = $request->unit;
        $product->description = $request->description;
        $product->discount = $request->discount;
        $product->discount_type = $request->discount_type;
        if(isset($request['image1']))
        {
            $imageName = time().'-'. $request->image1->getClientOriginalName();
            $request->image1->move(public_path('product/'),$imageName);
            $product->image1 = $imageName;
        }
        if(isset($request['image2']))
        {
            $imageName = time().'-'. $request->image2->getClientOriginalName();
            $request->image2->move(public_path('product/'),$imageName);
            $product->image2 = $imageName;
        }
        $product->save();
        $warehouse = Warehouse::where('branch_id',$product->branch_id)->get();
        foreach ($warehouse as $key => $value) {
            $inventory =  new WarehouseInventory();
            $inventory->branch_id = $value->branch_id;
            $inventory->warehouse_id = $value->id;
            $inventory->product_id = $product->id;
            $inventory->stock = 0;
            $inventory->used = 0;
            $inventory->actual_stock = 0;
            $inventory->save();
        }
        if($product)
        {
            return response()->json(['status' => 'success']);
        }else
        {
            return response()->json(['status' => 'error']);
        }
    }
    public function edit($id)
    {
        $data['title'] = 'Edit ' . $this->viewName;
        $data['data'] = Product::where('id', $id)->first();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        
        $data['branch'] = User::where('role',2)->get();
        
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $data['productcategory'] = ProductCategory::where('status',1)->get();
                $data['company'] = Company::where('status',1)->get();
                $data['employee'] = Employee::get();
            }else {
                $data['productcategory'] = ProductCategory::where('status',1)->where('branch_id',Auth::user()->id)->get();
                $data['company'] = Company::where('status',1)->where('branch_id',Auth::user()->id)->get();
                $data['employee'] = Employee::where('branch_id',Auth::user()->id)->get();
            }
        }

        if(Auth::guard('employee')->check())
        {
            $data['productcategory'] = ProductCategory::where('status',1)->where('branch_id',Auth::user()->branch_id)->get();
            $data['company'] = Company::where('status',1)->where('branch_id',Auth::user()->branch_id)->get();
            $data['employee'] = Employee::where('branch_id',Auth::user()->branch_id)->get();
        }
        return view('general.edit_form')->with($data);
    }
    public function show($id)
    {
        $data['title'] = 'View ' . $this->viewName;
        $data['data'] = Product::where('id', $id)->first();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        
        $data['branch'] = User::where('role',2)->get();
        
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $data['productcategory'] = ProductCategory::where('status',1)->get();
                $data['company'] = Company::where('status',1)->get();
                $data['employee'] = Employee::get();
            }else {
                $data['productcategory'] = ProductCategory::where('status',1)->where('branch_id',Auth::user()->id)->get();
                $data['company'] = Company::where('status',1)->where('branch_id',Auth::user()->id)->get();
                $data['employee'] = Employee::where('branch_id',Auth::user()->id)->get();
            }
        }

        if(Auth::guard('employee')->check())
        {
            $data['productcategory'] = ProductCategory::where('status',1)->where('branch_id',Auth::user()->branch_id)->get();
            $data['company'] = Company::where('status',1)->where('branch_id',Auth::user()->branch_id)->get();
            $data['employee'] = Employee::where('branch_id',Auth::user()->branch_id)->get();
        }
        $data['branchname'] = User::where('id',$data['data']->branch_id)->value('name');
        $data['category'] = ProductCategory::where('id',$data['data']->productcategory_id)->value('name');
        $data['companys'] = Company::where('id',$data['data']->company_id)->value('name');


        return view('product.show')->with($data);
    }
    public function update(Request $request, $id)
    {
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $branch_id = $request->branch_id;
            }
            else
            {
                $branch_id = Auth::user()->id;
            }
        }
        else
        {
            $branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
        }

        $chk_product = Product::where('id','!=',$id)->where('branch_id',$branch_id)->where('code',$request->code)->first();
        if(isset($chk_product))
        {
            return response()->json(['status' => 'required', 'message' => 'Duplicate SKU Code Entered !!!']);
        }

        $product = Product::where('id',$id)->first();
        $product->product = $request->product;
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $product->branch_id = $request->branch_id;
                $product->employee_id = $request->employee_id;
            }
            else
            {
                $product->branch_id = Auth::user()->id;
                $product->employee_id = $request->employee_id;
            }
        }
        else
        {
            $product->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
            $product->employee_id = Auth::user()->id;
        }
        $product->company_id = $request->company_id;
        $product->code = $request->code;
        $product->price = $request->price;
        $product->productcategory_id = $request->productcategory_id;
        $product->unit = $request->unit;
        $product->discount = $request->discount;
        $product->description = $request->description;
        $product->discount_type = $request->discount_type;
        if(isset($request['image1']))
        {
            $imageName = time().'-'. $request->image1->getClientOriginalName();
            $request->image1->move(public_path('product/'),$imageName);
            $product->image1 = $imageName;
        }
        if(isset($request['image2']))
        {
            $imageName = time().'-'. $request->image2->getClientOriginalName();
            $request->image2->move(public_path('product/'),$imageName);
            $product->image2 = $imageName;
        }
        $product->save();
        if($product)
        {
            return response()->json(['status' => 'success']);
        }else
        {
            return response()->json(['status' => 'error']);
        }
    }
    public function product_export() 
    {
        return Excel::download(new ProductExport, 'product.xlsx');
    }
    public function product_import(Request $request) 
    {
        try {
            Excel::import(new ProductImport, $request->file('product_excel_import'));
            return response()->json(['status' => 'success']);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {

            $failures = $e->failures();
            $errorMessage = [];
            foreach ($failures as $failure) {
                $errorMessages = $failure->errors();
                $errorString = "At Row " . $failure->row() . ": " . implode(", ", $errorMessages);
                $errorMessage[] = $errorString;
            }
            return response()->json(['status' => 'error', 'data' => $errorMessage]);
        } catch (\Exception $ex) {

            return response()->json(['status'=>'error','data'=>$ex->getMessage()]);
        }
    }
    public function getproductcategory(Request $request)
    {
        $data = ProductCategory::where('branch_id',$request->branch_id)->get();
        $html = "<option value=''>Select</option>";
        foreach ($data as $key => $value) {
            $html .= "<option value=".$value->id.">".$value->name."</option>";
        }
        echo $html;
    }
    public function getcompany(Request $request)
    {
        // if(Auth::user()->role == 1)
        // {
        //     $data = Company::where('pro_category_id',$request->productcategory_id)->get();
        // }else
        // {
        //     $data = Company::where('pro_category_id',$request->productcategory_id)->get();
        // }
        $data = Company::where('pro_category_id',$request->productcategory_id)->get();
        $html = "<option value=''>Select</option>";
        foreach ($data as $key => $value) {
            $html .= "<option value=".$value->id.">".$value->name."</option>";
        }
        echo $html;
    }
    public function product_exportmrp() 
    {
        return Excel::download(new ProductExportMrp, 'product.xlsx');
    }
    public function product_importmrp(Request $request) 
    {
        // if($request->ajax() && $request->isMethod('post')){
        //     Excel::import(new ProductImportMrp, $request->file('product_excel'));
        //     return response()->json(['status'=>'success']);
        // } else {
        //     return view('home');
        // }

        try {
            Excel::import(new ProductImportMrp, $request->file('product_excel'));
            return response()->json(['status' => 'success']);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {

            $failures = $e->failures();
            $errorMessage = [];
            foreach ($failures as $failure) {
                $errorMessages = $failure->errors();
                $errorString = "At Row " . $failure->row() . ": " . implode(", ", $errorMessages);
                $errorMessage[] = $errorString;
            }
            return response()->json(['status' => 'error', 'data' => $errorMessage]);
        } catch (\Exception $ex) {

            return response()->json(['status'=>'error','data'=>$ex->getMessage()]);
        }
    }
    public function delete($id)
    {
        $data = Product::where('id',$id)->delete();
        return back();
    }
    public function getcategory(Request $request)
    {
        $data = ProductCategory::where('branch_id',$request->branch_id)->get();
        $html = "<option value=''>Select</option>";
        foreach ($data as $key => $value) {
            $html .= "<option value=".$value->id.">".$value->name."</option>";
        }
        echo $html;
    }
}