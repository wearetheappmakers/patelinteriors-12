<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\SourceName;
use App\Employee;
use App\Symbol;
use DataTables;
use App\User;
use Auth;
class SymbolController extends Controller
{
    public function __construct(Symbol $s)
    {
        $this->view = 'symbol';
        $this->route = 'symbol';
        $this->viewName = 'Symbol';
    }
    public function index(Request $request)
    {
        if ($request->ajax())
        {
            $query = Symbol::orderby('id','DESC');
            if(Auth::guard('employee')->check())
            {
               $query = Symbol::where('branch_id',Auth::user()->branch_id);
            }
            else
            {
                if(Auth::user()->role == 2)
                {
                    $query = Symbol::where('branch_id',Auth::user()->id);
                }
                else
                {
                    $query = Symbol::orderby('id','DESC');
                }
            }

            if($request->branch_id)
            {
                $query->where('branch_id',$request->branch_id);
            }
            $query = $query->orderBy('id','DESC')->latest();

            return Datatables::of($query)
            ->addColumn('action', function ($row) {
                $btn = '<a style="background: transparent;" href="'.route('user.symbol.edit',$row->id).'" title="Edit details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: green;" class="la la-edit"></i>
                </a>&nbsp;&nbsp;&nbsp;';
                $btn .= '<a style="background: white;" href="'.route('user.symbol.show',$row->id).'"  title="View Details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-eye"></i></a>&nbsp;&nbsp;';
                if(Auth::user()->role == 1)
                {

                    $btn .= '<a href="'.route('user.symbol.delete',$row->id).'" onclick="return confirm(`Are you sure?`)" style="background: white;" title="Delete details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: red;" class="la la-trash"></i></a>';
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
        return view('symbol.index');
    }
    public function create()
    {
        $data['title'] = 'Add ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        $data['source'] = Symbol::where('status',1)->get();
        return view('general.add_form')->with($data);
    }
    public function store(Request $request)
    {
        $symbol = new Symbol();
        $symbol->name = $request->name;
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $symbol->branch_id = $request->branch_id;
            }
            else
            {
                $symbol->branch_id = Auth::user()->id;
            }
        }
        else
        {
            $symbol->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
        }
        if($request['image'])
        {
            $imageName = time().'.'. $request->image->getClientOriginalExtension();
            $request->image->move(public_path('symbol/'),$imageName);
            $symbol['image'] = $imageName;
        }

        $symbol->save();
        if ($symbol){
            return response()->json(['status'=>'success']);
        }else{
            return response()->json(['status'=>'error']);
        }
    }
    public function show($id)
    {
        $data['title'] = 'View ' . $this->viewName;
        $data['data'] = Symbol::where('id', $id)->first();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        // $data['source'] = LeadSource::where('status',1)->get();
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        return view('symbol.show')->with($data);
    }
    public function edit($id)
    {
        $data['title'] = 'Edit ' . $this->viewName;
        $data['data'] = Symbol::where('id', $id)->first();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        // $data['source'] = LeadSource::where('status',1)->get();
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        return view('general.edit_form')->with($data);
    }
    public function update(Request $request, $id)
    {
        $symbol = Symbol::where('id', $id)->first();
        $symbol->name = $request->name;
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $symbol->branch_id = $request->branch_id;
            }
            else
            {
                $symbol->branch_id = Auth::user()->id;
            }
        }
        else
        {
            $symbol->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
        }
        if($request['image'])
        {
            $imageName = time().'.'. $request->image->getClientOriginalExtension();
            $request->image->move(public_path('symbol/'),$imageName);
            $symbol['image'] = $imageName;
        }

        $symbol->save();
        if ($symbol){
            return response()->json(['status'=>'success']);
        }else{
            return response()->json(['status'=>'error']);
        }
    }
    public function delete($id)
    {
        $data = Symbol::where('id',$id)->delete();
        return back();
    }
}