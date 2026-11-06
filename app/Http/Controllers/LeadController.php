<?php
namespace App\Http\Controllers;
use App\User;
use DataTables;
use App\Lead;
use App\Arid;
use App\Client;
use App\LeadSource;
use App\Section;
use App\LeadStage;
use App\Employee;
use App\Symbol;
use App\LeadFollowUp;
use App\LeadBasicChild;
use App\LeadBasicMainChild;
use App\LeadBasicDesignChild;
use App\LeadBasicQuotationChild;
use App\Quotation;
use App\SourceName;
use App\Warehouse;
use App\WarehouseInventory;
use App\LeadCommunication;
use App\LeadAssign;
use App\Company;
use App\FollowupLog;
use App\Type;
use App\ProductCategory;
use App\Product;
use App\LeadToshow;
use App\ProjectType;
use App\ProjectName;
use Auth;
use DB;
use Mail;
use File;
use Illuminate\Http\Request;
class LeadController extends Controller
{
    public function __construct(Lead $s)
    {
        $this->view = 'lead';
        $this->route = 'lead';
        $this->viewName = 'Lead';
    }
    function sendnotification($pushtoken,$title,$body,$urls,$leads_id,$name)
    {
        $firebaseToken = [$pushtoken];
        $SERVER_API_KEY = env('Employee_server');
        $data = [
            "registration_ids" => $firebaseToken,
            "notification" => [
                "title" => $title,
                "body" => $body,
            ],
            "data" => [
                'urls' => $urls,
                'leads_id' => $leads_id,
                'name' => $name,
            ]
        ];
        $dataString = json_encode($data);
        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        $response = curl_exec($ch);
    }
    function adminsendnotification($pushtoken,$title,$body,$urls,$leads_id,$name)
    {
        $firebaseToken = [$pushtoken];
        $SERVER_API_KEY = env('Admin_server');
        $data = [
            "registration_ids" => $firebaseToken,
            "notification" => [
                "title" => $title,
                "body" => $body,
            ],
            "data" => [
                'urls' => $urls,
                'leads_id' => $leads_id,
                'name' => $name,
            ]
        ];
        $dataString = json_encode($data);
        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        $response = curl_exec($ch);
    }   
    public function index(Request $request)
    {
        if(Auth::guard('employee')->check())
        {
            if ($request->ajax()) {
                $data = Lead::with('client','lead_assign.stage','lead_assign.employee','sourcename','source')->where('active',1)->orderBy('date','DESC');
                $array = [];
                foreach($data as $d)
                {
                    $explode = explode(',',$d->employee_id);
                    if(in_array(Auth::user()->id,$explode))
                    {
                        array_push($array,$d->id);
                    }
                }
                $data = LeadAssign::get();
                $arr = [];
                foreach($data as $dt)
                {
                    $ex_arr = explode(',', $dt->employee_id);
                    if(in_array(Auth::user()->id,$ex_arr))
                    {
                        array_push($array,$dt->lead_id);
                    }
                }
                $query = Lead::orderBy('date','DESC')->with('client','lead_assign.stage','lead_assign.employee','sourcename','source')
                ->orWhereIn('id',$array)->where('active',1)->latest();
                if($request->employee_id)
                {
                    $query->where('employee_id',$request->employee_id);
                }
                if($request->branch_id)
                {
                    $query->where('branch_id',$request->branch_id);
                }
                if($request->status)
                {
                    $query->where('status',$request->status);
                }
                if($request->source_id)
                {
                    $query->where('source_id',$request->source_id);
                }
                if($request->client_id)
                {
                    $query->where('client_id',$request->client_id);
                }
                if($request->lead_name)
                {
                    $data = $query;
                    $data = $data->get();
                    $array_id = [];
                    foreach($data as $dt)
                    {
                        $co_co = explode(',', $dt['lead_name']);
                        foreach($co_co as $co)
                        {
                            if($co == $request->lead_name)
                            {
                                array_push($array_id, $dt->id);
                            }
                        }
                    }
                    $query->whereIn('id',$array_id);
                }
                if($request->company_name)
                {
                    $data = $query;
                    $data = $data->get();
                    $array_id = [];
                    foreach($data as $dt)
                    {
                        $co_co = explode(',', $dt['company_name']);
                        foreach($co_co as $co)
                        {
                            if($co == $request->company_name)
                            {
                                array_push($array_id, $dt->id);
                            }
                        }
                    }
                    $query->whereIn('id',$array_id);
                }
                if($request->source_name)
                {
                    $query->where('source_name',$request->source_name);
                }
                if($request->arid)
                {
                    $query->where('arid_id',$request->arid);
                }
                if($request->from_date && $request->to_date)
                {
                    $query->where('date','>=',$request->from_date)->where('date','<=',$request->to_date);
                }

                $query = $query->latest();
                return Datatables::of($query)
                ->addColumn('action', function ($row) {
                    $btn = '<a style="background: white;" href="'.route('user.lead.edit',$row->id).'" title="Edit details" class="btn btn-sm btn-clean btn-icon btn-icon-md">
                    <i style="color: green;" class="la la-edit"></i>
                    </a>&nbsp;&nbsp;
                    <button style="background: white;" onclick="model('.$row->id.')" title="Followup" class="btn btn-sm btn-clean btn-icon btn-icon-md " >
                    <i style="color: orange;" class="fa fa-arrow-up"></i>
                    </button>&nbsp;&nbsp;';
                    $btn .= '<a style="background: white;" href="'.route('user.lead.show',$row->id).'"  title="View Details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-eye"></i></a>&nbsp;&nbsp;';
                    $flag = 1;
                    $checkers = Quotation::where('lead_id',$row->id)->first();
                    if(isset($checkers))
                    {
                        $flag = 0;
                    }
                    if($flag){
                        $btn .= '<a style="background: white;" href="'. route('user.quotation.create', $row->id).'" title="Quotation" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-envelope"></i></a>';
                    }
                    return $btn;
                })
                ->addColumn('date', function ($row) {
                    $ac = '<a href="'.route("user.lead.edit",$row->id).'" style="color: black;">'.date('d-m-Y',strtotime($row->date)).'</a>';
                    return $ac;
                })
                ->addColumn('lead_name', function ($row) {
                    $exp = explode(',',$row->lead_name);
                    $emp = ProductCategory::whereIn('id',$exp)->where('status',1)->get();
                    $arr = [];
                    foreach ($emp as $key => $value) {
                        array_push($arr,$value->name);
                    }
                    $lead_name = implode(', ',$arr);
                    $btn = '<a href="'.route("user.lead.edit",$row->id).'" style="color: black;">'.$lead_name.'</a>';
                    return $btn;
                })
                ->addColumn('company_name', function ($row) {
                    $exp = explode(',',$row->company_name);
                    $emp = Company::whereIn('id',$exp)->where('status',1)->get();
                    $arr = [];
                    foreach ($emp as $key => $value) {
                        array_push($arr,$value->name);
                    }
                    $company_name = implode(', ',$arr);
                    $btn = '<a href="'.route("user.lead.edit",$row->id).'" style="color: black;">'.$company_name.'</a>';
                    return $btn;
                })
                ->addColumn('id', function ($row) {
                    $btn = '<a href="'.route("user.lead.edit",$row->id).'" style="color: black;">'.$row->id.'</a>';
                    return $btn;
                })
                ->addColumn('branch_id', function ($row) {
                    $btn = '<a href="'.route("user.lead.edit",$row->id).'" style="color: black;">'.$row->branch->name.'</a>';
                    return $btn;
                })
                ->editColumn('source_name', function ($row) {
                    $btn = SourceName::where('id',$row->source_name)->value('name');
                    return $btn;
                })
                ->editColumn('arid_id', function ($row) {
                    $btn = Arid::where('id',$row->arid_id)->value('name');
                    return $btn;
                })
                ->addColumn('employee_id', function ($row) {
                    $leads = LeadAssign::where('lead_id',$row->id)->first();
                    if($leads)
                    {
                        $exp = explode(',',$leads->employee_id);
                        $emp = Employee::whereIn('id',$exp)->get();
                        $arr = [];
                        foreach ($emp as $key => $value) {
                            array_push($arr,$value->name);
                        }
                        $btn = implode(', ',$arr);
                    }
                    else
                    {
                        $btn = '';
                    }

                    return $btn;

                })
                ->addColumn('status', function ($row) {
                    $btn = '';
                    if ($row->status == 1) {
                        $btn = 'Pending';
                    }elseif ($row->status == 2) {
                        $btn = 'Discussion';
                    }elseif ($row->status == 3) {
                        $btn = 'Rivision';
                    }elseif ($row->status == 4) {
                        $btn = 'Win';
                    }elseif ($row->status == 5) {
                        $btn = 'Lost';
                    }
                    return $btn;
                })
                ->addColumn('followup_status', function ($row) {
                    $followup = LeadFollowUp::where('lead_id',$row->id)->where('employee_id',Auth::user()->id)->first();
                    if($followup)
                    {
                        $date = date('d/m/Y', strtotime($followup->date));
                        $status = '';
                        if ($followup->is_completed == 1) {
                            $status = 'Pending';
                        }elseif ($followup->is_completed == 2) {
                            $status = 'Discussion';
                        }elseif ($followup->is_completed == 3) {
                            $status = 'Rivision';
                        }elseif ($followup->is_completed == 4) {
                            $status = 'Win';
                        }elseif ($followup->is_completed == 5) {
                            $status = 'Lost';
                        }
                        elseif ($followup->is_completed == 6) {
                            $status = "Showroom Visit";
                        }elseif ($followup->is_completed == 7) {
                            $status = "Site Measurement";
                        }elseif ($followup->is_completed == 8) {
                            $status = "Designing";
                        }elseif ($followup->is_completed == 9) {
                            $status = "Quotation";
                        } 
                    }
                    else
                    {
                        $date = '-';
                        $status = '-';
                    }
                    $data = $status;
                    $btn = $data;
                    return $btn;
                })
                ->addColumn('yesterday_followup', function ($row) {
                    $yesterday = date('Y-m-d',strtotime("-1 days"));
                    $followup = LeadFollowUp::where('lead_id',$row->id)->where('employee_id',Auth::user()->id)->whereDate('created_at',$yesterday)->latest()->first();
                    $arr = [];
                    if($followup)
                    {
                        $btn = Auth::user()->name .' - Yes' ;
                    }
                    else
                    {
                        $btn = Auth::user()->name .' - No' ;
                    }

                    return $btn;
                })
                ->rawColumns(['status','numbers','action','client_id','source_id','lead_name','branch_id','id','employee_id','source_id','date','source_name','followup_status','company_name','yesterday_followup'])
                ->make(true);
            } 
        }
        else
        {
            if ($request->ajax()) 
            {
                if(Auth::user()->role == 2)
                {
                    $query = Lead::orderBy('date','DESC')->with('client','lead_assign.stage','lead_assign.employee','sourcename','source')->where('branch_id',Auth::user()->id)->where('active',1);
                    if($request->employee_id)
                    {
                        $data = Lead::with('client','lead_assign.stage','lead_assign.employee','sourcename','source')->where('active',1)->orderBy('date','DESC')->latest();
                        $array = [];
                        foreach($data as $d)
                        {
                            $explode = explode(',',$d->employee_id);
                            if(in_array($request->employee_id,$explode))
                            {
                                array_push($array,$d->id);
                            }
                        }
                        $data = LeadAssign::get();
                        $arr = [];
                        foreach($data as $dt)
                        {
                            $ex_arr = explode(',', $dt->employee_id);
                            if(in_array($request->employee_id,$ex_arr))
                            {
                                array_push($array,$dt->lead_id);
                            }
                        }
                        $query = $query->whereIn('id',$array);
                    }
                    if($request->branch_id)
                    {
                        $query = $query->where('branch_id',$request->branch_id);
                    }
                    if($request->status)
                    {
                        $query = $query->where('status',$request->status);
                    }
                    if($request->source_id)
                    {
                        $query->where('source_id',$request->source_id);
                    }
                    if($request->client_id)
                    {
                        $query->where('client_id',$request->client_id);
                    }
                    if($request->lead_name)
                    {
                        $data = $query;
                        $data = $data->get();
                        $array_id = [];
                        foreach($data as $dt)
                        {
                            $co_co = explode(',', $dt['lead_name']);
                            foreach($co_co as $co)
                            {
                                if($co == $request->lead_name)
                                {
                                    array_push($array_id, $dt->id);
                                }
                            }
                        }
                        $query = $query->whereIn('id',$array_id);
                    }
                    if($request->company_name)
                    {
                        $data = $query;
                        $data = $data->get();
                        $array_id = [];
                        foreach($data as $dt)
                        {
                            $co_co = explode(',', $dt['company_name']);
                            foreach($co_co as $co)
                            {
                                if($co == $request->company_name)
                                {
                                    array_push($array_id, $dt->id);
                                }
                            }
                        }
                        $query = $query->whereIn('id',$array_id);
                    }
                    if($request->source_name)
                    {
                        $query->where('source_name',$request->source_name);
                    }
                    if($request->arid)
                    {
                        $query->where('arid_id',$request->arid);
                    }
                    if($request->from_date)
                    {
                        $query = $query->where('date','>=',$request->from_date);
                    }
                    if($request->to_date)
                    {
                        $query = $query->where('date','<=',$request->to_date);
                    }
                    $query = $query->latest();
                }else{
                    $query = Lead::orderBy('date','DESC')->with('client','lead_assign.stage','lead_assign.employee','sourcename','source')->where('active',1)->latest();
                    if($request->employee_id)
                    {
                        $data = Lead::with('client','lead_assign.stage','lead_assign.employee','sourcename','source')->where('active',1)->orderBy('date','DESC')->latest();
                        $array = [];
                        foreach($data as $d)
                        {
                            $explode = explode(',',$d->employee_id);
                            if(in_array($request->employee_id,$explode))
                            {
                                array_push($array,$d->id);
                            }
                        }
                        $data = LeadAssign::get();
                        $arr = [];
                        foreach($data as $dt)
                        {
                            $ex_arr = explode(',', $dt->employee_id);
                            if(in_array($request->employee_id,$ex_arr))
                            {
                                array_push($array,$dt->lead_id);
                            }
                        }
                        $query = $query->whereIn('id',$array);
                    }
                    if($request->branch_id)
                    {
                        $query = $query->where('branch_id',$request->branch_id);
                    }
                    if($request->status)
                    {
                        $query = $query->where('status',$request->status);
                    }
                    if($request->source_id)
                    {
                        $query->where('source_id',$request->source_id);
                    }
                    if($request->client_id)
                    {
                        $query->where('client_id',$request->client_id);
                    }
                    if($request->source_name)
                    {
                        $query->where('source_name',$request->source_name);
                    }
                    if($request->arid)
                    {
                        $query->where('arid_id',$request->arid);
                    }
                    if($request->lead_name)
                    {
                        $data = $query;
                        $data = $data->get();
                        $array_id = [];
                        foreach($data as $dt)
                        {
                            $co_co = explode(',', $dt['lead_name']);
                            foreach($co_co as $co)
                            {
                                if($co == $request->lead_name)
                                {
                                    array_push($array_id, $dt->id);
                                }
                            }
                        }
                        $query = $query->whereIn('id',$array_id);
                    }
                    if($request->company_name)
                    {
                        $data = $query;
                        $data = $data->get();
                        $array_id = [];
                        foreach($data as $dt)
                        {
                            $co_co = explode(',', $dt['company_name']);
                            foreach($co_co as $co)
                            {
                                if($co == $request->company_name)
                                {
                                    array_push($array_id, $dt->id);
                                }
                            }
                        }
                        $query = $query->whereIn('id',$array_id);
                    }
                    if($request->from_date)
                    {
                        $query = $query->where('date','>=',$request->from_date);
                    }
                    if($request->to_date)
                    {
                        $query = $query->where('date','<=',$request->to_date);
                    }
                    $query = $query->latest();
                }
                return Datatables::of($query)
                ->addColumn('action', function ($row) {
                    $btn = '<a style="background: white;" href="'.route('user.lead.edit',$row->id).'" title="Edit details" class="btn btn-sm btn-clean btn-icon btn-icon-md">
                    <i style="color: green;" class="la la-edit"></i>
                    </a>&nbsp;&nbsp;';
                    $btn .= '&nbsp;&nbsp;<a style="background: white;" href="'.route('user.lead.show',$row->id).'"  title="View Details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-eye"></i></a>';
                    if(Auth::user()->role == 1)
                    {
                        $btn .= '<button style="background: white;" title="Delete" data-id="'.$row->id.'" class="btn btn-sm btn-clean btn-icon btn-icon-md delete-record">
                        <i style="color: red;" class="la la-trash">
                        </i></button>&nbsp;&nbsp;';
                    }
                    $btn .='<button style="background: white;" onclick="model('.$row->id.')" title="Followup" class="btn btn-sm btn-clean btn-icon btn-icon-md " >
                    <i style="color: orange;" class="fa fa-arrow-up"></i>
                    </button>&nbsp;&nbsp;
                    <a style="background: white;" href="'.route('user.lead.close',$row->id).'" title="Close Lead" class="btn btn-sm btn-clean btn-icon btn-icon-md">
                    <i style="color: black;" class="flaticon2-cancel"></i>
                    </a>&nbsp;&nbsp;';
                    $checkers = Quotation::where('lead_id',$row->id)->first();
                    $flag = 1;
                    if(isset($checkers))
                    {
                        $flag = 0;
                    }
                    if($flag){
                        $btn .= '<a style="background: white;" href="'. route('user.quotation.create', $row->id).'" title="Quotation" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-envelope"></i></a>';
                    }
                    return $btn;
                })
                ->addColumn('date', function ($row) {
                    if($row->date)
                    {
                        $ac = '<a href="'.route("user.lead.edit",$row->id).'" style="color: black;">'.date('d-m-Y',strtotime($row->date)).'</a>';
                    }
                    else
                    {
                        $ac = '-';
                    }
                    return $ac;
                })
                ->addColumn('lead_name', function ($row) {
                    $exp = explode(',',$row->lead_name);
                    $emp = ProductCategory::whereIn('id',$exp)->where('status',1)->get();
                    $arr = [];
                    foreach ($emp as $key => $value) {
                        array_push($arr,$value->name);
                    }
                    $lead_name = implode(', ',$arr);
                    $btn = '<a href="'.route("user.lead.edit",$row->id).'" style="color: black;">'.$lead_name.'</a>';
                    return $btn;
                })
                ->addColumn('company_name', function ($row) {
                    $exp = explode(',',$row->company_name);
                    $emp = Company::whereIn('id',$exp)->where('status',1)->get();
                    $arr = [];
                    foreach ($emp as $key => $value) {
                        array_push($arr,$value->name);
                    }
                    $company_name = implode(', ',$arr);
                    $btn = '<a href="'.route("user.lead.edit",$row->id).'" style="color: black;">'.$company_name.'</a>';
                    return $btn;
                })
                ->addColumn('id', function ($row) {
                    $btn = '<a href="'.route("user.lead.edit",$row->id).'" style="color: black;">'.$row->id.'</a>';
                    return $btn;
                })
                ->editColumn('source_name', function ($row) {
                    $btn = SourceName::where('id',$row->source_name)->value('name');
                    return $btn;
                })
                ->editColumn('arid_id', function ($row) {
                    $btn = Arid::where('id',$row->arid_id)->value('name');
                    return $btn;
                })
                ->addColumn('branch_id', function ($row) {
                    $btn = '<a href="'.route("user.lead.edit",$row->id).'" style="color: black;">'.$row->branch->name.'</a>';
                    return $btn;
                })
                ->addColumn('employee_id', function ($row) {
                    if(Auth::user()->role == 1)
                    {
                        $leads = LeadAssign::where('lead_id',$row->id)->first();
                        if($leads)
                        {
                            $exp = explode(',',$leads->employee_id);
                            $emp = Employee::whereIn('id',$exp)->get();
                            $arr = [];

                            foreach ($emp as $keys => $values) {
                                $name = LeadBasicMainChild::where('lead_id',$row->id)->where('employee_id',$values->id)->first();
                                if($name)
                                {
                                    $n = $name->name;
                                }
                                else
                                {
                                    $n = '';
                                }
                                $followup = LeadFollowUp::where('lead_id',$row->id)->where('employee_id',$values->id)->first();
                                if($followup)
                                {
                                    $date = date('d/m/Y', strtotime($followup->date));
                                    $status = '';
                                    if ($followup->is_completed == 1) {
                                        $status = 'Pending';
                                    }elseif ($followup->is_completed == 2) {
                                        $status = 'Discussion';
                                    }elseif ($followup->is_completed == 3) {
                                        $status = 'Rivision';
                                    }elseif ($followup->is_completed == 4) {
                                        $status = 'Win';
                                    }elseif ($followup->is_completed == 5) {
                                        $status = 'Lost';
                                    }
                                    elseif ($followup->is_completed == 6) {
                                        $status = "Showroom Visit";
                                    }elseif ($followup->is_completed == 7) {
                                        $status = "Site Measurement";
                                    }elseif ($followup->is_completed == 8) {
                                        $status = "Designing";
                                    }elseif ($followup->is_completed == 9) {
                                        $status = "Quotation";
                                    } 
                                }
                                else
                                {
                                    $date = '-';
                                    $status = '-';
                                }
                                $data = $values->name ;
                                array_push($arr,$data);
                            }
                            $btn = implode(', <br>',$arr);
                        }
                        else
                        {
                            $btn = '-';
                        }
                    }
                    else
                    {
                        $leads = LeadAssign::where('lead_id',$row->id)->first();
                        if($leads)
                        {
                            $exp = explode(',',$leads->employee_id);
                            $emp = Employee::whereIn('id',$exp)->get();
                            $arr = [];
                            foreach ($emp as $key => $value) {
                                array_push($arr,$value->name);
                            }
                            $btn = implode(', <br>',$arr);
                        }
                        else
                        {
                            $btn = '';
                        }
                    }

                    return $btn;
                })
                ->addColumn('status', function ($row) {
                    $btn = '';
                    if ($row->status == 1) {
                        $btn = 'Pending';
                    }elseif ($row->status == 2) {
                        $btn = 'Discussion';
                    }elseif ($row->status == 3) {
                        $btn = 'Rivision';
                    }elseif ($row->status == 4) {
                        $btn = 'Win';
                    }elseif ($row->status == 5) {
                        $btn = 'Lost';
                    }
                    return $btn;
                })
                ->addColumn('followup_status', function ($row) {
                    $leads = LeadAssign::where('lead_id',$row->id)->first();
                    $exp = explode(',',$leads->employee_id);
                    $emp = Employee::whereIn('id',$exp)->get();
                    $arr = [];

                    foreach ($emp as $keys => $values) {
                        $followup = LeadFollowUp::where('lead_id',$row->id)->where('employee_id',$values->id)->first();
                        if($followup)
                        {
                            $date = date('d/m/Y', strtotime($followup->date));
                            $status = '';
                            if ($followup->is_completed == 1) {
                                $status = 'Pending';
                            }elseif ($followup->is_completed == 2) {
                                $status = 'Discussion';
                            }elseif ($followup->is_completed == 3) {
                                $status = 'Rivision';
                            }elseif ($followup->is_completed == 4) {
                                $status = 'Win';
                            }elseif ($followup->is_completed == 5) {
                                $status = 'Lost';
                            }
                            elseif ($followup->is_completed == 6) {
                                $status = "Showroom Visit";
                            }elseif ($followup->is_completed == 7) {
                                $status = "Site Measurement";
                            }elseif ($followup->is_completed == 8) {
                                $status = "Designing";
                            }elseif ($followup->is_completed == 9) {
                                $status = "Quotation";
                            } 
                        }
                        else
                        {
                            $date = '-';
                            $status = '-';
                        }
                        $data = $values->name .' - ' .$status;
                        array_push($arr,$data);
                    }
                    $btn = implode(', <br>',$arr);
                    return $btn;
                })
                ->addColumn('yesterday_followup', function ($row) {
                    $leads = LeadAssign::where('lead_id',$row->id)->first();
                    $exp = explode(',',$leads->employee_id);
                    $emp = Employee::whereIn('id',$exp)->get();
                    $arr = [];

                    foreach ($emp as $keys => $values) {
                        $yesterday = date('Y-m-d',strtotime("-1 days"));
                        $followup = FollowupLog::where('lead_id',$row->id)->where('employee_id',$values->id)->whereDate('created_at',$yesterday)->latest()->first();
                        if($followup)
                        {
                            $data = $values->name .' - Yes' ;
                        }
                        else
                        {
                            $data = $values->name .' - No' ;
                        }
                        array_push($arr, $data);
                    }
                    $btn = implode(', <br>',$arr);
                    return $btn;
                })
                ->rawColumns(['numbers','action','client_id','source_id','lead_name','branch_id','id','employee_id','source_id','date','source_name','followup_status','company_name','yesterday_followup'])
                ->make(true);
            }
        }
        $data['product'] = Product::get();
        $data['stage'] = LeadStage::get();
        if(Auth::user()->role == 1)
        {
            $data['employee'] = Employee::get();
        }
        if(Auth::guard('employee')->check())
        {
            $data['source'] = LeadSource::where('status',1)->where('branch_id',Auth::user()->branch_id)->get();
            $data['sourcename'] = SourceName::where('status',1)->where('branch_id',Auth::user()->branch_id)->get();
            $data['client'] = Client::where('branch_id',Auth::user()->branch_id)->get();
            $data['productcategory'] = ProductCategory::where('branch_id',Auth::user()->branch_id)->get();
            $data['company'] = Company::where('branch_id',Auth::user()->branch_id)->get();
            $data['arids'] = Arid::where('branch_id',Auth::user()->branch_id)->orderBy('name')->get();
        }
        $data['source'] = LeadSource::get();
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        $data['employee'] = Employee::where('status',1)->get();
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 2)
            {
                $data['employee'] = Employee::where('status',1)->where('branch_id',Auth::user()->id)->get();
                $data['source'] = LeadSource::where('status',1)->where('branch_id',Auth::user()->id)->get();
                $data['sourcename'] = SourceName::where('status',1)->where('branch_id',Auth::user()->id)->get();
                $data['client'] = Client::where('branch_id',Auth::user()->id)->get();
                $data['productcategory'] = ProductCategory::where('branch_id',Auth::user()->id)->get();
                $data['company'] = Company::where('branch_id',Auth::user()->id)->get();
                $data['arids'] = Arid::where('branch_id',Auth::user()->id)->orderBy('name')->get();
            }
        }
        return view('lead.index')->with($data);
    }
    public function create()
    {
        $data['title'] = 'Add ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        if(Auth::guard('employee')->check())
        {
            $branch = Employee::where('id',Auth::user()->id)->value('branch_id');
            $lead = Lead::where('branch_id',$branch)->pluck('client_id')->toArray();
            $data['client'] = Client::whereNotIn('id',$lead)->where('branch_id',$branch)->get();
            $data['company'] = Company::where('status',1)->where('branch_id',Auth::user()->branch_id)->get();
            $data['productcategory'] = ProductCategory::where('status',1)->where('branch_id',Auth::user()->branch_id)->get();
            $data['sourcename'] = SourceName::where('status',1)->where('branch_id',Auth::user()->branch_id)->get();
            $data['employees'] = Employee::where('id',Auth::user()->id)->where('status',1)->get();
            $data['source'] = LeadSource::where('status',1)->where('branch_id',Auth::user()->branch_id)->get();
            $data['symbol'] = Symbol::where('status',1)->where('branch_id',Auth::user()->branch_id)->get();
            $data['project_type'] = ProjectType::where('branch_id',Auth::user()->branch_id)->get();
            $data['project_name'] = ProjectName::where('branch_id',Auth::user()->branch_id)->get();
            $data['arids'] = Arid::where('branch_id',Auth::user()->branch_id)->orderBy('name')->get();
        }
        else
        {
            $data['client'] = Client::get();
            if(Auth::user()->role == 2)
            {
                $lead = Lead::where('branch_id',Auth::user()->id)->pluck('client_id')->toArray();
                $data['client'] = Client::whereNotIn('id',$lead)->where('branch_id',Auth::user()->id)->get();
                $data['company'] = Company::where('status',1)->where('branch_id',Auth::user()->id)->get();
                $data['productcategory'] = ProductCategory::where('status',1)->where('branch_id',Auth::user()->id)->get();
                $data['sourcename'] = SourceName::where('status',1)->where('branch_id',Auth::user()->id)->get();
                $data['source'] = LeadSource::where('status',1)->where('branch_id',Auth::user()->id)->get();
                $data['employees'] = Employee::where('branch_id',Auth::user()->id)->where('status',1)->get();
                $data['symbol'] = Symbol::where('status',1)->where('branch_id',Auth::user()->id)->get();
                $data['project_type'] = ProjectType::where('branch_id',Auth::user()->id)->get();
                $data['project_name'] = ProjectName::where('branch_id',Auth::user()->id)->get();
                $data['arids'] = Arid::where('branch_id',Auth::user()->id)->orderBy('name')->get();
            }
        }
        return view('lead.create')->with($data);
    }
    public function store(Request $request)
    {
        if($request->form_value == 'basic')
        {
            $lead = new Lead();
            $lead->client_id = $request->client_id;
            $lead->lead_name = $request->lead_name;
            $lead->description = $request->description;
            $lead->source_id = $request->source_id;
            $lead->productcategory_id = $request->productcategory_id;
            $lead->product_id = $request->product_id;
            $lead->source_name = $request->source_name;
            $lead->no_of_machines = $request->no_of_machines;
            $lead->date = $request->date;
            $lead->current_project_phase = $request->current_project_phase;
            $lead->lead_value = $request->lead_value;
            $lead->status = $request->status ?? 1;
            $lead->showroom = $request->showroom ?? 2;
            $lead->app_link = "https://patelinteriorsadmin.page.link?utm_campaign=Lead&link=https%3A%2F%2Fpatelinteriorsadmin.page.link";
            $lead->save();
        }
        if($lead)
        {
            return response()->json(['status' => 'success']);
        }else
        {
            return response()->json(['status' => 'error']);
        }
    }
    public function leadbasic(Request $request)
    {
        if($request->status == 5)
        {
            if($request->reason == '')
            {
                return response()->json(['status' => 'required', 'message' => 'Reason Required !!!']);
            }
        }

        if ($request->name) {
            foreach ($request->name as $validate_key => $validate_val) {
                if ($validate_val != '')
                {
                    if (!empty($request['productcategory_id'][$validate_key])) {
                        foreach ($request['productcategory_id'][$validate_key] as $validate_pc_key => $validate_pc_val) {
                            if (!empty($validate_pc_val))
                            {
                                if(empty($request->hidden_child[$validate_key][$validate_pc_key]))
                                {
                                    if(empty($request->company_id[$validate_key][$validate_pc_key]))
                                    {
                                        return response()->json(['status' => 'required', 'message' => 'Company Required for '.$validate_val.' !!!']);
                                    }

                                    if(empty($request->product_id[$validate_key][$validate_pc_key]))
                                    {
                                        return response()->json(['status' => 'required', 'message' => 'Product Required for '.$validate_val.' !!!']);
                                    }

                                    if(empty($request->warehouse_id[$validate_key][$validate_pc_key]))
                                    {
                                        return response()->json(['status' => 'required', 'message' => 'Warehouse Required for '.$validate_val.' !!!']);
                                    }

                                    if(empty($request->qty[$validate_key][$validate_pc_key]))
                                    {
                                        return response()->json(['status' => 'required', 'message' => 'Qty Required for '.$validate_val.' !!!']);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $check = Lead::where('client_id',$request->client_id)->first();
        if(isset($check))
        {
            $assign = LeadAssign::where('lead_id',$check->id)->first();
            if(isset($assign))
            {
                $employee = explode(',', $assign->employee_id);
                $array = [];
                foreach ($employee as $key => $value) {
                    $data = Employee::where('id',$value)->value('name');
                    array_push($array, $data);
                }
                $datas = implode(',', $array);
                if($check){
                    return response()->json(['status' => 'required', 'data' => $datas, 'message' => 'Lead already exists for '.$datas.'!!!']);
                }
            }
        }


        $lead = new Lead();
        $lead->client_id = $request->client_id;
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $lead->branch_id = $request->branch_id;
                $lead->employee_id = $request->employee_id;
            }
            else
            {
                $lead->branch_id = Auth::user()->id;
                $lead->employee_id = $request->employee_id;
            }
        }
        else
        {
            $lead->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
            $lead->employee_id = Auth::user()->id;
        }
        $lead->project_type_id = $request->project_type_id;
        $lead->project_name_id = $request->project_name_id;
        $lead->arid_id = $request->arid_id;

        if($request->lead_name != NULL){
            $params = implode(",",$request->lead_name);
            $lead['lead_name'] = $params;
        }else{
            $lead['lead_name'] = NULL;
        }
        if($request->company_name != NULL){
            $param = implode(",",$request->company_name);
            $lead['company_name'] = $param;
        }else{
            $lead['company_name'] = NULL;
        }
        $lead->status = $request->status ?? 1;
        
        $lead->reason = $request->reason;
        $lead->description = $request->description;
        $lead->source_id = $request->source_id;
        $lead->source_name = $request->source_name;
        $lead->no_of_machines = $request->no_of_machines;
        $lead->date = $request->date;
        $lead->current_project_phase = $request->current_project_phase;
        $lead->lead_value = $request->value;
        $lead->showroom = $request->showroom ?? 2;
        $lead->app_link = "https://patelinteriorsadmin.page.link?utm_campaign=Lead&link=https%3A%2F%2Fpatelinteriorsadmin.page.link";
        $lead->save();

        $lead_assign = new LeadAssign();
        $lead_assign->lead_id = $lead->id;
        $lead_assign->employee_id = $lead->employee_id;
        $lead_assign->save();

        $lead_followup = new LeadFollowUp();
        $lead_followup->lead_id = $lead->id;
        $lead_followup->employee_id = $lead->employee_id;
        $lead_followup->save();

        if($request->design_date)
        {
            foreach ($request->design_date as $key => $value) {
                if($value != '')
                {
                    $design_child = new LeadBasicDesignChild();
                    $design_child->lead_id = $lead->id;
                    $design_child->date = $value;
                    if(isset($request['design']))
                    {
                        $imageName =  time().$key.'.'. $request->design[$key]->getClientOriginalExtension();
                        $request->design[$key]->move(public_path('design/'),$imageName);
                        $design_child->design = $imageName;
                    }
                    $design_child->save();
                }
            }
        }

        if($request->quotation_date)
        {
            foreach ($request->quotation_date as $key => $value) {
                if($value != '')
                {
                    $quotation_child = new LeadBasicQuotationChild();
                    $quotation_child->lead_id = $lead->id;
                    $quotation_child->quotation_date = $value;
                    if(isset($request['quotation_image']))
                    {
                        $imageName =  time().$key.'.'. $request->quotation_image[$key]->getClientOriginalExtension();
                        $request->quotation_image[$key]->move(public_path('lead_basic_quotation_image/'),$imageName);
                        $quotation_child->quotation_image = $imageName;
                    }
                    $quotation_child->save();
                }
            }
        }

        if($request->name)
        {
            foreach($request->name as $key => $value)
            {
                if($value != '')
                {
                    $lead_basic_main_child = new LeadBasicMainChild();
                    $lead_basic_main_child->lead_id = $lead->id;
                    $lead_basic_main_child->name = $value;
                    $lead_basic_main_child->employee_id = $request->main_employee_id[$key];
                    $lead_basic_main_child->symbol = $request->symbol[$key];
                    $lead_basic_main_child->save();

                    if(!empty($request->productcategory_id[$key]) && count($request->productcategory_id[$key]) > 0)
                    {
                        foreach($request->productcategory_id[$key] as $pc_key => $pc_val)
                        {

                            if($pc_val != '')
                            {
                                $child = new LeadBasicChild();
                                $child->lead_id = $lead->id;
                                $child->lead_basic_main_id = $lead_basic_main_child->id;
                                $child->productcategory_id = $pc_val;
                                $child->company_id = $request->company_id[$key][$pc_key];
                                $child->product_id = $request->product_id[$key][$pc_key];
                                $child->warehouse_id = $request->warehouse_id[$key][$pc_key];
                                $child->qty = $request->qty[$key][$pc_key];
                                $child->save();
                            }
                        }
                    }
                }
            }
        }
        
        

        $datas = [
            'lead' => $lead,
        ];
        $client = Client::where('id',$lead->client_id)->value('member');
        $employee = Employee::where('id',$lead_assign->employee_id)->first();
        $email = $employee->email;
        $text = 'Lead Created Name.' . $client;
        Mail::send('lead.lead_create',$datas, function($message) use($email,$text) {
            $message->to($email);
            $message->subject($text);
        });
        $stores_token = Employee::where('id',$employee->id)->value('push_token');
        if($stores_token)
        {
            $device_token = $stores_token;
            $urls = env('Lead_employee_link');
            $leads_id = $lead->id;
            $name = 'Lead';
            $title = 'Lead Created Name !!';
            $body = 'Lead Created Name.' . $client;
            $this->sendnotification($device_token,$title,$body,$urls,$leads_id,$name);
        }
        
        $datas = [
            'lead' => $lead,
        ];
        $branch = User::where('id',$lead->branch_id)->first();
        $email = $branch->email;
        $text = 'Lead Created Name.' . $client;
        Mail::send('lead.lead_create',$datas, function($message) use($email,$text) {
            $message->to($email);
            $message->subject($text);
        });
        $stores_token = User::where('id',$branch->id)->value('push_token');
        if($stores_token)
        {
            $device_token = $stores_token;
            $urls = env('Lead_admin_link');
            $leads_id = $lead->id;
            $name = 'Lead';
            $title = 'Lead Created Name. !!';
            $body = 'Lead Created Name.' . $client;
            $this->adminsendnotification($device_token,$title,$body,$urls,$leads_id,$name);
        }

        if($lead)
        {
            return response()->json(['status' => 'success' , 'id' => $lead->id ]);
        }else
        {
            return response()->json(['status' => 'error']);
        }
    }
    public function leadassign(Request $request)
    {
        $lead_assign = LeadAssign::where('lead_id',$request->lead_id)->first();
        $lead_assign->stage_id = $request->stage_id;
        $lead_assign->section_id = $request->section_id;
        $params = implode(",",$request->employee_id);
        $lead_assign['employee_id'] = $params;
        $lead_assign->note = $request->note;
        $lead_assign->date = $request->date;
        $lead_assign->save();

        $array = LeadFollowUp::where('lead_id',$request->lead_id)->pluck('employee_id')->toArray();
        $deleted_emp = array_diff($array,explode(',',$params));
        LeadFollowUp::where('lead_id',$request->lead_id)->whereIn('employee_id',$deleted_emp)->delete();

        foreach($request->employee_id as $key => $value) {
            if($value != '')
            {
                $childs = LeadFollowUp::where('lead_id',$request->lead_id)->where('employee_id',$value)->first();
                if($childs)
                {
                }else
                {
                    $childs = new LeadFollowUp();
                    $childs->lead_id = $request->lead_id;
                    $childs->employee_id = $value;
                    $childs->save();
                }
            }
        }

        $lead = Lead::where('id',$lead_assign->lead_id)->first();
        $client = Client::where('id',$lead->client_id)->value('member');
        $datas = [
            'lead' => $lead,
        ];
        $exp = array();
        $path = explode(",", $params);
        $exp = array_merge($exp, $path);
        foreach ($exp as $key => $value) {
            $employee = Employee::where('id',$value)->first();
            $email = $employee->email;
            $text = 'Lead Updated Name.' . $client;
            Mail::send('lead.lead_create',$datas, function($message) use($email,$text) {
                $message->to($email);
                $message->subject($text);
            });
            $stores_token = Employee::where('id',$employee->id)->value('push_token');
            if($stores_token)
            {
                $device_token = $stores_token;
                $urls = env('Lead_employee_link');
                $leads_id = $lead->id;
                $name = 'Lead';
                $title = 'Lead Updated !!';
                $body = 'Lead Updated Name.' . $client;
                $this->sendnotification($device_token,$title,$body,$urls,$leads_id,$name);
            }
        }
        $datas = [
            'lead' => $lead,
        ];
        $branch = User::where('id',$lead->branch_id)->first();
        $email = $branch->email;
        $text = 'Lead Updated Name.' . $client;
        Mail::send('lead.lead_create',$datas, function($message) use($email,$text) {
            $message->to($email);
            $message->subject($text);
        });
        $stores_token = User::where('id',$branch->id)->value('push_token');
        if($stores_token)
        {
            $device_token = $stores_token;
            $urls = env('Lead_admin_link');
            $leads_id = $lead->id;
            $name = 'Lead';
            $title = 'Lead Updated !!';
            $body = 'Lead Updated Name.' . $client;
            $this->adminsendnotification($device_token,$title,$body,$urls,$leads_id,$name);
        }

        if($lead_assign)
        {
            return response()->json(['status' => 'success']);
        }else
        {
            return response()->json(['status' => 'error']);
        }
    }
    public function leadfollowup(Request $request)
    {
        $lead_followup = LeadFollowUp::where('lead_id',$request->lead_id)->first();
        if($lead_followup)
        {

            if($request->subject)
            {
                foreach($request['subject'] as $key=>$value)
                {
                    if($value != '')
                    {
                        $edit_childs['subject'] = $value;
                        $edit_childs['description'] = $request->description[$key];
                        $edit_childs['date'] = $request->date[$key];
                        $edit_childs['is_completed'] = $request->is_completed[$key];
                        $edit_childs['time'] = $request->time[$key];
                        $edit_childs['type'] = $request->type[$key];
                        $edit_childs['status_date'] = $request->status_date[$key];
                        $edit_childs_store = LeadFollowUp::where('id',$request->followup_id[$key])->update($edit_childs);
                        $followup = LeadFollowUp::where('id',$request->followup_id[$key])->first();
                        $followuplog = new FollowupLog();
                        $followuplog->employee_id = $followup->employee_id;
                        $followuplog->lead_id = $followup->lead_id;
                        $followuplog->subject = $followup->subject;
                        $followuplog->date = $followup->date;
                        $followuplog->is_completed = $followup->is_completed;
                        $followuplog->time = $followup->time;
                        $followuplog->type = $followup->type;
                        $followuplog->description = $followup->description;
                        $followuplog->save();
                    }
                }
            }
        }
        else
        {

            foreach ($request->subject as $key => $value)
            {
                if($value != '')
                {
                    $lead_followup = new LeadFollowUp();
                    $lead_followup->lead_id = $request->lead_id;
                    $lead_followup->subject = $request->subject;
                    $lead_followup->description = $request->description;
                    $lead_followup->date = $request->date;
                    $lead_followup->time = $request->time;
                    $lead_followup->type = $request->type;
                    $lead_followup->is_completed = $request->is_completed;
                    $lead_followup->save();
                    $followuplog = new FollowupLog();
                    $followuplog->employee_id = $lead_followup->employee_id;
                    $followuplog->subject = $lead_followup->subject;
                    $followuplog->lead_id = $lead_followup->lead_id;
                    $followuplog->date = $lead_followup->date;
                    $followuplog->is_completed = $lead_followup->is_completed;
                    $followuplog->time = $lead_followup->time;
                    $followuplog->type = $lead_followup->type;
                    $followuplog->description = $lead_followup->description;
                    $followuplog->save();
                }
            }
        }
        $lead = Lead::where('id',$lead_followup->lead_id)->first();
        $client = Client::where('id',$lead->client_id)->value('member');
        $lead_assign = LeadAssign::where('lead_id',$lead_followup->lead_id)->first();
        $datas = [
            'lead' => $lead,
        ];
        $exp = array();
        $path = explode(",", $lead_assign->employee_id);
        $exp = array_merge($exp, $path);
        foreach ($exp as $key => $value) {
            $employee = Employee::where('id',$value)->first();
            $email = $employee->email;
            $text = 'Lead Updated Name.' . $client;
            Mail::send('lead.lead_create',$datas, function($message) use($email,$text) {
                $message->to($email);
                $message->subject($text);
            });
            $stores_token = Employee::where('id',$employee->id)->value('push_token');
            if($stores_token)
            {
                $device_token = $stores_token;
                $urls = env('Lead_employee_link');
                $leads_id = $lead->id;
                $name = 'Lead';
                $title = 'Lead Updated !!';
                $body = 'Lead Updated Name.' . $client;
                $this->sendnotification($device_token,$title,$body,$urls,$leads_id,$name);
            }
        }
        $datas = [
            'lead' => $lead,
        ];
        $branch = User::where('id',$lead->branch_id)->first();
        $email = $branch->email;
        $text = 'Lead Updated Name.' . $client;
        Mail::send('lead.lead_create',$datas, function($message) use($email,$text) {
            $message->to($email);
            $message->subject($text);
        });
        $stores_token = User::where('id',$branch->id)->value('push_token');
        if($stores_token)
        {
            $device_token = $stores_token;
            $urls = env('Lead_admin_link');
            $name = 'Lead';
            $leads_id = $lead->id;
            $title = 'Lead Updated !!';
            $body = 'Lead Updated Name.' . $client;
            $this->adminsendnotification($device_token,$title,$body,$urls,$leads_id,$name);
        }
        if($lead_followup)
        {
            return response()->json(['status' => 'success']);
        }else
        {
            return response()->json(['status' => 'error']);
        }
    }

    public function leadtoshow(Request $request)
    {
        $lead = Lead::find($request->lead_id);
        $lead_toshow = LeadToshow::where('lead_id', $request->lead_id)->first();
        if ($lead_toshow)
        {
            if ($request->edit_description) {
                foreach ($request->edit_description as $key => $value) {

                    if($value != '')
                    {
                        $editchild = [
                            'employee_id' => $request->edit_employee_id[$key],
                            'category_id' => $request->edit_category_id[$key],
                            'branch_id' => $lead->branch_id,
                            'description' => $value,
                            'status' => $request->edit_status[$key],
                            'emp_description' => $request->edit_emp_description[$key],
                        ];

                        $editchildupdate = LeadToshow::where('id',$request['child_id'][$key])->update($editchild);
                    }
                }
            }



            if($request->employee_id)
            {
                foreach ($request->employee_id as $key => $value)
                {
                    if($value != '')
                    {
                        $lead_show = new LeadToshow();
                        $lead_show->lead_id = $request->lead_id;
                        $lead_show->employee_id = $value;
                        $lead_show->branch_id = $lead->branch_id;
                        $lead_show->category_id = $request->category_id[$key];
                        $lead_show->description = $request->description[$key];
                        $lead_show->status = $request->status[$key];
                        $lead_show->emp_description = $request->emp_description[$key];
                        $lead_show->save();
                    }
                }
            }
        } 
        else {

            if ($request->employee_id) {
                foreach ($request->employee_id as $key => $value) {
                    if (!empty($value)) {
                        $lead_toshow = new LeadToshow();
                        $lead_toshow->lead_id = $request->lead_id;
                        $lead_toshow->employee_id = $value;
                        $lead_toshow->branch_id = $lead->branch_id;
                        $lead_toshow->category_id = isset($request->category_id[$key]) ? $request->category_id[$key] : null;
                        $lead_toshow->description = isset($request->description[$key]) ? $request->description[$key] : null;
                        $lead_toshow->status = isset($request->status[$key]) ? $request->status[$key] : null;
                        $lead_toshow->emp_description = isset($request->emp_description[$key]) ? $request->emp_description[$key] : null;
                        $lead_toshow->save();
                    }
                }
            }
        }
        if ($lead_toshow) {
            return response()->json(['status' => 'success']);
        } else {
            return response()->json(['status' => 'error']);
        }
    }
    public function leadcommunication(Request $request)
    {
        $lead_communication = LeadCommunication::where('lead_id',$request->lead_id)->first();
        if($lead_communication)
        {
            $lead_communication->lead_id = $request->lead_id;
            $lead_communication->type = $request->type;
            $lead_communication->email = $request->email;
            $lead_communication->autoresponder = $request->autoresponder;
            $lead_communication->subject = $request->subject;
            $lead_communication->message = $request->message;
            $lead_communication->save(); 
        }
        else
        {
            $lead_communication = new LeadCommunication();
            $lead_communication->lead_id = $request->lead_id;
            $lead_communication->type = $request->type;
            $lead_communication->email = $request->email;
            $lead_communication->autoresponder = $request->autoresponder;
            $lead_communication->subject = $request->subject;
            $lead_communication->message = $request->message;
            $lead_communication->save();
        } 
        if($lead_communication)
        {
            return response()->json(['status' => 'success']);
        }else
        {
            return response()->json(['status' => 'error']);
        }
    }
    public function edit($id)
    {
        $data['title'] =   $this->viewName;
        $data['data'] = Lead::where('id', $id)->first();
        $data['assign'] = LeadAssign::where('lead_id',$id)->first();
        $data['followup'] = LeadFollowUp::where('lead_id',$id)->first();
        $data['communication'] = LeadCommunication::where('lead_id',$id)->first();
        $data['leadbasicmainchild'] = LeadBasicMainChild::where('lead_id',$id)->get();
        $data['leadbasicchild'] = LeadBasicChild::where('lead_id',$id)->get();
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        $data['type'] = Type::where('status',1)->get();
        $data['design'] = LeadBasicDesignChild::where('lead_id',$id)->get();  
        $data['quotationchild'] = LeadBasicQuotationChild::where('lead_id',$id)->get();    
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        $data['client'] = Client::where('branch_id',$data['data']->branch_id)->get();
        $data['project_type'] = ProjectType::where('branch_id',$data['data']->branch_id)->get();
        $data['project_name'] = ProjectName::where('branch_id',$data['data']->branch_id)->get();
        $data['arids'] = Arid::where('branch_id',$data['data']->branch_id)->orderBy('name')->get();
        $data['type'] = Type::where('status',1)->where('branch_id',$data['data']->branch_id)->get();
        $data['section'] = Section::where('status',1)->where('branch_id',$data['data']->branch_id)->get();
        $data['stage'] = LeadStage::where('status',1)->where('branch_id',$data['data']->branch_id)->get();
        $data['warehouse'] = Warehouse::where('status',1)->where('branch_id',$data['data']->branch_id)->get();
        $data['productcategory'] = ProductCategory::where('status',1)->where('branch_id',$data['data']->branch_id)->get();
        $leadNames = explode(',', $data['data']->lead_name);
        $data['company'] = Company::where('status',1)->whereIn('pro_category_id',$leadNames)->where('branch_id',$data['data']->branch_id)->get();
        $data['sourcename'] = SourceName::where('status',1)->where('lead_sourceid',$data['data']->source_id)->where('branch_id',$data['data']->branch_id)->get();
        $data['employees'] = Employee::where('status',1)->where('branch_id',$data['data']->branch_id)->get();
        $data['source'] = LeadSource::where('status',1)->where('branch_id',$data['data']->branch_id)->get();
        
        $data['symbol'] = Symbol::where('status',1)->where('branch_id',$data['data']->branch_id)->get();
        return view('lead.edit')->with($data);
    }
    public function show($id)
    {
        $data['title'] =   $this->viewName;
        $data['data'] = Lead::where('id', $id)->first();
        $data['assign'] = LeadAssign::where('lead_id',$id)->first();
        $data['followup'] = LeadFollowUp::where('lead_id',$id)->first();
        $data['communication'] = LeadCommunication::where('lead_id',$id)->first();
        $data['leadbasicmainchild'] = LeadBasicMainChild::where('lead_id',$id)->get();
        $data['leadbasicchild'] = LeadBasicChild::where('lead_id',$id)->get();
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        $data['type'] = Type::where('status',1)->get();
        $data['design'] = LeadBasicDesignChild::where('lead_id',$id)->get();  
        $data['quotationchild'] = LeadBasicQuotationChild::where('lead_id',$id)->get();    
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        $data['client'] = Client::where('branch_id',$data['data']->branch_id)->get();
        $data['project_type'] = ProjectType::where('branch_id',$data['data']->branch_id)->get();
        $data['project_name'] = ProjectName::where('branch_id',$data['data']->branch_id)->get();
        $data['arids'] = Arid::where('branch_id',$data['data']->branch_id)->get();
        $data['type'] = Type::where('status',1)->where('branch_id',$data['data']->branch_id)->get();
        $data['section'] = Section::where('status',1)->where('branch_id',$data['data']->branch_id)->get();
        $data['stage'] = LeadStage::where('status',1)->where('branch_id',$data['data']->branch_id)->get();
        $data['warehouse'] = Warehouse::where('status',1)->where('branch_id',$data['data']->branch_id)->get();
        $data['productcategory'] = ProductCategory::where('status',1)->where('branch_id',$data['data']->branch_id)->get();
        $leadNames = explode(',', $data['data']->lead_name);
        $data['company'] = Company::where('status',1)->whereIn('pro_category_id',$leadNames)->where('branch_id',$data['data']->branch_id)->get();
        $data['sourcename'] = SourceName::where('status',1)->where('lead_sourceid',$data['data']->source_id)->where('branch_id',$data['data']->branch_id)->get();
        $data['employees'] = Employee::where('status',1)->where('branch_id',$data['data']->branch_id)->get();
        $data['source'] = LeadSource::where('status',1)->where('branch_id',$data['data']->branch_id)->get();
        
        $data['symbol'] = Symbol::where('status',1)->where('branch_id',$data['data']->branch_id)->get();
        return view('lead.show')->with($data);
    }
    public function update(Request $request, $id)
    {
        // dd($request->all());
        if($request->status == 5)
        {
            if($request->reason == '')
            {
                return response()->json(['status' => 'required', 'message' => 'Reason Required !!!']);
            }
        }

        if ($request->name) {
            foreach ($request->name as $validate_key => $validate_val) {
                if ($validate_val != '')
                {
                    if (!empty($request['productcategory_id'][$validate_key])) {
                        foreach ($request['productcategory_id'][$validate_key] as $validate_pc_key => $validate_pc_val) {
                            if (!empty($validate_pc_val))
                            {
                                if(empty($request->hidden_child[$validate_key][$validate_pc_key]))
                                {
                                    if(empty($request->company_id[$validate_key][$validate_pc_key]))
                                    {
                                        return response()->json(['status' => 'required', 'message' => 'Company Required for '.$validate_val.' !!!']);
                                    }

                                    if(empty($request->product_id[$validate_key][$validate_pc_key]))
                                    {
                                        return response()->json(['status' => 'required', 'message' => 'Product Required for '.$validate_val.' !!!']);
                                    }

                                    if(empty($request->warehouse_id[$validate_key][$validate_pc_key]))
                                    {
                                        return response()->json(['status' => 'required', 'message' => 'Warehouse Required for '.$validate_val.' !!!']);
                                    }

                                    if(empty($request->qty[$validate_key][$validate_pc_key]))
                                    {
                                        return response()->json(['status' => 'required', 'message' => 'Qty Required for '.$validate_val.' !!!']);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $lead = Lead::where('id',$id)->first();
        if(Auth::guard('user')->check())
        {
            $lead->employee_id = $request->employee_id;
        }
        else
        {
            $lead->employee_id = Auth::user()->id;
        }

        $lead->project_type_id = $request->project_type_id;
        $lead->project_name_id = $request->project_name_id;
        $lead->arid_id = $request->arid_id;

        if($request->lead_name != NULL){
            $params = implode(",",$request->lead_name);
            $lead['lead_name'] = $params;
        }else{
            $lead['lead_name'] = NULL;
        }
        if($request->company_name != NULL){
            $param = implode(",",$request->company_name);
            $lead['company_name'] = $param;
        }else{
            $lead['company_name'] = NULL;
        }
        if($request->status == 5)
        {
            if($request->reason == '')
            {
                return response()->json(['status' => 'reason_error']);
            }
        }
        $lead->reason = $request->reason;
        $lead->description = $request->description;
        $lead->source_id = $request->source_id;
        $lead->source_name = $request->source_name;
        $lead->status = $request->status ?? 1;
        $lead->no_of_machines = $request->no_of_machines;
        $lead->date = $request->date;
        $lead->current_project_phase = $request->current_project_phase;
        $lead->showroom = $request->showroom ?? 2;
        $lead->lead_value = $request->value;
        $lead->save();
        

        if($request->design_date)
        {
            foreach ($request->design_date as $key => $value) {
                if($value != '')
                {
                    $design_child = new LeadBasicDesignChild();
                    $design_child->lead_id = $lead->id;
                    $design_child->date = $value;
                    if(isset($request['design']))
                    {
                        $imageName =  time().$key.'.'. $request->design[$key]->getClientOriginalExtension();
                        $request->design[$key]->move(public_path('design/'),$imageName);
                        $design_child->design = $imageName;
                    }
                    $design_child->save();
                }
            }
        }

        
        if($request->quotation_date)
        {
            foreach ($request->quotation_date as $key => $value) {
                if($value != '')
                {
                    $quotation_child = new LeadBasicQuotationChild();
                    $quotation_child->lead_id = $lead->id;
                    $quotation_child->quotation_date = $value;
                    if(isset($request['quotation_image']))
                    {
                        $imageName =  time().$key.'.'. $request->quotation_image[$key]->getClientOriginalExtension();
                        $request->quotation_image[$key]->move(public_path('lead_basic_quotation_image/'),$imageName);
                        $quotation_child->quotation_image = $imageName;
                    }
                    $quotation_child->save();
                }
            }
        }

        
        if ($request->name) {
            foreach ($request->name as $key => $value) {
                if ($value != '') {

                    if (!empty($request['hidden_main'][$key]))
                    {
                        $lead_basic_main_child = LeadBasicMainChild::where('id',$request['hidden_main'][$key])->first();
                    }
                    else
                    {
                        $lead_basic_main_child = new LeadBasicMainChild();
                        $lead_basic_main_child->lead_id = $lead->id;
                    }
                    
                    $lead_basic_main_child->name = $value;
                    $lead_basic_main_child->employee_id = $request->main_employee_id[$key];
                    $lead_basic_main_child->symbol = $request->symbol[$key];
                    $lead_basic_main_child->save();

                    if (!empty($request['productcategory_id'][$key])) {
                        foreach ($request['productcategory_id'][$key] as $pc_key => $pc_val) {
                            if (!empty($pc_val)) {
                                $child = null;

                                if (!empty($request->hidden_child[$key][$pc_key])) {
                                    $child = LeadBasicChild::find($request->hidden_child[$key][$pc_key]);
                                }
                                
                                if (!$child) {
                                    $child = new LeadBasicChild();
                                    $child->lead_id = $lead->id;
                                    $child->lead_basic_main_id = $lead_basic_main_child->id;
                                    $child->productcategory_id = $pc_val;
                                    $child->company_id = $request->company_id[$key][$pc_key] ?? null;
                                    $child->product_id = $request->product_id[$key][$pc_key] ?? null;
                                }

                                $child->warehouse_id = $request->warehouse_id[$key][$pc_key] ?? null;
                                $child->qty = $request->qty[$key][$pc_key] ?? null;
                                $child->save();
                            }
                        }
                    }
                }
            }
        }

        
        

        $lead_assign = LeadAssign::where('lead_id',$id)->first();
        $client = Client::where('id',$lead->client_id)->value('member');
        $datas = [
            'lead' => $lead,
        ];
        $exp = array();
        $path = explode(",", $lead_assign->employee_id);
        $exp = array_merge($exp, $path);
        foreach ($exp as $key => $value) {
            $employee = Employee::where('id',$value)->first();
            $email = $employee->email;
            $text = 'Lead Updated Name.' . $client;
            Mail::send('lead.lead_create',$datas, function($message) use($email,$text) {
                $message->to($email);
                $message->subject($text);
            });
            $stores_token = Employee::where('id',$employee->id)->value('push_token');
            if($stores_token)
            {
                $device_token = $stores_token;
                $urls = env('Lead_employee_link');
                $name = 'Lead';
                $leads_id = $lead->id;
                $title = 'Lead Updated !!';
                $body = 'Lead Updated Name.' . $client;
                $this->sendnotification($device_token,$title,$body,$urls,$leads_id,$name);
            }
        }
        $datas = [
            'lead' => $lead,
        ];
        $branch = User::where('id',$lead->branch_id)->first();
        $email = $branch->email;
        $text = 'Lead Updated Name.' . $client;
        Mail::send('lead.lead_create',$datas, function($message) use($email,$text) {
            $message->to($email);
            $message->subject($text);
        });
        $stores_token = User::where('id',$branch->id)->value('push_token');
        if($stores_token)
        {
            $urls = env('Lead_admin_link');
            $name = 'Lead';
            $leads_id = $lead->id;
            $device_token = $stores_token;
            $title = 'Lead Updated !!';
            $body = 'Lead Updated Name.' . $client;
            $this->adminsendnotification($device_token,$title,$body,$urls,$leads_id,$name);
        }


        if($lead)
        {
            return response()->json(['status' => 'success']);
        }else
        {
            return response()->json(['status' => 'error']);
        }
    }
    public function leadchilddelete($id)
    {
        $leadbasicchild = LeadBasicChild::where('id',$id)->delete();
        return redirect()->back();
    }
    public function leadchilmainddelete($id)
    {
        $child = LeadBasicChild::where('lead_basic_main_id',$id)->delete();
        $leadbasicchild = LeadBasicMainChild::where('id',$id)->delete();
        return redirect()->back();
    }
    public function leaddesigndelete($id)
    {
        $leadbasicchild = LeadBasicDesignChild::where('id',$id)->first();
        if (isset($leadbasicchild->design)) {
            File::delete(public_path('design/' . $leadbasicchild->design));
        }
        $leadbasicchild->delete();

        if($leadbasicchild)
        {
            return response()->json(['status' => 'success'  ]);
        }else
        {
            return response()->json(['status' => 'error']);
        }
    }
    public function leadquotationdelete($id)
    {
        $leadquotationchild = LeadBasicQuotationChild::where('id',$id)->first();
        if (isset($leadquotationchild->quotation_image)) {
            File::delete(public_path('lead_basic_quotation_image/' . $leadquotationchild->quotation_image));
        }
        $leadquotationchild->delete();

        if($leadquotationchild)
        {
            return response()->json(['status' => 'success'  ]);
        }else
        {
            return response()->json(['status' => 'error']);
        }
    }
    public function getcompanyfromcategory(Request $request)
    {
        $html = "<option value=''>Select</option>";
        if($request->lead_name)
        {
            $data = Company::whereIn('pro_category_id',$request->lead_name)->get();
            foreach ($data as $key => $value) {
                $selected = '';
                if($request->company_name && count($request->company_name))
                {
                    foreach ($request->company_name as $key1 => $value1)
                    {
                        if($value1 == $value->id)
                        {
                            $selected = 'selected';
                        }
                    }
                }
                $html .= "<option value=".$value->id." ".$selected.">".$value->name."</option>";
            }
        }
        echo $html;
    }
    public function getproduct(Request $request)
    {
        $data = Product::where('company_id',$request->company_id)->where('status',1)->get();
        $html = "<option value=''>Select</option>";
        foreach ($data as $key => $value) {
            $html .= "<option value=".$value->id.">".$value->dropdown_name."</option>";
        }
        
        $data2 = Product::where('company_id',$request->edit_company_id)->where('status',1)->get();
        $html2 = "<option value=''>Select</option>";
        foreach ($data2 as $key => $value) {
            $html2 .= "<option value=".$value->id.">".$value->dropdown_name."</option>";
        }
        $data1 = Product::where('company_id',$request->new_company_id)->where('status',1)->get();
        $html1 = "<option value=''>Select</option>";
        foreach ($data1 as $key => $value) {
            $html1 .= "<option value=".$value->id.">".$value->dropdown_name."</option>";
        }
        
        return response()->json(['data'=>$html,'data1'=>$html1,'data2'=>$html2]);
    }
    public function getwarehouse(Request $request)
    {
        $data = WarehouseInventory::where('product_id',$request->product_id)->with('warehouse')->get();
        $html = "<option value=''>Select</option>";
        foreach ($data as $key => $value) {
            $warehousenm = Warehouse::where('id',$value->warehouse_id)->value('name');
            $html .= "<option value=".$value->warehouse_id.">".$warehousenm."</option>";
        }
        $data1 = WarehouseInventory::where('product_id',$request->new_product_id)->with('warehouse')->get();
        $html1 = "<option value=''>Select</option>";
        foreach ($data1 as $key => $value) {
            $warehousenm = Warehouse::where('id',$value->warehouse_id)->value('name');
            $html1 .= "<option value=".$value->warehouse_id.">".$warehousenm."</option>";
        }
        return response()->json(['data'=>$html,'data1'=>$html1]);
    }

    public function getwarehouse_from_product(Request $request)
    {
        $whinve = WarehouseInventory::where('product_id',$request->product_id);
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $whinve->where('branch_id',$request->branch_id);
            }
            else
            {
                $whinve->where('branch_id',Auth::user()->id);
            }
        }
        else
        {
            $whinve->where('branch_id',Auth::user()->branch_id);
        }
        $whinve = $whinve->pluck('warehouse_id')->toArray();

        $data = Warehouse::whereIn('id',$whinve)->get();

        $html = "<option value=''>Select</option>";
        foreach ($data as $key => $value) {
            $html .= "<option value=".$value->id.">".$value->name."</option>";
        }

        $html1 = Product::where('id',$request->product_id)->value('product');

        return response()->json(['data' => $html, 'data1' => $html1]);
    }
    public function getemployee(Request $request)
    {
        $data = Employee::where('branch_id',$request->branch_id)->where('status',1)->get();
        $html = "<option value=''>Select</option>";
        foreach ($data as $key => $value) {
            $html .= "<option value=".$value->id.">".$value->name."</option>";
        }

        $lead = Lead::where('branch_id',$request->branch_id)->pluck('client_id')->toArray();
        $client = Client::whereNotIn('id',$lead)->where('branch_id',$request->branch_id)->get();
        $html1 = "<option value=''>Select</option>";
        foreach ($client as $keys => $values) {
            $html1 .= "<option value=".$values->id.">".$values->member."</option>";
        }
        
        $sourcename = SourceName::where('status',1)->where('branch_id',$request->branch_id)->get();
        $html2 = "<option value=''>Select</option>";
        foreach ($sourcename as $keyss => $valuess) {
            $html2 .= "<option value=".$valuess->id.">".$valuess->name."</option>";
        }
        $source = LeadSource::where('status',1)->where('branch_id',$request->branch_id)->get();
        $html3 = "<option value=''>Select</option>";
        foreach ($source as $keysss => $valuesss) {
            $html3 .= "<option value=".$valuesss->id.">".$valuesss->name."</option>";
        }
        $productcategory = ProductCategory::where('status',1)->where('branch_id',$request->branch_id)->get();
        $html4 = "<option value=''>Select</option>";
        foreach ($productcategory as $keyssss => $valuessss) {
            $html4 .= "<option value=".$valuessss->id.">".$valuessss->name."</option>";
        }
        $company = Company::where('branch_id',$request->branch_id)->get();
        $html5 = "<option value=''>Select</option>";
        foreach ($company as $keysssss => $valuesssss) {
            $html5 .= "<option value=".$valuesssss->id.">".$valuesssss->name."</option>";
        }
        $symbol = Symbol::where('branch_id',$request->branch_id)->get();
        $html6 = "<option value=''>Select</option>";
        foreach ($symbol as $keyssssss => $valuessssss) {
            $html6 .= "<option value=".$valuessssss->id.">".$valuessssss->name."</option>";
        }
        $pro_type = ProjectType::where('branch_id',$request->branch_id)->get();
        $html7 = "<option value=''>Select</option>";
        foreach ($pro_type as $keysssssss => $valuesssssss) {
            $html7 .= "<option value=".$valuesssssss->id.">".$valuesssssss->name."</option>";
        }
        $pro_name = ProjectName::where('branch_id',$request->branch_id)->get();
        $html8 = "<option value=''>Select</option>";
        foreach ($pro_name as $keyssssssss => $valuessssssss) {
            $html8 .= "<option value=".$valuessssssss->id.">".$valuessssssss->name."</option>";
        }
        $arids = Arid::where('branch_id',$request->branch_id)->orderBy('name')->get();
        $html9 = "<option value=''>Select</option>";
        foreach ($arids as $key1 => $value1) {
            $html9 .= "<option value=".$value1->id.">".$value1->name."</option>";
        }
        return response()->json(['data'=>$html,'data1'=>$html1,'data2'=>$html2,'data3'=>$html3,'data4'=>$html4,'data5'=>$html5,'data6'=>$html6,'data7'=>$html7,'data8'=>$html8,'data9'=>$html9]);
    }
    public function getclient(Request $request)
    {
        $data = Client::where('employee_id',$request->employee_id)->where('status',1)->get();
        $html = "<option value=''>Select</option>";
        foreach ($data as $key => $value) {
            $html .= "<option value=".$value->id.">".$value->member."</option>";
        }
        echo $html;
    }
    public function getsourcename(Request $request)
    {
        $data = SourceName::where('lead_sourceid',$request->source_id)->where('status',1)->get();
        $html = "<option value=''>Select</option>";
        foreach ($data as $key => $value) {
            $html .= "<option value=".$value->id.">".$value->name."</option>";
        }
        echo $html;
    }
    public function getcompany(Request $request)
    {
        $data = Company::where('pro_category_id',$request->productcategory_id)->where('status',1)->get();
        $html = "<option value=''>Select</option>";
        foreach ($data as $key => $value) {
            $html .= "<option value=".$value->id.">".$value->name."</option>";
        }
        $data2 = Company::where('pro_category_id',$request->edit_productcategory_id)->where('status',1)->get();
        $html2 = "<option value=''>Select</option>";
        foreach ($data2 as $key => $value) {
            $html2 .= "<option value=".$value->id.">".$value->name."</option>";
        }
        $data1 = Company::where('pro_category_id',$request->new_productcategory_id)->where('status',1)->get();
        $html1 = "<option value=''>Select</option>";
        foreach ($data1 as $key => $value) {
            $html1 .= "<option value=".$value->id.">".$value->name."</option>";
        }
        return response()->json(['data'=>$html,'data1'=>$html1,'data2'=>$html2]);
    }
    public function model(Request $request)
    {
        $followuplog = FollowupLog::where('lead_id',$request->id)->get();
        $data = '<table class="table table-striped table-bordered table-hover table-checkable datatable" id="datatable_rows">
        <tr>
        <th>Employee</th>
        <th>Subject</th>
        <th>Description</th>
        <th>Date</th>
        <th>Time</th>
        <th>Updated Date</th>
        <th>Type</th>
        <th>Is Completed</th>
        </tr>';
        foreach ($followuplog as $key => $value) {
            $employee = Employee::where('id',$value->employee_id)->value('name');
            if($value->type)
            {
                $type = Type::where('id',$value->type)->value('name');
            }
            else
            {
                $type = '-';
            }
            if($value->is_completed == 1)
            {
                $is_completed = 'Pending';
            }
            elseif ($value->is_completed == 2) {
                $is_completed = 'Discussion';
            }
            elseif ($value->is_completed == 3) {
                $is_completed = 'Rivision';
            }
            elseif ($value->is_completed == 4) {
                $is_completed = 'Win';
            }
            elseif ($value->is_completed == 6) {
                $is_completed = "Showroom Visit";
            }elseif ($value->is_completed == 7) {
                $is_completed = "Site Measurement";
            }elseif ($value->is_completed == 8) {
                $is_completed = "Designing";
            }elseif ($value->is_completed == 9) {
                $is_completed = "Quotation";
            } 
            else {
                $is_completed = '';
            }

            if($value->created_at)
            {
                $created_at = date('d-m-Y h:i:s',strtotime($value->created_at));
            }
            else
            {
                $created_at = '-';
            }
            if($value->date)
            {
                $date = date('d-m-Y',strtotime($value->date));
            }
            else
            {
                $date = '-';
            }
            $data .= '<tr><td>'.$employee.'</td>
            <td>'.$value->subject.'</td>
            <td>'.$value->description.'</td>
            <td>'.$date.'</td>
            <td>'.$value->time.'</td>
            <td>'.$created_at.'</td>
            <td>'.$type .'</td>
            <td>'.$is_completed.'</td></tr>
            ';
        }
        $data .= '</table>';
        return response()->json(['data' => $data]);
    }
    public function active($id)
    {
        $lead = Lead::where('id',$id)->first();
        $lead->active = 0;
        $lead->save();
        return redirect()->back();
    }
    public function deletetoshow($id)
    {
        $child = LeadToshow::where('id',$id)->first();
        $child->delete();
        $notification = array(
            'message' => 'Deleted Sussfully.',
            'alert-type' => 'success',
        );
        return redirect()->back()->with($notification);
    }
}