<?php
namespace App\Http\Controllers;
use App\User;
use DataTables;
use App\LeadSource;
use Auth;
use App\Employee;
use App\ProductCategory;
use Illuminate\Http\Request;
class LeadSourceController extends Controller
{
    public function __construct(LeadSource $s)
    {
        $this->view = 'leadsource';
        $this->route = 'leadsource';
        $this->viewName = 'leadsource';
    }   
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = LeadSource::orderBy('id','DESC')->latest();
            if(Auth::guard('employee')->check())
            {
               $query = LeadSource::where('branch_id',Auth::user()->branch_id)->latest();
            }
            else
            {
                if(Auth::user()->role == 2)
                {
                    $query = LeadSource::where('branch_id',Auth::user()->id)->latest();
                }else{
                    $query = LeadSource::orderBy('id','DESC')->latest();
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
        //     $query = LeadSource::latest();
        // }
        return Datatables::of($query)
        ->addColumn('action', function ($row) {
            $btn = '<a style="background: transparent;" href="'.route('user.leadsource.edit',$row->id).'" title="Edit details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: green;" class="la la-edit"></i>
            </a>&nbsp;&nbsp;&nbsp;';

            $btn .= '<a style="background: white;" href="'.route('user.leadsource.show',$row->id).'"  title="View Details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-eye"></i></a>&nbsp;&nbsp;';

            if(Auth::user()->role == 1)
            {
                $btn .= '<a href="'.route('user.deletesleadsource.clones',$row->id).'" onclick="return confirm(`Are you sure?`)" style="background: white;" title="Delete details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: red;" class="la la-trash"></i></a>';
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
    return view('leadsource.index');
}
public function create()
{
    $data['title'] = 'Add ' . $this->viewName;
    $data['module'] = $this->viewName;
    $data['resourcePath'] = $this->view;
    $data['branch'] = User::where('role',2)->where('status',1)->get();
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
    $leadsource = new LeadSource();
    $leadsource->name = $request->name;
    if(Auth::guard('user')->check())
    {
        if(Auth::user()->role == 1)
        {
            $leadsource->branch_id = $request->branch_id;
            $leadsource->employee_id = $request->employee_id;
        }
        else
        {
            $leadsource->branch_id = Auth::user()->id;
            $leadsource->employee_id = $request->employee_id;
        }
    }
    else
    {
        $leadsource->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');

    }
    $leadsource->save();
    if($leadsource)
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
    $data['data'] = LeadSource::where('id', $id)->first();
    $data['module'] = $this->viewName;
    $data['resourcePath'] = $this->view;
    $data['branch'] = User::where('role',2)->where('status',1)->get();
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
    $data['title'] = 'Edit ' . $this->viewName;
    $data['data'] = LeadSource::where('id', $id)->first();
    $data['module'] = $this->viewName;
    $data['resourcePath'] = $this->view;
    $data['branch'] = User::where('role',2)->where('status',1)->get();
    if(Auth::user()->role == 1)
    {
        $data['employee'] = Employee::get();
    }
    else
    {
        $data['employee'] = Employee::where('branch_id',Auth::user()->id)->get();
    }
    return view('leadsource.show')->with($data);
}
public function update(Request $request, $id)
{
    $leadsource = LeadSource::where('id',$id)->first();
    $leadsource->name = $request->name;
    if(Auth::guard('user')->check())
    {
        if(Auth::user()->role == 1)
        {
            $leadsource->branch_id = $request->branch_id;
            $leadsource->employee_id = $request->employee_id;
        }
        else
        {
            $leadsource->branch_id = Auth::user()->id;
            $leadsource->employee_id = $request->employee_id;
        }
    }
    else
    {
        $leadsource->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');

    }
    $leadsource->save();
    if($leadsource)
    {
        return response()->json(['status' => 'success']);
    }else
    {
        return response()->json(['status' => 'error']);
    }
}
public function deleteleadsourceclone($id)
{
    $data = LeadSource::where('id',$id)->delete();
    return back();
}
}
