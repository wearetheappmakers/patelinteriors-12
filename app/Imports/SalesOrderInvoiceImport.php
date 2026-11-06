<?php
namespace App\Imports;
use DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\User;
use App\SalesOrderInvoice;
use App\SalesOrderInvoiceChild;
use App\SalesOrder;
use App\SalesOrderChild;
use App\Company;
use App\WarehouseInventory;
use App\Employee;
use App\Warehouse;
use App\GST;
use App\Product;
use App\Client;
use App\ProductCategory;
use App\ItemLedger;
use Auth;

class SalesOrderInvoiceImport implements ToModel,WithHeadingRow, WithValidation
{
	private $rowNumber = 2;

	public function model(array $row)
	{
		if(Auth::guard('user')->check())
		{
			if(Auth::user()->role == 1)
			{
				$branch_id = User::where('name',$row['branch'])->value('id');
			}
			else
			{
				$branch_id = Auth::user()->id;
			}
		}
		else
		{
			$branch_id = Auth::user()->branch_id;
		}

		if(Auth::guard('user')->check())
		{
			if(Auth::user()->role == 1)
			{
				$branch = User::where('name',$row['branch'])->first();
				if(!isset($branch))
				{
					throw new \Exception("At Row ".$this->rowNumber.": ".$row['branch']." Branch doesn't exists.");
				}
			}

			$employee = Employee::where('branch_id',$branch_id)->where('name',$row['employee'])->where('status',1)->whereIn('role',['1','2'])->first();
			if(!isset($employee))
			{
				throw new \Exception("At Row ".$this->rowNumber.": ".$row['employee']." Employee doesn't exists.");
			}
		}
		if(Auth::guard('employee')->check())
		{
			if(Auth::user()->role == 4)
			{
				$employee = Employee::where('branch_id',$branch_id)->where('name',$row['employee'])->where('status',1)->whereIn('role',['1','2'])->first();
				if(!isset($employee))
				{
					throw new \Exception("At Row ".$this->rowNumber.": ".$row['employee']." Employee doesn't exists.");
				}
			}
		}

		$client = Client::where('branch_id',$branch_id)->where('member',$row['client'])->first();
		if(!isset($client))
		{
			throw new \Exception("At Row ".$this->rowNumber.": ".$row['client']." Client doesn't exists.");
		}
		if(!isset($client->state_id))
		{
			throw new \Exception("At Row ".$this->rowNumber.": Please add the state for the client ".$row['client']);
		}

		$so = SalesOrder::where('branch_id',$branch_id)
		->where('name',$client->id)
		->where('order_no',$row['pending_so'])
		->whereIn('status',[0,2])
		->whereHas('salesorderchild',function($q){
			$q->where('pending_qty','>',0);
		});
		$so = $so->first();
		if(!isset($so))
		{
			throw new \Exception("At Row ".$this->rowNumber.": Sales Order ".$row['pending_so']." does not exist.");
		}

		$product = Product::where('branch_id', $branch_id)->where('code', $row['product_code'])->first();

		if (!isset($product)) {
			throw new \Exception("At Row ".$this->rowNumber.": Product Code ".$row['product_code']." does not exist in Product Master.");
		}

		$so_child = $so->salesorderchild()
		->where('product_id', $product->id)
		->first();

		if (!isset($so_child)) {
			throw new \Exception("At Row ".$this->rowNumber.": Product .".$row['product_code']." not found in Sales Order .".$row['pending_so'].".");
		}

		if ($row['qty'] > $so_child->pending_qty) {
			throw new \Exception("At Row ".$this->rowNumber.": Entered quantity is greater than the available pending quantity (".$so_child->pending_qty.").");
		}

		$warehouse = Warehouse::where('branch_id',$branch_id)->where('name',$row['warehouse'])->first();
		if(!isset($warehouse))
		{
			throw new \Exception("At Row ".$this->rowNumber.": ".$row['warehouse']." doesn't exists in Warehouse Master.");
		}

		$gst = GST::where('branch_id',$branch_id)->where('gst',$row['gst'])->first();
		if(!isset($gst))
		{
			throw new \Exception("At Row ".$this->rowNumber.": ".$row['gst']." doesn't exists in GST Master.");
		}

		$inventory = WarehouseInventory::where('branch_id',$branch_id)->where('product_id',$product->id)->where('warehouse_id',$warehouse->id)->first();
		if(!isset($inventory))
		{
			throw new \Exception("At Row ".$this->rowNumber.": Warehouse inventory not found for ".$row['product_code'].".");
		}
		if(isset($inventory))
		{
			$stock = $inventory->stock - $inventory->used;
			if($row['qty'] > $stock)
			{
				throw new \Exception("At Row ".$this->rowNumber.": Entered qty is greater than warehouse inventory stock for ".$row['product_code'].".");
			}
		}

		if(Auth::guard('user')->check())
		{
			if(Auth::user()->role == 1)
			{
				$branch_nm = (($branch)?($branch->id):NULL);
			}
			else
			{
				$branch_nm = Auth::user()->id;
			}
			$employee_nm = (($employee)?($employee->id):NULL);
		}

		if(Auth::guard('employee')->check())
		{
			if(Auth::user()->role == 4)
			{
				$employee_nm = (($employee)?($employee->id):NULL);
			}
			else
			{
				$employee_nm = Auth::user()->id;
			}
			$branch_nm = Auth::user()->branch_id; 
		}

		$static_var = '0.00';
		$si = SalesOrderInvoice::where('branch_id',$branch_id)->where('invoice_no',$row['invoice_no'])->first();
		if(!isset($si))
		{
			$param = [
				'branch_id' => $branch_nm,
				'employee_id' => $employee_nm,
				'client_id' => (($client)?($client->id):NULL),
				'pending_so' => (($so)?($so->id):NULL),
				'invoice_no' => $row['invoice_no'],
				'ids' => $row['invoice_no'],
				'date' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date'])->format('Y-m-d'),
				'labour_charges' => $row['labour_charges'],
				'transport_charges' => $row['transport_charges'],
			];
			$si = SalesOrderInvoice::create($param);
		}

		$si_child = SalesOrderInvoiceChild::where('invoice_id',$si->id)->where('product_id',$product->id)->first();
		if(!isset($si_child))
		{
			$base_amt = (string)number_format(((float)$row['qty'] * (float)$row['rate']),2,'.','');
			if($row['discount_amt'])
			{
				$discount_per = (string)number_format((100 * (float)$row['discount_amt'] / (float)$base_amt),2,'.','');
			}
			else
			{
				$discount_per = 0;
			}

			$dis_amt = (string)number_format(((float)$base_amt - (float)$row['discount_amt']),2,'.','');
			$gst_amt = (string)number_format(((float)$dis_amt * (float)$row['gst'] / 100),2,'.','');
			$amount = (string)number_format(((float)$dis_amt + (float)$gst_amt),2,'.','');

			$param_child = [
				'invoice_id' => $si->id,
				'salesorder_child_id' => $so_child->id,
				'category_id' => (($product)?($product->productcategory_id):NULL),
				'company_id' => (($product)?($product->company_id):NULL),
				'product_id' => (($product)?($product->id):NULL),
				'warehouse_id' => (($warehouse)?($warehouse->id):NULL),
				'qty' => (string)number_format((float)$row['qty'],2,'.','') ?? (string)$static_var,
				'rate' => (string)number_format((float)$row['rate'],2,'.','') ?? (string)$static_var,
				'discount' => $discount_per,
				'discount_amt' => (string)number_format((float)$row['discount_amt'],2,'.','') ?? (string)$static_var,
				'gst_id' => (($gst)?($gst->id):NULL),
				'gst' => (string)number_format((float)$row['gst'],2,'.','') ?? (string)$static_var,
				'gst_amt' => $gst_amt,
				'amount' => $amount,
				'pending_qty' => (string)number_format((float)$row['qty'],2,'.','') ?? (string)$static_var,
				'new_qty' => (string)number_format((float)$row['qty'],2,'.','') ?? (string)$static_var,
			];

			$si_child = SalesOrderInvoiceChild::create($param_child);

			$total_qty = SalesOrderInvoiceChild::where('invoice_id',$si->id)->sum('qty');
			$total_discount_amt = SalesOrderInvoiceChild::where('invoice_id',$si->id)->sum('discount_amt');
			$total_gst_amt = SalesOrderInvoiceChild::where('invoice_id',$si->id)->sum('gst_amt');
			$total_amount = SalesOrderInvoiceChild::where('invoice_id',$si->id)->sum('amount');

			$si->total_qty = (string)number_format((float)$total_qty,2,'.','') ?? (string)$static_var;
			$si->total_discount_amt = (string)number_format((float)$total_discount_amt,2,'.','') ?? (string)$static_var;
			$si->total_gst_amt = (string)number_format((float)$total_gst_amt,2,'.','') ?? (string)$static_var;
			$si->total_amount = (string)number_format((float)$total_amount,2,'.','') ?? (string)$static_var;
			$si->grand_total = (string)number_format(((float)$total_amount + (float)$row['labour_charges'] + (float)$row['transport_charges']),2,'.','');
			$si->save();

			$sales_child = SalesOrderChild::where('id',$si_child->salesorder_child_id)->first();
			$sales_child->pending_qty = $sales_child->pending_qty - $row['qty'];
			$sales_child->save();

			$inventory = WarehouseInventory::where('branch_id',$branch_nm)->where('product_id',$product->id)->where('warehouse_id',$warehouse->id)->first();
			$inventory->used = $inventory->used + $row['qty'];
			$inventory->save();

			$item_ledger = new ItemLedger();
			$item_ledger->date = $si->date;
			$item_ledger->tran_type = 'Sales Invoice';
			$item_ledger->client_id = $si->client_id;
			$item_ledger->branch_id = $si->branch_id;
			$item_ledger->warehouse_id = $si_child->warehouse_id;
			$item_ledger->category_id = $si_child->category_id;
			$item_ledger->company_id = $si_child->company_id;
			$item_ledger->product_id = $si_child->product_id;
			$item_ledger->product_code = $product->code;
			$item_ledger->invoice_no = $si->invoice_no;
			$item_ledger->salesinvoice_id = $si->id;
			$item_ledger->salesinvoice_child_id = $si_child->id;
			$item_ledger->outward = (string)number_format((float)$si_child->qty,2,'.','') ?? (string)$static_var;
			$item_ledger->save();
		}
		else
		{
			throw new \Exception("At Row ".$this->rowNumber.": The product code ".$row['product_code']." has already been used.");
		}

		$sales_order = SalesOrder::where('id',$si->pending_so)->first();
		if(isset($sales_order))
		{
			$total_qty = SalesOrderChild::where('salesorder_id', $sales_order->id)->sum('qty');
			$total_pending_qty = SalesOrderChild::where('salesorder_id', $sales_order->id)->sum('pending_qty');

			if ($total_pending_qty == 0)
			{
				$status = 1;
			} 
			elseif($total_pending_qty < $total_qty)
			{
				$status = 2;
			} 
			else 
			{
				$status = 0;
			}
			$sales_order->status = $status;
			$sales_order->save();
		}

		$this->rowNumber++;
	}

	public function rules(): array
	{
		if(Auth::guard('user')->check())
		{
			if(Auth::user()->role == 1)
			{
				return [
					'*.branch' => 'required',
					'*.employee' => 'required',
					'*.client' => 'required',
					'*.pending_so' => 'required',
					'*.invoice_no' => 'required',
					'*.date' => 'required',
					'*.product_code' => 'required',
					'*.warehouse' => 'required',
					'*.qty' => 'required|numeric',
					'*.rate' => 'required',
					'*.gst' => 'required',
				];
			}
			else
			{
				return [
					'*.employee' => 'required',
					'*.client' => 'required',
					'*.pending_so' => 'required',
					'*.invoice_no' => 'required',
					'*.date' => 'required',
					'*.product_code' => 'required',
					'*.warehouse' => 'required',
					'*.qty' => 'required|numeric',
					'*.rate' => 'required',
					'*.gst' => 'required',
				];
			}
		}
		if(Auth::guard('employee')->check())
		{
			if(Auth::user()->role == 4)
			{
				return [
					'*.employee' => 'required',
					'*.client' => 'required',
					'*.pending_so' => 'required',
					'*.invoice_no' => 'required',
					'*.date' => 'required',
					'*.product_code' => 'required',
					'*.warehouse' => 'required',
					'*.qty' => 'required|numeric',
					'*.rate' => 'required',
					'*.gst' => 'required',
				];
			}
			else
			{
				return [
					'*.client' => 'required',
					'*.pending_so' => 'required',
					'*.invoice_no' => 'required',
					'*.date' => 'required',
					'*.product_code' => 'required',
					'*.warehouse' => 'required',
					'*.qty' => 'required|numeric',
					'*.rate' => 'required',
					'*.gst' => 'required',
				];
			}
		}
	}
}