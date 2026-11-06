<?php
namespace App\Http\Controllers;
use App\User;
use DataTables;
use App\Section;
use App\SalesOrder;
use App\SalesOrderChild;
use App\Company;
use App\WarehouseInventory;
use App\Employee;
use App\Vendor;
use Auth;
use App\Warehouse;
use App\Product;
use App\Client;
use App\PurchaseInvoice;
use App\PurchaseInvoiceChild;
use App\ProductCategory;
use App\SalesOrderInvoice;
use App\SalesOrderInvoiceChild;
use Illuminate\Http\Request;
use App\PurchaseOrder;
use App\PurchaseOrderChild;
use App\Credit;
use App\CreditChild;
class SalesOrderReportController extends Controller
{
	public function salesorder_report()
	{
		return view('salesorder.report');
	}
	public function getdata(Request $request)
	{
		if($request->client_id)
		{
			$data = SalesOrder::where('name',$request->client_id)->get();
		}
		if($request->employee_id)
		{
			$data = SalesOrder::where('employee_id',$request->employee_id)->get();
		}
		$html = '';
		if(count($data) > 0)
		{
			foreach ($data as $key => $value) {
				$child = SalesOrderChild::where('salesorder_id',$value->id)->get();
				$date = SalesOrderInvoice::where('pending_so',$value->id)->latest()->first();
				if($date)
				{
					if($date->date)
					{
						$dates = date('d/m/Y', strtotime($date->date));
					}else
					{
						$dates = '';
					}
				}else
				{
					$dates = '';
				}
				$html .= '<table class="table table-striped- table-bordered table-hover table-checkable datatable">
				<tr>
				<td colspan="3" style="text-align: center !important; border: 1.5px solid black;">'.$value->order_no.' </h3></td>
				<td colspan="3" style="text-align: center !important; border: 1.5px solid black;">Invoice Date : '.$dates.'</td>
				</tr>
				<tr>
				<td style="border: 1.5px solid black;">Product Name</td>
				<td style="border: 1.5px solid black;">Product Code</td>
				<td style="border: 1.5px solid black;">Warehouse</td>
				<td style="border: 1.5px solid black;">Order qty</td>
				<td style="border: 1.5px solid black;">Invoice Qty</td>
				<td style="border: 1.5px solid black;">Pending Qty</th>
				</tr>';
				if(count($child) > 0)
				{
					foreach ($child as $keys => $values) {
						$product = Product::where('id',$values->product_id)->first();
						$warehouse = Warehouse::where('id',$values->warehouse_id)->first();
						$invoice = $values->qty - $values->pending_qty;
						$html .= '<tr>
						<td style="border: 1.5px solid black;">'.$product->product.'</td>
						<td style="border: 1.5px solid black;">'.$product->code.'</td>
						<td style="border: 1.5px solid black;">'.$warehouse->name.'</td>
						<td style="border: 1.5px solid black;">'.$values->qty.'</td>
						<td style="border: 1.5px solid black;">'.$invoice.'</td>
						<td style="border: 1.5px solid black; text-align: center;">'.$values->pending_qty.'</th>
						</tr>';
					}
				}
				$html .= '</table><br/>';
			}
		}

		$param = Credit::where('client_id',$request->client_id)->get();
		$html1 = '';
		if(count($param) > 0)
		{
			foreach ($param as $key => $value)
			{
				$creditchild = CreditChild::where('credit_id',$value->id)->get();
				$date = Credit::where('id',$value->id)->latest()->first();
				if($date)
				{
					if($date->date)
					{
						$dates = date('d/m/Y', strtotime($date->date));
					}else
					{
						$dates = '';
					}
					if($date->order_no)
					{
						$order = $date->order_no;
					}else{
						$order = '';
					}
				}else
				{
					$dates = '';
					$order = '';
				}
				$html1 .= '<table class="table table-striped- table-bordered table-hover table-checkable datatable">
				<tr>
				<td colspan="2" style="text-align: center !important; border: 1.5px solid black;">Credit Date : '.$dates.'</h3></td>
				<td colspan="2" style="text-align: center !important; border: 1.5px solid black;">Order No : '.$order.'</td>
				</tr>
				<tr>
				<td style="border: 1.5px solid black;">Product Name</td>
				<td style="border: 1.5px solid black;">Product Code</td>
				<td style="border: 1.5px solid black;">Warehouse</td>
				<td style="border: 1.5px solid black;">Quantity</th>

				</tr>';
				if(count($creditchild) > 0)
				{
					foreach ($creditchild as $keys => $values) {
						$product = Product::where('id',$values->product_id)->first();
						$warehouse = Warehouse::where('id',$values->warehouse_id)->first();
						if($warehouse)
						{
							$warehouse = $warehouse->name;
						}else{
							$warehouse= '';
						}

						$credit = $values->qty - $values->pending_qty;
						$html1 .= '<tr>
						<td style="border: 1.5px solid black;">'.$product->product.'</td>
						<td style="border: 1.5px solid black;">'.$product->code.'</td>
						<td style="border: 1.5px solid black;">'.$warehouse.'</td>
						<td style="border: 1.5px solid black; text-align: center;">'.$values->pending_qty.'</th>
						</tr>';
					}
				}
				$html1 .= '</table><br/>';
			}
		}
		return response()->json(['status'=>'success','data'=>$html ,'data1' =>$html1]);
	}
}
