<?php
namespace App\Exports;
use App\StockChild;
use App\Lead;
use App\Category;
use App\Employee;
use App\ProductCategory;
use App\LeadSource;
use App\LeadAssign;
use App\SourceName;
use App\User;
use App\Type;
use App\Client;
use App\LeadFollowUp;
use App\Company;
use App\LeadBasicMainChild;
use Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
class LeadExport implements FromCollection,WithHeadings
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
               if(Auth::user()->role == 2)
               {
                    return [
                         'employee',
                         'client',
                         'category',
                         'company',
                         'lead_date',
                         'source',
                         'source_name',
                         'followup_description',
                         'followup_subject',
                         'followup_type',
                         'followup_status',
                         'lead_status',
                         'showroom'
                    ];
               }else
               {
                    return [
                         'branch',
                         'employee',
                         'client',
                         'category',
                         'company',
                         'lead_date',
                         'source',
                         'sourcename',
                         'followup_description',
                         'followup_subject',
                         'followup_type',
                         'followup_status',
                         'lead_status',
                         'showroom'
                    ];
               }
          }
          else
          {
               return [
                    'client',
                    'category',
                    'company',
                    'lead_date',
                    'source',
                    'sourcename',
                    'followup_description',
                    'followup_subject',
                    'followup_type',
                    'followup_status',
                    'lead_status',
                    'showroom'
               ];
          }
     }
     public function collection()
     {
          $array = [];
          if(Auth::guard('employee')->check())
          {
               $query = LeadFollowUp::leftJoin('lead as l','l.id','lead_followup.lead_id')->where('l.active',1)->where('lead_followup.employee_id',Auth::user()->id);
          }
          else
          {
               $query = LeadFollowUp::leftJoin('lead as l','l.id','lead_followup.lead_id')->where('l.active',1);
               if(Auth::user()->role == 2)
               {
                    $query = LeadFollowUp::leftjoin('lead as l','l.id','lead_followup.lead_id')->where('l.active',1)->where('l.branch_id',Auth::user()->id);
               }
          }
          if(Auth::guard('user')->check())
          { 
               if(Auth::user()->role == 1)
               {
                    if($this->param['branch_id'])
                    {
                         $query->where('l.branch_id',$this->param['branch_id']);
                    }
                    if($this->param['employee_id'])
                    {
                         $query->where('lead_followup.employee_id',$this->param['employee_id']);
                    }
               }else
               {
                    if($this->param['employee_id'])
                    {
                         $query->where('lead_followup.employee_id',$this->param['employee_id']);
                    }
               }
          }
          if($this->param['client_id'])
          {
               $query->where('l.client_id',$this->param['client_id']);
          }
          if($this->param['from_date'])
          {
               $query->where('l.date','>=',$this->param['from_date']);
          }
          if($this->param['to_date'])
          {
               $query->where('l.date','<=',$this->param['to_date']);
          }
          if($this->param['source_id'])
          {
               $query->where('l.source_id',$this->param['source_id']);
          }
          if($this->param['source_name'])
          {
               $query->where('l.source_name',$this->param['source_name']);
          }
          if($this->param['lead_name'])
          {
               $data = $query->select('lead_followup.*')->get();
               $array_id = [];
               foreach($data as $dt)
               {
                    $lead = Lead::where('id',$dt->lead_id)->first();
                    $co_co = explode(',', $lead->lead_name);
                    foreach($co_co as $co)
                    {
                         if($co == $this->param['lead_name'])
                         {
                              array_push($array_id, $dt->id);
                         }
                    }
               }
               $query->whereIn('lead_followup.id',$array_id);
          } 
          if($this->param['company_name'])
          {
               $data = $query->select('lead_followup.*')->get();
               $array_id = [];
               foreach($data as $dt)
               {
                    $lead = Lead::where('id',$dt->lead_id)->first();
                    $co_co = explode(',', $lead->company_name);
                    foreach($co_co as $co)
                    {
                         if($co == $this->param['company_name'])
                         {
                              array_push($array_id, $dt->id);
                         }
                    }
               }
               $query->whereIn('lead_followup.id',$array_id);
          }
          $query = $query->select('lead_followup.*')->get();

          foreach($query as $main_key => $value)
          {
               $lead = Lead::where('id',$value->lead_id)->first();
               if(isset($lead->branch_id))
               {
                    $branch = User::where('id',$lead->branch_id)->value('name');
                    $branch_nm = $branch;
               }else{
                    $branch_nm = ' ';
               }

               if(isset($lead))
               {
                    $lead_assign = LeadAssign::where('lead_id',$lead->id)->first();
                    $emp_arr = [];
                    if(isset($lead_assign))
                    {
                         $expplode = explode(',',$lead_assign->employee_id);
                         $emp = Employee::whereIn('id',$expplode)->get();
                         if(count($emp) > 0)
                         {
                              foreach ($emp as $key => $value)
                              {
                                   array_push($emp_arr,$value->name);
                              }
                         }
                    }
                    $employee_implode = implode(', ', $emp_arr);
               }
               else
               {
                    $employee_implode = '';
               }

               if(isset($lead->lead_name))
               {
                    $category = explode(',', $lead->lead_name);
                    $category_arr = [];
                    foreach ($category as $keys => $values) {
                         $ca = ProductCategory::where('id',$values)->value('name');
                         array_push($category_arr, $ca);
                    }  
                    $category_nm = implode(',', $category_arr);
               }
               else
               {
                    $category_nm = '';
               }

               if(isset($lead->company_name))
               {
                    $company_array = [];
                    $company = explode(',', $lead->company_name);
                    foreach ($company as $keys => $values) {
                         $com = Company::where('id',$values)->value('name');
                         array_push($company_array, $com);
                    }
                    $company_nm = implode(',', $company_array);
               }
               else
               {
                    $company_nm = '';
               }

               if(isset($lead))
               {
                    $lead_assign = LeadAssign::where('lead_id',$lead->id)->first();
                    if(isset($lead_assign))
                    {
                         $exp = explode(',',$lead_assign->employee_id);
                         $emp = Employee::whereIn('id',$exp)->get();
                         $f_status_arr = [];
                         foreach ($emp as $keys => $values) {
                              $followup = LeadFollowUp::where('lead_id',$lead->id)->where('employee_id',$values->id)->first();
                              if(isset($followup))
                              {
                                   if ($followup->is_completed == 1) {
                                        $f_status = 'Pending';
                                   }elseif ($followup->is_completed == 2) {
                                        $f_status = 'Discussion';
                                   }elseif ($followup->is_completed == 3) {
                                        $f_status = 'Rivision';
                                   }elseif ($followup->is_completed == 4) {
                                        $f_status = 'Win';
                                   }elseif ($followup->is_completed == 5) {
                                        $f_status = 'Lost';
                                   }elseif ($followup->is_completed == 6) {
                                        $f_status = "Showroom Visit";
                                   }elseif ($followup->is_completed == 7) {
                                        $f_status = "Site Measurement";
                                   }elseif ($followup->is_completed == 8) {
                                        $f_status = "Designing";
                                   }elseif ($followup->is_completed == 9) {
                                        $f_status = "Quotation";
                                   }else{
                                        $f_status = "Pending";
                                   }
                              }
                              else
                              {
                                   $f_status = 'Pending';
                              }
                              $combine_data = $values->name .' - ' .$f_status;
                              array_push($f_status_arr,$combine_data);
                         }
                         $f_status_implode = implode(' - ',$f_status_arr);
                    }
                    else
                    {
                         $f_status_implode = '-';
                    }
               }
               else
               {
                    $f_status_implode = '-';
               }

               if(isset($lead->status))
               {
                    if($lead->status == 1)
                    {
                         $status = 'Pending';
                    }elseif ($lead->status == 2) {
                         $status = 'Discussion';
                    }elseif ($lead->status == 3) {
                         $status = 'Rivision';
                    }elseif ($lead->status == 4) {
                         $status = 'Win';
                    }elseif ($lead->status == 5) {
                         $status = 'Lost';
                    }
               }else{
                    $status = 'Pending';
               }

               if(isset($lead->showroom))
               {
                    if($lead->showroom == 1)
                    {
                         $showroom = 'Yes';
                    }else{
                         $showroom = 'No';
                    }
               }else{
                    $showroom = '';
               }

               if(Auth::guard('user')->check())
               {
                    if(Auth::user()->role == 2)
                    {
                         $param = [
                              'employee' => $employee_implode,
                              'client' => isset($lead->client_id) ? Client::where('id',$lead->client_id)->value('member') : NULL,
                              'category' => $category_nm,
                              'company' => $company_nm,
                              'lead_date' => isset($lead->date) ? (date('d-m-Y', strtotime($lead->date))) : NULL,
                              'source' => isset($lead->source_id) ? LeadSource::where('id',$lead->source_id)->value('name') : NULL,
                              'source_name' => isset($lead->source_name) ? SourceName::where('id',$lead->source_name)->value('name') : NULL,
                              'followup_description' => $query[$main_key]['description'],
                              'followup_subject' => $query[$main_key]['subject'],
                              'followup_type' => ($query[$main_key]['type']) ? Type::where('id',$query[$main_key]['type'])->value('name') : NULL,
                              'followup_status' => $f_status_implode,
                              'lead_status' => $status,
                              'showroom' => $showroom,
                         ];
                    }
                    else
                    {
                         $param = [
                              'branch' => $branch_nm,
                              'employee' => $employee_implode,
                              'client' => isset($lead->client_id) ? Client::where('id',$lead->client_id)->value('member') : NULL,
                              'category' => $category_nm,
                              'company' => $company_nm,
                              'lead_date' => isset($lead->date) ? (date('d-m-Y', strtotime($lead->date))) : NULL,
                              'source' => isset($lead->source_id) ? LeadSource::where('id',$lead->source_id)->value('name') : NULL,
                              'source_name' => isset($lead->source_name) ? SourceName::where('id',$lead->source_name)->value('name') : NULL,
                              'followup_description' => $query[$main_key]['description'],
                              'followup_subject' => $query[$main_key]['subject'],
                              'followup_type' => ($query[$main_key]['type']) ? Type::where('id',$query[$main_key]['type'])->value('name') : NULL,
                              'followup_status' => $f_status_implode,
                              'lead_status' => $status,
                              'showroom' => $showroom,
                         ];
                    }
               }
               else
               {
                    $param = [
                         'client' => isset($lead->client_id) ? Client::where('id',$lead->client_id)->value('member') : NULL,
                         'category' => $category_nm,
                         'company' => $company_nm,
                         'lead_date' => isset($lead->date) ? (date('d-m-Y', strtotime($lead->date))) : NULL,
                         'source' => isset($lead->source_id) ? LeadSource::where('id',$lead->source_id)->value('name') : NULL,
                         'source_name' => isset($lead->source_name) ? SourceName::where('id',$lead->source_name)->value('name') : NULL,
                         'followup_description' => $query[$main_key]['description'],
                         'followup_subject' => $query[$main_key]['subject'],
                         'followup_type' => ($query[$main_key]['type']) ? Type::where('id',$query[$main_key]['type'])->value('name') : NULL,
                         'followup_status' => $f_status_implode,
                         'lead_status' => $status,
                         'showroom' => $showroom,
                    ];
               }
               array_push($array,$param);

          }
          return collect($array);
     }
}