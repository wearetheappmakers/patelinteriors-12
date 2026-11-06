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
use App\Warehouse;
use App\GST;
use Auth;
use App\Product;
use App\Client;
use App\ProductCategory;
use Illuminate\Http\Request;
use DB;
use Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use App\Imports\SalesOrderImport;

class SalesOrderController extends Controller
{
    public function __construct(SalesOrder $s)
    {
        $this->view = 'salesorder';
        $this->route = 'salesorder';
        $this->viewName = 'Sales Order';

        $this->middleware(function ($request, $next) {
            $this->setSalesOrderNumber();
            return $next($request);
        });
    } 

    private function setSalesOrderNumber()
    {
        $so_no = SalesOrder::orderBy('id', 'DESC');

        if (Auth::guard('user')->check() && Auth::guard('user')->user()->role == 2) {

            $so_no->where('branch_id', Auth::guard('user')->user()->id);
        } elseif (Auth::guard('employee')->check()) {
            $so_no->where('branch_id', Auth::guard('employee')->user()->branch_id);
        }

        $so_no = $so_no->first();

        if ($so_no) {
            $no = explode('-', $so_no['order_no']);
            $no = $no[1] + 1;
            $this->so_no = 'SalesOrder-' . $no;
        } else {
            $this->so_no = 'SalesOrder-1';
        }
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = SalesOrder::with('branch','client')->orderBy('date','DESC');
            if(Auth::guard('user')->check() && Auth::user()->role == 2)
            {
                $query->where('branch_id',Auth::user()->id);
            }
            if(Auth::guard('employee')->check())
            {
                if(Auth::user()->role == 4)
                {
                    $query->where('branch_id',Auth::user()->branch_id);
                } else {
                    $query->where('employee_id',Auth::user()->id);
                }
            }
            if($request->status != '')
            {
                $query->where('status',$request->status);
            }
            if (Auth::guard('user')->check() &&
                Auth::user()->role == 1 &&
                $request->filled('branch_id')) {

                $query->where('branch_id', $request->branch_id);
            }
            $query = $query->latest();

            return Datatables::of($query)
            ->addColumn('action', function ($row) {
                $btn = '';
                $existstable = DB::table('salesorder_invoice')->where('pending_so', $row->id)->exists();

                if($existstable == false)
                {
                    $btn .= '<a style="background: transparent;" href="'.route('user.salesorder.edit',$row->id).'"  title="Edit details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color:green;" class="la la-edit"></i></a>&nbsp;&nbsp;&nbsp;';
                }
                $btn .= view('layouts.actionbtnpermission')->with(['id'=>$row->id,'route'=>'salesorder','per_value'=>'salesorder'])->render();      
                return $btn;
            })
            ->addColumn('date', function ($row) {
                $btn = '';
                if($row->date)
                {
                    $btn = date('d-m-Y', strtotime($row->date));
                }
                return $btn;
            })
            ->addColumn('status', function ($row) {

                $btn = '';
                if($row->status == 1)
                {
                    $btn .= '<span style="color: #17c653 !important;">Completed</span>';
                }
                if($row->status == 2)
                {
                    $btn .= '<span style="color: #7239ea !important;">Paritally Completed</span>';
                }
                if($row->status == 0)
                {
                    $btn .= '<span style="color: #f8285a !important;">Pending</span>';
                }
                return $btn;
            })
            ->rawColumns(['action','status','date'])
            ->make(true);
        }
          $branch = User::where('role',2)->get();
        return view('salesorder.index',compact('branch'));
    }

    public function create()
    {
        $data['title'] = 'Add ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        
        $data['branch'] = User::where('role',2)->get();
        $data['so_no'] = "";
        $data['client'] = collect();
        $data['employees'] = collect();
        $data['category'] = collect();

        if(Auth::guard('user')->check() && Auth::user()->role == 2)
        {
            $data['client'] = Client::where('branch_id',Auth::user()->id)->get();
            $data['employees'] = Employee::where('status',1)->whereIn('role',['1','2'])->where('branch_id',Auth::user()->id)->get();
            $data['category'] = ProductCategory::where('branch_id',Auth::user()->id)->get();
            $data['so_no'] = $this->so_no;
        }
        elseif(Auth::guard('employee')->check())
        {
            $data['client'] = Client::where('branch_id',Auth::user()->branch_id)->get();
            $data['employees'] = Employee::where('status',1)->whereIn('role',['1','2'])->where('branch_id',Auth::user()->branch_id)->get();
            $data['category'] = ProductCategory::where('branch_id',Auth::user()->branch_id)->get();
            $data['so_no'] = $this->so_no;
        }
        
        return view('general.add_form')->with($data);
    }

    public function status(Request $request)
    {
        $param['status'] = $request->status;

        $change_status = SalesOrder::where('id',$request->id)->update($param);
        if($change_status)
        {
            return response()->json(['status' => 'success']);
        }
        else
        {
            return response()->json(['status' => 'error']);
        }
    }

    public function store(Request $request)
    {
        if(!empty($request->product_id) && count($request->product_id) > 0)
        {
            foreach ($request->product_id as $key => $value)
            {
                if($value != '')
                {
                    if(!($request->warehouse_id[$key] && $request->qty[$key] && ($request->gst_id[$key] != null)))
                    {
                        return response()->json(['status' => 'required', 'message' => 'Warehouse, Qty And GST Is Required !!!']);
                    }
                }
            }
        }

        if(Auth::guard('user')->check() &&  Auth::user()->role == 1)
        {
            $code = SalesOrder::where('order_no','like','SalesOrder-%')->where('branch_id',$request->branch_id)->orderBy('id','DESC')->first();
            if(isset($code))
            {
                $no = explode('-',$code['order_no']);
                $no = $no[1] + 1;
                $so_no = 'SalesOrder-'.$no; 
            }
            else  
            {
                $so_no = 'SalesOrder-1';
            }
        }
        else
        {
            $so_no = $this->so_no;
        }

        $salesorder = new SalesOrder();
        $salesorder->order_no = $so_no;
        
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $salesorder->branch_id = $request->branch_id;
            }
            else
            {
                $salesorder->branch_id = Auth::user()->id;
            }
            $salesorder->employee_id = $request->employee_id;
        }

        if(Auth::guard('employee')->check())
        {
            if(Auth::user()->role == 4)
            {
                $salesorder->employee_id = $request->employee_id;
            } else {
                $salesorder->employee_id = Auth::user()->id;
            }
            $salesorder->branch_id = Auth::user()->branch_id; 
        }

        $salesorder->date = $request->date;
        $salesorder->name = $request->name;
        $salesorder->total_qty = $request->total_qty;
        $salesorder->total_discount_amt = $request->total_discount_amt;
        $salesorder->total_gst_amt = $request->total_gst_amt;
        $salesorder->total_amount = $request->total_amount;
        $salesorder->save();


        if($request->product_id)
        {
            foreach ($request->product_id as $key => $value) {
                if($value != '')
                {
                    $product = Product::where('id',$value)->first();

                    $child = new SalesOrderChild();
                    $child->salesorder_id = $salesorder->id;
                    $child->productcategory_id = $product->productcategory_id;
                    $child->company_id = $product->company_id;
                    $child->product_id = $value;
                    $child->warehouse_id = $request->warehouse_id[$key];
                    $child->qty = $request->qty[$key];
                    $child->rate = $request->rate[$key];
                    $child->discount_per = $request->discount_per[$key];
                    $child->discount_amt = $request->discount_amt[$key];
                    $child->gst_id = GST::where('gst',$request->gst_id[$key])->value('id');
                    $child->gst = $request->gst[$key];
                    $child->gst_amt = $request->gst_amt[$key];
                    $child->amount = $request->amount[$key];
                    $child->pending_qty = $request->qty[$key];
                    $child->save();
                }
            }
        }

        if($salesorder)
        {
            return response()->json(['status' => 'success']);
        }else
        {
            return response()->json(['status' => 'error']);
        }
    }

    public function edit($id)
    {
        $data['title'] = 'Edit ' . $this->viewName;
        $data['data'] = SalesOrder::where('id', $id)->first();
        $data['salesorderchild'] = SalesOrderChild::where('salesorder_id',$id)->get();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['client'] = Client::where('branch_id',$data['data']->branch_id)->get();
        $data['employees'] = Employee::where('status',1)->whereIn('role',['1','2'])->where('branch_id',$data['data']->branch_id)->get();
        $data['category'] = ProductCategory::where('branch_id',$data['data']->branch_id)->get();
        $data['warehouse'] = Warehouse::where('branch_id',$data['data']->branch_id)->get();
        $data['gst'] = GST::where('branch_id',$data['data']->branch_id)->get();
        return view('general.edit_form')->with($data);
    }
    
    public function update(Request $request, $id)
    {
        if(!empty($request->product_id) && count($request->product_id) > 0)
        {
            foreach ($request->product_id as $key => $value)
            {
                if($value != '')
                {
                    if(!($request->warehouse_id[$key] && $request->qty[$key] && ($request->gst_id[$key] != null)))
                    {
                        return response()->json(['status' => 'required', 'message' => 'Warehouse, Qty And GST Is Required !!!']);
                    }
                }
            }
        }

        $salesorder = SalesOrder::where('id',$id)->first();
        
        if(!($salesorder->status == '0'))
        {
            return response()->json(['status' => 'required', 'message' => 'You Can Only Update Sales Orders With Pending Status !!!']);
        }

        if(Auth::guard('user')->check())
        {
            $salesorder->employee_id = $request->employee_id;
        }

        if(Auth::guard('employee')->check())
        {
            if(Auth::user()->role == 4)
            {
                $salesorder->employee_id = $request->employee_id;
            } else {
                $salesorder->employee_id = Auth::user()->id;
            }
        }

        $salesorder->date = $request->date;
        $salesorder->total_qty = $request->total_qty;
        $salesorder->total_discount_amt = $request->total_discount_amt;
        $salesorder->total_gst_amt = $request->total_gst_amt;
        $salesorder->total_amount = $request->total_amount;
        $salesorder->save();

        if($request->product_id)
        {
            foreach ($request->product_id as $key => $value) {
                if($value != '')
                {
                    $product = Product::where('id',$value)->first();
                    
                    if(!empty($request->hidden_id[$key]))
                    {
                        $child = SalesOrderChild::where('id',$request->hidden_id[$key])->first();
                    }
                    else
                    {
                        $child = new SalesOrderChild();
                    }

                    $child->salesorder_id = $salesorder->id;
                    $child->productcategory_id = $product->productcategory_id;
                    $child->company_id = $product->company_id;
                    $child->product_id = $value;
                    $child->warehouse_id = $request->warehouse_id[$key];
                    $child->qty = $request->qty[$key];
                    $child->rate = $request->rate[$key];
                    $child->discount_per = $request->discount_per[$key];
                    $child->discount_amt = $request->discount_amt[$key];
                    $child->gst_id = GST::where('gst',$request->gst_id[$key])->value('id');
                    $child->gst = $request->gst[$key];
                    $child->gst_amt = $request->gst_amt[$key];
                    $child->amount = $request->amount[$key];
                    $child->pending_qty = $request->qty[$key];
                    $child->save();
                }
            }
        }

        if($salesorder)
        {
            return response()->json(['status' => 'success']);
        }else
        {
            return response()->json(['status' => 'error']);
        }
    }

    public function childdelete($id)
    {
        $basicchild = SalesOrderChild::where('id',$id)->first();
        $basic = SalesOrder::where('id',$basicchild->salesorder_id)->first();
        if($basic)
        {
            if(!($basic->status == '0'))
            {
                $notification = array(
                    'message' => 'You Can Only Update Sales Orders With Pending Status !!!',
                    'alert-type' => 'error',
                );
                return redirect()->back()->with($notification);
            }
            $basic->total_qty = round(($basic->total_qty - $basicchild->qty),2);
            $basic->total_discount_amt = round(($basic->total_discount_amt - $basicchild->discount_amt),2);
            $basic->total_gst_amt = round(($basic->total_gst_amt - $basicchild->gst_amt),2);
            $basic->total_amount = round(($basic->total_amount - $basicchild->amount),2);
            $basic->save();
        }
        $basicchild = SalesOrderChild::where('id',$id)->delete();
        
        $notification = array(
            'message' => 'Deleted Sussfully.',
            'alert-type' => 'success',
        );

        return redirect()->back()->with($notification);
    }

    public function show($id)
    {
        $data['id'] = $id;
        $data['data'] = SalesOrder::where('id',$id)->first();
        return view('salesorder.view')->with($data);
    }

    public function getemployee(Request $request)
    {
        $data = Employee::where('branch_id',$request->branch_id)->where('status',1)->whereIn('role',['1','2'])->get();
        $html = "<option value=''>Select</option>";
        foreach ($data as $key => $value) {
            $html .= "<option value=".$value->id.">".$value->name."</option>";
        }

        $productcategory = ProductCategory::where('status',1)->where('branch_id',$request->branch_id)->get();
        $html4 = "<option value=''>Select</option>";
        foreach ($productcategory as $keyssss => $valuessss) {
            $html4 .= "<option value=".$valuessss->id.">".$valuessss->name."</option>";
        }

        $clients = Client::where('branch_id',$request->branch_id)->get();
        $html5 = "<option value=''>Select</option>";
        foreach ($clients as $keysssss => $valuesssss) {
            $html5 .= "<option value=".$valuesssss->id.">".$valuesssss->member."</option>";
        }

        $so = SalesOrder::where('branch_id',$request->branch_id)->orderBy('id','DESC')->first();
        if(isset($so))
        {
            $no = explode('-',$so['order_no']);
            $no = $no[1] + 1;
            $so_no = 'SalesOrder-'.$no; 
        }
        else  
        {
            $so_no = 'SalesOrder-1';
        }

        return response()->json(['data'=>$html,'data4'=>$html4,'data5'=>$html5, 'data6' => $so_no]);
    }

    public function clientchange(Request $request)
    {
        $data = Client::where('id',$request->client_id)->first();

        return response()->json(['data'=>$data]);
    }

    public function getproductdata(Request $request)
    {
        $product = Product::where('id',$request->product_id)->first();

        $data = '';

        if(isset($product))
        {
            if(Auth::guard('user')->check())
            {
                if(Auth::user()->role == 1)
                {
                    $warehouse = Warehouse::where('branch_id',$request->branch_id)->get();
                    $gst = GST::where('branch_id',$request->branch_id)->get();
                }
                else
                {
                    $warehouse = Warehouse::where('branch_id',Auth::user()->id)->get();
                    $gst = GST::where('branch_id',Auth::user()->id)->get();
                }
            }
            else
            {
                $warehouse = Warehouse::where('branch_id',Auth::user()->branch_id)->get();
                $gst = GST::where('branch_id',Auth::user()->branch_id)->get();
            }

            $data .= '<tr class="master_clone">
            <td>
            <div style="display: inline-flex !important;">
            <button type="button" id="remove" class="btn btn-sm btn-icon btn-icon-md btn-clean remove"><i class="fa fa-minus" style="font-size:0.8rem !important"></i></button>
            </div>
            </td>
            
            <td>
            <input type="hidden" name="hidden_id[]" class="hidden_id">
            <input type="hidden" name="product_id[]" value="'.$product->id.'" class="product_id">
            '.$product->product.'<br>'.$product->code.'
            </td>
            
            <td>
            <select style="width: 100% !important;" class="warehouse_id" name="warehouse_id[]">
            <option value="">Select</option>';
            foreach($warehouse as $wh_key => $wh_val)
            {
                $data .= '<option value="'.$wh_val->id.'">'.$wh_val->name.'</option>';
            }
            $data .='</select>
            </td>
            
            <td><input type="number" name="qty[]" placeholder="Enter Qty" class="qty" style="width: 100% !important;"></td>

            <td><input type="number" name="rate[]" value="'.round($product->price,2).'" placeholder="Enter Rate" class="rate" style="width: 100% !important;"></td>

            <td>
            <input type="number" name="discount_per[]" placeholder="Enter Discount(%)" class="discount_per" style="width: 100% !important;">
            <input type="number" name="discount_amt[]" placeholder="Discount Amount" class="discount_amt" style="width: 100% !important;" >
            </td>

            <td>
            <input type="hidden" name="gst[]" class="gst" style="width: 100% !important;">
            <select style="width: 100% !important;" class="gst_id" name="gst_id[]">
            <option value="">Select</option>';
            foreach($gst as $gst_key => $gst_val)
            {
                $data .= '<option value="'.$gst_val->gst.'">'.$gst_val->gst.'</option>';
            }
            $data .='</select><br>
            <input type="text" placeholder="GST Amount" name="gst_amt[]" class="gst_amt" style="width: 100% !important;" readonly>
            </td>
            
            <td class="cgst_column" style="display: none;">
            <input type="text" placeholder="CGST" name="cgst[]" class="cgst" style="width: 100% !important;" readonly><br>
            <input type="text" placeholder="CGST Amount" name="cgst_amt[]" class="cgst_amt" style="width: 100% !important;" readonly>
            </td>

            <td class="cgst_column" style="display: none;">
            <input type="text" placeholder="SGST" name="sgst[]" class="sgst" style="width: 100% !important;" readonly><br>
            <input type="text" placeholder="SGST Amount" name="sgst_amt[]" class="sgst_amt" style="width: 100% !important;" readonly>
            </td>
            
            <td class="igst_column" style="display: none;">
            <input type="text" placeholder="IGST" name="igst[]" class="igst" style="width: 100% !important;" readonly><br>
            <input type="text" placeholder="IGST Amount" name="igst_amt[]" class="igst_amt" style="width: 100% !important;" readonly>
            </td>

            <td><input type="text" placeholder="Final Amount" name="amount[]" class="amount" style="width: 100% !important;" readonly></td>
            
            </tr>';
        }

        return response()->json(['data'=>$data]);
    }

    // public function getdata(Request $request)
    // {
    //     $check = Product::where('id',$request->product_id)->first();
    //     $data = '';
    //     if(isset($check))
    //     {
    //         $warehouse = WarehouseInventory::select('warehouse_id')->where('product_id',$request->product_id)->where('stock','!=',0)->groupBy('warehouse_id')->get();
    //         $data .= '<tr class = "div_clone" >
    //         <td>
    //         <div  style="display: inline-flex !important;">
    //         <button type="button" id="remove_container_details" class="btn btn-sm btn-icon btn-icon-md btn-clean remove_container_details" ><i class="fa fa-minus" style="font-size:0.8rem !important"></i></button>
    //         </div>
    //         </td>
    //         <td >
    //         <input type="hidden" name="new_product_id[]" value="'.$check->id.'" id="product_id_'.$check->id.'" data-id ="'.$check->id.'">
    //         <input type="text" readonly class="form-control product_name" name="product_name[]" id="product_name_'.$check->id.'" placeholder="Enter Name" data-id="'.$check->id.'" value="'.$check->product.'" >
    //         </td>
    //         <td >
    //         <input type="text" class="form-control product_code" value="'.$check->code.'" name="product_code[]" id="product_code_'.$check->id.'" placeholder="Enter Price" data-id="'.$check->id.'" readonly  >
    //         </td>
    //         <td >
    //         <select class="form-control warehouse_id" name="warehouse_id[]" id="warehouse_id_'.$check->id.'" data-id="'.$check->id.'" >
    //         <option value="">Select warehouse</option>';
    //         foreach ($warehouse as $key => $value)
    //         {
    //             $warehouse = Warehouse::where('id',$value->warehouse_id)->value('name');
    //             $data.= "<option value=".$value->warehouse_id.">".$warehouse."</option>";
    //         }
    //         $data .= '</select>
    //         </td>
    //         <td >
    //         <input type="number" class="form-control qty" id="qty_'.$check->id.'" placeholder="Enter Qty" name="qty[]" data-id="'.$check->id.'"  >
    //         </td>
    //         <td >
    //         <input type="number" class="form-control rate" id="rate_'.$check->id.'" placeholder="Enter Rate"  name="rate[]" data-id="'.$check->id.'"  >
    //         </td>
    //         <td >
    //         <input type="number" class="form-control discount" id="discount_'.$check->id.'" placeholder="Enter Discount"  name="discount[]" data-id="'.$check->id.'"  >
    //         </td>
    //         <td>
    //         <input type="number" class="form-control amount" id="amount_'.$check->id.'"  name="amount[]" data-id="'.$check->id.'" readonly  >
    //         </td></tr>
    //         ';
    //         return response()->json(['status'=>'success','data'=>$data]);
    //     }
    //     else
    //     {
    //         return response()->json(['status'=>'warehouse']);
    //     }
    // }

    public function sample_export()
    {
        return Excel::download(new SalesOrderSample, 'salesorder-sample.xlsx');
    }

    public function import(Request $request) 
    {
        try {
            Excel::import(new SalesOrderImport, $request->file('file1'));
            return response()->json(['status' => 'success']);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {

            $failures = $e->failures();
            $errorMessage = [];
            foreach ($failures as $failure) {
                $errorMessages = $failure->errors();
                $errorString = "At Row " . $failure->row() . ": " . implode(", ", $errorMessages);
                $errorMessage[] = $errorString;
            }
            return response()->json(['status' => 'error', 'data' => $errorMessage]);
        } catch (\Exception $ex) {

            return response()->json(['status'=>'error','data'=>$ex->getMessage()]);
        }
    }
}

class SalesOrderSample implements WithHeadings
{
    public function headings(): array
    {
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                return [
                    'branch',
                    'employee',
                    'client',
                    'order_no',
                    'date',
                    'product_code',
                    'warehouse',
                    'qty',
                    'rate',
                    'discount_amt',
                    'gst'
                ];
            }
            else
            {
                return [
                    'employee',
                    'client',
                    'order_no',
                    'date',
                    'product_code',
                    'warehouse',
                    'qty',
                    'rate',
                    'discount_amt',
                    'gst'
                ];
            }
        }
        if(Auth::guard('employee')->check())
        {
            if(Auth::user()->role == 4)
            {
                return [
                    'employee',
                    'client',
                    'order_no',
                    'date',
                    'product_code',
                    'warehouse',
                    'qty',
                    'rate',
                    'discount_amt',
                    'gst'
                ];
            }
            else
            {
                return [
                    'client',
                    'order_no',
                    'date',
                    'product_code',
                    'warehouse',
                    'qty',
                    'rate',
                    'discount_amt',
                    'gst'
                ];
            }
        }
    }
}