<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use JWTAuth;
use App\Employee;
use App\Supervisor;
use App\Lead;
use App\Leave;
use App\Attendance;
use App\LeadFollowUp;
use App\LeadAssign;
use App\ContactMaster;
use DB;
use Mail;
use JWTAuthException;
use App\User;
use App\Complain;
use App\Product;
use App\Quotation;
use App\ProductCategory;
use Carbon\Carbon;


class BranchController extends Controller
{

	function sendnotification($pushtoken,$title,$body,$urls,$leads_id,$name)
	{
		$firebaseToken = [$pushtoken];
          // dd($firebaseToken);
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
          // dd($headers);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

		$response = curl_exec($ch);
          // dd($response);
	}

	function adminsendnotification($pushtoken,$title,$body,$urls,$leads_id,$name)
	{
		$firebaseToken = [$pushtoken];
          // dd($firebaseToken);
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
          // dd($headers);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

		$response = curl_exec($ch);
          // dd($response);
	}

	

	public function createbranch(Request $request)
	{
		$this->validate($request, [
			'first_name' => 'required',
			'last_name' => 'required',
			'name' => 'required',
			'email' => 'required',
			'number' => 'required',
			'password' => 'required',
		]);

		$user = new User();
		$user->group_id = 1;
		$user->first_name = $request->first_name;
		$user->last_name = $request->last_name;
		$user->name = $request->name;
		$user->email = $request->email;
		$user->number = $request->number;
		$user->role = 2;
		if(isset($request['image']))
		{
			$imageName = time().'-'. $request->image->getClientOriginalName();
			$request->image->move(public_path('user/'),$imageName);
			$param['image'] = $imageName;
		}
		$user->show_password = $request->password;
		$user->password = bcrypt($request->password);
		$user->save();

		return response()->json(['success'=>1,'message' => 'Created Branch Successfully.']);
	}

	public function updatebranch(Request $request)
	{
		$this->validate($request, [
			'id' => 'required',
			'first_name' => 'required',
			'last_name' => 'required',
			
			'email' => 'required',
			'number' => 'required',
			
		]);

		$user = User::where('id',$request->id)->first();
		$user->group_id = 1;
		$user->first_name = $request->first_name;
		$user->last_name = $request->last_name;
		// $user->name = $request->name;
		$user->email = $request->email;
		$user->number = $request->number;
		// $user->role = 2;

		if(isset($request['image']))
		{
			$imageName = time().'.'. $request->image->getClientOriginalExtension();
			$request->image->move(public_path('user/'),$imageName);
			$user->image = $imageName;
		}

        // $user->show_password = $request->password;
        // $user->password = bcrypt($request->password);
		$user->save();

		return response()->json(['success'=>1,'message' => 'updated Branch Successfully.']);
	}

	public function deletebranch(Request $request)
	{
		$this->validate($request, [
			'id' => 'required',
			
		]);

		$user = User::where('id',$request->id)->delete();

		return response()->json(['success'=>1,'message' => 'Delete Branch Successfully.']);

	}

	public function dashboard()
	{
		if(JWTAuth::user()->role == 2)
		{

			$total_lead = Lead::where('branch_id',JWTAuth::user()->id)->count();
			$total_lead_today = Lead::whereDate('created_at',date('Y-m-d'))->where('branch_id',JWTAuth::user()->id)->count();
			$total_lead_month = Lead::whereMonth('created_at',date('m'))->where('branch_id',JWTAuth::user()->id)->count();
			$today_followup = LeadFollowUp::leftJoin('lead as ld','ld.id','lead_followup.lead_id')->where('ld.branch_id',JWTAuth::user()->id)->select('lead_followup.*')->where(['lead_followup.date'=>date('Y-m-d')])->count();
			$total_lead_week = Lead::select("*")
			->whereBetween('created_at', 
				[Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
			)->where('branch_id',JWTAuth::user()->id)
			->count();


		}
		else
		{
			$total_lead = Lead::count();
			$total_lead_today = Lead::whereDate('created_at', date('Y-m-d'))->count();
			$total_lead_month = Lead::whereMonth('created_at', date('m'))->count();
			$today_followup =LeadFollowUp::where('date',date('Y-m-d'))->count();
			$total_lead_week = Lead::select("*")
			->whereBetween('created_at', 
				[Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
			)
			->count();


		}

		return response()->json(['success'=>1,'total_lead' => $total_lead,'total_lead_today' => $total_lead_today,'total_lead_month'=>$total_lead_month, 'total_lead_week'=>$total_lead_week,'today_followup'=>$today_followup]);
	}


	public function getattendance()
	{
		$attendance = Attendance::with('employee','branch')->latest()->get();
		if(JWTAuth::user()->role == 2)
		{
			$attendance = Attendance::with('employee','branch')->where('branch_id',JWTAuth::user()->id)->latest()->get();
		}

		return response()->json(['success'=>1,'data' => $attendance]);
	}

	public function attendancestatus(Request $request)
	{
		$this->validate($request, [
			'id' => 'required',
			'attendance' => 'required',
			
		]);
		$attendance = Attendance::where('id',$request->id)->first();
		// dd($attendance);
		$attendance->attendance = $request->attendance;
		$attendance->save();

		return response()->json(['success'=>1,'message' => 'Status Change Successfully']);
	}

	public function getleave()
	{
		$leave = Leave::with('branch','employee')->orderBy('id', 'DESC')->get();
		if(JWTAuth::user()->role == 2)
		{
			$leave = Leave::with('branch','employee')->where('branch_id',JWTAuth::user()->id)->orderBy('id', 'DESC')->get();
		}

		return response()->json(['success'=>1,'data' => $leave]);
	}

	public function createleave(Request $request)
	{
		

		$leave = new Leave();

		if(JWTAuth::user()->role == 1)
		{
			$leave->branch_id = $request->branch_id;
			$leave->employee_id = $request->employee_id;
		}
		else if(JWTAuth::user()->role == 2)
		{
			$leave->branch_id = JWTAuth::user()->id;
			$leave->employee_id = $request->employee_id;
		}


		

		$leave->from_date = $request->from_date;
		$leave->to_date = $request->to_date;
		$leave->subject = $request->subject;
		$leave->reason = $request->reason;
		$leave->app_link = $request->app_link;
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
			$title = 'Leave Created !!';
			$urls = env('Leave_admin_link');
			$leads_id = $leave->id;
			$name = 'Leave';
			$body = 'Leave Request for ' .$employee->name;

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
			$title = 'Leave Created !!';
			$urls = env('Leave_admin_link');
			$leads_id = $leave->id;
			$name = 'Leave';
			$body = 'Leave Request for ' .$employee->name;

			$this->adminsendnotification($device_token,$title,$body,$urls,$leads_id,$name);
		}

		return response()->json(['success'=>1,'message' => 'Leave Created Successfully']);
	}

	

	public function statusleave(Request $request)
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
			$leave->save();
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
    // dd($leave);
			$datas = [
				'leave' => $leave,
			];


			$employee = Employee::where('id',$leave->employee_id)->first();
			$email = $employee->email;
  // dd($email);

			$text = 'Leave Approved for ' .$employee->name;
			Mail::send('leave.leave_approved',$datas, function($message) use($email,$text) {
				$message->to($email);
				$message->subject($text);
			});

			$stores_token = Employee::where('id',$employee->id)->value('push_token');
			if($stores_token)
			{
				$device_token = $stores_token;
				$title = 'Leave Created !!';
				$urls = env('Leave_employee_link');
				$leads_id = $leave->id;
				$name = 'Leave';
				$body = 'Leave Approved for ' .$employee->name;

				$this->sendnotification($device_token,$title,$body,$urls,$leads_id,$name);
			}
		}
		else if($request->status == 2)
		{
			$leave = Leave::where('id',$request->id)->first();
    // dd($leave);
			$datas = [
				'leave' => $leave,
			];


			$employee = Employee::where('id',$leave->employee_id)->first();
			$email = $employee->email;
  // dd($email);

			$text = 'Leave Reject for ' .$employee->name;
			Mail::send('leave.leave_reject',$datas, function($message) use($email,$text) {
				$message->to($email);
				$message->subject($text);
			});

			$stores_token = Employee::where('id',$employee->id)->value('push_token');
			if($stores_token)
			{
				$device_token = $stores_token;
				$title = 'Leave Rejected !!';
				$urls = env('Leave_employee_link');
				$leads_id = $leave->id;
				$name = 'Leave';

				$body = 'Leave Reject for ' .$employee->name;

				$this->sendnotification($device_token,$title,$body,$urls,$leads_id,$name);
			}
		}
		
		$leave->status = $request->status;
		// dd($leave);
		$leave->save();

		return response()->json(['success'=>1,'message' => 'Status Change Successfully']);

	}

	public function leadfilter(Request $request)
	{
		$query = Lead::orderBy('date','DESC')->with('branch','employee','client','lead_assign','source','sourcename','mainchild','mainchild.leadbasicchild','mainchild.leadbasicchild.productcategory','mainchild.leadbasicchild.company','mainchild.leadbasicchild.product','mainchild.leadbasicchild.warehouse','lead_assign.stage','mainchild.employees','basicdesign','lead_followup','lead_followup.employee','lead_followup.types')->latest();
  
		if(JWTAuth::user()->role == 2)
		{
			$query->orderBy('date','DESC')->with('branch','employee','client','lead_assign','source','sourcename','mainchild','mainchild.leadbasicchild','mainchild.leadbasicchild.productcategory','mainchild.leadbasicchild.company','mainchild.leadbasicchild.product','mainchild.leadbasicchild.warehouse','lead_assign.stage','mainchild.employees','basicdesign','lead_followup','lead_followup.employee','lead_followup.types')->where('branch_id',JWTAuth::user()->id);
		}

		if($request->employee_id)
		{
			$data = Lead::with('branch','employee','client','lead_assign','source','sourcename','mainchild','mainchild.leadbasicchild','mainchild.leadbasicchild.productcategory','mainchild.leadbasicchild.company','mainchild.leadbasicchild.product','mainchild.leadbasicchild.warehouse','lead_assign.stage','mainchild.employees','basicdesign','lead_followup','lead_followup.employee','lead_followup.types')->orderBy('date','DESC')->latest();
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
		if($request->from_date)
		{
			$query->where('date','>=',$request->from_date);
		}
		if($request->to_date)
		{
			$query->where('date','<=',$request->to_date);
		}
		$query = $query->latest()->get();

		$array = array();
		foreach ($query as $key => $value) {
			$param = explode(',', $value->lead_name);
			$value->lead_names_array = ProductCategory::whereIn('id',$param)->get();
			$employees = explode(',', $value->lead_assign->employee_id);
			$value->lead_assign_employee = Employee::whereIn('id',$employees)->get();

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
		return response()->json(['success'=>1,'data' => $query]);
	}

	

}