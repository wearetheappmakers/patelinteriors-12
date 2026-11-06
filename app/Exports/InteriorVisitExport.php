<?php
namespace App\Exports;
use App\Visits;
use App\User;
use App\Employee;
use App\SourceName;
use Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
class InteriorVisitExport implements FromCollection,WithHeadings
{ 
    private $param;
    public function __construct($param)
    {
        $this->param = $param;
    }
    public function headings(): array
    {
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                return [
                  'branch',
                  'employee',
                  'architect',
                  'date',          
              ];
          }
          if(Auth::user()->role == 2)
          {
            return [
              'employee',
              'architect',
              'date',
          ];
      }
  }
}
public function collection()
{
    if(Auth::guard('user')->check())
    {
        if(Auth::user()->role == 1)
        {
            if($this->param['from_date'] && $this->param['to_date'])
            {
                $from = date('Y-m-d', strtotime($this->param['from_date']));
                $to = date('Y-m-d', strtotime($this->param['to_date']));
                $data = Visits::whereDate('date','>=',$from)->whereDate('date','<=',$to)->get();
                foreach($data as $key=>$value)
                {
                    $data[$key]['branch_id'] = User::where('id',$value->branch_id)->value('name');
                    $data[$key]['employee_id'] = Employee::where('id',$value->employee_id)->value('name');
                    $data[$key]['architect_id'] = SourceName::where('id',$value->architect_id)->value('name');
                    $data[$key]['date'] = date('d/m/Y', strtotime($value->date));  
                    unset($data[$key]['id'],$data[$key]['pmc_id'],$data[$key]['assistant'],$data[$key]['company_name'],$data[$key]['address'],$data[$key]['email'],$data[$key]['number'],$data[$key]['remarks'],$data[$key]['company_person'],$data[$key]['status'],$data[$key]['created_at'],$data[$key]['updated_at']);
                }
            }
            elseif($this->param['branch_id'] && $this->param['employee_id'] && $this->param['source_name'])
            {
                $data = Visits::where('branch_id',$this->param['branch_id'])->where('employee_id',$this->param['employee_id'])->where('architect_id',$this->param['source_name'])->get();
                foreach($data as $key=>$value)
                {
                    $data[$key]['branch_id'] = User::where('id',$value->branch_id)->value('name');
                    $data[$key]['employee_id'] = Employee::where('id',$value->employee_id)->value('name');
                    $data[$key]['architect_id'] = SourceName::where('id',$value->architect_id)->value('name');
                    $data[$key]['date'] = date('d/m/Y', strtotime($value->date));  
                    unset($data[$key]['id'],$data[$key]['pmc_id'],$data[$key]['assistant'],$data[$key]['company_name'],$data[$key]['address'],$data[$key]['email'],$data[$key]['number'],$data[$key]['remarks'],$data[$key]['company_person'],$data[$key]['status'],$data[$key]['created_at'],$data[$key]['updated_at']);
                }
            }
            elseif($this->param['branch_id'] && $this->param['employee_id'])
            {
                $data = Visits::where('branch_id',$this->param['branch_id'])->where('employee_id',$this->param['employee_id'])->get();
                foreach($data as $key=>$value)
                {
                    $data[$key]['branch_id'] = User::where('id',$value->branch_id)->value('name');
                    $data[$key]['employee_id'] = Employee::where('id',$value->employee_id)->value('name');
                    $data[$key]['architect_id'] = SourceName::where('id',$value->architect_id)->value('name');
                    $data[$key]['date'] = date('d/m/Y', strtotime($value->date));  
                    unset($data[$key]['id'],$data[$key]['pmc_id'],$data[$key]['assistant'],$data[$key]['company_name'],$data[$key]['address'],$data[$key]['email'],$data[$key]['number'],$data[$key]['remarks'],$data[$key]['company_person'],$data[$key]['status'],$data[$key]['created_at'],$data[$key]['updated_at']);
                }
            }
            elseif($this->param['branch_id'] && $this->param['source_name'])
            {
                $data = Visits::where('branch_id',$this->param['branch_id'])->where('architect_id',$this->param['source_name'])->get();
                foreach($data as $key=>$value)
                {
                    $data[$key]['branch_id'] = User::where('id',$value->branch_id)->value('name');
                    $data[$key]['employee_id'] = Employee::where('id',$value->employee_id)->value('name');
                    $data[$key]['architect_id'] = SourceName::where('id',$value->architect_id)->value('name');
                    $data[$key]['date'] = date('d/m/Y', strtotime($value->date));  
                    unset($data[$key]['id'],$data[$key]['pmc_id'],$data[$key]['assistant'],$data[$key]['company_name'],$data[$key]['address'],$data[$key]['email'],$data[$key]['number'],$data[$key]['remarks'],$data[$key]['company_person'],$data[$key]['status'],$data[$key]['created_at'],$data[$key]['updated_at']);
                }
            }
            elseif($this->param['branch_id'])
            {
                $data = Visits::where('branch_id',$this->param['branch_id'])->get();
                foreach($data as $key=>$value)
                {
                    $data[$key]['branch_id'] = User::where('id',$value->branch_id)->value('name');
                    $data[$key]['employee_id'] = Employee::where('id',$value->employee_id)->value('name');
                    $data[$key]['architect_id'] = SourceName::where('id',$value->architect_id)->value('name');
                    $data[$key]['date'] = date('d/m/Y', strtotime($value->date));  
                    unset($data[$key]['id'],$data[$key]['pmc_id'],$data[$key]['assistant'],$data[$key]['company_name'],$data[$key]['address'],$data[$key]['email'],$data[$key]['number'],$data[$key]['remarks'],$data[$key]['company_person'],$data[$key]['status'],$data[$key]['created_at'],$data[$key]['updated_at']);
                }
            }
            else
            {
                $data = Visits::get();
                foreach($data as $key=>$value)
                {
                    $data[$key]['branch_id'] = User::where('id',$value->branch_id)->value('name');
                    $data[$key]['employee_id'] = Employee::where('id',$value->employee_id)->value('name');
                    $data[$key]['architect_id'] = SourceName::where('id',$value->architect_id)->value('name');
                    $data[$key]['date'] = date('d/m/Y', strtotime($value->date));  
                    unset($data[$key]['id'],$data[$key]['pmc_id'],$data[$key]['assistant'],$data[$key]['company_name'],$data[$key]['address'],$data[$key]['email'],$data[$key]['number'],$data[$key]['remarks'],$data[$key]['company_person'],$data[$key]['status'],$data[$key]['created_at'],$data[$key]['updated_at']);
                }
            }
        }
    }
    if(Auth::guard('user')->check())
    {
        if(Auth::user()->role == 2)
        {
            if($this->param['from_date'] && $this->param['to_date'])
            {
                $from = date('Y-m-d', strtotime($this->param['from_date']));
                $to = date('Y-m-d', strtotime($this->param['to_date']));
                $data = Visits::whereDate('date','>=',$from)->whereDate('date','<=',$to)->where('branch_id',Auth::user()->id)->get();
                foreach($data as $key=>$value)
                {
                    $data[$key]['employee_id'] = Employee::where('id',$value->employee_id)->value('name');
                    $data[$key]['architect_id'] = SourceName::where('id',$value->architect_id)->value('name');
                    $data[$key]['date'] = date('d/m/Y', strtotime($value->date));  
                    unset($data[$key]['id'],$data[$key]['branch_id'],$data[$key]['pmc_id'],$data[$key]['assistant'],$data[$key]['company_name'],$data[$key]['address'],$data[$key]['email'],$data[$key]['number'],$data[$key]['remarks'],$data[$key]['company_person'],$data[$key]['status'],$data[$key]['created_at'],$data[$key]['updated_at']);
                }
            }
            elseif($this->param['employee_id'] && $this->param['source_name'])
            {
                $data = Visits::where('employee_id',$this->param['employee_id'])->where('architect_id',$this->param['source_name'])->where('branch_id',Auth::user()->id)->get();
                foreach($data as $key=>$value)
                {
                    $data[$key]['employee_id'] = Employee::where('id',$value->employee_id)->value('name');
                    $data[$key]['architect_id'] = SourceName::where('id',$value->architect_id)->value('name');
                    $data[$key]['date'] = date('d/m/Y', strtotime($value->date));  
                    unset($data[$key]['id'],$data[$key]['branch_id'],$data[$key]['pmc_id'],$data[$key]['assistant'],$data[$key]['company_name'],$data[$key]['address'],$data[$key]['email'],$data[$key]['number'],$data[$key]['remarks'],$data[$key]['company_person'],$data[$key]['status'],$data[$key]['created_at'],$data[$key]['updated_at']);
                }
            }
            elseif($this->param['employee_id'])
            {
                $data = Visits::where('employee_id',$this->param['employee_id'])->where('branch_id',Auth::user()->id)->get();
                foreach($data as $key=>$value)
                {
                    $data[$key]['employee_id'] = Employee::where('id',$value->employee_id)->value('name');
                    $data[$key]['architect_id'] = SourceName::where('id',$value->architect_id)->value('name');
                    $data[$key]['date'] = date('d/m/Y', strtotime($value->date));  
                    unset($data[$key]['id'],$data[$key]['branch_id'],$data[$key]['pmc_id'],$data[$key]['assistant'],$data[$key]['company_name'],$data[$key]['address'],$data[$key]['email'],$data[$key]['number'],$data[$key]['remarks'],$data[$key]['company_person'],$data[$key]['status'],$data[$key]['created_at'],$data[$key]['updated_at']);
                }
            }
            elseif($this->param['source_name'])
            {
                $data = Visits::where('architect_id',$this->param['source_name'])->where('branch_id',Auth::user()->id)->get();
                foreach($data as $key=>$value)
                {
                    $data[$key]['employee_id'] = Employee::where('id',$value->employee_id)->value('name');
                    $data[$key]['architect_id'] = SourceName::where('id',$value->architect_id)->value('name');
                    $data[$key]['date'] = date('d/m/Y', strtotime($value->date));  
                    unset($data[$key]['id'],$data[$key]['branch_id'],$data[$key]['pmc_id'],$data[$key]['assistant'],$data[$key]['company_name'],$data[$key]['address'],$data[$key]['email'],$data[$key]['number'],$data[$key]['remarks'],$data[$key]['company_person'],$data[$key]['status'],$data[$key]['created_at'],$data[$key]['updated_at']);
                }
            }
            else
            {
                $data = Visits::where('branch_id',Auth::user()->id)->get();
                foreach($data as $key=>$value)
                {
                    $data[$key]['employee_id'] = Employee::where('id',$value->employee_id)->value('name');
                    $data[$key]['architect_id'] = SourceName::where('id',$value->architect_id)->value('name');
                    $data[$key]['date'] = date('d/m/Y', strtotime($value->date));  
                    unset($data[$key]['id'],$data[$key]['branch_id'],$data[$key]['pmc_id'],$data[$key]['assistant'],$data[$key]['company_name'],$data[$key]['address'],$data[$key]['email'],$data[$key]['number'],$data[$key]['remarks'],$data[$key]['company_person'],$data[$key]['status'],$data[$key]['created_at'],$data[$key]['updated_at']);
                }
            }
        }
    }
    return $data;
}
}
