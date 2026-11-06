<?php
namespace App\Http\Controllers;
use App\User;
use App\Client;
use App\LeadSource;
use App\ClientDocument;
use App\Product;
use App\Lead;
use App\Employee;
use App\Landmark;
use App\ProductCategory;
use App\Country;
use App\State;
use App\City;
use Illuminate\Http\Request;
use Auth;
use DataTables;
class ClientController extends Controller
{
    public function __construct(Client $s)
    {
        $this->view = 'client';
        $this->route = 'client';
        $this->viewName = 'Client';
    }   
    public function index(Request $request)
    {
        if(Auth::guard('employee')->check())
        {
            if ($request->ajax()) {
                $query = Client::where('branch_id',Auth::user()->branch_id)->latest();
                if($request->landmark_id)
                {
                    $query = $query->where('landmark_id',$request->landmark_id)->latest();
                }
                if($request->is_archive == 1)
                {
                    $query = $query->where('is_archive',1);
                }
                else if($request->is_archive == 0)
                {
                    $query = $query->where('is_archive',0);
                }
                elseif($request->is_archive == 2) 
                {
                    $query = $query;
                }
                else
                {
                    $query = Client::where('branch_id',Auth::user()->branch_id)->latest();
                }
                return Datatables::of($query)

                ->addColumn('action', function ($row) {
                    $btn = '<a style="background: transparent;" href="'.route('user.client.edit',$row->id).'" title="Edit details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: green;" class="la la-edit"></i>
                    </a>&nbsp;&nbsp;&nbsp;';

                    $btn .= '&nbsp;&nbsp;<a style="background: white;" href="'.route('user.client.show',$row->id).'"  title="View Details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-eye"></i></a>&nbsp;&nbsp;&nbsp;';

                    return $btn;
                })
                ->addColumn('singlecheckbox', function ($row) {
                    $schk = view('layouts.singlecheckbox')->with(['id' => $row->id , 'status'=>$row->status])->render();
                    return $schk;
                })
                ->addColumn('is_archive', function ($row) {
                    if($row->is_archive == 1)
                    {
                        $is_archive = 'Is Archive';
                    }else{
                        $is_archive = '-';
                    }
                    return $is_archive;
                })
                ->addColumn('branch_id', function ($row) {
                    $btn = $row->branch->name;
                    return $btn;
                })
                ->addColumn('landmark_id', function ($row) {
                    $btn = Landmark::where('id',$row->landmark_id)->value('name');
                    return $btn;
                })
                ->rawColumns(['numbers','action','singlecheckbox','is_archive','branch_id','landmark_id'])
                ->make(true);
            } 
        }
        else
        {
            if ($request->ajax()) {
                $query = Client::latest();
                if(Auth::user()->role == 2)
                {
                    $query = Client::where('branch_id',Auth::user()->id)->latest();
                }
                if($request->branch_id)
                {
                    $query = $query->where('branch_id',$request->branch_id)->latest();
                }
                if($request->employee_id)
                {
                    $query = $query->where('employee_id',$request->employee_id)->latest();
                }
                if($request->landmark_id)
                {
                    $query = $query->where('landmark_id',$request->landmark_id)->latest();
                }
                if($request->is_archive == 1)
                {
                    $query = $query->where('is_archive',1);
                }
                else if($request->is_archive == 0)
                {
                    $query = $query->where('is_archive',0);
                }
                elseif($request->is_archive == 2) 
                {
                    $query = $query;
                }
                else
                {
                    $query = Client::where('branch_id',Auth::user()->branch_id)->latest();
                }
                return Datatables::of($query)

                ->addColumn('action', function ($row) {
                    $btn = '<a style="background: transparent;" href="'.route('user.client.edit',$row->id).'" title="Edit details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: green;" class="la la-edit"></i>
                    </a>&nbsp;&nbsp;&nbsp;';

                    $btn .= '&nbsp;&nbsp;<a style="background: white;" href="'.route('user.client.show',$row->id).'"  title="View Details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-eye"></i></a>&nbsp;&nbsp;&nbsp;';

                    if(Auth::user()->role == 1)
                    {

                        $btn .= '<a href="'.route('user.delete.client',$row->id).'" onclick="return confirm(`Are you sure?`)" style="background: white;" title="Delete details" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: red;" class="la la-trash"></i></a>';
                    }
                    return $btn;
                })
                ->addColumn('singlecheckbox', function ($row) {
                    $schk = view('layouts.singlecheckbox')->with(['id' => $row->id , 'status'=>$row->status])->render();
                    return $schk;
                })
                ->addColumn('is_archive', function ($row) {
                    if($row->is_archive == 1)
                    {
                        $is_archive = 'Is Archive';
                    }else{
                        $is_archive = '-';
                    }
                    return $is_archive;
                })
                ->addColumn('branch_id', function ($row) {
                    $btn = $row->branch->name;
                    return $btn;
                })
                ->addColumn('landmark_id', function ($row) {
                    $btn = Landmark::where('id',$row->landmark_id)->value('name');
                    return $btn;
                })

                ->rawColumns(['numbers','action','singlecheckbox','is_archive','branch_id','landmark_id'])
                ->make(true);
            } 
        }
        $data['branch'] = User::where('role',2)->where('status',1)->get();
        if(Auth::user()->role == 1)
        {
            $data['employee'] = Employee::get();
            $data['landmark'] = Landmark::get();
        }
        else
        {
            $data['employee'] = Employee::where('branch_id',Auth::user()->id)->get();
            $data['landmark'] = Landmark::where('branch_id',Auth::user()->id)->get();
        }
        return view('client.index')->with($data);
    }
    public function create()
    {
        $data['title'] = 'Add ' . $this->viewName;
        $data['module'] = $this->viewName;
        $data['resourcePath'] = $this->view;
        $data['leadsource'] = LeadSource::get();
        $data['branch'] = User::where('role',2)->get();
        if(Auth::user()->role == 1)
        {
            $data['employee'] = Employee::get();
            $data['landmark'] = Landmark::get();
        }
        else
        {
            $data['employee'] = Employee::where('branch_id',Auth::user()->id)->get();
            $data['landmark'] = Landmark::where('branch_id',Auth::user()->id)->get();
        }
        if(Auth::guard('employee')->check())
        {
           $emp = Employee::where('id',Auth::user()->id)->value('branch_id');
           $data['landmark'] = Landmark::where('branch_id',$emp)->get();
       }
       $data['country'] = Country::get();
       return view('general.add_form')->with($data);
   }
   public function store(Request $request)
   {
    $client = new Client();
    $client->member = $request->member;
    if(Auth::guard('user')->check())
    {
        if(Auth::user()->role == 1)
        {
            $client->branch_id = $request->branch_id;
            $client->employee_id = $request->employee_id;
            $client->landmark_id = $request->landmark_id;
        }
        else
        {
            $client->branch_id = Auth::user()->id;
            $client->employee_id = $request->employee_id;
            $client->landmark_id = $request->landmark_id;
        }
    }
    else
    {
        $client->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
        $client->employee_id = Auth::user()->id;
        $client->landmark_id = $request->landmark_id;
    }
    $check = Client::where('mobile_no',$request->mobile_no)->where('branch_id',$client->branch_id)->exists();
    if($check){
        return response()->json(['status' => 'duplicate']);
    }
    $client->type = $request->type;
    $client->member_code = $request->member_code;
    $client->company = $request->company;
    $client->address = $request->address;
    $client->country_id = $request->country_id;
    $client->state_id = $request->state_id;
    $client->city_id = $request->city_id;
        // $client->city = $request->city;
    $client->other_mobile_no = $request->other_mobile_no;
    $client->email = $request->email;
    $client->referred_type = $request->referred_type;
    $client->dob = $request->dob;
    $client->mobile_no = $request->mobile_no;
    $client->save();
    if($client)
    {
        return response()->json(['status' => 'success','data'=>$client]);
    }else
    {
        return response()->json(['status' => 'error']);
    }
}
public function clientdocument(Request $request)
{
    $clientdocument = ClientDocument::where('client_id',$request->client_id)->first();
    if($clientdocument)
    {
        $clientdocument->client_id = $request->client_id;
        $clientdocument->product_id = $request->product_id;
        $clientdocument->lead_id = $request->lead_id;
        $clientdocument->distributor_id = $request->distributor_id;
        $clientdocument->status = 1;
        // $clientdocument->tracking_no = $request->tracking_no;
        // $clientdocument->tracking_link = $request->tracking_link;
        $clientdocument->date = $request->date;
        $clientdocument->remark = $request->remark;
        $clientdocument->aadhar_card = $request->aadhar_card;
        if($request->aadhar_img)
        {
            $imageName = time().'.'.$request->aadhar_img->getClientOriginalExtension();
            $request->aadhar_img->move(public_path('aadharimage/'), $imageName);
            $clientdocument['aadhar_img'] = $imageName;
        }
        $clientdocument->pan_card = $request->pan_card;
        if($request->pan_img)
        {
            $imageName = time().'.'.$request->pan_img->getClientOriginalExtension();
            $request->pan_img->move(public_path('panimage/'), $imageName);
            $clientdocument['pan_img'] = $imageName;
        }
        // $clientdocument->cancelled_cheque = $request->cancelled_cheque;
        // if($request->cancelledcheque_img)
        // {
        //     $imageName = time().'.'.$request->cancelledcheque_img->getClientOriginalExtension();
        //     $request->cancelledcheque_img->move(public_path('cancelledchequeimage/'), $imageName);
        //     $clientdocument['cancelledcheque_img'] = $imageName;
        // }
        // $clientdocument->rtgs_details = $request->rtgs_details;
        // if($request->rtgs_img)
        // {
        //     $imageName = time().'.'.$request->rtgs_img->getClientOriginalExtension();
        //     $request->rtgs_img->move(public_path('rtgsimage/'), $imageName);
        //     $clientdocument['rtgs_img'] = $imageName;
        // }
        // $clientdocument->form_submission = $request->form_submission;
        // if($request->form_submission_img)
        // {
        //     $imageName = time().'.'.$request->form_submission_img->getClientOriginalExtension();
        //     $request->form_submission_img->move(public_path('formsubmissionimage/'), $imageName);
        //     $clientdocument['form_submission_img'] = $imageName;
        // }
        // $clientdocument->machine_delivered = $request->machine_delivered;
        // if($request->machine_delivered_img)
        // {
        //     $imageName = time().'.'.$request->machine_delivered_img->getClientOriginalExtension();
        //     $request->machine_delivered_img->move(public_path('machinedeliveredimage/'), $imageName);
        //     $clientdocument['machine_delivered_img'] = $imageName;
        // }
        $clientdocument->gst = $request->gst;
        if($request->gst_img)
        {
            $imageName = time().'.'.$request->gst_img->getClientOriginalExtension();
            $request->gst_img->move(public_path('gstimage/'), $imageName);
            $clientdocument['gst_img'] = $imageName;
        }
        $clientdocument->save();
    }
    else
    {
        $clientdocument = new ClientDocument();
        $clientdocument->client_id = $request->client_id;
        $clientdocument->product_id = $request->product_id;
        $clientdocument->lead_id = $request->lead_id;
        $clientdocument->distributor_id = $request->distributor_id;
        $clientdocument->status = $request->status;
        $clientdocument->tracking_no = $request->tracking_no;
        $clientdocument->tracking_link = $request->tracking_link;
        $clientdocument->date = $request->date;
        $clientdocument->remark = $request->remark;
        $clientdocument->aadhar_card = $request->aadhar_card;
        if($request->aadhar_img)
        {
            $imageName = time().'.'.$request->aadhar_img->getClientOriginalExtension();
            $request->aadhar_img->move(public_path('aadharimage/'), $imageName);
            $clientdocument['aadhar_img'] = $imageName;
        }
        $clientdocument->pan_card = $request->pan_card;
        if($request->pan_img)
        {
            $imageName = time().'.'.$request->pan_img->getClientOriginalExtension();
            $request->pan_img->move(public_path('panimage/'), $imageName);
            $clientdocument['pan_img'] = $imageName;
        }
        $clientdocument->cancelled_cheque = $request->cancelled_cheque;
        if($request->cancelledcheque_img)
        {
            $imageName = time().'.'.$request->cancelledcheque_img->getClientOriginalExtension();
            $request->cancelledcheque_img->move(public_path('cancelledchequeimage/'), $imageName);
            $clientdocument['cancelledcheque_img'] = $imageName;
        }
        $clientdocument->rtgs_details = $request->rtgs_details;
        if($request->rtgs_img)
        {
            $imageName = time().'.'.$request->rtgs_img->getClientOriginalExtension();
            $request->rtgs_img->move(public_path('rtgsimage/'), $imageName);
            $clientdocument['rtgs_img'] = $imageName;
        }
        $clientdocument->form_submission = $request->form_submission;
        if($request->form_submission_img)
        {
            $imageName = time().'.'.$request->form_submission_img->getClientOriginalExtension();
            $request->form_submission_img->move(public_path('formsubmissionimage/'), $imageName);
            $clientdocument['form_submission_img'] = $imageName;
        }
        $clientdocument->machine_delivered = $request->machine_delivered;
        if($request->machine_delivered_img)
        {
            $imageName = time().'.'.$request->machine_delivered_img->getClientOriginalExtension();
            $request->machine_delivered_img->move(public_path('machinedeliveredimage/'), $imageName);
            $clientdocument['machine_delivered_img'] = $imageName;
        }
        $clientdocument->gst = $request->gst;
        if($request->gst_img)
        {
            $imageName = time().'.'.$request->gst_img->getClientOriginalExtension();
            $request->gst_img->move(public_path('gstimage/'), $imageName);
            $clientdocument['gst_img'] = $imageName;
        }
        $clientdocument->save();
    }
    if($clientdocument)
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
    $data['data'] = Client::where('id', $id)->first();
    $data['clientdocument'] = ClientDocument::where('client_id',$id)->first();
    $data['module'] = $this->viewName;
    $data['resourcePath'] = $this->view;
    $data['leadsource'] = LeadSource::get();
    $data['branch'] = User::where('role',2)->get();
    $data['country'] = Country::get();
    $data['state'] = State::where('country_id',$data['data']->country_id)->get();
    $data['city'] = City::where('state_id',$data['data']->state_id)->get();
    if(Auth::user()->role == 1)
    {
        $data['employee'] = Employee::get();
        $data['landmark'] = Landmark::get();
    }
    else
    {
        $data['employee'] = Employee::where('branch_id',Auth::user()->id)->get();
        $data['landmark'] = Landmark::where('branch_id',Auth::user()->id)->get();
    }
    if(Auth::guard('employee')->check())
    {
       $emp = Employee::where('id',Auth::user()->id)->value('branch_id');
       $data['landmark'] = Landmark::where('branch_id',$emp)->get();
   }
   return view('general.edit_form')->with($data);
}
public function update(Request $request, $id)
{
    $client = Client::where('id',$id)->first();
    if(Auth::guard('user')->check())
    {
        if(Auth::user()->role == 1)
        {
                // $client->branch_id = $request->branch_id;
            $client->employee_id = $request->employee_id;
            $client->landmark_id = $request->landmark_id;
        }
        else
        {
                // $client->branch_id = Auth::user()->id;
            $client->employee_id = $request->employee_id;
            $client->landmark_id = $request->landmark_id;
        }
    }
    else
    {
            // $client->branch_id = Employee::where('id',Auth::user()->id)->value('branch_id');
        $client->employee_id = Auth::user()->id;
        $client->landmark_id = $request->landmark_id;
    }
    $check = Client::where('mobile_no',$request->mobile_no)->where('branch_id',$client->branch_id)->where('id','!=',$id)->exists();
    if($check){
        return response()->json(['status' => 'duplicate']);
    }
    $client->type = $request->type;
    $client->member = $request->member;
    $client->member_code = $request->member_code;
    $client->company = $request->company;
    $client->address = $request->address;
        // $client->city = $request->city;
    $client->country_id = $request->country_id;
    $client->state_id = $request->state_id;
    $client->city_id = $request->city_id;
    $client->mobile_no = $request->mobile_no;
    $client->other_mobile_no = $request->other_mobile_no;
    $client->email = $request->email;
    $client->referred_type = $request->referred_type;
    $client->dob = $request->dob;
    $user = Client::where('id', $id)->first();
// if($request->mobile_no != $user->mobile_no)
// {
//     $check_email_new = Client::where('mobile_no',$client['mobile_no'])->first();
//     if(isset($check_email_new))
//     {
//         return response()->json(['status'=>'duplicate']);
//     }
// }
    $client->save();
    if($client)
    {
        return response()->json(['status' => 'success']);
    }else
    {
        return response()->json(['status' => 'error']);
    }
}
public function getemployee(Request $request)
{
    $data = Employee::where('branch_id',$request->branch_id)->get();
    $html1 = "<option value=''>Select</option>";
    foreach ($data as $key => $value) {
        $html1 .= "<option value=".$value->id.">".$value->name."</option>";
    }

    $data = Landmark::where('branch_id',$request->branch_id)->get();
    $html2 = "<option value=''>Select</option>";
    foreach ($data as $key => $value) {
        $html2 .= "<option value=".$value->id.">".$value->name."</option>";
    }

    return response()->json(['data1'=>$html1 ,'data2'=>$html2]);
}
public function delete($id)
{
    $data = Client::where('id',$id)->delete();
    return back();
}
public function show($id)
{
    $data['title'] = 'View ' . $this->viewName;
    $data['data'] = Client::where('id', $id)->first();
    $data['clientdocument'] = ClientDocument::where('client_id',$id)->first();
    $data['module'] = $this->viewName;
    $data['resourcePath'] = $this->view;
    $data['leadsource'] = LeadSource::get();
    $data['client'] = Client::get();
    $data['branch'] = User::where('role',2)->get();
    $data['branchname'] = User::where('id',$data['data']->branch_id)->value('name');
    $data['emp'] = Employee::where('id',$data['data']->employee_id)->value('name');
    $data['lan'] = Landmark::where('id',$data['data']->landmark_id)->value('name');

    if(Auth::user()->role == 1)
    {
        $data['employee'] = Employee::get();
        $data['landmark'] = Landmark::get();
    }
    else
    {
        $data['employee'] = Employee::where('branch_id',Auth::user()->id)->get();
        $data['landmark'] = Landmark::where('branch_id',Auth::user()->id)->get();
    }
    if(Auth::guard('employee')->check())
    {
       $emp = Employee::where('id',Auth::user()->id)->value('branch_id');
       $data['landmark'] = Landmark::where('branch_id',$emp)->get();
   }

   return view('client.show')->with($data);
}
}