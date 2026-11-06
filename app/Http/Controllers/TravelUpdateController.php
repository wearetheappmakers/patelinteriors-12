<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Employee;
use App\User; 
use App\TravelUpdate;
use Auth;
use DataTables;

class TravelUpdateController extends Controller
{
    public function __construct()
    {
        $this->view = 'travel_update';
        $this->route = 'travel_update';
        $this->viewName = 'Travel Update';
    }

   public function index(Request $request)
    {
        if ($request->ajax()) {

            // HR Login → See only their branch
            if (Auth::user()->role == 9) {
                $query = TravelUpdate::with('employee','branch')
                    ->where('branch_id', Auth::user()->branch_id)
                    ->orderBy('id','DESC');
            }

            // Employee Login → See only own records
            elseif (Auth::guard('employee')->check()) {
                $query = TravelUpdate::with('employee','branch')
                    ->where('employee_id', Auth::user()->id)
                    ->orderBy('id','DESC');
            }

            // Normal Admin / Super Admin
            else {
                $query = TravelUpdate::with('employee','branch')
                    ->orderBy('id','DESC');
            }

            // FILTERS (apply for all roles, including HR)
            if ($request->branch_id) {
                $query = $query->where('branch_id', $request->branch_id);
            }

            if ($request->employee_id) {
                $query = $query->where('employee_id', $request->employee_id);
            }

            return DataTables::of($query)
                ->addColumn('name', fn($row) => $row->name)
                ->addColumn('contact_no', fn($row) => $row->contact_no)
                ->addColumn('purpose_of_travel', fn($row) => $row->purpose_of_travel)
                ->addColumn('employee_name', fn($row) => optional($row->employee)->name)
                ->addColumn('branch_name', fn($row) => optional($row->branch)->name)
                ->addColumn('action', function ($row) {

                    $btn = '<a href="'.route('user.travel_update.show',$row->id).'" class="btn btn-sm btn-clean">
                                <i class="flaticon-eye" style="color:black"></i>
                            </a>';

                    $btn .= '<a href="'.route('user.travel_update.edit',$row->id).'" class="btn btn-sm btn-clean">
                                <i class="la la-edit" style="color:green"></i>
                            </a>';

                    $btn .= '<form action="'.route('user.travel_update.destroy',$row->id).'" method="POST" style="display:inline;">
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

        // Non-AJAX view data
        if (Auth::user()->role == 9) {
            $data['branch'] = User::where('id', Auth::user()->branch_id)->get();
            $data['employee'] = Employee::where('branch_id', Auth::user()->branch_id)->get();
        } else {
            $data['branch'] = User::where('role', 2)->get();
            $data['employee'] = Employee::get();
        }

        $data['employees'] = collect();

        // HR (employee guard, role = 9)
        if (Auth::guard('employee')->check() && Auth::guard('employee')->user()->role == 9) {
            $branchId = Auth::guard('employee')->user()->branch_id;
            $data['employees'] = Employee::where('branch_id', $branchId)->get();
        }

        // Branch user (user guard, role = 2)
        if (Auth::guard('user')->check() && Auth::guard('user')->user()->role == 2) {
            $branchId = Auth::user()->id;
            $data['employees'] = Employee::where('branch_id', $branchId)->get();
        }

        // Admin (role = 1)
        if (Auth::guard('user')->check() && Auth::guard('user')->user()->role == 1) {
            $data['employees'] = Employee::all();
        }

        return view('travel_update.index')->with($data);
    }




    public function create()
    {
        $data['title'] = 'Add ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
          $data['branch'] = User::where('role', 2)->get();
        $data['employees'] = collect();

        // HR (employee guard, role = 9)
        if (Auth::guard('employee')->check() && Auth::guard('employee')->user()->role == 9) {
            $branchId = Auth::guard('employee')->user()->branch_id;
            $data['employees'] = Employee::where('branch_id', $branchId)->get();
        }

        // Branch user (user guard, role = 2)
        if (Auth::guard('user')->check() && Auth::guard('user')->user()->role == 2) {
            $branchId = Auth::user()->id;
            $data['employees'] = Employee::where('branch_id', $branchId)->get();
        }

        // Admin (role = 1)
        if (Auth::guard('user')->check() && Auth::guard('user')->user()->role == 1) {
            $data['employees'] = Employee::all();
        }

        return view('general.add_form')->with($data);
    }

    public function store(Request $request)
    {
        $travel = new TravelUpdate();

      
        if (Auth::guard('employee')->check() && Auth::guard('employee')->user()->role != 9) {

            $travel->employee_id = Auth::guard('employee')->user()->id;
            $travel->branch_id = Auth::guard('employee')->user()->branch_id;
        }

       
        elseif (Auth::guard('employee')->check() && Auth::guard('employee')->user()->role == 9) {

            $travel->employee_id = $request->employee_id; 
            $travel->branch_id   = Auth::guard('employee')->user()->branch_id;
        }

        
        else {
            if (Auth::user()->role == 1) { 
                $travel->branch_id   = $request->branch_id;
                $travel->employee_id = $request->employee_id;
            } else { 
                $travel->branch_id   = Auth::user()->id;
                $travel->employee_id = $request->employee_id;
            }
        }

        $travel->sch_date = $request->sch_date;
        $travel->sch_time = $request->sch_time;
        $travel->name = $request->name;
        $travel->contact_no = $request->contact_no;
        $travel->purpose_of_travel = $request->purpose_of_travel;

        $travel->save();

        return response()->json(['status' => 'success']);
    }



    public function edit($id)
    {
        $data['title'] = 'Edit ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['data'] = TravelUpdate::find($id);
         $data['employees'] = collect();
         

        // HR (employee guard, role = 9)
        if (Auth::guard('employee')->check() && Auth::guard('employee')->user()->role == 9) {
            $branchId = Auth::guard('employee')->user()->branch_id;
            $data['employees'] = Employee::where('branch_id', $branchId)->get();
        }
       if (Auth::guard('user')->check() && Auth::guard('user')->user()->role == 2) {
            $branchId = Auth::user()->id;
            $data['employees'] = Employee::where('branch_id', $branchId)->get();
        }

        $data['branch'] = User::where('role', 2)->get();

        if (Auth::user()->role == 1) {
            $data['employee'] = Employee::get();
        } else {
            $data['employee'] = Employee::where('branch_id', Auth::user()->id)->get();
        }

        return view('general.edit_form')->with($data);
    }

   
     public function update(Request $request, $id)
    {
        $travel = TravelUpdate::find($id);

       
        if (Auth::guard('employee')->check() && Auth::guard('employee')->user()->role != 9) {
        
            $travel->employee_id = Auth::guard('employee')->user()->id;
            $travel->branch_id = Auth::guard('employee')->user()->branch_id;
        }

        
        elseif (Auth::guard('employee')->check() && Auth::guard('employee')->user()->role == 9) {
            $travel->employee_id = $request->employee_id; // from dropdown
            $travel->branch_id = Auth::guard('employee')->user()->branch_id;
        }

       
        else {
            if (Auth::user()->role == 1) { // super admin
                $travel->branch_id = $request->branch_id;
                $travel->employee_id = $request->employee_id;
            } else { // branch admin
                $travel->branch_id = Auth::user()->id;
                $travel->employee_id = $request->employee_id;
            }
        }

        $travel->sch_date = $request->sch_date;
        $travel->sch_time = $request->sch_time;
        $travel->name = $request->name;
        $travel->contact_no = $request->contact_no;
        $travel->purpose_of_travel = $request->purpose_of_travel;

        $travel->save();

        return response()->json(['status' => 'success']);
    }

    public function destroy($id)
    {
        TravelUpdate::destroy($id);
        return back()->with('success', 'Deleted Successfully!');
    }


    public function show($id)
    {
        $data['title'] = 'View ' . $this->viewName;
        $data['data'] = TravelUpdate::find($id);
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;


        $data['branch'] = User::where('role', 2)->get();
        $data['employees'] = collect();

        // HR (employee guard, role = 9)
        if (Auth::guard('employee')->check() && Auth::guard('employee')->user()->role == 9) {
            $branchId = Auth::guard('employee')->user()->branch_id;
            $data['employees'] = Employee::where('branch_id', $branchId)->get();
        }
        if (Auth::guard('user')->check() && Auth::guard('user')->user()->role == 2) {
            $branchId = Auth::user()->id;
            $data['employees'] = Employee::where('branch_id', $branchId)->get();
        }

        $data['branchname'] = User::where('id', $data['data']->branch_id)->value('name');
        $data['empname'] = Employee::where('id', $data['data']->employee_id)->value('name');

        return view('travel_update.show')->with($data);
    }

    public function getemployee(Request $request)
{
    $data = Employee::where('branch_id', $request->branch_id)->get();

    $html1 = '';

    // ✅ Add "Select" only when requested (index page)
    if ($request->has('with_select') && $request->with_select == 1) {
        $html1 .= "<option value=''>Select</option>";
    }

    foreach ($data as $value) {
        $html1 .= "<option value='{$value->id}'>{$value->name}</option>";
    }

    return response()->json(['data1' => $html1]);
}

}