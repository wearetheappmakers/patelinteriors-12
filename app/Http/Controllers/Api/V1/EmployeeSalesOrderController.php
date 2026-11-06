<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use JWTAuth;
use App\SourceName;
use App\Employee;
use App\WarehouseInventory;
use App\SalesOrderChild;
use App\SalesOrder;
use App\LeadAssign;
use App\LeadFollowUp;
use App\LeadCommunication;
use App\LeadBasicChild;
use App\Quotation;
use App\Supervisor;
use App\Delivery;
use App\ContactMaster;
use App\QuotationChild;
use DB;
use JWTAuthException;
use App\User;
use App\Lead;
use App\Client;
use App\Complain;
use App\GST;
use App\Product;
use App\Task;
use App\ProductCategory;
use Carbon\Carbon;
use PDF;

class EmployeeSalesOrderController extends Controller
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
	
    public function employeegetsalesorder(Request $request)
    {

        $salesorder = SalesOrder::with('branch','employee','client','salesorderchild','salesorderchild.productcategory','salesorderchild.company','salesorderchild.product','salesorderchild.warehouse')->where('employee_id',JWTAuth::user()->id)->orderBy('id', 'DESC')->get();
        

        return response()->json(['success'=>1,'data' => $salesorder]);

    }
    public function getcategory()
    {        
        $category = ProductCategory::where('branch_id',JWTAuth::user()->branch_id)->get();
        return response()->json(['success'=>1,'data' => $category]);
    }
    public function getclient()
    {        
        $client = Client::where('branch_id',JWTAuth::user()->branch_id)->get();
        return response()->json(['success'=>1,'data' => $client]);
    }
    public function employeecreatesalesorder(Request $request)
    {
        $this->validate($request, [
            'order_no' => 'required',            
        ]);
        $salesorder = new SalesOrder();
        $salesorder->order_no = $request->order_no;
        $salesorder->branch_id = Employee::where('id',JWTAuth::user()->id)->value('branch_id');
        $salesorder->employee_id = JWTAuth::user()->id;
        $salesorder->date = $request->date;
        $salesorder->name = $request->name;
        $salesorder->save();

        if($request->productcategory_id)
        {
            foreach ($request->productcategory_id as $key => $value) {
                if($value != '')
                {
                    $child = new SalesOrderChild();
                    $child->salesorder_id = $salesorder->id;
                    $child->productcategory_id = $value;
                    $child->company_id = $request->company_id[$key];
                    $child->product_id = $request->product_id[$key];
                    $child->warehouse_id = $request->warehouse_id[$key];
                    $child->qty = $request->qty[$key];
                    $child->save();
                }
            }
        }
        return response()->json(['success'=>1,'message' => 'Sales Order Created Successfully.']);
    }

    public function employeeupdatesalesorder(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',            
        ]);

        $salesorder = SalesOrder::where('id',$request->id)->first();
        $salesorder->order_no = $request->order_no;
        $salesorder->branch_id = Employee::where('id',JWTAuth::user()->id)->value('branch_id');
        $salesorder->employee_id = JWTAuth::user()->id;
        $salesorder->date = $request->date;
        $salesorder->name = $request->name;
        $salesorder->save();

        if($request->productcategory_id)
        {
            foreach ($request->productcategory_id as $key => $value) {
                if($value != '')
                {
                    $child = new SalesOrderChild();
                    $child->salesorder_id = $salesorder->id;
                    $child->productcategory_id = $value;
                    $child->company_id = $request->company_id[$key];
                    $child->product_id = $request->product_id[$key];
                    $child->warehouse_id = $request->warehouse_id[$key];
                    $child->qty = $request->qty[$key];
                    $child->save();

                }
            }
        }
        return response()->json(['success'=>1,'message' => 'Sales Order Updated Successfully.']);
    }

    public function employeesalesorderstatus(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'status' => 'required',            
        ]);

        $salesorder = SalesOrder::where('id',$request->id)->first();
        if($request->status == 1)
        {
            $salesorderchild = SalesOrderChild::where('salesorder_id',$request->id)->get();
            foreach ($salesorderchild as $key => $value) {
                $inventory = WarehouseInventory::where('product_id',$value->product_id)->where('warehouse_id',$value->warehouse_id)->first();
                $inventory->stock = $inventory->stock + $value->qty;
                $inventory->save();
            }
        }
        $salesorder->status = $request->status;
        $salesorder->save();
        return response()->json(['success'=>1,'message' => 'Status Change Successfully.']);
    }

    public function employeeupdatechildsalesorder(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'salesorder_id' => 'required',            
        ]);
        $salesorderchild = SalesOrderChild::where('id',$request->id)->first();
        $salesorderchild->salesorder_id = $request->salesorder_id;
        $salesorderchild->productcategory_id = $request->productcategory_id;
        $salesorderchild->company_id = $request->company_id;
        $salesorderchild->product_id = $request->product_id;
        $salesorderchild->warehouse_id = $request->warehouse_id;
        $salesorderchild->qty = $request->qty;
        $salesorderchild->save();
        return response()->json(['success'=>1,'message' => 'SalesOrder Child Updated Successfully.']);
    }

    public function employeecreatechildsalesorder(Request $request)
    {
        $this->validate($request, [
            'salesorder_id' => 'required',            
        ]);
        $salesorderchild = new SalesOrderChild();
        $salesorderchild->salesorder_id = $request->salesorder_id;
        $salesorderchild->productcategory_id = $request->productcategory_id;
        $salesorderchild->company_id = $request->company_id;
        $salesorderchild->product_id = $request->product_id;
        $salesorderchild->warehouse_id = $request->warehouse_id;
        $salesorderchild->qty = $request->qty;
        // dd($salesorderchild);
        $salesorderchild->save();
        return response()->json(['success'=>1,'message' => 'SalesOrder Child Updated Successfully.']);
    }

    public function employeegetsourcename()
    {
        $sourcename = SourceName::where('branch_id',JWTAuth::user()->branch_id)->get();
    

        return response()->json(['success'=>1,'data' => $sourcename]);

    }

    public function employeegetattendance()
    {

        $attendance = Attendance::where('employee_id',JWTAuth::user()->id)->get();

        

        return response()->json(['success'=>1,'data' => $attendance]);
    }

    public function employeeattendancestatus(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'attendance' => 'required',
            
        ]);
        $attendance = Attendance::where('id',$request->id)->first();
        // dd($attendance);
        $attendance->attendance = $request->attendance;
        $attendance->save();

        return response()->json(['success'=>1,'message' => 'Status Change Successfully']);
    }

    public function employeegetleave()
    {
        $leave = Leave::get();
        if(JWTAuth::user()->role == 2)
        {
            $leave = Leave::where('branch_id',JWTAuth::user()->id)->get();
        }

        return response()->json(['success'=>1,'data' => $leave]);
    }

    public function employeecreateleave(Request $request)
    {
        $this->validate($request, [
            'branch_id' => 'required',
            'employee_id' => 'required',
            
        ]);

        $leave = new Leave();


        $leave->branch_id = Employee::where('id',JWTAuth::user()->id)->value('branch_id');
        $leave->employee_id = JWTAuth::user()->id;
        

        $leave->from_date = $request->from_date;
        $leave->to_date = $request->to_date;
        $leave->subject = $request->subject;
        $leave->reason = $request->reason;
        $leave->save();

        return response()->json(['success'=>1,'message' => 'Leave Created Successfully']);
    }

    public function employeestatusleave(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'status' => 'required',
            
        ]);

        $leave = Leave::where('id',$request->id)->first();
        $leave->status = $request->status;
        if($request->status == 1)
        {
            $leave->approved_from_date = $request->approved_from_date;
            $leave->approved_to_date = $request->approved_to_date;
            $leave->status = $request->status;
            
        }
        $leave->save();

        return response()->json(['success'=>1,'message' => 'Status Change Successfully']);

    }

    public function employeequotationpdf(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'pdf_no' => 'required',            
        ]);
        $data = Quotation::where('id',$request->id)->where('employee_id',JWTAuth::user()->id)->first();
        if($request->pdf_no == 1)
        {
            // dd($request->pdf_no == 1);
            $pdf = PDF::setOptions(['logOutputFile' => storage_path('logs/log.htm'), 'tempDir' => storage_path('logs/'), 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('quotationapi')->setPaper('A4', 'landscape');
            $pdf->save('public/pdf/'.$data->id.'-1.pdf');
            $url = asset('pdf/'.$data->id.'-1.pdf');
        }
        if($request->pdf_no == 2)
        {
            if($request->q_price2)
            {
                $data->q_price2 = $request->q_price2;
                $data->save();
            }
            $pdf = PDF::setOptions(['logOutputFile' => storage_path('logs/log.htm'), 'tempDir' => storage_path('logs/'), 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('quotationpdf2')->setPaper('A4', 'portrait');

            $pdf->save('public/pdf/'.$data->id.'-2.pdf');
            $url = asset('pdf/'.$data->id.'-2.pdf');
        }
        if($request->pdf_no == 3)
        {
            if($request->q_price3)
            {
                $data->q_price3 = $request->q_price3;

                $data->save();
            }
            $pdf = PDF::setOptions(['logOutputFile' => storage_path('logs/log.htm'), 'tempDir' => storage_path('logs/'), 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('quotationpdf3')->setPaper('A4', 'portrait');
            $pdf->save('public/pdf/'.$data->id.'-3.pdf');
            $url = asset('pdf/'.$data->id.'-3.pdf');
        }
        if($request->pdf_no == 4)
        {
            if($request->q_price4)
            {
                $data->q_price4 = $request->q_price4;

                $data->save();
            }
            $pdf = PDF::setOptions(['logOutputFile' => storage_path('logs/log.htm'), 'tempDir' => storage_path('logs/'), 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('quotationpdf4')->setPaper('A4', 'portrait');
            $pdf->save('public/pdf/'.$data->id.'-4.pdf');
            $url = asset('public/pdf/'.$data->id.'-4.pdf');
        }


        return response()->json(['success'=>1,'data' => $url]);

    }

    public function deletesalesorder(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);  

       
        $child = SalesOrderChild::where('id',$request->id)->delete();
       
        return response()->json(['success' => 1, 'message' => 'Deleted Successfully']); 
    }

    public function getemployeegst()
    {
        $gst = GST::where('branch_id',JWTAuth::user()->branch_id)->get();

         return response()->json(['success' => 1, 'data' => $gst]); 

    }

}