<?php
namespace App\Http\Controllers;
use App\User;
use DataTables;
use App\LeadStage;
use Auth;
use App\Employee;
use App\ProductCategory;
use Illuminate\Http\Request;
class LeadStageController extends Controller
{
    public function __construct(LeadStage $s)
    {
        $this->view = 'leadstage';
        $this->route = 'leadstage';
        $this->viewName = 'leadstage';
    }   
    public function index(Request $request)
    {
        if ($request->ajax()) {
            if(Auth::guard('employee')->check())
            {
                $query = LeadStage::where('branch_id',Auth::user()->branch_id);
            }
            else
            {
                if(Auth::user()->role == 2)
                {
                    $query = LeadStage::where('branch_id',Auth::user()->id);
                }
                else
                {
                    $query = LeadStage::orderby('id','DESC');
                }
            }
    
            // if($request->status == 1)
            // {
            //     $query = $query->where('status',1);
            // }
            // else if($request->status == 0)
            // {
            //     $query = $query->where('status',0);
            // }
            // elseif($request->status == 2) 
            // {
            //     $query = $query;
            // }
            // else
            // {
            //     $query = LeadStage::latest();
            // }
            return Datatables::of($query)
            ->addColumn('action', function ($row) {
                $btn = '<a style="background: transparent;" href="'.route('user.leadstage.edit',$row->id).'" title="Edit details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: green;" class="la la-edit"></i>
                </a>&nbsp;&nbsp;&nbsp;';

                $btn .= '<a style="background: white;" href="'.route('user.leadstage.show',$row->id).'"  title="View Details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-eye"></i></a>&nbsp;&nbsp;';

                if(Auth::user()->role == 1)
                {
                    $btn .= '<a href="'.route('user.deletes.clones',$row->id).'" onclick="return confirm(`Are you sure?`)" style="background: white;" title="Delete details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: red;" class="la la-trash"></i></a>';
                    
                }
                return $btn;
            })
            ->addColumn('singlecheckbox', function ($row) {
                $schk = view('layouts.singlecheckbox')->with(['id' => $row->id , 'status'=>$row->status])->render();
                return $schk;
            })
            ->addColumn('color', function ($row) {
                $color = $row->color;
                $btn = '';
                $btn .= '<input type="color" name="color" value='.$color.'>'; 
                return $btn;
            })
            ->rawColumns(['numbers','action','singlecheckbox','color'])
            ->make(true);
        } 
        return view('leadstage.index');
    }
    public function create()
    {
        $data['title'] = 'Add ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        if(Auth::user()->role == 1)
        {
            $data['employee'] = Employee::get();
        }
        else
        {
            $data['employee'] = Employee::where('branch_id',Auth::user()->id)->get();
        }
        return view('general.add_form')->with($data);
    }
    public function store(Request $request)
    {
        $leadstage = new LeadStage();
        $leadstage->name = $request->name;
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $leadstage->branch_id = $request->branch_id;
                $leadstage->employee_id = $request->employee_id;
            }
            else
            {
                $leadstage->branch_id = Auth::user()->id;
                $leadstage->employee_id = $request->employee_id;
            }
        }
        else
        {
            $leadstage->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
            $leadstage->employee_id = Auth::user()->id;
        }
        $leadstage->color = $request->color;
        $leadstage->is_completed = $request->is_completed;
        $leadstage->is_canceled = $request->is_canceled;
        $leadstage->save();
        if($leadstage)
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
        $data['data'] = LeadStage::where('id', $id)->first();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        if(Auth::user()->role == 1)
        {
            $data['employee'] = Employee::get();
        }
        else
        {
            $data['employee'] = Employee::where('branch_id',Auth::user()->id)->get();
        }
        return view('general.edit_form')->with($data);
    }
    public function show($id)
    {
        $data['title'] = 'View ' . $this->viewName;
        $data['data'] = LeadStage::where('id', $id)->first();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        if(Auth::user()->role == 1)
        {
            $data['employee'] = Employee::get();
        }
        else
        {
            $data['employee'] = Employee::where('branch_id',Auth::user()->id)->get();
        }
        $data['branchname'] = User::where('id',$data['data']->branch_id)->value('name');
        return view('leadstage.show')->with($data);
    }
    public function update(Request $request, $id)
    {
        $leadstage = LeadStage::where('id',$id)->first();
        $leadstage->name = $request->name;
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $leadstage->branch_id = $request->branch_id;
                $leadstage->employee_id = $request->employee_id;
            }
            else
            {
                $leadstage->branch_id = Auth::user()->id;
                $leadstage->employee_id = $request->employee_id;
            }
        }
        else
        {
            $leadstage->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
            $leadstage->employee_id = Auth::user()->id;
        }
        $leadstage->color = $request->color;
        $leadstage->is_completed = $request->is_completed;
        $leadstage->is_canceled = $request->is_canceled;
        $leadstage->save();
        if($leadstage)
        {
            return response()->json(['status' => 'success']);
        }else
        {
            return response()->json(['status' => 'error']);
        }
    }
    public function deleteclone($id)
    {
        $data = LeadStage::where('id',$id)->delete();
        return back();
    }
}
