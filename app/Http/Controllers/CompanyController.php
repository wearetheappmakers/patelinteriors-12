<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Company;
use App\User;
use App\Employee;
use App\ProductCategory;
use DataTables;

use Auth;
class CompanyController extends Controller
{
    public function __construct(Company $s)
    {
        $this->view = 'company';
        $this->route = 'company';
        $this->viewName = 'Company';
    }
    public function index(Request $request)
    {
        if ($request->ajax())
        {
            if(Auth::user()->role == 2)
            {
                $query = Company::where('branch_id',Auth::user()->id)->latest();
            }else{
                $query = Company::orderBy('id','DESC')->latest();
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
//     $query = Company::latest();
// }
            if($request->productcategory_id)
            {
                $query = $query->where('pro_category_id',$request->productcategory_id);
            }
            return Datatables::of($query)
            ->addColumn('action', function ($row) {
                $btn = '<a style="background: transparent;" href="'.route('user.company.edit',$row->id).'" title="Edit details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: green;" class="la la-edit"></i>
                </a>&nbsp;&nbsp;&nbsp;';
                $btn .= '&nbsp;&nbsp;<a style="background: white;" href="'.route('user.company.show',$row->id).'"  title="View Details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-eye"></i></a>&nbsp;&nbsp;&nbsp;';
                if(Auth::user()->role == 1)
                {
                    $btn .= '<a href="'.route('user.deletes.company',$row->id).'" onclick="return confirm(`Are you sure?`)" style="background: white;" title="Delete details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: red;" class="la la-trash"></i></a>';

                }
                return $btn;
            })
            ->addColumn('singlecheckbox', function ($row) {
                $schk = view('layouts.singlecheckbox')->with(['id' => $row->id , 'status'=>$row->status])->render();
                return $schk;
            })
            ->addColumn('pro_category_id', function ($row) {
                if($row->pro_category_id)
                {
                    $btn = $row->productcategory->name;
                }
                else
                {
                    $btn = '';
                }
                return $btn;
            })
            ->rawColumns(['action','singlecheckbox','pro_category_id'])
            ->make(true);
        }
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $data['category'] = ProductCategory::where('status',1)->get();
            }
            else
            {
                $data['category'] = ProductCategory::where('status',1)->where('branch_id',Auth::user()->id)->get();
            }
        }
        else
        {
            $data['category'] = ProductCategory::where('status',1)->where('branch_id',Auth::user()->id)->get();
        }
        return view('company.index')->with($data);
    }
    public function create()
    {
        $data['title'] = 'Add ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;

        // ✅ Branch list
        $data['branch'] = User::where('role',2)->where('status',1)->get();

        // ✅ Category logic (ALL ROLES FIXED)
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                // Admin
                $data['category'] = ProductCategory::where('status',1)->get();
            }
            else
            {
                // Branch user
                $data['category'] = ProductCategory::where('status',1)
                    ->where('branch_id', Auth::user()->id)
                    ->get();
            }
        }
        else if(Auth::guard('employee')->check())
        {
            // ✅ HR (role 9)
            $data['category'] = ProductCategory::where('status',1)
                ->where('branch_id', Auth::guard('employee')->user()->branch_id)
                ->get();
        }
        else
        {
            $data['category'] = [];
        }

        return view('general.add_form')->with($data);
    }
    public function store(Request $request)
    {
        $company = new Company();
        $company->pro_category_id = $request->pro_category_id;
        $company->name = $request->name;
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $company->branch_id = implode(',', $request->branch_id);
            }
            else
            {
                $company->branch_id = Auth::user()->id;
            }
        }
        else
        {
            $company->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
        }
        $company->save();
        if ($company){
            return response()->json(['status'=>'success']);
        }else{
            return response()->json(['status'=>'error']);
        }
    }    
    // public function show($id)
    // {
    //     $data['title'] = 'VIew ' . $this->viewName;
    //     $data['data'] = Company::where('id', $id)->first();
    //     $data['module'] = $this->viewName;
    //     $data['resourcePath'] = $this->view;
    //     $data['branch'] = User::where('role',2)->where('status',1)->get();
    //     if(Auth::guard('user')->check())
    //     {
    //         if(Auth::user()->role == 1)
    //         {
    //             $data['category'] = ProductCategory::where('status',1)->get();
    //         }
    //         else
    //         {
    //             $data['category'] = ProductCategory::where('status',1)->where('branch_id',Auth::user()->id)->get();
    //         }
    //     }
    //     else
    //     {
    //         $data['category'] = ProductCategory::where('status',1)->where('branch_id',Auth::user()->id)->get();
    //     }      

    //     $data['branchname'] = User::where('id',$data['data']->branch_id)->value('name');
    //     $data['categorys'] = ProductCategory::where('id',$data['data']->pro_category_id)->value('name');

    //     return view('company.show')->with($data);
    // }
    public function show($id)
    {
        $data['title'] = 'View ' . $this->viewName;
        $data['data'] = Company::where('id', $id)->first();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;

        $data['branch'] = User::where('role',2)->where('status',1)->get();

        // ✅ CATEGORY (same logic as edit/create)
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $data['category'] = ProductCategory::where('status',1)->get();
            }
            else
            {
                $data['category'] = ProductCategory::where('status',1)
                    ->where('branch_id', Auth::user()->id)
                    ->get();
            }
        }
        elseif(Auth::guard('employee')->check())
        {
            // ✅ HR (role 9)
            $data['category'] = ProductCategory::where('status',1)
                ->where('branch_id', Auth::user()->branch_id)
                ->get();
        }

        // ✅ MULTIPLE BRANCH FIX
        $branchIds = explode(',', $data['data']->branch_id);

        $data['branchname'] = User::whereIn('id', $branchIds)
            ->pluck('name')
            ->implode(', ');

        // ✅ CATEGORY NAME
        $data['categorys'] = ProductCategory::where('id', $data['data']->pro_category_id)
            ->value('name');

        return view('company.show')->with($data);
    }
    public function edit($id)
    {
        $data['title'] = 'Edit ' . $this->viewName;
        $data['data'] = Company::where('id', $id)->first();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;

        // Branch list (for admin)
        $data['branch'] = User::where('role',2)->where('status',1)->get();

        // ✅ CATEGORY FIX (VERY IMPORTANT)
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                // Admin → all categories
                $data['category'] = ProductCategory::where('status',1)->get();
            }
            else
            {
                // Branch login
                $data['category'] = ProductCategory::where('status',1)
                    ->where('branch_id', Auth::user()->id)
                    ->get();
            }
        }
        elseif(Auth::guard('employee')->check())
        {
            // ✅ HR LOGIN (role 9)
            $data['category'] = ProductCategory::where('status',1)
                ->where('branch_id', Auth::user()->branch_id)
                ->get();
        }

        return view('general.edit_form')->with($data);
    }
    // public function update(Request $request, $id)
    // {
    //     $param = $request->all();
    //     unset($param['_token'], $param['_method']);
    //     $company = Company::where('id', $id)->first();
    //     $company->update($param);
    //     if ($company){
    //         return response()->json(['status'=>'success']);
    //     }else{
    //         return response()->json(['status'=>'error']);
    //     }
    // }
    public function update(Request $request, $id)
    {
        $company = Company::find($id);

        $company->name = $request->name;
        $company->pro_category_id = $request->pro_category_id;

        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $company->branch_id = implode(',', $request->branch_id);
            }
            else
            {
                $company->branch_id = Auth::user()->id;
            }
        }
        else
        {
            $company->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
        }

        $company->save();

        if ($company){
            return response()->json(['status'=>'success']);
        }else{
            return response()->json(['status'=>'error']);
        }
    }
    public function delete($id)
    {
        $data = Company::where('id',$id)->delete();
        return back();
    }
    // public function getbranchcategory(Request $request)
    // {
    //     $data = ProductCategory::whereIn('branch_id',$request->branch_id)->get();

    //     $html = "<option value=''>Select</option>";
    //     foreach ($data as $key => $value) {
    //         $html .= "<option value=".$value->id.">".$value->name."</option>";
    //     }
    //     echo $html;
    // }
    public function getbranchcategory(Request $request)
{
    $branchIds = $request->branch_id;

    $data = ProductCategory::where(function($query) use ($branchIds){
        foreach ($branchIds as $id) {
            $query->orWhereRaw("FIND_IN_SET(?, branch_id)", [$id]);
        }
    })->get();

    $html = "<option value=''>Select</option>";
    foreach ($data as $value) {
        $html .= "<option value=".$value->id.">".$value->name."</option>";
    }

    return $html;
}
}