<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use JWTAuth;
use App\Employee;
use App\Supervisor;
use App\Lead;
use App\Leave;
use App\Quotation;
use App\Attendance;
use App\ContactMaster;
use App\WarehouseInventory;
use App\SalesOrderChild;
use App\SalesOrder;
use App\Client;
use App\LeadSource;
use PDF;
use DB;
use App\SourceName;
use JWTAuthException;
use App\User;
use App\Complain;
use App\Product;
use App\ProductCategory;
use Carbon\Carbon;


class SalesOrderController extends Controller
{

	public function getsalesorder(Request $request)
	{
		$salesorder = SalesOrder::with('branch','employee','client','salesorderchild','salesorderchild.productcategory','salesorderchild.company','salesorderchild.product','salesorderchild.warehouse')->orderBy('id', 'DESC')->get();

		if(JWTAuth::user()->role == 2)
		{
			$salesorder = SalesOrder::with('branch','employee','client','salesorderchild','salesorderchild.productcategory','salesorderchild.company','salesorderchild.product','salesorderchild.warehouse')->where('branch_id',JWTAuth::user()->id)->orderBy('id', 'DESC')->get();
		}

		return response()->json(['success'=>1,'data' => $salesorder]);

	}

	public function getcategory(Request $request)
	{
		

		if(JWTAuth::user()->role == 1)
		{
			$category = ProductCategory::where('branch_id',$request->branch_id)->get();
		}
		elseif (JWTAuth::user()->role == 2) {
			$category = ProductCategory::where('branch_id',JWTAuth::user()->id)->get();
		}

		return response()->json(['success'=>1,'data' => $category]);
	}

	public function getsourcename(Request $request)
	{
		

		if(JWTAuth::user()->role == 1)
		{
			$sourcename = SourceName::where('branch_id',$request->branch_id)->get();
		}
		elseif (JWTAuth::user()->role == 2) {
			$sourcename = SourceName::where('branch_id',JWTAuth::user()->id)->get();
		}

		return response()->json(['success'=>1,'data' => $sourcename]);
	}

	public function getsource(Request $request)
	{
		

		if(JWTAuth::user()->role == 1)
		{
			$source = LeadSource::where('branch_id',$request->branch_id)->get();
		}
		elseif (JWTAuth::user()->role == 2) {
			$source = LeadSource::where('branch_id',JWTAuth::user()->id)->get();
		}

		return response()->json(['success'=>1,'data' => $source]);
	}

	public function getclient(Request $request)
	{
		

		if(JWTAuth::user()->role == 1)
		{
			$client = Client::where('branch_id',$request->branch_id)->orderBy('id', 'DESC')->get();
		}
		elseif (JWTAuth::user()->role == 2) {
			$client = Client::where('branch_id',JWTAuth::user()->id)->orderBy('id', 'DESC')->get();
		}

		return response()->json(['success'=>1,'data' => $client]);
	}

	public function createsalesorder(Request $request)
	{
		$this->validate($request, [
			'order_no' => 'required',			
		]);
		$salesorder = new SalesOrder();
		$salesorder->order_no = $request->order_no;
		if(JWTAuth::user()->role == 1)
		{
			$salesorder->branch_id = $request->branch_id;
			$salesorder->employee_id = $request->employee_id;
		}
		else if(JWTAuth::user()->role == 2)
		{
			$salesorder->branch_id = JWTAuth::user()->id;
			$salesorder->employee_id = $request->employee_id;
		}
		
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

	public function updatesalesorder(Request $request)
	{
		$this->validate($request, [
			'id' => 'required',		
		]);
		$salesorder = SalesOrder::where('id',$request->id)->first();
		$salesorder->order_no = $request->order_no;
		if(JWTAuth::user()->role == 1)
		{
			$salesorder->branch_id = $request->branch_id;
			$salesorder->employee_id = $request->employee_id;
		}
		else if(JWTAuth::user()->role == 2)
		{
			$salesorder->branch_id = JWTAuth::user()->id;
			$salesorder->employee_id = $request->employee_id;
		}
		
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

	public function salesorderstatus(Request $request)
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
     			// dd($inventory);
				$inventory->save();
			}
		}
		$salesorder->status = $request->status;
		$salesorder->save();

		return response()->json(['success'=>1,'message' => 'Status Change Successfully.']);
	}

	public function updatechildsalesorder(Request $request)
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

	public function createchildsalesorder(Request $request)
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
		$salesorderchild->save();
		return response()->json(['success'=>1,'message' => 'SalesOrder Child Created Successfully.']);
	}

	public function quotationpdf(Request $request)
	{
		$this->validate($request, [
			
			'id' => 'required',
			'pdf_no' => 'required',
			
			
		]);
		$data = Quotation::where('id',$request->id)->first();
		// dd($data);
		if($request->pdf_no == 1)
		{
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

		$main = SalesOrder::where('id',$request->id)->first();
		$child = SalesOrderChild::where('salesorder_id',$main->id)->delete();
		$main->delete();
		return response()->json(['success' => 1, 'message' => 'Deleted Successfully']); 
	}

	public function deletesalesorderchild(Request $request)
	{
		$this->validate($request, [
			'id' => 'required',
		]);  

		
		$child = SalesOrderChild::where('id',$request->id)->delete();
		
		return response()->json(['success' => 1, 'message' => 'Deleted Successfully Child']); 
	}
	

}