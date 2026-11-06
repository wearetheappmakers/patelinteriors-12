<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Lead;
use App\Employee;
use App\IVFollowup;
use App\LeadAssign;
use App\LeadFollowUp;
use App\LeadSource;
use App\WarehouseInventory;
use App\Quotation;
use App\ItemLedger;
use Carbon\Carbon;
use DB;
use Auth;
use DataTables;

class HomeController extends Controller
{
	public function index(Request $request)
	{   
		if(Auth::guard('employee')->check())
		{
			$data['today_followup'] = LeadFollowUp::where('date',date('Y-m-d'))->where('employee_id',Auth::user()->id)->count();
			$data['appointment_date'] = LeadFollowUp::groupBy('date')->where('employee_id',Auth::user()->id)->get('date');
			$data['title'] = '';
			$data['event1'] = [];
			foreach($data['appointment_date'] as $date)
			{
				$data['appointment_event'] = LeadFollowUp::where('date',$date['date'])->where('employee_id',Auth::user()->id)->get();
				foreach ($data['appointment_event'] as $app) 
				{
					if($app['date'] == $date['date'])
					{
						$title = '';
						$data['event1'][date('Y-m-d',strtotime($date['date']))]=[ 
							'Lead' => LeadFollowUp::leftJoin('lead as ld','ld.id','lead_followup.lead_id')->with('lead','lead.client')->where('lead_followup.employee_id',Auth::user()->id)->where('ld.active',1)->where(['lead_followup.date'=>$date['date']])->get(),
						];
						
						$data['title'] = $title;
					}
				}
			}

			$data['iv_followup_date'] = IVFollowup::groupBy('status_date')->where('employee_id',Auth::user()->id)->get('status_date');
			$data['title1'] = '';
			$data['event2'] = [];
			foreach($data['iv_followup_date'] as $date)
			{
				$data['iv_event'] = IVFollowup::where('status_date',$date['status_date'])->where('employee_id',Auth::user()->id)->get();
				foreach ($data['iv_event'] as $app) 
				{
					if($app['status_date'] == $date['status_date'])
					{
						$title = '';
						$data['event2'][date('Y-m-d',strtotime($date['status_date']))]=[ 
							'IV' => IVFollowup::with('employee')->where('employee_id',Auth::user()->id)->where(['status_date'=>$date['status_date']])->get(),
						];
						
						$data['title1'] = $title;
					}
				}
			}

			$array = LeadAssign::leftJoin('lead as ll','ll.id','lead_assign.lead_id')
			->whereRaw('FIND_IN_SET(?, lead_assign.employee_id)', [Auth::user()->id])
			->where('ll.active',1)->pluck('lead_id')->toArray();

			$startOfWeek = Carbon::now()->startOfWeek();
			$endOfWeek = Carbon::now()->endOfWeek();

			$startDate = $startOfWeek->format('Y-m-d');
			$endDate = $endOfWeek->format('Y-m-d');

			$data['weekly'] = Lead::select("*")->where('active',1)
			->whereBetween('date', [$startDate, $endDate])
			->whereIn('id',$array)
			->count();;

			$data['total_quotation_weekly'] = DB::table('quotation')
			->whereBetween('quotation_date', [$startDate, $endDate])
			->where('employee_id',Auth::user()->id)
			->count();

			$data['total_iv_weekly'] = DB::table('interior_visit')
			->whereBetween('date', [$startDate, $endDate])
			->where('employee_id',Auth::user()->id)
			->count();

			$data['total_service_weekly'] = DB::table('service')
			->whereBetween('register_date', [$startDate, $endDate])
			->where('employee_id',Auth::user()->id)
			->count();

			$data['total_attendance_weekly'] = DB::table('attendance')
			->whereBetween('date', [$startDate, $endDate])
			->where('employee_id',Auth::user()->id)
			->count();

			return view('employee.home')->with($data);
		}
		else
		{
			if(Auth::user()->role == 2)
			{
				$startOfWeek = Carbon::now()->startOfWeek();
				$endOfWeek = Carbon::now()->endOfWeek();

				$startDate = $startOfWeek->format('Y-m-d');
				$endDate = $endOfWeek->format('Y-m-d');
				
				$data['weekly'] = Lead::select("*")
				->whereBetween('date', [$startDate, $endDate])
				->where('branch_id',Auth::user()->id)
				->where('active',1)
				->count();

				$data['total_quotation_weekly'] = DB::table('quotation')
				->whereBetween('quotation_date', [$startDate, $endDate])
				->where('branch_id',Auth::user()->id)
				->count();

				$data['total_service_weekly'] = DB::table('service')
				->whereBetween('register_date', [$startDate, $endDate])
				->where('branch_id',Auth::user()->id)
				->count();

				$data['total_iv_weekly'] = DB::table('interior_visit')
				->whereBetween('date', [$startDate, $endDate])
				->where('branch_id',Auth::user()->id)
				->count();

				$data['total_attendance_weekly'] = DB::table('attendance')
				->whereBetween('date', [$startDate, $endDate])
				->where('branch_id',Auth::user()->id)
				->count();

				$data['today_followup'] = LeadFollowUp::leftJoin('lead as ld','ld.id','lead_followup.lead_id')->where('ld.active',1)->where('ld.branch_id',Auth::user()->id)->select('lead_followup.*')->where(['lead_followup.date'=>date('Y-m-d')])->count();
				$data['appointment_date'] = LeadFollowUp::groupBy('date')->get('date');
				
				$data['title'] = '';
				$data['event1'] = [];
				foreach($data['appointment_date'] as $date)
				{
					$title = '';
					$data['event1'][date('Y-m-d',strtotime($date['date']))]=[ 
						'Lead' => LeadFollowUp::leftJoin('lead as ld','ld.id','lead_followup.lead_id')->where('ld.active',1)->where('ld.branch_id',Auth::user()->id)->select('lead_followup.*')->with('lead','lead.client')->where(['lead_followup.date'=>$date['date']])->get(),
					];
					$data['title'] = $title;
				}


				$data['iv_followup_date'] = IVFollowup::groupBy('status_date')->get('status_date');
				
				$data['title1'] = '';
				$data['event2'] = [];
				foreach($data['iv_followup_date'] as $date)
				{
					$title = '';
					$data['event2'][date('Y-m-d',strtotime($date['status_date']))]=[ 
						'IV' => IVFollowup::leftJoin('interior_visit as iv','iv.id','iv_followup.iv_id')->where('iv.branch_id',Auth::user()->id)->select('iv_followup.*')->with('employee')->where(['status_date'=>$date['status_date']])->get(),
					];
					$data['title1'] = $title;
				}
				
			}
			else
			{
				$startOfWeek = Carbon::now()->startOfWeek();
				$endOfWeek = Carbon::now()->endOfWeek();

				$startDate = $startOfWeek->format('Y-m-d');
				$endDate = $endOfWeek->format('Y-m-d');

				$data['weekly'] = Lead::select("*")
				->whereBetween('date', [$startDate, $endDate])
				->count();

				$data['total_quotation_weekly'] = DB::table('quotation')
				->whereBetween('quotation_date', [$startDate, $endDate])
				->count();

				$data['total_service_weekly'] = DB::table('service')
				->whereBetween('register_date', [$startDate, $endDate])
				->count();

				$data['total_iv_weekly'] = DB::table('interior_visit')
				->whereBetween('date', [$startDate, $endDate])
				->count();

				$data['total_attendance_weekly'] = DB::table('attendance')
				->whereBetween('date', [$startDate, $endDate])
				->count();

				$data['today_followup'] = LeadFollowUp::where('date',date('Y-m-d'))->count();

				if($request->employee)
				{

					$data['appointment_date'] = LeadFollowUp::where('employee_id',$request->employee)->whereMonth('date',date('m'))->whereYear('date',date('Y'))->where('date','!=',null)->groupBy('date')->get('date');
					
					$data['title'] = '';
					$data['event1'] = [];
					foreach($data['appointment_date'] as $date)
					{
						if (isset($date['date'])) {
							$data['appointment_event'] = LeadFollowUp::where('employee_id',$request->employee)->whereMonth('date',date('m'))->whereYear('date',date('Y'))->where('date','!=',null)->where('date',$date['date'])->get();
							foreach ($data['appointment_event'] as $app) 
							{
								if($app['date'] == $date['date'])
								{
									$title = '';
									$data['event1'][date('Y-m-d',strtotime($date['date']))]=[ 
										'Lead' => LeadFollowUp::with('lead','lead.client')->leftJoin('lead as ld','ld.id','lead_followup.lead_id')->where('lead_followup.employee_id',$request->employee)->where('ld.active',1)->select('lead_followup.*')->with('lead','lead.client')->where(['lead_followup.date'=>$date['date']])->whereMonth('lead_followup.date',date('m'))->whereYear('lead_followup.date',date('Y'))->get()->toArray(),
									];
									$data['title'] = $title;
								}
							}
						}
					}

					$data['iv_followup_date'] = IVFollowup::where('employee_id',$request->employee)->where('status_date',date('Y-m-d'))->groupBy('status_date')->get('status_date');
					
					$data['title1'] = '';
					$data['event2'] = [];
					foreach($data['iv_followup_date'] as $date)
					{
						if (isset($date['status_date'])) {
							$data['iv_event'] = IVFollowup::where('employee_id',$request->employee)->where('status_date',$date['status_date'])->get();
							foreach ($data['iv_event'] as $app) 
							{
								if($app['status_date'] == $date['status_date'])
								{
									$title = '';
									$data['event2'][date('Y-m-d',strtotime($date['status_date']))]=[ 
										'IV' => IVFollowup::with('employee')->where('employee_id',$request->employee)->where('status_date',$date['status_date'])->get()->toArray(),
									];
									$data['title1'] = $title;
								}
							}
						}
					}
				}
				else
				{
					$data['appointment_date'] = LeadFollowUp::groupBy('date')->whereMonth('date',date('m'))->whereYear('date',date('Y'))->where('date','!=',null)->get('date');
					
					$data['title'] = '';
					$data['event1'] = [];
					foreach($data['appointment_date'] as $date)
					{
						$data['appointment_event'] = LeadFollowUp::where('date',$date['date'])->whereMonth('date',date('m'))->whereYear('date',date('Y'))->where('date','!=',null)->get();
						foreach ($data['appointment_event'] as $app) 
						{
							if($app['date'] == $date['date'])
							{
								$title = '';
								$data['event1'][date('Y-m-d',strtotime($date['date']))]=[ 
									'Lead' => LeadFollowUp::with('lead','lead.client')->leftJoin('lead as ld','ld.id','lead_followup.lead_id')->where('ld.active',1)->select('lead_followup.*')->with('lead','lead.client')->where(['lead_followup.date'=>$date['date']])->whereMonth('lead_followup.date',date('m'))->whereYear('lead_followup.date',date('Y'))->get()->toArray(),
								];
								$data['title'] = $title;
							}
						}
					}


					$data['iv_followup_date'] = IVFollowup::groupBy('status_date')->where('status_date',date('Y-m-d'))->get('status_date');
					
					$data['title1'] = '';
					$data['event2'] = [];
					foreach($data['iv_followup_date'] as $date)
					{
						$data['iv_event'] = IVFollowup::where('status_date',$date['status_date'])->get();
						foreach ($data['iv_event'] as $app) 
						{

							if($app['status_date'] == $date['status_date'])
							{
								$title = '';
								$data['event2'][date('Y-m-d',strtotime($date['status_date']))]=[ 
									'IV' => IVFollowup::with('employee')->where('status_date',$date['status_date'])->get()->toArray(),
								];
								$data['title1'] = $title;
							}
						}
					}
				}
			}
			$data['dt_event'] = [];
			foreach ($data['event1'] as $key => $value) 
			{
				if($value['Lead'])
				{

					foreach ($value['Lead'] as $keys => $values) 
					{
						if(isset($values['lead']['client']['member']))
						{
							$title =$values['lead']['client']['member'];
						}else
						{
							$title ='';
						}
						if(Auth::user()->role == 2)
						{
							$url='';

						}else{

							$url ='';
						}
						$test = [
							'title'=> $title,
							'start'=> $key,
							'end'=> $key,
							'icon'=> "clock",
							'url'=>$url,
							'className'=> 'eventClass'
						];
						array_push($data['dt_event'], $test);
					}
				}
			}

			if(count($data['event2']) > 0)
			{

				foreach ($data['event2'] as $key => $value) {

					foreach ($value['IV'] as $keys => $values) {

						if(isset($values['employee']['name']))
						{
							$title ='IV-'.$values['employee']['name'];
						}else
						{
							$title ='';
						}
						if(Auth::user()->role == 2)
						{
							$url='';

						}else{

							$url ='';
						}

						$test1 = [
							'title'=> $title,
							'start'=> $key,
							'end'=> $key,
							'icon'=> "clock",
							'url'=>$url,
							'className'=> 'eventClass1'
						];
						array_push($data['dt_event'], $test1);
					}
				}
			}

		
			return view('user.home')->with($data);
		}
	}
	public function indexs(Request $request)
	{   

		if(Auth::guard('employee')->check())
		{
			$data['today_followup'] =LeadFollowUp::where('date',date('Y-m-d'))->where('employee_id',Auth::user()->id)->count();
			$data['appointment_date'] = LeadFollowUp::groupBy('date')->where('employee_id',Auth::user()->id)->get('date');
			$data['title'] = '';
			$data['event1'] = [];
			foreach($data['appointment_date'] as $date)
			{
				$data['appointment_event'] = LeadFollowUp::where('date',$date['date'])->where('employee_id',Auth::user()->id)->get();
				foreach ($data['appointment_event'] as $app) 
				{
					if($app['date'] == $date['date'])
					{
						$title = '';
						$data['event1'][date('Y-m-d',strtotime($date['date']))]=[ 
							'Lead' => LeadFollowUp::leftJoin('lead as ld','ld.id','lead_followup.lead_id')->with('lead','lead.client')->where('lead_followup.employee_id',Auth::user()->id)->where('ld.active',1)->where(['lead_followup.date'=>$date['date']])->get(),
						];
						$data['title'] = $title;
					}
				}
			}
			$lead_assign = LeadAssign::get();
			$array = [];
			foreach($lead_assign as $dt)
			{
				$ex_arr = explode(',', $dt->employee_id);
				if(in_array(Auth::user()->id,$ex_arr))
				{
					array_push($array,$dt->lead_id);
				}
			}
			$data['weekly'] = Lead::select("*")->where('active',1)
			->whereBetween('date', 
				[Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
			)->whereIn('id',$array)
			->count();
			return view('employee.home')->with($data);
		}
		else
		{
			if(Auth::user()->role == 2)
			{
				$data['weekly'] = Lead::select("*")
				->whereBetween('date', 
					[Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
				)->where('branch_id',Auth::user()->id)->where('active',1)
				->count();
				$data['today_followup'] =LeadFollowUp::leftJoin('lead as ld','ld.id','lead_followup.lead_id')->where('ld.active',1)->where('ld.branch_id',Auth::user()->id)->select('lead_followup.*')->where(['lead_followup.date'=>date('Y-m-d')])->count();
				$data['appointment_date'] = LeadFollowUp::groupBy('date')->get('date');
				$data['title'] = '';
				$data['event1'] = [];
				foreach($data['appointment_date'] as $date)
				{
					$title = '';
					$data['event1'][date('Y-m-d',strtotime($date['date']))]=[ 
						'Lead' => LeadFollowUp::leftJoin('lead as ld','ld.id','lead_followup.lead_id')->where('ld.active',1)->where('ld.branch_id',Auth::user()->id)->select('lead_followup.*')->with('lead','lead.client')->where(['lead_followup.date'=>$date['date']])->get(),
					];
					$data['title'] = $title;
				}
			}
			else
			{
				$data['weekly'] = Lead::select("*")
				->whereBetween('date', 
					[Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
				)
				->count();
				$data['today_followup'] =LeadFollowUp::where('date',date('Y-m-d'))->count();
				if($request->employee)
				{
					$data['appointment_date'] = LeadFollowUp::where('employee_id',$request->employee)->whereMonth('date',date('m'))->whereYear('date',date('Y'))->where('date','!=',null)->groupBy('date')->get('date');
					$data['title'] = '';
					$data['event1'] = [];
					foreach($data['appointment_date'] as $date)
					{
						$data['appointment_event'] = LeadFollowUp::where('employee_id',$request->employee)->whereMonth('date',date('m'))->whereYear('date',date('Y'))->where('date','!=',null)->where('date',$date['date'])->get();
						foreach ($data['appointment_event'] as $app) 
						{
							if($app['date'] == $date['date'])
							{
								$title = '';
								$data['event1'][date('Y-m-d',strtotime($date['date']))]=[ 
									'Lead' => LeadFollowUp::with('lead','lead.client')->leftJoin('lead as ld','ld.id','lead_followup.lead_id')->where('lead_followup.employee_id',$request->employee)->where('ld.active',1)->select('lead_followup.*')->with('lead','lead.client')->where(['lead_followup.date'=>$date['date']])->whereMonth('lead_followup.date',date('m'))->whereYear('lead_followup.date',date('Y'))->get()->toArray(),
								];
								$data['title'] = $title;
							}
						}
					}
				}else
				{
					$data['appointment_date'] = LeadFollowUp::groupBy('date')->whereMonth('date',date('m'))->whereYear('date',date('Y'))->where('date','!=',null)->get('date');
					$data['title'] = '';
					$data['event1'] = [];
					foreach($data['appointment_date'] as $date)
					{
						$data['appointment_event'] = LeadFollowUp::where('date',$date['date'])->whereMonth('date',date('m'))->whereYear('date',date('Y'))->where('date','!=',null)->get();
						foreach ($data['appointment_event'] as $app) 
						{
							if($app['date'] == $date['date'])
							{
								$title = '';
								$data['event1'][date('Y-m-d',strtotime($date['date']))]=[ 
									'Lead' => LeadFollowUp::with('lead','lead.client')->leftJoin('lead as ld','ld.id','lead_followup.lead_id')->where('ld.active',1)->select('lead_followup.*')->with('lead','lead.client')->where(['lead_followup.date'=>$date['date']])->whereMonth('lead_followup.date',date('m'))->whereYear('lead_followup.date',date('Y'))->get()->toArray(),
								];
								$data['title'] = $title;
							}
						}
					}
				}
			}
			$data['dt_event'] = [];
			foreach ($data['event1'] as $key => $value) {
				foreach ($value['Lead'] as $keys => $values) {
					if(isset($values['lead']['client']['member']))
					{
						$title =$values['lead']['client']['member'];
					}else
					{
						$title ='';
					}
					$url = '';
					// $url =  route("user.lead.edit", $values['lead']['id']);
					$test = [
						'title'=> $title,
						'start'=> $key,
						'end'=> $key,
						'icon'=> "clock",
						'url'=>$url,
						'className'=> 'eventClass'
					];
					array_push($data['dt_event'], $test);
				}
			}
			return view('user.testhome')->with($data);
		}
	}
	public function changeMultipleStatus(Request $request)
	{
		$table_name = $request->get('table_name');
		$param = $request->get('param');
		$id_array = explode(',', $request->get('id'));
		try {
			if ($param == 0) {
				foreach ($id_array as $id) {
					DB::table($table_name)->where('id', $id)
					->update([
						$request->field => 0,
					]);
				}
			} elseif ($param == 1) {
				foreach ($id_array as $id) {
					DB::table($table_name)->where('id', $id)
					->update([
						$request->field => 1,
					]);
				}
			}
			if($table_name == 'settings') {
				$all_setting = Settings::where('status', 1)->pluck('value','name')->toArray();
				file_put_contents(base_path() .'/config/common.php',  '<?php return ' . var_export($all_setting, true) . ';');
			}
			$res['status'] = 'Success';
			$res['message'] = 'Status Change successfully';
		} catch (\Exception $ex) {
			$res['status'] = 'Error';
			$res['message'] = 'Something went wrong.';
		}
		return response()->json($res);
	}
	public function deleteMultiple(Request $request)
	{
		$table_name = $request->get('table_name');
		$id_array = explode(',', $request->get('id'));
		try {
			if($table_name === 'product')
			{
				$deleted_main = DB::table($table_name)->whereIn('id', $id_array)->get();
				if($deleted_main)
				{
					$delete_child = DB::table('warehouse_inventories')->whereIn('product_id',$id_array)->delete();
					DB::table($table_name)->whereIn('id', $id_array)->delete();

					$res['status'] = 'Success';
					$res['message'] = 'Deleted successfully';
				}
			}
			elseif($table_name == 'lead'){
				DB::table('lead')->whereIn('id',$id_array)->update(['deleted_at'=>now()]);
				DB::table('lead_assign')->whereIn('lead_id',$id_array)->delete();
				DB::table('lead_basic_child')->whereIn('lead_id',$id_array)->delete();
				DB::table('lead_basic_design_child')->whereIn('lead_id',$id_array)->delete();
				DB::table('lead_basic_main_child')->whereIn('lead_id',$id_array)->delete();
				DB::table('lead_communication')->whereIn('lead_id',$id_array)->delete();
				DB::table('lead_followup')->whereIn('lead_id',$id_array)->delete();

				$res['status'] = 'Success';
				$res['message'] = 'Deleted successfully';
			}
			else if($table_name === 'warehouses')
			{
				$deleted_main = DB::table($table_name)->whereIn('id', $id_array)->get();
				if($deleted_main)
				{
					$delete_child = DB::table('warehouse_inventories')->whereIn('warehouse_id',$id_array)->delete();
					DB::table($table_name)->whereIn('id', $id_array)->delete();

					$res['status'] = 'Success';
					$res['message'] = 'Deleted successfully';
				}
			}
			else if($table_name === 'purchase_order')
			{
				DB::table('purchase_order_child')->whereIn('purchase_order_id', $id_array)->delete();
				DB::table($table_name)->whereIn('id', $id_array)->delete();

				$res['status'] = 'Success';
				$res['message'] = 'Deleted successfully';
			}
			else if($table_name === 'purchase_invoice')
			{
				$deleted_main = DB::table($table_name)->whereIn('id', $id_array)->first();
				if(isset($deleted_main))
				{
					$deletechild = DB::table('purchase_invoice_child')->whereIn('invoice_id',$id_array)->get();
					if(count($deletechild) > 0){
						foreach($deletechild as $value){
							$inventory = WarehouseInventory::where('branch_id',$deleted_main->branch_id)->where('product_id',$value->product_id)->where('warehouse_id',$value->warehouse_id)->first();
							if(isset($inventory)){
								$inventory->stock = $inventory->stock - $value->qty;
								$inventory->save();
							}

							ItemLedger::where('purchaseinvoice_child_id',$value->id)->delete();
						}
					}
					DB::table('purchase_invoice_child')->whereIn('invoice_id',$id_array)->delete();
					DB::table($table_name)->whereIn('id', $id_array)->delete();

					$res['status'] = 'Success';
					$res['message'] = 'Deleted successfully';
				}
			}
			else if($table_name === 'salesorder')
			{
				$salesorder = DB::table($table_name)->whereIn('id', $id_array)->first();
				
				if($salesorder->status == 0)
				{
					DB::table('salesorder_child')->whereIn('salesorder_id', $id_array)->delete();
					DB::table($table_name)->whereIn('id', $id_array)->delete();

					$res['status'] = 'Success';
					$res['message'] = 'Deleted successfully';
				}
				else
				{
					$res['status'] = 'Error';
					$res['message'] = 'Status Not Pending !!!';
				}
			}
			else if($table_name === 'interior_visit')
			{
				DB::table('interior_visit_child')->whereIn('interior_visit_id', $id_array)->delete();
				DB::table($table_name)->whereIn('id', $id_array)->delete();

				$res['status'] = 'Success';
				$res['message'] = 'Deleted successfully';
			}
			else if($table_name === 'service')
			{
				DB::table('service_child')->whereIn('service_id', $id_array)->delete();
				DB::table('service_image')->whereIn('service_id', $id_array)->delete();
				DB::table($table_name)->whereIn('id', $id_array)->delete();

				$res['status'] = 'Success';
				$res['message'] = 'Deleted successfully';
			}
			else if($table_name === 'quotation')
			{
				$rev = DB::table('revised_quotation')->whereIn('quotation_id', $id_array)->first();
				if(isset($rev))
				{
					DB::table('revised_main_child')->where('revised_id',$rev->id)->delete();
					DB::table('revised_child')->where('revised_id',$rev->id)->delete();
				}
				DB::table('quotation_child')->whereIn('quotation_id', $id_array)->delete();
				DB::table('quotation_main_child')->whereIn('quot_id', $id_array)->delete();
				DB::table($table_name)->whereIn('id', $id_array)->delete();

				$res['status'] = 'Success';
				$res['message'] = 'Deleted successfully';
			}
			else if($table_name === 'credit')
			{
				DB::table('credit_child')->whereIn('credit_id', $id_array)->delete();
				DB::table($table_name)->whereIn('id', $id_array)->delete();

				$res['status'] = 'Success';
				$res['message'] = 'Deleted successfully';
			}
			else
			{
				DB::table($table_name)->whereIn('id', $id_array)->delete();

				$res['status'] = 'Success';
				$res['message'] = 'Deleted successfully';
			}
			// $res['status'] = 'Success';
			// $res['message'] = 'Deleted successfully';
		} catch (\Exception $ex) {
			$res['status'] = 'Error';
			$res['message'] = $ex->getMessage();
		}
        return response()->json($res); //return response()->json(['status' => 'success', 'message' => 'Deleted']);
    }
    public function event(Request $request)
    {
        // dd('sds');
    	$data['date'] = date('d-m-Y',strtotime($request->date));
    	$data['lead'] = LeadFollowUp::where('date',date('Y-m-d', strtotime($data['date'])))->get();
    	$data['lead_date'] = '';
    	$data['lead_date'] = 'Lead  - '.$data['lead'] ;
    	return response()->json(['status' => 'success','data' => $data]);
    }
    public function event_data(Request $request)
    {
    	if ($request->employee && $request->lesource)
    	{
    		// $datas['today_followup'] = LeadFollowUp::where('date', date('Y-m-d'))
    		// ->where('employee_id', $request->employee)
    		// ->count();
    		$data['appointment_date'] = LeadFollowUp::where('employee_id', $request->employee)
    		->whereMonth('date', date('m'))
    		->whereYear('date', date('Y'))
    		->where('date', '!=', null)
    		->groupBy('date')
    		->get('date');
    		$data['title'] = '';
    		$data['event1'] = [];
    		foreach ($data['appointment_date'] as $date) {
    			$data['appointment_event'] = LeadFollowUp::where('employee_id', $request->employee)
    			->whereMonth('date', date('m'))
    			->whereYear('date', date('Y'))
    			->where('date', '!=', null)
    			->where('date', $date['date'])
    			->get();
    			foreach ($data['appointment_event'] as $app) {
    				if ($app['date'] == $date['date']) {
    					$title = '';
    					$data['event1'][date('Y-m-d', strtotime($date['date']))] = [
    						'Lead' => LeadFollowUp::with('lead', 'lead.client')->leftJoin('lead as ld', 'ld.id', 'lead_followup.lead_id')->where('lead_followup.employee_id', $request->employee)->where('ld.active', 1)->select('lead_followup.*')->with('lead', 'lead.client')->where('ld.source_id',$request->lesource)->where(['lead_followup.date' => $date['date']])->whereMonth('lead_followup.date', date('m'))->whereYear('lead_followup.date', date('Y'))->get()->toArray(),
    					];
    					$data['title'] = $title;
    				}
    			}
    		}


    		$data['iv_followup_date'] = IVFollowup::where('employee_id', $request->employee)
    		->where('status_date', date('Y-m-d'))
    		->groupBy('status_date')
    		->get('status_date');
    		$data['title1'] = '';
    		$data['event2'] = [];
    		foreach ($data['iv_followup_date'] as $date) {
    			$data['iv_event'] = IVFollowup::where('employee_id', $request->employee)
    			->where('status_date', $date['status_date'])
    			->get();
    			foreach ($data['iv_event'] as $app) {
    				if ($app['status_date'] == $date['status_date']) {
    					$title = '';
    					$data['event2'][date('Y-m-d', strtotime($date['status_date']))] = [
    						'IV' => IVFollowup::with('employee')->where('employee_id', $request->employee)->where('status_date' , $date['status_date'])->get()->toArray(),
    					];
    					$data['title1'] = $title;
    				}
    			}
    		}


    		// $lead_assign = LeadAssign::get();
    		// $array = [];
    		// foreach ($lead_assign as $dt) {
    		// 	$ex_arr = explode(',', $dt->employee_id);
    		// 	if (in_array($request->employee, $ex_arr)) {
    		// 		array_push($array, $dt->lead_id);
    		// 	}
    		// }
    		// $datas['total_lead'] = Lead::where('active', 1)->whereIn('id', $array)->count();
    		// $datas['total_lead_month'] = Lead::whereMonth('date', date('m'))
    		// ->whereYear('date', date('Y'))
    		// ->whereIn('id', $array)
    		// ->where('active', 1)
    		// ->count();
    		// $datas['total_lead_today'] = Lead::whereDate('date', date('Y-m-d'))
    		// ->whereIn('id', $array)
    		// ->where('active', 1)
    		// ->count();
    		// $datas['weekly'] = Lead::select("*")
    		// ->where('active', 1)
    		// ->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
    		// ->whereIn('id', $array)
    		// ->count();
    	} 
    	else if($request->employee)
    	{    		
    		// $datas['today_followup'] =LeadFollowUp::where('date',date('Y-m-d'))->where('employee_id',$request->employee)->count();
    		$data['appointment_date'] = LeadFollowUp::where('employee_id',$request->employee)->whereMonth('date',date('m'))->whereYear('date',date('Y'))->where('date','!=',null)->groupBy('date')->get('date');
    		$data['title'] = '';
    		$data['event1'] = [];
    		foreach($data['appointment_date'] as $date)
    		{
    			$data['appointment_event'] = LeadFollowUp::where('employee_id',$request->employee)->whereMonth('date',date('m'))->whereYear('date',date('Y'))->where('date','!=',null)->where('date',$date['date'])->get();
    			foreach ($data['appointment_event'] as $app) 
    			{
    				if($app['date'] == $date['date'])
    				{
    					$title = '';
    					$data['event1'][date('Y-m-d',strtotime($date['date']))]=[ 
    						'Lead' => LeadFollowUp::with('lead','lead.client')->leftJoin('lead as ld','ld.id','lead_followup.lead_id')->where('lead_followup.employee_id',$request->employee)->where('ld.active',1)->select('lead_followup.*')->with('lead','lead.client')->where(['lead_followup.date'=>$date['date']])->whereMonth('lead_followup.date',date('m'))->whereYear('lead_followup.date',date('Y'))->get()->toArray(),
    					];
    					$data['title'] = $title;
    				}
    			}
    		}


    		$data['iv_followup_date'] = IVFollowup::where('employee_id', $request->employee)
    		->where('status_date', date('Y-m-d'))
    		->groupBy('status_date')
    		->get('status_date');
    		$data['title1'] = '';
    		$data['event2'] = [];
    		foreach ($data['iv_followup_date'] as $date) {
    			$data['iv_event'] = IVFollowup::where('employee_id', $request->employee)
    			->where('status_date', $date['status_date'])
    			->get();
    			foreach ($data['iv_event'] as $app) {
    				if ($app['status_date'] == $date['status_date']) {
    					$title = '';
    					$data['event2'][date('Y-m-d', strtotime($date['status_date']))] = [
    						'IV' => IVFollowup::with('employee')->where('employee_id', $request->employee)->where('status_date' , $date['status_date'])->get()->toArray(),
    					];
    					$data['title1'] = $title;
    				}
    			}
    		}


    		// $lead_assign = LeadAssign::get();
    		// $array = [];
    		// foreach($lead_assign as $dt)
    		// {
    		// 	$ex_arr = explode(',', $dt->employee_id);
    		// 	if(in_array($request->employee,$ex_arr))
    		// 	{
    		// 		array_push($array,$dt->lead_id);
    		// 	}
    		// }
    		// $datas['total_lead'] = Lead::where('active',1)->whereIn('id',$array)->count();
    		// $datas['total_lead_month'] = Lead::whereMonth('date', date('m'))->whereYear('date', date('Y'))->whereIn('id',$array)->where('active',1)->count();
    		// $datas['total_lead_today'] = Lead::whereDate('date', date('Y-m-d'))->whereIn('id',$array)->where('active',1)->count();
    		// $datas['weekly'] = Lead::select("*")->where('active',1)
    		// ->whereBetween('date', 
    		// 	[Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
    		// )->whereIn('id',$array)
    		// ->count();
    	}
    	elseif ($request->lesource)
    	{
    		if(Auth::user()->role == 2)
    		{
    			// $datas['total_lead'] = Lead::where('branch_id',Auth::user()->id)->where('active',1)->count();
    			// dd($datas['total_lead']);
    			// $datas['total_lead_today'] = Lead::whereDate('date',date('Y-m-d'))->where('branch_id',Auth::user()->id)->where('active',1)->count();
    			// $datas['weekly'] = Lead::select("*")->where('active',1)
    			// ->whereBetween('date', 
    			// 	[Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
    			// )->where('branch_id',Auth::user()->id)
    			// ->count();
    			// $datas['today_followup'] =LeadFollowUp::leftJoin('lead as ld','ld.id','lead_followup.lead_id')->where('ld.active',1)->where('ld.branch_id',Auth::user()->id)->select('lead_followup.*')->where(['lead_followup.date'=>date('Y-m-d')])->count();
    			// $datas['total_lead_month'] = Lead::whereMonth('date',date('m'))->whereYear('date', date('Y'))->where('branch_id',Auth::user()->id)->where('active',1)->count();
    			$data['appointment_date'] = LeadFollowUp::groupBy('date')->whereMonth('date',date('m'))->whereYear('date',date('Y'))->where('date','!=',null)->get('date');
    			$data['title'] = '';
    			$data['event1'] = [];
    			foreach($data['appointment_date'] as $date)
    			{
    				$data['appointment_event'] = LeadFollowUp::where('date',$date['date'])->whereMonth('date',date('m'))->whereYear('date',date('Y'))->where('date','!=',null)->get();
    				foreach ($data['appointment_event'] as $app) 
    				{
    					if($app['date'] == $date['date'])
    					{
    						$title = '';
    						$data['event1'][date('Y-m-d',strtotime($date['date']))]=[ 
    							'Lead' => LeadFollowUp::with('lead','lead.client')->leftJoin('lead as ld','ld.id','lead_followup.lead_id')->where('ld.branch_id',Auth::user()->id)->where('ld.active',1)->select('lead_followup.*')->with('lead','lead.client')->where(['lead_followup.date'=>$date['date']])->where('ld.source_id',$request->lesource)->whereMonth('lead_followup.date',date('m'))->whereYear('lead_followup.date',date('Y'))->get()->toArray(),
    						];
    						$data['title'] = $title;
    					}
    				}
    			}
    		}else
    		{
    			// $datas['total_lead'] = Lead::where('active',1)->count();
    			// $datas['total_lead_today'] = Lead::whereDate('date',date('Y-m-d'))->where('active',1)->count();
    			// $datas['weekly'] = Lead::select("*")->where('active',1)
    			// ->whereBetween('date', 
    			// 	[Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
    			// )
    			// ->count();
    			// $datas['today_followup'] = LeadFollowUp::leftJoin('lead as ld','ld.id','lead_followup.lead_id')->where('ld.active',1)->select('lead_followup.*')->where(['lead_followup.date'=>date('Y-m-d')])->count();
    			// $datas['total_lead_month'] = Lead::whereMonth('date',date('m'))->whereYear('date', date('Y'))->where('active',1)->count();
    			$data['appointment_date'] = LeadFollowUp::groupBy('date')->whereMonth('date',date('m'))->whereYear('date',date('Y'))->where('date','!=',null)->get('date');
    			$data['title'] = '';
    			$data['event1'] = [];
    			foreach($data['appointment_date'] as $date)
    			{
    				$data['appointment_event'] = LeadFollowUp::where('date',$date['date'])->whereMonth('date',date('m'))->whereYear('date',date('Y'))->where('date','!=',null)->get();
    				foreach ($data['appointment_event'] as $app) 
    				{
    					if($app['date'] == $date['date'])
    					{
    						$title = '';
    						$data['event1'][date('Y-m-d',strtotime($date['date']))]=[ 
    							'Lead' => LeadFollowUp::with('lead','lead.client')->leftJoin('lead as ld','ld.id','lead_followup.lead_id')->where('ld.active',1)->select('lead_followup.*')->with('lead','lead.client')->where(['lead_followup.date'=>$date['date']])->where('ld.source_id',$request->lesource)->whereMonth('lead_followup.date',date('m'))->whereYear('lead_followup.date',date('Y'))->get()->toArray(),
    						];
    						$data['title'] = $title;
    					}
    				}
    			}
    		}
    	}
    	else
    	{
    		if(Auth::user()->role == 2)
    		{
    			// $datas['total_lead'] = Lead::where('branch_id',Auth::user()->id)->where('active',1)->count();
    			// $datas['total_lead_today'] = Lead::whereDate('date',date('Y-m-d'))->where('branch_id',Auth::user()->id)->where('active',1)->count();
    			// $datas['weekly'] = Lead::select("*")->where('active',1)
    			// ->whereBetween('date', 
    			// 	[Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
    			// )->where('branch_id',Auth::user()->id)
    			// ->count();
    			// $datas['today_followup'] =LeadFollowUp::leftJoin('lead as ld','ld.id','lead_followup.lead_id')->where('ld.active',1)->where('ld.branch_id',Auth::user()->id)->select('lead_followup.*')->where(['lead_followup.date'=>date('Y-m-d')])->count();
    			// $datas['total_lead_month'] = Lead::whereMonth('date',date('m'))->whereYear('date', date('Y'))->where('branch_id',Auth::user()->id)->where('active',1)->count();
    			$data['appointment_date'] = LeadFollowUp::groupBy('date')->whereMonth('date',date('m'))->whereYear('date',date('Y'))->where('date','!=',null)->get('date');
    			$data['title'] = '';
    			$data['event1'] = [];
    			foreach($data['appointment_date'] as $date)
    			{
    				$data['appointment_event'] = LeadFollowUp::where('date',$date['date'])->whereMonth('date',date('m'))->whereYear('date',date('Y'))->where('date','!=',null)->get();
    				foreach ($data['appointment_event'] as $app) 
    				{
    					if($app['date'] == $date['date'])
    					{
    						$title = '';
    						$data['event1'][date('Y-m-d',strtotime($date['date']))]=[ 
    							'Lead' => LeadFollowUp::with('lead','lead.client')->leftJoin('lead as ld','ld.id','lead_followup.lead_id')->where('ld.branch_id',Auth::user()->id)->where('ld.active',1)->select('lead_followup.*')->with('lead','lead.client')->where(['lead_followup.date'=>$date['date']])->whereMonth('lead_followup.date',date('m'))->whereYear('lead_followup.date',date('Y'))->get()->toArray(),
    						];
    						$data['title'] = $title;
    					}
    				}
    			}

    			$data['iv_followup_date'] = IVFollowup::where('employee_id', $request->employee)
    			->where('status_date', date('Y-m-d'))
    			->groupBy('status_date')
    			->get('status_date');
    			$data['title1'] = '';
    			$data['event2'] = [];
    			foreach ($data['iv_followup_date'] as $date) {
    				$data['iv_event'] = IVFollowup::where('employee_id', $request->employee)
    				->where('status_date', $date['status_date'])
    				->get();
    				foreach ($data['iv_event'] as $app) {
    					if ($app['status_date'] == $date['status_date']) {
    						$title = '';
    						$data['event2'][date('Y-m-d', strtotime($date['status_date']))] = [
    							'IV' => IVFollowup::with('employee')->where('employee_id', $request->employee)->where('status_date' , $date['status_date'])->get()->toArray(),
    						];
    						$data['title1'] = $title;
    					}
    				}
    			}
    		}
    		else
    		{
    			// $datas['total_lead'] = Lead::where('active',1)->count();
    			// $datas['total_lead_today'] = Lead::whereDate('date',date('Y-m-d'))->where('active',1)->count();
    			// $datas['weekly'] = Lead::select("*")->where('active',1)
    			// ->whereBetween('date', 
    			// 	[Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
    			// )
    			// ->count();
    			// $datas['today_followup'] =LeadFollowUp::leftJoin('lead as ld','ld.id','lead_followup.lead_id')->where('ld.active',1)->select('lead_followup.*')->where(['lead_followup.date'=>date('Y-m-d')])->count();
    			// $datas['total_lead_month'] = Lead::whereMonth('date',date('m'))->whereYear('date', date('Y'))->where('active',1)->count();
    			$data['appointment_date'] = LeadFollowUp::groupBy('date')->whereMonth('date',date('m'))->whereYear('date',date('Y'))->where('date','!=',null)->get('date');
    			$data['title'] = '';
    			$data['event1'] = [];
    			foreach($data['appointment_date'] as $date)
    			{
    				$data['appointment_event'] = LeadFollowUp::where('date',$date['date'])->whereMonth('date',date('m'))->whereYear('date',date('Y'))->where('date','!=',null)->get();
    				foreach ($data['appointment_event'] as $app) 
    				{
    					if($app['date'] == $date['date'])
    					{
    						$title = '';
    						$data['event1'][date('Y-m-d',strtotime($date['date']))]=[ 
    							'Lead' => LeadFollowUp::with('lead','lead.client')->leftJoin('lead as ld','ld.id','lead_followup.lead_id')->where('ld.active',1)->select('lead_followup.*')->with('lead','lead.client')->where(['lead_followup.date'=>$date['date']])->whereMonth('lead_followup.date',date('m'))->whereYear('lead_followup.date',date('Y'))->get()->toArray(),
    						];
    						$data['title'] = $title;
    					}
    				}
    			}

    			$data['iv_followup_date'] = IVFollowup::where('employee_id', $request->employee)
    			->where('status_date', date('Y-m-d'))
    			->groupBy('status_date')
    			->get('status_date');
    			$data['title1'] = '';
    			$data['event2'] = [];
    			foreach ($data['iv_followup_date'] as $date) {
    				$data['iv_event'] = IVFollowup::where('employee_id', $request->employee)
    				->where('status_date', $date['status_date'])
    				->get();
    				foreach ($data['iv_event'] as $app) {
    					if ($app['status_date'] == $date['status_date']) {
    						$title = '';
    						$data['event2'][date('Y-m-d', strtotime($date['status_date']))] = [
    							'IV' => IVFollowup::with('employee')->where('employee_id', $request->employee)->where('status_date' , $date['status_date'])->get()->toArray(),
    						];
    						$data['title1'] = $title;
    					}
    				}
    			}
    		}
    	}
    	$datas['dt_event'] = [];
    	foreach ($data['event1'] as $key => $value) {
    		foreach ($value['Lead'] as $keys => $values) {
    			if(isset($values['lead']['client']['member']))
    			{
    				$title =$values['lead']['client']['member'];
    			}else
    			{
    				$title = '';
    			}
    			$url = '';
    			// $url =  route("user.lead.edit", $values['lead']['id']);
    			
    			$test = [
    				'title'=> $title,
    				'start'=> $key,
    				'end'=> $key,
    				'icon'=> "clock",
    				'url'=>$url,
    				'className'=> 'eventClass'
    			];
    			array_push($datas['dt_event'], $test);
    		}
    	}

    	foreach ($data['event2'] as $key => $value) {
    		foreach ($value['IV'] as $keys => $values) {
    			if(isset($values['employee']['name']))
    			{
    				$title ='IV-'.$values['employee']['name'];
    			}else
    			{
    				$title = '';
    			}
    			$url = '';
    			// $url =  route("user.lead.edit", $values['lead']['id']);
    			
    			$test1 = [
    				'title'=> $title,
    				'start'=> $key,
    				'end'=> $key,
    				'icon'=> "clock",
    				'url'=>$url,
    				'className'=> 'eventClass1'
    			];
    			array_push($datas['dt_event'], $test1);
    		}
    	}


    	return response()->json(['data'=>$datas]);
    }
    public function salesreport(Request $request)
    {
    	if ($request->ajax()) {
    		$query = Employee::where('role',1)->where('status',1);
    		if(Auth::user()->role == 2)
    		{
    			$query->where('branch_id',Auth::user()->id);
    		}
    		if($request->branch_id)
    		{
    			$query->where('branch_id',$request->branch_id);
    		}
    		$query = $query->get();
    		return Datatables::of($query)
    		->addColumn('leadsYesterday', function ($row) {
    			$btn = LeadAssign::leftJoin('lead as ll','ll.id','lead_assign.lead_id')
    			->whereRaw('FIND_IN_SET(?, lead_assign.employee_id)', [$row->id])
    			->where('ll.date', date('Y-m-d', strtotime('-1 day')))
    			->where('ll.active',1)->count();
    			return $btn;
    		})

    		->addColumn('leadactive', function ($row) {
    			$btn = LeadAssign::leftJoin('lead as ll','ll.id','lead_assign.lead_id')
    			->whereRaw('FIND_IN_SET(?, lead_assign.employee_id)', [$row->id])
    			->where('ll.active',1)->count();
    			return $btn;
    		})
    		->addColumn('leadpending', function ($row) {
    			$btn = LeadAssign::leftJoin('lead as ll','ll.id','lead_assign.lead_id')
    			->whereRaw('FIND_IN_SET(?, lead_assign.employee_id)', [$row->id])
    			->where('ll.active',1)->where('status',1)->count();
    			return $btn;
    		})
    		->addColumn('leaddiscuss', function ($row) {
    			$btn = LeadAssign::leftJoin('lead as ll','ll.id','lead_assign.lead_id')
    			->whereRaw('FIND_IN_SET(?, lead_assign.employee_id)', [$row->id])
    			->where('ll.active',1)->where('status',2)->count();
    			return $btn;
    		})
    		->addColumn('leadrevision', function ($row) {
    			$btn = LeadAssign::leftJoin('lead as ll','ll.id','lead_assign.lead_id')
    			->whereRaw('FIND_IN_SET(?, lead_assign.employee_id)', [$row->id])
    			->where('ll.active',1)->where('status',3)->count();
    			return $btn;
    		})
    		->addColumn('leadwin', function ($row) {
    			$btn = LeadAssign::leftJoin('lead as ll','ll.id','lead_assign.lead_id')
    			->whereRaw('FIND_IN_SET(?, lead_assign.employee_id)', [$row->id])
    			->where('ll.active',1)->where('status',4)->count();
    			return $btn;
    		})
    		->addColumn('leadlost', function ($row) {
    			$btn = LeadAssign::leftJoin('lead as ll','ll.id','lead_assign.lead_id')
    			->whereRaw('FIND_IN_SET(?, lead_assign.employee_id)', [$row->id])
    			->where('ll.active',1)->where('status',5)->count();
    			return $btn;
    		})
    		->setRowClass(function () {
    			return 'row-move';
    		})
    		->setRowId(function ($row) {
    			return 'row-' . $row->id;
    		})
    		->rawColumns(['leadsYesterday','leadactive','leadpending','leaddiscuss','leadrevision','leadwin','leadlost'])
    		->make(true);
    	} 
    	return view('user.home');
    }
    public function leadsourcereport(Request $request)
    {
    	if ($request->ajax()) {
    		$query = LeadSource::where('status',1);
    		if(Auth::user()->role == 2)
    		{
    			$query = LeadSource::where('branch_id',Auth::user()->id)->where('status',1);
    		}
    		if($request->branch_ids)
    		{
    			$query->where('branch_id',$request->branch_ids);
    		}
    		$query = $query->get();
    		return Datatables::of($query)
    		
    		->addColumn('sourceactive', function ($row) {
    			$btn = Lead::where('source_id',$row->id)->where('active',1)->count();
    			return $btn;
    		})
    		
    		->addColumn('sourcewin', function ($row) {
    			$btn = Lead::where('source_id',$row->id)->where('active', 1)->where('status',4)->count();
    			return $btn;
    		})
    		->setRowClass(function () {
    			return 'row-move';
    		})
    		->setRowId(function ($row) {
    			return 'row-' . $row->id;
    		})
    		->rawColumns(['leadsYesterday','sourceactive','leadpending','leaddiscuss','leadrevision','sourcewin','leadlost'])
    		->make(true);
    	} 
    	return view('user.home');
    }
    public function event_datatest(Request $request)
    {
    	if ($request->employee && $request->lesource)
    	{
    		$datas['today_followup'] = LeadFollowUp::where('date', date('Y-m-d'))
    		->where('employee_id', $request->employee)
    		->count();
    		$data['appointment_date'] = LeadFollowUp::where('employee_id', $request->employee)
    		->whereMonth('date', date('m'))
    		->whereYear('date', date('Y'))
    		->where('date', '!=', null)
    		->groupBy('date')
    		->get('date');
    		$data['title'] = '';
    		$data['event1'] = [];
    		foreach ($data['appointment_date'] as $date) {
    			$data['appointment_event'] = LeadFollowUp::where('employee_id', $request->employee)
    			->whereMonth('date', date('m'))
    			->whereYear('date', date('Y'))
    			->where('date', '!=', null)
    			->where('date', $date['date'])
    			->get();
    			foreach ($data['appointment_event'] as $app) {
    				if ($app['date'] == $date['date']) {
    					$title = '';
    					$data['event1'][date('Y-m-d', strtotime($date['date']))] = [
    						'Lead' => LeadFollowUp::with('lead', 'lead.client')->leftJoin('lead as ld', 'ld.id', 'lead_followup.lead_id')->where('lead_followup.employee_id', $request->employee)->where('ld.active', 1)->select('lead_followup.*')->with('lead', 'lead.client')->where('ld.source_id',$request->lesource)->where(['lead_followup.date' => $date['date']])->whereMonth('lead_followup.date', date('m'))->whereYear('lead_followup.date', date('Y'))->get()->toArray(),
    					];
    					$data['title'] = $title;
    				}
    			}
    		}
    		$lead_assign = LeadAssign::get();
    		$array = [];
    		foreach ($lead_assign as $dt) {
    			$ex_arr = explode(',', $dt->employee_id);
    			if (in_array($request->employee, $ex_arr)) {
    				array_push($array, $dt->lead_id);
    			}
    		}
    		$datas['total_lead'] = Lead::where('active', 1)->whereIn('id', $array)->count();
    		$datas['total_lead_month'] = Lead::whereMonth('date', date('m'))
    		->whereYear('date', date('Y'))
    		->whereIn('id', $array)
    		->where('active', 1)
    		->count();
    		$datas['total_lead_today'] = Lead::whereDate('date', date('Y-m-d'))
    		->whereIn('id', $array)
    		->where('active', 1)
    		->count();
    		$datas['weekly'] = Lead::select("*")
    		->where('active', 1)
    		->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
    		->whereIn('id', $array)
    		->count();
    	} 
    	else if($request->employee)
    	{    		
    		$datas['today_followup'] =LeadFollowUp::where('date',date('Y-m-d'))->where('employee_id',$request->employee)->count();
    		$data['appointment_date'] = LeadFollowUp::where('employee_id',$request->employee)->whereMonth('date',date('m'))->whereYear('date',date('Y'))->where('date','!=',null)->groupBy('date')->get('date');
    		$data['title'] = '';
    		$data['event1'] = [];
    		foreach($data['appointment_date'] as $date)
    		{
    			$data['appointment_event'] = LeadFollowUp::where('employee_id',$request->employee)->whereMonth('date',date('m'))->whereYear('date',date('Y'))->where('date','!=',null)->where('date',$date['date'])->get();
    			foreach ($data['appointment_event'] as $app) 
    			{
    				if($app['date'] == $date['date'])
    				{
    					$title = '';
    					$data['event1'][date('Y-m-d',strtotime($date['date']))]=[ 
    						'Lead' => LeadFollowUp::with('lead','lead.client')->leftJoin('lead as ld','ld.id','lead_followup.lead_id')->where('lead_followup.employee_id',$request->employee)->where('ld.active',1)->select('lead_followup.*')->with('lead','lead.client')->where(['lead_followup.date'=>$date['date']])->whereMonth('lead_followup.date',date('m'))->whereYear('lead_followup.date',date('Y'))->get()->toArray(),
    					];
    					$data['title'] = $title;
    				}
    			}
    		}
    		$lead_assign = LeadAssign::get();
    		$array = [];
    		foreach($lead_assign as $dt)
    		{
    			$ex_arr = explode(',', $dt->employee_id);
    			if(in_array($request->employee,$ex_arr))
    			{
    				array_push($array,$dt->lead_id);
    			}
    		}
    		$datas['total_lead'] = Lead::where('active',1)->whereIn('id',$array)->count();
    		$datas['total_lead_month'] = Lead::whereMonth('date', date('m'))->whereYear('date', date('Y'))->whereIn('id',$array)->where('active',1)->count();
    		$datas['total_lead_today'] = Lead::whereDate('date', date('Y-m-d'))->whereIn('id',$array)->where('active',1)->count();
    		$datas['weekly'] = Lead::select("*")->where('active',1)
    		->whereBetween('date', 
    			[Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
    		)->whereIn('id',$array)
    		->count();
    	}
    	elseif ($request->lesource)
    	{
    		if(Auth::user()->role == 2)
    		{
    			$datas['total_lead'] = Lead::where('branch_id',Auth::user()->id)->where('active',1)->count();
    			// dd($datas['total_lead']);
    			$datas['total_lead_today'] = Lead::whereDate('date',date('Y-m-d'))->where('branch_id',Auth::user()->id)->where('active',1)->count();
    			$datas['weekly'] = Lead::select("*")->where('active',1)
    			->whereBetween('date', 
    				[Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
    			)->where('branch_id',Auth::user()->id)
    			->count();
    			$datas['today_followup'] =LeadFollowUp::leftJoin('lead as ld','ld.id','lead_followup.lead_id')->where('ld.active',1)->where('ld.branch_id',Auth::user()->id)->select('lead_followup.*')->where(['lead_followup.date'=>date('Y-m-d')])->count();
    			$datas['total_lead_month'] = Lead::whereMonth('date',date('m'))->whereYear('date', date('Y'))->where('branch_id',Auth::user()->id)->where('active',1)->count();
    			$data['appointment_date'] = LeadFollowUp::groupBy('date')->whereMonth('date',date('m'))->whereYear('date',date('Y'))->where('date','!=',null)->get('date');
    			$data['title'] = '';
    			$data['event1'] = [];
    			foreach($data['appointment_date'] as $date)
    			{
    				$data['appointment_event'] = LeadFollowUp::where('date',$date['date'])->whereMonth('date',date('m'))->whereYear('date',date('Y'))->where('date','!=',null)->get();
    				foreach ($data['appointment_event'] as $app) 
    				{
    					if($app['date'] == $date['date'])
    					{
    						$title = '';
    						$data['event1'][date('Y-m-d',strtotime($date['date']))]=[ 
    							'Lead' => LeadFollowUp::with('lead','lead.client')->leftJoin('lead as ld','ld.id','lead_followup.lead_id')->where('ld.branch_id',Auth::user()->id)->where('ld.active',1)->select('lead_followup.*')->with('lead','lead.client')->where(['lead_followup.date'=>$date['date']])->where('ld.source_id',$request->lesource)->whereMonth('lead_followup.date',date('m'))->whereYear('lead_followup.date',date('Y'))->get()->toArray(),
    						];
    						$data['title'] = $title;
    					}
    				}
    			}
    		}else
    		{
    			$datas['total_lead'] = Lead::where('active',1)->count();
    			$datas['total_lead_today'] = Lead::whereDate('date',date('Y-m-d'))->where('active',1)->count();
    			$datas['weekly'] = Lead::select("*")->where('active',1)
    			->whereBetween('date', 
    				[Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
    			)
    			->count();
    			$datas['today_followup'] =LeadFollowUp::leftJoin('lead as ld','ld.id','lead_followup.lead_id')->where('ld.active',1)->select('lead_followup.*')->where(['lead_followup.date'=>date('Y-m-d')])->count();
    			$datas['total_lead_month'] = Lead::whereMonth('date',date('m'))->whereYear('date', date('Y'))->where('active',1)->count();
    			$data['appointment_date'] = LeadFollowUp::groupBy('date')->whereMonth('date',date('m'))->whereYear('date',date('Y'))->where('date','!=',null)->get('date');
    			$data['title'] = '';
    			$data['event1'] = [];
    			foreach($data['appointment_date'] as $date)
    			{
    				$data['appointment_event'] = LeadFollowUp::where('date',$date['date'])->whereMonth('date',date('m'))->whereYear('date',date('Y'))->where('date','!=',null)->get();
    				foreach ($data['appointment_event'] as $app) 
    				{
    					if($app['date'] == $date['date'])
    					{
    						$title = '';
    						$data['event1'][date('Y-m-d',strtotime($date['date']))]=[ 
    							'Lead' => LeadFollowUp::with('lead','lead.client')->leftJoin('lead as ld','ld.id','lead_followup.lead_id')->where('ld.active',1)->select('lead_followup.*')->with('lead','lead.client')->where(['lead_followup.date'=>$date['date']])->where('ld.source_id',$request->lesource)->whereMonth('lead_followup.date',date('m'))->whereYear('lead_followup.date',date('Y'))->get()->toArray(),
    						];
    						$data['title'] = $title;
    					}
    				}
    			}
    		}
    	}
    	else
    	{
    		if(Auth::user()->role == 2)
    		{
    			$datas['total_lead'] = Lead::where('branch_id',Auth::user()->id)->where('active',1)->count();
    			$datas['total_lead_today'] = Lead::whereDate('date',date('Y-m-d'))->where('branch_id',Auth::user()->id)->where('active',1)->count();
    			$datas['weekly'] = Lead::select("*")->where('active',1)
    			->whereBetween('date', 
    				[Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
    			)->where('branch_id',Auth::user()->id)
    			->count();
    			$datas['today_followup'] =LeadFollowUp::leftJoin('lead as ld','ld.id','lead_followup.lead_id')->where('ld.active',1)->where('ld.branch_id',Auth::user()->id)->select('lead_followup.*')->where(['lead_followup.date'=>date('Y-m-d')])->count();
    			$datas['total_lead_month'] = Lead::whereMonth('date',date('m'))->whereYear('date', date('Y'))->where('branch_id',Auth::user()->id)->where('active',1)->count();
    			$data['appointment_date'] = LeadFollowUp::groupBy('date')->whereMonth('date',date('m'))->whereYear('date',date('Y'))->where('date','!=',null)->get('date');
    			$data['title'] = '';
    			$data['event1'] = [];
    			foreach($data['appointment_date'] as $date)
    			{
    				$data['appointment_event'] = LeadFollowUp::where('date',$date['date'])->whereMonth('date',date('m'))->whereYear('date',date('Y'))->where('date','!=',null)->get();
    				foreach ($data['appointment_event'] as $app) 
    				{
    					if($app['date'] == $date['date'])
    					{
    						$title = '';
    						$data['event1'][date('Y-m-d',strtotime($date['date']))]=[ 
    							'Lead' => LeadFollowUp::with('lead','lead.client')->leftJoin('lead as ld','ld.id','lead_followup.lead_id')->where('ld.branch_id',Auth::user()->id)->where('ld.active',1)->select('lead_followup.*')->with('lead','lead.client')->where(['lead_followup.date'=>$date['date']])->whereMonth('lead_followup.date',date('m'))->whereYear('lead_followup.date',date('Y'))->get()->toArray(),
    						];
    						$data['title'] = $title;
    					}
    				}
    			}
    		}
    		else
    		{
    			$datas['total_lead'] = Lead::where('active',1)->count();
    			$datas['total_lead_today'] = Lead::whereDate('date',date('Y-m-d'))->where('active',1)->count();
    			$datas['weekly'] = Lead::select("*")->where('active',1)
    			->whereBetween('date', 
    				[Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
    			)
    			->count();
    			$datas['today_followup'] =LeadFollowUp::leftJoin('lead as ld','ld.id','lead_followup.lead_id')->where('ld.active',1)->select('lead_followup.*')->where(['lead_followup.date'=>date('Y-m-d')])->count();
    			$datas['total_lead_month'] = Lead::whereMonth('date',date('m'))->whereYear('date', date('Y'))->where('active',1)->count();
    			$data['appointment_date'] = LeadFollowUp::groupBy('date')->whereMonth('date',date('m'))->whereYear('date',date('Y'))->where('date','!=',null)->get('date');
    			$data['title'] = '';
    			$data['event1'] = [];
    			foreach($data['appointment_date'] as $date)
    			{
    				$data['appointment_event'] = LeadFollowUp::where('date',$date['date'])->whereMonth('date',date('m'))->whereYear('date',date('Y'))->where('date','!=',null)->get();
    				foreach ($data['appointment_event'] as $app) 
    				{
    					if($app['date'] == $date['date'])
    					{
    						$title = '';
    						$data['event1'][date('Y-m-d',strtotime($date['date']))]=[ 
    							'Lead' => LeadFollowUp::with('lead','lead.client')->leftJoin('lead as ld','ld.id','lead_followup.lead_id')->where('ld.active',1)->select('lead_followup.*')->with('lead','lead.client')->where(['lead_followup.date'=>$date['date']])->whereMonth('lead_followup.date',date('m'))->whereYear('lead_followup.date',date('Y'))->get()->toArray(),
    						];
    						$data['title'] = $title;
    					}
    				}
    			}
    		}
    	}
    	$datas['dt_event'] = [];
    	foreach ($data['event1'] as $key => $value) {
    		foreach ($value['Lead'] as $keys => $values) {
    			if(isset($values['lead']['client']['member']))
    			{
    				$title =$values['lead']['client']['member'];
    			}else
    			{
    				$title = '';
    			}
    			// $url = '';
    			$url =  route("user.lead.edit", $values['lead']['id']);
    			
    			$test = [
    				'title'=> $title,
    				'start'=> $key,
    				'end'=> $key,
    				'icon'=> "clock",
    				'url'=>$url,
    				'className'=> 'eventClass'
    			];
    			array_push($datas['dt_event'], $test);
    		}
    	}
    	return response()->json(['data'=>$datas]);
    }
}
