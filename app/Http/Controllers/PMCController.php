<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\SourceName;
use App\LeadSource;
use App\Employee;
use App\PMC;
use DataTables;
use App\User;
use Auth;
class PMCController extends Controller
{
    public function __construct(PMC $s)
    {
        $this->view = 'pmc';
        $this->route = 'pmc';
        $this->viewName = 'PMC';
    }
    public function index(Request $request)
    {
        if ($request->ajax())
        {
            $query = PMC::orderby('id','DESC');
            if(Auth::guard('employee')->check())
            {
               $query = PMC::where('branch_id',Auth::user()->branch_id);
            }
            else
            {
                if(Auth::user()->role == 2)
                {
                    $query = PMC::where('branch_id',Auth::user()->id);
                }
                else
                {
                    $query = PMC::orderby('id','DESC');
                }
            }
            if($request->branch_id)
            {
                $query->where('branch_id',$request->branch_id);
            }
            $query = $query->orderBy('id','DESC')->latest();
            return Datatables::of($query)
            ->addColumn('action', function ($row) {
                $btn = '<a style="background: transparent;" href="'.route('user.pmc.edit',$row->id).'" title="Edit details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: green;" class="la la-edit"></i>
                </a>&nbsp;&nbsp;&nbsp;';
                $btn .= '<a style="background: white;" href="'.route('user.pmc.show',$row->id).'"  title="View Details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-eye"></i></a>&nbsp;&nbsp;';
                if(Auth::user()->role == 1)
                {
                    $btn .= '<a href="'.route('user.pmc.delete',$row->id).'" onclick="return confirm(`Are you sure?`)" style="background: white;" title="Delete details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: red;" class="la la-trash"></i></a>';
                }
                return $btn;
            })
            ->addColumn('singlecheckbox', function ($row) {
                $schk = view('layouts.singlecheckbox')->with(['id' => $row->id , 'status'=>$row->status])->render();
                return $schk;
            })
            ->addColumn('branch_id', function ($row) {
                if($row->branch_id)
                {
                    $schk = User::where('id',$row->branch_id)->value('name');
                }else
                {
                    $schk = '-';
                }
                return $schk;
            })
            ->rawColumns(['action','singlecheckbox','branch_id'])   
            ->make(true);
        }
        return view('pmc.index');
    }
    public function create()
    {
        $data['title'] = 'Add ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        $data['source'] = LeadSource::where('status',1)->get();
        return view('general.add_form')->with($data);
    }
    public function store(Request $request)
    {
        $pmc = new PMC();
        $pmc->name = $request->name;
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $pmc->branch_id = $request->branch_id;
            }
            else
            {
                $pmc->branch_id = Auth::user()->id;
            }
        }
        else
        {
            $pmc->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
        }
        $pmc->number = $request->number;
        $pmc->email = $request->email;
        $pmc->contact_person = $request->contact_person;
        $pmc->save();
        if ($pmc){
            return response()->json(['status'=>'success']);
        }else{
            return response()->json(['status'=>'error']);
        }
    }
    public function show($id)
    {
        $data['title'] = 'View' . $this->viewName;
        $data['data'] = PMC::where('id', $id)->first();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        // $data['source'] = LeadSource::where('status',1)->get();
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        return view('pmc.show')->with($data);
    }
    public function edit($id)
    {
        $data['title'] = 'Edit ' . $this->viewName;
        $data['data'] = PMC::where('id', $id)->first();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        // $data['source'] = LeadSource::where('status',1)->get();
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        return view('general.edit_form')->with($data);
    }
    public function update(Request $request, $id)
    {
        $pmc = PMC::where('id', $id)->first();
        $pmc->name = $request->name;
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $pmc->branch_id = $request->branch_id;
            }
            else
            {
                $pmc->branch_id = Auth::user()->id;
            }
        }
        else
        {
            $pmc->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
        }
        $pmc->number = $request->number;
        $pmc->email = $request->email;
        $pmc->contact_person = $request->contact_person;
        $pmc->save();
        if ($pmc){
            return response()->json(['status'=>'success']);
        }else{
            return response()->json(['status'=>'error']);
        }
    }
    public function deletesourcename($id)
    {
        $data = PMC::where('id',$id)->delete();
        return back();
    }
}