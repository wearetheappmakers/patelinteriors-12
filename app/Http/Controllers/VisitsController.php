<?php
namespace App\Http\Controllers;
use App\Visits;
use DataTables;
use App\Employee;
use App\User;
use App\SourceName;
use App\Company;
use App\Product;
use App\ProductCategory;
use App\VisitsChild;
use App\IVFollowup;
use App\IVFollowupLog;
use App\PMC;
use App\Type;
use App\Arid;
use Auth;
use Illuminate\Http\Request;
use App\Exports\InteriorVisitExport;
use Excel;
use PDF;
class VisitsController extends Controller
{
    public function __construct(Visits $s)
    {
        $this->view = 'visits';
        $this->route = 'visits';
        $this->viewName = 'Visits';
    }
    public function index(Request $request)
    {
        if(Auth::guard('employee')->check())
        {
            if ($request->ajax()) {
                $query = Visits::where('employee_id',Auth::user()->id)->orderBy('date','DESC')->with('branch','employee','arids')->latest();
                if($request->arid)
                {
                    $query->where('arid',$request->arid);
                }
                $query = $query->orderBy('date','DESC')->get();
                return Datatables::of($query)
                ->addColumn('action', function ($row) {
                    $btn = view('layouts.actionbtnpermission')->with(['id'=>$row->id,'route'=>'visits'])->render();
                    $btn .= '&nbsp;&nbsp;<a style="background: white;" href="'.route('user.visits.show',$row->id).'"  title="View Details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-eye"></i></a>&nbsp;&nbsp;<button style="background: white;" onclick="model('.$row->id.')" title="Followup" class="btn btn-sm btn-clean btn-icon btn-icon-md " >
                    <i style="color: orange;" class="fa fa-arrow-up"></i>
                    </button>';
                    return $btn;
                })
                ->addColumn('date', function ($row){
                    $btn = ($row->date) ? date('d-m-Y', strtotime($row->date)) : '';
                    return $btn;
                })
                ->addColumn('showroomvisit', function($row){
                    if($row->showroom_visit == 1)
                    {
                        $btn = 'Yes';
                    }
                    elseif($row->showroom_visit == 2)
                    {
                        $btn = 'No';
                    }
                    else
                    {
                        $btn = '-';
                    }
                    return $btn;
                })
                ->addColumn('status', function ($row) {
                    if($row->status == 1)
                    {
                        $btn = '<select class="form-control" disabled><option value="1"';
                    }
                    else
                    {
                        $btn = '<select class="form-control" onchange="statuschange(this,'.$row->id.')"><option value="Not Approved"';
                    }
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
                    $btn .= $selected.'>Completed</option>
                    </select>';
                    return $btn;
                })
                ->addColumn('company', function ($row) {
                    $child = VisitsChild::where('interior_visit_id',$row->id)->get();
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
                ->rawColumns(['action','status','company','showroomvisit'])
                ->make(true);
            } 
        }
        else
        {
            if ($request->ajax()) {
                $query = Visits::with('branch','employee','arids')->orderBy('date','DESC');
                if(Auth::user()->role == 2)
                {
                    $query = Visits::with('branch','employee','arids')->where('branch_id',Auth::user()->id)->orderBy('date','DESC');
                }
                if($request->branch_id)
                {
                    $query->where('branch_id',$request->branch_id);
                }
                if($request->employee_id)
                {
                    $query->where('employee_id',$request->employee_id);
                }
                if($request->arid)
                {
                    $query->where('arid',$request->arid);
                }
                $query = $query->orderBy('date','DESC')->get();
                return Datatables::of($query)
                ->addColumn('action', function ($row) {
                    $btn = view('layouts.actionbtnpermission')->with(['id'=>$row->id,'route'=>'visits'])->render();
                    $btn .= '&nbsp;&nbsp;<a style="background: white;" href="'.route('user.visits.show',$row->id).'"  title="View Details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-eye"></i></a>&nbsp;&nbsp;<button style="background: white;" onclick="model('.$row->id.')" title="Followup" class="btn btn-sm btn-clean btn-icon btn-icon-md " >
                    <i style="color: orange;" class="fa fa-arrow-up"></i>
                    </button>';
                    return $btn;
                })
                ->addColumn('date', function ($row){
                    $btn = ($row->date) ? date('d-m-Y', strtotime($row->date)) : '';
                    return $btn;
                })
                ->addColumn('showroomvisit', function($row){
                    if($row->showroom_visit == 1)
                    {
                        $btn = 'Yes';
                    }
                    elseif($row->showroom_visit == 2)
                    {
                        $btn = 'No';
                    }
                    else
                    {
                        $btn = '-';
                    }
                    return $btn;
                })
                ->addColumn('status', function ($row) {
                    if($row->status == 1)
                    {
                        $btn = '<select class="form-control" disabled><option value="1"';
                    }
                    else
                    {
                        $btn = '<select class="form-control" onchange="statuschange(this,'.$row->id.')"><option value="Not Approved"';
                    }
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
                    $btn .= $selected.'>Completed</option>
                    </select>';
                    return $btn;
                })
                ->addColumn('company', function ($row) {
                    $child = VisitsChild::where('interior_visit_id',$row->id)->get();
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
                ->rawColumns(['action','status','company','showroomvisit'])
                ->make(true);
            } 
        }
        return view('visits.index');
    }

    public function model(Request $request)
    {
        $followuplog = IVFollowupLog::where('iv_id',$request->id)->get();
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

    public function statuschange(Request $request)
    {
        $param['status'] = $request->status;
        $change_status = Visits::where('id',$request->id)->update($param);
        if($change_status)
        {
            return response()->json(['status' => 'success']);
        }
        else
        {
            return response()->json(['status' => 'error']);
        }
    }
    public function create()
    {
        $data['title'] = 'Add ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['visits'] = Visits::get();
        $data['branch'] = User::where('role',2)->get();
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 2)
            {
                $data['employee'] = Employee::where('branch_id',Auth::user()->id)->get();
                $data['productcategory'] = ProductCategory::where('status',1)->where('branch_id',Auth::user()->id)->get();
                $data['arid'] = Arid::where('branch_id',Auth::user()->id)->get();
            }
        }
        else
        {
            $branch = Employee::where('id',Auth::user()->id)->value('branch_id');
            $data['productcategory'] = ProductCategory::where('status',1)->where('branch_id',$branch)->get();
            $data['arid'] = Arid::where('branch_id',$branch)->get();
        }

        return view('visits.create')->with($data);
    }
    public function store(Request $request)
    {
        $visits = new Visits();
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $visits->branch_id = $request->branch_id;
                $visits->employee_id = $request->employee_id;
            }
            else
            {
                $visits->branch_id = Auth::user()->id;
                $visits->employee_id = $request->employee_id;
            }
        }
        else
        {
            $visits->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
            $visits->employee_id = Auth::user()->id;
        }
        $visits->arid = $request->arid;
        $visits->date = $request->date;
        $visits->time = $request->time;
        $visits->remarks = $request->remarks;
        $visits->showroom_visit = $request->showroom_visit;
        $visits->company_person = $request->company_person;
        $visits->assistant = $request->assistant;
        $visits->email = $request->email;
        $visits->number = $request->number;
        $visits->firm_name = $request->firm_name;
        $visits->save();
        if($request->productcategory_id)
        {
            foreach ($request->productcategory_id as $key => $value) {
                if($value != '')
                {
                    $child = new VisitsChild();
                    $child->interior_visit_id = $visits->id;
                    if(Auth::guard('user')->check())
                    {
                        if(Auth::user()->role == 1)
                        {
                            $child->branch_id = $request->child_branch_id[$key];
                        }
                        else
                        {
                            $child->branch_id = Auth::user()->id;
                        }
                    }
                    else
                    {
                        $branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
                        $child->branch_id = $branch_id;
                    }
                    $child->category_id = $value;
                    $child->company_id = $request->company_id[$key];
                    $child->product_id = $request->product_id[$key];
                    $child->save();
                }
            }
        }
        if($visits)
        {
            return response()->json(['status' => 'success','id'=>$visits->id]);
        }else
        {
            return response()->json(['status' => 'error']);
        }
    }

    public function followup_store(Request $request)
    {
        $visit = Visits::where('id',$request->iv_id)->first();
        $check = IVFollowup::where('iv_id',$request->iv_id)->first();
        if(!$check)
        {
            $check = new IVFollowup();
            $check['iv_id'] = $request['iv_id'];
        }
        $check['employee_id'] = $visit['employee_id'];
        $check['subject'] = $request['subject'];
        $check['description'] = $request['description'];
        $check['date'] = $request['iv_date'];
        $check['time'] = $request['iv_time'];
        $check['status_date'] = $request['status_date'];
        $check['is_completed'] = $request['is_completed'];
        $check['type'] = $request['type'];
        $check->save();

        $followuplog = new IVFollowupLog();
        $followuplog['iv_id'] = $request['iv_id'];
        $followuplog['employee_id'] = $visit['employee_id'];
        $followuplog['subject'] = $request['subject'];
        $followuplog['description'] = $request['description'];
        $followuplog['date'] = $request['iv_date'];
        $followuplog['time'] = $request['iv_time'];
        $followuplog['status_date'] = $request['status_date'];
        $followuplog['is_completed'] = $request['is_completed'];
        $followuplog['type'] = $request['type'];
        $followuplog->save();

        if($check)
        {
            return response()->json(['status' => 'success']);
        }
    }
    public function edit($id)
    {
        $data['title'] = 'Edit ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['data'] = Visits::where('id',$id)->first();
        $data['clone'] = VisitsChild::where('interior_visit_id',$id)->get();
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 2)
            {
                $data['employee'] = Employee::where('branch_id',Auth::user()->id)->get();
                $data['productcategory'] = ProductCategory::where('status',1)->where('branch_id',Auth::user()->id)->get();
                $data['arid'] = Arid::where('branch_id',Auth::user()->id)->get();
            }
        }
        else
        {
            $branch = Employee::where('id',Auth::user()->id)->value('branch_id');
            $data['productcategory'] = ProductCategory::where('status',1)->where('branch_id',$branch)->get();
            $data['arid'] = Arid::where('branch_id',$branch)->get();
        }
        $data['branch'] = User::where('role',2)->get();

        return view('visits.edit')->with($data);
    }
    public function update(Request $request, $id)
    {
        $visits = Visits::where('id',$id)->first();
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $visits->branch_id = $request->branch_id;
                $visits->employee_id = $request->employee_id;
            }
            else
            {
                $visits->branch_id = Auth::user()->id;
                $visits->employee_id = $request->employee_id;
            }
        }
        else
        {
            $visits->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
            $visits->employee_id = Auth::user()->id;
        }
        $visits->arid = $request->arid;
        $visits->date = $request->date;
        $visits->time = $request->time;
        $visits->remarks = $request->remarks;
        $visits->company_person = $request->company_person;
        $visits->assistant = $request->assistant;
        $visits->email = $request->email;
        $visits->number = $request->number;
        $visits->firm_name = $request->firm_name;
        $visits->showroom_visit = $request->showroom_visit;
        $visits->save();
        if($request->productcategory_id)
        {
            foreach ($request->productcategory_id as $key => $value) {
                if($value != '')
                {
                    $child = new VisitsChild();
                    if(Auth::guard('user')->check())
                    {
                        if(Auth::user()->role == 1)
                        {
                            $child->branch_id = $request->child_branch_id[$key];
                        }
                        else
                        {
                            $child->branch_id = Auth::user()->id;
                        }
                    }
                    else
                    {
                        $branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
                        $child->branch_id = $branch_id;
                    }
                    $child->interior_visit_id = $visits->id;
                    $child->category_id = $value;
                    $child->company_id = $request->company_id[$key];
                    $child->product_id = $request->product_id[$key];
                    $child->save();
                }
            }
        }
        if($request->edit_productcategory_id)
        {
            foreach ($request->edit_productcategory_id as $key => $value) {
                {
                    if($value != '')
                    {
                        $editchild['interior_visit_id'] = $id;
                        $editchild['category_id'] = $value;
                        if(Auth::guard('user')->check())
                        {
                            if(Auth::user()->role == 1)
                            {
                                $editchild['branch_id'] = $request->edit_child_branch_id[$key];
                            }
                            else
                            {
                                $editchild['branch_id'] = Auth::user()->id;
                            }
                        }
                        else
                        {
                            $branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
                            $editchild['branch_id'] = $branch_id;
                        }
                        $editchild['company_id'] = $request->edit_company_id[$key];
                        $editchild['product_id'] = $request->edit_product_id[$key];
                        $childs = VisitsChild::where('id',$request->child_id[$key])->update($editchild);
                    }
                }
            }
        }
        if($visits)
        {
            return response()->json(['status' => 'success']);
        }
        else
        {
            return response()->json(['status' => 'error']);
        }
    }
    public function show($id)
    {
        $data['data'] = Visits::where('id',$id)->first();
        $data['clone'] = VisitsChild::where('interior_visit_id',$id)->get();
        $data['architect'] = SourceName::get();
        $data['branch'] = User::where('role',2)->get();

        if(Auth::user()->role == 1)
        {
            $data['employee'] = Employee::get();
        }
        else
        {
            $data['employee'] = Employee::where('branch_id',Auth::user()->id)->get();
        }
        return view('visits.show')->with($data);
    }
    public function clonedelete($id)
    {
        $data = VisitsChild::where('id',$id)->delete();
        return back();
    }
    public function getemployee(Request $request)
    {
        $data = Employee::where('branch_id',$request->branch_id)->where('status',1)->get();
        $html = "<option value=''>Select</option>";
        foreach ($data as $key => $value) {
            $html .= "<option value=".$value->id.">".$value->name."</option>";
        }
        $data1 = SourceName::where('branch_id',$request->branch_id)->where('status',1)->get();
        $html1 = "<option value=''>Select</option>";
        foreach ($data1 as $key => $value) {
            $html1 .= "<option value=".$value->id.">".$value->name."</option>";
        }
        $data2 = ProductCategory::where('branch_id',$request->branch_id)->where('status',1)->get();
        $html2 = "<option value=''>Select</option>";
        foreach ($data2 as $key => $value) {
            $html2 .= "<option value=".$value->id.">".$value->name."</option>";
        }
        $data3 = Arid::where('branch_id',$request->branch_id)->get();
        $html3 = "<option value=''>Select</option>";
        foreach ($data3 as $key => $value) {
            $html3 .= "<option value=".$value->id.">".$value->name."</option>";
        }
        return response()->json(['data'=>$html,'data1'=>$html1, 'data2'=>$html2, 'data3'=>$html3]);
    }
    public function getcategory(Request $request)
    {
        $data = ProductCategory::where('branch_id',$request->child_branch_id)->where('status',1)->get();
        $html = "<option value=''>Select</option>";
        foreach ($data as $key => $value) {
            $html .= "<option value=".$value->id.">".$value->name."</option>";
        }
        $data1 = ProductCategory::where('branch_id',$request->edit_child_branch_id)->where('status',1)->get();
        $html1 = "<option value=''>Select</option>";
        foreach ($data1 as $key => $value) {
            $html1 .= "<option value=".$value->id.">".$value->name."</option>";
        }
        return response()->json(['data'=>$html, 'data1'=>$html1]);
    }
    public function interiorvisitexport(Request $request) 
    {
        $param = $request->all();
        return Excel::download(new InteriorVisitExport($param), 'interiorvisit.xlsx');
    }
    public function interiorvisitpdf(Request $request)
    {
        $param = $request->all();
        $data = Visits::get();
        $pdf = PDF::setOptions(['logOutputFile' => storage_path('logs/log.htm'), 'tempDir' => storage_path('logs/'), 'isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true])->loadView('report.interiorvisitpdf',$param)->setPaper('A4', 'landscape');
        return $pdf->download('interiorvisit.pdf');
    }
    public function getemaildata(Request $request)
    {
        $html = Arid::where('id',$request->arid)->value('email');
        $html1 = Arid::where('id',$request->arid)->value('number');
        $html2 = Arid::where('id',$request->arid)->value('firm_name');

        return response()->json(['data' => $html, 'data1' => $html1, 'data2' => $html2]);
    }
}