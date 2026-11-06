<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BrandLogo;
use App\User;
use App\Employee;
use Auth;
use File;
use DataTables;


class BrandLogoController extends Controller
{

    public function __construct(BrandLogo $s)
    {
        $this->view = 'brandlogo';
        $this->route = 'brandlogo';
        $this->viewName = 'Brand Logo';

    }

    public function index(Request $request)
    {
        if ($request->ajax())
        {
            if(Auth::guard('employee')->check())
            {
                $query = BrandLogo::where('branch_id',Auth::user()->branch_id);
            }
            else
            {
                if(Auth::user()->role == 2)
                {
                    $query = BrandLogo::where('branch_id',Auth::user()->id);
                }
                else
                {
                    $query = BrandLogo::orderby('id','DESC');
                }
            }
            return Datatables::of($query)
            ->addColumn('action', function ($row) {
                $btn = '<a style="background: transparent;" href="'.route('user.brandlogo.edit',$row->id).'" title="Edit details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: green;" class="la la-edit"></i>
                </a>&nbsp;&nbsp;&nbsp;';
                $btn .= '<a href="'.route('user.deletebrandlogo',$row->id).'" onclick="return confirm(`Are you sure?`)" style="background: white;" title="Delete details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: red;" class="la la-trash"></i></a>';

                $btn .= '&nbsp;&nbsp;<a style="background: white;" href="'.route('user.brandlogo.show',$row->id).'"  title="View Details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-eye"></i></a>';

                return $btn;
            })
            ->addColumn('image', function ($row) {
                if ($row->brand_logo) {
                    return '<a href="' . asset('brand_logo/' . $row->brand_logo) . '" target="_blank"> <img src="' . url('brand_logo/' . $row->brand_logo) . '" height=50 width=50></a>';
                } else {

                }
            })

            ->rawColumns(['action','image'])
            ->make(true);
        }
        return view('brandlogo.index');
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
        $brandlogo = new BrandLogo();
        $brandlogo->name = $request->name;
        if(isset($request->brand_logo))
        {
            $imageName = time().'.'. $request->brand_logo->getClientOriginalExtension();
            $request->brand_logo->move(public_path('brand_logo/'),$imageName);
            $brandlogo['brand_logo'] = $imageName;
        }
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $brandlogo->branch_id = $request->branch_id;
            }
            else
            {
                $brandlogo->branch_id = Auth::user()->id;
            }

        }
        else
        {
            $brandlogo->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
        }
        $brandlogo->save();
        if($brandlogo)
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
        $data['data'] = BrandLogo::where('id', $id)->first();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        return view('brandlogo.show')->with($data);
    }

    public function edit($id)
    {
        $data['title'] = 'Edit ' . $this->viewName;
        $data['data'] = BrandLogo::where('id', $id)->first();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        return view('general.edit_form')->with($data);
    }

    public function update(Request $request, $id)
    {
        $data = BrandLogo::where('id', $id)->first();
        $data->name = $request->name;
        if(isset($request->brand_logo))
        {
            $old_image = BrandLogo::where('id', $id)->value('brand_logo');
            if(isset($old_image))
            {
                File::delete(public_path('brand_logo/'.$old_image));
            }
            $imageName = time().'.'. $request->brand_logo->getClientOriginalExtension();
            $request->brand_logo->move(public_path('brand_logo/'),$imageName);
            $data['brand_logo'] = $imageName;
        }
        if(Auth::guard('user')->check())
        {
            if(Auth::user()->role == 1)
            {
                $data->branch_id = $request->branch_id;
            }
            else
            {
                $data->branch_id = Auth::user()->id;
            }

        }
        else
        {
            $data->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
        }
        $data->save();
        if($data)
        {
            return response()->json(['status' => 'success']);
        }else
        {
            return response()->json(['status' => 'error']);
        }
    }

    public function deletebrandlogo($id)
    {
        $data = BrandLogo::where('id',$id)->delete();
        return back();
    }
}
