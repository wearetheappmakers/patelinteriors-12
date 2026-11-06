<?php

namespace App\Http\Controllers;
use DataTables;
use App\Payment;
use App\Delivery;
use App\Salesorder;
use App\QuotationChild;
use App\WarehouseInventory;
use App\Salesorder_Child;
use App\Payment_Child;

use Illuminate\Http\Request;

class DeliveryController extends Controller
{
    public function __construct(Delivery $s)
    {
        $this->view = 'delivery';
        $this->route = 'delivery';
        $this->viewName = 'Delivery';
    }

    public function index(Request $request,$id)
    {
        if ($request->ajax()) {
            $query = QuotationChild::where('quotation_id',$id)->get();
            
            return Datatables::of($query)
            // ->addColumn('action', function ($row) {
            //     $btn = view('layouts.actionbtnpermission')->with(['id'=>$row->id,'route'=>'delivery'])->render();           
            //     return $btn;
            // })

            // ->addColumn('singlecheckbox', function ($row) {

            //     $schk = view('layouts.singlecheckbox')->with(['id' => $row->id , 'status'=>$row->status])->render();

            //     return $schk;

            // })

            ->addColumn('product_id', function ($row) {
                $btn = $row->products->product;
                return $btn;
            })

            ->addColumn('used', function ($row) {


                $delivery = Delivery::where('quotation_id',$row->quotation_id)->where('product_id',$row->product_id)->latest()->first();
                if($delivery)
                {
                    $btn = $delivery->qty;

                }
                else
                {
                	$btn = '';
                }

                return $btn;
            })

            ->addColumn('return', function ($row) {

                $delivery = Delivery::where('quotation_id',$row->quotation_id)->where('product_id',$row->product_id)->latest()->first();
                if($delivery)
                {
                    $btn = $delivery->return;

                }
                else
                {
                	$btn = '';
                }

                return $btn;
            })


            ->addColumn('add_inventory', function ($row) {



              $btn = '<input type="number" class="form-control update" name="update['.$row->id.']" >';

              return $btn;

          })

            ->addColumn('update_return', function ($row) {



              $btn = '<input type="number" class="form-control return" name="return['.$row->id.']" >';

              return $btn;

          })

            ->rawColumns(['numbers','product_id','add_inventory','used','update_return','return'])
            ->make(true);
        } 
        $data['id'] = $id;


        return view('delivery.index')->with($data);
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


public function deliverylog(Request $request,$id)
{
    if ($request->ajax()) {
        $query = Delivery::where('quotation_id',$id)->get();

        return Datatables::of($query)
            // ->addColumn('action', function ($row) {
            //     $btn = view('layouts.actionbtnpermission')->with(['id'=>$row->id,'route'=>'delivery'])->render();           
            //     return $btn;
            // })

            // ->addColumn('singlecheckbox', function ($row) {

            //     $schk = view('layouts.singlecheckbox')->with(['id' => $row->id , 'status'=>$row->status])->render();

            //     return $schk;

            // })

        ->addColumn('product_id', function ($row) {
            $btn = $row->product->product;
            return $btn;
        })

        ->addColumn('date', function ($row) {
            $ac = date('d-m-Y',strtotime($row->date));
            return $ac;
        })

        ->addColumn('used', function ($row) {
            $delivery = Delivery::where('quotation_id',$row->quotation_id)->where('product_id',$row->product_id)->latest()->first();
            if($delivery)
            {
                $btn = $delivery->qty;
            }
            else
            {
                $btn = '';
            }

            return $btn;
        })

        ->editColumn('time',function($row){
            return date('h:i A', strtotime($row->date));

        })





        ->rawColumns(['numbers','product_id','used','date','time'])
        ->make(true);
    } 
    $data['id'] = $id;


    return view('delivery.log')->with($data);
}



}
