<?php
namespace App\Imports;
use DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\User;
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
use Auth;

class SalesOrderImport implements ToModel,WithHeadingRow, WithValidation
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

		$product = Product::where('branch_id',$branch_id)->where('code',$row['product_code'])->first();
		if(!isset($product))
		{
			throw new \Exception("At Row ".$this->rowNumber.": ".$row['product_code']." doesn't exists in Product Master.");
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
		$so = SalesOrder::where('branch_id',$branch_id)->where('order_no',$row['order_no'])->first();
		if(!isset($so))
		{
			$param = [
				'branch_id' => $branch_nm,
				'employee_id' => $employee_nm,
				'name' => (($client)?($client->id):NULL),
				'order_no' => $row['order_no'],
				'date' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date'])->format('Y-m-d'),
			];
			$so = SalesOrder::create($param);
		}

		$so_child = SalesOrderChild::where('salesorder_id',$so->id)->where('product_id',$product->id)->first();
		if(!isset($so_child))
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
				'salesorder_id' => $so->id,
				'productcategory_id' => (($product)?($product->productcategory_id):NULL),
				'company_id' => (($product)?($product->company_id):NULL),
				'product_id' => (($product)?($product->id):NULL),
				'warehouse_id' => (($warehouse)?($warehouse->id):NULL),
				'qty' => (string)number_format((float)$row['qty'],2,'.','') ?? (string)$static_var,
				'rate' => (string)number_format((float)$row['rate'],2,'.','') ?? (string)$static_var,
				'discount_per' => $discount_per,
				'discount_amt' => (string)number_format((float)$row['discount_amt'],2,'.','') ?? (string)$static_var,
				'gst_id' => (($gst)?($gst->id):NULL),
				'gst' => (string)number_format((float)$row['gst'],2,'.','') ?? (string)$static_var,
				'gst_amt' => $gst_amt,
				'amount' => $amount,
				'pending_qty' => (string)number_format((float)$row['qty'],2,'.','') ?? (string)$static_var,
			];

			$so_child = SalesOrderChild::create($param_child);

			$total_qty = SalesOrderChild::where('salesorder_id',$so->id)->sum('qty');
			$total_discount_amt = SalesOrderChild::where('salesorder_id',$so->id)->sum('discount_amt');
			$total_gst_amt = SalesOrderChild::where('salesorder_id',$so->id)->sum('gst_amt');
			$total_amount = SalesOrderChild::where('salesorder_id',$so->id)->sum('amount');

			$so->total_qty = (string)number_format((float)$total_qty,2,'.','') ?? (string)$static_var;
			$so->total_discount_amt = (string)number_format((float)$total_discount_amt,2,'.','') ?? (string)$static_var;
			$so->total_gst_amt = (string)number_format((float)$total_gst_amt,2,'.','') ?? (string)$static_var;
			$so->total_amount = (string)number_format((float)$total_amount,2,'.','') ?? (string)$static_var;
			$so->save();
		}
		else
		{
			throw new \Exception("At Row ".$this->rowNumber.": The product code ".$row['product_code']." has already been used.");
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
					'*.order_no' => 'required',
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
					'*.order_no' => 'required',
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
					'*.order_no' => 'required',
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
					'*.order_no' => 'required',
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