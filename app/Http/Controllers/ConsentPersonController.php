<?php
namespace App\Http\Controllers;
use App\User;
use App\ConsentPerson;
use App\Employee;
use Illuminate\Http\Request;
use Auth;
use DataTables;
class ConsentPersonController extends Controller
{
    public function __construct(ConsentPerson $s)
    {
        $this->view = 'consent_person';
        $this->route = 'consent_person';
        $this->viewName = 'Consent Person';
    }   
    public function index(Request $request)
    {
        if(Auth::guard('employee')->check())
        {
            if ($request->ajax()) {
                $query = ConsentPerson::where('branch_id',Auth::user()->branch_id)->latest();

                return Datatables::of($query)
                ->addColumn('action', function ($row) {
                    $btn = '<a style="background: transparent;" href="'.route('user.consent_person.edit',$row->id).'" title="Edit details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: green;" class="la la-edit"></i>
                    </a>&nbsp;&nbsp;&nbsp;';
                    $btn .= '<a style="background: white;" href="'.route('user.consent_person.show',$row->id).'"  title="View Details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-eye"></i></a>&nbsp;&nbsp;&nbsp;';
                    return $btn;
                })
                ->editColumn('branch_id', function ($row) {
                    $btn = $row->branch->name;
                    return $btn;
                })
                ->rawColumns(['numbers','action','branch_id'])
                ->make(true);
            } 
        }
        else
        {
            if ($request->ajax()) {
                $query = ConsentPerson::latest();
                if(Auth::user()->role == 2)
                {
                    $query = ConsentPerson::where('branch_id',Auth::user()->id)->latest();
                }
                if($request->branch_id)
                {
                    $query->where('branch_id',$request->branch_id);
                }

                return Datatables::of($query)

                ->addColumn('action', function ($row) {
                    $btn = '<a style="background: transparent;" href="'.route('user.consent_person.edit',$row->id).'" title="Edit details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: green;" class="la la-edit"></i>
                    </a>&nbsp;&nbsp;&nbsp;';
                    $btn .= '&nbsp;&nbsp;<a style="background: white;" href="'.route('user.consent_person.show',$row->id).'"  title="View Details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-eye"></i></a>&nbsp;&nbsp;&nbsp;';

                    if(Auth::user()->role == 1)
                    {
                        $btn .= '<a href="'.route('user.delete.consent_person',$row->id).'" onclick="return confirm(`Are you sure?`)" style="background: white;" title="Delete details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: red;" class="la la-trash"></i></a>';
                    }
                    return $btn;
                })
                ->editColumn('branch_id', function ($row) {
                    $btn = $row->branch->name;
                    return $btn;
                })
                ->rawColumns(['numbers','action','singlecheckbox','branch_id'])
                ->make(true);
            } 
        }
        $data['branch'] = User::where('role',2)->where('status',1)->get();

        return view('consent_person.index')->with($data);
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
        $consent_person = new ConsentPerson();
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $consent_person->branch_id = $request->branch_id;
            }
            else
            {
                $consent_person->branch_id = Auth::user()->id;
            }
        }
        else
        {
            $consent_person->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
        }
        $check = ConsentPerson::where('number',$request->number)->where('branch_id',$consent_person->branch_id)->exists();
        if($check)
        {
            return response()->json(['status' => 'required', 'message' => 'Duplicate Number']);
        }
        $consent_person->name = $request->name;
        $consent_person->number = $request->number;
        $consent_person->save();

        if($consent_person)
        {
            return response()->json(['status' => 'success']);
        }
        else
        {
            return response()->json(['status' => 'error']);
        }
    }

    public function edit($id)
    {
        $data['title'] = 'Edit ' . $this->viewName;
        $data['data'] = ConsentPerson::where('id', $id)->first();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['branch'] = User::where('role',2)->get();

        return view('general.edit_form')->with($data);
    }
    public function update(Request $request, $id)
    {
        $consent_person = ConsentPerson::where('id',$id)->first();
        $check = ConsentPerson::where('number',$request->number)->where('branch_id',$consent_person->branch_id)->where('id','!=',$id)->exists();
        if($check)
        {
            return response()->json(['status' => 'required', 'message' => 'Duplicate Number']);
        }
        $consent_person->name = $request->name;
        $consent_person->number = $request->number;
        $consent_person->save();

        if($consent_person)
        {
            return response()->json(['status' => 'success']);
        }
        else
        {
            return response()->json(['status' => 'error']);
        }
    }
    public function delete($id)
    {
        $data = ConsentPerson::where('id',$id)->delete();
        return back();
    }
    public function show($id)
    {
        $data['title'] = 'View ' . $this->viewName;
        $data['data'] = ConsentPerson::where('id', $id)->first();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;

        return view('consent_person.show')->with($data);
    }
}