<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\SourceName;
use App\Employee;
use App\LeadSource;
use DataTables;
use App\User;
use Auth;
class SourceNameController extends Controller
{
    public function __construct(SourceName $s)
    {
        $this->view = 'sourcename';
        $this->route = 'sourcename';
        $this->viewName = 'Source Name';
    }
    public function index(Request $request)
    {
        if ($request->ajax())
        {
            $query = SourceName::orderby('id','DESC')->latest();
            if(Auth::guard('employee')->check())
            {
               $query = SourceName::where('branch_id',Auth::user()->branch_id)->latest();
            }
            else
            {
                if(Auth::user()->role == 2)
                {
                    $query = SourceName::where('branch_id',Auth::user()->id)->latest();
                }else{
                    $query = SourceName::orderby('id','DESC')->latest();
                }
            }
            return Datatables::of($query)
            ->addColumn('action', function ($row) {
                $btn = '<a style="background: transparent;" href="'.route('user.sourcename.edit',$row->id).'" title="Edit details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: green;" class="la la-edit"></i>
                </a>&nbsp;&nbsp;&nbsp;';
                $btn .= '<a style="background: white;" href="'.route('user.sourcename.show',$row->id).'"  title="View Details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-eye"></i></a>&nbsp;&nbsp;';
                if(Auth::user()->role == 1)
                {
                    $btn .= '<a href="'.route('user.deletesourcename',$row->id).'" onclick="return confirm(`Are you sure?`)" style="background: white;" title="Delete details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: red;" class="la la-trash"></i></a>';
                }
                return $btn;
            })
            ->addColumn('singlecheckbox', function ($row) {
                $schk = view('layouts.singlecheckbox')->with(['id' => $row->id , 'status'=>$row->status])->render();
                return $schk;
            })
            ->editColumn('lead_sourceid', function ($row) {
                $schk = LeadSource::where('id',$row->lead_sourceid)->value('name');
                return $schk;
            })
            ->rawColumns(['action','singlecheckbox'])
            ->make(true);
        }
        return view('sourcename.index');
    }
    public function create()
    {
        $data['title'] = 'Add ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        return view('general.add_form')->with($data);
    }
    public function store(Request $request)
    {
        $sname = new SourceName();
        $sname->name = $request->name;
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $sname->branch_id = $request->branch_id;
            }
            else
            {
                $sname->branch_id = Auth::user()->id;
            }
        }
        else
        {
            $sname->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
        }
        $sname->lead_sourceid = $request->lead_sourceid;
        $sname->address = $request->address;
        $sname->phoneno = $request->phoneno;
        $sname->email = $request->email;
        $sname->birth_date = $request->birth_date;
        $sname->anniversary_date = $request->anniversary_date;
        $sname->save();
        if ($sname){
            return response()->json(['status'=>'success']);
        }else{
            return response()->json(['status'=>'error']);
        }
    }
    public function show($id)
    {
        $data['title'] = 'View ' . $this->viewName;
        $data['data'] = SourceName::where('id', $id)->first();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        return view('sourcename.show')->with($data);
    }
    public function edit($id)
    {
        $data['title'] = 'Edit ' . $this->viewName;
        $data['data'] = SourceName::where('id', $id)->first();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        return view('general.edit_form')->with($data);
    }
    public function update(Request $request, $id)
    {
        $sname = SourceName::where('id', $id)->first();
        $sname->name = $request->name;
        $sname->lead_sourceid = $request->lead_sourceid;
        $sname->address = $request->address;
        $sname->phoneno = $request->phoneno;
        $sname->email = $request->email;
        $sname->birth_date = $request->birth_date;
        $sname->anniversary_date = $request->anniversary_date;
        $sname->save();
        if ($sname){
            return response()->json(['status'=>'success']);
        }else{
            return response()->json(['status'=>'error']);
        }
    }
    public function deletesourcename($id)
    {
        $data = SourceName::where('id',$id)->delete();
        return back();
    }
    public function getleadsource(Request $request)
    {
        $html = '<option value="">Select</option>';
        $data = LeadSource::where('branch_id',$request->branch_id)->get();
        if(count($data))
        {
            foreach ($data as $key => $value)
            {
                $html .= '<option value="'.$value->id.'">'.$value->name.'</option>';
            }
        }
        return response()->json(['status' => 'success', 'data' => $html ]);
    }
}