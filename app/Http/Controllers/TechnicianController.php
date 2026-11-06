<?php
namespace App\Http\Controllers;
use App\User;
use DataTables;
use App\Company;
use App\Employee;
use Auth;
use App\Technician;
use App\Client;
use App\ProductCategory;
use App\Product;
use Illuminate\Http\Request;

class TechnicianController extends Controller
{
    public function __construct(Technician $s)
    {
        $this->view = 'technician';
        $this->route = 'technician';
        $this->viewName = 'Technician';
    }   

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Technician::with('employee','branch','company','product','category')->get();
            if(Auth::user()->role == 2)
            {
                $query = Technician::with('employee','branch','company','product','category')->where('branch_id',Auth::user()->id)->get();
            }
            if($request->branch_id)
            {
                $query = $query->where('branch_id',$request->branch_id);
            }
            if($request->employee_id)
            {
                $query = $query->where('employee_id',$request->employee_id);
            }
            if($request->status)
            {
                $query = $query->where('status',$request->status);
            }
            return Datatables::of($query)
            ->addColumn('action', function ($row) {
                $btn = view('layouts.actionbtnpermission')->with(['id'=>$row->id,'route'=>'technician','per_value'=>'technician'])->render();    
                $btn .= '&nbsp;&nbsp;<a style="background: white;" href="'.route('user.technician.show',$row->id).'"  title="View Details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-eye"></i></a>';  
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
        }
        $data['branch'] = User::where('role',2)->get();
        if(Auth::user()->role == 2)
        {
            $data['employee'] = Employee::where('branch_id',Auth::user()->id)->where('status',1)->get();
        }
        return view('technician.index')->with($data);
    }

    public function create()
    {
        $data['title'] = 'Add ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 2)
            {
                $data['employees'] = Employee::where('status',1)->where('branch_id',Auth::user()->id)->get();
                $data['productcategory'] = ProductCategory::where('status',1)->where('branch_id',Auth::user()->id)->get();
            }
        }
        else
        {
            $branch = Employee::where('id',Auth::user()->id)->value('branch_id');
            $data['employees'] = Employee::where('status',1)->where('branch_id',$branch)->get();
            $data['productcategory'] = ProductCategory::where('status',1)->where('branch_id',$branch)->get();
        }
        $data['branch'] = User::where('role',2)->get();
        return view('general.add_form')->with($data);
    }

    public function store(Request $request)
    {
        $technician = new Technician();
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $technician->branch_id = $request->branch_id;
                $technician->employee_id = $request->employee_id;
            }
            else
            {
                $technician->branch_id = Auth::user()->id;
                $technician->employee_id = $request->employee_id;
            }
        }
        else
        {
            $technician->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
            $technician->employee_id = Auth::user()->id;
        }
        $technician->productcategory_id = $request->productcategory_id;
        $technician->company_id = $request->company_id;
        $technician->technician_name = $request->technician_name;
        $technician->save();

        if($technician)
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
        $data['data'] = Technician::where('id', $id)->first();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 2)
            {
                $data['productcategory'] = ProductCategory::where('status',1)->where('branch_id',Auth::user()->id)->get();
            }
            else
            {
                $data['employees'] = Employee::where('status',1)->get();
                $data['productcategory'] = ProductCategory::where('status',1)->get();
            }
        }
        else
        {
            $branch = Employee::where('id',Auth::user()->id)->value('branch_id');
            $data['productcategory'] = ProductCategory::where('status',1)->where('branch_id',$branch)->get();
        }
        $data['branch'] = User::where('role',2)->get();
        $data['company'] = Company::where('pro_category_id',$data['data']->productcategory_id)->where('status',1)->get();
        $data['product'] = Product::where('company_id',$data['data']->company_id)->where('status',1)->get();
        return view('general.edit_form')->with($data);
    }

    public function update(Request $request, $id)
    {
        $technician = Technician::where('id', $id)->first();
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $technician->employee_id = $request->employee_id;
            }
            else
            {
                $technician->employee_id = $request->employee_id;
            }
        }
        else
        {
            $technician->employee_id = Auth::user()->id;
        }
        $technician->productcategory_id = $request->productcategory_id;
        $technician->company_id = $request->company_id;
        $technician->technician_name = $request->technician_name;
        $technician->save();

        if($technician)
        {
            return response()->json(['status' => 'success']);
        }else
        {
            return response()->json(['status' => 'error']);
        }
    }

    public function show($id)
    {
        $data['data'] = Technician::where('id', $id)->first();
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 2)
            {
                $data['productcategory'] = ProductCategory::where('status',1)->where('branch_id',Auth::user()->id)->get();
            }
            else
            {
                $data['employees'] = Employee::where('status',1)->get();
                $data['productcategory'] = ProductCategory::where('status',1)->get();
            }
        }
        else
        {
            $branch = Employee::where('id',Auth::user()->id)->value('branch_id');
            $data['productcategory'] = ProductCategory::where('status',1)->where('branch_id',$branch)->get();
        }
        $data['branch'] = User::where('role',2)->get();
        $data['company'] = Company::where('pro_category_id',$data['data']->productcategory_id)->where('status',1)->get();
        $data['product'] = Product::where('company_id',$data['data']->company_id)->where('status',1)->get();
        return view('technician.show')->with($data);
    }

    public function getemployees(Request $request)
    {
        $data = Employee::where('branch_id',$request->branch_id)->where('status',1)->get();
        $html = "<option value=''>Select</option>";
        foreach ($data as $key => $value) {
            $html .= "<option value=".$value->id.">".$value->name."</option>";
        }
        $client = Client::where('branch_id',$request->branch_id)->get();
        $html1 = "<option value=''>Select</option>";
        foreach ($client as $keys => $values) {
            $html1 .= "<option value=".$values->id.">".$values->member."</option>";
        }
        $productcategory = ProductCategory::where('branch_id',$request->branch_id)->get();
        $html2 = "<option value=''>Select</option>";
        foreach ($productcategory as $keys => $values) {
            $html2 .= "<option value=".$values->id.">".$values->name."</option>";
        }
        return response()->json(['data'=>$html,'data1'=>$html1,'data2'=>$html2]);
    }

    public function getcompany(Request $request)
    {
        $data = Company::where('pro_category_id',$request->productcategory_id)->where('status',1)->get();
        $html = "<option value=''>Select</option>";
        foreach ($data as $key => $value) {
            $html .= "<option value=".$value->id.">".$value->name."</option>";
        }
        $data1 = Company::where('pro_category_id',$request->edit_productcategory_id)->where('status',1)->get();
        $html1 = "<option value=''>Select</option>";
        foreach ($data1 as $key => $value) {
            $html1 .= "<option value=".$value->id.">".$value->name."</option>";
        }
        return response()->json(['data'=>$html, 'data1'=>$html1]);
    }
}