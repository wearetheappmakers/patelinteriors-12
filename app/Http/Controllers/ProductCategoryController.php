<?php
namespace App\Http\Controllers;
use App\User;
use DataTables;
use App\Holiday;
use App\Employee;
use App\ProductCategory;
use Auth;
use Illuminate\Http\Request;
class ProductCategoryController extends Controller
{
    public function __construct(ProductCategory $s)
    {
        $this->view = 'productcategory';
        $this->route = 'productcategory';
        $this->viewName = 'Category';
    }   
    public function index(Request $request)
    {
        if ($request->ajax()) {

            if(Auth::guard('employee')->check())
            {
                $query = ProductCategory::where('branch_id',Auth::user()->branch_id);
            }
            else
            {
                if(Auth::user()->role == 2)
                {
                    $query = ProductCategory::where('branch_id',Auth::user()->id);
                }
                else
                {
                    $query = ProductCategory::orderby('id','DESC');
                }
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
            //     $query = ProductCategory::latest();
            // }
            return Datatables::of($query)
            ->addColumn('action', function ($row) {
                $btn = '<a style="background: transparent;" href="'.route('user.productcategory.edit',$row->id).'" title="Edit details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: green;" class="la la-edit"></i>
                </a>&nbsp;&nbsp;&nbsp;';


                $btn .= '&nbsp;&nbsp;<a style="background: white;" href="'.route('user.productcategory.show',$row->id).'"  title="View Details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-eye"></i></a>&nbsp;&nbsp;&nbsp;';
                if(Auth::user()->role == 1)
                {

                    $btn .= '<a href="'.route('user.deletes.productcategory',$row->id).'" onclick="return confirm(`Are you sure?`)" style="background: white;" title="Delete details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: red;" class="la la-trash"></i></a>';
                }
                return $btn;
            })
            ->addColumn('singlecheckbox', function ($row) {
                $schk = view('layouts.singlecheckbox')->with(['id' => $row->id , 'status'=>$row->status])->render();
                return $schk;
            })
            ->rawColumns(['numbers','action','singlecheckbox'])
            ->make(true);
        } 
        return view('productcategory.index');
    }
    public function create()
    {
        $data['title'] = 'Add ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['branch'] = User::where('role',2)->get();
        $data['branch'] = User::where('role',2)->get();
        if(Auth::user()->role == 1)
        {
            $data['employee'] = Employee::get();
        }
        else
        {
            $data['employee'] = Employee::where('branch_id',Auth::user()->id)->get();
        }
        return view('general.add_form')->with($data);
    }
    public function store(Request $request)
    {
        $productcategory = new ProductCategory();
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $productcategory->branch_id = $request->branch_id;
                $productcategory->employee_id = $request->employee_id;
            }
            else
            {
                $productcategory->branch_id = Auth::user()->id;
                $productcategory->employee_id = $request->employee_id;
            }
        }
        else
        {
            $productcategory->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
            $productcategory->employee_id = Auth::user()->id;
        }
        $productcategory->name = $request->name;
        $productcategory->save();
        if($productcategory)
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
        $data['data'] = ProductCategory::where('id', $id)->first();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['branch'] = User::where('role',2)->get();
        if(Auth::user()->role == 1)
        {
            $data['employee'] = Employee::get();
        }
        else
        {
            $data['employee'] = Employee::where('branch_id',Auth::user()->id)->get();
        }
        return view('general.edit_form')->with($data);
    }

    public function show($id)
    {
        $data['title'] = 'View ' . $this->viewName;
        $data['data'] = ProductCategory::where('id', $id)->first();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['branch'] = User::where('role',2)->get();
        if(Auth::user()->role == 1)
        {
            $data['employee'] = Employee::get();
        }
        else
        {
            $data['employee'] = Employee::where('branch_id',Auth::user()->id)->get();
        }
        $data['branchname'] = User::where('id',$data['data']->branch_id)->value('name');
        return view('productcategory.show')->with($data);
    }
    public function update(Request $request, $id)
    {
        $productcategory = ProductCategory::where('id',$id)->first();
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $productcategory->branch_id = $request->branch_id;
                $productcategory->employee_id = $request->employee_id;
            }
            else
            {
                $productcategory->branch_id = Auth::user()->id;
                $productcategory->employee_id = $request->employee_id;
            }
        }
        else
        {
            $productcategory->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
            $productcategory->employee_id = Auth::user()->id;
        }
        $productcategory->name = $request->name;
        $productcategory->save();
        if($productcategory)
        {
            return response()->json(['status' => 'success']);
        }else
        {
            return response()->json(['status' => 'error']);
        }
    }
    public function delete($id)
    {
        $data = ProductCategory::where('id',$id)->delete();
        return back();
    }
}
