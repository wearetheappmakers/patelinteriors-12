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
use App\Vendor;
use App\Warehouse;
use App\Product;
use App\Client;
use App\PurchaseInvoice;
use App\PurchaseInvoiceChild;
use App\ProductCategory;
use App\SalesOrderInvoice;
use App\SalesOrderInvoiceChild;
use App\PurchaseOrder;
use App\PurchaseOrderChild;
use App\ItemLedger;
use App\GST;
use App\Credit;
use App\CreditChild;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use App\Imports\SalesOrderInvoiceImport;
use Excel;
use Auth;
use DataTables;

class SalesOrderInvoiceController extends Controller
{
    public function __construct(SalesOrderInvoice $s)
    {
        $this->view = 'salesorderinvoice';
        $this->route = 'salesorderinvoice';
        $this->viewName = 'Sales Invoice';

        $this->middleware(function ($request, $next) {
            $this->setSalesOrderInvoiceNumber();
            return $next($request);
        });
    } 

    private function setSalesOrderInvoiceNumber()
    {
        $si_no = SalesOrderInvoice::orderBy('id', 'DESC');

        $prefix = '';

        if (Auth::guard('user')->check() && Auth::guard('user')->user()->role == 2)
        {
            $si_no->where('branch_id', Auth::guard('user')->user()->id);
            $prefix = Auth::user()->prefix;

        } 
        elseif (Auth::guard('employee')->check()) 
        {
            $si_no->where('branch_id', Auth::guard('employee')->user()->branch_id);
            $branch = User::where('id',Auth::user()->branch_id)->first();
            $prefix = $branch->prefix;
        }

        $si_no = $si_no->first();

        if ($si_no) {
            $no = explode('-', $si_no['invoice_no']);
            $no = $no[1] + 1;
            $this->si_no = $prefix.'-' . $no;
        } else {
            $this->si_no = $prefix.'-1';
        }
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = SalesOrderInvoice::with('branch','employees','client')->orderBy('id','DESC');
            
            if(Auth::guard('user')->check())
            {
                if(Auth::user()->role == 2)
                {
                    $query->where('branch_id',Auth::user()->id);
                }
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
            if (Auth::guard('user')->check() &&
                Auth::user()->role == 1 &&
                $request->filled('branch_id')) {

                $query->where('branch_id', $request->branch_id);
            }

            $query = $query->latest();
            
            return Datatables::of($query)
            ->addColumn('action', function ($row) {
                $btn = '';

                if($row->status == 1)
                {
                    $btn .= '<a href="'.route('user.salesorderinvoice.edit', $row->id).'" class="btn btn-sm btn-clean btn-icon btn-icon-md" style="background: white;"  title="Edit">  <i style="color: green;" class="la la-edit">
                    </i></a>&nbsp;&nbsp;';
                }

                if(Auth::guard('user')->check() && Auth::user()->role == 1)
                {
                    if($row->status == 1)
                    {
                        $btn .= '<a href="'.route('user.salesorderinvoice.invoicedelete', $row->id).'" class="btn btn-sm btn-clean btn-icon btn-icon-md" style="background: white;" onclick="return confirm(`Are you sure??`)" title="Delete">  <i style="color: red;" class="la la-trash">
                        </i></a>&nbsp;&nbsp;';
                    }
                }

                $btn .= '<a href="'.route('user.salesorderinvoice.view', $row->id).'" class="btn btn-sm btn-clean btn-icon btn-icon-md" style="background: white;"  title="View">  <i style="color: black;" class="la la-eye">
                </i></a>&nbsp;&nbsp;'; 
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
            ->rawColumns(['action','date'])
            ->make(true);
        } 
        $branch = User::where('role',2)->get();
        return view('salesorderinvoice.index',compact('branch'));
    }

    public function create()
    {
        $data['title'] = 'Add ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['branch'] = User::where('role',2)->get();
        
        $data['si_no'] = "";
        $data['client'] = collect();
        $data['employees'] = collect();

        if(Auth::guard('user')->check() && Auth::user()->role == 2)
        {
            $data['client'] = Client::where('branch_id',Auth::user()->id)->get();
            $data['employees'] = Employee::where('status',1)->whereIn('role',['1','2'])->where('branch_id',Auth::user()->id)->get();
            $data['si_no'] = $this->si_no;
        }
        elseif(Auth::guard('employee')->check())
        {
            $data['client'] = Client::where('branch_id',Auth::user()->branch_id)->get();
            $data['employees'] = Employee::where('status',1)->whereIn('role',['1','2'])->where('branch_id',Auth::user()->branch_id)->get();
            $data['si_no'] = $this->si_no;
        }
        return view('salesorderinvoice.create')->with($data);
    }

    public function status(Request $request)
    {
        $param['status'] = $request->status;
        if($param['status'] == 2)
        {
            $purchaseinvoicechild = SalesOrderInvoiceChild::where('invoice_id',$request->id)->get();
            foreach ($purchaseinvoicechild as $key => $value) {
                $inventory = WarehouseInventory::where('product_id',$value->product_id)->where('warehouse_id',$value->warehouse_id)->first();
                $inventory->used = $inventory->used + $value->qty;
                $inventory->save();
            }
        }
        $change_status = SalesOrderInvoice::where('id',$request->id)->update($param);
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
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $branch_id = $request->branch_id;

                $code = SalesOrderInvoice::where('branch_id',$request->branch_id)->orderBy('id','DESC')->first();
                $prefix = User::where('id',$request->branch_id)->value('prefix');

                if(isset($code))
                {
                    $no = explode('-',$code['invoice_no']);
                    $no = $no[1] + 1;
                    $si_no = $prefix.'-'.$no; 
                }
                else  
                {
                    $si_no = $prefix.'-1';
                }
            }
            else
            {
                $branch_id = Auth::user()->id;
                $si_no = $this->si_no;
            }
            $employee_id = $request->employee_id;
        }

        if(Auth::guard('employee')->check())
        {
            if(Auth::user()->role == 4)
            {
                $employee_id = $request->employee_id;
            } else {
                $employee_id = Auth::user()->id;
            }
            $branch_id = Auth::user()->branch_id;
            $si_no = $this->si_no; 
        }

        if(!empty($request->product_id) && count($request->product_id) > 0)
        {
            foreach ($request->product_id as $key => $value)
            {
                if($value != '')
                {
                    $product = Product::where('id',$value)->first();

                    if(!($request->warehouse_id[$key] && $request->qty[$key] && ($request->gst_id[$key] != null)))
                    {
                        return response()->json(['status' => 'required', 'message' => 'Warehouse, Qty And GST Is Required !!!']);
                    }

                    if($request->qty[$key] < 1)
                    {
                        return response()->json(['status' => 'required', 'message' => 'Minimum 1 Qty Required !!!']);
                    }

                    $sales_child = SalesOrderChild::where('id',$request->salesorder_child_id[$key])->first();
                    if($request->qty[$key] > $sales_child->pending_qty)
                    {
                        return response()->json(['status' => 'required', 'message' => 'Entered Qty is Greater Than Pending Qty !!!']);
                    }

                    $inventory = WarehouseInventory::where('branch_id',$branch_id)->where('product_id',$value)->where('warehouse_id',$request->warehouse_id[$key])->first();
                    if(!isset($inventory))
                    {
                        return response()->json(['status' => 'required', 'message' => 'Warehouse Inventory Not Found for '.$product->code.' !!!']);
                    }
                    
                    $stock = $inventory->stock - $inventory->used;
                    if($request->qty[$key] > $stock)
                    {
                        return response()->json(['status' => 'required', 'message' => 'Entered Qty is Greater Than Warehouse Inventory Stock for '.$product->code.' !!!']);
                    }
                }
            }
        }
        
        $salesorderinvoice = new SalesOrderInvoice();
        $salesorderinvoice->invoice_no = $si_no;
        $salesorderinvoice->ids = $si_no;
        $salesorderinvoice->branch_id = $branch_id;
        $salesorderinvoice->employee_id = $employee_id;
        $salesorderinvoice->client_id = $request->name;
        $salesorderinvoice->pending_so = $request->pending_so;
        $salesorderinvoice->total_qty = $request->total_qty;
        $salesorderinvoice->total_discount_amt = $request->total_discount_amt;
        $salesorderinvoice->total_gst_amt = $request->total_gst_amt;
        $salesorderinvoice->total_amount = $request->total_amount;
        $salesorderinvoice->labour_charges = $request->labour_charges;
        $salesorderinvoice->transport_charges = $request->transport_charges;
        $salesorderinvoice->grand_total = $request->grand_total;
        $salesorderinvoice->date = $request->date;        
        $salesorderinvoice->save();

        $static_var = '0.00';
        if($request->product_id)
        {
            foreach ($request->product_id as $key => $value)
            {
                if($value != '')
                {
                    $product = Product::where('id',$value)->first();

                    $child = new SalesOrderInvoiceChild();
                    $child->invoice_id = $salesorderinvoice->id;
                    $child->salesorder_child_id = $request->salesorder_child_id[$key];
                    $child->category_id = $product->productcategory_id;
                    $child->company_id = $product->company_id;
                    $child->product_id = $value;
                    $child->warehouse_id = $request->warehouse_id[$key];
                    $child->qty = $request->qty[$key];
                    $child->rate = $request->rate[$key];
                    $child->discount = $request->discount_per[$key];
                    $child->discount_amt = $request->discount_amt[$key];
                    $child->gst = $request->gst[$key];
                    $child->gst_id = GST::where('gst',$request->gst_id[$key])->value('id');
                    $child->gst_amt = $request->gst_amt[$key];
                    $child->pending_qty = $request->qty[$key];
                    $child->new_qty = $request->qty[$key];
                    $child->amount = $request->amount[$key];
                    $child->blocking_qty = $request->blocking_qty[$key] ?? '0';


                    $child->save();

                    $sales_child = SalesOrderChild::where('id',$child->salesorder_child_id)->first();
                    $sales_child->pending_qty = $sales_child->pending_qty - $child->qty;
                    $sales_child->save();
                    
                    $inventory = WarehouseInventory::where('branch_id',$salesorderinvoice->branch_id)->where('product_id',$value)->where('warehouse_id',$child->warehouse_id)->first();
                    $inventory->used = $inventory->used + $child->qty;
                    $inventory->save();

                    if($request->blocking_qty[$key])
                    {
                    $inventory = WarehouseInventory::where('branch_id',$salesorderinvoice->branch_id)->where('product_id',$value)->where('warehouse_id',$child->warehouse_id)->first();
                    $inventory->si_blocking_qty = $inventory->si_blocking_qty + $child->blocking_qty;
                    $inventory->save();
                    }


                    $item_ledger = new ItemLedger();
                    $item_ledger->date = $salesorderinvoice->date;
                    $item_ledger->tran_type = 'Sales Invoice';
                    $item_ledger->client_id = $salesorderinvoice->client_id;
                    $item_ledger->branch_id = $salesorderinvoice->branch_id;
                    $item_ledger->warehouse_id = $child->warehouse_id;
                    $item_ledger->category_id = $child->category_id;
                    $item_ledger->company_id = $child->company_id;
                    $item_ledger->product_id = $child->product_id;
                    $item_ledger->product_code = $product->code;
                    $item_ledger->invoice_no = $salesorderinvoice->invoice_no;
                    $item_ledger->salesinvoice_id = $salesorderinvoice->id;
                    $item_ledger->salesinvoice_child_id = $child->id;
                    $item_ledger->outward = (string)number_format((float)$child->qty,2,'.','') ?? (string)$static_var;
                    $item_ledger->save();
                }
            }
        }

        $sales_order = SalesOrder::where('id',$salesorderinvoice->pending_so)->first();
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

        if($salesorderinvoice)
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
        $data['data'] = SalesOrderInvoice::where('id', $id)->first();
        $data['child'] = SalesOrderInvoiceChild::where('invoice_id',$id)->get();
        $data['module'] = $this->viewName;
        $data['id'] = $data['data']->salesorder_id;
        $data['resourcePath'] = $this->view;
        $data['employees'] = Employee::where('status',1)->where('branch_id',$data['data']->branch_id)->whereIn('role',['1','2'])->get();
        $data['gst'] = GST::where('branch_id',$data['data']->branch_id)->get();
        return view('salesorderinvoice.edit')->with($data);
    }

    public function update(Request $request, $id)
    {
        $salesorderinvoice = SalesOrderInvoice::where('id',$id)->first();
        if(!($salesorderinvoice->status == 1))
        {
            return response()->json(['status' => 'required', 'message' => 'Status Not Pending For This Invoice !!!']);
        }

        if(!empty($request->product_id) && count($request->product_id) > 0)
        {
            foreach ($request->product_id as $key => $value)
            {
                if($value != '')
                {
                    $product = Product::where('id',$value)->first();

                    if(!($request->qty[$key] && ($request->gst_id[$key] != null)))
                    {
                        return response()->json(['status' => 'required', 'message' => 'Qty And GST Is Required !!!']);
                    }

                    if($request->qty[$key] < 1)
                    {
                        return response()->json(['status' => 'required', 'message' => 'Minimum 1 Qty Required !!!']);
                    }

                    $sales_child = SalesOrderChild::where('id',$request->salesorder_child_id[$key])->first();
                    $sales_invoice_child = SalesOrderInvoiceChild::where('id',$request->hidden_id[$key])->first();
                    $pending_qty = $sales_child->pending_qty + $sales_invoice_child->qty;
                    
                    if($request->qty[$key] > $pending_qty)
                    {
                        return response()->json(['status' => 'required', 'message' => 'Entered Qty is Greater Than Pending Qty !!!']);
                    }

                    $inventory = WarehouseInventory::where('branch_id',$salesorderinvoice->branch_id)->where('product_id',$value)->where('warehouse_id',$request->warehouse_id[$key])->first();
                    if(!isset($inventory))
                    {
                        return response()->json(['status' => 'required', 'message' => 'Warehouse Inventory Not Found for '.$product->code.' !!!']);
                    }
                    $inve_old_used = $inventory->used - $sales_invoice_child->qty;
                    $stock = $inventory->stock - $inve_old_used;
                    
                    if($request->qty[$key] > $stock)
                    {
                        return response()->json(['status' => 'required', 'message' => 'Entered Qty is Greater Than Warehouse Inventory Stock for '.$product->code.' !!!']);
                    }
                }
            }
        }


        if(Auth::guard('user')->check())
        {
            $employee_id = $request->employee_id;
        }

        if(Auth::guard('employee')->check())
        {
            if(Auth::user()->role == 4)
            {
                $employee_id = $request->employee_id;
            }
            else
            {
                $employee_id = Auth::user()->id;
            }
        }
        
        $salesorderinvoice->employee_id = $employee_id;
        $salesorderinvoice->date = $request->date;
        $salesorderinvoice->total_qty = $request->total_qty;
        $salesorderinvoice->total_discount_amt = $request->total_discount_amt;
        $salesorderinvoice->total_gst_amt = $request->total_gst_amt;
        $salesorderinvoice->total_amount = $request->total_amount;
        $salesorderinvoice->labour_charges = $request->labour_charges;
        $salesorderinvoice->transport_charges = $request->transport_charges;
        $salesorderinvoice->grand_total = $request->grand_total;
        $salesorderinvoice->save();

        $static_var = '0.00';
        if($request->product_id)
        {
            foreach ($request->product_id as $key => $value)
            {
                if($value != '')
                {
                    $child = SalesOrderInvoiceChild::where('id',$request->hidden_id[$key])->first();
                    $product = Product::where('id',$value)->first();
                    
                    if(!($request->qty[$key] == $child->qty))
                    {
                        $sales_child = SalesOrderChild::where('id',$child->salesorder_child_id)->first();
                        $sales_child->pending_qty = ($sales_child->pending_qty + $child->qty) - $request->qty[$key];
                        $sales_child->save();

                        $inventory = WarehouseInventory::where('branch_id',$salesorderinvoice->branch_id)->where('product_id',$value)->where('warehouse_id',$child->warehouse_id)->first();
                        $inventory->used = ($inventory->used - $child->qty) + $request->qty[$key];
                        $inventory->save();
                        
                    }

                    if($request->blocking_qty[$key])
                    {
                        $inventory = WarehouseInventory::where('branch_id',$salesorderinvoice->branch_id)->where('product_id',$value)->where('warehouse_id',$child->warehouse_id)->first();
                        $inventory->si_blocking_qty = $inventory->si_blocking_qty - $child->blocking_qty + $request->blocking_qty[$key];
                        $inventory->save();
                    }
                    
                    $child->qty = $request->qty[$key];
                    $child->rate = $request->rate[$key];
                    $child->discount = $request->discount_per[$key];
                    $child->discount_amt = $request->discount_amt[$key];
                    $child->gst = $request->gst[$key];
                    $child->gst_id = GST::where('gst',$request->gst_id[$key])->value('id');
                    $child->gst_amt = $request->gst_amt[$key];
                    $child->pending_qty = $request->qty[$key];
                    $child->new_qty = $request->qty[$key];
                    $child->amount = $request->amount[$key];
                    $child->blocking_qty = $request->blocking_qty[$key] ?? '0';
                    $child->save();

                    $item_ledger = ItemLedger::where('salesinvoice_id',$salesorderinvoice->id)->where('salesinvoice_child_id',$child->id)->first();
                    if(isset($item_ledger))
                    {
                        $item_ledger->date = $salesorderinvoice->date;
                        $item_ledger->outward = (string)number_format((float)$child->qty,2,'.','') ?? (string)$static_var;
                        $item_ledger->save();
                    }
                    else
                    {
                        $item_ledger_new = new ItemLedger();
                        $item_ledger_new->date = $salesorderinvoice->date;
                        $item_ledger_new->tran_type = 'Sales Invoice';
                        $item_ledger_new->client_id = $salesorderinvoice->client_id;
                        $item_ledger_new->branch_id = $salesorderinvoice->branch_id;
                        $item_ledger_new->warehouse_id = $child->warehouse_id;
                        $item_ledger_new->category_id = $child->category_id;
                        $item_ledger_new->company_id = $child->company_id;
                        $item_ledger_new->product_id = $child->product_id;
                        $item_ledger_new->product_code = $product->code;
                        $item_ledger_new->invoice_no = $salesorderinvoice->invoice_no;
                        $item_ledger_new->salesinvoice_id = $salesorderinvoice->id;
                        $item_ledger_new->salesinvoice_child_id = $child->id;
                        $item_ledger_new->outward = (string)number_format((float)$child->qty,2,'.','') ?? (string)$static_var;
                        $item_ledger_new->save();
                    }
                }
            }
        }

        $sales_order = SalesOrder::where('id',$salesorderinvoice->pending_so)->first();
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

        if($salesorderinvoice)
        {
            return response()->json(['status' => 'success']);
        }else
        {
            return response()->json(['status' => 'error']);
        }
    }

    public function childdelete($id)
    {
        $basicchild = SalesOrderInvoiceChild::where('id',$id)->first();
        
        $main = SalesOrderInvoice::where('id',$basicchild->invoice_id)->first();
        if(!($main->status == 1))
        {
            return response()->json(['status' => 'required', 'message' => 'Status Not Pending For This Invoice !!!']);
        }

        $main->total_qty = $main->total_qty - $basicchild->qty;
        $main->total_discount_amt = round(($main->total_discount_amt - $basicchild->discount_amt),2);
        $main->total_gst_amt = round(($main->total_gst_amt - $basicchild->gst_amt),2);
        $main->total_amount = round(($main->total_amount - $basicchild->amount),2);
        $main->grand_total = round(($main->total_amount + $main->labour_charges + $main->transport_charges),2);
        $main->save();

        $so_child = SalesOrderChild::where('id', $basicchild->salesorder_child_id)->first();
        $so_child->pending_qty = $so_child->pending_qty + $basicchild->qty;
        $so_child->save();

        $so = SalesOrder::where('id', $so_child->salesorder_id)->first();
        $so->status = 2;
        $so->save();

        $inventory = WarehouseInventory::where('branch_id',$main->branch_id)->where('product_id',$basicchild->product_id)->where('warehouse_id',$basicchild->warehouse_id)->first();
        $inventory->used = $inventory->used - $basicchild->qty;
        $inventory->save();

        ItemLedger::where('salesinvoice_child_id',$basicchild->id)->where('salesinvoice_id',$basicchild->invoice_id)->delete();
        
        $basicchild->delete();
        
        if($basicchild)
        {
            return response()->json(['status' => 'success']);
        }
        else
        {
            return response()->json(['status' => 'error']);
        }
    }

    public function show($id)
    {
        $data['id'] = $id;
        $data['data'] = SalesOrderInvoice::where('id', $id)->first();
        $data['child'] = SalesOrderInvoiceChild::where('invoice_id',$id)->get();
        $data['module'] = $this->viewName;
        $data['id'] = $data['data']->salesorder_id;
        
        return view('salesorderinvoice.view')->with($data);
    }

    public function getemployee(Request $request)
    {
        $data = Employee::where('branch_id',$request->branch_id)->where('status',1)->whereIn('role',['1','2'])->get();
        $html = "<option value=''>Select</option>";
        foreach ($data as $key => $value) {
            $html .= "<option value=".$value->id.">".$value->name."</option>";
        }

        $clients = Client::where('branch_id',$request->branch_id)->get();
        $html1 = "<option value=''>Select</option>";
        foreach ($clients as $keysssss => $valuesssss) {
            $html1 .= "<option value=".$valuesssss->id.">".$valuesssss->member."</option>";
        }

        $si = SalesOrderInvoice::where('branch_id',$request->branch_id)->orderBy('id','DESC')->first();
        $prefix = User::where('id',$request->branch_id)->value('prefix');
        if(isset($si))
        {
            $no = explode('-',$si['invoice_no']);
            $no = $no[1] + 1;
            $si_no = $prefix.'-'.$no; 
        }
        else  
        {
            $si_no = $prefix.'-1';
        }

        return response()->json(['data'=>$html,'data1'=>$html1,'data2'=>$si_no]);
    }
    
    public function getsalesorder(Request $request)
    {
        $data = SalesOrder::where('name',$request->name)->whereIn('status',[0,2]);
        
        $data->whereHas('salesorderchild',function($q){
            $q->where('pending_qty','>',0);
        });
        $data = $data->get();
        
        $html = "<option value=''>Select</option>";
        foreach ($data as $key => $value) {
            $html .= "<option value=".$value->id.">".$value->order_no."</option>";
        }

        $client = Client::where('id',$request->name)->first();

        return response()->json(['data'=>$html, 'client' => $client]);
    }

    public function getso(Request $request)
    {    
        $so = SalesOrder::where('id',$request->pending_so)->first();
        $child = SalesOrderChild::where('salesorder_id',$so->id)->get();
        
        $warehouse = Warehouse::where('branch_id',$so->branch_id)->get();
        $gst = GST::where('branch_id',$so->branch_id)->get(); 

        $data['data'] = '';
        if(!empty($child))
        {
            foreach($child as $key => $value)
            {
                if(!($value->pending_qty == 0))
                {
                    $product = Product::where('id',$value->product_id)->first();
                    
                    
                    $data['data'] .= '<tr class="master_clone">
                    <td>
                    <div style="display: inline-flex !important;">
                    <button type="button" id="remove" class="btn btn-sm btn-icon btn-icon-md btn-clean remove"><i class="fa fa-minus" style="font-size:0.8rem !important"></i></button>
                    </div>
                    </td>

                    <td>
                    <input type="hidden" name="hidden_id[]" class="hidden_id">
                    <input type="hidden" name="salesorder_child_id[]" value="'.$value->id.'" class="salesorder_child_id">
                    <input type="hidden" name="product_id[]" value="'.$value->product_id.'" class="product_id">
                    '.$product->product.'<br>'.$product->code.'
                    </td>

                    <td>
                    <select style="width: 100% !important;" class="warehouse_id" name="warehouse_id[]">
                    <option value="">Select</option>';
                    foreach($warehouse as $wh_key => $wh_val)
                    {
                        $wh_selected = '';

                        if($value->warehouse_id == $wh_val->id)
                        {
                            $wh_selected = 'selected';
                        }
                        $data['data'] .= '<option value="'.$wh_val->id.'"'.$wh_selected.'>'.$wh_val->name.'</option>';
                    }
                    $data['data'] .='</select>
                    </td>

                    <td>
                    <input type="number" name="pending_qty[]" value="'.$value->pending_qty.'" placeholder="Pending Qty" class="pending_qty" style="width: 100% !important;" readonly>
                    <input type="number" name="qty[]" placeholder="Enter Qty" class="qty" style="width: 100% !important;">
                    <input type="number" name="blocking_qty[]" placeholder="Enter blocking qty" class="blocking_qty" style="width: 100% !important;">
                    </td>

                    <td><input type="number" name="rate[]" value="'.round($value->rate,2).'" placeholder="Enter Rate" class="rate" style="width: 100% !important;"></td>

                    <td>
                    <input type="number" name="discount_per[]"  placeholder="Enter Discount(%)" class="discount_per" style="width: 100% !important;" value="'.$value->discount_per.'">
                    <input type="number" name="discount_amt[]" placeholder="Discount Amount" class="discount_amt" style="width: 100% !important;" value="'.$value->discount_amt.'">
                    </td>

                    <td>
                    <input type="hidden" name="gst[]" value="'.$value->gst.'" class="gst" style="width: 100% !important;">
                    <select style="width: 100% !important;" class="gst_id" name="gst_id[]">
                    <option value="">Select</option>';
                    foreach($gst as $gst_key => $gst_val)
                    {
                        $gst_selected = '';

                        if($value->gst_id == $gst_val->id)
                        {
                            $gst_selected = 'selected';
                        }
                        $data['data'] .= '<option value="'.$gst_val->gst.'"'.$gst_selected.'>'.$gst_val->gst.'</option>';
                    }
                    $data['data'] .='</select><br>
                    <input type="text" placeholder="GST Amount" name="gst_amt[]" value="'.$value->gst_amt.'" class="gst_amt" style="width: 100% !important;" readonly>
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
            }
            $data['data'] .= ' </tbody></table>';
        }
        return response()->json($data);
    }

    public function invoicedelete($id)
    {
        $invoice = SalesOrderInvoice::where('id',$id)->first();

        if(!($invoice->status == 1))
        {
            $notification = array(
                'message' => 'Status Not Pending For This Invoice !!!',
                'alert-type' => 'error'
            );
            return redirect()->back()->with($notification);
        }

        $sales_invoice_child = SalesOrderInvoiceChild::where('invoice_id',$invoice->id)->get();
        
        $sales = SalesOrder::where('id',$invoice->pending_so)->first();
        $saleschild = SalesOrderChild::where('salesorder_id',$sales->id)->get();

        if(count($sales_invoice_child) > 0){
            foreach ($sales_invoice_child as $key => $value)
            {
                $so_child = SalesOrderChild::where('id',$value->salesorder_child_id)->first();
                $so_child->pending_qty = $so_child->pending_qty + $value->pending_qty;
                $so_child->save();
                
                $inventory = WarehouseInventory::where('branch_id',$invoice->branch_id)->where('product_id',$value->product_id)->where('warehouse_id',$value->warehouse_id)->first();
                $inventory->used = $inventory->used - $value->pending_qty;
                $inventory->save();

                ItemLedger::where('salesinvoice_child_id',$value->id)->delete();

                $value->delete();
            }
        }

        $sales_order = SalesOrder::where('id',$invoice->pending_so)->first();
        if(isset($sales_order)){
            $total_qty = SalesOrderChild::where('salesorder_id', $sales_order->id)->sum('qty');
            $total_pending_qty = SalesOrderChild::where('salesorder_id', $sales_order->id)->sum('pending_qty');

            if ($total_pending_qty == 0) {
                $status = 1;
            } elseif ($total_pending_qty < $total_qty) {
                $status = 2;
            } else {
                $status = 0;
            }
            $sales_order->status = $status;
            $sales_order->save();
        }
        $invoice->delete();

        $notification = array(
            'message' => 'Sales Invoice Deleted Successfully.',
            'alert-type' => 'success'
        );
        return redirect()->back()->with($notification);
    }

    // public function delete()
    // {
    //     $deleted_main = DB::table($table_name)->whereIn('id', $id_array)->first();

    //     $sales_invoice_child = DB::table('salesorder_invoice_child')->whereIn('invoice_id',$id_array)->get();
    //     if(count($sales_invoice_child) > 0)
    //     {
    //         foreach($sales_invoice_child as $sic_key => $sic_val)
    //         {
    //             $so_child = DB::table('salesorder_child')->where('id',$sic_val->salesorder_child_id)->first();
    //             $so_child->pending_qty = $so_child->pending_qty + $sic_val->qty;
    //             $so_child->save();


    //             $inventory = DB::table('warehouse_inventories')->where('product_id',$sic_val->product_id)->where('warehouse_id',$sic_val->warehouse_id)->first();
    //             $inventory->used = $inventory->used - $sic_val->qty;
    //             $inventory->save();
    //         }
    //     }   


    //     $sales_order = DB::table('salesorder')->where('id',$deleted_main->pending_so)->first();
    //     if(isset($sales_order))
    //     {
    //         $total_qty = DB::table('salesorder_child')->where('salesorder_id', $sales_order->id)->sum('qty');
    //         $total_pending_qty = DB::table('salesorder_child')->where('salesorder_id', $sales_order->id)->sum('pending_qty');

    //         if ($total_pending_qty == 0)
    //         {
    //             $status = 1;
    //         } 
    //         elseif($total_pending_qty < $total_qty)
    //         {
    //             $status = 2;
    //         } 
    //         else 
    //         {
    //             $status = 0;
    //         }
    //         $sales_order->status = $status;
    //         $sales_order->save();
    //     }

    //     DB::table('salesorder_invoice_child')->whereIn('invoice_id',$id_array)->delete();
    //     DB::table($table_name)->whereIn('id', $id_array)->delete();
    // }

    // public function getso(Request $request)
    // {    
    //     $so = SalesOrder::where('id',$request->pending_so)->first();
    //     $child = SalesOrderChild::where('salesorder_id',$so->id)->get();

    //     $data['branch'] = User::where('id',$so->branch_id)->value('name');
    //     $data['client'] = Client::where('id',$so->name)->value('member');
    //     $emp = Employee::where('branch_id',$so->branch_id)->get();
    //     $data['employee'] = "<option value=''>Select</option>";
    //     foreach ($emp as $keysssss => $valuesssss) {
    //         $data['employee'] .= "<option value=".$valuesssss->id.">".$valuesssss->name."</option>";
    //     }

    //     $data['data'] = '';
    //     if(!empty($child)){
    //         foreach($child as $key => $value){
    //             if($value->pending_qty != 0){
    //                 $category = ProductCategory::where('id',$value->productcategory_id)->value('name'); 
    //                 $company = Company::where('id',$value->company_id)->value('name'); 
    //                 $product = Product::where('id',$value->product_id)->first(); 
    //                 $warehouse = Warehouse::where('id',$value->warehouse_id)->value('name'); 
    //                 $data['data'] .= ' <tr class="master_clone" data-id="0">
    //                 <td>
    //                 <input type="hidden" name="product_id[]" data-id="'.$value->id.'" id="product_id_'.$value->id.'" value="'.$value->product_id.'">
    //                 <input type="text" class="form-control" value="'.$product->product.'" readonly>
    //                 </td>
    //                 <td>
    //                 <input type="hidden" name="salesorder_child_id[]" value="'.$value->id.'">
    //                 <input type="text" class="form-control" value="'.$product->code.'" readonly>
    //                 </td>
    //                 <td>
    //                 <input type="hidden" name="warehouse_id[]" data-id ="'.$value->id.'" id="warehouse_id_'.$value->id.'" value="'.$value->warehouse_id.'">
    //                 <input type="text" class="form-control" value="'.$warehouse.'" readonly>
    //                 </td>
    //                 <td>';
    //                 if($value->pending_qty > 0){
    //                     $data['data'] .= '<input type="number" name="qty[]" data-id="'.$value->id.'" id="qty_'.$value->id.'" value="'.$value->pending_qty.'" class="form-control qty" readonly >';
    //                 }else{
    //                     $data['data'] .= '<input type="number" name="qty[]" data-id="'.$value->id.'" id="qty_'.$value->id.'" value="0" class="form-control qty" readonly >';
    //                 }
    //                 $data['data'] .= '</td><td>
    //                 <input type="number" name="rate[]" data-id="'.$value->id.'" id="rate_'.$value->id.'" value="'.$value->rate.'" class="form-control rate " readonly>
    //                 </td>
    //                 <td>
    //                 <input type="number" name="pending_qty[]" data-id="'.$value->id.'" id="pending_qty_'.$value->id.'"  class="form-control pending_qty"  >
    //                 </td>
    //                 <td>
    //                 <input type="number" name="discount[]"  class="form-control discount" id="discount_'.$value->id.'" data-id="'.$value->id.'" >
    //                 </td>
    //                 <td>
    //                 <input type="number" placeholder="Enter Amount" name="amount[]" data-id="'.$value->id.'" id="amount_'.$value->id.'" readonly  class="form-control
    //                 amount">
    //                 </td>
    //                 </tr>';
    //             }
    //         }
    //         $data['data'] .= ' </tbody></table>';
    //     }
    //     return response()->json($data);
    // }

    public function salesorderinvoice_report()
    {
        return view('salesorderinvoice.report');
    }
    public function get_report_data(Request $request)
    {
        if($request->client_id)
        {
            $data = SalesOrderInvoice::where('client_id',$request->client_id)->get();
        }
        if($request->employee_id)
        {
            $data = SalesOrderInvoice::where('employee_id',$request->employee_id)->get();
        }
        $html = '';
        if(count($data) > 0)
        {
            foreach ($data as $key => $value) {
                $html .= '<table class="table table-striped- table-bordered table-hover table-checkable datatable">
                <tr>
                <td style="border: 1.5px solid black;">Date</td>
                <td style="border: 1.5px solid black;">Employee</td>
                <td style="border: 1.5px solid black;">Client</td>
                <td style="border: 1.5px solid black;">Invoice No.</td>
                <td style="border: 1.5px solid black;">Pending SO</td>
                </tr>';

                $child = SalesOrderInvoiceChild::where('invoice_id',$value->id)->get();
                if(count($child) > 0)
                {
                    foreach ($child as $keys => $values) {

                        $so = SalesOrderChild::where('id',$values->salesorder_child_id)->latest()->first();
                        $employee = Employee::where('id',$value->employee_id)->first();
                        $client = Client::where('id',$value->client_id)->first();

                        $html .= '<tr>
                        <td style="border: 1.5px solid black;">'. date('d-m-Y', strtotime($value->date)) .'</td>
                        <td style="border: 1.5px solid black;">'. (isset($employee) ? $employee->name : '') .'</td>
                        <td style="border: 1.5px solid black;">'. (isset($client) ? $client->member : '') .'</td>
                        <td style="border: 1.5px solid black;">'. $value->invoice_no .'</td>
                        <td style="border: 1.5px solid black; text-align: center;">'. (isset($so) ? $so->pending_qty : 0) .'</td>
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

    public function invoice_sample_export()
    {
        return Excel::download(new SalesOrderInvoiceSample, 'salesorder-invoice-sample.xlsx');
    }

    public function invoice_import(Request $request) 
    {
        try {
            Excel::import(new SalesOrderInvoiceImport, $request->file('file1'));
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

class SalesOrderInvoiceSample implements WithHeadings
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
                    'pending_so',
                    'invoice_no',
                    'date',
                    'labour_charges',
                    'transport_charges',
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
                    'pending_so',
                    'invoice_no',
                    'date',
                    'labour_charges',
                    'transport_charges',
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
                    'pending_so',
                    'invoice_no',
                    'date',
                    'labour_charges',
                    'transport_charges',
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
                    'pending_so',
                    'invoice_no',
                    'date',
                    'labour_charges',
                    'transport_charges',
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