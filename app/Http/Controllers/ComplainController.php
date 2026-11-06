<?php

namespace App\Http\Controllers;
use App\User;
use DataTables;
use App\Complain;
use App\ProductCategory;
use App\Employee;
use Auth;
use Illuminate\Http\Request;


class ComplainController extends Controller
{
     public function __construct(Complain $s)
    {
        $this->view = 'complains';
        $this->route = 'complains';
        $this->viewName = 'Complain';
    }   

    public function index(Request $request)
    {
         if(Auth::guard('employee')->check())
        {
        if ($request->ajax()) {
            $query = Complain::where('employee_id',Auth::user()->id)->latest();

             if($request->status == 1)
            {
                // dd($request->status);
                $query = $query->where('status',1);
            }

            else if($request->status == 0)
            {
                 $query = $query->where('status',0);
            }
            elseif($request->status == 2) 
            {
                $query = $query;
            }
            else
            {
                $query = Complain::latest();
            }
            return Datatables::of($query)
            ->addColumn('action', function ($row) {
                // $btn = '<a href="'.route('user.edit', $row->id).'">Edit</a>&nbsp&nbsp&nbsp<a href="'.route('user.delete', $row->id).'">Delete</a>'; 
               
                   
                     $btn = view('layouts.actionbtnpermission')->with(['id'=>$row->id,'route'=>'complains','per_value'=>'complain'])->render();      
                          
               
                return $btn;
            })

             ->addColumn('singlecheckbox', function ($row) {

               
                   
                    $schk = view('layouts.singlecheckbox')->with(['id' => $row->id , 'status'=>$row->status])->render();
                    
                

                return $schk;

            })



            

            ->rawColumns(['numbers','action','singlecheckbox'])
            ->make(true);
        } 
    }
    else
    {
         if ($request->ajax()) {
            $query = Complain::latest();
             if(Auth::user()->role == 2)
            {
                $query = Complain::where('branch_id',Auth::user()->id)->latest();
                // dd(Auth::user());
            }

             if($request->status == 1)
            {
                // dd($request->status);
                $query = $query->where('status',1);
            }

            else if($request->status == 0)
            {
                 $query = $query->where('status',0);
            }
            elseif($request->status == 2) 
            {
                $query = $query;
            }
            else
            {
                $query = Complain::latest();
            }
            return Datatables::of($query)
            ->addColumn('action', function ($row) {
                // $btn = '<a href="'.route('user.edit', $row->id).'">Edit</a>&nbsp&nbsp&nbsp<a href="'.route('user.delete', $row->id).'">Delete</a>'; 
               
                   
                     $btn = view('layouts.actionbtnpermission')->with(['id'=>$row->id,'route'=>'complains','per_value'=>'complain'])->render();      
                          
               
                return $btn;
            })

             ->addColumn('singlecheckbox', function ($row) {

               
                   
                    $schk = view('layouts.singlecheckbox')->with(['id' => $row->id , 'status'=>$row->status])->render();
                    
                

                return $schk;

            })



            

            ->rawColumns(['numbers','action','singlecheckbox'])
            ->make(true);
        } 
    }

        return view('complains.index');
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
        $complain = new Complain();
         if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $complain->branch_id = $request->branch_id;
                $complain->employee_id = $request->employee_id;
            }
            else
            {
                $complain->branch_id = Auth::user()->id;
                $complain->employee_id = $request->employee_id;
            }
            
        }
        else
        {
            $complain->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
            $complain->employee_id = Auth::user()->id;
        }
        $complain->name = $request->name;
        $complain->mobile_no = $request->mobile_no;
        $complain->company = $request->company;
        $complain->address = $request->address;
        $complain->problem = $request->problem;
        $complain->reference_no = $request->reference_no;
        $complain->note = $request->note;
        if(isset($request['image']))
        {
            $imageName = time().'-'. $request->image->getClientOriginalName();
            $request->image->move(public_path('complain/'),$imageName);
            $complain->image = $imageName;
        }
        $complain->save();

        if($complain)
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
        $data['data'] = Complain::where('id', $id)->first();
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
    public function update(Request $request, $id)
    {
        $complain = Complain::where('id',$id)->first();
         if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $complain->branch_id = $request->branch_id;
                $complain->employee_id = $request->employee_id;
            }
            else
            {
                $complain->branch_id = Auth::user()->id;
                $complain->employee_id = $request->employee_id;
            }
            
        }
        else
        {
            $complain->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
            $complain->employee_id = Auth::user()->id;
        }
        $complain->name = $request->name;
        $complain->mobile_no = $request->mobile_no;
        $complain->company = $request->company;
        $complain->address = $request->address;
        $complain->problem = $request->problem;
        $complain->reference_no = $request->reference_no;
        $complain->note = $request->note;
        if(isset($request['image']))
        {
            $imageName = time().'-'. $request->image->getClientOriginalName();
            $request->image->move(public_path('complain/'),$imageName);
            $complain->image = $imageName;
        }
        $complain->save();

        if($complain)
        {
            return response()->json(['status' => 'success']);
        }else
        {
            return response()->json(['status' => 'error']);
        }
    }

    public function getemployee(Request $request)
    {
          $data = Employee::where('branch_id',$request->branch_id)->get();
        $html = "<option value=''>Select</option>";
        foreach ($data as $key => $value) {
            $html .= "<option value=".$value->id.">".$value->name."</option>";
        }

        echo $html;
    }
}
