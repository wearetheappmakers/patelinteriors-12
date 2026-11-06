<?php
namespace App\Http\Controllers;
use App\User;
use DataTables;
use App\Section;
use App\SalesOrder;
use App\SalesOrderChild;
use App\Company;
use App\WarehouseInventory;
use App\Employee;
use App\ServiceImage;
use Auth;
use App\Service;
use App\ServiceChild;
use App\Product;
use Mail;
use App\Client;
use App\ProductCategory;
use App\Technician;
use App\ConsentPerson;
use Illuminate\Http\Request;
class ServiceController extends Controller
{
    public function __construct(Service $s)
    {
        $this->view = 'service';
        $this->route = 'service';
        $this->viewName = 'Service';
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
                'name' => $name
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
                $query = Service::with('employee','branch','client')->where('employee_id',Auth::user()->id)->orderBy('register_date','DESC')->get();
                if($request->branch_id)
                {
                    $query = $query->where('branch_id',$request->branch_id);
                }
                if($request->employee_id)
                {
                    $query = $query->where('employee_id',$request->employee_id);
                }
                if($request->status)
                {
                    $query = $query->where('status',$request->status);
                }
                return Datatables::of($query)
                ->addColumn('action', function ($row) {
                    $btn = view('layouts.actionbtnpermission')->with(['id'=>$row->id,'route'=>'service','per_value'=>'service'])->render();    
                    $btn .= '&nbsp;&nbsp;<a style="background: white;" href="'.route('user.service.show',$row->id).'"  title="View Details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-eye"></i></a>';  
                    return $btn;
                })
                ->addColumn('register_date', function ($row) {
                    $btn = '';      
                    if($row->register_date)
                    {
                        $btn = date('d-m-Y',strtotime($row->register_date));
                    }
                    return $btn;
                })
                ->addColumn('status', function ($row) {
                    if($row->status == 3 )
                    {
                        $btn = '<select class="form-control" disabled><option value="1"';
                    }
                    else
                    {
                        $btn = '<select class="form-control" onchange="status(this,'.$row->id.')"><option value="1"';
                    }
                    $selected = '';
                    if($row->status == 1) 
                    {
                        $selected = 'selected';
                    }
                    $btn .= $selected.'>Open</option><option value="2" ';
                    $selected = '';
                    if($row->status == 2)
                    {
                        $selected = 'selected';
                    }
                    $btn .= $selected.'>Pending</option><option value="3" ';
                    $selected = '';
                    if($row->status == 3)
                    {
                        $selected = 'selected';
                    }
                    $btn .= $selected.'>Completed</option></select>';
                    return $btn;
                })
                ->addColumn('company', function ($row) {
                    $child = ServiceChild::where('service_id',$row->id)->get();
                    $gr_array = [];
                    foreach($child as $ch)
                    {
                        $arr = [];
                        array_push($gr_array,$ch->company_id);
                    }
                    if(!empty($gr_array))
                    {
                        $arr = [];
                        foreach($gr_array as $ga)
                        {
                            array_push($arr,Company::where('id',$ga)->value('name'));
                        }
                        $arr = implode(' , ',$arr);
                    }
                    else
                    {
                        $arr = '-';
                    }
                    return $arr;
                })
                ->rawColumns(['numbers','register_date','action','singlecheckbox','status','company'])
                ->make(true);
            } 
        }
        else
        {
            if ($request->ajax()) {
                $query = Service::with('employee','branch','client')->orderBy('register_date','DESC')->get();
                if(Auth::user()->role == 2)
                {
                    $query = Service::with('employee','branch','client')->where('branch_id',Auth::user()->id)->orderBy('register_date','DESC')->get();
                }
                if($request->branch_id)
                {
                    $query = $query->where('branch_id',$request->branch_id);
                }
                if($request->employee_id)
                {
                    $query = $query->where('employee_id',$request->employee_id);
                }
                if($request->status)
                {
                    $query = $query->where('status',$request->status);
                }
                return Datatables::of($query)
                ->addColumn('action', function ($row) {
                    $btn = view('layouts.actionbtnpermission')->with(['id'=>$row->id,'route'=>'service','per_value'=>'service'])->render();  

                    $btn .= '&nbsp;&nbsp;<a style="background: white;" href="'.route('user.service.show',$row->id).'"  title="View Details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-eye"></i></a>';
                    return $btn;
                })
                ->addColumn('register_date', function ($row) {
                    $btn = '';      
                    if($row->register_date)
                    {
                        $btn = date('d-m-Y',strtotime($row->register_date));
                    }
                    return $btn;
                })
                ->addColumn('status', function ($row) {
                    if($row->status == 3 )
                    {
                        $btn = '<select class="form-control" disabled><option value="1"';
                    }
                    else
                    {
                        $btn = '<select class="form-control" onchange="status(this,'.$row->id.')"><option value="1"';
                    }
                    $selected = '';
                    if($row->status == 1) 
                    {
                        $selected = 'selected';
                    }
                    $btn .= $selected.'>Open</option><option value="2" ';
                    $selected = '';
                    if($row->status == 2)
                    {
                        $selected = 'selected';
                    }
                    $btn .= $selected.'>Pending</option><option value="3" ';
                    $selected = '';
                    if($row->status == 3)
                    {
                        $selected = 'selected';
                    }
                    $btn .= $selected.'>Completed</option></select>';
                    return $btn;
                })
                ->addColumn('company', function ($row) {
                    $child = ServiceChild::where('service_id',$row->id)->get();
                    $gr_array = [];
                    foreach($child as $ch)
                    {
                        $arr = [];
                        array_push($gr_array,$ch->company_id);
                    }
                    if(!empty($gr_array))
                    {
                        $arr = [];
                        foreach($gr_array as $ga)
                        {
                            array_push($arr,Company::where('id',$ga)->value('name'));
                        }
                        $arr = implode(' , ',$arr);
                    }
                    else
                    {
                        $arr = '-';
                    }
                    return $arr;
                })
                ->rawColumns(['numbers','action','register_date','singlecheckbox','status','company'])
                ->make(true);
            } 
        }
        $data['branch'] = User::where('role',2)->get();
        $data['employee'] = Employee::where('status',1)->get();
        if(Auth::user()->role == 2)
        {
            $data['employee'] = Employee::where('branch_id',Auth::user()->id)->where('status',1)->get();
        }
        return view('service.index')->with($data);
    }
    public function create()
    {
        $data['title'] = 'Add ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 2)
            {
                $data['employees'] = Employee::where('status',1)->where('role',3)->where('branch_id',Auth::user()->id)->get();
                $data['client'] = Client::where('branch_id',Auth::user()->id)->get();
                $data['consent_person'] = ConsentPerson::where('branch_id',Auth::user()->id)->get();
                $data['productcategory'] = ProductCategory::where('status',1)->where('branch_id',Auth::user()->id)->get();
            }
        }
        else
        {
            $branch = Employee::where('id',Auth::user()->id)->value('branch_id');
            $data['employees'] = Employee::where('status',1)->where('branch_id',$branch)->where('role',3)->get();
            $data['client'] = Client::where('branch_id',$branch)->get();
            $data['consent_person'] = ConsentPerson::where('branch_id',$branch)->get();
            $data['productcategory'] = ProductCategory::where('status',1)->where('branch_id',$branch)->get();
        }
        $data['branch'] = User::where('role',2)->get();
        return view('general.add_form')->with($data);
    }
    public function status(Request $request)
    {
        $param['status'] = $request->status;
        $change_status = Service::where('id',$request->id)->update($param);
        $service = Service::where('id',$request->id)->first();
        // $datas = [
        //     'service' => $service,
        // ];
        // $admin = User::where('id',1)->first();
        // $client = Client::where('id',$service->client_id)->value('member');
        // $email = $admin->email;
        // $text = 'Service Status Change .' ;
        // Mail::send('service.service_create',$datas, function($message) use($email,$text) {
        //     $message->to($email);
        //     $message->subject($text);
        // });
        // $client = Client::where('id',$service->client_id)->value('member');
        // $stores_token = User::where('id',1)->value('push_token');
        // if($stores_token)
        // {
        //     $device_token = $stores_token;
        //     $urls = env('Service_admin_link');
        //     $leads_id = $service->id;
        //     $name = 'Service';
        //     $title = 'Service Status Change !!';
        //     $body = 'Service Status Change.';
        //     $this->adminsendnotification($device_token,$title,$body,$urls,$leads_id,$name);
        // }
        $datas = [
            'service' => $service,
        ];
        $branch = User::where('id',$service->branch_id)->first();
        $client = Client::where('id',$service->client_id)->value('member');
        $email = $branch->email;
        $text = 'Service Status Change.';
        Mail::send('service.service_create',$datas, function($message) use($email,$text) {
            $message->to($email);
            $message->subject($text);
        });
        $client = Client::where('id',$service->client_id)->value('member');
        $stores_token = User::where('id',$branch->id)->value('push_token');
        if($stores_token)
        {
            $device_token = $stores_token;
            $urls = env('Service_admin_link');
            $leads_id = $service->id;
            $name = 'Service';
            $title = 'Service Status Change !!';
            $body = 'Service Status Change.';
            $this->adminsendnotification($device_token,$title,$body,$urls,$leads_id,$name);
        }
        $datas = [
            'service' => $service,
        ];
        $employee = Employee::where('id',$service->employee_id)->first();
        $client = Client::where('id',$service->client_id)->value('member');
        $email = $branch->email;
        $text = 'Service Status Change.';
        Mail::send('service.service_create',$datas, function($message) use($email,$text) {
            $message->to($email);
            $message->subject($text);
        });
        $client = Client::where('id',$service->client_id)->value('member');
        $stores_token = Employee::where('id',$employee->id)->value('push_token');
        if($stores_token)
        {
            $device_token = $stores_token;
            $urls = env('Service_employee_link');
            $leads_id = $service->id;
            $name = 'Service';
            $title = 'Service Status Change !!';
            $body = 'Service Status Change.';
            $this->sendnotification($device_token,$title,$body,$urls,$leads_id,$name);
        }
        if($change_status)
        {
            return response()->json(['status' => 'success']);
        }
        else
        {
            return response()->json(['status' => 'error']);
        }
    }
    public function store(Request $request)
    {
        // if($request->product_id)
        // {
        //     foreach ($request->product_id as $key => $value) {
        //         if($value == '')
        //         {
        //             return response()->json(['status' => 'required', 'message' => 'Product is required!!']);
        //         }
        //     }
        // }

        if($request->qty)
        {
            foreach ($request->qty as $key1 => $value1) {
                if($value1 == '')
                {
                    return response()->json(['status' => 'required', 'message' => 'Qty. is required!!']);
                }
            }
        }

        $service = new Service();
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $service->branch_id = $request->branch_id;
                $service->employee_id = $request->employee_id;
            }
            else
            {
                $service->branch_id = Auth::user()->id;
                $service->employee_id = $request->employee_id;
            }
        }
        else
        {
            $service->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
            $service->employee_id = Auth::user()->id;
        }
        $service->client_id = $request->client_id;
        $service->consent_person_id = $request->consent_person_id;
        $service->phone_no = $request->phone_no;
        $service->address = $request->address;
        $service->complain_no = $request->complain_no;
        $service->register_date = $request->register_date;
        $service->remark = $request->remark;
        $service->completed_date = $request->completed_date;
        $service->type = $request->type ?? 0;
        $service->save();

        if($request->qty)
        {
            foreach ($request->qty as $key => $value) {
                if($value != '')
                {
                    $child = new ServiceChild();
                    $child->service_id = $service->id;
                    $child->category_id = $request->productcategory_id[$key];
                    $child->company_id = $request->company_id[$key];
                    $child->technician_id = $request->technician_id[$key];
                    $child->product_id = $request->product_id[$key];
                    $child->qty = $request->qty[$key];
                    $child->save();
                }
            }
        }

        if($request->image)
        {
            foreach ($request->image as $key => $value) {
                if($value != '')
                {
                    $service_image = new ServiceImage();
                    $service_image->service_id = $service->id;
                    if(isset($value))
                    {
                        $imageName = time().'-'.$key.'.'. $value->getClientOriginalExtension();
                        $value->move(public_path('service/'),$imageName);
                        $service_image->image = $imageName;
                    }


                    $service_image->save();
                }
            }
        }
        // $datas = [
        //     'service' => $service,
        // ];
        // $admin = User::where('id',1)->first();
        // $client = Client::where('id',$service->client_id)->value('member');
        // $email = $admin->email;
        // $text = 'Service Created Name.' . $client;
        // Mail::send('service.service_create',$datas, function($message) use($email,$text) {
        //     $message->to($email);
        //     $message->subject($text);
        // });
        // $client = Client::where('id',$service->client_id)->value('member');
        // $stores_token = User::where('id',1)->value('push_token');
        // if($stores_token)
        // {
        //     $device_token = $stores_token;
        //     $urls = env('Service_admin_link');
        //     $leads_id = $service->id;
        //     $name = 'Service';
        //     $title = 'Service Created !!';
        //     $body = 'Service Created Name.' . $client;
        //     $this->adminsendnotification($device_token,$title,$body,$urls,$leads_id,$name);
        // }
        $datas = [
            'service' => $service,
        ];
        $branch = User::where('id',$service->branch_id)->first();
        $client = Client::where('id',$service->client_id)->value('member');
        $email = $branch->email;
        $text = 'Service Created Name.' . $client;
        Mail::send('service.service_create',$datas, function($message) use($email,$text) {
            $message->to($email);
            $message->subject($text);
        });
        $client = Client::where('id',$service->client_id)->value('member');
        $stores_token = User::where('id',$branch->id)->value('push_token');
        if($stores_token)
        {
            $device_token = $stores_token;
            $urls = env('Service_admin_link');
            $leads_id = $service->id;
            $name = 'Service';
            $title = 'Service Created !!';
            $body = 'Service Created Name.' . $client;
            $this->adminsendnotification($device_token,$title,$body,$urls,$leads_id,$name);
        }
        $datas = [
            'service' => $service,
        ];
        $employee = Employee::where('id',$service->employee_id)->first();
        $client = Client::where('id',$service->client_id)->value('member');
        $email = $branch->email;
        $text = 'Service Created Name.' . $client;
        Mail::send('service.service_create',$datas, function($message) use($email,$text) {
            $message->to($email);
            $message->subject($text);
        });
        $client = Client::where('id',$service->client_id)->value('member');
        $stores_token = Employee::where('id',$employee->id)->value('push_token');
        if($stores_token)
        {
            $device_token = $stores_token;
            $urls = env('Service_employee_link');
            $leads_id = $service->id;
            $name = 'Service';
            $title = 'Service Created !!';
            $body = 'Service Created Name.' . $client;
            $this->sendnotification($device_token,$title,$body,$urls,$leads_id,$name);
        }
        if($service)
        {
            return response()->json(['status' => 'success']);
        }else
        {
            return response()->json(['status' => 'error']);
        }
    }
    public function edit($id)
    {
        $data['title'] = 'Edit ' . $this->viewName;
        $data['data'] = Service::where('id', $id)->first();
        $data['child'] = ServiceChild::where('service_id',$id)->get();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $data['employees'] = Employee::where('status',1)->where('role',3)->where('branch_id',$data['data']->branch_id)->get();
                $data['client'] = Client::where('branch_id',$data['data']->branch_id)->get();
                $data['consent_person'] = ConsentPerson::where('branch_id',$data['data']->branch_id)->get();
                $data['productcategory'] = ProductCategory::where('status',1)->where('branch_id',$data['data']->branch_id)->get();
            }
            else    
            {
                $data['employees'] = Employee::where('status',1)->where('role',3)->where('branch_id',Auth::user()->id)->get();
                $data['client'] = Client::where('branch_id',Auth::user()->id)->get();
                $data['consent_person'] = ConsentPerson::where('branch_id',Auth::user()->id)->get();
                $data['productcategory'] = ProductCategory::where('status',1)->where('branch_id',Auth::user()->id)->get();
            }
        }
        else
        {
            $branch = Employee::where('id',Auth::user()->id)->value('branch_id');
            $data['employees'] = Employee::where('status',1)->where('branch_id',$branch)->where('role',3)->get();
            $data['client'] = Client::where('branch_id',$branch)->get();
            $data['consent_person'] = ConsentPerson::where('branch_id',$branch)->get();
            $data['productcategory'] = ProductCategory::where('status',1)->where('branch_id',$branch)->get();
        }
        $data['branch'] = User::where('role',2)->get();
        return view('general.edit_form')->with($data);
    }
    public function update(Request $request, $id)
    {
        if($request->product_id)
        {
            foreach ($request->product_id as $key => $value) {
                if($value)
                {
                    if($request->qty[$key] == '')
                    {
                        return response()->json(['status' => 'required', 'message' => 'Qty. is required!!']);
                    }
                }
            }
        }

        if($request->edit_qty)
        {
            foreach ($request->edit_qty as $key1 => $value1) {
                if($value1 == '')
                {
                    return response()->json(['status' => 'required', 'message' => 'Qty. is required!!']);
                }
            }
        }
        
        $service = Service::where('id',$id)->first();
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                // $service->branch_id = $request->branch_id;
                $service->employee_id = $request->employee_id;
            }
            else
            {
                // $service->branch_id = Auth::user()->id;
                $service->employee_id = $request->employee_id;
            }
        }
        else
        {
            // $service->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
            $service->employee_id = Auth::user()->id;
        }
        $service->client_id = $request->client_id;
        $service->consent_person_id = $request->consent_person_id;
        $service->phone_no = $request->phone_no;
        $service->address = $request->address;
        $service->complain_no = $request->complain_no;
        $service->register_date = $request->register_date;
        $service->remark = $request->remark;
        $service->completed_date = $request->completed_date;
        $service->type = $request->type ?? 0;
        $service->save();
        if($request->qty)
        {
            foreach ($request->qty as $key => $value) {
                if($value != '')
                {
                    $child = new ServiceChild();
                    $child->service_id = $service->id;
                    $child->category_id = $request->productcategory_id[$key];
                    $child->company_id = $request->company_id[$key];
                    $child->technician_id = $request->technician_id[$key];
                    $child->product_id = $request->product_id[$key];
                    $child->qty = $request->qty[$key];
                    $child->save();
                }
            }
        }
        if($request->edit_qty)
        {
            foreach ($request->edit_qty as $key => $value) {
                {
                    if($value != '')
                    {
                        $service_child['qty'] = $value;
                        $service1 = ServiceChild::where('id',$request->child_id[$key])->update($service_child);
                    }
                }
            }
        }
        if($request->image)
        {
            foreach ($request->image as $key => $value) {
                if($value != '')
                {
                    $service_image = new ServiceImage();
                    $service_image->service_id = $service->id;
                    if(isset($value))
                    {
                        $imageName = time().'-'.$key.'.'. $value->getClientOriginalExtension();
                        $value->move(public_path('service/'),$imageName);
                        $service_image->image = $imageName;
                    }


                    $service_image->save();
                }
            }
        }
        // $datas = [
        //     'service' => $service,
        // ];
        // $admin = User::where('id',1)->first();
        // $client = Client::where('id',$service->client_id)->value('member');
        // $email = $admin->email;
        // $text = 'Service Updated Name.' . $client;
        // Mail::send('service.service_create',$datas, function($message) use($email,$text) {
        //     $message->to($email);
        //     $message->subject($text);
        // });
        // $client = Client::where('id',$service->client_id)->value('member');
        // $stores_token = User::where('id',1)->value('push_token');
        // if($stores_token)
        // {
        //     $device_token = $stores_token;
        //     $urls = env('Service_admin_link');
        //     $leads_id = $service->id;
        //     $name = 'Service';
        //     $title = 'Service Updated !!';
        //     $body = 'Service Updated Name.' . $client;
        //     $this->adminsendnotification($device_token,$title,$body,$urls,$leads_id,$name);
        // }
        $datas = [
            'service' => $service,
        ];
        $branch = User::where('id',$service->branch_id)->first();
        $client = Client::where('id',$service->client_id)->value('member');
        $email = $branch->email;
        $text = 'Service Updated Name.' . $client;
        Mail::send('service.service_create',$datas, function($message) use($email,$text) {
            $message->to($email);
            $message->subject($text);
        });
        $client = Client::where('id',$service->client_id)->value('member');
        $stores_token = User::where('id',$branch->id)->value('push_token');
        if($stores_token)
        {
            $device_token = $stores_token;
            $urls = env('Service_admin_link');
            $leads_id = $service->id;
            $name = 'Service';
            $title = 'Service Updated !!';
            $body = 'Service Updated Name.' . $client;
            $this->adminsendnotification($device_token,$title,$body,$urls,$leads_id,$name);
        }
        $datas = [
            'service' => $service,
        ];
        $employee = Employee::where('id',$service->employee_id)->first();
        $client = Client::where('id',$service->client_id)->value('member');
        $email = $branch->email;
        $text = 'Service Updated Name.' . $client;
        Mail::send('service.service_create',$datas, function($message) use($email,$text) {
            $message->to($email);
            $message->subject($text);
        });
        $client = Client::where('id',$service->client_id)->value('member');
        $stores_token = Employee::where('id',$employee->id)->value('push_token');
        if($stores_token)
        {
            $device_token = $stores_token;
            $urls = env('Service_employee_link');
            $leads_id = $service->id;
            $name = 'Service';
            $title = 'Service Updated !!';
            $body = 'Service Updated Name.' . $client;
            $this->sendnotification($device_token,$title,$body,$urls,$leads_id,$name);
        }
        if($service)
        {
            return response()->json(['status' => 'success']);
        }else
        {
            return response()->json(['status' => 'error']);
        }
    }
    public function clonedelete($id)
    {
        $data = ServiceChild::where('id',$id)->delete();
        return back();
    }
    public function show($id)
    {
        $data['title'] = 'Edit ' . $this->viewName;
        $data['data'] = Service::where('id', $id)->first();
        $data['child'] = ServiceChild::where('service_id',$id)->get();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $data['employees'] = Employee::where('status',1)->where('role',3)->get();
                $data['product'] = Product::where('status',1)->get();
                $data['client'] = Client::get();
                $data['company'] = Company::where('status',1)->get();
                $data['productcategory'] = ProductCategory::where('status',1)->get();
            }
            else
            {
                $data['employees'] = Employee::where('status',1)->where('role',3)->where('branch_id',Auth::user()->id)->get();
                $data['product'] = Product::where('status',1)->where('branch_id',Auth::user()->id)->get();
                $data['client'] = Client::where('branch_id',Auth::user()->id)->get();
                $data['company'] = Company::where('status',1)->where('branch_id',Auth::user()->id)->get();
                $data['productcategory'] = ProductCategory::where('status',1)->where('branch_id',Auth::user()->id)->get();
            }
        }
        else
        {
            $branch = Employee::where('id',Auth::user()->id)->value('branch_id');
            $data['employees'] = Employee::where('status',1)->where('branch_id',$branch)->where('role',3)->get();
            $data['product'] = Product::where('status',1)->where('branch_id',$branch)->get();
            $data['client'] = Client::where('branch_id',$branch)->get();
            $data['company'] = Company::where('status',1)->where('branch_id',$branch)->get();
            $data['productcategory'] = ProductCategory::where('status',1)->where('branch_id',$branch)->get();
        }
        $data['branch'] = User::where('role',2)->get();
        return view('service.show')->with($data);
    }
    public function getnumber(Request $request)
    {
        $html = Client::where('id',$request->client_id)->value('mobile_no');
        $html1 = Client::where('id',$request->client_id)->value('address');
        return response()->json(['data'=>$html,'data1'=>$html1]);
    }
    public function getemployees(Request $request)
    {
        $data = Employee::where('role',3)->where('branch_id',$request->branch_id)->where('status',1)->get();
        $html = "<option value=''>Select</option>";
        foreach ($data as $key => $value) {
            $html .= "<option value=".$value->id.">".$value->name."</option>";
        }
        $client = Client::where('branch_id',$request->branch_id)->get();
        $html1 = "<option value=''>Select</option>";
        foreach ($client as $keys => $values) {
            $html1 .= "<option value=".$values->id.">".$values->member."</option>";
        }
        $productcategory = ProductCategory::where('branch_id',$request->branch_id)->get();
        $html2 = "<option value=''>Select</option>";
        foreach ($productcategory as $keys => $values) {
            $html2 .= "<option value=".$values->id.">".$values->name."</option>";
        }
        $cp = ConsentPerson::where('branch_id',$request->branch_id)->get();
        $html3 = "<option value=''>Select</option>";
        foreach ($cp as $keys => $values) {
            $html3 .= "<option value=".$values->id.">".$values->name."</option>";
        }

        return response()->json(['data' => $html, 'data1' => $html1, 'data2' => $html2, 'data3' => $html3]);
    }
    public function getcompany(Request $request)
    {
        $data = Company::where('pro_category_id',$request->productcategory_id)->where('status',1)->get();
        $html = "<option value=''>Select</option>";
        foreach ($data as $key => $value) {
            $html .= "<option value=".$value->id.">".$value->name."</option>";
        }
        $data1 = Company::where('pro_category_id',$request->edit_productcategory_id)->where('status',1)->get();
        $html1 = "<option value=''>Select</option>";
        foreach ($data1 as $key => $value) {
            $html1 .= "<option value=".$value->id.">".$value->name."</option>";
        }
        return response()->json(['data'=>$html, 'data1'=>$html1]);
    }
    public function getproduct(Request $request)
    {
        $data = Product::where('company_id',$request->company_id)->where('status',1)->get();
        $html = "<option value=''>Select</option>";
        foreach ($data as $key => $value) {
            $html .= "<option value=".$value->id.">".$value->dropdown_name."</option>";
        }
        $data1 = Product::where('company_id',$request->edit_company_id)->where('status',1)->get();
        $html1 = "<option value=''>Select</option>";
        foreach ($data1 as $key => $value) {
            $html1 .= "<option value=".$value->id.">".$value->dropdown_name."</option>";
        }
        $data3 = Technician::where('company_id',$request->company_id)->get();
        $html3 = "<option value=''>Select</option>";
        foreach ($data3 as $key => $value) {
            $html3 .= "<option value=".$value->id.">".$value->technician_name."</option>";
        }
        return response()->json(['data'=>$html, 'data1'=>$html1, 'data3'=>$html3]);
    }
    public function deleteimage($id)
    {
        $serviceimage = ServiceImage::where('id',$id)->delete();
        return back();
    }
}