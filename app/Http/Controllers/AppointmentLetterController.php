<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\AppointmentLetter;
use App\Employee;
use App\User;
use Auth;
use DataTables;

class AppointmentLetterController extends Controller
{
    public function __construct()
    {
        $this->view = 'appointment_letter';
        $this->route = 'appointment_letter';
        $this->viewName = 'Appointment Letter';
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = AppointmentLetter::orderby('id','DESC');

            if(Auth::guard('employee')->check())
            {
                $query = AppointmentLetter::where('branch_id',Auth::user()->branch_id)->where('created_by',Auth::user()->id);
            }
            else
            {
                if(Auth::user()->role == 2)
                {
                    $query = AppointmentLetter::where('branch_id',Auth::user()->id);
                }
                else
                {
                    $query = AppointmentLetter::orderby('id','DESC');
                }
            }

            if($request->branch_id)
            {
                $query->where('branch_id',$request->branch_id);
            }

            return DataTables::of($query)

            ->addColumn('created_by_name', function ($row) {

                if ($row->createdByAdmin) {
                    return '' . ($row->createdByAdmin->name ?? '-');
                }

                if ($row->createdByEmployee) {
                    return '' . ($row->createdByEmployee->name ?? '-');
                }

                return '-';
            })

            ->addColumn('action', function ($row) {

                $btn = '<a href="'.route('user.appointment_letter.edit', $row->id).'" 
                class="btn btn-sm btn-clean">
                <i class="la la-edit" style="color:green"></i></a>';

                $btn .= '<a href="'.route('user.appointment_letter.show', $row->id).'" 
                class="btn btn-sm btn-clean">
                <i class="flaticon-eye" style="color:black"></i></a>';

                $btn .= '<a href="'.route('user.appointment_letter.pdf', $row->id).'" 
                class="btn btn-sm btn-clean">
                <i class="fa fa-solid fa-file-pdf"></i></a>';

                $btn .= '<form method="POST" action="'.route('user.appointment_letter.destroy', $row->id).'" 
                style="display:inline-block;">
                '.csrf_field().method_field("DELETE").'
                <button type="submit" onclick="return confirm(\'Are you sure?\')" 
                class="btn btn-sm btn-clean">
                <i class="la la-trash" style="color:red;"></i>
                </button></form>';

                return $btn;
            })

            ->addColumn('branch_id', function ($row) {
                if($row->branch_id)
                {
                    $schk = User::where('id',$row->branch_id)->value('name');
                }else
                {
                    $schk = '-';
                }
                return $schk;
            })

            ->rawColumns(['action','branch_id'])
            ->make(true);
        }

        return view('appointment_letter.index');
    }


    public function create()
    {
        $data['title'] = 'Add ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;

        $data['branch'] = User::where('role', 2)->get();

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
        $letter = new AppointmentLetter();

// ✅ ADD created_by
        $letter->created_by = Auth::user()->id;

// ✅ KEEP SAME BRANCH LOGIC (only remove employee_id)
        if (Auth::guard('employee')->check() && Auth::guard('employee')->user()->role != 9) {

// ❌ removed employee_id
            $letter->branch_id = Auth::guard('employee')->user()->branch_id;
        }

        elseif (Auth::guard('employee')->check() && Auth::guard('employee')->user()->role == 9) {

// ❌ removed employee_id
            $letter->branch_id = Auth::guard('employee')->user()->branch_id;
        }

        else {
            if (Auth::user()->role == 1) { 
                $letter->branch_id = $request->branch_id;
            } else { 
                $letter->branch_id = Auth::user()->id;
            }
        }

// Assign fields
        $letter->employee_name = $request->employee_name;
        $letter->address = $request->address;
        $letter->date = $request->date;
        $letter->position = $request->position;
        $letter->amount = $request->amount;
        $letter->basic_month = $request->basic_month;
        $letter->basic_annual = $request->basic_annual;
        $letter->hra_month = $request->hra_month;
        $letter->hra_annual = $request->hra_annual;
        $letter->convenyance_allowance_month = $request->convenyance_allowance_month;
        $letter->convenyance_allowance_annual = $request->convenyance_allowance_annual;
        $letter->special_month = $request->special_month;
        $letter->special_annual = $request->special_annual;
        $letter->deductions_month = $request->deductions_month;
        $letter->deductions_annual = $request->deductions_annual;
        $letter->pt_month = $request->pt_month;
        $letter->pt_annual = $request->pt_annual;
        $letter->deposit_amt_month = $request->deposit_amt_month;
        $letter->deposit_amt_annual = $request->deposit_amt_annual;
        $letter->total_deductions_month = $request->total_deductions_month;
        $letter->total_deductions_annual = $request->total_deductions_annual;
        $letter->net_salary_month = $request->net_salary_month;
        $letter->net_salary_annual = $request->net_salary_annual;
        $letter->joining_date = $request->joining_date;
        $letter->hr_admin = $request->hr_admin;

        if ($request->hasFile('signature_upload')) {

            $file = $request->file('signature_upload');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/signature'), $filename);

            $letter->signature_upload = $filename;
        }

        $letter->save();

        return response()->json(['status' => 'success']);
    }

    public function edit($id)
    {
        $data['title'] = 'Edit ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['data'] = AppointmentLetter::find($id);

        $data['branch'] = User::where('role', 2)->get();

        if (Auth::guard('employee')->check() && Auth::guard('employee')->user()->role == 9) {
            $branchId = Auth::guard('employee')->user()->branch_id;
            $data['employees'] = Employee::where('branch_id', $branchId)->get();
        }
        if (Auth::guard('user')->check() && Auth::guard('user')->user()->role == 2) {
            $branchId = Auth::user()->id;
            $data['employees'] = Employee::where('branch_id', $branchId)->get();
        }

        if (Auth::user()->role == 1) {
            $data['employee'] = Employee::get();
        } else {
            $data['employee'] = Employee::where('branch_id', Auth::user()->id)->get();
        }

        return view('general.edit_form')->with($data);
    }

    public function update(Request $request, $id)
    {
        $letter = AppointmentLetter::find($id);

// ✅ ADD THIS LINE
        $letter->created_by = Auth::user()->id;

// ✅ KEEP YOUR BRANCH LOGIC SAME (unchanged)
        if (Auth::guard('employee')->check() && Auth::guard('employee')->user()->role != 9) {

// ❌ REMOVE employee_id
// $letter->employee_id = Auth::guard('employee')->user()->id;

            $letter->branch_id = Auth::guard('employee')->user()->branch_id;
        }

        elseif (Auth::guard('employee')->check() && Auth::guard('employee')->user()->role == 9) {

// ❌ REMOVE employee_id
// $letter->employee_id = $request->employee_id;

            $letter->branch_id = Auth::guard('employee')->user()->branch_id;
        }

        else {
            if (Auth::user()->role == 1) {
                $letter->branch_id = $request->branch_id;
            } else {
                $letter->branch_id = Auth::user()->id;
            }
        }

// ✅ ALL FIELDS SAME
        $letter->employee_name = $request->employee_name;
        $letter->address = $request->address;
        $letter->date = $request->date;
        $letter->position = $request->position;
        $letter->amount = $request->amount;
        $letter->basic_month = $request->basic_month;
        $letter->basic_annual = $request->basic_annual;
        $letter->hra_month = $request->hra_month;
        $letter->hra_annual = $request->hra_annual;
        $letter->convenyance_allowance_month = $request->convenyance_allowance_month;
        $letter->convenyance_allowance_annual = $request->convenyance_allowance_annual;
        $letter->special_month = $request->special_month;
        $letter->special_annual = $request->special_annual;
        $letter->deductions_month = $request->deductions_month;
        $letter->deductions_annual = $request->deductions_annual;
        $letter->pt_month = $request->pt_month;
        $letter->pt_annual = $request->pt_annual;
        $letter->deposit_amt_month = $request->deposit_amt_month;
        $letter->deposit_amt_annual = $request->deposit_amt_annual;
        $letter->total_deductions_month = $request->total_deductions_month;
        $letter->total_deductions_annual = $request->total_deductions_annual;
        $letter->net_salary_month = $request->net_salary_month;
        $letter->net_salary_annual = $request->net_salary_annual;
        $letter->joining_date = $request->joining_date;
        $letter->hr_admin = $request->hr_admin;

        if ($request->hasFile('signature_upload')) {
            $file = $request->file('signature_upload');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/signature'), $filename);

            $letter->signature_upload = $filename;
        }

        $letter->save();

        return response()->json(['status' => 'success']);
    }
    public function destroy($id)
    {
        AppointmentLetter::destroy($id);
        return back()->with('success', 'Deleted Successfully!');
    }

    public function show($id)
    {
        $data['title'] = 'View ' . $this->viewName;
        $data['data'] = AppointmentLetter::find($id);
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;

        $data['branch'] = User::where('role', 2)->get();

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

        return view('appointment_letter.show')->with($data);
    }

    public function pdf($id)
    {
        $letter = AppointmentLetter::findOrFail($id);

        $data['title'] = 'Appointment Letter';
        $data['module'] = 'Appointment Letter';
        $data['resourcePath'] = $this->view;

        $data['data'] = $letter;
        $data['branchname'] = User::where('id', $letter->branch_id)->value('name');
        $data['empname'] = Employee::where('id', $letter->employee_id)->value('name');

        $pdf = \PDF::loadView('appointment_letter.pdf', $data);
        return $pdf->download('AppointmentLetter_'.$id.'.pdf');

    }
}

