<?php
namespace App\Http\Controllers;
use App\User;
use DataTables;
use App\Section;
use App\SalesOrder;
use App\SalesOrderChild;
use App\Company;
use App\Warehouse;
use App\WarehouseInventory;
use App\Employee;
use App\Vendor;
use Auth;
use App\Product;
use App\Client;
use App\Debit;
use App\DebitChild;
use App\GST;
use App\ProductCategory;
use Illuminate\Http\Request;
use App\PurchaseOrder;
use App\PurchaseOrderChild;
class PurchaseOrderController extends Controller
{
    public function __construct(PurchaseOrder $s)
    {
        $this->view = 'purchaseorder';
        $this->route = 'purchaseorder';
        $this->viewName = 'PurchaseOrder';
    }   
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = PurchaseOrder::orderBy('date','DESC');
            if(Auth::guard('user')->check()){
                if(Auth::user()->role == 1)
                {
                    $query = $query->latest('date');
                }
                if(Auth::user()->role == 2)
                {
                    $query = $query->where('branch_id',Auth::user()->id)->latest('date');
                }
            }
            if(Auth::guard('employee')->check())
            {
                $query = $query->where('branch_id',Auth::user()->branch_id)->latest('date');
            }
            // Branch Filter (Only Super Admin)
            if (Auth::guard('user')->check() &&
                Auth::user()->role == 1 &&
                $request->filled('branch_id')) {

                $query->where('branch_id', $request->branch_id);
            }
            return Datatables::of($query)
            ->addColumn('action', function ($row) {
                $btn = '';
                if($row->status != 1)
                {
                    $btn .= '<a style="background: transparent;" href="'.route('user.purchaseorder.edit',$row->id).'"  title="Edit details" class="btn btn-sm btn-clean btn-icon btn-icon-md">
                    <i style="color: green;" class="la la-edit"></i>
                    </a>&nbsp;&nbsp;&nbsp;';
                }
                if(Auth::guard('user')->check())
                {
                    if(Auth::user()->role == 1)
                    {
                        $btn .= '<button style="background: white;" title="Delete" data-id="'.$row->id.'" class="btn btn-sm btn-clean btn-icon btn-icon-md delete-record">
                        <i style="color: red;" class="la la-trash">
                        </i>
                        </button>&nbsp;&nbsp;&nbsp;';
                    }
                }
                $btn .= '<a style="background: transparent;" href="'.route('user.purchaseorder.show',$row->id).'"  title="View" class="btn btn-sm btn-clean btn-icon btn-icon-md">
                <i style="color: black;" class="la la-eye"></i>
                </a>&nbsp;&nbsp;&nbsp;';   
                return $btn;
            })
            ->addColumn('singlecheckbox', function ($row) {
                $schk = view('layouts.singlecheckbox')->with(['id' => $row->id , 'status'=>$row->status])->render();
                return $schk;
            })
            ->addColumn('vendor_id', function ($row) {
                if($row->vendor_id)
                {
                    $btn = Vendor::where('id',$row->vendor_id)->value('name');
                }else
                {
                    $btn = '-';
                }
                return $btn;
            })
            ->addColumn('branch_id', function ($row) {
                if($row->branch_id)
                {
                    $btn = User::where('id',$row->branch_id)->value('name');
                }else
                {
                    $btn = '-';
                }
                return $btn;
            })
            ->addColumn('date', function ($row) {
                $btn = date('d-m-Y', strtotime($row->date));
                return $btn;
            })
            ->addColumn('status', function ($row) {
                if($row->status == 1 )
                {
                    $btn = '<select class="form-control" disabled><option value="0"';
                }
                else
                {
                    $btn = '<select class="form-control" onchange="status(this,'.$row->id.')"><option value="0"';
                }
                $selected = '';
                if($row->status == 0) 
                {
                    $selected = 'selected';
                }
                $btn .= $selected.'>Pending</option><option value="1" ';
                $selected = '';
                if($row->status == 1)
                {
                    $selected = 'selected';
                }
                $btn .= $selected.'>Completed</option>
                </select>';
                return $btn;
            })
            ->rawColumns(['numbers','action','singlecheckbox','status','date','vendor_id','branch_id'])
            ->make(true);
        } 
        $branches = User::where('role', 2)
        ->where('status', 1)
        ->orderBy('name')
        ->get();

       return view('purchaseorder.index', compact('branches'));
    }
    public function create()
    {
        $data['title'] = 'Add ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        
        $data['branch'] = User::where('role',2)->get();
        
        return view('general.add_form')->with($data);
    }
    public function status(Request $request)
    {
        $param['status'] = $request->status;
        $change_status = PurchaseOrder::where('id',$request->id)->update($param);
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
        if($request->warehouse_id)
        {
            foreach ($request->warehouse_id as $key => $value) {
                if($value == '')
                {
                    return response()->json(['status' => 'required', 'message' => 'Warehouse is required!!']);
                }
            }
        }
        if($request->qty)
        {
            foreach ($request->qty as $key => $value) {
                if($value == '')
                {
                    return response()->json(['status' => 'required', 'message' => 'Qty. is required!!']);
                }
            }
        }
        if($request->gst)
        {
            foreach ($request->gst as $key => $value) {
                if($value == '')
                {
                    return response()->json(['status' => 'required', 'message' => 'GST is required!!']);
                }
            }
        }
        if($request->order_no)
        {
            $order = PurchaseOrder::where('branch_id',$request->branch_id)->where('order_no',$request->order_no)->first();
            if($order)
            {
                return response()->json(['status' => 'required', 'message' => 'Duplicate Order No. Entered.']);
            }
        }
        $purchaseorder = new PurchaseOrder();
        $purchaseorder->order_no = $request->order_no;
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $purchaseorder->branch_id = $request->branch_id;
            }
            else
            {
                $purchaseorder->branch_id = Auth::user()->id;
            }
        }
        if(Auth::guard('employee')->check())
        {
            $purchaseorder->branch_id = Auth::user()->branch_id;
        }
        $purchaseorder->date = $request->date;
        $purchaseorder->vendor_id = $request->vendor_id;
        $purchaseorder->total_qty = $request->total_qty;
        $purchaseorder->total_discount = $request->total_discount;
        $purchaseorder->total_gst = $request->total_gst;
        $purchaseorder->total_amount = $request->final_amount;
        $purchaseorder->save();
        if($request->new_product_id)
        {
            foreach ($request->new_product_id as $key => $value) {
                if($value != '')
                {
                    $product = Product::where('id',$value)->first();
                    $child = new PurchaseOrderChild();
                    $child->purchase_order_id = $purchaseorder->id;
                    $child->category_id = $product->productcategory_id;
                    $child->company_id = $product->company_id;
                    $child->product_id = $product->id;
                    $child->warehouse_id = $request->warehouse_id[$key];
                    $child->qty = $request->qty[$key];
                    $child->rate = $request->rate[$key];
                    $child->discount = $request->discount[$key];
                    $child->discount_amount = $request->discount_amount[$key];
                    $child->gst = $request->gst[$key];
                    $child->gst_amt = $request->gst_amt[$key];
                    $child->pending_qty = $request->qty[$key];
                    $child->amount = $request->amount[$key];
                    $child->save();
                }
            }
        }
        if($purchaseorder)
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
        $data['data'] = PurchaseOrder::where('id', $id)->first();
        $data['purchaseorderchild'] = PurchaseOrderChild::where('purchase_order_id',$id)->get();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['branch'] = User::where('role',2)->get();
        
        return view('general.edit_form')->with($data);
    }
    public function update(Request $request, $id)
    {
        if($request->warehouse_id)
        {
            foreach ($request->warehouse_id as $key => $value) {
                if($value == '')
                {
                    return response()->json(['status' => 'required', 'message' => 'Warehouse is required!!']);
                }
            }
        }
        if($request->edit_qty)
        {
            foreach ($request->edit_qty as $key => $value) {
                if($value == '')
                {
                    return response()->json(['status' => 'required', 'message' => 'Qty. is required!!']);
                }
            }
        }
        if($request->qty)
        {
            foreach ($request->qty as $key => $value) {
                if($value == '')
                {
                    return response()->json(['status' => 'required', 'message' => 'Qty. is required!!']);
                }
            }
        }
        if($request->edit_gst)
        {
            foreach ($request->edit_gst as $key => $value) {
                if($value == '')
                {
                    return response()->json(['status' => 'required', 'message' => 'GST is required!!']);
                }
            }
        }
        if($request->gst)
        {
            foreach ($request->gst as $key => $value) {
                if($value == '')
                {
                    return response()->json(['status' => 'required', 'message' => 'GST is required!!']);
                }
            }
        }
        $purchaseorder = PurchaseOrder::where('id',$id)->first();
        if($request->order_no)
        {
            $order = PurchaseOrder::where('branch_id',$purchaseorder->branch_id)->where('order_no',$request->order_no)->where('id','!=',$id)->first();
            if($order)
            {
                return response()->json(['status' => 'required', 'message' => 'Duplicate Order No. Entered.']);
            }
        }
        $purchaseorder->order_no = $request->order_no;
        $purchaseorder->date = $request->date;
        $purchaseorder->total_qty = $request->total_qty;
        $purchaseorder->total_discount = $request->total_discount;
        $purchaseorder->total_gst = $request->total_gst;
        $purchaseorder->total_amount = $request->final_amount;
        // $purchaseorder->vendor_id = $request->vendor_id;
        $purchaseorder->save();
        if($request->new_product_id)
        {
            foreach ($request->new_product_id as $key => $value) {
                if($value != '')
                {
                    $product = Product::where('id',$value)->first();
                    $child = new PurchaseOrderChild();
                    $child->purchase_order_id = $purchaseorder->id;
                    $child->category_id = $product->productcategory_id;
                    $child->company_id = $product->company_id;
                    $child->product_id = $product->id;
                    $child->warehouse_id = $request->warehouse_id[$key];
                    $child->qty = $request->qty[$key];
                    $child->rate = $request->rate[$key];
                    $child->discount = $request->discount[$key];
                    $child->discount_amount = $request->discount_amount[$key];
                    $child->gst = $request->gst[$key];
                    $child->gst_amt = $request->gst_amt[$key];
                    $child->pending_qty = $request->qty[$key];
                    $child->amount = $request->amount[$key];
                    $child->save();
                }
            }
        }
        if($request->edit_qty)
        {
            foreach ($request->edit_qty as $key => $value) {
                {
                    if($value != '')
                    {
                        $purchaseorderchild['purchase_order_id'] = $id;
                        $purchaseorderchild['qty'] = $value;
                        $purchaseorderchild['rate'] = $request->edit_rate[$key];
                        $purchaseorderchild['discount'] = $request->edit_discount[$key];
                        $purchaseorderchild['discount_amount'] = $request->edit_discount_amount[$key];
                        $purchaseorderchild['gst'] = $request->edit_gst[$key];
                        $purchaseorderchild['gst_amt'] = $request->edit_gst_amt[$key];
                        $purchaseorderchild['amount'] = $request->edit_amount[$key];
                        $purchaseorderchild1 = PurchaseOrderChild::where('id',$request->edit_main_id[$key])->update($purchaseorderchild);
                    }
                }
            }
        }
        if($purchaseorder)
        {
            return response()->json(['status' => 'success']);
        }else
        {
            return response()->json(['status' => 'error']);
        }
    }
    public function childdelete($id)
    {
        $basicchild = PurchaseOrderChild::where('id',$id)->first();
        $basic = PurchaseOrder::where('id',$basicchild->purchase_order_id)->first();
        if($basic)
        {
            $basic->total_qty = round(($basic->total_qty - $basicchild->qty),2);
            $basic->total_discount = round(($basic->total_discount - $basicchild->discount_amount),2);
            $basic->total_gst = round(($basic->total_gst - $basicchild->gst_amt),2);
            $basic->total_amount = round(($basic->total_amount - $basicchild->amount),2);
            $basic->save();
        }
        PurchaseOrderChild::where('id',$id)->delete();
        
        return response()->json(['status' => 'success'  ]);
    }
    public function show($id)
    {
        $data['id'] = $id;
        $data['branch'] = User::where('role',2)->get();
        $data['employee'] = Employee::get();
        $data['data'] = PurchaseOrder::where('id',$id)->first();
        $data['purchaseorderchild'] = PurchaseOrderChild::where('purchase_order_id',$data['data']->id)->get();
        
        return view('purchaseorder.view')->with($data);
    }
    public function getemployee(Request $request)
    {
        $data = Vendor::where('branch_id',$request->branch_id)->get();
        $html = "<option value=''>Select</option>";
        foreach ($data as $key => $value) {
            $html .= "<option value=".$value->id.">".$value->name."</option>";
        }
        $productcategory = ProductCategory::where('status',1)->where('branch_id',$request->branch_id)->get();
        $html4 = "<option value=''>Select</option>";
        foreach ($productcategory as $keyssss => $valuessss) {
            $html4 .= "<option value=".$valuessss->id.">".$valuessss->name."</option>";
        }
        
        return response()->json(['data'=>$html,'data4'=>$html4]);
    }
    // public function getcompany(Request $request)
    // {
    //     $branch_id = $request->branch_id;
    //     $data = Company::where('pro_category_id',$request->category_id)
    //                     ->whereRaw("FIND_IN_SET(?, branch_id)", [$branch_id])
    //                     ->get();
    //     $html = "<option value=''>Select</option>";
    //     foreach ($data as $key => $value) {
    //         $html .= "<option value=".$value->id.">".$value->name."</option>";
    //     }      
    //     return response()->json(['data'=>$html]);
    // }
    public function getcompany(Request $request)
    {
        // ✅ STEP 1: detect branch_id properly
        if ($request->branch_id) {
            // Admin case
            $branch_id = $request->branch_id;

        } else {
            // Branch login (user guard)
            if (Auth::guard('user')->check()) {
                if (Auth::user()->role == 2) {
                    $branch_id = Auth::user()->id; // branch user id = branch_id
                } else {
                    $branch_id = Auth::user()->branch_id ?? Auth::user()->id;
                }
            }

            // HR login (employee guard)
            elseif (Auth::guard('employee')->check()) {
                $branch_id = Auth::user()->branch_id;
            }
        }

        // ✅ STEP 2: query
        $data = Company::where('pro_category_id', $request->category_id)
            ->whereRaw("FIND_IN_SET(?, branch_id)", [$branch_id])
            ->get();

        // ✅ STEP 3: response
        $html = "<option value=''>Select</option>";
        foreach ($data as $value) {
            $html .= "<option value='{$value->id}'>{$value->name}</option>";
        }

        return response()->json(['data' => $html]);
    }
    public function getproduct(Request $request)
    {
        $data = Product::where('company_id',$request->company_id)->get();
        $html = "<option value=''>Select</option>";
        foreach ($data as $key => $value) {
            $html .= "<option value=".$value->id.">".$value->dropdown_name."</option>";
        }
        return response()->json(['data'=>$html]);
    }
    public function getdata(Request $request)
    {
        $check = Product::where('id',$request->product_id)->first();
        $data = '';
        if(isset($check))
        {
            if(Auth::guard('user')->check())
            {
                if(Auth::user()->role == 1)
                {
                    $warehouse = Warehouse::where('branch_id',$request->branch_id)->get();
                }
                if(Auth::user()->role == 2)
                {
                    $warehouse = Warehouse::where('branch_id',Auth::user()->id)->get();
                }
            }
            else
            {
                $warehouse = Warehouse::where('branch_id',Auth::user()->branch_id)->get();
            }
            if(Auth::guard('user')->check())
            {
                if(Auth::user()->role == 1)
                {
                    $gst = GST::where('branch_id',$request->branch_id)->get();
                }
                if(Auth::user()->role == 2)
                {
                    $gst = GST::where('branch_id',Auth::user()->id)->get();
                }
            }
            else
            {
                $gst = GST::where('branch_id',Auth::user()->branch_id)->get();
            }
            $data .= '<tr class="div_clone">
            <td>
            <div  style="display: inline-flex !important;">
            <button type="button" id="remove_container_details" class="btn btn-sm btn-icon btn-icon-md btn-clean remove_container_details" ><i class="fa fa-minus" style="font-size:0.8rem !important"></i></button>
            </div>
            </td>
            <td style="font-size: 15px !important;">
            <input type="hidden" name="new_product_id[]" value="'.$check->id.'" id="product_id_'.$check->id.'" data-id ="'.$check->id.'">
            <input type="hidden" readonly class="form-control product_name" name="product_name[]" id="product_name_'.$check->id.'" placeholder="Enter Name" data-id="'.$check->id.'" value="'.$check->product.'" >
            <input type="hidden" class="form-control product_code" value="'.$check->code.'" name="product_code[]" id="product_code_'.$check->id.'" placeholder="Enter Price" data-id="'.$check->id.'" readonly  >

            '.$check->product.' <br> '.$check->code.'
            </td>
            <td style="font-size: 15px !important;">
            <select style="width: 100% !important;" class="warehouse_id" name="warehouse_id[]" id="warehouse_id_'.$check->id.'" data-id="'.$check->id.'" >
            <option value="">Select</option>';
            foreach ($warehouse as $key => $value)
            {
                $data.= "<option value=".$value->id.">".$value->name."</option>";
            }
            $data .= '</select>
            </td>
            <td style="font-size: 15px !important;">
            <input type="number" style="width: 100% !important;" class="qty" id="qty_'.$check->id.'" placeholder="Enter Qty" name="qty[]" data-id="'.$check->id.'">
            </td>
            <td style="font-size: 15px !important;">
            <input type="number" style="width: 100% !important;" class="rate" id="rate_'.$check->id.'" placeholder="Enter Rate" value="'.round($check->price,2).'"  name="rate[]" data-id="'.$check->id.'"  >
            </td>
            <td style="font-size: 15px !important;">
            <input type="number" style="width: 100% !important;" class="discount" id="discount_'.$check->id.'" placeholder="Enter Discount(%)" name="discount[]" data-id="'.$check->id.'"><br>
            <input type="number" style="width: 100% !important;" class="discount_amount" id="discount_amount_'.$check->id.'" placeholder="Enter Discount Amount" name="discount_amount[]" data-id="'.$check->id.'" >
            </td>
            <td style="font-size: 15px !important;">
            <select style="width: 100% !important;" class="gst" name="gst[]" id="gst_'.$check->id.'" data-id="'.$check->id.'" >
            <option value="">Select</option>';
            foreach ($gst as $key1 => $value1)
            {
                $data.= "<option value=".$value1->id.">".$value1->gst."</option>";
            }
            $data .= '</select><br>
            <input type="number" style="width: 100% !important;" class="gst_amt" id="gst_amt_'.$check->id.'" placeholder="Enter GST Amount" name="gst_amt[]" data-id="'.$check->id.'" readonly>
            </td>
            <td style="font-size: 15px !important;" class="cgstdiv" data-id="'.$check->id.'" id="cgstdiv_'.$check->id.'">
            <input type="number" style="width: 100% !important;" class="cgst" id="cgst_'.$check->id.'" placeholder="CGST" name="cgst[]" data-id="'.$check->id.'" readonly><br>
            <input type="number" style="width: 100% !important;" readonly class="cgst_amt" id="cgst_amt_'.$check->id.'" placeholder="CGST Amt" name="cgst_amt[]" data-id="'.$check->id.'">
            </td>
            <td style="font-size: 15px !important;" class="sgstdiv" data-id="'.$check->id.'" id="sgstdiv_'.$check->id.'">
            <input type="number" style="width: 100% !important;" class="sgst" id="sgst_'.$check->id.'" placeholder="SGST" name="sgst[]" data-id="'.$check->id.'" readonly><br>
            <input type="number" style="width: 100% !important;" readonly class="sgst_amt" id="sgst_amt_'.$check->id.'" placeholder="SGST Amt" name="sgst_amt[]" data-id="'.$check->id.'">
            </td>
            <td style="font-size: 15px !important;" class="igstdiv" data-id="'.$check->id.'" id="igstdiv_'.$check->id.'">
            <input type="number" style="width: 100% !important;" class="igst" id="igst_'.$check->id.'" placeholder="IGST" name="igst[]" data-id="'.$check->id.'" readonly><br>
            <input type="number" style="width: 100% !important;" readonly class="igst_amt" id="igst_amt_'.$check->id.'" placeholder="IGST Amt" name="igst_amt[]" data-id="'.$check->id.'">
            </td>
            <td style="font-size: 15px !important;">
            <input type="number" style="width: 100% !important;" class="amount" id="amount_'.$check->id.'"  name="amount[]" data-id="'.$check->id.'" readonly  >
            </td></tr>
            ';
            return response()->json(['status'=>'success','data'=>$data]);
        }
        else
        {
            return response()->json(['status'=>'warehouse']);
        }
    }

    public function get_vendor_data(Request $request)
    {
        $data = Vendor::where('id',$request->id)->first();

        if($data->state_id)
        {
            return response()->json(['status' => 'success', 'data' => $data]);
        }
        else
        {
            return response()->json(['status' => 'state_error']);
        }
    }
}