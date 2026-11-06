<?php

namespace App\Http\Controllers;
use App\User;
use DataTables;
use App\Holiday;
use App\Group;
use App\Attendance;
use Illuminate\Http\Request;
use Auth;


class SalesController extends Controller
{
     public function __construct(User $s)
    {
        $this->view = 'sales';
        $this->route = 'sales';
        $this->viewName = 'User';
    }   

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = User::whereIn('role',[4,5])->latest();
            if(Auth::user()->role == 2)
            {
                $query = User::whereIn('role',[4,5])->where('branch_id',Auth::user()->id)->latest();
                // dd(Auth::user());
            }
            return Datatables::of($query)
            ->addColumn('action', function ($row) {
                // $btn = '<a href="'.route('user.edit', $row->id).'">Edit</a>&nbsp&nbsp&nbsp<a href="'.route('user.delete', $row->id).'">Delete</a>'; 
                if($row->id == 1)
                { 
                     $btn = 'Cannot Change'; 
                    
                }
                else
                {
                   
                     $btn = view('layouts.actionbtnpermission')->with(['id'=>$row->id,'route'=>'sales','per_value'=>'user'])->render();      
                          
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

        return view('sales.index');
    }

    public function create()
    {
        $data['title'] = 'Add ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['branch'] = User::where('role',2)->get();
        $data['group'] = Group::where('id','!=',1)->get();
        return view('general.add_form')->with($data);
    }

    public function store(Request $request)
    {
        $param = [];
        $param['group_id'] = 1;
       
        $param['branch_id'] = Auth::user()->id;
        $param['name'] = $request['name'];
        $param['email'] = $request['email'];
        $param['number'] = $request['number'];
        
        $param['role'] = $request['role'];
       
            if(Auth::user()->role == 1)
            {

             $param['branch_id'] = $request['branch_id'];
            }
            else
            {
                $param['branch_id'] = AUth::user()->id;
            }
        
            
       
        $param['first_name'] = $request['first_name'];
        $param['last_name'] = $request['last_name'];
         if(isset($request['image']))
        {
            $imageName = time().'-'. $request->image->getClientOriginalName();
            $request->image->move(public_path('user/'),$imageName);
            $param['image'] = $imageName;
        }
        $param['show_password'] = $request['password'];
        $param['password'] = bcrypt($request['password']);

       // dd($param);

        $user=User::create($param);
//     
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
         $data['branch'] = User::where('role',2)->get();
        $data['group'] = Group::where('id','!=',1)->get();
        return view('general.edit_form')->with($data);
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
       if(Auth::user()->role == 1)
            {

             $param['branch_id'] = $request['branch_id'];
            }
            else
            {
                $param['branch_id'] = AUth::user()->id;
            }
            $param['role'] = $request['role'];
       
         $param['first_name'] = $request['first_name'];
        $param['last_name'] = $request['last_name'];
         if(isset($request['image']))
        {
            $imageName = time().'-'. $request->image->getClientOriginalName();
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

    public function show()
    {}


}
