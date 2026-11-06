<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Attendance;
use App\User;
use App\Employee;
use Auth;
use DataTables;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function __construct(Attendance $s)
    {
        $this->view = 'attendance';
        $this->route = 'attendance';
        $this->viewName = 'Attendance';
    }
    
    public function index(Request $request)
    {
        if(Auth::guard('user')->check())
        {
            if ($request->ajax())
            { 
                $query = Attendance::with('employee','branch')->orderBy('id','DESC');
                if(Auth::user()->role == 2){
                    $query = Attendance::with('employee','branch')->where('branch_id',Auth::user()->id)->orderBy('id','DESC');
                }

                if($request->branch_id)
                {
                    $query = $query->where('branch_id',$request->branch_id);
                }

                if($request->employee_id)
                {
                    $query = $query->where('employee_id',$request->employee_id);
                }

                if($request->from_date && $request->to_date)
                {
                    $query = $query->whereBetween('date', [$request->from_date, $request->to_date]);
                }
                return Datatables::of($query)

                ->addColumn('date', function ($row) {
                    $ac = date('d-m-Y',strtotime($row->date));
                    return $ac;

                })

                ->addColumn('action', function ($row) {
                    $btn = '<a style="background: white;" href="'.route('user.attendance.show',$row->id).'"  title="View Details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-eye"></i></a>&nbsp;&nbsp;&nbsp;';
                    return $btn;
                })

                ->addColumn('attendance', function ($row) {
                    // if($row->attendance == 'Approved' || $row->attendance == 'Leave')
                    // {

                    //     $btn = '<select class="form-control" disabled><option value="Approved"';

                    // }
                    // else
                    // {
                    //     $btn = '<select class="form-control" onchange="attendance(this,'.$row->id.')"><option value="Not Approved"';
                    // }

                    // $selected = '';
                    // if($row->attendance == 'Not Approved' )
                    // {
                    //     $selected = 'selected';
                    // }
                    // $btn .= $selected.'>Not Approved</option><option value="Approved" ';
                    // $selected = '';
                    // if($row->attendance == 'Approved')
                    // {
                    //     $selected = 'selected';
                    // }
                    // $btn .= $selected.'>Approved</option><option value="Leave" ';
                    // $selected = '';
                    // if($row->attendance == 'Leave')
                    // {
                    //     $selected = 'selected';
                    // }
                    // $btn .= $selected.'>Leave</option>
                    // </select>';

                    $btn = ($row->attendance_status == 1)
                    ? '<span style="color:#2675d1;">On Time</span>'
                    : (($row->attendance_status == 2)
                        ? '<span style="color:red;">Late</span>'
                        : '-');
                    return $btn;
                })

                ->rawColumns(['date','attendance','action'])
                ->make(true);
            }
        }

        else if(Auth::guard('employee')->check())
        {
            if ($request->ajax())
            { 
                $query = Attendance::with('employee','branch')->where('employee_id',Auth::user()->id)->orderBy('id','DESC');
                if($request->from_date && $request->to_date)
                {
                    $query = $query->whereBetween('date', [$request->from_date, $request->to_date]);
                }
                return Datatables::of($query)

                ->addColumn('date', function ($row) {
                    $ac = date('d-m-Y',strtotime($row->date));
                    return $ac;

                })

                ->addColumn('action', function ($row) {
                    $btn = '<a style="background: white;" href="'.route('user.attendance.show',$row->id).'"  title="View Details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-eye"></i></a>&nbsp;&nbsp;&nbsp;';
                    return $btn;
                })

                ->addColumn('attendance', function ($row) {
                    // if($row->attendance == 'Approved' || $row->attendance == 'Leave' || $row->attendance == 'Not Approved ')
                    // {

                    //     $btn = '<select class="form-control" disabled><option value="Approved"';

                    // }
                    // else
                    // {
                    //     $btn = '<select class="form-control" onchange="attendance(this,'.$row->id.')"><option value="Not Approved"';
                    // }

                    // $selected = '';
                    // if($row->attendance == 'Not Approved' )
                    // {
                    //     $selected = 'selected';
                    // }
                    // $btn .= $selected.'>Not Approved</option><option value="Approved" ';
                    // $selected = '';
                    // if($row->attendance == 'Approved')
                    // {
                    //     $selected = 'selected';
                    // }
                    // $btn .= $selected.'>Approved</option><option value="Leave" ';
                    // $selected = '';
                    // if($row->attendance == 'Leave')
                    // {
                    //     $selected = 'selected';
                    // }
                    // $btn .= $selected.'>Leave</option>
                    // </select>';
                    $btn = ($row->attendance_status == 1)
                    ? '<span style="color:#2675d1;">On Time</span>'
                    : (($row->attendance_status == 2)
                        ? '<span style="color:red;">Late</span>'
                        : '-');
                    return $btn;
                })

                ->rawColumns(['date','attendance','action'])
                ->make(true);
            }
        }
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        $data['employee'] = Employee::where('status',1)->get();
        if(Auth::user()->role == 2)
        {
            $data['employee'] = Employee::where('branch_id',Auth::user()->id)->where('status',1)->get();
        }
        return view('attendance.index')->with($data);
    }

    public function attendance(Request $request)
    {
        $param['attendance'] = $request->attendance;
        $change_status = Attendance::where('id',$request->id)->update($param);
        if($change_status)
        {
            return response()->json(['status' => 'success']);
        }
        else
        {
            return response()->json(['status' => 'error']);
        }
    }

    public function show($id)
    {
        $data['attendance'] = Attendance::findOrFail($id);
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        if(Auth::guard('employee')->check())
        {
            $data['employees'] = Employee::where('id',Auth::user()->id)->where('status',1)->whereIn('role',['1','2'])->get();
        }
        elseif(Auth::user()->role == 2)
        {
            $data['employees'] = Employee::where('branch_id',Auth::user()->id)->where('status',1)->whereIn('role',['1','2'])->get();
        }
        return view('attendance.show', compact('data'))->with($data);
    }

    public function create()
    {
        return view('attendance.create');
    }

    public function store(Request $request)
    {
        try {
            $check = Attendance::where('employee_id', Auth::user()->id)
            ->where('date', now()->toDateString())
            ->first();

            if ($check) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already marked attendance today.'
                ], 400);
            }

            if (!$request->hasFile('photo')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No photo was uploaded.'
                ], 400);
            }

            $employee = Employee::find(Auth::id());
            $officeStart = Carbon::parse($employee->office_start_time);
            $proxyTime = $officeStart->copy()->addMinutes($employee->buffer_minutes);
            $currentTime = Carbon::now();

            $status = 0;
            if($employee->office_start_time != null)
            {
                if ($currentTime->greaterThan($proxyTime)) {
                    $status = 2;
                } else {
                    $status = 1;
                }
            }

            $attendance = new Attendance();
            $attendance->branch_id = Auth::user()->branch_id;
            $attendance->employee_id = Auth::user()->id;

            $year = Carbon::now()->format('Y');
            $month = Carbon::now()->format('m');
            $path = 'attendance/' . $year . '/' . $month;
            $filename = time() . '.png';
            $request->file('photo')->move(public_path($path), $filename);
            $attendance->photo = $path . '/' . $filename;

            $attendance->attendance = 'Not Approved';
            $attendance->date = now()->toDateString();
            $attendance->time = now()->toTimeString();
            $attendance->latitude = $request->latitude;
            $attendance->longitude = $request->longitude;
            $attendance->attendance_status = $status;
            $attendance->save();

            return response()->json([
                'success' => true,
                'message' => 'Attendance saved successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
            ], 500);
        }
    }
}