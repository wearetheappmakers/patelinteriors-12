<?php
namespace App\Http\Controllers;
use App\Warehouse;
use App\Product;
use App\WarehouseInventory;
use DataTables;
use App\OpeningStock;
use App\OpeningStockChild;
use Illuminate\Http\Request;
use App\ProductCategory;
use App\Exports\InventoryExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Company;
use Auth;
use App\StockTransferHistory;
use Illuminate\Support\Facades\DB;
use App\User;
class InventoryController extends Controller
{
    public function __construct(WarehouseInventory $s)
    {
        $this->view = 'warehouse';
        $this->route = 'warehouse.inventory';
        $this->viewName = 'Inventory';
    }
    public function index(Request $request)
    {
        if ($request->ajax())
        {
            // if(Auth::user()->role == 2)
            // {
            //     $query = WarehouseInventory::with('warehouse','product')->where('branch_id',Auth::user()->id)->latest();
            // }else{
            //     $query = WarehouseInventory::with('warehouse','product')->orderby('id','DESC')->latest();
            // }
            $query = WarehouseInventory::with('warehouse','product');

            if(Auth::guard('user')->check())
            {
                if(Auth::user()->role == 2)
                {
                    $query = $query->where('branch_id',Auth::user()->id)->latest();
                } else {
                    $query->latest();
                }
            }

            if(Auth::guard('employee')->check())
            {
                $query = $query->where('branch_id',Auth::user()->branch_id)->latest();
            }

            if($request->branch_id)
            {
                $query = $query->where('branch_id',$request->branch_id);
            }
            if($request->product_id)
            {
                $query = $query->where('product_id',$request->product_id);
            }
            if($request->warehouse_id)
            {
                $query = $query->where('warehouse_id',$request->warehouse_id);
            }
            if($request->productcategory_id)
            {
                $productcategory_id = $request->productcategory_id;
                $query = $query->with('product')
                ->whereHas('product', function ($query) use($productcategory_id){
                    $query->where('productcategory_id', $productcategory_id);
                });
            }
            if($request->company_id)
            {
                $company_id = $request->company_id;
                $query = $query->with('product')
                ->whereHas('product', function ($query) use($company_id){
                    $query->where('company_id', $company_id);
                });
            }
            return Datatables::of($query)
            ->addColumn('add_inventory', function ($row) {
                $btn = '<input type="number" class="form-control update" name="update['.$row->id.']" onchange="stockUpdate(`input`)">';
                return $btn;
            })
            ->addColumn('actual_inventory', function ($row) {
                $btn = $row->stock - $row->used - $row->si_blocking_qty;
                return $btn;
            })
            ->addColumn('si_blocking_qty', function ($row) {  
                return $row->si_blocking_qty ?? 0;
            })

            ->addColumn('stock', function ($row) {
                $date=date('Y-m-d');
                if (date('m') >= 4) {
                    $financial_year = (date('Y')) . '-' . (date('y')+1);
                } else {
                    $financial_year = (date('Y')-1) . '-' . date('y');
                }
                
                $main = OpeningStock::where('fy_year',$financial_year)->first();
                $opening_stock = 0; 
                if($main)
                {
                    $opening = OpeningStockChild::where('pro_id',$row->product_id)->where('warehouse_id',$row->warehouse_id)->where('branch_id',$row->branch_id)->where('os_id',$main->id)->first();
                    if($opening)
                    {
                        $opening_stock = $opening->qty;
                    }
                }
                $btn = $row->stock - $opening_stock;
                return $btn;
            })
            ->addColumn('warehouse_id', function ($row) {
                $btn = Warehouse::where('id',$row->warehouse_id)->value('name');
                return $btn;
            })
            ->editColumn('product_nm', function($row){
                return '<a href="'.route('user.stock.report', [
                    'product_id' => $row->product_id,
                    'warehouse_id' => $row->warehouse_id
                ]).'" target="_blank" title="View Stock Report" style="color:dodgerblue;font-weight:bold;">'
                .$row->product->product.
                '</a>';
            })
            ->filterColumn('product_nm', function($query, $keyword) {
                $query->whereHas('product', function ($q) use ($keyword) {
                    $q->where('product', 'like', "%{$keyword}%");
                });
            })

            ->editColumn('productcode', function($row){
                return '<a href="'.route('user.stock.report', [
                    'product_id' => $row->product_id,
                    'warehouse_id' => $row->warehouse_id
                ]).'" target="_blank" title="View Stock Report" style="color:dodgerblue;font-weight:bold;">'
                .$row->product->code.
                '</a>';
            })
            ->filterColumn('productcode', function($query, $keyword) {
                $query->whereHas('product', function ($q) use ($keyword) {
                    $q->where('code', 'like', "%{$keyword}%");
                });
            })

            ->addColumn('category_ids',function($row)
            {
                if($row->product)
                {
                    $btn = $row->product->productcategory->name;
                }else{
                    $btn = '';
                }
                return $btn;
            })
            ->addColumn('company_ids',function($row)
            {
                if($row->product)
                {
                    $btn = $row->product->company->name;
                }else{
                    $btn = '';
                }
                return $btn;
            })
            ->addColumn('product_code', function ($row) {
                if($row->product_id)
                {
                    if($row->product->code)
                    {
                        $btn = $row->product->code;
                    }else
                    {
                        $btn = '';
                    }
                }
                else
                {
                    $btn = '';
                }
                return $btn;
            })
            ->addColumn('opening_stock', function ($row) {
                $date=date('Y-m-d');
                if (date('m') >= 4) {
                    $financial_year = (date('Y')) . '-' . (date('y')+1);
                } else {
                    $financial_year = (date('Y')-1) . '-' . date('y');
                }
                $btn = 0;
                $main = OpeningStock::where('fy_year',$financial_year)->first();
                if($main)
                {
                    $opening = OpeningStockChild::where('pro_id',$row->product_id)->where('warehouse_id',$row->warehouse_id)->where('branch_id',$row->branch_id)->where('os_id',$main->id)->first();
                    if($opening)
                    {
                        $btn = $opening->qty;
                    }
                }

                return $btn;
            })
            ->addColumn('branch_id', function ($row) {
                $btn = User::where('id',$row->branch_id)->where('role',2)->where('status',1)->value('name');
                return $btn;
            })
            // ->addColumn('action', function ($row) {

            //     if(Auth::guard('user')->check())
            //     {
            //         if(Auth::user()->role != 1)
            //         {
            //             return '<a href="javascript:void(0);"
            //             class="btn btn-sm btn-clean btn-icon stock-transfer"
            //             data-id="'.$row->id.'"
            //             data-warehouse-id="'.$row->warehouse_id.'"
            //             data-warehouse-name="'.$row->warehouse->name.'">
            //             <i class="fa fa-exchange-alt"></i>
            //             </a>';
            //         }
            //     }
            // })
            ->addColumn('action', function ($row) {

                $canTransfer = false;

                // Branch user
                if (
                    Auth::guard('user')->check() &&
                    Auth::guard('user')->user()->role != 1
                ) {
                    $canTransfer = true;
                }

                // Employee except role 2 and 3
                if (
                    Auth::guard('employee')->check() &&
                    !in_array(Auth::guard('employee')->user()->role, [2, 3])
                ) {
                    $canTransfer = true;
                }

                if ($canTransfer) {
                    return '
                        <a href="javascript:void(0);"
                            class="btn btn-sm btn-clean btn-icon stock-transfer"
                            data-id="'.$row->id.'"
                            data-warehouse-id="'.$row->warehouse_id.'"
                            data-warehouse-name="'.$row->warehouse->name.'">
                            <i class="fa fa-exchange-alt"></i>
                        </a>
                    ';
                }

                return '';
            })
            ->rawColumns(['company_ids','category_ids','add_inventory','actual_inventory','si_blocking_qty','warehouse_id','product_id','branch_id','product_code','company_id','opening_stock','stock','product_nm','productcode','action'])
            ->make(true);
        }
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        return view('warehouse.inventory')->with($data);
    }
    public function create()
    {
    }
    public function store(Request $request)
    {
    }
    public function show($id)
    {
    }
    public function edit($id)
    {
    }
    public function update(Request $request)
    {
        $update_inventory = $request->update;
        foreach ($update_inventory as $key => $value) 
        {
            if($value != '')
            {
                $stock = WarehouseInventory::where('id',$key)->value('stock');
                $inventory = $value + $stock;
                $updated = WarehouseInventory::where('id',$key)
                ->update(['stock'=>$inventory]);                
            }
        }
        return response()->json(['status'=>'success']);
    }
    public function destroy($id)
    {
    }

    public function closing_stock(Request $request)
    {
        if ($request->ajax())
        {
            $query = WarehouseInventory::with('product');

            if(Auth::guard('user')->check())
            {
                if(Auth::user()->role == 2)
                {
                    $query = $query->where('branch_id',Auth::user()->id)->latest();
                }
                else
                {
                    $query->latest();
                }
            }

            if(Auth::guard('employee')->check())
            {
                $query = $query->where('branch_id',Auth::user()->branch_id)->latest();
            }

            if($request->branch_id)
            {
                $query = $query->where('branch_id',$request->branch_id);
            }
            if($request->product_id)
            {
                $query = $query->where('product_id',$request->product_id);
            }
            if($request->productcategory_id)
            {
                $productcategory_id = $request->productcategory_id;
                $query = $query->with('product')
                ->whereHas('product', function ($query) use($productcategory_id){
                    $query->where('productcategory_id', $productcategory_id);
                });
            }
            if($request->company_id)
            {
                $company_id = $request->company_id;
                $query = $query->with('product')
                ->whereHas('product', function ($query) use($company_id){
                    $query->where('company_id', $company_id);
                });
            }
            return Datatables::of($query)
            ->addColumn('actual_inventory', function ($row) {
                $btn = $row->stock - $row->used;
                return $btn;
            })
            ->addColumn('product_id', function ($row) {
                if($row->product)
                {
                    $btn = $row->product->product;
                }
                else
                {
                    $btn = '';
                }
                return $btn;
            })
            ->addColumn('category_ids',function($row)
            {
                if($row->product)
                {
                    $btn = $row->product->productcategory->name;
                }else{
                    $btn = '';
                }
                return $btn;
            })
            ->addColumn('company_ids',function($row)
            {
                if($row->product)
                {
                    $btn = $row->product->company->name;
                }else{
                    $btn = '';
                }
                return $btn;
            })
            ->addColumn('product_code', function ($row) {
                if($row->product_id)
                {
                    if($row->product->code)
                    {
                        $btn = $row->product->code;
                    }else
                    {
                        $btn = '';
                    }
                }
                else
                {
                    $btn = '';
                }
                return $btn;
            })
            ->addColumn('branch_id', function ($row) {
                $btn = User::where('id',$row->branch_id)->where('role',2)->where('status',1)->value('name');
                return $btn;
            })
            ->rawColumns(['company_ids','category_ids','actual_inventory','product_id','branch_id','product_code','company_id'])
            ->make(true);
        }
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        
        return view('warehouse.closing_stock')->with($data);
    }

    public function getTransferWarehouse(Request $request)
    {
        $inventoryQuery = WarehouseInventory::with('warehouse')
            ->where('id', $request->inventory_id);

        // Employee login
        if (Auth::guard('employee')->check()) {

            $employeeBranchId = Auth::guard('employee')->user()->branch_id;

            $inventoryQuery->where('branch_id', $employeeBranchId);
        }

        $inventory = $inventoryQuery->firstOrFail();

        $warehouses = Warehouse::where('branch_id', $inventory->branch_id)
            ->where('id', '!=', $inventory->warehouse_id)
            ->where('status', 1)
            ->get();

        return response()->json([
            'from_id'    => $inventory->warehouse_id,
            'from_name'  => $inventory->warehouse->name,
            'warehouses' => $warehouses
        ]);
    }


    public function transferStock(Request $request)
    {
        $request->validate([
            'inventory_id'    => 'required',
            'to_warehouse_id' => 'required',
            'qty'             => 'required|numeric|min:1',
        ]);

        try {

            DB::transaction(function () use ($request) {

                $employeeId = Auth::guard('employee')->check()
                ? Auth::guard('employee')->user()->id
                : null;

                // From Warehouse Inventory
                // $fromInventory = WarehouseInventory::findOrFail($request->inventory_id);
                $inventoryQuery = WarehouseInventory::where(
                    'id',
                    $request->inventory_id
                );

                // If employee login, restrict to employee branch
                if (Auth::guard('employee')->check()) {

                    $employeeBranchId = Auth::guard('employee')->user()->branch_id;

                    $inventoryQuery->where(
                        'branch_id',
                        $employeeBranchId
                    );
                }

                $fromInventory = $inventoryQuery->firstOrFail();

                // Available Stock
                $availableQty = $fromInventory->stock
                - $fromInventory->used
                - $fromInventory->si_blocking_qty;

                if ($availableQty < $request->qty) {
                    throw new \Exception('Not enough stock available');
                }

                // Minus Stock
                $fromInventory->stock = $fromInventory->stock - $request->qty;
                $fromInventory->save();


                // To Warehouse Inventory
                $toInventory = WarehouseInventory::where([
                    'branch_id'    => $fromInventory->branch_id,
                    'warehouse_id' => $request->to_warehouse_id,
                    'product_id'   => $fromInventory->product_id,
                ])->first();


                if ($toInventory) {

                    $toInventory->stock = $toInventory->stock + $request->qty;
                    $toInventory->save();

                } else {

                    WarehouseInventory::create([
                        'branch_id'       => $fromInventory->branch_id,
                        'warehouse_id'    => $request->to_warehouse_id,
                        'product_id'      => $fromInventory->product_id,
                        'stock'           => $request->qty,
                        'used'            => 0,
                        'si_blocking_qty' => 0,
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | Stock Transfer History - OUTWARD
                |--------------------------------------------------------------------------
                */

                StockTransferHistory::create([
                    'transfer_date'     => $request->transfer_date,
                    'branch_id'         => $fromInventory->branch_id,
                    'from_warehouse_id' => $fromInventory->warehouse_id,
                    'to_warehouse_id'   => $request->to_warehouse_id,
                    'product_id'        => $fromInventory->product_id,
                    'employee_id'       => $employeeId,
                    'qty'               => $request->qty,
                    'inward'            => 0,
                    'outward'           => $request->qty,
                    'remark'           => $request->remark,
                ]);


                /*
                |--------------------------------------------------------------------------
                | Stock Transfer History - INWARD
                |--------------------------------------------------------------------------
                */

                StockTransferHistory::create([
                    'transfer_date'     => $request->transfer_date,
                    'branch_id'         => $fromInventory->branch_id,
                    'from_warehouse_id' => $fromInventory->warehouse_id,
                    'to_warehouse_id'   => $request->to_warehouse_id,
                    'product_id'        => $fromInventory->product_id,
                    'employee_id'       => $employeeId,
                    'qty'               => $request->qty,
                    'inward'            => $request->qty,
                    'outward'           => 0,
                    'remark'           => $request->remark,
                ]);

            });

            return response()->json([
                'status'  => 'success',
                'message' => 'Stock transferred successfully'
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 422);

        }
    }
    public function export(Request $request)
    {
        $branch_id = $request->branch_id;

        // Logged-in user guard
        if (Auth::guard('user')->check()) {

            $user = Auth::guard('user')->user();

            // Branch user
            if ($user->role == 2) {
                $branch_id = $user->id;
            }
        }

        // Logged-in employee guard
        if (Auth::guard('employee')->check()) {

            $employee = Auth::guard('employee')->user();

            $branch_id = $employee->branch_id;
        }

        // Super admin must select branch
        if (!$branch_id) {
            return redirect()->back()->with(
                'error',
                'Please select a branch.'
            );
        }

        return Excel::download(
            new InventoryExport(
                $branch_id,
                $request->warehouse_id,
                $request->product_id,
                $request->productcategory_id,
                $request->company_id
            ),
            'Inventory_' . date('Ymd_His') . '.xlsx'
        );
    }
}