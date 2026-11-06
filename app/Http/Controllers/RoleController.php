<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Role;
use Auth;
use DataTables;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->view = 'role';
        $this->route = 'role';
        $this->viewName = 'Role';
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            // load all roles
            $query = Role::latest();

            return DataTables::of($query)
                ->addColumn('action', function ($row) {

                    $btn = '<a href="' . route('user.role.edit', $row->id) . '" class="btn btn-sm btn-clean">
                                <i class="la la-edit" style="color:green"></i>
                            </a>';

                    // $btn .= '<a href="' . route('user.role.show', $row->id) . '" class="btn btn-sm btn-clean">
                    //             <i class="flaticon-eye" style="color:black"></i>
                    //         </a>';

                    // allow delete for role 1 only
                    if (in_array(Auth::user()->role, [1, 2])) { 
                        $btn .= '<form method="POST" action="'.route('user.role.destroy', $row->id).'" style="display:inline-block;">
                                    '.csrf_field().method_field("DELETE").'
                                    <button type="submit" onclick="return confirm(\'Are you sure want to delete?\')" class="btn btn-sm btn-clean delete-btn">
                                        <i class="la la-trash" style="color:red"></i>
                                    </button>
                                </form>';
                    }

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $data = []; // no extra data needed

        return view('role.index', $data);
    }

    public function create()
    {
        // allow both role 1 and 2 to access create form
        if (!in_array(Auth::user()->role, [1,2])) {
            abort(403, 'Unauthorized action.');
        }

        $data['title'] = 'Add ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;

        return view('general.add_form')->with($data);
    }

    public function store(Request $request)
    {
        // allow both role 1 and 2 to store roles
        if (!in_array(Auth::user()->role, [1,2])) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized']);
        }

      

        $role = new Role();
        $role->name = $request->name;
        $role->save();

        return response()->json(['status' => 'success']);
    }

    public function edit($id)
    {
        if (!in_array(Auth::user()->role, [1,2])) {
            abort(403, 'Unauthorized action.');
        }

        $data['title'] = 'Edit ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['data'] = Role::findOrFail($id);

        return view('general.edit_form')->with($data);
    }

    public function update(Request $request, $id)
    {
        if (!in_array(Auth::user()->role, [1,2])) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized']);
        }

       

        $role = Role::findOrFail($id);
        $role->name = $request->name;
        $role->save();

        return response()->json(['status' => 'success']);
    }

    public function destroy($id)
    {
        
        if (!in_array(Auth::user()->role, [1, 2])) {
            abort(403, 'Unauthorized action.');
        }
        
        $role = Role::findOrFail($id);
        $role->delete();

        return back()->with('success', 'Deleted Successfully!');
    }

    
}
