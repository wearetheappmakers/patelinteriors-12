<?php
namespace App\Http\Controllers;
use App\User;
use DataTables;
use App\Employee;
use App\EmployeeChild;
use App\ProductCategory;
use App\Group;
use App\Role;
use Auth;
use Illuminate\Http\Request;
class EmployeeController extends Controller
{
    public function __construct(Employee $s)
    {
        $this->view = 'employee';
        $this->route = 'employee';
        $this->viewName = 'Employee';
    }
    
    public function index(Request $request)
    {
        $roles = Role::pluck('name','id')->toArray();

        if ($request->ajax()) {
            $query = Employee::latest();
            if(Auth::user()->role == 2)
            {
                $query = Employee::where('branch_id',Auth::user()->id)->latest();
            }
            if($request->status == 1)
            {
                $query = $query->where('status',1);
            }
            else if($request->status == 0)
            {
                $query = $query->where('status',0);
            }
            else
            {
                $query = $query;
            }
            if($request->branch_id)
            {
                $query = $query->where('branch_id',$request->branch_id)->latest();
            }
            if($request->role)
            {
                $query = $query->where('role',$request->role)->latest();
            }
            return Datatables::of($query)
            ->addColumn('action', function ($row) {
                $btn = '<a style="background: transparent;" href="'.route('user.employee.edit',$row->id).'" title="Edit details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: green;" class="la la-edit"></i>
                </a>&nbsp;&nbsp;&nbsp;';
                $btn .= '&nbsp;&nbsp;<a style="background: white;" href="'.route('user.employee.show',$row->id).'"  title="View Details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-eye"></i></a>&nbsp;&nbsp;&nbsp;';
                if(Auth::user()->role == 1){
                    $btn .= '<a href="'.route('user.delete.emp',$row->id).'" onclick="return confirm(`Are you sure?`)" style="background: white;" title="Delete details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: red;" class="la la-trash"></i></a>';
                }
                return $btn;
            })
            ->addColumn('singlecheckbox', function ($row) {
                $schk = view('layouts.singlecheckbox')->with(['id' => $row->id , 'status'=>$row->status])->render();
                return $schk;
            })
            ->addColumn('branch_id', function ($row) {
                $btn = $row->branchs->name;
                return $btn;
            })
            ->addColumn('role', function ($row) use ($roles) {
                return $roles[$row->role] ?? '-';
            })
            ->rawColumns(['numbers','action','branch_id','singlecheckbox','role'])
            ->make(true);
        } 
        $data['branch'] = Auth::user()->role == 1 
        ? User::where('role',2)->where('status',1)->get() 
        : User::where('id', Auth::user()->id)->get();


        $data['employee'] = Auth::user()->role == 2 
            ? Employee::where('branch_id', Auth::user()->id)->get() 
            : Employee::get();

       $data['roles'] = Role::pluck('name', 'id')->toArray();
        return view('employee.index')->with($data);
    }

    public function create()
    {
        $data['title'] = 'Add ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
    
        $user = Auth::user();
        $employeeGuard = Auth::guard('employee')->check() ? Auth::guard('employee')->user() : null;
    
        if ($user->role == 1) {
            $data['branch'] = User::where('role', 2)->where('status',1)->get();
        } else {
            $data['branch'] = User::where('id', $user->role == 2 ? $user->id : $employeeGuard->branch_id)->get();
        }
    
    
        $branchId = $user->role == 1 ? null : ($user->role == 2 ? $user->id : $employeeGuard->branch_id);
        $employeeId = $employeeGuard->id ?? null;
        $employeeRole = $employeeGuard->role ?? null;
    
        $data['generalmanager'] = Employee::where('role', 6)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($employeeRole == 6, fn($q) => $q->where('id', $employeeId)) 
            ->where('status',1)
            ->get();
    
        $data['manager'] = Employee::where('role', 7)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('status',1)
            ->get();
    

        $data['salesareamanager'] = Employee::where('role', 8)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('status',1)
            ->get();
    
        $data['group'] = Group::where('id','!=',1)->get();
        $data['roles'] = Role::all();
    
        return view('general.add_form')->with($data);
    }
     
    public function edit($id)
    {
        $data['title'] = 'Edit ' . $this->viewName;
        $data['data'] = Employee::findOrFail($id);
        $data['employeechild'] = EmployeeChild::where('employee_id', $id)->get();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;

        $user = Auth::user();
        $employeeGuard = Auth::guard('employee')->check() ? Auth::guard('employee')->user() : null;

        if ($user->role == 1) {
      
            $data['branch'] = User::where('role', 2)->where('status',1)->get();
            $branchId = null;
        } else {
 
            $branchId = $user->role == 2 ? $user->id : $employeeGuard->branch_id;
            $data['branch'] = User::where('id', $branchId)->get();
        }

        $employeeId = $employeeGuard->id ?? null;
        $employeeRole = $employeeGuard->role ?? null;

 
        $data['generalmanager'] = Employee::where('role', 6)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->when($employeeRole == 6, fn($q) => $q->where('id', $employeeId)) 
            ->where('status',1)
            ->get();

    
        $data['manager'] = Employee::where('role', 7)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('status',1)
            ->get();

 
        $data['salesareamanager'] = Employee::where('role', 8)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('status',1)
            ->get();

        $data['group'] = Group::where('id','!=',1)->get();
        $data['roles'] = Role::all();
        return view('general.edit_form')->with($data);
    }

    public function store(Request $request)
    {
        $employee = new Employee();
        $employee->first_name = $request->first_name;
        if(Auth::user()->role == 2)
        {
            $employee->branch_id = Auth::user()->id;
        }
        else
        {
            $employee->branch_id = $request->branch_id;
        }

        $check = Employee::where('email',$request->email)->first();
        if(isset($check))
        {
            return response()->json(['status'=>'email_exists']);
        }

        $employee->last_name = $request->last_name;
        $employee->name = $request->name;
        $employee->role = $request->role;
        $employee->gender = $request->gender;
        $employee->email = $request->email;
        $employee->mobile_no = $request->mobile_no;
        $employee->other_mobile_no = $request->other_mobile_no;
        $employee->address = $request->address;
        $employee->general_manager = $request->general_manager;
        $employee->manager = $request->manager;
        $employee->sales_area_manager = $request->sales_area_manager;
        $employee->office_start_time = $request->office_start_time;
        $employee->buffer_time = $request->buffer_time;
        $employee->password =  bcrypt($request->password);
        if($request->password == $request->confirm_password)
        {
            $employee->confirm_password = $request->confirm_password;
        }
        else
        {
            return response()->json(['status' => 'password']);
        }
        $employee->dob = $request->dob;
        $employee->join_date = $request->join_date;
        if(isset($request['image']))
        {
            $imageName = time().'-'. $request->image->getClientOriginalName();
            $request->image->move(public_path('employee/'),$imageName);
            $employee->image = $imageName;
        }
        $employee->save();
        if($request->sections)
        {
            foreach ($request->sections as $key => $value) {
                if($value != '')
                {
                    $child = new EmployeeChild();
                    $child->employee_id = $employee->id;
                    $child->name = $value;
                    $child->save();
                }
            }
        }
        if($employee)
        {
            return response()->json(['status' => 'success']);
        }else
        {
            return response()->json(['status' => 'error']);
        }
    }
  
    public function show($id)
    {
        $data['title'] = 'View ' . $this->viewName;
        $data['data'] = Employee::findOrFail($id);
        $data['employeechild'] = EmployeeChild::where('employee_id', $id)->get();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
    
        $user = Auth::user();
        $employeeGuard = Auth::guard('employee')->check() ? Auth::guard('employee')->user() : null;
    

        if ($user->role == 1) {
     
            $data['branch'] = User::where('role', 2)->where('status',1)->get();
            $branchId = null;
            $data['branchname'] = User::where('id', $data['data']->branch_id)->value('name');
        } else {
        
            $branchId = $user->role == 2 ? $user->id : $employeeGuard->branch_id;
            $data['branch'] = User::where('id', $branchId)->get();
            $data['branchname'] = User::where('id', $branchId)->value('name');
        }
    
        $employeeId = $employeeGuard->id ?? null;
        $employeeRole = $employeeGuard->role ?? null;
    
   
        $data['generalmanager'] = Employee::where('role', 6)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('status',1)
            ->get();
    
  
        $data['manager'] = Employee::where('role', 7)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('status',1)
            ->get();
    
     
        $data['salesareamanager'] = Employee::where('role', 8)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('status',1)
            ->get();
    
        $data['group'] = Group::where('id','!=',1)->get();
        $data['roles'] = Role::all();
    
        return view('employee.show')->with($data);
    }
    
    public function update(Request $request, $id)
    {
        $employee = Employee::where('id',$id)->first();
        $employee->first_name = $request->first_name;
        if(Auth::user()->role == 2)
        {
            $employee->branch_id = Auth::user()->id;
        }
        else
        {
            $employee->branch_id = $request->branch_id;
        }
        $exists = Employee::where('email',$request->email)->first();
        if(isset($exists) && !($exists->id == $id))
        {
            return response()->json(['status'=>'email_exists']);
        }
        $employee->last_name = $request->last_name;
        $employee->name = $request->name;
        $employee->gender = $request->gender;
        $employee->email = $request->email;
        $employee->role = $request->role;
        $employee->mobile_no = $request->mobile_no;
        $employee->other_mobile_no = $request->other_mobile_no;
        $employee->address = $request->address;
         $employee->general_manager = $request->general_manager;
        $employee->manager = $request->manager;
        $employee->sales_area_manager = $request->sales_area_manager;
        $employee->office_start_time = $request->office_start_time;
        $employee->buffer_time = $request->buffer_time;
        if($request->password && $request->confirm_password)
        {
            $employee->password =  bcrypt($request->password);
            if($request->password == $request->confirm_password)
            {
                $employee->confirm_password = $request->confirm_password;
            }
            else
            {
                return response()->json(['status' => 'password']);
            }
        }
        $employee->dob = $request->dob;
        $employee->join_date = $request->join_date;
        if(isset($request['image']))
        {
            $imageName = time().'-'. $request->image->getClientOriginalName();
            $request->image->move(public_path('employee/'),$imageName);
            $employee->image = $imageName;
        }
        $employee->save();
        if($request->edit_sections)
        {
            foreach ($request->edit_sections as $key => $value) {
                {
                    if($value != '')
                    {
                        $child['employee_id'] = $id;
                        $child['name'] = $value;
                        $employeechild = EmployeeChild::where('id',$request->edit_main_id[$key])->update($child);
                    }
                }
            }
        }
        if($request->sections)
        {
            foreach ($request->sections as $key => $value) {
                if($value != '')
                {
                    $child = new EmployeeChild();
                    $child->employee_id = $employee->id;
                    $child->name = $value;
                    $child->save();
                }
            }
        }
        if($employee)
        {
            return response()->json(['status' => 'success']);
        }else
        {
            return response()->json(['status' => 'error']);
        }
    }

    public function employeedelete($id)
    {
        $employeechild = EmployeeChild::where('id',$id)->delete();
        if($employeechild)
        {
            return response()->json(['status' => 'success']);
        }else
        {
            return response()->json(['status' => 'error']);
        }
    }

    public function delete($id)
    {
        $data = Employee::where('id',$id)->delete();
        return back();
    }

    public function getEmployeesByBranch($id)
    {
        $generalmanager = Employee::where('branch_id', $id)->where('role', 6)->where('status', 1)->get();
        $manager = Employee::where('branch_id', $id)->where('role', 7)->where('status', 1)->get();
        $salesareamanager = Employee::where('branch_id', $id)->where('role', 8)->where('status', 1)->get();

        return response()->json([
            'generalmanager' => $generalmanager,
            'manager' => $manager,
            'salesareamanager' => $salesareamanager
        ]);
    }
}