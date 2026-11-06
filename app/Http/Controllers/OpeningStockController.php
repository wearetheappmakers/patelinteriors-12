<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\SourceName;
use App\LeadSource;
use App\Employee;
use App\OpeningStock;
use App\OpeningStockChild;
use App\Warehouse;
use App\Product;
use App\Imports\OpeningStockImport;
use App\Imports\ChildStockImport;
use DataTables;
use App\User;
use Auth;
use Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
class OpeningStockController extends Controller
{
    public function __construct(OpeningStock $s)
    {
        $this->view = 'openingstock';
        $this->route = 'openingstock';
        $this->viewName = 'OpeningStock';
    }
    public function index(Request $request)
    {
        $data['id'] = 0;
        if(Auth::user()->role == 2)
        {
            $date=date('Y-m-d');
            if (date('m') >= 4) {
                $financial_year = (date('Y')) . '-' . (date('y')+1);
            } else {
                $financial_year = (date('Y')-1) . '-' . date('y');
            }
            $data['id'] = OpeningStock::where('branch_id',Auth::user()->id)->where('fy_year',$financial_year)->value('id');
        }
        if ($request->ajax())
        {
            // $query = OpeningStock::orderby('id','DESC')->latest();
            if(Auth::guard('user')->check())
            {
                if(Auth::user()->role == 2)
                {
                    $query = OpeningStock::where('branch_id',Auth::user()->id)->latest();
                }else{
                    $query = OpeningStock::orderby('id','DESC')->latest();
                }
            }
            return Datatables::of($query)
            ->addColumn('action', function ($row) {
                $date=date('Y-m-d');
                if (date('m') >= 4) {
                    $financial_year = (date('Y')) . '-' . (date('y')+1);
                } else {
                    $financial_year = (date('Y')-1) . '-' . date('y');
                }
                $btn = '<a style="background: transparent;" href="'.route('user.opening.stock.childexport',$row->id).'" title="Export Stock" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="fa fa-file-export"></i>
                </a>&nbsp;&nbsp;&nbsp;';
                // if($row->fy_year == $financial_year && Auth::user()->role == 2)
                // {
                //     $btn .= '<button  style="background: transparent;" title="Import Stock" id="childbutton" class="btn btn-sm btn-clean btn-icon btn-icon-md childbutton"><i style="color: blue;" class="fa fa-file-import"></i>
                //     </button>&nbsp;&nbsp;&nbsp;';
                // }
                return $btn;
            })
            ->addColumn('branch_id', function ($row) {
                if($row->branch_id)
                {
                    $schk = User::where('id',$row->branch_id)->value('name');
                }else
                {
                    $schk = '-';
                }
                return $schk;
            })
            ->rawColumns(['action','branch_id'])   
            ->make(true);
        }
        return view('openingstock.index')->with($data);
    }
    public function sample()
    {
        return Excel::download(new Sample, 'opening-stock-sample.xlsx');
    }
    public function import(Request $request) 
    {
        try
        {
            $import = Excel::import(new OpeningStockImport,request()->file('importfile'));
            return response()->json(['status'=>'success']);
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
    public function childimport(Request $request,$id) 
    {
        try
        {
            $param['id'] = $id;
            $import = Excel::import(new ChildStockImport($param),request()->file('childimportfile'));
            return response()->json(['status'=>'success']);
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
    public function childexport($id)
    {
        $param = $id;
        return Excel::download(new ChildExport($param), 'opening-stock-export.xlsx');
    }
}
class Sample implements WithHeadings
{
    public function headings(): array
    {
        return [
            'product',
            'warehouse',
            'quantity',
        ];
    }
}
class ChildExport implements FromCollection, WithHeadings
{
    private $param;
    function __construct($param)
    {
        $this->param = $param;
    }
    public function headings(): array
    {
        return [
            'product',
            'warehouse',
            'quantity',
        ];
    }
    public function collection()
    {
        $type = OpeningStockChild::where('os_id',$this->param)->get();
        foreach ($type as $key => $value) {
            $type[$key]['product'] = Product::where('id',$value['pro_id'])->value('code');
            $type[$key]['warehouse'] = Warehouse::where('id',$value['warehouse_id'])->value('name');
            $type[$key]['quantity'] = $value['qty'];
            unset($type[$key]['id'],$type[$key]['os_id'],$type[$key]['pro_id'],$type[$key]['warehouse_id'],$type[$key]['branch_id'],$type[$key]['qty'],$type[$key]['created_at'],$type[$key]['updated_at']);
        }
        return $type;
    }
}
