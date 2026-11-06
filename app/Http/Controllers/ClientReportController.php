<?php
namespace App\Http\Controllers;
use App\User;
use DataTables;
use App\Client;
use App\LeadSource;
use App\ClientDocument;
use App\Product;
use App\Lead;
use App\Employee;
use App\Landmark;
use Auth;
use App\ProductCategory;
use Illuminate\Http\Request;
use App\Exports\ClientReportExport;
use Excel;
class ClientReportController extends Controller
{
    public function client_report(Request $request)
    {
        if ($request->ajax()) {
            if(Auth::guard('user')->check())
            {
                if(Auth::user()->role == 2)
                {
                    $query = Client::where('branch_id',Auth::user()->id)->latest();
                }else
                {
                    $query = Client::latest();
                }
            }
            if($request->branch_id)
            {
                $query->where('branch_id',$request->branch_id);
            }
            if($request->employee_id)
            {
                $query->where('employee_id',$request->employee_id);
            }
            if($request->landmark_id)
            {
                $query->where('landmark_id',$request->landmark_id);
            }
            if($request->from_date)
            {
                $query->whereDate('created_at','>=',$request->from_date);
            }
            if($request->to_date)
            {
                $query->whereDate('created_at','<=',$request->to_date);
            }
            return Datatables::of($query)
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
            ->addColumn('employee_id', function ($row) {
                if($row->employee_id)
                {
                    $btn = Employee::where('id',$row->employee_id)->value('name');
                }
                else
                {
                    $btn = '';
                }
                return $btn;
            })
            ->addColumn('landmark_id', function ($row) {
                if($row->landmark_id)
                {
                    $btn = Landmark::where('id',$row->landmark_id)->value('name');
                }
                else
                {
                    $btn = '';
                }
                return $btn;
            })
            ->rawColumns(['numbers','action','singlecheckbox','branch_id','employee_id','landmark_id'])
            ->make(true);
        } 
        $data['branch'] = User::where('role',2)->get();
        return view('report.clientreport')->with($data);
    }

    public function client_report_export(Request $request)
    {
        $param = $request->all();
        return Excel::download(new ClientReportExport($param), 'client_report_export.xlsx');
    }
}
