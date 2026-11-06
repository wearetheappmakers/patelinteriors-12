<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use JWTAuth;
use App\Employee;
use App\Attendance;
use App\Leave;
use App\LeadAssign;
use App\LeadFollowUp;
use App\LeadCommunication;
use App\LeadBasicChild;
use App\ClientDocument;
use App\Quotation;
use App\Supervisor;
use App\Company;
use App\LeadSource;
use App\Type;
use App\RevisedQuotation;
use App\RevisedMainChild;
use App\RevisedChild;
use App\LeadBasicDesignChild;
use App\QuotationMainChild;
use App\WarehouseInventory;
use App\Delivery;
use App\LeadBasicMainChild;
use App\ContactMaster;
use App\LeadStage;
use App\QuotationChild;
use JWTAuthException;
use App\User;
use App\Lead;
use App\Client;
use App\Complain;
use App\Product;
use Mail;
use Auth;
use App\Task;
use App\ProductCategory;
use Carbon\Carbon;
use DB;

class EmployeeLoginController extends Controller
{
     public function __construct(Employee $employee)
     {
          $this->user = $employee;
          \Config::set('jwt.user', "App\Employee");
          \Config::set('auth.providers', ['users' => [
               'driver' => 'eloquent',
               'model' => \App\Employee::class,
          ]]);
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
          $SERVER_API_KEY = env('Admin_server');;
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

     public function employee_login(Request $request)
     {
          $credentials = request(['email', 'password']);
          if (! $token = JWTAuth::attempt($credentials)) {
               return response()->json(['success' => 0, 'message' => 'These credentials do not match our records.'], 200);
          }

          $employee = Employee::where('id',JWTAuth::user()->id)->first();

          if($employee->status == 1)
          {
               $check = Employee::with('branchs')->where('email',$request->email)->first();
               if($request->push_token)
               {
                    $check->push_token = $request->push_token;
                    $check->save();
               }
               else
               {
                    $check->push_token = NULL;
                    $check->save();
               }
               // $branch = User::where('id',$check->branch_id)->first();
               // $data_att = Attendance::where('employee_id',$check->id)->whereDate('created_at',Carbon::today())->first();
               // $leave = Attendance::where('employee_id',$check->id)->whereDate('date',date('Y-m-d'))->first();
               // if(!empty($leave)){
               //      return response()->json(['success' => 1, 'token' => $token,'user'=>$check,'message'=>'Login Successfully.']);
               // }else if(!empty($data_att)){
               //      return response()->json(['success' => 1, 'token' => $token,'user'=>$check,'message'=>'Login Successfully.']);
               // }
               // else
               // {
               //      $param['date'] = date('Y-m-d');
               //      $param['time'] = date('H:i:s');
               //      $param['employee_id'] = $check->id;
               //      $param['branch_id'] = $check->branch_id;
               //      $param['attendance'] = 'Not Approved ';
               // }
               return response()->json(['success' => 1, 'token' => $token,'user'=>$check,'message'=>'Login Successfully.']);
          }
          else
          {
               return response()->json(['success' => 0, 'message'=>'Status In Active.']);
          }

     }

     public function employee_create_attendance(Request $request)
     {
          $check = Attendance::where('employee_id', JWTAuth::user()->id)->where('date', now()->toDateString())->first();

          if ($check) {
               return response()->json(['success' => 0, 'message' => 'You have already marked attendance today.']);
          }

          if (!$request->hasFile('photo')) {
               return response()->json(['success' => 0, 'message' => 'No photo was uploaded.']);
          }

          $employee = Employee::find(Auth::id());
          $officeStart = Carbon::parse($employee->office_start_time);
          $proxyTime = $officeStart->copy()->addMinutes($employee->buffer_minutes);
          $currentTime = Carbon::now();

          $status = 0;
          if($employee->office_start_time != null)
          {
               if ($currentTime->greaterThan($proxyTime)) {
                    $status = 2;
               } else {
                    $status = 1;
               }
          }

          $attendance = new Attendance();
          $attendance->branch_id = JWTAuth::user()->branch_id;
          $attendance->employee_id = JWTAuth::user()->id;

          $year = Carbon::now()->format('Y');
          $month = Carbon::now()->format('m');
          $path = 'attendance/' . $year . '/' . $month;
          $filename = time() . '.png';
          $request->file('photo')->move(public_path($path), $filename);
          $attendance->photo = $path . '/' . $filename;

          $attendance->attendance = 'Not Approved';
          $attendance->date = now()->toDateString();
          $attendance->time = now()->toTimeString();
          $attendance->latitude = $request->latitude;
          $attendance->longitude = $request->longitude;
          $attendance->attendance_status = $status;
          $attendance->save();

          return response()->json(['success' => 1, 'message' => 'Attendance saved successfully.']);
     }

     public function getemployeeleave()
     {
          $attendance = Leave::with('employee','branch')->where('employee_id',JWTAuth::user()->id)->orderBy('id', 'DESC')->get();
          return response()->json(['success'=>1,'data' => $attendance]);
     }

     public function employeeget()
     {
          $data = Employee::where('branch_id',JWTAuth::user()->branch_id)->where('status',1)->where('role','!=',3)->get();
          return response()->json(['success'=>1,'data' => $data]);
     }

     public function getdatatask(Request $request)
     {
          $this->validate($request, [
               'id' => 'required',

          ]);
          $task = Task::with('employee','branch','assign_to')->where('id',$request->id)->first();
          return response()->json(['success'=>1,'data' => $task]);

     }

     public function employeecreateleave(Request $request)
     {
          $this->validate($request, [
               'from_date' => 'required',
               'to_date' => 'required',
          ]);
          $leave = new Leave();
          $leave->branch_id = Employee::where('id',JWTAuth::user()->id)->value('branch_id');
          $leave->employee_id = JWTAuth::user()->id;
          $leave->from_date = $request->from_date;
          $leave->to_date = $request->to_date;
          $leave->subject = $request->subject;
          $leave->reason = $request->reason;
          $leave->save();
          $datas = [
               'leave' => $leave,
          ];
          $branch = User::where('id',$leave->branch_id)->first();
          $employee = Employee::where('id',$leave->employee_id)->first();
          $email = $branch->email;
          $text = 'Leave Request for ' .$employee->name;
          Mail::send('leave.leave_request',$datas, function($message) use($email,$text) {
               $message->to($email);
               $message->subject($text);
          });
          $stores_token = User::where('id',$branch->id)->value('push_token');
          if($stores_token)
          {
               $device_token = $stores_token;
               $urls = env('Leave_admin_link');
               $leads_id = $leave->id;
               $name = 'Leave';
               $title = 'Leave Status Change !!';
               $body = 'Leave Request for ' . $employee->name;

               $this->adminsendnotification($device_token,$title,$body,$urls,$leads_id,$name);
          }
          $datas = [
               'leave' => $leave,
          ];
          $branch = User::where('id',1)->first();
          $employee = Employee::where('id',$leave->employee_id)->first();
          $email = $branch->email;
          $text = 'Leave Request for ' .$employee->name;
          Mail::send('leave.leave_request',$datas, function($message) use($email,$text) {
               $message->to($email);
               $message->subject($text);
          });
          $stores_token = User::where('id',$branch->id)->value('push_token');
          if($stores_token)
          {
               $device_token = $stores_token;
               $title = 'Leave Status Change !!';
               $urls = env('Leave_admin_link');
               $name = 'Leave';
               $leads_id = $leave->id;
               $body = 'Leave Request for ' . $employee->name;

               $this->adminsendnotification($device_token,$title,$body,$urls,$leads_id,$name);
          }
          return response()->json(['success'=>1,'message' => 'Leave Created Successfully']);
     }

     public function employeestatusleave(Request $request)
     {
          $this->validate($request, [
               'id' => 'required',
               'status' => 'required',
          ]);
          $leave = Leave::where('id',$request->id)->first();
          if($request->status == 1)
          {
               $leave['approved_from_date'] = $request->approved_from_date;
               $leave['approved_to_date'] = $request->approved_to_date;
               $approved_from_date = $request->approved_from_date;
               $approved_to_date = $request->approved_to_date;
               $days = Carbon::parse($approved_to_date)->diffInDays($approved_from_date);
               for ($i = $approved_from_date; $i <= $approved_to_date; $i++) {
                    if($i == $approved_from_date)
                    {
                         $adddate = date('Y-m-d', strtotime($approved_from_date));
                    }
                    else
                    {
                         $adddate = date('Y-m-d',strtotime($attendance->date. ' +1 days'));
                    }
                    $attendance = Attendance::where('date',$adddate)->where('employee_id',$leave->employee_id)->first();
                    if($attendance)
                    {
                    }
                    else
                    {
                         $attendance = new Attendance();
                         $attendance->branch_id = $leave->branch_id;
                         $attendance->employee_id = $leave->employee_id;
                         $attendance->date = $adddate;
                         $attendance->time = Carbon::now()->format('H:i:s');
                         $attendance->attendance = 'Leave';
                         $attendance->save();
                    }
               }
               $leave = Leave::where('id',$request->id)->first();
               $datas = [
                    'leave' => $leave,
               ];
               $employee = Employee::where('id',$leave->employee_id)->first();
               $email = $employee->email;
               $text = 'Leave Approved for ' .$employee->name;
               Mail::send('leave.leave_approved',$datas, function($message) use($email,$text) {
                    $message->to($email);
                    $message->subject($text);
               });
               $stores_token = Employee::where('id',$employee->id)->value('push_token');
               if($stores_token)
               {
                    $device_token = $stores_token;
                    $title = 'Leave Status Change !!';
                    $body = 'Leave Approved for ' . $employee->name;
                    $urls = env('Leave_employee_link');
                    $name = 'Leave';
                    $leads_id = $leave->id;

                    $this->sendnotification($device_token,$title,$body,$urls,$leads_id,$name);
               }
          }
          else if($request->status == 2)
          {
               $leave = Leave::where('id',$request->id)->first();
               $datas = [
                    'leave' => $leave,
               ];
               $employee = Employee::where('id',$leave->employee_id)->first();
               $email = $employee->email;
               $text = 'Leave Reject for ' .$employee->name;
               Mail::send('leave.leave_reject',$datas, function($message) use($email,$text) {
                    $message->to($email);
                    $message->subject($text);
               });
               $stores_token = Employee::where('id',$employee->id)->value('push_token');
               if($stores_token)
               {
                    $device_token = $stores_token;
                    $title = 'Leave Status Change !!';
                    $body = 'Leave Reject for ' . $employee->name;
                    $urls = env('Leave_employee_link');
                    $name = 'Leave';
                    $leads_id = $leave->id;

                    $this->sendnotification($device_token,$title,$body,$urls,$leads_id,$name);
               }
          }
          $leave->status = $request->status;
          $leave->save();
          return response()->json(['success'=>1,'message' => 'Status Change Successfully']);
     }

     public function employeegetdatalead(Request $request)
     {
          $this->validate($request, [
               'id' => 'required',
          ]);
          $lead = Lead::with('branch','employee','client','source','sourcename','mainchild','mainchild.leadbasicchild','mainchild.leadbasicchild.productcategory','mainchild.leadbasicchild.company','mainchild.leadbasicchild.product','mainchild.leadbasicchild.warehouse','lead_assign.stage','mainchild.employees','basicdesign')->where('id',$request->id)->first();
          $array = array();
          $param = explode(',', $lead->lead_name);
          $lead->lead_names_array = ProductCategory::whereIn('id',$param)->get();
          return response()->json(['success'=>1,'data' => $lead]);
     }

     public function getemployeeprofile()    
     {
          $employee = Employee::with('branchs')->where('id',JWTAuth::user()->id)->first();
          return response()->json(['success' => 1, 'data'=>$employee]);
     }

     public function getemployeeclient()
     {
          $client = Client::where('employee_id',JWTAuth::user()->id)->with('branch','employee')->orderBy('id', 'DESC')->get();
          return response()->json(['success' => 1, 'data'=>$client]);
     }

     public function employeegetdataleave(Request $request)
     {
          $this->validate($request, [
               'id' => 'required',

          ]);
          $leave = Leave::with('branch','employee')->where('id',$request->id)->first();
          return response()->json(['success'=>1,'data' => $leave]);
     }

     public function employeecreateclient(Request $request)
     {
          $this->validate($request, [
               'member' => 'required',
          ]);
          $client = new Client();
          $client->branch_id = Employee::where('id',JWTAuth::user()->id)->value('branch_id');
          $client->employee_id = JWTAuth::user()->id;
          $client->member = $request->member;
          $client->address = $request->address;
          $client->city = $request->city;
          $client->mobile_no = $request->mobile_no;
          $client->other_mobile_no = $request->other_mobile_no;
          $client->email = $request->email;
          $client->referred_type = $request->referred_type;
          $client->dob = $request->dob;
          $client->is_archive = $request->is_archive ?? 0;
          $client->save();
          return response()->json(['success'=>1,'message' => 'Client Created Successfully.']);
     }

     public function employeeupdateclient(Request $request)
     {
          $this->validate($request, [
               'id' => 'required',
               'member' => 'required',
          ]);
          $client = Client::where('id',$request->id)->first();
          $client->branch_id = Employee::where('id',JWTAuth::user()->id)->value('branch_id');
          $client->employee_id = JWTAuth::user()->id;
          $client->member = $request->member;
          $client->address = $request->address;
          $client->city = $request->city;
          $client->mobile_no = $request->mobile_no;
          $client->other_mobile_no = $request->other_mobile_no;
          $client->email = $request->email;
          $client->dob = $request->dob;
          $client->is_archive = $request->is_archive ?? 0;
          $client->save();
          return response()->json(['success'=>1,'message' => 'Client Updated Successfully.']);
     }

     public function employeecreateclientdocument(Request $request)
     {
          $this->validate($request, [
               'client_id' => 'required',
          ]);
          $clientdocument = ClientDocument::where('client_id',$request->client_id)->first();
          if($clientdocument)
          {
               $clientdocument->client_id = $request->client_id;
               $clientdocument->distributor_id = $request->distributor_id;
               $clientdocument->status = $request->status;
               $clientdocument->tracking_no = $request->tracking_no;
               $clientdocument->tracking_link = $request->tracking_link;
               $clientdocument->date = $request->date;
               $clientdocument->remark = $request->remark;
               $clientdocument->aadhar_card = $request->aadhar_card;
               if($request->aadhar_img)
               {
                    $imageName = time().'.'.$request->aadhar_img->getClientOriginalExtension();
                    $request->aadhar_img->move(public_path('aadharimage/'), $imageName);
                    $clientdocument['aadhar_img'] = $imageName;
               }
               $clientdocument->pan_card = $request->pan_card;
               if($request->pan_img)
               {
                    $imageName = time().'.'.$request->pan_img->getClientOriginalExtension();
                    $request->pan_img->move(public_path('panimage/'), $imageName);
                    $clientdocument['pan_img'] = $imageName;
               }
               $clientdocument->cancelled_cheque = $request->cancelled_cheque;
               if($request->cancelledcheque_img)
               {
                    $imageName = time().'.'.$request->cancelledcheque_img->getClientOriginalExtension();
                    $request->cancelledcheque_img->move(public_path('cancelledchequeimage/'), $imageName);
                    $clientdocument['cancelledcheque_img'] = $imageName;
               }
               $clientdocument->rtgs_details = $request->rtgs_details;
               if($request->rtgs_img)
               {
                    $imageName = time().'.'.$request->rtgs_img->getClientOriginalExtension();
                    $request->rtgs_img->move(public_path('rtgsimage/'), $imageName);
                    $clientdocument['rtgs_img'] = $imageName;
               }
               $clientdocument->form_submission = $request->form_submission;
               if($request->form_submission_img)
               {
                    $imageName = time().'.'.$request->form_submission_img->getClientOriginalExtension();
                    $request->form_submission_img->move(public_path('formsubmissionimage/'), $imageName);
                    $clientdocument['form_submission_img'] = $imageName;
               }
               $clientdocument->machine_delivered = $request->machine_delivered;
               if($request->machine_delivered_img)
               {
                    $imageName = time().'.'.$request->machine_delivered_img->getClientOriginalExtension();
                    $request->machine_delivered_img->move(public_path('machinedeliveredimage/'), $imageName);
                    $clientdocument['machine_delivered_img'] = $imageName;
               }
               $clientdocument->gst = $request->gst;
               if($request->gst_img)
               {
                    $imageName = time().'.'.$request->gst_img->getClientOriginalExtension();
                    $request->gst_img->move(public_path('gstimage/'), $imageName);
                    $clientdocument['gst_img'] = $imageName;
               }
               $clientdocument->save();
          }
          else
          {
               $clientdocument = new ClientDocument();
               $clientdocument->client_id = $request->client_id;
               $clientdocument->product_id = $request->product_id;
               $clientdocument->lead_id = $request->lead_id;
               $clientdocument->distributor_id = $request->distributor_id;
               $clientdocument->status = $request->status;
               $clientdocument->tracking_no = $request->tracking_no;
               $clientdocument->tracking_link = $request->tracking_link;
               $clientdocument->date = $request->date;
               $clientdocument->remark = $request->remark;
               $clientdocument->aadhar_card = $request->aadhar_card;
               if($request->aadhar_img)
               {
                    $imageName = time().'.'.$request->aadhar_img->getClientOriginalExtension();
                    $request->aadhar_img->move(public_path('aadharimage/'), $imageName);
                    $clientdocument['aadhar_img'] = $imageName;
               }
               $clientdocument->pan_card = $request->pan_card;
               if($request->pan_img)
               {
                    $imageName = time().'.'.$request->pan_img->getClientOriginalExtension();
                    $request->pan_img->move(public_path('panimage/'), $imageName);
                    $clientdocument['pan_img'] = $imageName;
               }
               $clientdocument->cancelled_cheque = $request->cancelled_cheque;
               if($request->cancelledcheque_img)
               {
                    $imageName = time().'.'.$request->cancelledcheque_img->getClientOriginalExtension();
                    $request->cancelledcheque_img->move(public_path('cancelledchequeimage/'), $imageName);
                    $clientdocument['cancelledcheque_img'] = $imageName;
               }
               $clientdocument->rtgs_details = $request->rtgs_details;
               if($request->rtgs_img)
               {
                    $imageName = time().'.'.$request->rtgs_img->getClientOriginalExtension();
                    $request->rtgs_img->move(public_path('rtgsimage/'), $imageName);
                    $clientdocument['rtgs_img'] = $imageName;
               }
               $clientdocument->form_submission = $request->form_submission;
               if($request->form_submission_img)
               {
                    $imageName = time().'.'.$request->form_submission_img->getClientOriginalExtension();
                    $request->form_submission_img->move(public_path('formsubmissionimage/'), $imageName);
                    $clientdocument['form_submission_img'] = $imageName;
               }
               $clientdocument->machine_delivered = $request->machine_delivered;
               if($request->machine_delivered_img)
               {
                    $imageName = time().'.'.$request->machine_delivered_img->getClientOriginalExtension();
                    $request->machine_delivered_img->move(public_path('machinedeliveredimage/'), $imageName);
                    $clientdocument['machine_delivered_img'] = $imageName;
               }
               $clientdocument->gst = $request->gst;
               if($request->gst_img)
               {
                    $imageName = time().'.'.$request->gst_img->getClientOriginalExtension();
                    $request->gst_img->move(public_path('gstimage/'), $imageName);
                    $clientdocument['gst_img'] = $imageName;
               }
               $clientdocument->save();
          }
          return response()->json(['success'=>1,'message' => 'Client Document Created Successfully.']);
     }

     public function employeedeleteclient(Request $request)
     {
          $this->validate($request, [
               'id' => 'required',
          ]);
          $client = Client::where('id',$request->id)->delete();
          return response()->json(['success'=>1,'message' => 'Client Deleted Successfully.']);
     }

     public function updateemployeeprofile(Request $request)
     {
          $this->validate($request, [
               'id' => 'required',
               'first_name' => 'required',
               'last_name' => 'required',
               'email' => 'required',
          ]);
          $employee = Employee::where('id',$request->id)->first();
          $employee->first_name = $request->first_name;
          // $employee->supervisor_id = $request->supervisor_id;
          $employee->last_name = $request->last_name;
          $employee->name = $request->name;
          $employee->gender = $request->gender;
          $employee->email = $request->email;
          // $employee->role = $request->role;
          $employee->mobile_no = $request->mobile_no;
          // $employee->other_mobile_no = $request->other_mobile_no;
          // $employee->address = $request->address;
          // $employee->password = $request->password;
          // $employee->confirm_password = $request->confirm_password;
          // $employee->dob = $request->dob;
          // $employee->join_date = $request->join_date;
          if(isset($request['image']))
          {
               $imageName = time().'.'. $request->image->getClientOriginalExtension();
               $request->image->move(public_path('employee/'),$imageName);
               $employee->image = $imageName;
          }
          $employee->save();
          return response()->json(['success'=>1,'message' => 'Employee Updated Successfully.']);
     }

     public function getemployeelead()
     {
          $data = LeadAssign::get();
          $arr = [];
          foreach($data as $dt)
          {
               $ex_arr = explode(',', $dt->employee_id);
               if(in_array(JWTAuth::user()->id,$ex_arr))
               {
                    array_push($arr,$dt->lead_id);
               }
          }
          $lead = Lead::with('branch','employee','client','lead_assign','source','sourcename','mainchild','mainchild.leadbasicchild','mainchild.leadbasicchild.productcategory','mainchild.leadbasicchild.company','mainchild.leadbasicchild.product','mainchild.leadbasicchild.warehouse','lead_assign.stage','mainchild.employees','basicdesign','lead_followup','lead_followup.types')->orWhereIn('id',$arr)->latest()->get();

          $array = array();
          foreach ($lead as $key => $value) {
               $param = explode(',', $value->lead_name);
               $value->lead_names_array = ProductCategory::whereIn('id',$param)->get();
               $employees = explode(',', $value->lead_assign->employee_id);

               $value->lead_assign_employee = Employee::whereIn('id',$employees)->get();
               $value->lead_followup_employee = LeadFollowUp::where('employee_id',JWTAuth::user()->id)->where('lead_id',$value->id)->first();
               $quotation = Quotation::where('lead_id',$value->id)->first();
               if($quotation)
               {
                    $value->quot_status = 1;
               }
               else
               {
                    $value->quot_status = 0;
               }


          }


          return response()->json(['success'=>1,'data' =>$lead]);
     }

     public function employeecreateleadbasic(Request $request)
     {
          $this->validate($request, [
               'client_id' => 'required',
               'source_id' => 'required',
          ]);
          $lead = new Lead();
          $lead->client_id = $request->client_id;
          $lead->branch_id = Employee::where('id',JWTAuth::user()->id)->value('branch_id');
          $lead->employee_id = JWTAuth::user()->id;

          $params = implode(",",$request->lead_name);
          $lead->lead_name = $params;
          $lead->description = $request->description;
          $lead->source_id = $request->source_id;
          $lead->source_name = $request->source_name;
          $lead->date = $request->date;
          $lead->current_project_phase = $request->current_project_phase;
          $lead->lead_value = $request->lead_value;
          $lead->app_link =  env('Lead_admin_link');
          $lead->employee_app_link = env('Lead_employee_link');
          $check = Lead::where('client_id',$request->client_id)->first();
          if(isset($check))
          {
               $assign = LeadAssign::where('lead_id',$check->id)->first();

               $employee = explode(',', $assign->employee_id);
               $array = [];
               foreach ($employee as $key => $value) {
                    $data = Employee::where('id',$value)->value('name');
                    array_push($array, $data);
               }
               $datas = implode(',', $array);
               if($check){
                    return response()->json(['status' => false, 'msg' => 'Duplicate Client found !']);
               }
          }

          $lead->status = $request->status;
          $lead->save();
          $lead_assign = new LeadAssign();
          $lead_assign->lead_id = $lead->id;
          $lead_assign->employee_id = $lead->employee_id;
          $lead_assign->save();

          $lead_followup = new LeadFollowUp();
          $lead_followup->lead_id = $lead->id;
          $lead_followup->employee_id = $lead->employee_id;
          $lead_followup->save();
          // foreach($request['name'] as $key=>$value)
          // {
          //      if($value != '')
          //      {
          //           $lead_basic_main_child['lead_id'] = $lead->id;
          //           $lead_basic_main_child['name'] = $value;
          //           $lead_basic_main_child['employee_id'] = $request->employee_id[$key];
          //           $lead_basic_main_child_store = LeadBasicMainChild::create($lead_basic_main_child);
          //           foreach($request['productcategory_id'][$key] as $keys=>$values)
          //           {
          //                if ($key == $request['main_id'][$key][$keys]) 
          //                {
          //                     if($values != '')
          //                     {
          //                          $child = new LeadBasicChild();
          //                          $child->lead_id = $lead->id;
          //                          $child->lead_basic_main_id = $lead_basic_main_child_store->id;
          //                          $child->productcategory_id = $values;
          //                          $child->company_id = $request->company_id[$key][$keys];
          //                          $child->product_id = $request->product_id[$key][$keys];
          //                          $child->warehouse_id = $request->warehouse_id[$key][$keys];
          //                          $child->qty = $request->qty[$key][$keys];
          //                          $child->save();
          //                     }
          //                }
          //           }
          //      }
          // }
          if($request->design_date)
          {
               foreach ($request->design_date as $keyss => $valuess) {
                    if($valuess != '')
                    {
                         $design_child = new LeadBasicDesignChild();
                         $design_child->lead_id = $lead->id;
                         $design_child->date = $valuess;
                         if(isset($request['design']))
                         {
                              $imageName =  time().$keyss.'.'. $request->design[$keyss]->getClientOriginalExtension();
                              $request->design[$keyss]->move(public_path('design/'),$imageName);
                              $design_child->design = $imageName;
                         }
                         $design_child->save();
                    }
               }
          }

          $datas = [
               'lead' => $lead,
          ];
          $employee = Employee::where('id',$lead_assign->employee_id)->first();
          $email = $employee->email;
          $client = Client::where('id',$lead->client_id)->value('member');
          $text = 'Lead Created Name.' . $client;
          Mail::send('lead.lead_create',$datas, function($message) use($email,$text) {
               $message->to($email);
               $message->subject($text);
          });
          $client = Client::where('id',$lead->client_id)->value('member');
          $stores_token = Employee::where('id',$employee->id)->value('push_token');
          if($stores_token)
          {
               $device_token = $stores_token;
               $urls = env('Lead_employee_link');
               $leads_id = $lead->id;
               $name = 'Lead';
               $title = 'Lead Created !!';
               $body = 'Lead Created Name.' . $client;

               $this->sendnotification($device_token,$title,$body,$urls,$leads_id,$name);
          }
          $datas = [
               'lead' => $lead,
          ];
          $branch = User::where('id',$lead->branch_id)->first();
          $client = Client::where('id',$lead->client_id)->value('member');
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
               $title = 'Lead Created !!';
               $body = 'Lead Created Name.' . $client;
               $this->adminsendnotification($device_token,$title,$body,$urls,$leads_id,$name);
          }
          return response()->json(['success' => 1, 'message' => 'Created Lead Successfully.']);
     }

     public function employeecreateleadassign(Request $request)
     {
          $this->validate($request, [
               'lead_id' => 'required',
          ]);
          $lead_assign = LeadAssign::where('lead_id',$request->lead_id)->first();
          if($lead_assign)
          {
               $lead_assign->lead_id = $request->lead_id;
               $lead_assign->stage_id = $request->stage_id;
               $lead_assign->section_id = $request->section_id;
               $params = implode(",",$request->employee_id);
               $lead_assign->employee_id = $params;

               $lead_assign->note = $request->note;
               $lead_assign->save();
               $expload = explode(',',$lead_assign->employee_id);
               $followup = LeadFollowUp::where('lead_id',$request->lead_id)->get();
               $array = [];
               foreach($followup as $fp)
               {
                    array_push($array,$fp->employee_id);
               }
               $deleted_emp = array_diff($array,$expload);
               LeadFollowUp::where('lead_id',$request->lead_id)->whereIn('employee_id',$deleted_emp)->delete();
               foreach($expload as $key => $value) {
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
          }
          else
          {
               $lead_assign = new LeadAssign();
               $lead_assign->lead_id = $request->lead_id;
               $lead_assign->stage_id = $request->stage_id;
               $params = implode(",",$request->employee_id);
               $lead_assign['employee_id'] = $params;
               $lead_assign->note = $request->note;
               $lead_assign->save();
               foreach($request->employee_id as $key => $value) {
                    if($value != '')
                    {
                         $childs = new LeadFollowUp();
                         $childs->lead_id = $request->lead_id;
                         $childs->employee_id = $value;
                         $childs->save();
                    }
               }
          }
          $lead = Lead::where('id',$lead_assign->lead_id)->first();
          $client =Client::where('id',$lead->client_id)->value('member');
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
               $client =Client::where('id',$lead->client_id)->value('member');
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
          $client = Client::where('id',$lead->client_id)->value('member');
          $email = $branch->email;
          $text = 'Lead Updated Name.' . $client;
          Mail::send('lead.lead_create',$datas, function($message) use($email,$text) {
               $message->to($email);
               $message->subject($text);
          });
          $client = Client::where('id',$lead->client_id)->value('member');
          $stores_token = User::where('id',$branch->id)->value('push_token');
          if($stores_token)
          {
               $device_token = $stores_token;
               $urls = env('Lead_admin_link');
               $leads_id = $lead->id;
               $title = 'Lead Updated !!';
               $name = 'Lead';
               $body = 'Lead Updated Name.' . $client;
               $this->adminsendnotification($device_token,$title,$body,$urls,$leads_id,$name);
          }
          return response()->json(['success' => 1, 'message' => 'Lead Assign Created Successfully']);
     }

     public function updateleadassign(Request $request)
     {
          $this->validate($request, [
               'id' => 'required',
               'lead_id' => 'required',
          ]);
          $lead_assign = LeadAssign::where('lead_id',$request->lead_id)->first();
          $lead_assign->lead_id = $request->lead_id;
          $lead_assign->stage_id = $request->stage_id;
          $lead_assign->section_id = $request->section_id;
          $lead_assign->employee_id = $request->employee_id;
          $lead_assign->note = $request->note;
          $lead_assign->date = $request->date;
          $lead_assign->save();
     }

     public function employeecreateleadfollowup(Request $request)
     {
          $this->validate($request, [
               'employee_id' => 'required',
               'lead_id' => 'required',
          ]);
          $lead_followup = LeadFollowUp::where('employee_id',$request->employee_id)->where('lead_id',$request->lead_id)->first();
          if($lead_followup)
          {
               $lead_followup->lead_id = $request->lead_id;
               $lead_followup->employee_id = JWTAuth::user()->id;
               $lead_followup->subject = $request->subject;
               $lead_followup->description = $request->description;
               $lead_followup->date = $request->date;
               $lead_followup->time = $request->time;
               $lead_followup->type = $request->type;
               $lead_followup->is_completed = $request->is_completed;
               $lead_followup->save(); 
          }
          else
          {
               $lead_followup = new LeadFollowUp();
               $lead_followup->lead_id = $request->lead_id;
               $lead_followup->employee_id = JWTAuth::user()->id;
               $lead_followup->subject = $request->subject;
               $lead_followup->description = $request->description;
               $lead_followup->date = $request->date;
               $lead_followup->time = $request->time;
               $lead_followup->type = $request->type;
               $lead_followup->is_completed = $request->is_completed;
               $lead_followup->save(); 
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
               $client = Client::where('id',$lead->client_id)->value('member');
               $stores_token = Employee::where('id',$employee->id)->value('push_token');
               if($stores_token)
               {
                    $device_token = $stores_token;
                    $urls = env('Lead_employee_link');
                    $leads_id = $lead->id;
                    $title = 'Lead Updated !!';
                    $name = 'Lead';
                    $body = 'Lead Updated Name.' . $client;
                    $this->sendnotification($device_token,$title,$body,$urls,$leads_id,$name);
               }
          }
          $datas = [
               'lead' => $lead,
          ];
          $client = Client::where('id',$lead->client_id)->value('member');
          $branch = User::where('id',$lead->branch_id)->first();
          $email = $branch->email;
          $text = 'Lead Updated Name.' . $client;
          Mail::send('lead.lead_create',$datas, function($message) use($email,$text) {
               $message->to($email);
               $message->subject($text);
          });
          $client = Client::where('id',$lead->client_id)->value('member');
          $stores_token = User::where('id',$branch->id)->value('push_token');
          if($stores_token)
          {
               $device_token = $stores_token;
               $urls = env('Lead_admin_link');
               $leads_id = $lead->id;
               $title = 'Lead Updated !!';
               $name = 'Lead';
               $body = 'Lead Updated Name.' . $client;
               $this->adminsendnotification($device_token,$title,$body,$urls,$leads_id,$name);
          }
          return response()->json(['success' => 1, 'message' => 'Lead FollowUp Updated Successfully.']);
     }

     public function employeecreateleadcommunication(Request $request)
     {
          $this->validate($request, [
               'lead_id' => 'required',
          ]);
          $lead_communication = LeadCommunication::where('lead_id',$request->lead_id)->first();
          if($lead_communication)
          {
               $lead_communication->lead_id = $request->lead_id;
               $lead_communication->type = $request->type;
               $lead_communication->email = $request->email;
               $lead_communication->subject = $request->subject;
               $lead_communication->save(); 
          }
          else
          {
               $lead_communication = new LeadCommunication();
               $lead_communication->lead_id = $request->lead_id;
               $lead_communication->type = $request->type;
               $lead_communication->email = $request->email;
               $lead_communication->subject = $request->subject;
               $lead_communication->save();
          }
          return response()->json(['success' => 1, 'message' => 'Lead Communication Created Successfully']); 
     }

     public function employeeupdateleadbasic(Request $request)
     {
          $this->validate($request, [
               'id' => 'required',
               'client_id' => 'required',
               'source_id' => 'required',
          ]);
          $lead = Lead::where('id',$request->id)->first();
          $lead->branch_id = Employee::where('id',JWTAuth::user()->id)->value('branch_id');
          $lead->employee_id = JWTAuth::user()->id;
          $lead->client_id = $request->client_id;
          $params = implode(',', $request->lead_name);
          $lead->lead_name = $params;
          $lead->description = $request->description;
          $lead->source_id = $request->source_id;
          $lead->source_name = $request->source_name;
          $lead->no_of_machines = $request->no_of_machines;
          $lead->date = $request->date;
          $lead->current_project_phase = $request->current_project_phase;
          $lead->status = $request->status;
          $lead->lead_value = $request->lead_value;
          $lead->save(); 
          return response()->json(['success' => 1, 'message' => 'Lead Basic Updated Successfully']);  
     }

     public function employeegetleadproduct(Request $request)
     {
          $this->validate($request, [
               'company_id' => 'required',
          ]);
          $product = Product::where('company_id',$request->company_id)->get();
          return response()->json(['success'=>1,'data' =>$product]);
     }

     public function employeegetleadcompany(Request $request)
     {
          $this->validate($request, [
               'pro_category_id' => 'required',
          ]);
          $company = Company::where('pro_category_id',$request->pro_category_id)->get();
          return response()->json(['success'=>1,'data' =>$company]);
     }

     public function employeegetleadwarehouse(Request $request)
     {
          $this->validate($request, [
               'product_id' => 'required',
          ]);
          $warehouse = WarehouseInventory::with('warehouse')->where('product_id',$request->product_id)->get();
          return response()->json(['success'=>1,'data' =>$warehouse]);
     }

     public function employeecreatebasicchild(Request $request)
     {
          $this->validate($request, [
               'lead_id' => 'required',
               'lead_basic_main_id' => 'required',
          ]);           
          if($request->productcategory_id)
          {
               foreach ($request->productcategory_id as $key => $value) {
                    if($value != '')
                    {
                         $child = new LeadBasicChild();
                         $child->lead_id = $request->lead_id;
                         $child->lead_basic_main_id = $request->lead_basic_main_id;
                         $child->productcategory_id = $value;
                         $child->company_id = $request->company_id[$key];
                         $child->product_id = $request->product_id[$key];
                         $child->warehouse_id = $request->warehouse_id[$key];
                         $child->qty = $request->qty[$key];
                         $child->save();
                    }
               }
          }

          return response()->json(['success' => 1, 'message' => 'Lead Basic Child Created Successfully']);
     }

     public function employeeupdatebasicchild(Request $request)
     {
          $this->validate($request, [
               'id' => 'required',
               'lead_id' => 'required',
               'lead_basic_main_id' => 'required',
          ]);           
          $child = LeadBasicChild::where('id',$request->id)->first();
          $child->lead_id = $request->lead_id;
          $child->lead_basic_main_id = $request->lead_basic_main_id;
          $child->productcategory_id = $request->productcategory_id;
          $child->company_id = $request->company_id;
          $child->product_id = $request->product_id;
          $child->warehouse_id = $request->warehouse_id;
          $child->qty = $request->qty;
          $child->save();
          return response()->json(['success' => 1, 'message' => 'Lead Basic Child Updated Successfully']);
     }

     public function employeedeletebasicchild(Request $request)
     {
          $this->validate($request, [
               'id' => 'required',
          ]);
          $child =LeadBasicChild::where('id',$request->id)->delete();
          return response()->json(['success' => 1, 'message' => 'Lead Basic Child Deleted Successfully']);
     }

     public function getemployeequotation()
     {
          $quotation = Quotation::with('client','quotationchild','branch','employee','delivery')->with('quotationchild.products','quotationchild.productcategory','quotationchild.companys','quotationchild.warehouses')->where('employee_id',JWTAuth::user()->id)->get();
          return response()->json(['success'=>1,'data' => $quotation]);
     }

     public function employeegetquotationproduct(Request $request)
     {
          $this->validate($request, [
               'productcategory_id' => 'required',
          ]);
          $product = Product::where('productcategory_id',$request->productcategory_id)->get();
          return response()->json(['success'=>1,'data' => $product]);
     }

     public function employeegetquotationprice(Request $request)
     {
          $this->validate($request, [
               'product_id' => 'required',
          ]);
          $price = Product::where('id',$request->product_id)->get();
          return response()->json(['success'=>1,'data' => $price]);
     }

     public function employeecreatequotation(Request $request)
     {
          $this->validate($request, [
               'lead_id' => 'required',
               'client_id' => 'required',
               'quotation_date' => 'required',
          ]);
          $quotation = new Quotation();
          $quotation->client_id = $request->client_id;
          $quotation->branch_id = Employee::where('id',JWTAuth::user()->id)->value('branch_id');
          $quotation->employee_id = JWTAuth::user()->id;
          $quotation->lead_id = $request->lead_id; 
          $quotation->quotation_date = $request->quotation_date;
          $quotation->status = $request->status ?? 1;
          $quotation->save();
          if($request->name)
          {
               foreach($request['name'] as $key=>$value)
               {
                    if($value != '')
                    {
                         $quot_main_child['quot_id'] = $quotation->id;
                         $quot_main_child['employee_id'] = $request->employee_id[$key];
                         $quot_main_child['name'] = $value;
                         $quot_main_child_store = QuotationMainChild::create($quot_main_child);
                    }
               }
          }
          return response()->json(['success'=>1,'message' => 'Quotation Created Successfully']);
     }

     public function employeecreatequotationchild(Request $request)
     {
          $this->validate($request, [
               'quotation_id' => 'required',
               'quot_main_child_id' => 'required',
          ]);

          if($request->company_id)
          {
               foreach ($request->company_id as $key => $value) {
                    if($value != '')
                    {
                         $child = new QuotationChild();
                         $child->quotation_id = $request->quotation_id;
                         $child->quot_main_child_id = $request->quot_main_child_id;
                         $child->company_id = $value;
                         $child->productcategory_id = $request->productcategory_id[$key];
                         $child->warehouse_id = $request->warehouse_id[$key];
                         $child->product_id = $request->product_id[$key];
                         $child->gst_id = $request->gst_id[$key];
                         $product = Product::where('id',$request->product_id[$key])->first();
                         $child->price = $product->price;
                         $child->qty = $request->qty[$key];
                         $total = $product->price * $request->qty[$key];
                         $amount = $product->price;
                         $child->value_of_amount = $request->value_of_amount[$key];
                         $child->discount = $request->discount[$key];
                         if($request->discount[$key])
                         {
                              $amount1 = $amount * $request->discount[$key] /100;
                              $amount = $amount - $amount1;
                              $total1 = $total * $request->discount[$key] / 100; 
                              $total = $total - $total1;
                         }
                         $child->amount = $amount;
                         $child->total = $total;
                         $child->delivery_date = $request->delivery_date[$key];
                         if(isset($request['image'][$key]))
                         {
                              $imageName = time().'-'. $request->image[$key]->getClientOriginalName();
                              $request->image[$key]->move(public_path('quotation/'),$imageName);
                              $child->image = $imageName;
                         }
                         $child->save();
                    }



               }
          }
          $quotation = Quotation::where('id',$request->quotation_id)->first();
          $revised = new RevisedQuotation();

          $revised->branch_id = Employee::where('id',JWTAuth::user()->id)->value('branch_id');
          $revised->employee_id = JWTAuth::user()->id;

          $revised->quotation_id = $quotation->id;
          $revised->client_id = $quotation->client_id;
          $revised->revised = $request->revised;
          $revised->quotation_date = $quotation->quotation_date;
          $revised->status = $quotation->status;
          $revised->lead_id = $quotation->lead_id;
          $revised->save();
          $quotationmainchild = QuotationMainChild::where('quot_id',$quotation->id)->get();
          foreach($quotationmainchild as $qmc)
          {
               $revisedmainchild['revised_id'] = $revised->id;
               $revisedmainchild['name'] = $qmc->name;
               $revisedmainchild['employee_id'] = $qmc->employee_id;
               $store = RevisedMainChild::create($revisedmainchild);
               $quotationchild = QuotationChild::where('quot_main_child_id',$qmc->id)->get();
               foreach($quotationchild as $qc)
               {
                    $childss['revised_id'] = $revised->id;
                    $childss['revised_main_child_id'] = $store->id;
                    $childss['productcategory_id'] = $qc->productcategory_id;
                    $childss['company_id'] = $qc->company_id;
                    $childss['product_id'] = $qc->product_id;
                    $childss['warehouse_id'] = $qc->warehouse_id;
                    $childss['price'] = $qc->price;
                    $childss['qty'] = $qc->qty;
                    $childss['value_of_amount'] = $qc->value_of_amount;
                    $childss['discount'] = $qc->discount;
                    $childss['gst_id'] = $qc->gst_id;
                    $childss['amount'] = $qc->amount;
                    $childss['total'] = $qc->total;
                    $childss['delivery_date'] = $qc->delivery_date;
                    $childss_store = RevisedChild::create($childss);
               }
          }



          return response()->json(['success'=>1,'message' => 'Quotation-child created successfully.']);
     }

     public function employeeupdatequotation(Request $request)
     {
          $this->validate($request, [
               'id' => 'required',
               'quotation_date' => 'required',
          ]);
          $quotation =  Quotation::where('id',$request->id)->first();
          $quotation->quotation_date = $request->quotation_date;
          $quotation->status = $request->status;

          $quotation->save();
          $quotation = Quotation::where('id',$request->id)->first();
          $revised = new RevisedQuotation();

          $revised->branch_id = Employee::where('id',JWTAuth::user()->id)->value('branch_id');
          $revised->employee_id = JWTAuth::user()->id;

          $revised->quotation_id = $quotation->id;
          $revised->client_id = $quotation->client_id;
          $revised->revised = $request->revised;
          $revised->quotation_date = $quotation->quotation_date;
          $revised->status = $request->status;
          $revised->lead_id = $quotation->lead_id;
          $revised->save();
          $quotationmainchild = QuotationMainChild::where('quot_id',$quotation->id)->get();
          foreach($quotationmainchild as $qmc)
          {
               $revisedmainchild['revised_id'] = $revised->id;
               $revisedmainchild['name'] = $qmc->name;
               $revisedmainchild['employee_id'] = $qmc->employee_id;
               $store = RevisedMainChild::create($revisedmainchild);
               $quotationchild = QuotationChild::where('quot_main_child_id',$qmc->id)->get();
               foreach($quotationchild as $qc)
               {
                    $childss['revised_id'] = $revised->id;
                    $childss['revised_main_child_id'] = $store->id;
                    $childss['productcategory_id'] = $qc->productcategory_id;
                    $childss['company_id'] = $qc->company_id;
                    $childss['product_id'] = $qc->product_id;
                    $childss['warehouse_id'] = $qc->warehouse_id;
                    $childss['price'] = $qc->price;
                    $childss['qty'] = $qc->qty;
                    $childss['value_of_amount'] = $qc->value_of_amount;
                    $childss['discount'] = $qc->discount;
                    $childss['gst_id'] = $qc->gst_id;
                    $childss['amount'] = $qc->amount;
                    $childss['total'] = $qc->total;
                    $childss['delivery_date'] = $qc->delivery_date;
                    $childss_store = RevisedChild::create($childss);
               }
          }
          return response()->json(['success'=>1,'message' => 'Quotation Updated Successfully']);
     }
     //  public function employeeupdatequotationmainchild(Request $request)
     // {
     //     $this->validate($request, [
     //         'id' => 'required',
     //         'quot_id' => 'required',
     //     ]);
     //     $quot_main_child = QuotationMainChild::where('id',$request->id)->first();
     //     $quot_main_child->name = $request->name;
     //     $quot_main_child->save();
     //     return response()->json(['success'=>1,'message' => 'Quotation-main updated successfully.']);
     // }
     // public function employeeupdatequotationchild(Request $request)
     // {
     //      $this->validate($request, [
     //           'id' => 'required',
     //           'quotation_id' => 'required',
     //      ]);
     //      $child = QuotationChild::where('id',$request->id)->first();
     //      $child->quotation_id = $request->quotation_id;
     //      // $child->area = $request->area;
     //      $child->productcategory_id = $request->productcategory_id;
     //      $child->product_id = $request->product_id;
     //      $product = Product::where('id',$request->product_id)->first();
     //      $child->price = $product->price;
     //      $child->qty = $request->qty;
     //      $total = $product->price * $request->qty;
     //      $amount = $product->price;
     //      $child->value_of_amount = $request->value_of_amount;
     //      $child->discount = $request->discount;
     //      if($request->discount)
     //      {
     //           $amount1 = $amount * $request->discount /100;
     //           $amount = $amount - $amount1;
     //           $total1 = $total * $request->discount / 100; 
     //           $total = $total - $total1;
     //      }
     //      $child->amount = $amount;
     //      $child->total = $total;
     //      $child->delivery_date = $request->delivery_date;
     //      if(isset($request['image']))
     //      {
     //           $imageName = time().'-'. $request->image->getClientOriginalName();
     //           $request->image->move(public_path('quotation/'),$imageName);
     //           $child->image = $imageName;
     //      }
     //      $child->save();
     //      return response()->json(['success'=>1,'message' => 'Updated Quotation Child Successfully']);
     // }

     public function employeedelivery(Request $request)
     {
          $this->validate($request, [
               'quotation_id' => 'required',
          ]);
          $delivery = Delivery::where('quotation_id',$request->quotation_id)->first();
          if($delivery)
          {
               $delivery->quotation_id = $request->quotation_id;
               $delivery->date = $request->date;
               $delivery->client_id = $request->client_id;
               $delivery->note = $request->note;
               $delivery->save();
          }
          else
          {
               $delivery = new Delivery();
               $delivery->quotation_id = $request->quotation_id;
               $delivery->date = $request->date;
               $delivery->client_id = $request->client_id;
               $delivery->note = $request->note;
               $delivery->save();
          }
          return response()->json(['success'=>1,'message' => 'Delivery Created Successfully']);
     }

     public function employeedeletequotationchild(Request $request)
     {
          $this->validate($request, [
               'id' => 'required',
          ]);
          $child =QuotationChild::where('id',$request->id)->delete();
          return response()->json(['success' => 1, 'message' => 'Quotation Child Deleted Successfully']);  
     }

     public function employeegetclient()
     {

          $client = Client::where('branch_id',JWTAuth::user()->branch_id)->latest()->get();
          return response()->json(['success' => 1, 'data' => $client]);
     }

     public function employee_dashboard()
     {
          // $total_lead = Lead::where('employee_id',JWTAuth::user()->id)->count();
          // $total_lead_today = Lead::whereDate('created_at',date('Y-m-d'))->where('employee_id',JWTAuth::user()->id)->count();
          // $total_lead_month = Lead::whereMonth('created_at',date('m'))->where('employee_id',JWTAuth::user()->id)->count();
          //  $today_followup =LeadFollowUp::where('date',date('Y-m-d'))->where('employee_id',Auth::user()->id)->count();
          // $total_lead_week = Lead::select("*")
          // ->whereBetween('created_at', 
          //      [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
          // )->where('employee_id',JWTAuth::user()->id)
          // ->count();
          $data = LeadAssign::get();
          $array = [];
          foreach($data as $dt)
          {
               $ex_arr = explode(',', $dt->employee_id);
               if(in_array(JWTAuth::user()->id,$ex_arr))
               {
                    array_push($array,$dt->lead_id);
               }
          }
          $total_lead = Lead::orderBy('date','DESC')->orWhereIn('id',$array)->where('active',1)->count();
          $total_lead_today = Lead::orWhereIn('id',$array)->where('active',1)->whereDate('date', date('Y-m-d'))->count();
          $total_lead_month = Lead::orWhereIn('id',$array)->where('active',1)->whereMonth('date', date('m'))->whereYear('date', date('Y'))->count();
          $today_followup = LeadFollowUp::where('date',date('Y-m-d'))->where('employee_id',JWTAuth::user()->id)->count();
          $total_lead_week = Lead::select("*")->where('active',1)
          ->whereBetween('date', 
               [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
          )->whereIn('id',$array)
          ->count();
          return response()->json(['success'=>1,'total_lead' => $total_lead,'total_lead_today' => $total_lead_today,'total_lead_month'=>$total_lead_month,'total_lead_week'=>$total_lead_week,'today_followup'=>$today_followup]);
     }

     public function employeegettask()
     {
          $task = Task::with('employee','branch','assign_to')->where('employee_id',JWTAuth::user()->id)->orWhere('assign_to',JWTAuth::user()->id)->orderBy('id', 'DESC')->get();
          return response()->json(['success' => 1, 'data' => $task]);
     }

     public function employeetaskcreate(Request $request)
     {
          $this->validate($request, [
               'mobile' => 'required',
               'purpose' => 'required',
               'name' => 'required',
          ]);
          $task = new Task();
          $task->name = $request->name;
          $task->branch_id = Employee::where('id',JWTAuth::user()->id)->value('branch_id');
          $task->employee_id = JWTAuth::user()->id;
          $task->mobile = $request->mobile;
          $task->purpose = $request->purpose;
          $task->assign_to = $request->assign_to;
          $task->description = $request->description;
          $task->is_completed = $request->is_completed ?? 0;
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
               $leads_id = $task->id;
               $name = 'Task';
               $title = 'Task Created !!';
               $body = 'New Task Created Name.' . $task->purpose;
               $this->sendnotification($device_token,$title,$body,$urls,$leads_id,$name);
          }
          return response()->json(['success' => 1, 'message' => 'Task created successfully.']); 
     }

     public function employeetaskupdate(Request $request)
     {
          $this->validate($request, [
               'id' => 'required',
               'mobile' => 'required',
               'purpose' => 'required',
               'name' => 'required',
          ]);
          $task = Task::where('id',$request->id)->first();
          $task->name = $request->name;
          $task->branch_id = Employee::where('id',JWTAuth::user()->id)->value('branch_id');
          $task->employee_id = JWTAuth::user()->id;
          $task->mobile = $request->mobile;
          $task->purpose = $request->purpose;
          $task->assign_to = $request->assign_to;
          $task->description = $request->description;
          $task->is_completed = $request->is_completed ?? 0;
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
               $leads_id = $task->id;
               $title = 'Task Updated !!';
               $name = 'Task';
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
               $body = 'Task Updated No.' . $task->purpose;
               $this->sendnotification($device_token,$title,$body,$urls,$leads_id,$name);
          }
          return response()->json(['success' => 1, 'message' => 'Task updated successfully.']); 
     }

     public function employeetaskstatus(Request $request)
     {
          $this->validate($request, [
               'id' => 'required',
               'status' => 'required',
          ]);
          $task = Task::where('id',$request->id)->first();
          $task->status = $request->status;
          $task->save();
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
               $urls = env('Lead_employee_link');
               $leads_id = $task->id;
               $title = 'Task Updated !!';
               $name = 'Task';
               $body = 'Task Updated Name.' . $task->purpose;
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
               $leads_id = $task->id;
               $name = 'Task';
               $title = 'Task Updated !!';
               $body = 'Task Updated Name.' . $task->purpose;
               $this->sendnotification($device_token,$title,$body,$urls,$leads_id,$name);
          }
          return response()->json(['success' => 1, 'message' => 'Status change successfully.']); 
     }

     public function employeeleadcreatemainchild(Request $request)
     {
          $this->validate($request, [
               'lead_id' => 'required',
          ]);
          $lead = Lead::where('id',$request->lead_id)->first();
          foreach($request['name'] as $key=>$value)
          {
               if($value != '')
               {
                    $lead_basic_main_child['lead_id'] = $lead->id;
                    $lead_basic_main_child['name'] = $value;
                    $lead_basic_main_child['employee_id'] = $request->employee_id[$key];
                    $lead_basic_main_child_store = LeadBasicMainChild::create($lead_basic_main_child);

               }
          }
          return response()->json(['success' => 1, 'message' => 'Lead Main Child Created Successfully']);  
     }

     public function employeeleadmainchildupdate(Request $request)
     {
          $this->validate($request, [
               'id' => 'required',
               'lead_id' => 'required',
          ]);
          $main = LeadBasicMainChild::where('id',$request->id)->first();
          $main->lead_id = $request->lead_id;
          $main->name = $request->name;
          $main->employee_id = $request->employee_id;
          $main->save();
          return response()->json(['success' => 1, 'message' => 'Lead Main Child Updated Successfully']);
     }

     public function employeeleadmainchilddelte(Request $request)
     {
          $this->validate($request, [
               'id' => 'required',
          ]);  
          $main = LeadBasicMainChild::where('id',$request->id)->first();
          $child = LeadBasicChild::where('lead_basic_main_id',$main->id)->delete();
          $main->delete();
          return response()->json(['success' => 1, 'message' => 'Deleted Successfully']);
     }

     public function employeecreatedesign(Request $request)
     {
          $this->validate($request, [
               'lead_id' => 'required',
          ]);
          foreach ($request->design_date as $keyss => $valuess) {
               if($valuess != '')
               {
                    $design_child = new LeadBasicDesignChild();
                    $design_child->lead_id = $request->lead_id;
                    $design_child->date = $valuess;
                    if(isset($request['design']))
                    {
                         $imageName =  time().$keyss.'.'. $request->design[$keyss]->getClientOriginalExtension();
                         $request->design[$keyss]->move(public_path('design/'),$imageName);
                         $design_child->design = $imageName;
                    }
                    $design_child->save();
               }
          }
          return response()->json(['success' => 1, 'message' => 'Created Lead Design Successfully']);
     }

     public function employeedeletedesign(Request $request)
     {
          $this->validate($request, [
               'id' => 'required',
          ]);
          $design = LeadBasicDesignChild::where('id',$request->id)->delete();
          return response()->json(['success' => 1, 'message' => 'Lead Design Deleted Successfully']);
     }

     public function employeegetquotation()
     {
          $quotation = Quotation::with('client','branch','employee','delivery')->with('mainchild.quotationchild','mainchild.employees','mainchild.quotationchild.products','mainchild.quotationchild.productcategory','mainchild.quotationchild.companys','mainchild.quotationchild.warehouses','mainchild.quotationchild.gsts')->where('employee_id',JWTAuth::user()->id)->latest()->get();
          return response()->json(['success'=>1,'data' => $quotation]);
     }

     public function employeeupdatequotationmainchild(Request $request)
     {
          $this->validate($request, [
               'id' => 'required',
               'quot_id' => 'required',
          ]);
          $quot_main_child = QuotationMainChild::where('id',$request->id)->first();
          $quot_main_child->name = $request->name;
          $quot_main_child->is_hide = $request->is_hide;
          $quot_main_child->employee_id = $request->employee_id;
          $quot_main_child->save();
          return response()->json(['success'=>1,'message' => 'Quotation-main updated successfully.']);
     }

     public function employeeupdatequotationchild(Request $request)
     {
          $this->validate($request, [
               'id' => 'required',
               'quotation_id' => 'required',
          ]);
          $child = QuotationChild::where('id',$request->id)->first();
          $child->productcategory_id = $request->productcategory_id;
          $child->company_id = $request->company_id;
          $child->product_id = $request->product_id;
          $product = Product::where('id',$request->product_id)->first();
          $child->warehouse_id = $request->warehouse_id;
          $child->price = $product->price;
          $child->qty = $request->qty;
          $child->value_of_amount = $request->value_of_amount;
          $child->discount = $request->discount;
          $child->gst_id = $request->gst_id;
          $amount = $product->price * $request->qty;
          $gst_amount = $request->discount * $amount / 100;
          $discount = $amount - $gst_amount;
          $total_amount = $product->price - $gst_amount;
          $child->amount = $total_amount;
          $child->total = $discount;
          $child->delivery_date = $request->delivery_date;
          if(isset($request['image']))
          {
               $imageName = time().'-'. $request->image->getClientOriginalName();
               $request->image->move(public_path('quotation/'),$imageName);
               $child->image = $imageName;
          }
          $child->save();
          $quotation = Quotation::where('id',$request->quotation_id)->first();
          $revised = new RevisedQuotation();

          $revised->branch_id = Employee::where('id',JWTAuth::user()->id)->value('branch_id');
          $revised->employee_id = JWTAuth::user()->id;

          $revised->quotation_id = $quotation->id;
          $revised->client_id = $quotation->client_id;
          $revised->revised = $request->revised;
          $revised->quotation_date = $quotation->quotation_date;
          $revised->status = $quotation->status;
          $revised->lead_id = $quotation->lead_id;
          $revised->save();
          $quotationmainchild = QuotationMainChild::where('quot_id',$quotation->id)->get();
          foreach($quotationmainchild as $qmc)
          {
               $revisedmainchild['revised_id'] = $revised->id;
               $revisedmainchild['name'] = $qmc->name;
               $revisedmainchild['employee_id'] = $qmc->employee_id;
               $store = RevisedMainChild::create($revisedmainchild);
               $quotationchild = QuotationChild::where('quot_main_child_id',$qmc->id)->get();
               foreach($quotationchild as $qc)
               {
                    $childss['revised_id'] = $revised->id;
                    $childss['revised_main_child_id'] = $store->id;
                    $childss['productcategory_id'] = $qc->productcategory_id;
                    $childss['company_id'] = $qc->company_id;
                    $childss['product_id'] = $qc->product_id;
                    $childss['warehouse_id'] = $qc->warehouse_id;
                    $childss['price'] = $qc->price;
                    $childss['qty'] = $qc->qty;
                    $childss['value_of_amount'] = $qc->value_of_amount;
                    $childss['discount'] = $qc->discount;
                    $childss['gst_id'] = $qc->gst_id;
                    $childss['amount'] = $qc->amount;
                    $childss['total'] = $qc->total;
                    $childss['delivery_date'] = $qc->delivery_date;
                    $childss_store = RevisedChild::create($childss);
               }
          }
          return response()->json(['success'=>1,'message' => 'Quotation-child updated successfully.']);
     }

     public function employee_createquotationchild(Request $request)
     {
          $this->validate($request, [
               'quotation_id' => 'required',
          ]);
          $quotation = Quotation::where('id',$request->quotation_id)->first();
          foreach($request['name'] as $key=>$value)
          {
               if($value != '')
               {
                    $quot_main_child['quot_id'] = $quotation->id;
                    $quot_main_child['name'] = $value;
                    $quot_main_child['employee_id'] = $request->employee_id[$key];
                    $quot_main_child_store = QuotationMainChild::create($quot_main_child);

               }
          }
          return response()->json(['success'=>1,'message' => 'Quotation created successfully.']);
     }

     public function employee_deletequotation(Request $request)
     {
          $this->validate($request, [
               'id' => 'required',
          ]);
          $lead =Quotation::where('id',$request->id)->delete();
          $main = QuotationMainChild::where('quot_id',$request->id)->delete();
          $child = QuotationChild::where('quotation_id',$request->id)->delete();
          return response()->json(['success' => 1, 'message' => 'Quotation deleted successfully.']);
     }

     public function employee_deletequotationmain(Request $request)
     {
          $this->validate($request, [
               'id' => 'required',
          ]);
          $quotation = QuotationMainChild::where('id',$request->id)->delete();
          $child = QuotationChild::where('quot_main_child_id',$request->id)->delete();
          return response()->json(['success'=>1,'message' => 'Quotation-main & child deleted successfully.']);
     }

     public function employee_deletequotationchild(Request $request)
     {
          $this->validate($request, [
               'id' => 'required',
          ]);
          $child = QuotationChild::where('id',$request->id)->delete();
          return response()->json(['success'=>1,'message' => 'Quotation-child deleted successfully.']);
     }

     public function employee_getquotation()
     {
          $quotation = Quotation::with('client','quotationchild','branch','employee','delivery')->with('quotationchild.products','quotationchild.productcategory','quotationchild.companys','quotationchild.warehouses','quotationchild.mainchild')->where('employee_id',JWTAuth::user()->id)->latest()->get();
          return response()->json(['success'=>1,'data' => $quotation]);
     }

     public function getassign(Request $request)
     {
          $data = Employee::where('branch_id',JWTAuth::user()->branch_id)->where('status',1)->get();
          return response()->json(['success'=>1,'data' => $data]);
     }

     public function employeegetstage()
     {
          $data = LeadStage::where('branch_id',JWTAuth::user()->branch_id)->where('status',1)->get();
          return response()->json(['success'=>1,'data' => $data]);
     }

     public function employeefollowuptype()
     {
          $data = Type::where('branch_id',JWTAuth::user()->branch_id)->get();
          return response()->json(['success'=>1,'data' => $data]);
     }

     public function employee_leadsource()
     {
          $leadsource = LeadSource::where('branch_id',JWTAuth::user()->branch_id)->get();
          return response()->json(['success'=>1,'data' => $leadsource]);
     }
}