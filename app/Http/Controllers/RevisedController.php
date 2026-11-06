<?php
namespace App\Http\Controllers;
use DataTables;
use App\Payment;
use App\Delivery;
use App\User;
use App\Salesorder;
use App\QuotationChild;
use App\RevisedQuotation;
use App\ProductCategory;
use App\Product;
use App\RevisedChild;
use App\RevisedMainChild;
use App\GST;
use App\WarehouseInventory;
use App\Salesorder_Child;
use App\Payment_Child;
use App\Employee;
use App\Client;
use Illuminate\Http\Request;

class RevisedController extends Controller
{
    public function __construct(RevisedQuotation $s)
    {
        $this->view = 'revised';
        $this->route = 'revised';
        $this->viewName = 'Revised Quotation';
    }

    public function index(Request $request,$id)
    {
        if ($request->ajax()) {
            $query = RevisedQuotation::where('quotation_id',$id)->get();
            
            return Datatables::of($query)
            ->addColumn('action', function ($row) {         
                $btn = '<a style="background: white;" href="'.route('user.revised.view', $row->id).'" title="view" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-eye"></i></a>';
                return $btn;
            })
            ->addColumn('date', function ($row) {
                $ac = date('d-m-Y',strtotime($row->created_at));
                return $ac;
            })
            ->editColumn('time',function($row){
              return date('h:i A', strtotime($row->created_at));
            })
            
            ->rawColumns(['numbers','action','date','time'])
            ->make(true);
        } 
        $data['id'] = $id;
        return view('revised.index')->with($data);
    }

    public function create($id)
    {
        // $data['id'] = $id;
        // $data['title'] = 'Add Payment';
        // $data['module'] = $this->viewName;
        // $data['resourcePath'] = $this->view;
        // $data['payment'] = Payment::where('salesorder_id',$id)->first();
        // $data['value'] = Payment_Child::where('payment_id',$id)->get();
        // $data ['data'] = Salesorder::where('id', $id)->first();
        // $data['edit_clone'] = Salesorder_Child::where('salesorder_id',$id)->get();
        // $data['redirect_id'] = $id;
        // $data['amount_paid'] = Payment::where('salesorder_id',$id)->sum('amount');
        // $data['product'] = Product::get();
        // return view('general.add_form')->with($data);
    }

    public function store(Request $request)
    {
        // $data = new Payment();
        // $data->salesorder_id = $request->salesorder_id;
        // $data->name = $request->name;
        // $data->address = $request->address;
        // $data->contact_no = $request->contact_no;
        // $data->amount = $request->amount;
        // $data->bill_no = $request->bill_no;
        // $data->date = $request->date;
        // $data->bfl = $request->bfl;
        // $data->on_ac = $request->on_ac;
        // $data->payment_details = $request->payment_details;
        // $data->referance_by = $request->referance_by;
        // // $data->amount_pay = $request->amount_pay;
        // $data->save();
        // if($data)
        // {
        //     return response()->json(['status' => 'success']);
        // }else
        // {
        //     return response()->json(['status' => 'error']);
        // }
    }

    public function edit($id)
    {
        // $data['id'] = $id;
        // $data['title'] = 'Edit ' . $this->viewName;
        // $data['module'] = $this->viewName;
        // $data['resourcePath'] = $this->view;
        // $data['payment'] = Payment::where('id',$id)->first();
        // $data ['data'] = Salesorder::where('id', $data['payment']['salesorder_id'])->first();
        // $data['edit_clone'] = Salesorder_Child::where( 'salesorder_id', $data ['data']->id)->get();
        // $data['redirect_id'] = $data['payment']['salesorder_id'];
        // $data['amount_paid'] = Payment::where('salesorder_id',$id)->sum('amount');
        // $data['product'] = Product::get();
        // return view('general.edit_form')->with($data);
    }

    public function update(Request $request, $id)
    {
       
         $update_inventory = $request->update;
         $update_return = $request->return;
         // dd($request->updated);
        $qty = 0;
        foreach ($update_inventory as $key => $value) 
        {
            if($value != '')
            {
                
                $child = QuotationChild::where('id',$key)->first();
                $delivery = new Delivery();
                $delivery->product_id = $child->product_id;
                $delivery->quotation_id = $child->quotation_id;
                $deliverylog = Delivery::where('quotation_id',$id)->where('product_id',$child->product_id)->latest()->first();
                if($deliverylog)
                {
                $delivery->qty = $deliverylog->qty + $value;
                }
                else
                {
                $delivery->qty = $value;
                }
                $delivery->return = $deliverylog->return ?? 0;
                $delivery->used = $value;
                $delivery->date = now();
                $delivery->save();
            }
        
        }
        
                foreach ($update_inventory as $key => $value) {
                    if($value != '')
                    {
                        $child = QuotationChild::where('id',$key)->first();
                        $stock = WarehouseInventory::where('product_id',$child->product_id)->where('warehouse_id',$child->warehouse_id)->first();
                        // dd($stock);
                       $stock->used = $stock->used + $value;
                       $stock->save();
                    }
                }
                 foreach ($update_return as $key => $value) {
                    if($value != '')
                    {
                        $child = QuotationChild::where('id',$key)->first();
                        $stock = WarehouseInventory::where('product_id',$child->product_id)->where('warehouse_id',$child->warehouse_id)->first();
                        // dd($stock);
                       $stock->used = $stock->used - $value;
                       // dd($stock->used);
                       $stock->stock = $stock->stock + $value;
                       $stock->save();
                    }
                }
                foreach ($update_return as $key => $value) 
                {
                	if($value != '')
                	{
                		$child = QuotationChild::where('id',$key)->first();
                		$delivery = new Delivery();
                		$delivery->product_id = $child->product_id;
                		$delivery->quotation_id = $child->quotation_id;
                		$deliverylog = Delivery::where('quotation_id',$id)->where('product_id',$child->product_id)->latest()->first();
                // dd($deliverylog);
                		$delivery->return = $deliverylog->return + $value;
                		$delivery->qty = $deliverylog->qty;
                		$delivery->used = $delivery->qty;
                		$delivery->date = now();
                		$delivery->save();
                	}
        }
        return response()->json(['status'=>'success']);
    }

    public function show($id)
    {
        $data['id'] = $id;
        $data['revised'] = RevisedQuotation::where('id',$id)->first();
        $data['revisedchild'] = RevisedMainChild::where('revised_id',$data['revised']->id)->get();
        return view('revised.view')->with($data);
    }
}
