<?php
namespace App\Http\Controllers;
use App\User;
use App\Arid;
use App\Employee;
use App\Landmark;
use App\Country;
use App\State;
use App\City;
use Illuminate\Http\Request;
use Auth;
use DataTables;
class AridController extends Controller
{
    public function __construct(Arid $s)
    {
        $this->view = 'arid';
        $this->route = 'arid';
        $this->viewName = 'Ar/Id';
    }   
    public function index(Request $request)
    {
        if(Auth::guard('employee')->check())
        {
            if ($request->ajax()) {
                $query = Arid::where('branch_id',Auth::user()->branch_id)->latest();

                return Datatables::of($query)
                ->addColumn('action', function ($row) {
                    $btn = '<a style="background: transparent;" href="'.route('user.arid.edit',$row->id).'" title="Edit details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: green;" class="la la-edit"></i>
                    </a>&nbsp;&nbsp;&nbsp;';
                    $btn .= '<a style="background: white;" href="'.route('user.arid.show',$row->id).'"  title="View Details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-eye"></i></a>&nbsp;&nbsp;&nbsp;';
                    return $btn;
                })
                ->addColumn('singlecheckbox', function ($row) {
                    $schk = view('layouts.singlecheckbox')->with(['id' => $row->id , 'status'=>$row->status])->render();
                    return $schk;
                })
                ->editColumn('branch_id', function ($row) {
                    $btn = $row->branch->name;
                    return $btn;
                })
                ->editColumn('city_id', function ($row) {
                    $btn = City::where('id',$row->city_id)->value('name');
                    return $btn;
                })
                ->editColumn('type', function ($row) {
                    if($row->type == 1)
                    {
                        $btn = 'Ar';
                    }else{
                        $btn = 'ID';
                    }
                    return $btn;
                })
                ->rawColumns(['numbers','action','singlecheckbox','branch_id'])
                ->make(true);
            } 
        }
        else
        {
            if ($request->ajax()) {
                $query = Arid::latest();
                if(Auth::user()->role == 2)
                {
                    $query = Arid::where('branch_id',Auth::user()->id)->latest();
                }
                if($request->branch_id)
                {
                    $query->where('branch_id',$request->branch_id);
                }
                if($request->employee_id)
                {
                    $query->where('employee_id',$request->employee_id);
                }

                return Datatables::of($query)

                ->addColumn('action', function ($row) {
                    $btn = '<a style="background: transparent;" href="'.route('user.arid.edit',$row->id).'" title="Edit details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: green;" class="la la-edit"></i>
                    </a>&nbsp;&nbsp;&nbsp;';
                    $btn .= '&nbsp;&nbsp;<a style="background: white;" href="'.route('user.arid.show',$row->id).'"  title="View Details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-eye"></i></a>&nbsp;&nbsp;&nbsp;';

                    if(Auth::user()->role == 1)
                    {
                        $btn .= '<a href="'.route('user.delete.arid',$row->id).'" onclick="return confirm(`Are you sure?`)" style="background: white;" title="Delete details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: red;" class="la la-trash"></i></a>';
                    }
                    return $btn;
                })
                ->addColumn('singlecheckbox', function ($row) {
                    $schk = view('layouts.singlecheckbox')->with(['id' => $row->id , 'status'=>$row->status])->render();
                    return $schk;
                })
                ->editColumn('branch_id', function ($row) {
                    $btn = $row->branch->name;
                    return $btn;
                })
                ->editColumn('city_id', function ($row) {
                    $btn = City::where('id',$row->city_id)->value('name');
                    return $btn;
                })
                ->editColumn('type', function ($row) {
                    if($row->type == 1)
                    {
                        $btn = 'Ar';
                    }else{
                        $btn = 'ID';
                    }
                    return $btn;
                })
                ->rawColumns(['numbers','action','singlecheckbox','branch_id'])
                ->make(true);
            } 
        }
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        if(Auth::user()->role == 1)
        {
            $data['employee'] = Employee::get();
        }
        else
        {
            $data['employee'] = Employee::where('branch_id',Auth::user()->id)->get();
        }
        return view('arid.index')->with($data);
    }
    public function create()
    {
        $data['title'] = 'Add ' . $this->viewName;
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
        if(Auth::guard('employee')->check())
        {
            $emp = Employee::where('id',Auth::user()->id)->value('branch_id');
        }
        $data['country'] = Country::get();
        return view('general.add_form')->with($data);
    }
    public function store(Request $request)
    {
        $arid = new Arid();
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $arid->branch_id = $request->branch_id;
                $arid->employee_id = $request->employee_id;
            }
            else
            {
                $arid->branch_id = Auth::user()->id;
                $arid->employee_id = $request->employee_id;
            }
        }
        else
        {
            $arid->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
            $arid->employee_id = Auth::user()->id;
        }
        $check = Arid::where('number',$request->number)->where('branch_id',$arid->branch_id)->exists();
        if($check){
            return response()->json(['status' => 'required', 'message' => 'Duplicate Number']);
        }
        $arid->type = $request->type;
        $arid->name = $request->name;
        $arid->number = $request->number;
        $arid->office_no = $request->office_no;
        $arid->firm_name = $request->firm_name;
        $arid->firm_address = $request->firm_address;
        $arid->country_id = $request->country_id;
        $arid->state_id = $request->state_id;
        $arid->city_id = $request->city_id;
        $arid->email = $request->email;
        $arid->dob = $request->dob;
        $arid->save();
        if($arid)
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
        $data['data'] = Arid::where('id', $id)->first();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['branch'] = User::where('role',2)->get();
        $data['country'] = Country::get();
        $data['state'] = State::where('country_id',$data['data']->country_id)->get();
        $data['city'] = City::where('state_id',$data['data']->state_id)->get();
        if(Auth::user()->role == 1)
        {
            $data['employee'] = Employee::get();
        }
        else
        {
            $data['employee'] = Employee::where('branch_id',Auth::user()->id)->get();
        }
        if(Auth::guard('employee')->check())
        {
            $emp = Employee::where('id',Auth::user()->id)->value('branch_id');
        }
        return view('general.edit_form')->with($data);
    }
    public function update(Request $request, $id)
    {
        $arid = Arid::where('id',$id)->first();
        $check = Arid::where('number',$request->number)->where('branch_id',$arid->branch_id)->where('id','!=',$id)->exists();
        if($check){
            return response()->json(['status' => 'required', 'message' => 'Duplicate Number']);
        }
        $arid->type = $request->type;
        $arid->name = $request->name;
        $arid->number = $request->number;
        $arid->office_no = $request->office_no;
        $arid->firm_name = $request->firm_name;
        $arid->firm_address = $request->firm_address;
        $arid->country_id = $request->country_id;
        $arid->state_id = $request->state_id;
        $arid->city_id = $request->city_id;
        $arid->email = $request->email;
        $arid->dob = $request->dob;
        $arid->save();
        if($arid)
        {
            return response()->json(['status' => 'success']);
        }else
        {
            return response()->json(['status' => 'error']);
        }
    }
    public function getemployee(Request $request)
    {
        $data = Employee::where('branch_id',$request->branch_id)->get();
        $html1 = "<option value=''>Select</option>";
        foreach ($data as $key => $value) {
            $html1 .= "<option value=".$value->id.">".$value->name."</option>";
        }

        return response()->json(['data1'=>$html1 ]);
    }
    public function delete($id)
    {
        $data = Arid::where('id',$id)->delete();
        return back();
    }
    public function show($id)
    {
        $data['title'] = 'View ' . $this->viewName;
        $data['data'] = Arid::where('id', $id)->first();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['branch'] = User::where('role',2)->get();
        $data['branchname'] = User::where('id',$data['data']->branch_id)->value('name');
        $data['emp'] = Employee::where('id',$data['data']->employee_id)->value('name');

        if(Auth::user()->role == 1)
        {
            $data['employee'] = Employee::get();
        }
        else
        {
            $data['employee'] = Employee::where('branch_id',Auth::user()->id)->get();
        }
        if(Auth::guard('employee')->check())
        {
            $emp = Employee::where('id',Auth::user()->id)->value('branch_id');
        }
        return view('arid.show')->with($data);
    }
}