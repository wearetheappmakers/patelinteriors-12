<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use JWTAuth;
use App\Employee;
use App\LeadAssign;
use App\LeadFollowUp;
use App\LeadCommunication;
use App\LeadBasicChild;
use App\Quotation;
use App\Supervisor;
use App\VisitsChild;
use App\ContactMaster;
use App\QuotationChild;
use DB;
use JWTAuthException;
use App\User;
use App\Lead;
use App\Client;
use App\Complain;
use App\Product;
use App\Task;
use App\Visits;
use App\ProductCategory;
use Carbon\Carbon;


class EmployeeVisitController extends Controller
{

    public function __construct(Employee $employee)
    {
        $this->user = $employee;
        \Config::set('jwt.user', "App\Employee");
        \Config::set('auth.providers', ['users' => [
            'driver' => 'eloquent',
            'model' => \App\Employee::class,
        ]]);
    }

    public function employeeget_interiorvisit()
    {

        $data = Visits::with('employee','branch','sourcenm','child','child.products','child.companys','child.categories')->where('employee_id',JWTAuth::user()->id)->latest()->get();
        return response()->json(['success'=>1,'data' => $data]);
    }

    public function employeecreate_interiorvisit(Request $request)
    {
        $visits = new Visits();

        $visits->branch_id = Employee::where('id',JWTAuth::user()->id)->value('branch_id');
        $visits->employee_id = JWTAuth::user()->id;

        $visits->date = $request->date;
        $visits->remarks = $request->remarks;
        $visits->architect_id = $request->architect_id;
        $visits->save();

        if($request->productcategory_id)
        {
            foreach ($request->productcategory_id as $key => $value) {
                if($value != '')
                {
                    $child = new VisitsChild();
                    $child->interior_visit_id = $visits->id;
                    $child->category_id = $value;
                    $child->company_id = $request->company_id[$key];
                    $child->product_id = $request->product_id[$key];
                    $child->save();
                }
            }
        }

        return response()->json(['success'=>1,'message' => 'Visits Created SuccessFully.']);
    }

    public function employeeupdatevisit(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $visits = Visits::where('id',$request->id)->first();

        $visits->branch_id = Employee::where('id',JWTAuth::user()->id)->value('branch_id');
        $visits->employee_id = JWTAuth::user()->id;

        $visits->date = $request->date;
        $visits->remarks = $request->remarks;
        $visits->architect_id = $request->architect_id;
        $visits->save();

        return response()->json(['success'=>1,'message' => 'Visits Updated SuccessFully.']);
    }

    public function employeecreatevisitchild(Request $request)
    {
        $this->validate($request, [
            'visit_id' => 'required',
        ]);
        $child = new VisitsChild();
        $child->interior_visit_id = $request->visit_id;
        $child->category_id = $request->productcategory_id;
        $child->company_id = $request->company_id;
        $child->product_id = $request->product_id;
        $child->save();

        return response()->json(['success'=>1,'message' => 'Visits Child Created SuccessFully.']);
    }

    public function employeeupdatevisitchild(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'visit_id' => 'required',
        ]);
        $child = VisitsChild::where('id',$request->id)->first();
        $child->interior_visit_id = $request->visit_id;
        $child->category_id = $request->productcategory_id;
        $child->company_id = $request->company_id;
        $child->product_id = $request->product_id;
        $child->save();

        return response()->json(['success'=>1,'message' => 'Visits Child Updated SuccessFully.']);
    }

    public function employeevisitdeletechild(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        $child = VisitsChild::where('id',$request->id)->delete();
        return response()->json(['success'=>1,'message' => 'Visits Child Deleted SuccessFully.']);
    }

    public function employeevisitdelete(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);

        $visit = Visits::where('id',$request->id)->first();
        $child = VisitsChild::where('interior_visit_id',$request->id)->delete();
        $visit->delete();

        return response()->json(['success'=>1,'message' => 'Visits Deleted SuccessFully.']);

    }

    public function employeevisitstatus(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'status' => 'required',
        ]);

        $visit = Visits::where('id',$request->id)->first();
        $visit->status = $request->status;
        $visit->save();

        return response()->json(['success'=>1,'message' => 'Visits Status Change SuccessFully.']);

    }
}