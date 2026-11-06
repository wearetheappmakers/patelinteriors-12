<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Credit;
use App\Company;
use App\User;
use App\Client;
use App\Vendor;
use App\SalesOrderInvoice;
use App\SalesOrderInvoiceChild;
use App\CreditChild;
use App\Product;
use App\Employee;
use App\Warehouse;
use App\ProductCategory;
use App\GST;
use App\WarehouseInventory;
use DataTables;
use Auth;


class CreditController extends Controller
{
    public function __construct(Credit $s)
    {
        $this->view = 'credit';
        $this->route = 'credit';
        $this->viewName = 'Credit';
    }

    public function index(Request $request)
    {
        if ($request->ajax())
        {
            $query = Credit::orderBy('id','DESC');
            if(Auth::guard('user')->check())
            {
                if(Auth::user()->role == 2)
                {
                    $query = $query->where('branch_id',Auth::user()->id)->latest();
                }else{
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

                    $btn .= '<a style="background: transparent;" href="'.route('user.credit.edit',$row->id).'" title="Edit details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: green;" class="la la-edit"></i>
                    </a>&nbsp;&nbsp;&nbsp;';
                }
                if(Auth::guard('user')->check() && Auth::user()->role == 1)
                {
                    $btn .= '<a href="'.route('user.deletes.credit',$row->id).'" onclick="return confirm(`Are you sure?`)" style="background: white;" title="Delete details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: red;" class="la la-trash"></i></a>&nbsp;&nbsp;&nbsp;';

                    
                }
                $btn .= '<a style="background: transparent;" href="'.route('user.credit.show',$row->id).'" title="View details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="la la-eye"></i>
                </a>&nbsp;&nbsp;&nbsp;';
                // if($row->status == 1)
                // {
                //     $btn .= '<a style="background: transparent;" href="'.route('user.credit.show',$row->id).'" title="View details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="la la-eye"></i>
                //     </a>&nbsp;&nbsp;&nbsp;';
                // }
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

            ->editColumn('branch_id', function ($row) {
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

            ->editColumn('date', function ($row) {
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

            ->editColumn('client_id', function ($row) {
                if($row->client_id)
                {
                    $btn = Client::where('id',$row->client_id)->value('member');

                }
                else
                {
                    $btn = '';
                }
                return $btn;
            })
            ->addColumn('invoice_id', function ($row) {
                if($row->invoice_id)
                {
                    $btn = SalesOrderInvoice::where('id',$row->invoice_id)->value('invoice_no');

                }
                else
                {
                    $btn = '';
                }
                return $btn;
            })
            ->rawColumns(['action','singlecheckbox','invoice_id'])
            ->make(true);
        }
        $branch = User::where('role',2)->get();

        return view('credit.index',compact('branch'));
    }

    public function create()
    {
        $data['title'] = 'Add ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;

        $data['branch'] = User::where('role',2)->where('status',1)->get();

        $data['client'] = collect();

        if(Auth::guard('user')->check() && Auth::user()->role == 2)
        {
            $data['client'] = Client::where('branch_id',Auth::user()->id)->get();
        }
        elseif(Auth::guard('employee')->check())
        {
            $data['client'] = Client::where('branch_id',Auth::user()->branch_id)->get();
        }
        return view('general.add_form')->with($data);
    }

    public function status(Request $request)
    {
        $main = Credit::where('id',$request->id)->first();

        if($request->status == 1)
        {
            $purchaseinvoicechild = CreditChild::where('credit_id',$request->id)->get();
            foreach ($purchaseinvoicechild as $key => $value)
            {
                $inventory = WarehouseInventory::where('branch_id',$main->branch_id)->where('product_id',$value->product_id)->where('warehouse_id',$value->warehouse_id)->first();
                $inventory->stock = $inventory->stock + $value->qty;
                $inventory->save();

                $so_invoice_child = SalesOrderInvoiceChild::where('id',$value->sales_invoice_child_id)->first();
                $so_invoice_child->pending_qty = $so_invoice_child->pending_qty - $value->qty;
                $so_invoice_child->save();

                $so_invoice = SalesOrderInvoice::where('id',$main->invoice_id)->first();
                $so_invoice->status = 2;
                $so_invoice->save();
            }
        }
        $main->status = $request->status;
        $main->save();

        if($main)
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
            }
            else
            {
                $branch_id = Auth::user()->id;
            }
        }

        if(Auth::guard('employee')->check())
        {
            $branch_id = Auth::user()->branch_id;
        }

        if(!empty($request->product_id) && count($request->product_id) > 0)
        {
            foreach ($request->product_id as $key => $value)
            {
                if($value != '')
                {
                    $product = Product::where('id',$value)->first();

                    if(!($request->warehouse_id[$key] && $request->qty[$key] && $request->gst_id[$key]))
                    {
                        return response()->json(['status' => 'required', 'message' => 'Warehouse, Qty And GST Is Required !!!']);
                    }

                    if($request->qty[$key] < 1)
                    {
                        return response()->json(['status' => 'required', 'message' => 'Minimum 1 Qty Required !!!']);
                    }

                    $sales_invoice_child = SalesOrderInvoiceChild::where('id',$request->sales_invoice_child_id[$key])->first();
                    if($request->qty[$key] > $sales_invoice_child->pending_qty)
                    {
                        return response()->json(['status' => 'required', 'message' => 'Entered Qty is Greater Than Pending Qty !!!']);
                    }

                    $inventory = WarehouseInventory::where('branch_id',$branch_id)->where('product_id',$value)->where('warehouse_id',$request->warehouse_id[$key])->first();
                    if(!isset($inventory))
                    {
                        return response()->json(['status' => 'required', 'message' => 'Warehouse Inventory Not Found for '.$product->code.' !!!']);
                    }
                }
            }
        }

        $credit = new Credit();
        $credit->branch_id = $branch_id;
        $credit->client_id = $request->client_id;
        $credit->invoice_id = $request->invoice_id;
        $credit->date = $request->date;
        $credit->order_no = $request->order_no;
        $credit->remark = $request->remark;
        $credit->total_qty = $request->total_qty;
        $credit->total_discount_amt = $request->total_discount_amt;
        $credit->total_gst_amt = $request->total_gst_amt;
        $credit->total_amount = $request->total_amount;
        $credit->amount = $request->total_amount;
        $credit->save();



        if($request->product_id)
        {
            foreach ($request->product_id as $key => $value) {
                if($value != '')
                {
                    $product = Product::where('id',$value)->first();
                    
                    $child = new CreditChild();
                    $child->credit_id = $credit->id;
                    $child->sales_invoice_child_id = $request->sales_invoice_child_id[$key];
                    $child->product_id = $value;
                    $child->category_id = $product->productcategory_id;
                    $child->company_id = $product->company_id;
                    $child->warehouse_id = $request->warehouse_id[$key];
                    $child->qty = $request->qty[$key];
                    $child->pending_qty = $request->qty[$key];
                    $child->rate = $request->rate[$key];
                    $child->discount_per = $request->discount_per[$key];
                    $child->discount_amt = $request->discount_amt[$key];
                    $child->gst = $request->gst[$key];
                    $child->gst_id = $request->gst_id[$key];
                    $child->gst_amt = $request->gst_amt[$key];
                    $child->amount = $request->amount[$key];
                    $child->save();
                }
            }
        }


        if ($credit){
            return response()->json(['status'=>'success']);
        }else{
            return response()->json(['status'=>'error']);
        }
    }

    public function show($id)
    {
        $data['data'] = Credit::where('id',$id)->first();
        $data['child'] = CreditChild::where('credit_id',$id)->get();

        return view('credit.view')->with($data);
    }

    public function edit($id)
    {
        $data['title'] = 'Edit ' . $this->viewName;
        $data['data'] = Credit::where('id', $id)->first();
        $data['child'] = CreditChild::where('credit_id', $id)->get();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['product'] = SalesOrderInvoiceChild::where('pending_qty','>',0)->where('invoice_id',$data['data']->invoice_id)->get();
        $data['gst'] = GST::where('branch_id',$data['data']->branch_id)->get();
        $data['warehouses'] = Warehouse::where('branch_id',$data['data']->branch_id)->get();
        
        return view('general.edit_form')->with($data);
    }

    public function update(Request $request, $id)
    {
        $credit = Credit::where('id',$id)->first();
        $branch_id = $credit->branch_id;
        
        if(!empty($request->product_id) && count($request->product_id) > 0)
        {
            foreach ($request->product_id as $key => $value)
            {
                if($value != '')
                {
                    $product = Product::where('id',$value)->first();

                    if(!($request->warehouse_id[$key] && $request->qty[$key] && $request->gst_id[$key]))
                    {
                        return response()->json(['status' => 'required', 'message' => 'Warehouse, Qty And GST Is Required !!!']);
                    }

                    if($request->qty[$key] < 1)
                    {
                        return response()->json(['status' => 'required', 'message' => 'Minimum 1 Qty Required !!!']);
                    }

                    $sales_invoice_child = SalesOrderInvoiceChild::where('id',$request->sales_invoice_child_id[$key])->first();
                    if($request->qty[$key] > $sales_invoice_child->pending_qty)
                    {
                        return response()->json(['status' => 'required', 'message' => 'Entered Qty is Greater Than Pending Qty !!!']);
                    }

                    $inventory = WarehouseInventory::where('branch_id',$branch_id)->where('product_id',$value)->where('warehouse_id',$request->warehouse_id[$key])->first();
                    if(!isset($inventory))
                    {
                        return response()->json(['status' => 'required', 'message' => 'Warehouse Inventory Not Found for '.$product->code.' !!!']);
                    }
                }
            }
        }

        $credit->date = $request->date;
        $credit->order_no = $request->order_no;
        $credit->remark = $request->remark;
        $credit->total_qty = $request->total_qty;
        $credit->total_discount_amt = $request->total_discount_amt;
        $credit->total_gst_amt = $request->total_gst_amt;
        $credit->total_amount = $request->total_amount;
        $credit->amount = $request->total_amount;
        $credit->save();


        if($request->product_id)
        {
            foreach ($request->product_id as $key => $value) {
                if($value != '')
                {
                    $product = Product::where('id',$value)->first();
                    
                    if($request->hidden_id[$key] == null)
                    {
                        $child = new CreditChild();
                        $child->credit_id = $credit->id;
                    }
                    else
                    {
                        $child = CreditChild::where('id',$request->hidden_id[$key])->first();
                    }
                    $child->sales_invoice_child_id = $request->sales_invoice_child_id[$key];
                    $child->product_id = $value;
                    $child->category_id = $product->productcategory_id;
                    $child->company_id = $product->company_id;
                    $child->warehouse_id = $request->warehouse_id[$key];
                    $child->qty = $request->qty[$key];
                    $child->pending_qty = $request->qty[$key];
                    $child->rate = $request->rate[$key];
                    $child->discount_per = $request->discount_per[$key];
                    $child->discount_amt = $request->discount_amt[$key];
                    $child->gst = $request->gst[$key];
                    $child->gst_id = $request->gst_id[$key];
                    $child->gst_amt = $request->gst_amt[$key];
                    $child->amount = $request->amount[$key];
                    $child->save();
                }
            }
        }

        
        if ($credit){
            return response()->json(['status'=>'success']);
        }else{
            return response()->json(['status'=>'error']);
        }
    }

    public function getclient(Request $request)
    {
        $data = Client::where('branch_id',$request->branch_id)->get();
        $html = "<option value=''>Select</option>";

        foreach ($data as $key => $value) {

            $html .= "<option value=".$value->id.">".$value->member."</option>";

        }

        $data2 = Vendor::where('branch_id',$request->branch_id)->get();
        $html2 = "<option value=''>Select</option>";

        foreach ($data2 as $key => $value) {

            $html2 .= "<option value=".$value->id.">".$value->name."</option>";

        }

        $data1 = ProductCategory::where('branch_id',$request->branch_id)->get();
        $html1 = "<option value=''>Select</option>";

        foreach ($data1 as $key => $value) {

            $html1 .= "<option value=".$value->id.">".$value->name."</option>";

        }

        return response()->json(['data'=>$html, 'data1' => $html1,'data2' => $html2]);
    }

    public function getinvoice(Request $request)
    {
        $client = Client::where('id',$request->client_id)->first();

        $data = SalesOrderInvoice::where('status','!=',3)->where('client_id',$request->client_id)->get();
        $html = "<option value=''>Select</option>";
        foreach ($data as $key => $value)
        {
            $html .= "<option value=".$value->id.">".$value->invoice_no." / ".$value->date."</option>";
        }
        return response()->json(['data'=>$html, 'client' => $client]);
    }

    public function pending_so_invoice_product(Request $request)
    {
        $data = SalesOrderInvoiceChild::where('pending_qty','>',0)->where('invoice_id',$request->invoice_id)->get();

        $html = "<option value=''>Select</option>";
        foreach ($data as $key => $value)
        {
            $html .= "<option value=".$value->id.">".$value->product->code."</option>";
        }
        return response()->json(['data'=>$html]);
    }

    public function getdata(Request $request)
    {
        $child = SalesOrderInvoiceChild::where('id',$request->sales_invoice_child_id)->first();
        $invoice = SalesOrderInvoice::where('id',$child->invoice_id)->first();

        $data['data'] = '';
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
        $data['data'] .= '<tr class="master_clone">
        <td>
        <div style="display: inline-flex !important;">
        <button type="button" id="remove" class="btn btn-sm btn-icon btn-icon-md btn-clean remove"><i class="fa fa-minus" style="font-size:0.8rem !important"></i></button>
        </div>
        </td>

        <td>
        <input type="hidden" name="hidden_id[]" class="hidden_id">
        <input type="hidden" name="sales_invoice_child_id[]" value="'.$child->id.'" class="sales_invoice_child_id">
        <input type="hidden" name="product_id[]" value="'.$child->product_id.'" class="product_id">
        '.$child->product->product.'<br>'.$child->product->code.'
        </td>

        <td>
        
        <select style="width: 100% !important;" class="warehouse_id" name="warehouse_id[]" >
        <option value="">Select</option>';
        foreach ($warehouse as $key => $value)
        {
            $data['data'] .= "<option value=".$value->id." >".$value->name."</option>";
        }
        $data['data'] .= '</select>
        </td>

        <td>
        <input type="number" name="pending_qty[]" value="'.$child->pending_qty.'" placeholder="Pending Qty" class="pending_qty" style="width: 100% !important;" readonly>
        <input type="number" name="qty[]" placeholder="Enter Qty" class="qty" style="width: 100% !important;">
        </td>

        <td><input type="number" name="rate[]" value="'.round($child->rate,2).'" placeholder="Enter Rate" class="rate" style="width: 100% !important;"></td>

        <td>
        <input type="number" name="discount_per[]"  placeholder="Enter Discount(%)" class="discount_per" style="width: 100% !important;" readonly>
        <input type="number" name="discount_amt[]" placeholder="Discount Amount" class="discount_amt" style="width: 100% !important;" value="'.$child->discount_amt.'">
        </td>

        <td>
        <input type="hidden" name="gst[]" value="'.$child->gst.'" class="gst" style="width: 100% !important;">
        <select style="width: 100% !important;" class="gst_id" name="gst_id[]">
        <option value="">Select</option>';
        foreach($gst as $gst_key => $gst_val)
        {
            $gst_selected = '';

            if($child->gst_id == $gst_val->id)
            {
                $gst_selected = 'selected';
            }
            $data['data'] .= '<option value="'.$gst_val->id.'"'.$gst_selected.'>'.$gst_val->gst.'</option>';
        }
        $data['data'] .='</select><br>
        <input type="text" placeholder="GST Amount" name="gst_amt[]" value="'.$child->gst_amt.'" class="gst_amt" style="width: 100% !important;" readonly>
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

        <td><input type="text" placeholder="Final Amount" name="amount[]" value="'.$child->amount.'" class="amount" style="width: 100% !important;" readonly></td>

        </tr>';

        return response()->json($data);
    }

    public function childdelete($id)
    {
        $basicchild = CreditChild::where('id',$id)->first();

        $main = Credit::where('id',$basicchild->credit_id)->first();
        $main->total_qty = $main->total_qty - $basicchild->qty;
        $main->total_discount_amt = round(($main->total_discount_amt - $basicchild->discount_amt),2);
        $main->total_gst_amt = round(($main->total_gst_amt - $basicchild->gst_amt),2);
        $main->total_amount = round(($main->total_amount - $basicchild->amount),2);
        $main->amount = $main->total_amount;
        $main->save();
        $basicchild->delete();

        if($basicchild)
        {
            return response()->json(['status' => 'success'  ]);
        }else
        {
            return response()->json(['status' => 'error']);
        }
    }

    public function delete($id)
    {
        $main = Credit::where('id',$id)->first();
        $invoice_id = $main->invoice_id;
        
        $purchaseinvoicechild = CreditChild::where('credit_id',$main->id)->get();
        foreach ($purchaseinvoicechild as $key => $value)
        {
            $inventory = WarehouseInventory::where('branch_id',$main->branch_id)->where('product_id',$value->product_id)->where('warehouse_id',$value->warehouse_id)->first();
            $inventory->stock = $inventory->stock - $value->qty;
            $inventory->save();

            $so_invoice_child = SalesOrderInvoiceChild::where('id',$value->sales_invoice_child_id)->first();
            $so_invoice_child->pending_qty = $so_invoice_child->pending_qty + $value->qty;
            $so_invoice_child->save();

            $value->delete();
        }
        $main->delete();

        
        $so_invoice = SalesOrderInvoice::where('id',$invoice_id)->first();
        $pending_credit = Credit::where('invoice_id',$invoice_id)->first();
        if(isset($pending_credit))
        {
            $so_invoice->status = 2;
        }
        else
        {
            $so_invoice->status = 1;
        }
        $so_invoice->save();


        return redirect()->back();
    }

    // public function getdata(Request $request)
    // {
    //     $invoice = SalesOrderInvoice::where('id',$request->invoice_id)->first();
    //     $child = SalesOrderInvoiceChild::where('invoice_id',$request->invoice_id)->get();


    //     $data['branch'] = User::where('id',$invoice->branch_id)->value('name');
    //     $data['client'] = Client::where('id',$invoice->name)->value('member');
    //     $data['data'] = '';

    //     if(!empty($child)){
    //         foreach($child as $key => $value){
    //             $category = ProductCategory::where('id',$value->category_id)->value('name'); 
    //             $company = Company::where('id',$value->company_id)->value('name'); 
    //             $product = Product::where('id',$value->product_id)->first(); 
    //             $warehouse = Warehouse::where('id',$value->warehouse_id)->value('name'); 
    //             $data['data'] .= ' <tr class="master_clone" data-id="0" data-master="0">



    //             <td>
    //             <input type="hidden" name="product_id[]" data-id="'.$value->id.'" id="product_id_'.$value->id.'" value="'.$value->product_id.'">

    //             <input type="text" class="form-control" value="'.$product->product.'" readonly>
    //             </td>

    //             <td>
    //             <input type="hidden" name="salesorder_child_id[]" value="'.$value->id.'">
    //             <input type="text" class="form-control" value="'.$product->code.'" readonly>


    //             </td>




    //             <td>
    //             <input type="hidden" name="warehouse_id[]" data-id ="'.$value->id.'" id="warehouse_id_'.$value->id.'" value="'.$value->warehouse_id.'">

    //             <input type="text" class="form-control" value="'.$warehouse.'" readonly>
    //             </td>
    //             <td>
    //             <input type="number" name="qty[]" data-id="'.$value->id.'" id="qty_'.$value->id.'" value="'.$value->pending_qty.'" class="form-control qty" readonly >
    //             </td>
    //             <td>
    //             <input type="number" name="rate[]" data-id="'.$value->id.'" id="rate_'.$value->id.'" value="'.$value->rate.'" class="form-control rate " readonly>
    //             </td>
    //             <td>
    //             <input type="number" name="pending_qty[]" data-id="'.$value->id.'" id="pending_qty_'.$value->id.'"  class="form-control pending_qty"  >
    //             </td>

    //             <td>
    //             <input type="number" placeholder="Enter Amount" name="amount[]" data-id="'.$value->id.'" id="amount_'.$value->id.'" readonly  class="form-control
    //             amount">
    //             </td>
    //             </tr>';
    //         }
    //         $data['data'] .= ' </tbody></table>';
    //     }
    //     return response()->json($data);
    // }
}
