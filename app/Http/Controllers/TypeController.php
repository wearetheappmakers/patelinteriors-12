<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Type;
use App\User;
use DataTables;
use Auth;

class TypeController extends Controller
{

    public function __construct(Type $s)
    {
        $this->view = 'type';
        $this->route = 'type';
        $this->viewName = 'Followup Type';
    }

    public function index(Request $request)
    {
        if ($request->ajax())
        {
            if(Auth::guard('employee')->check())
            {
                $query = Type::where('branch_id',Auth::user()->branch_id);
            }
            else
            {
                if(Auth::user()->role == 2)
                {
                    $query = Type::where('branch_id',Auth::user()->id);
                }
                else
                {
                    $query = Type::orderby('id','DESC');
                }
            }
            return Datatables::of($query)
            ->addColumn('action', function ($row) {
                $btn = '<a style="background: transparent;" href="'.route('user.type.edit',$row->id).'" title="Edit details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: green;" class="la la-edit"></i>
                </a>&nbsp;&nbsp;&nbsp;';

                $btn .= '<a style="background: white;" href="'.route('user.type.show',$row->id).'"  title="View Details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-eye"></i></a>&nbsp;&nbsp;';

                if(Auth::user()->role == 1)
                {

                    $btn .= '<a href="'.route('user.deletestype.clones',$row->id).'" onclick="return confirm(`Are you sure?`)" style="background: white;" title="Delete details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: red;" class="la la-trash"></i></a>';
                }
                return $btn;
            })


            ->addColumn('singlecheckbox', function ($row) {
                $schk = view('layouts.singlecheckbox')->with(['id' => $row->id , 'status'=>$row->status])->render();
                return $schk;
            })

            ->rawColumns(['action','singlecheckbox'])
            ->make(true);
        }
        return view('type.index');
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
        $types = new Type();
        $types->name = $request->name;
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $types->branch_id = $request->branch_id;
            }
            else
            {
                $types->branch_id = Auth::user()->id;
            }
        }
        else
        {
            $types->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
        }
        $types->save();

        if ($types){
            return response()->json(['status'=>'success']);
        }else{
            return response()->json(['status'=>'error']);
        }
    }

    public function show($id)
    {
       $data['title'] = 'Edit ' . $this->viewName;
       $data['data'] = Type::where('id', $id)->first();
       $data['module'] = $this->viewName;
       $data['resourcePath'] = $this->view;
       $data['branch'] = User::where('role',2)->where('status',1)->get();
       return view('type.show')->with($data);
   }

   public function edit($id)
   {
    $data['title'] = 'Edit ' . $this->viewName;
    $data['data'] = Type::where('id', $id)->first();
    $data['module'] = $this->viewName;
    $data['resourcePath'] = $this->view;
    $data['branch'] = User::where('role',2)->where('status',1)->get();
    return view('general.edit_form')->with($data);

}

public function update(Request $request, $id)
{
    $types = Type::where('id',$id)->first();
    $types->name = $request->name;
    if(Auth::guard('user')->check())
    {
        if(Auth::user()->role == 1)
        {
            $types->branch_id = $request->branch_id;
        }
        else
        {
            $types->branch_id = Auth::user()->id;
        }
    }
    else
    {
        $types->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
    }
    $types->save();
    if ($types){
        return response()->json(['status'=>'success']);
    }else{
        return response()->json(['status'=>'error']);
    }
}

public function deletetypeclone($id)
{
    $data = Type::where('id',$id)->delete();
    return back();
}
}
