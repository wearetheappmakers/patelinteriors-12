<?php
namespace App\Http\Controllers;
use App\User;
use DataTables;
use App\Holiday;
use App\Group;
use App\Attendance;
use Illuminate\Http\Request;
class UserController extends Controller
{
    public function __construct(User $s)
    {
        $this->view = 'user';
        $this->route = 'user';
        $this->viewName = 'Branch';
    }   
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = User::whereIn('role',[1,2])->latest();
            return Datatables::of($query)
            ->addColumn('action', function ($row) {
                // $btn = '<a href="'.route('user.edit', $row->id).'">Edit</a>&nbsp&nbsp&nbsp<a href="'.route('user.delete', $row->id).'">Delete</a>'; 
                if($row->id == 1)
                { 
                    $btn = 'Cannot Change'; 
                }
                else
                {                   
                    // $btn = view('layouts.actionbtnpermission')->with(['id'=>$row->id,'route'=>'user','per_value'=>'user'])->render();
                    $btn = '<a style="background: transparent;" href="'.route('user.user.edit',$row->id).'" title="Edit details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: green;" class="la la-edit"></i>
                    </a>&nbsp;&nbsp;&nbsp;';

                    $btn .= '<a href="'.route('user.delete.user',$row->id).'" onclick="return confirm(`Are you sure?`)" style="background: white;" title="Delete details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: red;" class="la la-trash"></i></a>';
                    $btn .= '&nbsp;&nbsp;<a style="background: white;" href="'.route('user.user.show',$row->id).'"  title="View Details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-eye"></i></a>';

                    return $btn;
                }
                return $btn;
            })
            ->addColumn('singlecheckbox', function ($row) {
                if($row->id == 1)
                {
                    $schk = 'Cannot Change';
                }
                else
                {
                    $schk = view('layouts.singlecheckbox')->with(['id' => $row->id , 'status'=>$row->status])->render();
                }
                return $schk;
            })
            ->rawColumns(['numbers','action','singlecheckbox'])
            ->make(true);
        } 
        return view('user.index');
    }
    public function create()
    {
        $data['title'] = 'Add ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['group'] = Group::where('id','!=',1)->get();
        return view('general.add_form')->with($data);
    }
    public function store(Request $request)
    {
        $param = [];
        $param['group_id'] = 1;
        $param['name'] = $request['name'];
        $param['email'] = $request['email'];
        $param['number'] = $request['number'];
        $param['prefix'] = $request['prefix'];
        $param['role'] = 2;
        $param['first_name'] = $request['first_name'];
        $param['last_name'] = $request['last_name'];
        if(isset($request['image']))
        {
            $imageName = time().'.'. $request->image->getClientOriginalExtension();
            $request->image->move(public_path('user/'),$imageName);
            $param['image'] = $imageName;
        }
        $param['show_password'] = $request['password'];
        $param['password'] = bcrypt($request['password']);
        $user=User::create($param);  
        if($user)
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
        $data['data'] = User::where('id', $id)->first();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['group'] = Group::where('id','!=',1)->get();
        return view('general.edit_form')->with($data);
    }
    public function show($id)
    {
        $data['title'] = 'View ' . $this->viewName;
        $data['data'] = User::where('id', $id)->first();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['group'] = Group::where('id','!=',1)->get();
        return view('user.show')->with($data);
    }
    public function update(Request $request, $id)
    {
        // $data = $request->all();
        // unset($data['_token'],$data['_method']);
        // if($request->password)
        // {
        //     $data['show_password'] = $request->password;
        //     $data['password'] = bcrypt($request->password);
        // }else{
        //     unset($data['password']);
        // }
        // $data = User::where('id', $id)->update($data);
        $param = User::where('id',$id)->first();
        $param['group_id'] = 1;
        $param['name'] = $request['name'];
        $param['email'] = $request['email'];
        $param['number'] = $request['number'];
        $param['prefix'] = $request['prefix'];
        $param['role'] = 2;
        $param['first_name'] = $request['first_name'];
        $param['last_name'] = $request['last_name'];
        if(isset($request['image']))
        {
            $imageName = time().'.'. $request->image->getClientOriginalExtension();
            $request->image->move(public_path('user/'),$imageName);
            $param['image'] = $imageName;
        }
        $param['show_password'] = $request['password'];
        $param['password'] = bcrypt($request['password']);
        $param->save();
        if($param)
        {
            return response()->json(['status' => 'success']);
        }else
        {
            return response()->json(['status' => 'error']);
        }
    }
    public function delete($id)
    {
        $data = User::where('id',$id)->delete();
        return back();
    }
}