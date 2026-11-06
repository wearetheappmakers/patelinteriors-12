<?php
namespace App\Exports;
use App\User;
use App\Client;
use App\Employee;
use App\Landmark;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DB;
use Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Excel;
use Carbon\Carbon;

class ClientReportExport implements FromView
{
    private $param;
    public function __construct($param)
    {
        $this->param = $param;
    }
    public function view(): View
    {
        $query = Client::latest();

        if(Auth::guard('user')->check() && Auth::user()->role == 2)
        {
            $query->where('branch_id', Auth::user()->id);
        }
        if(!empty($this->param['branch_id']))
        {
            $query->where('branch_id', $this->param['branch_id']);
        }
        if(!empty($this->param['employee_id']))
        {
            $query->where('employee_id',$this->param['employee_id']);
        }
        if(!empty($this->param['landmark_id']))
        {
            $query->where('landmark_id',$this->param['landmark_id']);
        }
        if(!empty($this->param['from_date']))
        {
            $query->whereDate('created_at','>=',$this->param['from_date']);
        }
        if(!empty($this->param['to_date']))
        {
            $query->whereDate('created_at','<=',$this->param['to_date']);
        }
        
        $clients = $query->get();

        return view('report.clientreportexport', [
            'clients' => $clients
        ]);
    }
}