<?php

namespace App\Http\Controllers;
use App\User;
use DataTables;
use App\Group;
use App\Attendance;
use Illuminate\Http\Request;


class GroupController extends Controller
{
     public function __construct(Group $s)
    {
        $this->view = 'group';
        $this->route = 'group';
        $this->viewName = 'Group';
    }   

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Group::where('id','!=',1)->latest();
            return Datatables::of($query)
              ->addColumn('action', function ($row) {
                // $btn = '<a href="'.route('user.edit', $row->id).'">Edit</a>&nbsp&nbsp&nbsp<a href="'.route('user.delete', $row->id).'">Delete</a>'; 
               
                   
                     $btn = view('layouts.actionbtnpermission')->with(['id'=>$row->id,'route'=>'group','per_value'=>'group'])->render();      
                          
               
                return $btn;
            })
           



            // ->addColumn('singlecheckbox', function ($row) {

            //     if($row->id == 1)
            //     {
            //         $schk = 'Cannot Change';
            //     }
            //     else
            //     {
                   
            //         $schk = view('layouts.singlecheckbox')->with(['id' => $row->id , 'status'=>$row->status])->render();
                    
            //     }

            //     return $schk;

            

            ->rawColumns(['numbers','action','singlecheckbox'])
            ->make(true);
        } 

        return view('group.index');
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
        $param = [];
        $param['name'] = $request['name'];
      

       
           $param['pc'] = [
            '0'=>$request->pc_view ?? 0,
            '1'=>$request->pc_add ?? 0,
            '2'=>$request->pc_edit ?? 0,
            '3'=>$request->pc_delete ?? 0,
        ];
        $param['product'] = [
            '0'=>$request->product_view ?? 0,
            '1'=>$request->product_add ?? 0,
            '2'=>$request->product_edit ?? 0,
            '3'=>$request->product_delete ?? 0,
        ];
         $param['lead'] = [
            '0'=>$request->lead_view ?? 0,
            '1'=>$request->lead_add ?? 0,
            '2'=>$request->lead_edit ?? 0,
            '3'=>$request->lead_delete ?? 0,
        ];

         $param['complain'] = [
            '0'=>$request->complain_view ?? 0,
            '1'=>$request->complain_add ?? 0,
            '2'=>$request->complain_edit ?? 0,
            '3'=>$request->complain_delete ?? 0,
        ];

        $param['quot'] = [
            '0'=>$request->quot_view ?? 0,
            '1'=>$request->quot_add ?? 0,
            '2'=>$request->quot_edit ?? 0,
            '3'=>$request->quot_delete ?? 0,
        ];

         $param['super'] = [
            '0'=>$request->super_view ?? 0,
            '1'=>$request->super_add ?? 0,
            '2'=>$request->super_edit ?? 0,
            '3'=>$request->super_delete ?? 0,
        ];

         $param['contact'] = [
            '0'=>$request->contact_view ?? 0,
            '1'=>$request->contact_add ?? 0,
            '2'=>$request->contact_edit ?? 0,
            '3'=>$request->contact_delete ?? 0,
        ];



         $param['employee'] = [
            '0'=>$request->employee_view ?? 0,
            '1'=>$request->employee_add ?? 0,
            '2'=>$request->employee_edit ?? 0,
            '3'=>$request->employee_delete ?? 0,
        ];

         $param['distri'] = [
            '0'=>$request->distri_view ?? 0,
            '1'=>$request->distri_add ?? 0,
            '2'=>$request->distri_edit ?? 0,
            '3'=>$request->distri_delete ?? 0,
        ];

         $param['section'] = [
            '0'=>$request->section_view ?? 0,
            '1'=>$request->section_add ?? 0,
            '2'=>$request->section_edit ?? 0,
            '3'=>$request->section_delete ?? 0,
        ];
         $param['stage'] = [
            '0'=>$request->stage_view ?? 0,
            '1'=>$request->stage_add ?? 0,
            '2'=>$request->stage_edit ?? 0,
            '3'=>$request->stage_delete ?? 0,
        ];
         $param['source'] = [
            '0'=>$request->source_view ?? 0,
            '1'=>$request->source_add ?? 0,
            '2'=>$request->source_edit ?? 0,
            '3'=>$request->source_delete ?? 0,
        ];

         $param['gst'] = [
            '0'=>$request->gst_view ?? 0,
            '1'=>$request->gst_add ?? 0,
            '2'=>$request->gst_edit ?? 0,
            '3'=>$request->gst_delete ?? 0,
        ];

         $param['client'] = [
            '0'=>$request->client_view ?? 0,
            '1'=>$request->client_add ?? 0,
            '2'=>$request->client_edit ?? 0,
            '3'=>$request->client_delete ?? 0,
        ];

         $param['quotation_footer'] = [
            '0'=>$request->footer_view ?? 0,
            '1'=>$request->footer_add ?? 0,
            '2'=>$request->footer_edit ?? 0,
            '3'=>$request->footer_delete ?? 0,
        ];

         $param['task'] = [
            '0'=>$request->task_view ?? 0,
            '1'=>$request->task_add ?? 0,
            '2'=>$request->task_edit ?? 0,
            '3'=>$request->task_delete ?? 0,
        ];

         $param['amc'] = [
            '0'=>$request->amc_view ?? 0,
            '1'=>$request->amc_add ?? 0,
            '2'=>$request->amc_edit ?? 0,
            '3'=>$request->amc_delete ?? 0,
        ];

         $param['member'] = [
            '0'=>$request->member_view ?? 0,
            '1'=>$request->member_add ?? 0,
            '2'=>$request->member_edit ?? 0,
            '3'=>$request->member_delete ?? 0,
        ];
         $param['template'] = [
            '0'=>$request->template_view ?? 0,
            '1'=>$request->template_add ?? 0,
            '2'=>$request->template_edit ?? 0,
            '3'=>$request->template_delete ?? 0,
        ];
         $param['a_keyword'] = [
            '0'=>$request->a_keyword_view ?? 0,
            '1'=>$request->a_keyword_add ?? 0,
            '2'=>$request->a_keyword_edit ?? 0,
            '3'=>$request->a_keyword_delete ?? 0,
        ];
         $param['system_a'] = [
            '0'=>$request->system_a_view ?? 0,
            '1'=>$request->system_a_add ?? 0,
            '2'=>$request->system_a_edit ?? 0,
            '3'=>$request->system_a_delete ?? 0,
        ];
         $param['autoresponder'] = [
            '0'=>$request->autoresponder_view ?? 0,
            '1'=>$request->autoresponder_add ?? 0,
            '2'=>$request->autoresponder_edit ?? 0,
            '3'=>$request->autoresponder_delete ?? 0,
        ];


        $param['pc'] = implode(',',$param['pc']);
        $param['product'] = implode(',',$param['product']);
        $param['lead'] = implode(',',$param['lead']);
        $param['complain'] = implode(',',$param['complain']);
        $param['quot'] = implode(',',$param['quot']);
        $param['super'] = implode(',',$param['super']);
        $param['contact'] = implode(',',$param['contact']);
        $param['employee'] = implode(',',$param['employee']);
        $param['distri'] = implode(',',$param['distri']);
        $param['section'] = implode(',',$param['section']);
        $param['stage'] = implode(',',$param['stage']);
        $param['source'] = implode(',',$param['source']);
        $param['gst'] = implode(',',$param['gst']);
        $param['client'] = implode(',',$param['client']);
        $param['quotation_footer'] = implode(',',$param['quotation_footer']);
        $param['task'] = implode(',',$param['task']);
        $param['amc'] = implode(',',$param['amc']);
        $param['member'] = implode(',',$param['member']);
        $param['template'] = implode(',',$param['template']);
        $param['a_keyword'] = implode(',',$param['a_keyword']);
        $param['system_a'] = implode(',',$param['system_a']);
        $param['autoresponder'] = implode(',',$param['autoresponder']);
    
       

        $group=Group::create($param);
//     
        if($group)
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
        $data['data'] = Group::where('id', $id)->first();
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        return view('general.edit_form')->with($data);
    }
    public function update(Request $request, $id)
    {
        // $data = $request->all();
        // unset($data['_token'],$data['_method']);
        // if($request->password)
        // {
        //     $data['show_password'] = $request->password;
        //     $data['password'] = bcrypt($request->password);
        // }else{
        //     unset($data['password']);
        // }
        // $data = User::where('id', $id)->update($data);
        $param = Group::where('id',$id)->first();
         $param['name'] = $request['name'];
       
           $param['pc'] = [
            '0'=>$request->pc_view ?? 0,
            '1'=>$request->pc_add ?? 0,
            '2'=>$request->pc_edit ?? 0,
            '3'=>$request->pc_delete ?? 0,
        ];
        $param['product'] = [
            '0'=>$request->product_view ?? 0,
            '1'=>$request->product_add ?? 0,
            '2'=>$request->product_edit ?? 0,
            '3'=>$request->product_delete ?? 0,
        ];
         $param['lead'] = [
            '0'=>$request->lead_view ?? 0,
            '1'=>$request->lead_add ?? 0,
            '2'=>$request->lead_edit ?? 0,
            '3'=>$request->lead_delete ?? 0,
        ];

         $param['complain'] = [
            '0'=>$request->complain_view ?? 0,
            '1'=>$request->complain_add ?? 0,
            '2'=>$request->complain_edit ?? 0,
            '3'=>$request->complain_delete ?? 0,
        ];

        $param['quot'] = [
            '0'=>$request->quot_view ?? 0,
            '1'=>$request->quot_add ?? 0,
            '2'=>$request->quot_edit ?? 0,
            '3'=>$request->quot_delete ?? 0,
        ];

         $param['super'] = [
            '0'=>$request->super_view ?? 0,
            '1'=>$request->super_add ?? 0,
            '2'=>$request->super_edit ?? 0,
            '3'=>$request->super_delete ?? 0,
        ];

         $param['contact'] = [
            '0'=>$request->contact_view ?? 0,
            '1'=>$request->contact_add ?? 0,
            '2'=>$request->contact_edit ?? 0,
            '3'=>$request->contact_delete ?? 0,
        ];

         $param['employee'] = [
            '0'=>$request->employee_view ?? 0,
            '1'=>$request->employee_add ?? 0,
            '2'=>$request->employee_edit ?? 0,
            '3'=>$request->employee_delete ?? 0,
        ];

         $param['distri'] = [
            '0'=>$request->distri_view ?? 0,
            '1'=>$request->distri_add ?? 0,
            '2'=>$request->distri_edit ?? 0,
            '3'=>$request->distri_delete ?? 0,
        ];

         $param['section'] = [
            '0'=>$request->section_view ?? 0,
            '1'=>$request->section_add ?? 0,
            '2'=>$request->section_edit ?? 0,
            '3'=>$request->section_delete ?? 0,
        ];
         $param['stage'] = [
            '0'=>$request->stage_view ?? 0,
            '1'=>$request->stage_add ?? 0,
            '2'=>$request->stage_edit ?? 0,
            '3'=>$request->stage_delete ?? 0,
        ];
         $param['source'] = [
            '0'=>$request->source_view ?? 0,
            '1'=>$request->source_add ?? 0,
            '2'=>$request->source_edit ?? 0,
            '3'=>$request->source_delete ?? 0,
        ];

         $param['gst'] = [
            '0'=>$request->gst_view ?? 0,
            '1'=>$request->gst_add ?? 0,
            '2'=>$request->gst_edit ?? 0,
            '3'=>$request->gst_delete ?? 0,
        ];

         $param['client'] = [
            '0'=>$request->client_view ?? 0,
            '1'=>$request->client_add ?? 0,
            '2'=>$request->client_edit ?? 0,
            '3'=>$request->client_delete ?? 0,
        ];

         $param['quotation_footer'] = [
            '0'=>$request->footer_view ?? 0,
            '1'=>$request->footer_add ?? 0,
            '2'=>$request->footer_edit ?? 0,
            '3'=>$request->footer_delete ?? 0,
        ];

         $param['task'] = [
            '0'=>$request->task_view ?? 0,
            '1'=>$request->task_add ?? 0,
            '2'=>$request->task_edit ?? 0,
            '3'=>$request->task_delete ?? 0,
        ];

         $param['amc'] = [
            '0'=>$request->amc_view ?? 0,
            '1'=>$request->amc_add ?? 0,
            '2'=>$request->amc_edit ?? 0,
            '3'=>$request->amc_delete ?? 0,
        ];

         $param['member'] = [
            '0'=>$request->member_view ?? 0,
            '1'=>$request->member_add ?? 0,
            '2'=>$request->member_edit ?? 0,
            '3'=>$request->member_delete ?? 0,
        ];
         $param['template'] = [
            '0'=>$request->template_view ?? 0,
            '1'=>$request->template_add ?? 0,
            '2'=>$request->template_edit ?? 0,
            '3'=>$request->template_delete ?? 0,
        ];
         $param['a_keyword'] = [
            '0'=>$request->a_keyword_view ?? 0,
            '1'=>$request->a_keyword_add ?? 0,
            '2'=>$request->a_keyword_edit ?? 0,
            '3'=>$request->a_keyword_delete ?? 0,
        ];
         $param['system_a'] = [
            '0'=>$request->system_a_view ?? 0,
            '1'=>$request->system_a_add ?? 0,
            '2'=>$request->system_a_edit ?? 0,
            '3'=>$request->system_a_delete ?? 0,
        ];
         $param['autoresponder'] = [
            '0'=>$request->autoresponder_view ?? 0,
            '1'=>$request->autoresponder_add ?? 0,
            '2'=>$request->autoresponder_edit ?? 0,
            '3'=>$request->autoresponder_delete ?? 0,
        ];


        $param['pc'] = implode(',',$param['pc']);
        $param['product'] = implode(',',$param['product']);
        $param['lead'] = implode(',',$param['lead']);
        $param['complain'] = implode(',',$param['complain']);
        $param['quot'] = implode(',',$param['quot']);
        $param['super'] = implode(',',$param['super']);
        $param['contact'] = implode(',',$param['contact']);
        $param['employee'] = implode(',',$param['employee']);
        $param['distri'] = implode(',',$param['distri']);
        $param['section'] = implode(',',$param['section']);
        $param['stage'] = implode(',',$param['stage']);
        $param['source'] = implode(',',$param['source']);
        $param['gst'] = implode(',',$param['gst']);
        $param['client'] = implode(',',$param['client']);
        $param['quotation_footer'] = implode(',',$param['quotation_footer']);
        $param['task'] = implode(',',$param['task']);
        $param['amc'] = implode(',',$param['amc']);
         $param['member'] = implode(',',$param['member']);
        $param['template'] = implode(',',$param['template']);
        $param['a_keyword'] = implode(',',$param['a_keyword']);
        $param['system_a'] = implode(',',$param['system_a']);
        $param['autoresponder'] = implode(',',$param['autoresponder']);
    
       $param->save();

        if($param)
        {
            return response()->json(['status' => 'success']);
        }else
        {
            return response()->json(['status' => 'error']);

        }
    }
}
