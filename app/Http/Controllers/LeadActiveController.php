<?php



namespace App\Http\Controllers;

use App\User;

use DataTables;

use App\Lead;

use App\Client;

use App\LeadSource;

use App\Section;

use App\LeadStage;

use App\Employee;

use App\LeadFollowUp;

use App\LeadBasicChild;

use App\LeadBasicMainChild;

use App\LeadBasicDesignChild;

use App\LeadBasicQuotationChild;

use App\Quotation;

use App\SourceName;

use App\Warehouse;

use App\WarehouseInventory;

use App\LeadCommunication;

use App\LeadAssign;

use App\Company;

use App\FollowupLog;

use App\Type;

use App\ProductCategory;

use App\Product;

use Auth;

use DB;

use Mail;

use Illuminate\Http\Request;





class LeadActiveController extends Controller

{

    public function __construct(Lead $s)

    {

        $this->view = 'lead';

        $this->route = 'lead';

        $this->viewName = 'Lead';

    }



    function sendnotification($pushtoken,$title,$body,$urls,$leads_id,$name)

    {

      $firebaseToken = [$pushtoken];

          // dd($firebaseToken);

      $SERVER_API_KEY = env('Employee_server');



      $data = [

       "registration_ids" => $firebaseToken,

       "notification" => [

        "title" => $title,

        "body" => $body,

    ],

    "data" => [

        'urls' => $urls,

        'leads_id' => $leads_id,

        'name' => $name,

    ]

];

$dataString = json_encode($data);



$headers = [

   'Authorization: key=' . $SERVER_API_KEY,

   'Content-Type: application/json',

];

          // dd($headers);



$ch = curl_init();



curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');

curl_setopt($ch, CURLOPT_POST, true);

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);



$response = curl_exec($ch);

          // dd($response);

}



function adminsendnotification($pushtoken,$title,$body,$urls,$leads_id,$name)

{

  $firebaseToken = [$pushtoken];

          // dd($firebaseToken);

  $SERVER_API_KEY = env('Admin_server');



  $data = [

   "registration_ids" => $firebaseToken,

   "notification" => [

    "title" => $title,

    "body" => $body,

],

"data" => [

    'urls' => $urls,

    'leads_id' => $leads_id,

    'name' => $name,

]

];

$dataString = json_encode($data);



$headers = [

   'Authorization: key=' . $SERVER_API_KEY,

   'Content-Type: application/json',

];

          // dd($headers);



$ch = curl_init();



curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');

curl_setopt($ch, CURLOPT_POST, true);

curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);



$response = curl_exec($ch);

          // dd($response);

}   



public function index(Request $request)

{

    // $arr = [];

    // $get_data = Lead::get();

    // foreach ($get_data as $key => $value) {

    //     $check = LeadAssign::where('lead_id',$value->id)->first();

    //     if(!isset($check))

    //     {

    //         array_push($arr,$value->id);

    //     }

    // }

    // dd($arr);

    if(Auth::guard('employee')->check())

    {



        if ($request->ajax()) {

            $data = Lead::with('client','lead_assign.stage','lead_assign.employee','sourcename','source')->where('active',0)->orderBy('date','DESC')->latest();

            $array = [];

            foreach($data as $d)

            {

                $explode = explode(',',$d->employee_id);

                if(in_array(Auth::user()->id,$explode))

                {

                    array_push($array,$d->id);

                }

            }

            $data = LeadAssign::get();

            $arr = [];

            foreach($data as $dt)

            {

                $ex_arr = explode(',', $dt->employee_id);

                if(in_array(Auth::user()->id,$ex_arr))

                {

                    array_push($array,$dt->lead_id);



                }

            }

            $query = Lead::orderBy('date','DESC')->with('client','lead_assign.stage','lead_assign.employee','sourcename','source')

            ->orWhereIn('id',$array)->where('active',0)->latest();

            if($request->employee_id)

            {

               $query->where('employee_id',$request->employee_id);

           }

           if($request->branch_id)

           {

             $query->where('branch_id',$request->branch_id);

         }

         if($request->status)

         {

           $query->where('status',$request->status);

       }

       if($request->source_id)

       {

           $query->where('source_id',$request->source_id);

       }

       if($request->client_id)

       {



           $query->where('client_id',$request->client_id);

       }

       if($request->lead_name)

       {

        $data = $query;

        $data = $data->get();

        $array_id = [];

        foreach($data as $dt)

        {

            $co_co = explode(',', $dt['lead_name']);

            foreach($co_co as $co)

            {

                if($co == $request->lead_name)

                {

                    array_push($array_id, $dt->id);

                }

            }

        }

        $query = $query->whereIn('id',$array_id);

    }

    if($request->company_name)

    {

        $data = $query;

        $data = $data->get();

        $array_id = [];

        foreach($data as $dt)

        {

            $co_co = explode(',', $dt['company_name']);

            foreach($co_co as $co)

            {

                if($co == $request->company_name)

                {

                    array_push($array_id, $dt->id);

                }

            }

        }

        $query = $query->whereIn('id',$array_id);

    }

    if($request->source_name)

    {

       $query->where('source_name',$request->source_name);

   }

   if($request->from_date && $request->to_date)

   {

    $query = $query->where('date','>=',$request->from_date)->where('date','<=',$request->to_date);

}

            // if($request->to_date)

            // {

            //     $query = $query->where('date','<=',$request->to_date);

            // }

$query = $query->latest();

return Datatables::of($query)

->addColumn('action', function ($row) {

// $btn = view('layouts.actionbtnpermission')->with(['id'=>$row->id,'route'=>'lead','per_value'=>'lead'])->render();          



    $btn = '<a style="background: white;" href="'.route('user.lead.edit',$row->id).'" title="Edit details" class="btn btn-sm btn-clean btn-icon btn-icon-md">

    <i style="color: green;" class="la la-edit"></i>

    </a>&nbsp;&nbsp;

  

    <button style="background: white;" onclick="model('.$row->id.')" title="Followup" class="btn btn-sm btn-clean btn-icon btn-icon-md " >

    <i style="color: orange;" class="fa fa-arrow-up"></i>

    </button>&nbsp;&nbsp;';

    $flag = 1;

    $checkers = Quotation::where('lead_id',$row->id)->first();

    if(isset($checkers))

    {

        $flag = 0;

    }

    if($flag){



        $btn .= '<a style="background: white;" href="'. route('user.quotation.create', $row->id).'" title="Quotation" class="btn btn-sm btn-clean btn-icon btn-icon-md"><i style="color: black;" class="flaticon-envelope"></i></a>';

    }

    return $btn;

})

->addColumn('date', function ($row) {

    $ac = '<a href="'.route("user.lead.edit",$row->id).'" style="color: black;">'.date('d-m-Y',strtotime($row->date)).'</a>';

    return $ac;



})

->addColumn('lead_name', function ($row) {

    $exp = explode(',',$row->lead_name);

    $emp = ProductCategory::whereIn('id',$exp)->where('status',1)->get();

    $arr = [];

    foreach ($emp as $key => $value) {

        array_push($arr,$value->name);

    }

    $lead_name = implode(', ',$arr);

    $btn = '<a href="'.route("user.lead.edit",$row->id).'" style="color: black;">'.$lead_name.'</a>';

// return $btn;

    return $btn;

})

->addColumn('company_name', function ($row) {

    $exp = explode(',',$row->company_name);

    $emp = Company::whereIn('id',$exp)->where('status',1)->get();

    $arr = [];

    foreach ($emp as $key => $value) {

        array_push($arr,$value->name);

    }

    $company_name = implode(', ',$arr);

    $btn = '<a href="'.route("user.lead.edit",$row->id).'" style="color: black;">'.$company_name.'</a>';

    return $btn;

})



->addColumn('id', function ($row) {

    $btn = '<a href="'.route("user.lead.edit",$row->id).'" style="color: black;">'.$row->id.'</a>';

    return $btn;

})

->addColumn('branch_id', function ($row) {

    $btn = '<a href="'.route("user.lead.edit",$row->id).'" style="color: black;">'.$row->branch->name.'</a>';



    return $btn;

})

->addColumn('employee_id', function ($row) {

    $leads = LeadAssign::where('lead_id',$row->id)->first();

    if($leads)

    {



        $exp = explode(',',$leads->employee_id);

        $emp = Employee::whereIn('id',$exp)->get();

        $arr = [];

        foreach ($emp as $key => $value) {

            array_push($arr,$value->name);

        }

        $btn = implode(', ',$arr);

    }

    else

    {

        $btn = '';

    }

// dd($btn);

// $btn = '<a href="'.route("user.lead.edit",$row->id).'" style="color: black;">'.$row->employee->name.'</a>';

    return $btn;

// $btn = '<a href="'.route("user.lead.edit",$row->id).'" style="color: black;">'.$row->employee->name.'</a>';

// return $btn;

})

->addColumn('status', function ($row) {

    $btn = '';

    if ($row->status == 1) {

        $btn = 'Pending';

    }elseif ($row->status == 2) {

        $btn = 'Discussion';

    }elseif ($row->status == 3) {

        $btn = 'Rivision';

    }elseif ($row->status == 4) {

        $btn = 'Win';

    }elseif ($row->status == 5) {

        $btn = 'Lost';

    }

    return $btn;

})

->addColumn('followup_status', function ($row) {









    $followup = LeadFollowUp::where('lead_id',$row->id)->where('employee_id',Auth::user()->id)->first();

    if($followup)

    {

        $date = date('d/m/Y', strtotime($followup->date));

        $status = '';

        if ($followup->is_completed == 1) {

            $status = 'Pending';

        }elseif ($followup->is_completed == 2) {

            $status = 'Discussion';

        }elseif ($followup->is_completed == 3) {

            $status = 'Rivision';

        }elseif ($followup->is_completed == 4) {

            $status = 'Win';

        }elseif ($followup->is_completed == 5) {

            $status = 'Lost';

        }

    }

    else

    {

     $date = '-';

     $status = '-';

 }



                       // is_completed

 $data = $status;









 $btn = $data;



 return $btn;

})

->rawColumns(['status','numbers','action','client_id','source_id','lead_name','branch_id','id','employee_id','source_id','date','source_name','followup_status','company_name'])

->make(true);

} 

}

else

{

    if ($request->ajax()) {



        if(Auth::user()->role == 2)

        {



            $query = Lead::orderBy('date','DESC')->with('client','lead_assign.stage','lead_assign.employee','sourcename','source')->where('branch_id',Auth::user()->id)->where('active',0);

            if($request->employee_id)

            {

                $data = Lead::with('client','lead_assign.stage','lead_assign.employee','sourcename','source')->where('active',0)->orderBy('date','DESC')->latest();

                $array = [];

                foreach($data as $d)

                {

                    $explode = explode(',',$d->employee_id);

                    if(in_array($request->employee_id,$explode))

                    {

                        array_push($array,$d->id);

                    }

                }

                $data = LeadAssign::get();

                $arr = [];

                foreach($data as $dt)

                {

                    $ex_arr = explode(',', $dt->employee_id);

                    if(in_array($request->employee_id,$ex_arr))

                    {

                        array_push($array,$dt->lead_id);



                    }

                }

                $query = $query->whereIn('id',$array);

            }

            if($request->branch_id)

            {

                $query = $query->where('branch_id',$request->branch_id);

            }

            if($request->status)

            {

                $query = $query->where('status',$request->status);

            }

            if($request->source_id)

            {

               $query->where('source_id',$request->source_id);

           }

           if($request->client_id)

           {



               $query->where('client_id',$request->client_id);

           }

           if($request->lead_name)

           {

            $data = $query;

            $data = $data->get();

            $array_id = [];

            foreach($data as $dt)

            {

                $co_co = explode(',', $dt['lead_name']);

                foreach($co_co as $co)

                {

                    if($co == $request->lead_name)

                    {

                        array_push($array_id, $dt->id);

                    }

                }

            }

            $query = $query->whereIn('id',$array_id);

        }

        if($request->company_name)

        {

            $data = $query;

            $data = $data->get();

            $array_id = [];

            foreach($data as $dt)

            {

                $co_co = explode(',', $dt['company_name']);

                foreach($co_co as $co)

                {

                    if($co == $request->company_name)

                    {

                        array_push($array_id, $dt->id);

                    }

                }

            }

            $query = $query->whereIn('id',$array_id);

        }

        if($request->source_name)

        {

           $query->where('source_name',$request->source_name);

       }

       if($request->from_date)

       {

        $query = $query->where('date','>=',$request->from_date);

    }

    if($request->to_date)

    {

        $query = $query->where('date','<=',$request->to_date);

    }

    $query = $query->latest();

}else{

    $query = Lead::orderBy('date','DESC')->with('client','lead_assign.stage','lead_assign.employee','sourcename','source')->where('active',0)->latest();

    if($request->employee_id)

    {

        $data = Lead::with('client','lead_assign.stage','lead_assign.employee','sourcename','source')->where('active',0)->orderBy('date','DESC')->latest();

        $array = [];

        foreach($data as $d)

        {

            $explode = explode(',',$d->employee_id);

            if(in_array($request->employee_id,$explode))

            {

                array_push($array,$d->id);

            }

        }

        $data = LeadAssign::get();

        $arr = [];

        foreach($data as $dt)

        {

            $ex_arr = explode(',', $dt->employee_id);

            if(in_array($request->employee_id,$ex_arr))

            {

                array_push($array,$dt->lead_id);



            }

        }

        $query = $query->whereIn('id',$array);



    }

    if($request->branch_id)

    {

        $query = $query->where('branch_id',$request->branch_id);

    }

    if($request->status)

    {

        $query = $query->where('status',$request->status);

    }

    if($request->source_id)

    {

       $query->where('source_id',$request->source_id);

   }

   if($request->client_id)

   {



       $query->where('client_id',$request->client_id);

   }

   if($request->source_name)

   {

       $query->where('source_name',$request->source_name);

   }



   if($request->lead_name)

   {

    $data = $query;

    $data = $data->get();

    $array_id = [];

    foreach($data as $dt)

    {

        $co_co = explode(',', $dt['lead_name']);

        foreach($co_co as $co)

        {

            if($co == $request->lead_name)

            {

                array_push($array_id, $dt->id);

            }

        }

    }

    $query = $query->whereIn('id',$array_id);

}



if($request->company_name)

{

    $data = $query;

    $data = $data->get();

    $array_id = [];

    foreach($data as $dt)

    {

        $co_co = explode(',', $dt['company_name']);

        foreach($co_co as $co)

        {

            if($co == $request->company_name)

            {

                array_push($array_id, $dt->id);

            }

        }

    }

    $query = $query->whereIn('id',$array_id);

}

if($request->from_date)

{

    $query = $query->where('date','>=',$request->from_date);

}

if($request->to_date)

{

    $query = $query->where('date','<=',$request->to_date);

}

$query = $query->latest();



}

return Datatables::of($query)

->addColumn('action', function ($row) {

// $btn = view('layouts.actionbtnpermission')->with(['id'=>$row->id,'route'=>'lead','per_value'=>'lead'])->render(); 

    $btn = '';

   

    $btn .='
     <a style="background: white;" href="'.route('user.lead.active',$row->id).'" title="Active Lead" class="btn btn-sm btn-clean btn-icon btn-icon-md">

    <i style="color: black;" class="flaticon2-accept"></i>

    </a>&nbsp;&nbsp;';




    return $btn;

})



->addColumn('date', function ($row) {

    if($row->date)

    {



        $ac = '<a href="'.route("user.lead.edit",$row->id).'" style="color: black;">'.date('d-m-Y',strtotime($row->date)).'</a>';

    }

    else

    {

        $ac = '-';

    }

    return $ac;



})



->addColumn('lead_name', function ($row) {

    $exp = explode(',',$row->lead_name);

    $emp = ProductCategory::whereIn('id',$exp)->where('status',1)->get();

    $arr = [];

    foreach ($emp as $key => $value) {

        array_push($arr,$value->name);

    }

    $lead_name = implode(', ',$arr);

    $btn = '<a href="'.route("user.lead.edit",$row->id).'" style="color: black;">'.$lead_name.'</a>';

    return $btn;

})



->addColumn('company_name', function ($row) {

    $exp = explode(',',$row->company_name);

    $emp = Company::whereIn('id',$exp)->where('status',1)->get();

    $arr = [];

    foreach ($emp as $key => $value) {

        array_push($arr,$value->name);

    }

    $company_name = implode(', ',$arr);

    $btn = '<a href="'.route("user.lead.edit",$row->id).'" style="color: black;">'.$company_name.'</a>';

    return $btn;

})



->addColumn('id', function ($row) {

    $btn = '<a href="'.route("user.lead.edit",$row->id).'" style="color: black;">'.$row->id.'</a>';

    return $btn;

})

->addColumn('branch_id', function ($row) {

    $btn = '<a href="'.route("user.lead.edit",$row->id).'" style="color: black;">'.$row->branch->name.'</a>';



    return $btn;

})





->addColumn('employee_id', function ($row) {

    if(Auth::user()->role == 1)

    {

      $leads = LeadAssign::where('lead_id',$row->id)->first();

      // dd($leads);

      if($leads)

      {

              $exp = explode(',',$leads->employee_id);

      $emp = Employee::whereIn('id',$exp)->get();

      $arr = [];

                    // foreach ($emp as $key => $value) {

                    //     array_push($arr,$value->name);

                    // }

      foreach ($emp as $keys => $values) {





        $name = LeadBasicMainChild::where('lead_id',$row->id)->where('employee_id',$values->id)->first();

        if($name)

        {

            $n = $name->name;

        }

        else

        {

            $n = '';

        }



        $followup = LeadFollowUp::where('lead_id',$row->id)->where('employee_id',$values->id)->first();

        if($followup)

        {

            $date = date('d/m/Y', strtotime($followup->date));

            $status = '';

            if ($followup->is_completed == 1) {

                $status = 'Pending';

            }elseif ($followup->is_completed == 2) {

                $status = 'Discussion';

            }elseif ($followup->is_completed == 3) {

                $status = 'Rivision';

            }elseif ($followup->is_completed == 4) {

                $status = 'Win';

            }elseif ($followup->is_completed == 5) {

                $status = 'Lost';

            }

        }

        else

        {

         $date = '-';

         $status = '-';

     }

                       // is_completed

     $data = $values->name ;



     array_push($arr,$data);



 }

     $btn = implode(', <br>',$arr);

      }

      else

      {

        $btn = '-';

      }

    

}

else

{

 $leads = LeadAssign::where('lead_id',$row->id)->first();

 if($leads)

 {

   $exp = explode(',',$leads->employee_id);

   $emp = Employee::whereIn('id',$exp)->get();

   $arr = [];

   foreach ($emp as $key => $value) {

    array_push($arr,$value->name);

}

$btn = implode(', <br>',$arr);

 }

 else

 {

    $btn = '';

 }



}









// dd($btn);



// $btn = '<a href="'.route("user.lead.edit",$row->id).'" style="color: black;">'.$row->employee->name.'</a>';

return $btn;

})

->addColumn('status', function ($row) {

    $btn = '';

    if ($row->status == 1) {

        $btn = 'Pending';

    }elseif ($row->status == 2) {

        $btn = 'Discussion';

    }elseif ($row->status == 3) {

        $btn = 'Rivision';

    }elseif ($row->status == 4) {

        $btn = 'Win';

    }elseif ($row->status == 5) {

        $btn = 'Lost';

    }

    return $btn;

})

->addColumn('followup_status', function ($row) {

   $leads = LeadAssign::where('lead_id',$row->id)->first();

   $exp = explode(',',$leads->employee_id);

   $emp = Employee::whereIn('id',$exp)->get();

   $arr = [];

                    // foreach ($emp as $key => $value) {

                    //     array_push($arr,$value->name);

                    // }

   foreach ($emp as $keys => $values) {









    $followup = LeadFollowUp::where('lead_id',$row->id)->where('employee_id',$values->id)->first();

    if($followup)

    {

        $date = date('d/m/Y', strtotime($followup->date));

        $status = '';

        if ($followup->is_completed == 1) {

            $status = 'Pending';

        }elseif ($followup->is_completed == 2) {

            $status = 'Discussion';

        }elseif ($followup->is_completed == 3) {

            $status = 'Rivision';

        }elseif ($followup->is_completed == 4) {

            $status = 'Win';

        }elseif ($followup->is_completed == 5) {

            $status = 'Lost';

        }

    }

    else

    {

     $date = '-';

     $status = '-';

 }

                       // is_completed

 $data = $values->name .' - ' .$status;

 array_push($arr,$data);

}



$btn = implode(', <br>',$arr);



return $btn;

})

->rawColumns(['numbers','action','client_id','source_id','lead_name','branch_id','id','employee_id','source_id','date','source_name','followup_status','company_name'])

->make(true);

}

}



$data['product'] = Product::get();

$data['stage'] = LeadStage::get();

if(Auth::user()->role == 1)

{

    $data['employee'] = Employee::get();



}

if(Auth::guard('employee')->check())

{

   $data['source'] = LeadSource::where('status',1)->where('branch_id',Auth::user()->branch_id)->get();

   $data['sourcename'] = SourceName::where('status',1)->where('branch_id',Auth::user()->branch_id)->get();

   $data['client'] = Client::where('branch_id',Auth::user()->branch_id)->get();

   $data['productcategory'] = ProductCategory::where('branch_id',Auth::user()->branch_id)->get();

   $data['company'] = Company::where('branch_id',Auth::user()->branch_id)->get();

}

$data['source'] = LeadSource::get();

$data['branch'] = User::where('role',2)->where('status',1)->get();

$data['employee'] = Employee::where('status',1)->whereIn('role',['1','2'])->get();

if(Auth::user()->role == 2)

{

    $data['employee'] = Employee::where('status',1)->where('branch_id',Auth::user()->id)->whereIn('role',['1','2'])->get();

    $data['source'] = LeadSource::where('status',1)->where('branch_id',Auth::user()->id)->get();

    $data['sourcename'] = SourceName::where('status',1)->where('branch_id',Auth::user()->id)->get();

    $data['client'] = Client::where('branch_id',Auth::user()->id)->get();

    $data['productcategory'] = ProductCategory::where('branch_id',Auth::user()->id)->get();

    $data['company'] = Company::where('branch_id',Auth::user()->id)->get();



}



return view('lead.activeindex')->with($data);

}


















public function active($id)
{
    $lead = Lead::where('id',$id)->first();
    $lead->active = 1;
    $lead->save();

     return redirect()->back();

}



}

