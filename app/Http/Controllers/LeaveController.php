<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Leave;
use App\User;
use Carbon\Carbon;
use App\Attendance;
use App\Employee;
use DataTables;
use Mail;
use Auth;


class LeaveController extends Controller
{

    public function __construct(Leave $s)
    {
        $this->view = 'leave';
        $this->route = 'leave';
        $this->viewName = 'Leave';

    }

     
      function sendnotification($pushtoken,$title,$body,$urls,$leads_id,$name)
      {
        $firebaseToken = [$pushtoken];
                // dd($firebaseToken);
        $SERVER_API_KEY = env('Employee_server');

        $data = [
         "registration_ids" => $firebaseToken,
         "notification" => [
          "title" => $title,
          "body" => $body,
        ],
        "data" => [
          'urls' => $urls,
          'leads_id' => $leads_id,
          'name' => $name,
        ]
      ];
      $dataString = json_encode($data);

      $headers = [
       'Authorization: key=' . $SERVER_API_KEY,
       'Content-Type: application/json',
      ];
                // dd($headers);

      $ch = curl_init();

      curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

      $response = curl_exec($ch);
                // dd($response);
      }

        function adminsendnotification($pushtoken,$title,$body,$urls,$leads_id,$name)
        {
            $firebaseToken = [$pushtoken];
              // dd($firebaseToken);
            $SERVER_API_KEY = env('Admin_server');

            $data = [
             "registration_ids" => $firebaseToken,
             "notification" => [
              "title" => $title,
              "body" => $body,
            ],
            "data" => [
              'urls' => $urls,
              'leads_id' => $leads_id,
              'name' => $name,
            ]
          ];
          $dataString = json_encode($data);

          $headers = [
           'Authorization: key=' . $SERVER_API_KEY,
           'Content-Type: application/json',
         ];
              // dd($headers);

         $ch = curl_init();

         curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
         curl_setopt($ch, CURLOPT_POST, true);
         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
         curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

         $response = curl_exec($ch);
              // dd($response);
     }   

    public function index(Request $request)
    {
        if(Auth::guard('user')->check())
        {
            if ($request->ajax())
            {
                $query = Leave::with('employee','branch')->orderBy('id','DESC');
                if(Auth::user()->role == 2){
                    $query = Leave::with('employee','branch')->where('branch_id',Auth::user()->id)->orderBy('id','DESC');
                }

                if($request->branch_id)
                {
                 $query = $query->where('branch_id',$request->branch_id);
             }

             if($request->employee_id)
             {
                 $query = $query->where('employee_id',$request->employee_id);
             }
             return Datatables::of($query)
             ->addColumn('from_date', function($row) {
                return $row->from_date ? Carbon::parse($row->from_date)->format('d-m-Y') : '';
            })
            ->addColumn('to_date', function($row) {
                return $row->to_date ? Carbon::parse($row->to_date)->format('d-m-Y') : '';
            })
             ->addColumn('action', function ($row) {         
                $btn = '<a style="background: white;" href="'.route('user.leave.show', $row->id).'" title="view" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-eye"></i></a>';
               
                 $btn .= '<a href="'.route('user.leave.edit',$row->id).'" class="btn btn-sm btn-clean">
                                <i class="la la-edit" style="color:green"></i>
                            </a>';
                
           $btn .= '<form action="'.route('user.leave.destroy',$row->id).'"
                                method="POST" style="display:inline;">
                                '.csrf_field().method_field('DELETE').'
                                <button class="btn btn-sm btn-clean" onclick="return confirm(\'Delete entry?\')">
                                    <i class="la la-trash" style="color:red"></i>
                                </button>
                            </form>';
                return $btn;
            })
           

             ->addColumn('date', function ($row) {
                $ac = date('d-m-Y',strtotime($row->date));
                return $ac;

            })

             ->addColumn('date', function ($row) {
                $ac = date('d-m-Y',strtotime($row->from_date));
                return $ac;
            })

             ->addColumn('date1', function ($row) {
                $ac = date('d-m-Y',strtotime($row->to_date));
                return $ac;
            })

             
             ->addColumn('status', function ($row) {
                if($row->status == 2 || $row->status == 1)
                {

                    $btn = '<select class="form-control" disabled><option value="1"';

                }
                else
                {
                    $from_date = $row->from_date;
                    $to_date = $row->to_date;

                    $btn = '<input type="hidden" id="from_date_'.$row->id.'" value="'.$row->from_date.'"><input type="hidden" id="to_date_'.$row->id.'" value="'.$row->to_date.'"><select class="form-control" onchange="status(this,'.$from_date.','.$to_date.','.$row->id.')"><option value="0"';
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
                $btn .= $selected.'>Approved</option> <option value="2"';

                $selected = '';
                if($row->status == 2)
                {
                    $selected = 'selected';
                }
                $btn .= $selected.'>Reject</option></select>';


                return $btn;

            })
             ->rawColumns(['action','date','date1','status','approvedate','approvetodate'])
             ->make(true);
         }
     }

     if(Auth::guard('employee')->check())
     {
        if ($request->ajax())
        {
            // $query = Leave::with('employee','branch')->where('employee_id',Auth::user()->id)->orderBy('id','DESC');
            if(Auth::user()->role == 9) {
                $query = Leave::with('employee','branch')
                    ->where('branch_id', Auth::user()->branch_id)
                    ->orderBy('id','DESC');
            } else {
                $query = Leave::with('employee','branch')
                    ->where('employee_id', Auth::user()->id)
                    ->orderBy('id','DESC');
            }
     
          if($request->branch_id)
          {
              $query = $query->where('branch_id', $request->branch_id);
          }
  
          if($request->employee_id)
          {
              $query = $query->where('employee_id', $request->employee_id);
          }
            return Datatables::of($query)

            ->addColumn('from_date', function($row) {
                return $row->from_date ? Carbon::parse($row->from_date)->format('d-m-Y') : '';
            })
            ->addColumn('to_date', function($row) {
                return $row->to_date ? Carbon::parse($row->to_date)->format('d-m-Y') : '';
            })
            ->addColumn('action', function ($row) {

                $btn = '<a href="'.route('user.leave.show',$row->id).'" class="btn btn-sm btn-clean">
                            <i class="flaticon-eye" style="color:black"></i>
                        </a>';

                
                    $btn .= '<a href="'.route('user.leave.edit',$row->id).'" class="btn btn-sm btn-clean">
                                <i class="la la-edit" style="color:green"></i>
                            </a>';
                
                    $btn .= '<form action="'.route('user.leave.destroy',$row->id).'"
                                method="POST" style="display:inline;">
                                '.csrf_field().method_field('DELETE').'
                                <button class="btn btn-sm btn-clean" onclick="return confirm(\'Delete entry?\')">
                                    <i class="la la-trash" style="color:red"></i>
                                </button>
                            </form>';
                
                

                return $btn;
            })
            
            ->addColumn('employee_name', function ($row) {
                return $row->employee ? $row->employee->name : '-';
            })
            

            ->addColumn('date', function ($row) {
                $ac = date('d-m-Y',strtotime($row->date));
                return $ac;

            })

            ->addColumn('date', function ($row) {
                $ac = date('d-m-Y',strtotime($row->from_date));
                return $ac;
            })

            ->addColumn('date1', function ($row) {
                $ac = date('d-m-Y',strtotime($row->to_date));
                return $ac;
            })

           
            ->addColumn('status', function ($row) {

             
                if (Auth::user()->role == 9) {
            
                    $from_date = $row->from_date;
                    $to_date   = $row->to_date;
            
                    $btn = '<input type="hidden" id="from_date_'.$row->id.'" value="'.$row->from_date.'">
                            <input type="hidden" id="to_date_'.$row->id.'" value="'.$row->to_date.'">
            
                            <select class="form-control" onchange="status(this,'.$from_date.','.$to_date.','.$row->id.')">
                                <option value="0" '.($row->status == 0 ? 'selected' : '').'>Pending</option>
                                <option value="1" '.($row->status == 1 ? 'selected' : '').'>Approved</option>
                                <option value="2" '.($row->status == 2 ? 'selected' : '').'>Reject</option>
                            </select>';
            
                    return $btn;
                }
                $btn = '<select class="form-control" disabled>
                            <option value="0" '.($row->status == 0 ? 'selected' : '').'>Pending</option>
                            <option value="1" '.($row->status == 1 ? 'selected' : '').'>Approved</option>
                            <option value="2" '.($row->status == 2 ? 'selected' : '').'>Reject</option>
                        </select>';
            
                return $btn;
            })
            
            ->rawColumns(['action','date','date1','status'])
            ->make(true);
        }
    }

    $data['branch'] = User::where('role',2)->where('status',1)->get();



    if(Auth::guard('user')->check())
    {
        if(Auth::user()->role == 1)
        {
            $data['employee'] = Employee::where('status',1)->get();
        }
        else
        {
           $data['employee'] = Employee::where('branch_id',Auth::user()->id)->where('status',1)->get();
       }

    }
    if(Auth::guard('employee')->check())
    {
        if(Auth::user()->role == 9) {
            $data['employee'] = Employee::where('branch_id', Auth::user()->branch_id)
                                        ->where('status',1)
                                        ->get();
        } else {
            $data['employee'] = Employee::where('id', Auth::user()->id)
                                        ->where('status',1)
                                        ->get();
        }
    }


   return view('leave.index')->with($data);
}

    public function status(Request $request)
    {
        $leave = Leave::find($request->id);

        if (!$leave) {
            return response()->json(['status' => 'error', 'message' => 'Leave not found']);
        }

        $leave->status = $request->status;
        $leave->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Status updated successfully'
        ]);
    }




    public function create()
    {
        $data['title'] = 'Add ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        
        // HR (employee guard, role = 9)
        if (Auth::guard('employee')->check() && Auth::guard('employee')->user()->role == 9) {
            $branchId = Auth::guard('employee')->user()->branch_id;
            $data['employees'] = Employee::where('branch_id', $branchId)->get();
        }

        // Branch user (user guard, role = 2)
        if (Auth::guard('user')->check() && Auth::guard('user')->user()->role == 2) {
            $branchId = Auth::user()->id;
            $data['employees'] = Employee::where('branch_id', $branchId)->get();
        }

        // Admin (role = 1)
        if (Auth::guard('user')->check() && Auth::guard('user')->user()->role == 1) {
            $data['employees'] = Employee::all();
        }
        return view('general.add_form')->with($data);
    }

    public function store(Request $request)
    {
        $leave = new Leave();

        if (Auth::guard('employee')->check() && Auth::guard('employee')->user()->role != 9) {

            $leave->employee_id = Auth::guard('employee')->user()->id;
            $leave->branch_id = Auth::guard('employee')->user()->branch_id;
        }

       
        elseif (Auth::guard('employee')->check() && Auth::guard('employee')->user()->role == 9) {

            $leave->employee_id = $request->employee_id; 
            $leave->branch_id   = Auth::guard('employee')->user()->branch_id;
        }

        
        else {
            if (Auth::user()->role == 1) { 
                $leave->branch_id   = $request->branch_id;
                $leave->employee_id = $request->employee_id;
            } else { 
                $leave->branch_id   = Auth::user()->id;
                $leave->employee_id = $request->employee_id;
            }
        }
        
        $leave->from_date = $request->from_date;
        $leave->to_date = $request->to_date;
        $leave->department = $request->department;
        $leave->reason = $request->reason;
        $leave->save();

        // Your mail + notification code remains same
        // ...

        return response()->json(['status' => 'success']);
    }


    public function show($id)
    {
        $data['data'] = Leave::where('id', $id)->first();
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        $data['employee'] = Employee::get();  

        if (Auth::guard('employee')->check() && Auth::guard('employee')->user()->role == 9) {
            $branchId = Auth::guard('employee')->user()->branch_id;
            $data['employees'] = Employee::where('branch_id', $branchId)->get();
        }
        if (Auth::guard('user')->check() && Auth::guard('user')->user()->role == 2) {
            $branchId = Auth::user()->id;
            $data['employees'] = Employee::where('branch_id', $branchId)->get();
        }

        return view('leave.show')->with($data);
    }

    public function edit($id)
    {
        $data['title'] = 'Edit ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['data'] = Leave::find($id);

        $data['branch'] = User::where('role', 2)->get();

    
        if (Auth::guard('employee')->check() && Auth::guard('employee')->user()->role == 9) {
            $branchId = Auth::guard('employee')->user()->branch_id;

            $data['employees'] = Employee::where('branch_id', $branchId)->get();
        }
     if (Auth::guard('user')->check() && Auth::guard('user')->user()->role == 2) {
            $branchId = Auth::user()->id;
            $data['employees'] = Employee::where('branch_id', $branchId)->get();
        }

        $data['branch'] = User::where('role', 2)->get();

        if (Auth::user()->role == 1) {
            $data['employee'] = Employee::get();
        } else {
            $data['employee'] = Employee::where('branch_id', Auth::user()->id)->get();
        }


        return view('general.edit_form')->with($data);
    }

    public function update(Request $request, $id)
    {
        $leave = Leave::find($id);
    
        if (!$leave) {
            return redirect()->back()->with('error', 'Leave not found');
        }

        
        if (Auth::guard('employee')->check() && Auth::guard('employee')->user()->role != 9) {
        
            $leave->employee_id = Auth::guard('employee')->user()->id;
            $leave->branch_id = Auth::guard('employee')->user()->branch_id;
        }

        
        elseif (Auth::guard('employee')->check() && Auth::guard('employee')->user()->role == 9) {
            $leave->employee_id = $request->employee_id; // from dropdown
            $leave->branch_id = Auth::guard('employee')->user()->branch_id;
        }

       
        else {
            if (Auth::user()->role == 1) { // super admin
                $leave->branch_id = $request->branch_id;
                $leave->employee_id = $request->employee_id;
            } else { // branch admin
                $leave->branch_id = Auth::user()->id;
                $leave->employee_id = $request->employee_id;
            }
        }
       
        $leave->from_date  = $request->from_date;
        $leave->to_date    = $request->to_date;
        $leave->department = $request->department;
        $leave->reason     = $request->reason;
    
        $leave->save();
    
        return response()->json(['status' => 'success']);
    }
    

public function destroy($id)
{
        //
}

public function getdata(Request $request)
{
    $employees = Employee::where('branch_id', $request->branch_id)->get();

        $options = '';
        foreach ($employees as $emp) {
            $options .= '<option value="'.$emp->id.'">'.$emp->name.'</option>';
        }

        return response()->json([
            'data1' => $options
        ]);
}
}
