<?php
namespace App\Http\Controllers;
use App\User;
use DataTables;
use App\Landmark;
use Auth;
use Illuminate\Http\Request;

class LandmarkController extends Controller
{
    public function __construct(Landmark $s)
    {
        $this->view = 'landmark';
        $this->route = 'landmark';
        $this->viewName = 'Landmark';
    }   
    public function index(Request $request)
    {
        if ($request->ajax()) {
            if(Auth::guard('employee')->check())
            {
                $query = Landmark::where('branch_id',Auth::user()->branch_id);
            }
            else
            {
                if(Auth::user()->role == 2)
                {
                    $query = Landmark::where('branch_id',Auth::user()->id);
                }
                else
                {
                    $query = Landmark::orderby('id','DESC');
                }
            }
            return Datatables::of($query)
            ->addColumn('action', function ($row) {
                $btn = '<a style="background: transparent;" href="'.route('user.landmark.edit',$row->id).'" title="Edit details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: green;" class="la la-edit"></i>
                </a>&nbsp;&nbsp;&nbsp;';

                $btn .= '&nbsp;&nbsp;<a style="background: white;" href="'.route('user.landmark.show',$row->id).'"  title="View Details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-eye"></i></a>&nbsp;&nbsp;&nbsp;';

                if(Auth::user()->role == 1)
                {
                    $btn .= '<a href="'.route('user.deletes.landmark',$row->id).'" onclick="return confirm(`Are you sure?`)" style="background: white;" title="Delete details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: red;" class="la la-trash"></i></a>';
                }

                return $btn;
            })

            ->rawColumns(['numbers','action'])
            ->make(true);
        } 
        return view('landmark.index');
    }
    public function create()
    {
        $data['title'] = 'Add ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['branch'] = User::where('role',2)->get();
        return view('general.add_form')->with($data);
    }
    public function store(Request $request)
    {
        $landmark = new Landmark();
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $landmark->branch_id = $request->branch_id;
            }
            else
            {
                $landmark->branch_id = Auth::user()->id;
            }
        }
        else
        {
            $landmark->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
        }
        $landmark->name = $request->name;
        $landmark->save();

        if($landmark)
        {
            return response()->json(['status' => 'success']);
        }else
        {
            return response()->json(['status' => 'error']);
        }
    }
    public function show($id)
    {
        $data['title'] = 'View ' . $this->viewName;
        $data['data'] = Landmark::where('id', $id)->first();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['branch'] = User::where('role',2)->get();
        $data['branchname'] = User::where('id',$data['data']->branch_id)->value('name');
        return view('landmark.show')->with($data);
    }
    public function edit($id)
    {
        $data['title'] = 'Edit ' . $this->viewName;
        $data['data'] = Landmark::where('id', $id)->first();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['branch'] = User::where('role',2)->get();
        return view('general.edit_form')->with($data);
    }
    public function update(Request $request, $id)
    {
        $landmark = Landmark::where('id',$id)->first();
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $landmark->branch_id = $request->branch_id;
            }
            else
            {
                $landmark->branch_id = Auth::user()->id;
            }
        }
        else
        {
            $landmark->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
        }
        $landmark->name = $request->name;
        $landmark->save();

        if($landmark)
        {
            return response()->json(['status' => 'success']);
        }else
        {
            return response()->json(['status' => 'error']);
        }
    }
    public function delete($id)
    {
        $data = Landmark::where('id',$id)->delete();
        return back();
    }
}
