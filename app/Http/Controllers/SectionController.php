<?php

namespace App\Http\Controllers;
use App\User;
use DataTables;
use App\Section;
use App\ProductCategory;
use Illuminate\Http\Request;


class SectionController extends Controller
{
     public function __construct(Section $s)
    {
        $this->view = 'section';
        $this->route = 'section';
        $this->viewName = 'Section';
    }   

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Section::orderBy('id','DESC');

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
                $query = Section::latest();
            }
            return Datatables::of($query)
            ->addColumn('action', function ($row) {
                // $btn = '<a href="'.route('user.edit', $row->id).'">Edit</a>&nbsp&nbsp&nbsp<a href="'.route('user.delete', $row->id).'">Delete</a>'; 
               
                   
                     $btn = view('layouts.actionbtnpermission')->with(['id'=>$row->id,'route'=>'section','per_value'=>'section'])->render();      
                          
               
                return $btn;
            })

             ->addColumn('singlecheckbox', function ($row) {

               
                   
                    $schk = view('layouts.singlecheckbox')->with(['id' => $row->id , 'status'=>$row->status])->render();
                    
                

                return $schk;

            })



            

            ->rawColumns(['numbers','action','singlecheckbox'])
            ->make(true);
        } 

        return view('section.index');
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
        $section = new Section();
        $section->name = $request->name;
        $section->save();

        if($section)
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
        $data['data'] = Section::where('id', $id)->first();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
         $data['branch'] = User::where('role',2)->get();
        return view('general.edit_form')->with($data);
    }
    public function update(Request $request, $id)
    {
        $section = Section::where('id',$id)->first();
        $section->name = $request->name;
        $section->save();

        if($section)
        {
            return response()->json(['status' => 'success']);
        }else
        {
            return response()->json(['status' => 'error']);
        }
    }
}
