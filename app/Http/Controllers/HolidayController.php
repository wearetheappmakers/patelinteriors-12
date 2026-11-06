<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Holiday;
use Carbon\Carbon;
use DataTables;

class HolidayController extends Controller
{
    public function __construct()
    {
        $this->view = 'holiday';
        $this->route = 'holiday';
        $this->viewName = 'Holiday';
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $query = Holiday::latest();

            return DataTables::of($query)
            ->addColumn('from_date', function($row) {
                return $row->from_date ? Carbon::parse($row->from_date)->format('d-m-Y') : '';
            })
            ->addColumn('to_date', function($row) {
                return $row->to_date ? Carbon::parse($row->to_date)->format('d-m-Y') : '';
            })
            ->addColumn('name', function($row) {
                return $row->name;
            })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="'.route('user.holiday.edit',$row->id).'" class="btn btn-sm btn-clean">
                                <i class="la la-edit" style="color:green"></i>
                            </a>';

                    $btn .= '<a href="'.route('user.holiday.show',$row->id).'" class="btn btn-sm btn-clean">
                                <i class="flaticon-eye" style="color:black"></i>
                            </a>';

                    $btn .= '<form method="POST" action="'.route('user.holiday.destroy', $row->id).'" style="display:inline-block;">
                        '.csrf_field().method_field("DELETE").'
                        <button type="submit" onclick="return confirm(\'Are you sure want to delete?\')" class="btn btn-sm btn-clean delete-btn">
                        <i style="color: red;" class="la la-trash"></i>
                        </button>
                        </form>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('holiday.index');
    }


    public function create()
    {
        $data['title'] = 'Add ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;

        return view('general.add_form')->with($data);
    }


    public function store(Request $request)
    {
        $holiday = new Holiday();

        $holiday->from_date = $request->from_date;
        $holiday->to_date = $request->to_date;
        $holiday->name = $request->name;
        $holiday->save();

        return response()->json(['status' => 'success']);
    }


    public function edit($id)
    {
        $data['title'] = 'Edit ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['data'] = Holiday::find($id);

        return view('general.edit_form')->with($data);
    }


    public function update(Request $request, $id)
    {
        $holiday = Holiday::find($id);

        $holiday->from_date = $request->from_date;
        $holiday->to_date = $request->to_date;
        $holiday->name = $request->name;
        $holiday->save();

        return response()->json(['status' => 'success']);
    }


    public function destroy($id)
    {
        Holiday::destroy($id);
        return back()->with('success', 'Deleted Successfully!');
    }


    public function show($id)
    {
        $data['title'] = 'View ' . $this->viewName;
        $data['data'] = Holiday::find($id);
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;

        return view('holiday.show')->with($data);
    }
}
