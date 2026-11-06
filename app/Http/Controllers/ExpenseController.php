<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Employee;
use App\User; 
use App\Expense;
use App\ExpenseChild;
use Auth;
use DataTables;

class ExpenseController extends Controller
{
    public function __construct()
    {
        $this->view = 'expense';
        $this->route = 'expense';
        $this->viewName = 'Expense Update';
    }

    public function index(Request $request)
    {
       //
    }


    public function create()
    {
        //
    }

    public function store(Request $request)
    {
       //
    }

    public function edit($id)
    {
        $data['title'] = 'Edit ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['data'] = Expense::with('children')->find($id);
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
        $expense = Expense::findOrFail($id);
        $role = Auth::user()->role;

        $expense->save();

        // ---------- HR UPDATE (ONLY STATUS CHANGE) ----------
        if ($role == 9) {
            foreach ($request->status as $index => $status_value) {
                $child = ExpenseChild::where('expense_id', $id)
                                    ->skip($index)
                                    ->first();
                if ($child) {
                    $child->status = $status_value;
                    $child->save();
                }
            }

            return response()->json([
                'status' => 'success',
                'id' => $expense->id,
                'redirect' => url()->previous()
            ]);
        }

        // ---------- EMPLOYEE UPDATE (DELETE + RECREATE) ----------
        ExpenseChild::where('expense_id', $id)->delete();

        if ($request->has('date')) {
            foreach ($request->date as $index => $date) {
                // skip empty rows
                if (empty($date) && empty($request->amount[$index]) && empty($request->name[$index])) {
                    continue;
                }

                // Create child first
                $child = new ExpenseChild();
                $child->expense_id = $expense->id;
                $child->date       = $date;
                $child->amount     = $request->amount[$index] ?? 0;
                $child->name       = $request->name[$index] ?? '';
                $child->status     = $request->status[$index] ?? '0';

                // Assign branch and employee based on role
                if (Auth::guard('employee')->check() && Auth::guard('employee')->user()->role != 9) {
                    $child->employee_id = Auth::guard('employee')->user()->id;
                    $child->branch_id   = Auth::guard('employee')->user()->branch_id;
                }
                elseif (Auth::guard('employee')->check() && Auth::guard('employee')->user()->role == 9) {
                    $child->employee_id = $request->employee_id[$index] ?? null;
                    $child->branch_id   = Auth::guard('employee')->user()->branch_id;
                }
                else {
                    if (Auth::user()->role == 1) { // super admin
                        $child->branch_id   = $request->branch_id[$index] ?? null;
                        $child->employee_id = $request->employee_id[$index] ?? null;
                    } else { // branch admin
                        $child->branch_id   = Auth::user()->id;
                        $child->employee_id = $request->employee_id[$index] ?? null;
                    }
                }

                // File Upload with old file preservation
                if ($request->hasFile("bill_upload.$index")) {
                    $file = $request->file("bill_upload.$index");
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('uploads/expenses'), $filename);
                    $child->bill_upload = $filename;
                } else {
                    // preserve old file
                    $child->bill_upload = $request->bill_upload_old[$index] ?? null;
                }

                $child->save();
            }
        }

        return response()->json([
            'status' => 'success',
            'id'     => $expense->id,
            'redirect' => url()->previous()
        ]);
    }

    

    
    public function destroy($id)
    {
        Expense::destroy($id);
        return back()->with('success', 'Deleted Successfully!');
    }

    public function destroyChild($id)
    {
        $child = ExpenseChild::find($id);
    
        if (!$child) {
            return response()->json([
                'status' => 'error',
                'message' => 'Child record not found'
            ], 404);
        }
    
        $child->delete();
    
        return response()->json([
            'status' => 'success',
            'message' => 'Expense child deleted successfully'
        ]);
    }
    
    


    public function show($id)
    {
        $data['title'] = 'View ' . $this->viewName;
        $data['data'] = Expense::find($id);
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;


        $data['branch'] = User::where('role', 2)->get();

        return view('expense.show')->with($data);
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
}