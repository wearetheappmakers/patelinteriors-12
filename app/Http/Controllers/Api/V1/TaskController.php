<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\User;
use DataTables;
use App\Task;
use App\Employee;
use App\ProductCategory;
use App\Visits;
use App\VisitsChild;
use Auth;
use Mail;
use Illuminate\Http\Request;
use JWTAuth;
use JWTAuthException;

class TaskController extends Controller
{
    public function gettask()
    {
        if(JWTAuth::user()->role == 1)
        {
            $task = Task::with('employee','branch','assign_to')->latest()->get();
        }
        else
        {
            $task = Task::with('employee','branch','assign_to')->where('branch_id',JWTAuth::user()->id)->latest()->get();
        }
        return response()->json(['success'=>1,'data' => $task]);
    }

    public function get_interiorvisit()
    {
        if(JWTAuth::user()->role == 1)
        {
            $data = Visits::with('employee','branch','sourcenm','child','child.products','child.companys','child.categories')->latest()->get();
        }
        else
        {
            $data = Visits::with('employee','branch','sourcenm','child','child.products','child.companys','child.categories')->where('branch_id',JWTAuth::user()->id)->latest()->get();
        }

        return response()->json(['success'=>1,'data' => $data]);
    }

    public function create_interiorvisit(Request $request)
    {
        $visits = new Visits();

        if(JWTAuth::user()->role == 1)
        {
            $visits->branch_id = $request->branch_id;
            $visits->employee_id = $request->employee_id;
        }
        else
        {
            $visits->branch_id = JWTAuth::user()->id;
            $visits->employee_id = $request->employee_id;
        }

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

    public function updatevisit(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',


        ]);
        $visits = Visits::where('id',$request->id)->first();

        if(JWTAuth::user()->role == 1)
        {
            $visits->branch_id = $request->branch_id;
            $visits->employee_id = $request->employee_id;
        }
        else
        {
            $visits->branch_id = JWTAuth::user()->id;
            $visits->employee_id = $request->employee_id;
        }

        $visits->date = $request->date;
        $visits->remarks = $request->remarks;
        $visits->architect_id = $request->architect_id;
        $visits->save();

        return response()->json(['success'=>1,'message' => 'Visits Updated SuccessFully.']);
    }

    public function createvisitchild(Request $request)
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

    public function updatevisitchild(Request $request)
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

    public function visitdeletechild(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',



        ]);

        $child = VisitsChild::where('id',$request->id)->delete();

        return response()->json(['success'=>1,'message' => 'Visits Child Deleted SuccessFully.']);

    }

    public function visitdelete(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',



        ]);

        $visit = Visits::where('id',$request->id)->first();
        $child = VisitsChild::where('interior_visit_id',$request->id)->delete();
        $visit->delete();

        return response()->json(['success'=>1,'message' => 'Visits Deleted SuccessFully.']);

    }

    public function visitstatus(Request $request)
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