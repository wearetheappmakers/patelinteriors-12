<?php

namespace App\Http\Controllers;
use App\Warehouse;
use App\Product;
use App\WarehouseInventory;
use DataTables;
use Illuminate\Http\Request;
use Auth;
use App\User;
use App\Employee;

class WarehouseController extends Controller
{
    public function __construct(Warehouse $s)
    {
        $this->view = 'warehouse';
        $this->route = 'warehouse';
        $this->viewName = 'Warehouse';

    }

    public function index(Request $request)
    {
        if ($request->ajax())
        { 
            if(Auth::guard('employee')->check())
            {
                $query = Warehouse::where('branch_id',Auth::user()->branch_id);
            }
            else
            {
                if(Auth::user()->role == 2)
                {
                    $query = Warehouse::where('branch_id',Auth::user()->id);
                }
                else
                {
                    $query = Warehouse::orderby('id','DESC');
                }
            }
            return Datatables::of($query)
            ->addColumn('action', function ($row) {
                $btn = '<a style="background: transparent;" href="'.route('user.warehouse.edit',$row->id).'" title="Edit details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: green;" class="la la-edit"></i>
                </a>&nbsp;&nbsp;&nbsp;';


                $btn .= '<a style="background: white;" href="'.route('user.warehouse.show',$row->id).'"  title="View Details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-eye"></i></a>&nbsp;&nbsp;';


                if(Auth::user()->role == 1)
                {
                    $btn .= '<a href="'.route('user.deletewarehouse',$row->id).'" onclick="return confirm(`Are you sure?`)" style="background: white;" title="Delete details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: red;" class="la la-trash"></i></a>';

                }
                return $btn;
            })

            ->rawColumns(['action'])
            ->make(true);
        }
        return view('warehouse.index');
    }

    public function create()
    {
        $data['title'] = 'Add ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        return view('general.add_form')->with($data);
    }

    public function store(Request $request)
    {
        $warehouse = new Warehouse();
        $warehouse->name = $request->name;
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $warehouse->branch_id = $request->branch_id;
            }
            else
            {
                $warehouse->branch_id = Auth::user()->id;
            }

        }
        else
        {
            $warehouse->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
        }
        $warehouse->save();

        $product = Product::get();
        foreach ($product as $key => $value) {
            $inventory =  new WarehouseInventory();
            $inventory->branch_id = $warehouse->branch_id;
            $inventory->warehouse_id = $warehouse->id;
            $inventory->product_id = $value->id;
            $inventory->stock = 0;
            $inventory->used = 0;
            $inventory->actual_stock = 0;
            $inventory->save();
        }

        if ($warehouse){
            return response()->json(['status'=>'success']);
        }else{
            return response()->json(['status'=>'error']);
        }
    }

    public function show($id)
    {
        $data['title'] = 'View ' . $this->viewName;
        $data['data'] = Warehouse::where('id', $id)->first();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        return view('warehouse.show')->with($data);
    }

    public function edit($id)
    {
        $data['title'] = 'Edit ' . $this->viewName;
        $data['data'] = Warehouse::where('id', $id)->first();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        return view('general.edit_form')->with($data);
    }

    public function update(Request $request, $id)
    {
        $warehouse = Warehouse::where('id',$id)->first();
        $warehouse->name = $request->name;
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $warehouse->branch_id = $request->branch_id;
            }
            else
            {
                $warehouse->branch_id = Auth::user()->id;
            }

        }
        else
        {
            $warehouse->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
        }        $warehouse->save();

        if ($warehouse){
            return response()->json(['status'=>'success']);
        }else{
            return response()->json(['status'=>'error']);
        }
    }

    public function deletewarehouse($id)
    {
        $data = Warehouse::where('id',$id)->delete();
        return back();
    }
}
