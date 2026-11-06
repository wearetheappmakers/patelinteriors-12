<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MeetingEntryEmp;
use App\Employee;
use App\User; 
use Auth;
use Carbon\Carbon;
use DataTables;

class MeetingEntryEmpController extends Controller
{
    public function __construct()
    {
        $this->view = 'meeting_entry_employee';
        $this->route = 'meeting_entry_employee';
        $this->viewName = 'Meeting Entry (Employee)';
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            if (Auth::user()->role == 9) {

                $query = MeetingEntryEmp::with('employee','branch')
                    ->where('branch_id', Auth::user()->branch_id)
                    ->orderBy('id','DESC');
            }

     
            elseif (Auth::guard('employee')->check()) {

                $query = MeetingEntryEmp::with('employee','branch')
                    ->where('employee_id', Auth::user()->id)
                    ->orderBy('id','DESC');
            }

          
            else {

                $query = MeetingEntryEmp::with('employee','branch')
                    ->orderBy('id','DESC');
            }

            if (Auth::user()->role == 9) {
                if ($request->employee_id) {
                    $query = $query->where('employee_id', $request->employee_id);
                }
            }
            if (Auth::user()->role != 9) {
                if ($request->branch_id) {
                    $query = $query->where('branch_id', $request->branch_id);
                }

                if ($request->employee_id) {
                    $query = $query->where('employee_id', $request->employee_id);
                }
            }
            

            return DataTables::of($query)

                ->addColumn('client_name', fn($row) => $row->client_name)
                ->addColumn('contact_no', fn($row) => $row->contact_no)
                ->addColumn('purpose_of_meeting', fn($row) => $row->purpose_of_meeting)

                ->addColumn('employee_name', fn($row) => optional($row->employee)->name)
                ->addColumn('branch_name', fn($row) => optional($row->branch)->name)

                ->addColumn('action', function ($row) {

                    $btn = '<a href="'.route('user.meeting_entry_employee.show',$row->id).'" class="btn btn-sm btn-clean">
                                <i class="flaticon-eye" style="color:black"></i>
                            </a>';

                    
                        $btn .= '<a href="'.route('user.meeting_entry_employee.edit',$row->id).'" class="btn btn-sm btn-clean">
                                    <i class="la la-edit" style="color:green"></i>
                                </a>';
                    
                        $btn .= '<form action="'.route('user.meeting_entry_employee.destroy',$row->id).'"
                                    method="POST" style="display:inline;">
                                    '.csrf_field().method_field('DELETE').'
                                    <button class="btn btn-sm btn-clean" onclick="return confirm(\'Delete entry?\')">
                                        <i class="la la-trash" style="color:red"></i>
                                    </button>
                                </form>';
                
                    

                    return $btn;
                })

                ->rawColumns(['action'])
                ->make(true);
        }

      
        $data['branch'] = User::where('role', 2)->get();
        $data['employee'] = Employee::get();

        return view('meeting_entry_employee.index')->with($data);
    }
    public function create()
    {
        $data['title'] = 'Add ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
    
        $data['branch'] = User::where('role', 2)->get();
        $data['employees'] = collect();
    
        if (Auth::guard('employee')->check() && Auth::guard('employee')->user()->role == 9) {
            $branchId = Auth::guard('employee')->user()->branch_id;
            $data['employees'] = Employee::where('branch_id', $branchId)->get();
        }
    
      
        if (Auth::guard('user')->check() && Auth::user()->role == 2) {
            $branchId = Auth::user()->id; 
            $data['employees'] = Employee::where('branch_id', $branchId)->get();
        }
    
        return view('general.add_form')->with($data);
    }
    
    public function store(Request $request)
    {
        $entry = new MeetingEntryEmp();
    
        if (Auth::guard('employee')->check()) {
    
            $employee = Auth::guard('employee')->user();
    
    
            if ($employee->role == 9) {
                $entry->employee_id = $request->employee_id;
                $entry->branch_id   = $employee->branch_id;
            } 
      
            else {
                $entry->employee_id = $employee->id;
                $entry->branch_id   = $employee->branch_id;
            }
        }
    
  
        elseif (Auth::guard('user')->check()) {
    
            if (Auth::user()->role == 1) { 
                $entry->branch_id   = $request->branch_id;
                $entry->employee_id = $request->employee_id;
            } 
            else { 
                $entry->branch_id   = Auth::user()->id;
                $entry->employee_id = $request->employee_id;
            }
        }
    
        $entry->sch_date = $request->sch_date;
        $entry->sch_time = $request->sch_time;
        $entry->client_name = $request->client_name;
        $entry->contact_no = $request->contact_no;
        $entry->purpose_of_meeting = $request->purpose_of_meeting;
        $entry->save();
    
        return response()->json(['status' => 'success']);
    }
    


    public function edit($id)
    {
        $data['title'] = 'Edit ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['data'] = MeetingEntryEmp::findOrFail($id);
    
        $data['branch'] = User::where('role', 2)->get();
        $data['employees'] = collect();
    
        if (Auth::guard('employee')->check() && Auth::guard('employee')->user()->role == 9) {
            $branchId = Auth::guard('employee')->user()->branch_id;
            $data['employees'] = Employee::where('branch_id', $branchId)->get();
        }
    
   
        if (Auth::guard('user')->check() && Auth::user()->role == 2) {
            $branchId = Auth::user()->id; 
            $data['employees'] = Employee::where('branch_id', $branchId)->get();
        }
    
        return view('general.edit_form')->with($data);
    }
    
    

   
    public function update(Request $request, $id)
    {
        $entry = MeetingEntryEmp::findOrFail($id);
    
      
        if (Auth::guard('employee')->check()) {
    
           
            if (Auth::guard('employee')->user()->role == 9) {
                $entry->employee_id = $request->employee_id;
                $entry->branch_id = Auth::guard('employee')->user()->branch_id;
            }
    
            else {
                $entry->employee_id = Auth::guard('employee')->user()->id;
                $entry->branch_id = Auth::guard('employee')->user()->branch_id;
            }

        }
     
        else {
    
          
            if (Auth::user()->role == 1) {
                $entry->branch_id = $request->branch_id;
                $entry->employee_id = $request->employee_id;
            }
    
            else {
                $entry->branch_id = Auth::user()->id;
                $entry->employee_id = $request->employee_id;
            }
        }
    
        $entry->sch_date = $request->sch_date;
        $entry->sch_time = $request->sch_time;
        $entry->client_name = $request->client_name;
        $entry->contact_no = $request->contact_no;
        $entry->purpose_of_meeting = $request->purpose_of_meeting;
    
        $entry->save();
    
        return response()->json(['status' => 'success']);
    }
    
    public function destroy($id)
    {
        MeetingEntryEmp::where('id', $id)->delete();
        return back();
    }
    public function show($id)
    {
        $data['title'] = 'View ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
    

        $data['data'] = MeetingEntryEmp::with('employee', 'branch')->findOrFail($id);
    

        $data['branch'] = User::where('role', 2)->get();
    
        $data['employees'] = collect();

        if (Auth::guard('employee')->check() && Auth::guard('employee')->user()->role == 9) {
            $branchId = Auth::guard('employee')->user()->branch_id;
            $data['employees'] = Employee::where('branch_id', $branchId)->get();
        }
    
       
        if (Auth::guard('user')->check() && Auth::user()->role == 2) {
            $branchId = Auth::user()->id;
            $data['employees'] = Employee::where('branch_id', $branchId)->get();
        }
    
        $data['branchname'] = optional($data['data']->branch)->name;
        $data['empname']    = optional($data['data']->employee)->name;
    
        return view('meeting_entry_employee.show')->with($data);
    }
    
    
    public function getEmployee(Request $request)
    {
        $employees = Employee::where('branch_id', $request->branch_id)->get();

        $options = '';
        foreach ($employees as $emp) {
            $options .= '<option value="'.$emp->id.'">'.$emp->name.'</option>';
        }

        return response()->json([
            'data1' => $options
        ]);
    }

}
