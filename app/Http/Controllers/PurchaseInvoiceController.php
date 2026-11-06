<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;
use App\Section;
use App\SalesOrder;
use App\SalesOrderChild;
use App\Company;
use App\WarehouseInventory;
use App\Employee;
use App\Warehouse;
use App\Vendor;
use App\Product;
use App\Client;
use App\PurchaseInvoice;
use App\PurchaseInvoiceChild;
use App\ProductCategory;
use App\GST;
use App\PurchaseOrder;
use App\PurchaseOrderChild;
use App\ItemLedger;
use App\Debit;
use App\DebitChild;
use DataTables;
use Auth;

class PurchaseInvoiceController extends Controller
{
    public function __construct(PurchaseInvoice $s)
    {
        $this->view = 'purchaseinvoice';
        $this->route = 'purchaseinvoice';
        $this->viewName = 'PurchaseInvoice';
    } 

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = PurchaseInvoice::orderBy('date','DESC');
            if(Auth::guard('user')->check())
            {
                if(Auth::user()->role == 2)
                {
                    $query->where('branch_id',Auth::user()->id);
                }
            }
            if(Auth::guard('employee')->check())
            {
                $query->where('branch_id',Auth::user()->branch_id);
            }
            // Branch Filter for Super Admin
            if (Auth::guard('user')->check() &&
                Auth::user()->role == 1 &&
                $request->filled('branch_id')) {

                $query->where('branch_id', $request->branch_id);
            }
            $query =  $query->latest();
            return Datatables::of($query)
            ->addColumn('action', function ($row) {
                $btn = '';
                $btn .= '<a href="'.route('user.purchaseinvoice.edit', $row->id).'" class="btn btn-sm btn-clean btn-icon btn-icon-md" style="background: white;"  title="Edit">  <i style="color: green;" class="la la-edit">
                </i></a>&nbsp;&nbsp;'; 
                if(Auth::guard('user')->check())
                {
                    if(Auth::user()->role == 1)
                    {
                        $child = PurchaseInvoiceChild::where('invoice_id',$row->id)->pluck('id')->toArray();
                        $check = DebitChild::where('invoice_child_id',$child)->first();
                        if(!isset($check))
                        {
                            $btn .= '<button style="background: white;" title="Delete" data-id="'.$row->id.'" class="btn btn-sm btn-clean btn-icon btn-icon-md delete-record">
                            <i style="color: red;" class="la la-trash">
                            </i>
                            </button>&nbsp;&nbsp;';
                        }
                    }
                }
                $btn .= '<a href="'.route('user.purchaseinvoice.view', $row->id).'" class="btn btn-sm btn-clean btn-icon btn-icon-md" style="background: white;"  title="View">  <i style="color: black;" class="la la-eye">
                </i></a>&nbsp;&nbsp;';       
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
            ->addColumn('purchase_order_no', function ($row) {
                if($row->purchase_id)
                {
                    $btn = PurchaseOrder::where('id',$row->purchase_id)->value('order_no');
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
                if($row->status == 2 )
                {
                    $btn = '<select class="form-control" disabled><option value="1"';
                }
                else
                {
                    $btn = '<select class="form-control" onchange="status(this,'.$row->id.')"><option value="1"';
                }
                $selected = '';
                if($row->status == 1) 
                {
                    $selected = 'selected';
                }
                $btn .= $selected.'>Pending</option><option value="2" ';
                $selected = '';
                if($row->status == 2)
                {
                    $selected = 'selected';
                }
                $btn .= $selected.'>Completed</option>
                </select>';
                return $btn;
            })
            ->rawColumns(['numbers','action','singlecheckbox','status','name','date','vendor_id','branch_id','purchase_order_no'])
            ->make(true);
        } 

        $data['vendor'] = Vendor::get();
        $data['branch'] = User::where('role',2)->get();
        return view('purchaseinvoice.index')->with($data);
    }

    public function create()
    {
        $data['title'] = 'Add ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['branch'] = User::where('role',2)->get();
        
        return view('purchaseinvoice.create')->with($data);
    }

    public function status(Request $request)
    {
        $change_status = PurchaseInvoice::where('id',$request->id)->first();
        $param['status'] = $request->status;
        if($param['status'] == 2)
        {
            $purchaseinvoicechild = PurchaseInvoiceChild::where('invoice_id',$request->id)->get();
            foreach ($purchaseinvoicechild as $key => $value) {
                $inventory = WarehouseInventory::where('branch_id',$change_status->branch_id)->where('product_id',$value->product_id)->where('warehouse_id',$value->warehouse_id)->first();
                $inventory->stock = $inventory->stock + $value->qty;
                $inventory->save();

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

        $purchaseinvoice = new PurchaseInvoice();
        $purchaseinvoice->invoice_no = $request->invoice_no;
        $purchaseinvoice->order_no = $request->order_no;
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $purchaseinvoice->branch_id = $request->branch_id;
                $id = PurchaseInvoice::where('branch_id',$request->branch_id)->latest()->first();
                $branch = User::where('id',$request->branch_id)->value('prefix');
                if($id)
                {
                    if($id->ids)
                    {
                        $no = explode('-', $id->ids);
                        $order =  $no[1] + 1;
                        $order_record = $branch .'-'. $order;
                    }
                    else
                    {
                        $order_record = $branch .'-'. 1;

                    }
                }else
                {
                    $order_record = $branch .'-'. 1;
                }
                $purchaseinvoice->ids = $order_record;

            }
            else
            {
                $purchaseinvoice->branch_id = Auth::user()->id;
                $id = PurchaseInvoice::where('branch_id',Auth::user()->id)->latest()->first();
                $branch = User::where('id',Auth::user()->id)->value('prefix');
                if($id)
                {
                    if($id->ids)
                    {
                        $no = explode('-', $id->ids);
                        $order =  $no[1] + 1;
                        $order_record = $branch .'-'. $order;
                    }
                    else
                    {
                        $order_record = $branch .'-'. 1;

                    }
                }else
                {
                    $order_record = $branch .'-'. 1;
                }
                $purchaseinvoice->ids = $order_record;
            }
        }

        if(Auth::guard('employee')->check())
        {
            $purchaseinvoice->branch_id = Auth::user()->branch_id;
            $id = PurchaseInvoice::where('branch_id',Auth::user()->branch_id)->latest()->first();
            $branch = User::where('id',Auth::user()->branch_id)->value('prefix');
            if($id)
            {
                if($id->ids)
                {
                    $no = explode('-', $id->ids);
                    $order =  $no[1] + 1;
                    $order_record = $branch .'-'. $order;
                }
                else
                {
                    $order_record = $branch .'-'. 1;
                }
            }else
            {
                $order_record = $branch .'-'. 1;
            }
            $purchaseinvoice->ids = $order_record;
        }

        $purchaseinvoice->vendor_id = $request->vendor_id;
        $purchaseinvoice->date = $request->date;
        $purchaseinvoice->total_qty = $request->total_qty;
        $purchaseinvoice->total_discount = $request->total_discount;
        $purchaseinvoice->total_gst = $request->total_gst;
        $purchaseinvoice->total_amount = $request->final_amount;
        $purchaseinvoice->flat_discount = $request->flat_discount;
        $purchaseinvoice->labour_charges = $request->labour_charges;
        $purchaseinvoice->transport_charges = $request->transport_charges;
        $purchaseinvoice->grand_total = $request->grand_total;
        $purchaseinvoice->remark = $request->remark;
        $purchaseinvoice->save();

        $static_var = '0.00';
        if($request->new_product_id)
        {
            foreach ($request->new_product_id as $key => $value) {
                if($value != '')
                {
                    $product = Product::where('id',$value)->first();
                    $child = new PurchaseInvoiceChild();
                    $child->invoice_id = $purchaseinvoice->id;
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
                    $child->pending_return_qty = $request->qty[$key];
                    $child->amount = $request->amount[$key];
                    $child->save();

                    $inventory = WarehouseInventory::where('branch_id',$purchaseinvoice->branch_id)->where('product_id',$value)->where('warehouse_id',$request->warehouse_id[$key])->first();
                    if(isset($inventory)){
                        $inventory->stock = $inventory->stock + $request->qty[$key];
                        $inventory->save();
                    }
                    else
                    {
                        $inventory_new = new WarehouseInventory();
                        $inventory_new->branch_id = $purchaseinvoice->branch_id;
                        $inventory_new->warehouse_id = $request->warehouse_id[$key];
                        $inventory_new->product_id = $value;
                        $inventory_new->stock = $request->qty[$key];
                        $inventory_new->used = 0;
                        $inventory_new->actual_stock = 0;
                        $inventory_new->save();
                    }

                    $item_ledger = new ItemLedger();
                    $item_ledger->date = $purchaseinvoice->date;
                    $item_ledger->tran_type = 'Purchase Invoice';
                    $item_ledger->vendor_id = $purchaseinvoice->vendor_id;
                    $item_ledger->branch_id = $purchaseinvoice->branch_id;
                    $item_ledger->warehouse_id = $child->warehouse_id;
                    $item_ledger->category_id = $child->category_id;
                    $item_ledger->company_id = $child->company_id;
                    $item_ledger->product_id = $child->product_id;
                    $item_ledger->product_code = $product->code;
                    $item_ledger->invoice_no = $purchaseinvoice->invoice_no;
                    $item_ledger->purchaseinvoice_id = $purchaseinvoice->id;
                    $item_ledger->purchaseinvoice_child_id = $child->id;
                    $item_ledger->inward = (string)number_format((float)$child->qty,2,'.','') ?? (string)$static_var;
                    $item_ledger->save();
                }
            }
        }
        if($purchaseinvoice)
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
        $data['data'] = PurchaseInvoice::where('id', $id)->first();
        $data['child'] = PurchaseInvoiceChild::where('invoice_id',$id)->get();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['branch'] = User::where('role',2)->get();
        
        return view('purchaseinvoice.edit')->with($data);
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

        $purchaseinvoice = PurchaseInvoice::where('id',$id)->first();
        $purchaseinvoice->invoice_no = $request->invoice_no;
        $purchaseinvoice->order_no = $request->order_no;
        $purchaseinvoice->date = $request->date;
        $purchaseinvoice->total_qty = $request->total_qty;
        $purchaseinvoice->total_discount = $request->total_discount;
        $purchaseinvoice->total_gst = $request->total_gst;
        $purchaseinvoice->total_amount = $request->final_amount;
        $purchaseinvoice->flat_discount = $request->flat_discount;
        $purchaseinvoice->labour_charges = $request->labour_charges;
        $purchaseinvoice->transport_charges = $request->transport_charges;
        $purchaseinvoice->grand_total = $request->grand_total;
        $purchaseinvoice->remark = $request->remark;
        $purchaseinvoice->save();

        $static_var = '0.00';
        if($request->new_product_id)
        {
            foreach ($request->new_product_id as $key => $value) {
                if($value != '')
                {
                    $product = Product::where('id',$value)->first();
                    $child = new PurchaseInvoiceChild();
                    $child->invoice_id = $id;
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
                    $child->pending_return_qty = $request->qty[$key];
                    $child->amount = $request->amount[$key];
                    $child->save();

                    $inventory = WarehouseInventory::where('branch_id',$purchaseinvoice->branch_id)->where('product_id',$value)->where('warehouse_id',$request->warehouse_id[$key])->first();
                    if(isset($inventory)){
                        $inventory->stock = $inventory->stock + $request->qty[$key];
                        $inventory->save();
                    }
                    else
                    {
                        $inventory_new = new WarehouseInventory();
                        $inventory_new->branch_id = $purchaseinvoice->branch_id;
                        $inventory_new->warehouse_id = $request->warehouse_id[$key];
                        $inventory_new->product_id = $value;
                        $inventory_new->stock = $request->qty[$key];
                        $inventory_new->used = 0;
                        $inventory_new->actual_stock = 0;
                        $inventory_new->save();
                    }

                    $item_ledger = new ItemLedger();
                    $item_ledger->date = $purchaseinvoice->date;
                    $item_ledger->tran_type = 'Purchase Invoice';
                    $item_ledger->vendor_id = $purchaseinvoice->vendor_id;
                    $item_ledger->branch_id = $purchaseinvoice->branch_id;
                    $item_ledger->warehouse_id = $child->warehouse_id;
                    $item_ledger->category_id = $child->category_id;
                    $item_ledger->company_id = $child->company_id;
                    $item_ledger->product_id = $child->product_id;
                    $item_ledger->product_code = $product->code;
                    $item_ledger->invoice_no = $purchaseinvoice->invoice_no;
                    $item_ledger->purchaseinvoice_id = $purchaseinvoice->id;
                    $item_ledger->purchaseinvoice_child_id = $child->id;
                    $item_ledger->inward = (string)number_format((float)$child->qty,2,'.','') ?? (string)$static_var;
                    $item_ledger->save();
                }
            }
        }

        if($request->edit_qty)
        {
            foreach ($request->edit_qty as $key => $value) {
                {
                    if($value != '')
                    {
                        $purchaseorderchild1 = PurchaseInvoiceChild::where('id',$request->edit_main_id[$key])->first();
                        $product = Product::where('id',$purchaseorderchild1->product_id)->first();
                        
                        $purchaseorderchild['invoice_id'] = $id;
                        $purchaseorderchild['qty'] = $value;
                        $purchaseorderchild['rate'] = $request->edit_rate[$key];
                        $purchaseorderchild['discount'] = $request->edit_discount[$key];
                        $purchaseorderchild['discount_amount'] = $request->edit_discount_amount[$key];
                        $purchaseorderchild['gst'] = $request->edit_gst[$key];
                        $purchaseorderchild['gst_amt'] = $request->edit_gst_amt[$key];
                        $purchaseorderchild['amount'] = $request->edit_amount[$key];

                        $inventory = WarehouseInventory::where('product_id',$request->edit_product_name[$key])->where('warehouse_id',$request->edit_warehouse_id[$key])->first();
                        if(isset($inventory)){
                            $inventory->stock = $inventory->stock - $purchaseorderchild1->qty + $value;
                            $inventory->save();
                        }
                        else
                        {
                            $inventory_new = new WarehouseInventory();
                            $inventory_new->branch_id = $purchaseinvoice->branch_id;
                            $inventory_new->warehouse_id = $request->edit_warehouse_id[$key];
                            $inventory_new->product_id = $request->edit_product_name[$key];
                            $inventory_new->stock = $value;
                            $inventory_new->used = 0;
                            $inventory_new->actual_stock = 0;
                            $inventory_new->save();
                        }

                        $purchaseorderchild1->update($purchaseorderchild);

                        $item_ledger = ItemLedger::where('purchaseinvoice_id',$purchaseinvoice->id)->where('purchaseinvoice_child_id',$purchaseorderchild1->id)->first();
                        if(isset($item_ledger))
                        {
                            $item_ledger->date = $purchaseinvoice->date;
                            $item_ledger->invoice_no = $purchaseinvoice->invoice_no;
                            $item_ledger->inward = (string)number_format((float)$value,2,'.','') ?? (string)$static_var;
                            $item_ledger->save();
                        }
                        else
                        {
                            $item_ledger_new = new ItemLedger();
                            $item_ledger_new->date = $purchaseinvoice->date;
                            $item_ledger_new->tran_type = 'Purchase Invoice';
                            $item_ledger_new->vendor_id = $purchaseinvoice->vendor_id;
                            $item_ledger_new->branch_id = $purchaseinvoice->branch_id;
                            $item_ledger_new->warehouse_id = $purchaseorderchild1->warehouse_id;
                            $item_ledger_new->category_id = $purchaseorderchild1->category_id;
                            $item_ledger_new->company_id = $purchaseorderchild1->company_id;
                            $item_ledger_new->product_id = $purchaseorderchild1->product_id;
                            $item_ledger_new->product_code = $product->code;
                            $item_ledger_new->invoice_no = $purchaseinvoice->invoice_no;
                            $item_ledger_new->purchaseinvoice_id = $purchaseinvoice->id;
                            $item_ledger_new->purchaseinvoice_child_id = $purchaseorderchild1->id;
                            $item_ledger_new->inward = (string)number_format((float)$value,2,'.','') ?? (string)$static_var;
                            $item_ledger_new->save();
                        }
                    }
                }
            }
        }
        if($purchaseinvoice)
        {
            return response()->json(['status' => 'success']);
        }else
        {
            return response()->json(['status' => 'error']);
        }
    }

    public function childdelete($id)
    {
        $data = PurchaseInvoiceChild::where('id',$id)->first();

        if(isset($data))
        {
            $basic = PurchaseInvoice::where('id',$data->invoice_id)->first();
            $inventory = WarehouseInventory::where('branch_id',$basic->branch_id)->where('product_id',$data->product_id)->where('warehouse_id',$data->warehouse_id)->first();
            if(isset($inventory) && isset($basic))
            {
                $inventory->stock = $inventory->stock - $data->qty;
                $inventory->save();

                $basic->total_qty = round(($basic->total_qty - $data->qty),2);
                $basic->total_discount = round(($basic->total_discount - $data->discount_amount),2);
                $basic->total_gst = round(($basic->total_gst - $data->gst_amt),2);
                $basic->total_amount = round(($basic->total_amount - $data->amount),2);
                $basic->grand_total = round(($basic->grand_total - $data->amount),2);
                $basic->save();
            }
            $data->delete();
        }

        ItemLedger::where('purchaseinvoice_child_id',$data->id)->where('purchaseinvoice_id',$data->invoice_id)->delete();

        if($data)
        {
            return response()->json(['status' => 'success'  ]);
        }else
        {
            return response()->json(['status' => 'error']);
        }
    }

    public function show($id)
    {
        $data['id'] = $id;
        $data['data'] = PurchaseInvoice::where('id', $id)->first();
        $data['child'] = PurchaseInvoiceChild::where('invoice_id',$id)->get();
        $data['module'] = $this->viewName;

        return view('purchaseinvoice.view')->with($data);
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

    public function getpo(Request $request)
    {
        $po = PurchaseOrder::where('id',$request->id)->first();
        $child = PurchaseOrderChild::where('purchase_order_id',$po->id)->get();
        $vdr = Vendor::where('id',$po->vendor_id)->first();
        $data['branch'] = User::where('id',$po->branch_id)->value('name');
        $data['vendor'] = Vendor::where('id',$po->vendor_id)->value('name');
        $data['data'] = '  <table cellspacing="0" border="0" class="table table-bordered">
        <thead>
        <tr>

        <th>Category</th>
        <th>Company</th>
        <th>Product</th>
        <th>Warehouse</th>
        <th>Actual Qty</th>
        <th>Pending Qty</th>
        <th>Qty</th>
        </tr>
        </thead>
        <tbody>';
        if(!empty($child)){
            foreach($child as $key => $value){
                $category = ProductCategory::where('id',$value->category_id)->value('name'); 
                $company = Company::where('id',$value->company_id)->value('name'); 
                $product = Product::where('id',$value->product_id)->value('product'); 
                $warehouse = Warehouse::where('id',$value->warehouse_id)->value('name'); 
                $data['data'] .= ' <tr class="master_clone" data-id="0" data-master="0">
                <td>
                <input type="hidden" name="purchase_child_id[]" value="'.$value->id.'">
                <input type="hidden" name="category_id[]" value="'.$value->category_id.'">
                <input type="text" class="form-control" value="'.$category.'" readonly>
                </td>
                <td>
                <input type="hidden" name="company_id[]" value="'.$value->company_id.'">
                <input type="text" class="form-control" value="'.$company.'" readonly>
                </td>
                <td>
                <input type="hidden" name="product_id[]" value="'.$value->product_id.'">
                <input type="text" class="form-control" value="'.$product.'" readonly>
                </td>
                <td>
                <input type="hidden" name="warehouse_id[]" value="'.$value->warehouse_id.'">

                <input type="text" class="form-control" value="'.$warehouse.'" readonly>
                </td>
                <td>
                <input type="number" name="qty[]" value="'.$value->qty.'" class="form-control" readonly>
                </td>
                <td>
                <input type="number" name="pending_qty[]" value="'.$value->pending_qty.'" class="form-control" readonly>
                </td>
                <td>
                <input type="number" placeholder="Enter Qty" name="new_qty[]"  class="form-control">
                </td>
                </tr>';
            }
            $data['data'] .= ' </tbody></table>';
        }
        return response()->json($data);
    }

    public function pi_getdata(Request $request)
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

    public function search(Request $request)
    {
        $search = $request->get('q');
        $company_id = $request->get('company_id');

        $query = Product::query();

        if ($company_id) {
            $query->where('company_id', $company_id);
        }

        if ($search) {
            $query->where('code', 'like', '%' . $search . '%');
        }

        $items = $query->select('id', 'code')->limit(20)->get();

        return response()->json([
            'items' => $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'text' => $item->code,
                ];
            })
        ]);
    }

    public function purchaseinvoice_report()
    {
        $data['branch'] = User::where('role',2)->get();
        return view('purchaseinvoice.report')->with($data);
    }

    public function get_report_data(Request $request)
    {
        if($request->vendor_id)
        {
            $data = PurchaseInvoice::where('vendor_id',$request->vendor_id)->get();
        }
        $html = '';
        if(count($data) > 0)
        {
            foreach ($data as $key => $value) {
                $child = PurchaseInvoiceChild::where('invoice_id',$value->id)->get();
                $html .= '<table class="table table-striped- table-bordered table-hover table-checkable datatable">
                <tr>
                <td style="border: 1.5px solid black;">Vendor</td>
                <td style="border: 1.5px solid black;">Order No.</td>
                <td style="border: 1.5px solid black;">Invoice No.</td>
                <td style="border: 1.5px solid black;">Date</td>
                <td style="border: 1.5px solid black;">Category</td>
                <td style="border: 1.5px solid black;">Company</td>
                <td style="border: 1.5px solid black;">Product</td>
                <td style="border: 1.5px solid black;">SKU Code</td>
                </tr>';
                if(count($child) > 0)
                {
                    foreach ($child as $keys => $values) {

                        $company = Company::where('id',$values->company_id)->first();
                        $category = ProductCategory::where('id',$values->category_id)->first();
                        $product = Product::where('id',$values->product_id)->first();
                        $vendor = Vendor::where('id',$value->vendor_id)->first();

                        $html .= '<tr>
                        <td style="border: 1.5px solid black;">' . (isset($vendor) ? $vendor->name : '') . '</td>
                        <td style="border: 1.5px solid black;">' . $value->order_no . '</td>
                        <td style="border: 1.5px solid black;">' . $value->invoice_no . '</td>
                        <td style="border: 1.5px solid black;">' . date('d-m-Y', strtotime($value->date)) . '</td>
                        <td style="border: 1.5px solid black;">' . (isset($category) ? $category->name : '') . '</td>
                        <td style="border: 1.5px solid black;">' . (isset($company) ? $company->name : '') . '</td>
                        <td style="border: 1.5px solid black;">' . (isset($product) ? $product->product : '') . '</td>
                        <td style="border: 1.5px solid black;">' . (isset($product) ? $product->code : '') . '</td>
                        </tr>';
                    }
                }
                $html .= '</table><br/>';
            }
        }

        $param = Debit::where('vendor_id',$request->vendor_id)->get();
        $html1 = '';
        if(count($param) > 0)
        {
            foreach ($param as $key => $value)
            {
                $debitchild = DebitChild::where('debit_id',$value->id)->get();
                $date = Debit::where('id',$value->id)->latest()->first();
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
                <td colspan="2" style="text-align: center !important; border: 1.5px solid black;">Debit Date : '.$dates.'</h3></td>
                <td colspan="2" style="text-align: center !important; border: 1.5px solid black;">Order No : '.$order.'</td>
                </tr>
                <tr>
                <td style="border: 1.5px solid black;">Product Name</td>
                <td style="border: 1.5px solid black;">Product Code</td>
                <td style="border: 1.5px solid black;">Warehouse</td>
                <td style="border: 1.5px solid black;">Quantity</th>

                </tr>';
                if(count($debitchild) > 0)
                {
                    foreach ($debitchild as $keys => $values) {

                        $product = Product::where('id',$values->product_id)->first();
                        $warehouse = Warehouse::where('id',$values->warehouse_id)->first();

                        // $invoice_child = PurchaseInvoiceChild::where('id', $values->invoice_child_id)->first();
                        // $pending_qty = 0;
                        // if(isset($invoice_child))
                        // {
                        //     $pending_qty = $values->qty - $invoice_child->qty;
                        // }

                        $html1 .= '<tr>
                        <td style="border: 1.5px solid black;">'.$product->product.'</td>
                        <td style="border: 1.5px solid black;">'.$product->code.'</td>
                        <td style="border: 1.5px solid black;">'. (isset($warehouse) ? $warehouse->name : '') .'</td>
                        <td style="border: 1.5px solid black; text-align: center;">'.$values->qty.'</th>
                        </tr>';
                    }
                }
                $html1 .= '</table><br/>';
            }
        }
        return response()->json(['status'=>'success','data'=>$html ,'data1' =>$html1]);
    }
}