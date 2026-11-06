<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\SourceName;
use App\Employee;
use App\Symbol;
use App\Country;
use App\State;
use App\City;
use DataTables;
use App\Vendor;
use App\User;
use Auth;
class VendorController extends Controller
{
    public function __construct(Vendor $s)
    {
        $this->view = 'vendor';
        $this->route = 'vendor';
        $this->viewName = 'Vendor';
    }
    public function index(Request $request)
    {
        if ($request->ajax())
        {
            $query = Vendor::orderby('id','DESC');
            if(Auth::guard('employee')->check())
            {
                $query = Vendor::where('branch_id',Auth::user()->branch_id);
            }
            else
            {
                if(Auth::user()->role == 2)
                {
                    $query = Vendor::where('branch_id',Auth::user()->id);
                }
                else
                {
                    $query = Vendor::orderby('id','DESC');
                }
            }
            if($request->branch_id)
            {
                $query->where('branch_id',$request->branch_id);
            }
            $query = $query->orderBy('id','DESC')->latest();
            return Datatables::of($query)
            ->addColumn('action', function ($row) {
                $btn = '<a style="background: transparent;" href="'.route('user.vendor.edit',$row->id).'" title="Edit details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: green;" class="la la-edit"></i>
                </a>&nbsp;&nbsp;&nbsp;';
                $btn .= '<a style="background: white;" href="'.route('user.vendor.show',$row->id).'"  title="View Details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-eye"></i></a>&nbsp;&nbsp;';
                if(Auth::user()->role == 1)
                {
                    $btn .= '<a href="'.route('user.vendor.delete',$row->id).'" onclick="return confirm(`Are you sure?`)" style="background: white;" title="Delete details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: red;" class="la la-trash"></i></a>';
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
        return view('vendor.index');
    }
    public function create()
    {
        $data['title'] = 'Add ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        $data['source'] = Symbol::where('status',1)->get();
        $data['country'] = Country::get();

        return view('general.add_form')->with($data);
    }
    public function store(Request $request)
    {
        $vendor = new Vendor();
        $vendor->name = $request->name;
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $vendor->branch_id = $request->branch_id;
            }
            else
            {
                $vendor->branch_id = Auth::user()->id;
            }
        }
        else
        {
            $vendor->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
        }
        $vendor->gst_no = $request->gst_no;
        $vendor->number = $request->number;
        $vendor->email = $request->email;
        $vendor->address = $request->address;
        $vendor->company_name = $request->company_name;
        $vendor->company_address = $request->company_address;
        $vendor->country_id = $request->country_id;
        $vendor->state_id = $request->state_id;
        $vendor->city_id = $request->city_id;
        $vendor->save();
        if ($vendor){
            return response()->json(['status'=>'success']);
        }else{
            return response()->json(['status'=>'error']);
        }
    }
    public function show($id)
    {
        $data['title'] = 'View ' . $this->viewName;
        $data['data'] = Vendor::where('id', $id)->first();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        // $data['source'] = LeadSource::where('status',1)->get();
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        $data['country'] = Country::get();
        $data['state'] = State::where('country_id',$data['data']->country)->get();
        $data['city'] = City::where('state_id',$data['data']->state)->get();
        
        return view('vendor.show')->with($data);
    }
    public function edit($id)
    {
        $data['title'] = 'Edit ' . $this->viewName;
        $data['data'] = Vendor::where('id', $id)->first();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        // $data['source'] = LeadSource::where('status',1)->get();
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        $data['country'] = Country::get();
        $data['state'] = State::where('country_id',$data['data']->country_id)->get();
        $data['city'] = City::where('state_id',$data['data']->state_id)->get();

        return view('general.edit_form')->with($data);
    }
    public function update(Request $request, $id)
    {
        $vendor = Vendor::where('id', $id)->first();
        $vendor->name = $request->name;
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $vendor->branch_id = $request->branch_id;
            }
            else
            {
                $vendor->branch_id = Auth::user()->id;
            }
        }
        else
        {
            $vendor->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
        }
        $vendor->gst_no = $request->gst_no;
        $vendor->number = $request->number;
        $vendor->email = $request->email;
        $vendor->address = $request->address;
        $vendor->company_name = $request->company_name;
        $vendor->company_address = $request->company_address;
        $vendor->country_id = $request->country_id;
        $vendor->state_id = $request->state_id;
        $vendor->city_id = $request->city_id;
        $vendor->save();
        if ($vendor){
            return response()->json(['status'=>'success']);
        }else{
            return response()->json(['status'=>'error']);
        }
    }
    public function deletesourcename($id)
    {
        $data = Vendor::where('id',$id)->delete();
        return back();
    }

    public function getstate(Request $request)
    {
        $data = State::where('country_id',$request->country_id)->get();
        $html = "<option value=''>Select</option>";
        foreach ($data as $key => $value) {
            $html .= "<option value=".$value->id.">".$value->name."</option>";
        }
        return response()->json(['data'=>$html]);
    }
    public function getcity(Request $request)
    {
        $data = City::where('state_id',$request->state_id)->get();
        $html = "<option value=''>Select</option>";
        foreach ($data as $key => $value) {
            $html .= "<option value=".$value->id.">".$value->name."</option>";
        }

        $html1 = City::where('state_id',$request->state_id)->value('state_id');

        return response()->json(['data'=>$html, 'data1'=>$html1]);
    }
}