<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Debit;
use App\Company;
use App\User;
use App\Vendor;
use App\DebitChild;
use App\Product;
use App\Client;
use App\Employee;
use App\ProductCategory;
use App\Warehouse;
use App\GST;
use DataTables;
use Auth;
use App\PurchaseInvoice;
use App\PurchaseInvoiceChild;
use App\WarehouseInventory;

class DebitController extends Controller
{
    public function __construct(Debit $s)
    {
        $this->view = 'debit';
        $this->route = 'debit';
        $this->viewName = 'Debit';
    }

    public function index(Request $request)
    {
        if ($request->ajax())
        {
            $query = Debit::orderBy('id','DESC');
            if(Auth::guard('user')->check())
            {
                if(Auth::user()->role == 2)
                {
                    $query = $query->where('branch_id',Auth::user()->id)->latest();
                }
                else
                {
                    $query = $query->latest();
                }
            }

            if(Auth::guard('employee')->check())
            {
                $query = $query->where('branch_id',Auth::user()->branch_id)->latest();
            }

            // if($request->branch_id)
            // {
            //     $query = $query->where('branch_id',$request->branch_id);
            // }
             if (
                Auth::guard('user')->check() &&
                Auth::user()->role == 1 &&
                $request->filled('branch_id')
            ) {
                $query->where('branch_id', $request->branch_id);
            }
            return Datatables::of($query)
            ->addColumn('action', function ($row) {
                $btn = '';
                if($row->status == 0)
                {
                    $btn .= '<a style="background: transparent;" href="'.route('user.debit.edit',$row->id).'" title="Edit details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: green;" class="la la-edit"></i>
                    </a>&nbsp;&nbsp;&nbsp;';
                }
                if(Auth::guard('user')->check())
                {
                    if(Auth::user()->role == 1)
                    {
                        $btn .= '<a href="'.route('user.deletes.debit',$row->id).'" onclick="return confirm(`Are you sure?`)" style="background: white;" title="Delete details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: red;" class="la la-trash"></i></a>&nbsp;&nbsp;&nbsp;';
                    }
                }
                $btn .= '<a style="background: transparent;" href="'.route('user.debit.show',$row->id).'" title="View details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="la la-eye"></i>
                </a>&nbsp;&nbsp;&nbsp;';
                return $btn;
            })

            ->addColumn('singlecheckbox', function ($row) {
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

            ->addColumn('branch_id', function ($row) {
                if($row->branch_id)
                {
                    $btn = User::where('id',$row->branch_id)->value('name');
                }
                else
                {
                    $btn = '';
                }
                return $btn;
            })

            ->addColumn('date', function ($row) {
                if($row->date)
                {
                    $btn =date('d-m-Y', strtotime($row->date));
                }
                else
                {
                    $btn = '';
                }
                return $btn;
            })

            ->editColumn('vendor_id', function ($row) {
                if($row->vendor_id)
                {
                    $btn = Vendor::where('id',$row->vendor_id)->value('name');
                }
                else
                {
                    $btn = '';
                }
                return $btn;
            })
            ->editColumn('invoice_id', function ($row) {
                if($row->vendor_id)
                {
                    $btn = PurchaseInvoice::where('id',$row->invoice_id)->value('invoice_no');
                }
                else
                {
                    $btn = '';
                }
                return $btn;
            })
            ->rawColumns(['action','singlecheckbox','branch_id','vendor_id','invoice_id'])
            ->make(true);
        }
        $branch = User::where('role',2)->get();

        return view('debit.index',compact('branch'));
    }

    public function create()
    {
        $data['title'] = 'Add ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['branch'] = User::where('role',2)->where('status',1)->get();

        return view('general.add_form')->with($data);
    }

    public function status(Request $request)
    {
        $change_status = Debit::where('id',$request->id)->first();
        $purchaseinvoicechild = DebitChild::where('debit_id',$request->id)->get();
        if(count($purchaseinvoicechild))
        {
            foreach ($purchaseinvoicechild as $key => $value)
            {
                $invoice_child = PurchaseInvoiceChild::where('id',$value->invoice_child_id)->first();
                if(isset($invoice_child))
                {
                    if(((float)$invoice_child->pending_return_qty - (float)$value->qty) < 0)
                    {
                        return response()->json(['status' => 'required', 'message' => 'Cannot add Qty greater than Invoice Qty']);
                    }
                    $inventory = WarehouseInventory::where('branch_id',$change_status->branch_id)->where('product_id',$value->product_id)->where('warehouse_id',$value->warehouse_id)->first();
                    
                    if(((float)$inventory->stock - (float)$value->qty) < 0)
                    {
                        return response()->json(['status' => 'required', 'message' => 'Stock not Available for some products']);
                    }
                }
            }
        }


        $param['status'] = $request->status;
        if($param['status'] == 1)
        {
            $debitchild = DebitChild::where('debit_id',$request->id)->get();
            if(count($debitchild))
            {
                foreach ($debitchild as $key => $value)
                {
                    $invoice_child = PurchaseInvoiceChild::where('id',$value->invoice_child_id)->first();
                    $invoice_child->pending_return_qty = (float)$invoice_child->pending_return_qty - (float)$value->qty;
                    $invoice_child->save();

                    $inventory = WarehouseInventory::where('branch_id',$change_status->branch_id)->where('product_id',$value->product_id)->where('warehouse_id',$value->warehouse_id)->first();
                    $inventory->stock = (float)$inventory->stock - (float)$value->qty;
                    $inventory->save();
                }
            }
        }
        $change_status->update($param);
        $change_status->save();

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
                $invoice_child = PurchaseInvoiceChild::where('id',$request->invoice_child_id[$key])->first();
                if(!($invoice_child->pending_return_qty >= $value))
                {
                    return response()->json(['status' => 'required', 'message' => 'Qty is greater than Invoice Qty!!']);
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

        $debit = new Debit();
        $debit->vendor_id = $request->vendor_id;
        $debit->invoice_id = $request->invoice_id;
        $debit->date = $request->date;
        $debit->order_no = $request->order_no;

        $debit->remark = $request->remark;
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $debit->branch_id = $request->branch_id;
            }
            else
            {
                $debit->branch_id = Auth::user()->id;
            }
        }
        else
        {
            $debit->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
        }
        $debit->total_qty = $request->total_qty;
        $debit->total_discount = $request->total_discount;
        $debit->total_gst = $request->total_gst;
        $debit->total_amount = $request->final_amount;
        $debit->save();

        if($request->new_product_id)
        {
            foreach ($request->new_product_id as $key => $value) {
                if($value != '')
                {
                    $product = Product::where('id',$value)->first();
                    $child = new DebitChild();
                    $child->debit_id = $debit->id;
                    $child->invoice_child_id = $request->invoice_child_id[$key];
                    $child->product_id = $product->id;
                    $child->category_id = $product->productcategory_id;
                    $child->company_id = $product->company_id;
                    $child->warehouse_id = $request->warehouse_id[$key];
                    $child->qty = $request->qty[$key];
                    $child->rate = $request->rate[$key];
                    $child->discount = $request->discount[$key];
                    $child->discount_amount = $request->discount_amount[$key];
                    $child->gst = $request->gst[$key];
                    $child->gst_amt = $request->gst_amt[$key];
                    $child->amount = $request->amount[$key];
                    $child->save();
                }
            }
        }
        if ($debit){
            return response()->json(['status'=>'success']);
        }else{
            return response()->json(['status'=>'error']);
        }
    }

    public function show($id)
    {
        $data['data'] = Debit::where('id',$id)->first();
        $data['child'] = DebitChild::where('debit_id',$id)->get();

        return view('debit.view')->with($data);
    }

    public function edit($id)
    {
        $data['title'] = 'Edit ' . $this->viewName;
        $data['data'] = Debit::where('id', $id)->first();
        $data['child'] = DebitChild::where('debit_id', $id)->get();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        $data['warehouses'] = Warehouse::where('branch_id',$data['data']->branch_id)->get();
        
        return view('general.edit_form')->with($data);
    }

    public function update(Request $request, $id)
    {
        if($request->edit_warehouse_id)
        {
            foreach ($request->edit_warehouse_id as $key => $value) {
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
        if($request->edit_gst)
        {
            foreach ($request->edit_gst as $key => $value) {
                if($value == '')
                {
                    return response()->json(['status' => 'required', 'message' => 'GST is required!!']);
                }
            }
        }

        $debit = Debit::where('id',$id)->first();
        $debit->date = $request->date;
        $debit->order_no = $request->order_no;
        $debit->remark = $request->remark;
        $debit->total_qty = $request->total_qty;
        $debit->total_discount = $request->total_discount;
        $debit->total_gst = $request->total_gst;
        $debit->total_amount = $request->final_amount;
        $debit->save();

        if($request->edit_qty)
        {
            foreach ($request->edit_qty as $keys => $values) {
                {
                    if($values != '')
                    {
                        $debit_child['debit_id'] = $id;
                        $debit_child['qty'] = $values;
                        $debit_child['warehouse_id'] = $request->edit_warehouse_id[$key];
                        $debit_child['rate'] = $request->edit_rate[$key];
                        $debit_child['discount'] = $request->edit_discount[$key];
                        $debit_child['discount_amount'] = $request->edit_discount_amount[$key];
                        $debit_child['gst'] = $request->edit_gst[$key];
                        $debit_child['gst_amt'] = $request->edit_gst_amt[$key];
                        $debit_child['amount'] = $request->edit_amount[$keys];
                        $debit_child1 = DebitChild::where('id',$request->edit_main_id[$keys])->update($debit_child);
                    }
                }
            }
        }

        if ($debit){
            return response()->json(['status'=>'success']);
        }else{
            return response()->json(['status'=>'error']);
        }
    }

    public function getclient(Request $request)
    {
        $data = Client::where('branch_id',$request->branch_id)->get();
        $html = "<option value=''>Select</option>";

        foreach ($data as $key => $value)
        {
            $html .= "<option value=".$value->id.">".$value->member."</option>";
        }

        return response()->json(['data'=>$html]);
    }

    public function childdelete($id)
    {
        $basicchild = DebitChild::where('id',$id)->first();
        $basic = Debit::where('id',$basicchild->debit_id)->first();
        if($basic)
        {
            $basic->total_qty = round(($basic->total_qty - $basicchild->qty),2);
            $basic->total_discount = round(($basic->total_discount - $basicchild->discount_amount),2);
            $basic->total_gst = round(($basic->total_gst - $basicchild->gst_amt),2);
            $basic->total_amount = round(($basic->total_amount - $basicchild->amount),2);
            $basic->save();
        }
        DebitChild::where('id',$id)->delete();
        
        return response()->json(['status' => 'success']);
    }

    public function delete($id)
    {
        $debit = Debit::where('id',$id)->first();
        $debit_child = DebitChild::where('debit_id',$id)->get();
        
        if(count($debit_child))
        {
            foreach ($debit_child as $key => $value)
            {
                $invoice_child = PurchaseInvoiceChild::where('id',$value->invoice_child_id)->first();
                if(isset($invoice_child))
                {
                    $invoice_child->pending_return_qty = (float)$invoice_child->pending_return_qty + (float)$value->qty;
                    $invoice_child->save();

                    $inventory = WarehouseInventory::where('branch_id',$debit->branch_id)->where('product_id',$value->product_id)->where('warehouse_id',$value->warehouse_id)->first();
                    $inventory->stock = (float)$inventory->stock + (float)$value->qty;
                    $inventory->save();
                }
            }
        }
        Debit::where('id',$id)->delete();
        DebitChild::where('debit_id',$id)->delete();
        return redirect()->back();
    }
    public function get_vendor_data(Request $request)
    {
        $vendor_id = $request->id;
        $data = Vendor::where('id',$request->id)->first();
        if(!$data->state_id)
        {
            return response()->json(['status' => 'state_error']);
        }
        $invoice = PurchaseInvoice::where('vendor_id',$request->id)
        ->whereHas('child', function($q) {
            $q->where('pending_return_qty','>',0);
        })->get();

        $html = '<option value="">Select</option>';
        if(count($invoice))
        {
            foreach ($invoice as $key => $value)
            {
                $html .= '<option value="'.$value->id.'">'.$value->invoice_no.' / '. date('d-m-Y',strtotime($value->date)).' </option>';
            }
        }
        
        return response()->json(['status' => 'success', 'data' => $data, 'html' => $html ]);
    }
    public function get_vendor_invoicedata(Request $request)
    {
        $invoice_child = PurchaseInvoiceChild::where('invoice_id',$request->id)->where('pending_return_qty','>',0)->get();
        $html = '<option value="">Select</option>';
        if(count($invoice_child))
        {
            foreach ($invoice_child as $key => $value)
            {
                $html .= '<option value="'.$value->id.'">'. $value->product->code .' </option>';
            }
        }

        return response()->json(['status' => 'success', 'html' => $html ]);
    }
    public function get_vendor_invoicechilddata(Request $request)
    {
        $check = PurchaseInvoiceChild::where('id',$request->id)->first();
        $product = Product::where('id',$check->product_id)->first();
        $data = '';
        if(isset($product))
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
            <input type="hidden" name="invoice_child_id[]" class="invoice_child_id" id="invoice_child_id_'.$check->id.'" data-id="'.$check->id.'" value="'.$check->id.'">
            <input type="hidden" name="new_product_id[]" value="'.$check->id.'" class="new_product_id" id="new_product_id_'.$check->id.'" data-id ="'.$check->id.'">
            <input type="hidden" readonly class="form-control product_name" name="product_name[]" id="product_name_'.$check->id.'" placeholder="Enter Name" data-id="'.$check->id.'" value="'.$product->product.'" >
            <input type="hidden" class="form-control product_code" value="'.$product->code.'" name="product_code[]" id="product_code_'.$check->id.'" placeholder="Enter Price" data-id="'.$check->id.'" readonly  >
            '.$product->product.' <br> '.$product->code.'
            </td>
            <td style="font-size: 15px !important;">
            <select style="width: 100% !important;" class="warehouse_id" name="warehouse_id[]" id="warehouse_id_'.$check->id.'" data-id="'.$check->id.'" >
            <option value="">Select</option>';
            foreach ($warehouse as $key => $value)
            {
                $data.= "<option value=".$value->id." >".$value->name."</option>";
            }
            $data .= '</select>
            </td>
            <td style="font-size: 15px !important;">
            <input type="number" style="width: 100% !important;" class="pending_return_qty" id="pending_return_qty_'.$check->id.'" data-id="'.$check->id.'" value="'.$check->pending_return_qty.'" readonly>
            <input type="number" style="width: 100% !important;" class="qty" id="qty_'.$check->id.'" placeholder="Enter Qty" name="qty[]" data-id="'.$check->id.'">
            </td>
            <td style="font-size: 15px !important;">
            <input type="number" style="width: 100% !important;" class="rate" id="rate_'.$check->id.'" placeholder="Enter Rate" value="'.round($check->rate,2).'"  name="rate[]" data-id="'.$check->id.'"  >
            </td>
            <td style="font-size: 15px !important;">
            <input type="number" style="width: 100% !important;" class="discount" id="discount_'.$check->id.'" placeholder="Enter Discount(%)" name="discount[]" data-id="'.$check->id.'" readonly><br>
            <input type="number" style="width: 100% !important;" class="discount_amount" id="discount_amount_'.$check->id.'" placeholder="Enter Discount Amount" name="discount_amount[]" data-id="'.$check->id.'" value="'.$check->discount_amount.'" >
            </td>
            <td style="font-size: 15px !important;">
            <select style="width: 100% !important;" class="gst" name="gst[]" id="gst_'.$check->id.'" data-id="'.$check->id.'" >
            <option value="">Select</option>';
            foreach ($gst as $key1 => $value1)
            {
                $selected = '';
                if($value1->id == $check->gst)
                {
                    $selected = 'selected';
                }
                $data.= "<option value=".$value1->id." ".$selected." >".$value1->gst."</option>";
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
        }else{
            return response()->json(['status'=>'error','data'=>$data]);
        }
    }
    public function debit_getdata(Request $request)
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
            <input type="number" style="width: 100% !important;" class="discount_amount" id="discount_amount_'.$check->id.'" placeholder="Enter Discount Amount" name="discount_amount[]" data-id="'.$check->id.'" readonly>
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
}