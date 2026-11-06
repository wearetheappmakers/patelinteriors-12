<?php
namespace App\Http\Controllers;
use App\User;
use DataTables;
use App\Task;
use App\Employee;
use App\Client;
use Auth;
use Mail;
use Illuminate\Http\Request;
class TaskController extends Controller
{
    public function __construct(Task $s)
    {
        $this->view = 'task';
        $this->route = 'task';
        $this->viewName = 'Task';
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
                $query = Task::where('employee_id',Auth::user()->id)->orWhere('assign_to',Auth::user()->id)->latest();
                if($request->is_completed == 1)
                {
                    $query = $query->where('is_completed',1);
                }
                else if($request->is_completed == 0)
                {
                    $query = $query->where('is_completed',0);
                }
                elseif($request->is_completed == 2) 
                {
                    $query = $query;
                }
                else
                {
                    $query = Task::latest();
                }
                if($request->employee_id)
                {
                    $query = $query->where('employee_id',$request->employee_id);
                }
                return Datatables::of($query)
                ->addColumn('action', function ($row) {
                    $btn = view('layouts.actionbtnpermission')->with(['id'=>$row->id,'route'=>'task','per_value'=>'task'])->render();      

                    $btn .= '&nbsp;&nbsp;<a style="background: white;" href="'.route('user.task.show',$row->id).'"  title="View Details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-eye"></i></a>';

                    return $btn;
                })
                ->addColumn('singlecheckbox', function ($row) {
                    $schk = view('layouts.singlecheckbox')->with(['id' => $row->id , 'status'=>$row->status])->render();
                    return $schk;
                })
                ->addColumn('is_completed', function ($row) {
                    if($row->is_completed == 0)
                    {
                        $is_completed = 'Pending';
                    }else
                    {
                        $is_completed = 'Completed';
                    }
                    return $is_completed;
                })
                ->addColumn('date', function ($row) {
                    $btn = date('d-m-Y',strtotime($row->created_at));
                    return $btn;
                })
                ->addColumn('employee_id', function ($row) {
                    if($row->employee)
                    {
                        $btn = $row->employee->name;
                    }
                    else
                    {
                        $btn = '-';
                    }
                    return $btn;
                })
                ->addColumn('assign', function ($row) {
                    $btn = Employee::where('id',$row->assign_to)->value('name');
                    return $btn;
                })
                ->addColumn('dropdown', function ($row) {
                    $btn = '<select class="form-control" onchange="status(this,'.$row->id.')"><option value="0"';
                    $selected = '';
                    if($row->status == 0)
                    {
                        $selected = 'selected';
                    }
                    $btn .= $selected.'>Pending</option><option value="1" ';
                    $selected = '';
                    if($row->status == 1)
                    {
                        $selected = 'selected';
                    }
                    $btn .= $selected.'>On hold</option> <option value="2"';
                    $selected = '';
                    if($row->status == 2)
                    {
                        $selected = 'selected';
                    }
                    $btn .= $selected.'>Completed</option><option value="3"';
                    $selected = '';
                    if($row->status == 3)
                    {
                        $selected = 'selected';
                    }
                    $btn .= $selected.'>Cancel</option><option value="4"';
                    $selected = '';
                    if($row->status == 4)
                    {
                        $selected = 'selected';
                    }
                    $btn .= $selected.'>Partially Completed</option>
                    </select>';
                    return $btn;
                })
                ->rawColumns(['numbers','action','date','singlecheckbox','is_completed','employee_id','dropdown','assign'])
                ->make(true);
            } 
        }
        else
        {
            if ($request->ajax()) {
                $query = Task::latest();
                if(Auth::user()->role == 2)
                {
                    $query = Task::where('branch_id',Auth::user()->id)->latest();
                }
                if($request->is_completed == 1)
                {
                    $query = $query->where('is_completed',1);
                }
                else if($request->is_completed == 0)
                {
                    $query = $query->where('is_completed',0);
                }
                elseif($request->is_completed == 2) 
                {
                    $query = $query;
                }
                else
                {
                    $query = Task::latest();
                }
                if($request->branch_id)
                {
                    $query = $query->where('branch_id',$request->branch_id);
                }
                if($request->employee_id)
                {
                    $query = $query->where('employee_id',$request->employee_id);
                }
                return Datatables::of($query)
                ->addColumn('action', function ($row) {
                    $btn = view('layouts.actionbtnpermission')->with(['id'=>$row->id,'route'=>'task','per_value'=>'task'])->render();  
                    $btn .= '&nbsp;&nbsp;<a style="background: white;" href="'.route('user.task.show',$row->id).'"  title="View Details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-eye"></i></a>';    
                    return $btn;
                })
                ->addColumn('date', function ($row) {
                    $btn = date('d-m-Y',strtotime($row->created_at));
                    return $btn;
                })
                ->addColumn('singlecheckbox', function ($row) {
                    $schk = view('layouts.singlecheckbox')->with(['id' => $row->id , 'status'=>$row->status])->render();
                    return $schk;
                })
                // ->addColumn('is_completed', function ($row) {
                //     if($row->is_completed == 0)
                //     {
                //         $is_completed = 'Pending';
                //     }else
                //     {
                //         $is_completed = 'Completed';
                //     }
                //     return $is_completed;
                // })
                ->addColumn('dropdown', function ($row) {
                    $btn = '<select class="form-control" onchange="status(this,'.$row->id.')"><option value="0"';
                    $selected = '';
                    if($row->status == 0)
                    {
                        $selected = 'selected';
                    }
                    $btn .= $selected.'>Pending</option><option value="1" ';
                    $selected = '';
                    if($row->status == 1)
                    {
                        $selected = 'selected';
                    }
                    $btn .= $selected.'>On hold</option> <option value="2"';
                    $selected = '';
                    if($row->status == 2)
                    {
                        $selected = 'selected';
                    }
                    $btn .= $selected.'>Completed</option><option value="3"';
                    $selected = '';
                    if($row->status == 3)
                    {
                        $selected = 'selected';
                    }
                    $btn .= $selected.'>Cancel</option><option value="4"';
                    $selected = '';
                    if($row->status == 4)
                    {
                        $selected = 'selected';
                    }
                    $btn .= $selected.'>Partially Completed</option>
                    </select>';
                    return $btn;
                })
                ->addColumn('branch_id', function ($row) {
                    $btn = $row->branch->name;
                    return $btn;
                })
                ->addColumn('employee_id', function ($row) {
                    if($row->employee)
                    {
                        $btn = $row->employee->name;
                    }
                    else
                    {
                        $btn = '-';
                    }
                    return $btn;
                })
                ->addColumn('assign', function ($row) {
                    $btn = Employee::where('id',$row->assign_to)->value('name');
                    return $btn;
                })
                ->rawColumns(['numbers','action','date','singlecheckbox','dropdown','employee_id','branch_id','assign'])
                ->make(true);
            }
        }
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        $data['employee'] = Employee::where('status',1)->get();
        if(Auth::user()->role == 2)
        {
            $data['employee'] = Employee::where('branch_id',Auth::user()->id)->where('status',1)->get();
        }
        return view('task.index')->with($data);
    }
    public function create()
    {
        $data['title'] = 'Add ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['branch'] = User::where('role',2)->get();
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 2)
            {
                $data['employee'] = Employee::where('branch_id',Auth::user()->id)->where('status',1)->get();
                $data['client'] = Client::where('branch_id',Auth::user()->id)->get();
            }
        }
        else
        {
            $data['employee'] = Employee::where('branch_id',Auth::user()->branch_id)->where('status',1)->get();
            $data['client'] = Client::where('branch_id',Auth::user()->branch_id)->get();
        }
        return view('general.add_form')->with($data);
    }
    public function status(Request $request)
    {
        $param['status'] = $request->status;
        $change_status = Task::where('id',$request->id)->update($param);
        $task = Task::where('id',$request->id)->first();
        $datas = [
            'task' => $task,
        ];
        $employee = Employee::where('id',$task->assign_to)->first();
        $email = $employee->email;
        $text = 'Status Change Task Name.' . $task->purpose;
        Mail::send('task.task_change',$datas, function($message) use($email,$text) {
            $message->to($email);
            $message->subject($text);
        });
        $stores_token = Employee::where('id',$employee->id)->value('push_token');
        if($stores_token)
        {
            $device_token = $stores_token;
            $urls = env('Task_employee_link');
            $name = 'Task';
            $leads_id = $task->id;
            $title = 'Status Created !!';
            $body = 'Status Change Task Name.' . $task->purpose;
            $this->sendnotification($device_token,$title,$body,$urls,$leads_id,$name);
        }
        $datas = [
            'task' => $task,
        ];
        $employee = Employee::where('id',$task->employee_id)->first();
        $email = $employee->email;
        $text = 'Status Change Task Name. ' . $task->purpose;
        Mail::send('task.task_change',$datas, function($message) use($email,$text) {
            $message->to($email);
            $message->subject($text);
        });
        $stores_token = Employee::where('id',$employee->id)->value('push_token');
        if($stores_token)
        {
            $device_token = $stores_token;
            $urls = env('Task_employee_link');
            $name = 'Task';
            $leads_id = $task->id;
            $title = 'Status Created !!';
            $body = 'Status Change Task Name.' . $task->purpose;
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
        $task = new Task();
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $task->branch_id = $request->branch_id;
                $task->employee_id = $request->employee_id;
            }
            else
            {
                $task->branch_id = Auth::user()->id;
                $task->employee_id = $request->employee_id;
            }
        }
        else
        {
            $task->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
            $task->employee_id = Auth::user()->id;
        }
        $task->client_id = $request->client_id;
        $task->mobile = $request->mobile;
        $task->purpose = $request->purpose;
        $task->assign_to = $request->employee_ids;
        $task->is_completed = $request->is_completed ?? 0;
        $task->description = $request->description;
        $task->save();
        $datas = [
            'task' => $task,
        ];
        $employee = Employee::where('id',$task->assign_to)->first();
        $email = $employee->email;
        $text = 'New Task Created Name.' . $task->purpose;
        Mail::send('task.task_create',$datas, function($message) use($email,$text) {
            $message->to($email);
            $message->subject($text);
        });
        $stores_token = Employee::where('id',$employee->id)->value('push_token');
        if($stores_token)
        {
            $device_token = $stores_token;
            $urls = env('Task_employee_link');
            $name = 'Task';
            $leads_id = $task->id;
            $title = 'Status Created !!';
            $body = 'Status Change Task Name.' . $task->purpose;
            $this->sendnotification($device_token,$title,$body,$urls,$leads_id,$name);
        }
        if($task)
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
        $data['data'] = Task::where('id', $id)->first();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['branch'] = User::where('role',2)->get();
        if(Auth::guard('user')->check())
        {   
            if(Auth::user()->role == 1)
            {
                $data['employee'] = Employee::where('branch_id',$data['data']->branch_id)->get();
            }
            else
            {
                $data['employee'] = Employee::where('branch_id',Auth::user()->id)->where('status',1)->get();
                $data['client'] = Client::where('branch_id',Auth::user()->id)->get();
            }
        }
        else
        {
            $data['employee'] = Employee::where('branch_id',Auth::user()->branch_id)->where('status',1)->get();
            $data['client'] = Client::where('branch_id',Auth::user()->branch_id)->get();
        }
        return view('general.edit_form')->with($data);
    }

    public function show($id)
    {
        $data['title'] = 'Edit ' . $this->viewName;
        $data['data'] = Task::where('id', $id)->first();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['branch'] = User::where('role',2)->get();
        if(Auth::user()->role == 1)
        {
            $data['employee'] = Employee::get();
        }
        else
        {
            $data['employee'] = Employee::where('branch_id',Auth::user()->id)->get();
        }
        return view('task.show')->with($data);
    }
    public function update(Request $request, $id)
    {
        $task = Task::where('id',$id)->first();
        $task->purpose = $request->purpose;
        $task->assign_to = $request->employee_ids;
        $task->is_completed = $request->is_completed ?? 0;
        $task->description = $request->description;
        $task->save();
        $datas = [
            'task' => $task,
        ];
        $employee = Employee::where('id',$task->assign_to)->first();
        $email = $employee->email;
        $text = 'Task Updated Name.' . $task->purpose;
        Mail::send('task.task_create',$datas, function($message) use($email,$text) {
            $message->to($email);
            $message->subject($text);
        });
        $stores_token = Employee::where('id',$employee->id)->value('push_token');
        if($stores_token)
        {
            $device_token = $stores_token;
            $urls = env('Task_employee_link');
            $name = 'Task';
            $leads_id = $task->id;
            $title = 'Task Updated !!';
            $body = 'Task Updated Name.' . $task->purpose;
            $this->sendnotification($device_token,$title,$body,$urls,$leads_id,$name);
        }
        $datas = [
            'task' => $task,
        ];
        $employee = Employee::where('id',$task->employee_id)->first();
        $email = $employee->email;
        $text = 'Task Updated' . $task->purpose;
        Mail::send('task.task_create',$datas, function($message) use($email,$text) {
            $message->to($email);
            $message->subject($text);
        });
        $stores_token = Employee::where('id',$employee->id)->value('push_token');
        if($stores_token)
        {
            $device_token = $stores_token;
            $urls = env('Task_employee_link');
            $leads_id = $task->id;
            $name = 'Task';
            $title = 'Task Updated !!';
            $body = 'Task Updated Name.' . $task->purpose;
            $this->sendnotification($device_token,$title,$body,$urls,$leads_id,$name);
        }
        if($task)
        {
            return response()->json(['status' => 'success']);
        }else
        {
            return response()->json(['status' => 'error']);
        }
    }
    public function getemployee(Request $request)
    {
        $data = Employee::where('branch_id',$request->branch_id)->get();
        $html = "<option value=''>Select</option>";
        foreach ($data as $key => $value) {
            $html .= "<option value=".$value->id.">".$value->name."</option>";
        }

        $client = Client::where('branch_id',$request->branch_id)->get();
        $html1 = "<option value=''>Select</option>";
        foreach ($client as $keys => $values) {
            $html1 .= "<option value=".$values->id.">".$values->member."</option>";
        }
        
        return response()->json(['data' => $html, 'data1' => $html1]);
    }
    public function getclientdata(Request $request)
    {
        $html = Client::where('id',$request->client_id)->value('mobile_no');

        return response()->json(['data' => $html]);
    }
}