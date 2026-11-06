<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CompanyPolicy;
use App\User;
use Auth;
use DataTables;

class CompanyPolicyController extends Controller
{
    public function __construct()
    {
        $this->view = 'company_policy';
        $this->route = 'company_policy';
        $this->viewName = 'Company Policy';
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = CompanyPolicy::orderby('id','DESC');

            if(Auth::guard('employee')->check())
            {
                $query = CompanyPolicy::where('branch_id',Auth::user()->branch_id)
                        ->where('created_by',Auth::user()->id);
            }
            else
            {
                if(Auth::user()->role == 2)
                {
                    $query = CompanyPolicy::where('branch_id',Auth::user()->id);
                }
                else
                {
                    $query = CompanyPolicy::orderby('id','DESC');
                }
            }

            if($request->branch_id)
            {
                $query->where('branch_id',$request->branch_id);
            }

            return DataTables::of($query)
            ->addColumn('DT_RowIndex', function ($row) {
                return $row->id;
            })
            ->addColumn('created_by_name', function ($row) {

                if ($row->createdByAdmin) {
                    return $row->createdByAdmin->name ?? '-';
                }

                if ($row->createdByEmployee) {
                    return $row->createdByEmployee->name ?? '-';
                }

                return '-';
            })

            ->addColumn('branch_id', function ($row) {

                if($row->branch_id)
                {
                    return User::where('id',$row->branch_id)->value('name');
                }

                return '-';
            })

            ->addColumn('action', function ($row) {

                $btn = '<a href="'.route('user.company_policy.edit',$row->id).'" class="btn btn-sm btn-clean">
                <i class="la la-edit" style="color:green"></i></a>';

                $btn .= '<a href="'.route('user.company_policy.show',$row->id).'" class="btn btn-sm btn-clean">
                <i class="flaticon-eye" style="color:black"></i></a>';

                $btn .= '<form method="POST" action="'.route('user.company_policy.destroy',$row->id).'" style="display:inline-block;">
                '.csrf_field().method_field("DELETE").'
                <button type="submit" onclick="return confirm(\'Are you sure?\')" class="btn btn-sm btn-clean">
                <i class="la la-trash" style="color:red;"></i>
                </button></form>';
                $btn .= '<a href="'.route('user.company_policy.pdf', $row->id).'" 
                    class="btn btn-sm btn-clean">
                    <i class="fa fa-file-pdf"></i></a>';

                return $btn;
            })

            ->rawColumns(['action'])
            ->make(true);
        }

        return view('company_policy.index');
    }

    public function create()
    {
        $data['title'] = 'Add '.$this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;

        $data['branch'] = User::where('role',2)->get();

        return view('general.add_form')->with($data);
    }

    public function store(Request $request)
    {
        $policy = new CompanyPolicy();

        $policy->created_by = Auth::user()->id;

        if(Auth::guard('employee')->check())
        {
            $policy->branch_id = Auth::guard('employee')->user()->branch_id;
        }
        else
        {
            if(Auth::user()->role == 1)
            {
                $policy->branch_id = $request->branch_id;
            }
            else
            {
                $policy->branch_id = Auth::user()->id;
            }
        }

        $policy->policy_description = $request->policy_description;

        $policy->save();

        return response()->json(['status'=>'success']);
    }

    public function edit($id)
    {
        $data['title'] = 'Edit '.$this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;

        $data['data'] = CompanyPolicy::find($id);

        $data['branch'] = User::where('role',2)->get();

        return view('general.edit_form')->with($data);
    }

    public function update(Request $request,$id)
    {
        $policy = CompanyPolicy::find($id);

        $policy->created_by = Auth::user()->id;

        if(Auth::guard('employee')->check())
        {
            $policy->branch_id = Auth::guard('employee')->user()->branch_id;
        }
        else
        {
            if(Auth::user()->role == 1)
            {
                $policy->branch_id = $request->branch_id;
            }
            else
            {
                $policy->branch_id = Auth::user()->id;
            }
        }

        $policy->policy_description = $request->policy_description;

        $policy->save();

        return response()->json(['status'=>'success']);
    }

    public function destroy($id)
    {
        CompanyPolicy::destroy($id);
        return back()->with('success','Deleted Successfully!');
    }

    public function show($id)
    {
        $data['title'] = 'View '.$this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;

        $data['data'] = CompanyPolicy::find($id);
        
        $data['branch'] = User::where('role',2)->get();

        return view('company_policy.show')->with($data);
    }
    public function pdf($id)
    {
        $policy = CompanyPolicy::findOrFail($id);

        $data['title'] = 'Company Policy';
        $data['data'] = $policy;

        $data['branchname'] = User::where('id',$policy->branch_id)->value('name');
        $data['branchlogo'] = User::where('id',$policy->branch_id)->value('image');

        $pdf = \PDF::loadView('company_policy.pdf', $data);

        return $pdf->download('CompanyPolicy_'.$id.'.pdf');
    }
}