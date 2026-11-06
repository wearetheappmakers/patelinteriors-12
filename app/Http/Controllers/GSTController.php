<?php
namespace App\Http\Controllers;
use App\GST;
use DataTables;
use App\Section;
use Auth;
use App\User;
use App\Employee;
use App\ProductCategory;
use Illuminate\Http\Request;
class GSTController extends Controller
{
    public function __construct(GST $s)
    {
        $this->view = 'gst';
        $this->route = 'gst';
        $this->viewName = 'GST';
    }   
    public function index(Request $request)
    {
        if ($request->ajax()) {
            if(Auth::guard('employee')->check())
            {
                $query = GST::where('branch_id',Auth::user()->branch_id);
            }
            else
            {
                if(Auth::user()->role == 2)
                {
                    $query = GST::where('branch_id',Auth::user()->id);
                }
                else
                {
                    $query = GST::orderby('id','DESC');
                }
            }
            return Datatables::of($query)
            ->addColumn('action', function ($row) {
                $btn = '<a style="background: transparent;" href="'.route('user.gst.edit',$row->id).'" title="Edit details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: green;" class="la la-edit"></i>
                </a>&nbsp;&nbsp;&nbsp;';
                $btn .= '<a style="background: white;" href="'.route('user.gst.show',$row->id).'"  title="View Details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-eye"></i></a>&nbsp;&nbsp;';
                if(Auth::user()->role == 1)
                {

                    $btn .= '<a href="'.route('user.deletesgst.clones',$row->id).'" onclick="return confirm(`Are you sure?`)" style="background: white;" title="Delete details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: red;" class="la la-trash"></i></a>';
                }
                return $btn;
            })
            ->addColumn('singlecheckbox', function ($row) {
                $schk = view('layouts.singlecheckbox')->with(['id' => $row->id , 'status'=>$row->status])->render();
                return $schk;
            })
            ->rawColumns(['numbers','action','singlecheckbox'])
            ->make(true);
        } 
        return view('gst.index');
    }
    public function create()
    {
        $data['title'] = 'Add ' . $this->viewName;
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
        return view('general.add_form')->with($data);
    }
    public function store(Request $request)
    {
        $gst = new GST();
        $gst->gst = $request->gst;
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $gst->branch_id = $request->branch_id;
                $gst->employee_id = $request->employee_id;
            }
            else
            {
                $gst->branch_id = Auth::user()->id;
                $gst->employee_id = $request->employee_id;
            }
        }
        else
        {
            $gst->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
            $gst->employee_id = Auth::user()->id;
        }
        $gst->save();
        if($gst)
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
        $data['data'] = GST::where('id', $id)->first();
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
        return view('general.edit_form')->with($data);
    }

    public function show($id)
    {
        $data['title'] = 'View ' . $this->viewName;
        $data['data'] = GST::where('id', $id)->first();
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
        return view('gst.show')->with($data);
    }
    public function update(Request $request, $id)
    {
        $gst = GST::where('id',$id)->first();
        $gst->gst = $request->gst;
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $gst->branch_id = $request->branch_id;
                $gst->employee_id = $request->employee_id;
            }
            else
            {
                $gst->branch_id = Auth::user()->id;
                $gst->employee_id = $request->employee_id;
            }
        }
        else
        {
            $gst->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
            $gst->employee_id = Auth::user()->id;
        }
        $gst->save();
        if($gst)
        {
            return response()->json(['status' => 'success']);
        }else
        {
            return response()->json(['status' => 'error']);
        }
    }
    public function deletegstclone($id)
    {
        $data = GST::where('id',$id)->delete();
        return back();
    }
}
