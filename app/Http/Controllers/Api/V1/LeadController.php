<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use JWTAuth;
use App\AMC;
use App\Task;
use App\LeadCommunication;
use App\AMCChild;
use App\LeadBasicMainChild;
use App\Lead;
use App\LeadFollowUp;
use App\ProductCategory;
use App\QuotationFooter;
use App\GST;
use App\Employee;
use App\LeadAssign;
use App\ContactMaster;
use DB;
use App\Type;
use App\Leave;
use App\Product;
use App\LeadBasicDesignChild;
use App\LeadBasicChild;
use JWTAuthException;
use App\User;
use App\Client;
use App\Quotation;
use App\QuotationChild;
use App\SourceName;
use App\Company;
use App\WarehouseInventory;
use Carbon\Carbon;
use Mail;

class LeadController extends Controller
{

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
                'name' => $name
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
public function getgst()
{
    $gst = GST::get();

    if(JwTAuth::user()->role == 2)
    {
       $gst = GST::where('branch_id',JWTAuth::user()->id)->get();
   }
   return response()->json(['success'=>1,'data' => $gst]);
}

public function creategst(Request $request)
{
    $this->validate($request, [
        'gst' => 'required',
    ]);
    $gst = new GST();
    $gst->gst = $request->gst;
    $gst->save();

    return response()->json(['success'=>1,'message' => 'GST Created Successfully.']);
}

public function updategst(Request $request)
{
    $this->validate($request, [
        'id' => 'required',
        'gst' => 'required',
    ]);
    $gst = GST::where('id',$request->id)->first();
    $gst->gst = $request->gst;
    $gst->save();
    return response()->json(['success'=>1,'message' => 'GST Updated Successfully.']);
}

public function deletegst(Request $request)
{
    $this->validate($request, [
        'id' => 'required',
    ]);
    $gst = GST::where('id',$request->id)->delete();
    return response()->json(['success'=>1,'message' => 'GST Deleted Successfully.']);
}

public function statusgst(Request $request)
{
    $this->validate($request, [
        'id' => 'required',
        'status' => 'required',
    ]);
    $gst = GST::where('id',$request->id)->first();
    $gst->status = $request->status;
    $gst->save();

    return response()->json(['success'=>1,'message' => 'Status Updated Successfully']);

}

public function getquotationfooter()
{
    $quotationfooter = QuotationFooter::get();

    return response()->json(['success'=>1,'data' => $quotationfooter]);

}

public function quotationfootercreate(Request $request)
{
    $this->validate($request, [
        'name' => 'required',
    ]);
    $quotationfooter = new QuotationFooter();
    $quotationfooter->name = $request->name;
    $quotationfooter->template = $request->template;
    $quotationfooter->save();
    return response()->json(['success'=>1,'message' => 'Quotation Footer Created Successfully']);
}

public function quotationfooterupdate(Request $request)
{
    $this->validate($request, [
        'id' => 'required',
        'name' => 'required',
    ]);
    $quotationfooter = QuotationFooter::where('id',$request->id)->first();
    $quotationfooter->name = $request->name;
    $quotationfooter->template = $request->template;
    $quotationfooter->save();
    return response()->json(['success'=>1,'message' => 'Quotation Footer Updated Successfully']);
}

public function quotationfooterdelete(Request $request)
{
    $this->validate($request, [
        'id' => 'required',

    ]);

    $quotationfooter = QuotationFooter::where('id',$request->id)->delete();


    return response()->json(['success'=>1,'message' => 'Quotation Footer Deleted Successfully']);
}

public function quotationfooterstatus(Request $request)
{
    $this->validate($request, [
        'id' => 'required',
        'status' => 'required',
    ]);

    $quotationfooter = QuotationFooter::where('id',$request->id)->first();
    $quotationfooter->status = $request->status;

    $quotationfooter->save();

    return response()->json(['success'=>1,'message' => 'Status Updated Successfully']);
}

public function getlead()
{

    if(JWTAuth::user()->role == 1)
    {
            // $lead = Lead::with('branch','employee','client','lead_assign','leadbasicchild','leadbasicchild.productcategory','leadbasicchild.company','leadbasicchild.product','leadbasicchild.warehouse','lead_assign.stage','leadbasicchild.mainchild','leadbasicchild.mainchild.employees','basicdesign','lead_followup','lead_followup.types')->latest()->get();
      $lead = Lead::with('branch','employee','client','lead_assign','source','sourcename','mainchild','mainchild.leadbasicchild','mainchild.leadbasicchild.productcategory','mainchild.leadbasicchild.company','mainchild.leadbasicchild.product','mainchild.leadbasicchild.warehouse','lead_assign.stage','mainchild.employees','basicdesign','lead_followup','lead_followup.employee','lead_followup.types')->latest()->get();
      $array = array();
      foreach ($lead as $key => $value) {
         $param = explode(',', $value->lead_name);
         $value->lead_names_array = ProductCategory::whereIn('id',$param)->get();
         $employees = explode(',', $value->lead_assign->employee_id);
         $value->lead_assign_employee = Employee::whereIn('id',$employees)->get();
         
         $quotation = Quotation::where('lead_id',$value->id)->first();
         if($quotation)
         {
            $value->quot_status = 1;
        }
        else
        {
            $value->quot_status = 0;
        }

        

    }

}
else if(JWTAuth::user()->role == 2)
{
            // $lead = Lead::with('branch','employee','client','lead_assign','leadbasicchild','leadbasicchild.productcategory','leadbasicchild.company','leadbasicchild.product','leadbasicchild.warehouse','lead_assign.stage','leadbasicchild.mainchild','leadbasicchild.mainchild.employees','basicdesign','lead_followup','lead_followup.types')->where('branch_id',JWTAuth::user()->id)->latest()->get();
 $lead = Lead::with('branch','employee','client','lead_assign','source','sourcename','mainchild','mainchild.leadbasicchild','mainchild.leadbasicchild.productcategory','mainchild.leadbasicchild.company','mainchild.leadbasicchild.product','mainchild.leadbasicchild.warehouse','lead_assign.stage','mainchild.employees','basicdesign','lead_followup','lead_followup.employee','lead_followup.types')->where('branch_id',JWTAuth::user()->id)->latest()->get();
 $array = array();
 foreach ($lead as $key => $value) {
   $param = explode(',', $value->lead_name);
   $value->lead_names_array = ProductCategory::whereIn('id',$param)->get();
   $employees = explode(',', $value->lead_assign->employee_id);
   $value->lead_assign_employee = Employee::whereIn('id',$employees)->get();
   
   $quotation = Quotation::where('lead_id',$value->id)->first();
   if($quotation)
   {
    $value->quot_status = 1;
}
else
{
    $value->quot_status = 0;
}



}
}

return response()->json(['success'=>1,'data' =>$lead]);
}

public function getleadproduct(Request $request)
{
    $this->validate($request, [
        'company_id' => 'required',

    ]);

    $product = Product::where('company_id',$request->company_id)->get();

    return response()->json(['success'=>1,'data' =>$product]);

}


public function getleadcompany(Request $request)
{
    $this->validate($request, [
        'pro_category_id' => 'required',

    ]);

    $company = Company::where('pro_category_id',$request->pro_category_id)->get();

    return response()->json(['success'=>1,'data' =>$company]);

}

public function getdatalead(Request $request)
    {
     $this->validate($request, [
         'id' => 'required',
                    
     ]);

      $lead = Lead::with('branch','employee','client','source','sourcename','mainchild','mainchild.leadbasicchild','mainchild.leadbasicchild.productcategory','mainchild.leadbasicchild.company','mainchild.leadbasicchild.product','mainchild.leadbasicchild.warehouse','lead_assign.stage','mainchild.employees','basicdesign')->where('id',$request->id)->first();

         $array = array();
     
         $param = explode(',', $lead->lead_name);
         $lead->lead_names_array = ProductCategory::whereIn('id',$param)->get();



    
    

     return response()->json(['success'=>1,'data' => $lead]);
    }

    public function getbranch(Request $request)
    {
        $branch = User::where('role',2)->get();

        return response()->json(['success'=>1,'data' => $branch]);

    }

    public function getdataleave(Request $request)
    {
         $this->validate($request, [
         'id' => 'required',
                    
     ]);
        $leave = Leave::with('branch','employee')->where('id',$request->id)->first();

         return response()->json(['success'=>1,'data' => $leave]);
    }


public function getfollowuptype(Request $request)
{
    

    $followup = Type::where('branch_id',$request->branch_id)->get();
    if(JwTAuth::user()->role == 2)
    {
      $followup = Type::where('branch_id',JWTAuth::user()->id)->get();
  }

  return response()->json(['success'=>1,'data' =>$followup]);

}


public function getleadwarehouse(Request $request)
{
    $this->validate($request, [
        'product_id' => 'required',

    ]);

    $warehouse = WarehouseInventory::with('warehouse')->where('product_id',$request->product_id)->get();
    


    return response()->json(['success'=>1,'data' =>$warehouse]);

}

public function getleademployee(Request $request)
{
    $this->validate($request, [
        'branch_id' => 'required',

    ]);

    $data['employee'] = Employee::where('branch_id',$request->branch_id)->get();
    $data['client'] = Client::where('branch_id',$request->branch_id)->get();

    return response()->json(['success'=>1,'data' =>$data]);

}


public function getleadsource_name(Request $request)
{
    $this->validate($request, [
        'id' => 'required',

    ]);

    $data['source_name'] = SourceName::where('lead_sourceid',$request->id)->get();

    return response()->json(['success'=>1,'data' =>$data]);

}

public function createleadbasic(Request $request)
{

    $this->validate($request, [
        'client_id' => 'required',
        'source_id' => 'required',
    ]);

    $lead = new Lead();
    $lead->client_id = $request->client_id;
    if(JWTAuth::user()->role == 1)
    {
        $lead->branch_id = $request->branch_id;
        $lead->employee_id = $request->employee_ids;
    }
    else if(JWTAuth::user()->role == 2)
    {
        $lead->branch_id = JWTAuth::user()->id;
        $lead->employee_id = $request->employee_ids;
    }
    $param =implode(',', $request->lead_name);
    $lead->lead_name = $param;
    $lead->description = $request->description;
    $lead->source_id = $request->source_id;
    $lead->source_name = $request->source_name;
    $lead->date = $request->date;
    $lead->current_project_phase = $request->current_project_phase;
    $lead->lead_value = $request->lead_value;
    $lead->app_link =  env('Lead_admin_link');
    $lead->employee_app_link = env('Lead_employee_link');
   

    $check = Lead::where('client_id',$request->client_id)->first();

    if(isset($check))
    {
        $assign = LeadAssign::where('lead_id',$check->id)->first();
        
        $employee = explode(',', $assign->employee_id);
        $array = [];
        foreach ($employee as $key => $value) {
            $data = Employee::where('id',$value)->value('name');
            array_push($array, $data);
        }
        $datas = implode(',', $array);

        if($check){
            return response()->json(['status' => false, 'msg' => 'Duplicate Client found !']);
        }
    }
    $lead->status = $request->status;
    $lead->save();
        // foreach($request['name'] as $key=>$value)
        // {
        //     if($value != '')
        //     {
        //         $lead_basic_main_child['lead_id'] = $lead->id;
        //         $lead_basic_main_child['name'] = $value;
        //         $lead_basic_main_child['employee_id'] = $request->employee_id[$key];
        //         $lead_basic_main_child_store = LeadBasicMainChild::create($lead_basic_main_child);

        //         foreach($request['productcategory_id'][$key] as $keys=>$values)
        //         {
        //             if ($key == $request['main_id'][$key][$keys]) 
        //             {
        //                 if($values != '')
        //                 {
        //                     $child = new LeadBasicChild();
        //                     $child->lead_id = $lead->id;
        //                     $child->lead_basic_main_id = $lead_basic_main_child_store->id;
        //                     $child->productcategory_id = $values;
        //                     $child->company_id = $request->company_id[$key][$keys];
        //                     $child->product_id = $request->product_id[$key][$keys];
        //                     $child->warehouse_id = $request->warehouse_id[$key][$keys];
        //                     $child->qty = $request->qty[$key][$keys];
        //                     $child->save();
        //                 }
        //             }
        //         }

        //     }
        // }

    if($request->design_date)
    {

        foreach ($request->design_date as $keyss => $valuess) {
            if($valuess != '')
            {
                $design_child = new LeadBasicDesignChild();
                $design_child->lead_id = $lead->id;
                $design_child->date = $valuess;
                if(isset($request['design']))
                {
                    $imageName =  time().$keyss.'.'. $request->design[$keyss]->getClientOriginalExtension();
                    $request->design[$keyss]->move(public_path('design/'),$imageName);
                    $design_child->design = $imageName;
                }
                $design_child->save();

            }
        }
    }

    $lead_assign = new LeadAssign();
    $lead_assign->lead_id = $lead->id;
    $lead_assign->employee_id = $lead->employee_id;
    $lead_assign->save();

    $lead_followup = new LeadFollowUp();
    $lead_followup->lead_id = $lead->id;
    $lead_followup->employee_id = $lead->employee_id;
    $lead_followup->save();
    $datas = [
        'lead' => $lead,
    ];


    $employee = Employee::where('id',$lead_assign->employee_id)->first();
    $client = Client::where('id',$lead->client_id)->value('member');

    $email = $employee->email;



    $text = 'Lead Created Name.' . $client;

    Mail::send('lead.lead_create',$datas, function($message) use($email,$text) {
        $message->to($email);
        $message->subject($text);

    });

    $client = Client::where('id',$lead->client_id)->value('member');
    $stores_token = Employee::where('id',$employee->id)->value('push_token');
    if($stores_token)
    {
     $device_token = $stores_token;
     $urls = env('Lead_employee_link');
        $leads_id = $lead->id;
        $name = 'Lead';
     $title = 'Lead Created !!';
     $body = 'Lead Created Name.' . $client;

     $this->sendnotification($device_token,$title,$body,$urls,$leads_id,$name);
 }



 $datas = [
    'lead' => $lead,
];

$client = Client::where('id',$lead->client_id)->value('member');
$branch = User::where('id',$lead->branch_id)->first();
$email = $branch->email;



$text = 'Lead Created Name.' . $client;

Mail::send('lead.lead_create',$datas, function($message) use($email,$text) {
    $message->to($email);
    $message->subject($text);

});
$client = Client::where('id',$lead->client_id)->value('member');
$stores_token = User::where('id',$branch->id)->value('push_token');
if($stores_token)
{
 $device_token = $stores_token;
 $urls = env('Lead_admin_link');
 $leads_id = $lead->id;
 $name = 'Lead';
 $title = 'Lead Created !!';
 $body = 'Lead Created Name.' . $client;

 $this->adminsendnotification($device_token,$title,$body,$urls,$leads_id,$name);
}


return response()->json(['success' => 1, 'message' => 'Lead Created Successfully.']);

}

public function createleadassign(Request $request)
{

    $this->validate($request, [
        'lead_id' => 'required',

    ]);

    $lead_assign = LeadAssign::where('lead_id',$request->lead_id)->first();
    if($lead_assign)
    {
        $lead_assign->lead_id = $request->lead_id;
        $lead_assign->stage_id = $request->stage_id;
        $lead_assign->section_id = $request->section_id;
        $params = implode(',', $request->employee_id);
        $lead_assign->employee_id = $params;
        $lead_assign->note = $request->note;
        $lead_assign->save();
        $expload = explode(',',$lead_assign->employee_id);
        $followup = LeadFollowUp::where('lead_id',$request->lead_id)->get();
        $array = [];
        foreach($followup as $fp)
        {
            array_push($array,$fp->employee_id);
        }
        $deleted_emp = array_diff($array,$expload);
        LeadFollowUp::where('lead_id',$request->lead_id)->whereIn('employee_id',$deleted_emp)->delete();
        foreach($expload as $key => $value) {
            if($value != '')
            {
                $childs = LeadFollowUp::where('lead_id',$request->lead_id)->where('employee_id',$value)->first();
                if($childs)
                {

                }else
                {
                    $childs = new LeadFollowUp();
                    $childs->lead_id = $request->lead_id;
                    $childs->employee_id = $value;
                    $childs->save();
                }
            }
        }
    }
    else
    {
        $lead_assign = new LeadAssign();
        $lead_assign->lead_id = $request->lead_id;
        $lead_assign->stage_id = $request->stage_id;
            // $lead_assign->section_id = $request->section_id;
            // $params = implode(",",$request->employee_id);
            // $lead_assign['employee_id'] = $params;  
            // $lead_assign->employee_id = $request->employee_id;
        $params = implode(',', $request->employee_id);
        $lead_assign->employee_id = $params;
        $lead_assign->note = $request->note;
            // $lead_assign->date = $request->date; 
        $lead_assign->save();
        foreach($request->employee_id as $key => $value) {
            if($value != '')
            {
                $childs = new LeadFollowUp();
                $childs->lead_id = $request->lead_id;
                $childs->employee_id = $value;
                $childs->save();
            }
        }
    }

    $lead = Lead::where('id',$lead_assign->lead_id)->first();
    $client = Client::where('id',$lead->client_id)->value('member');

    $datas = [
        'lead' => $lead,
    ];
    $exp = array();
    $path = explode(",", $lead_assign->employee_id);
    $exp = array_merge($exp, $path);
    foreach ($exp as $key => $value) {
        $employee = Employee::where('id',$value)->first();
        $email = $employee->email;
        $text = 'Lead Updated Name.' . $client;

        Mail::send('lead.lead_create',$datas, function($message) use($email,$text) {
            $message->to($email);
            $message->subject($text);
        });

        $client = Client::where('id',$lead->client_id)->value('member');
        $stores_token = Employee::where('id',$employee->id)->value('push_token');
        if($stores_token)
        {
           $device_token = $stores_token;
           $urls = env('Lead_employee_link');
           $leads_id = $lead->id;
           $name = 'Lead';
           $title = 'Lead Updated !!';
           $body = 'Lead Updated Name.' . $client;

           $this->sendnotification($device_token,$title,$body,$urls,$leads_id,$name);
       }


   }

   $datas = [
    'lead' => $lead,
];

$client = Client::where('id',$lead->client_id)->value('member');
$branch = User::where('id',$lead->branch_id)->first();

$email = $branch->email;
$text = 'Lead Updated Name.' . $client;        
Mail::send('lead.lead_create',$datas, function($message) use($email,$text) {
    $message->to($email);
    $message->subject($text);
});

$client = Client::where('id',$lead->client_id)->value('member');
$stores_token = User::where('id',$branch->id)->value('push_token');
if($stores_token)
{
   $device_token = $stores_token;
   $urls = env('Lead_admin_link');
   $leads_id = $lead->id;
   $name = 'Name';
   $title = 'Lead Updated !!';
   $body = 'Lead Updated Name.' . $client;

   $this->adminsendnotification($device_token,$title,$body,$urls,$leads_id,$name);
}
return response()->json(['success' => 1, 'message' => 'Lead Assign Created Successfully']);
}

    // public function updateleadassign(Request $request)
    // {
    //     $this->validate($request, [
    //         'id' => 'required',
    //         'lead_id' => 'required',

    //     ]);
    //     $lead_assign = LeadAssign::where('lead_id',$request->lead_id)->first();

    //     $lead_assign->lead_id = $request->lead_id;
    //     $lead_assign->stage_id = $request->stage_id;
    //     // $lead_assign->section_id = $request->section_id;

    //     $params = implode(",",$request->employee_id);
    //         $lead_assign['employee_id'] = $params;
    //     $lead_assign->note = $request->note;
    //     // $lead_assign->date = $request->date;

    //     $lead_assign->save();

    //     return response()->json(['success' => 1, 'message' => 'Lead Assign Updated Successfully']);

    // }

public function createleadfollowup(Request $request)
{
    $this->validate($request, [
        'lead_id' => 'required',
        'id' => 'required',
    ]);
    
    $lead_followup = LeadFollowUp::where('lead_id',$request->lead_id)->where('id',$request->id)->first();
    if($lead_followup)
    {
        $lead_followup->lead_id = $request->lead_id;
        $lead_followup->subject = $request->subject;
        $lead_followup->description = $request->description;
        $lead_followup->date = $request->date;
        $lead_followup->time = $request->time;
        $lead_followup->type = $request->type;
        $lead_followup->is_completed = $request->is_completed;
        $lead_followup->save();
    }
    
    else
    {
        $lead_followup = new LeadFollowUp();
        $lead_followup->lead_id = $request->lead_id;
        $lead_followup->subject = $request->subject;
        $lead_followup->description = $request->description;
        $lead_followup->date = $request->date;
        $lead_followup->time = $request->time;
        $lead_followup->type = $request->type;
        $lead_followup->is_completed = $request->is_completed;
        $lead_followup->save();
    }

    $lead = Lead::where('id',$lead_followup->lead_id)->first();
    $client = Client::where('id',$lead->client_id)->value('member');
    $lead_assign = LeadAssign::where('lead_id',$lead_followup->lead_id)->first();

    $datas = [
        'lead' => $lead,
    ];
    $exp = array();
    $path = explode(",", $lead_assign->employee_id);
    $exp = array_merge($exp, $path);
    foreach ($exp as $key => $value) {
        $employee = Employee::where('id',$value)->first();
        $email = $employee->email;
        $text = 'Lead Updated Name.' . $client;

        Mail::send('lead.lead_create',$datas, function($message) use($email,$text) {
            $message->to($email);
            $message->subject($text);
        });

        $client = Client::where('id',$lead->client_id)->value('member');
        $stores_token = Employee::where('id',$employee->id)->value('push_token');
        if($stores_token)
        {
         $device_token = $stores_token;
         $urls = env('Lead_employee_link');
            $leads_id = $lead->id;
            $name = 'Lead';
         $title = 'Lead Updated !!';
         $body = 'Lead Updated Name.' . $client;

         $this->sendnotification($device_token,$title,$body,$urls,$leads_id,$name);
     }
 }

 $datas = [
    'lead' => $lead,
];

$client = Client::where('id',$lead->client_id)->value('member');
$branch = User::where('id',$lead->branch_id)->first();
$email = $branch->email;
$text = 'Lead Updated Name.' . $client;

Mail::send('lead.lead_create',$datas, function($message) use($email,$text) {
    $message->to($email);
    $message->subject($text);
});

$client = Client::where('id',$lead->client_id)->value('member');
$stores_token = User::where('id',$branch->id)->value('push_token');
if($stores_token)
{
   $device_token = $stores_token;
   $urls = env('Lead_admin_link');
    $leads_id = $lead->id;
   $title = 'Lead Updated !!';
   $name = 'Lead';
   $body = 'Lead Updated Name.' . $client;

   $this->adminsendnotification($device_token,$title,$body,$urls,$leads_id,$name);
}

return response()->json(['success' => 1, 'message' => 'Lead FollowUp Updated Successfully.']);
}

public function createdesign(Request $request)
{
    $this->validate($request, [
        'lead_id' => 'required',
    ]);


    foreach ($request->design_date as $keyss => $valuess) {
        if($valuess != '')
        {
            $design_child = new LeadBasicDesignChild();
            $design_child->lead_id = $request->lead_id;
            $design_child->date = $valuess;

            if(isset($request['design']))
            {
                $imageName =  time().$keyss.'.'. $request->design[$keyss]->getClientOriginalExtension();
                $request->design[$keyss]->move(public_path('design/'),$imageName);
                $design_child->design = $imageName;
            }
            $design_child->save();

        }
    }
    return response()->json(['success' => 1, 'message' => 'Lead Design Created Successfully']);

}

public function deletedesign(Request $request)
{
    $this->validate($request, [
        'id' => 'required',
    ]);

    $design = LeadBasicDesignChild::where('id',$request->id)->delete();
    return response()->json(['success' => 1, 'message' => 'Lead Design Deleted Successfully']);
}


public function createleadcommunication(Request $request)
{

    $this->validate($request, [
        'lead_id' => 'required',
    ]);

    $lead_communication = LeadCommunication::where('lead_id',$request->lead_id)->first();
    if($lead_communication)
    {

        $lead_communication->lead_id = $request->lead_id;
        $lead_communication->type = $request->type;
        $lead_communication->email = $request->email;
        $lead_communication->autoresponder = $request->autoresponder;
        $lead_communication->subject = $request->subject;
        $lead_communication->message = $request->message;

        $lead_communication->save(); 
    }
    else
    {
        $lead_communication = new LeadCommunication();
        $lead_communication->lead_id = $request->lead_id;
        $lead_communication->type = $request->type;
        $lead_communication->email = $request->email;
        $lead_communication->autoresponder = $request->autoresponder;
        $lead_communication->subject = $request->subject;
        $lead_communication->message = $request->message;

        $lead_communication->save();
    }

    return response()->json(['success' => 1, 'message' => 'Created Lead Communication Successfully']); 
}  


public function updateleadbasic(Request $request)
{
    $this->validate($request, [
        'id' => 'required',
        'client_id' => 'required',
        'source_id' => 'required',
    ]);

    $lead = Lead::where('id',$request->id)->first();

    if(JWTAuth::user()->role == 1)
    {
        $lead->branch_id = $request->branch_id;
        $lead->employee_id = $request->employee_ids;
    }
    else if(JWTAuth::user()->role == 2)
    {
        $lead->branch_id = JWTAuth::user()->id;
        $lead->employee_id = $request->employee_ids;
    }


    
    $lead->client_id = $request->client_id;
    $params = implode(',', $request->lead_name);
    $lead->lead_name = $params;
    $lead->description = $request->description;
    $lead->source_id = $request->source_id;
    $lead->source_name = $request->source_name;
    $lead->no_of_machines = $request->no_of_machines;
    $lead->date = $request->date;
    $lead->current_project_phase = $request->current_project_phase;
    $lead->status = $request->status;
    $lead->lead_value = $request->lead_value;
    $lead->save(); 

    $lead_assign = LeadAssign::where('lead_id',$lead->id)->first();

    $datas = [
        'lead' => $lead,
    ];


    $employee = Employee::where('id',$lead_assign->employee_id)->first();
    $client = Client::where('id',$lead->client_id)->value('member');

    $email = $employee->email;



    $text = 'Lead Updated Name.' . $client;

    Mail::send('lead.lead_create',$datas, function($message) use($email,$text) {
        $message->to($email);
        $message->subject($text);

    });

    $client = Client::where('id',$lead->client_id)->value('member');
    $stores_token = Employee::where('id',$employee->id)->value('push_token');
    if($stores_token)
    {
     $device_token = $stores_token;
     $urls = env('Lead_employee_link');
        $leads_id = $lead->id;
        $name = 'Lead';
     $title = 'Lead Updated !!';
     $body = 'Lead Updated Name.' . $client;

     $this->sendnotification($device_token,$title,$body,$urls,$leads_id,$name);
 }



 $datas = [
    'lead' => $lead,
];

$client = Client::where('id',$lead->client_id)->value('member');
$branch = User::where('id',$lead->branch_id)->first();
$email = $branch->email;



$text = 'Lead Updated Name.' . $client;

Mail::send('lead.lead_create',$datas, function($message) use($email,$text) {
    $message->to($email);
    $message->subject($text);

});
$client = Client::where('id',$lead->client_id)->value('member');
$stores_token = User::where('id',$branch->id)->value('push_token');
if($stores_token)
{
 $device_token = $stores_token;
 $urls = env('Lead_admin_link');
 $leads_id = $lead->id;
 $name = 'Lead';
 $title = 'Lead Updated !!';
 $body = 'Lead Updated Name.' . $client;

 $this->adminsendnotification($device_token,$title,$body,$urls,$leads_id,$name);
}


    return response()->json(['success' => 1, 'message' => 'Lead Basic Updated Successfully']);  
}

public function createbasicchild(Request $request)
{
    $this->validate($request, [
        'lead_id' => 'required',
        'lead_basic_main_id' => 'required',
    ]);           

    if($request->productcategory_id)
    {
        foreach ($request->productcategory_id as $key => $value) {
            $child = new LeadBasicChild();
            $child->lead_id = $request->lead_id;
            $child->lead_basic_main_id = $request->lead_basic_main_id;
            $child->productcategory_id = $value;
            $child->company_id = $request->company_id[$key];
            $child->product_id = $request->product_id[$key];
            $child->warehouse_id = $request->warehouse_id[$key];
            $child->qty = $request->qty[$key];
            $child->save();
        }
    }
    

    return response()->json(['success' => 1, 'message' => 'Lead Basic Child Created Successfully']);
}

    // public function updatebasicchild(Request $request)
    // {
    //     $this->validate($request, [
    //         'id' => 'required',
    //         'lead_id' => 'required',
    //         'lead_basic_main_id' => 'required',

    //     ]);           

    //     $child = LeadBasicChild::where('id',$request->id)->first();
    //     $child->lead_id = $request->lead_id;
    //     $child->lead_basic_main_id = $request->lead_basic_main_id;
    //     $child->productcategory_id = $request->productcategory_id;
    //     $child->company_id = $request->company_id;
    //     $child->product_id = $request->product_id;
    //     $child->warehouse_id = $request->warehouse_id;
    //     $child->qty = $request->qty;
    //                         // dd($child);
    //     $child->save();


    //     return response()->json(['success' => 1, 'message' => 'Created Lead Basic Child Successfully']);  
    //     // dd($child);
    // }

public function leadcreatemainchild(Request $request)
{
    $this->validate($request, [
        'lead_id' => 'required',
    ]);

    $lead = Lead::where('id',$request->lead_id)->first();

    foreach($request['name'] as $key=>$value)
    {
        if($value != '')
        {
            $lead_basic_main_child['lead_id'] = $lead->id;
            $lead_basic_main_child['name'] = $value;
            $lead_basic_main_child['employee_id'] = $request->employee_id[$key];

            $lead_basic_main_child_store = LeadBasicMainChild::create($lead_basic_main_child);

                // foreach($request['productcategory_id'] as $keys=>$values)
                // {
                //     if ($key == $request['main_id'][$keys]) 
                //     {
                //         if($values != '')
                //         {
                //             $child = new LeadBasicChild();
                //             $child->lead_id = $lead->id;
                //             $child->lead_basic_main_id = $lead_basic_main_child_store->id;
                //             $child->productcategory_id = $values;
                //             $child->company_id = $request->company_id[$keys];
                //             $child->product_id = $request->product_id[$keys];
                //             $child->warehouse_id = $request->warehouse_id[$keys];
                //             $child->qty = $request->qty[$keys];
                //             $child->save();
                //         }
                //     }
                // }

        }
    }

    return response()->json(['success' => 1, 'message' => 'Lead Main Child Created Successfully']);  
}

public function leadmainchildupdate(Request $request)
{
    $this->validate($request, [
        'id' => 'required',
        'lead_id' => 'required',
    ]);  

    $main = LeadBasicMainChild::where('id',$request->id)->first();
    $main->lead_id = $request->lead_id;
    $main->name = $request->name;
    $main->employee_id = $request->employee_id;
    $main->save();

    return response()->json(['success' => 1, 'message' => 'Lead Main Child Updated Successfully']);  

}

public function leadmainchilddelte(Request $request)
{
    $this->validate($request, [
        'id' => 'required',
    ]);  

    $main = LeadBasicMainChild::where('id',$request->id)->first();
    $child = LeadBasicChild::where('lead_basic_main_id',$main->id)->delete();
    $main->delete();

    return response()->json(['success' => 1, 'message' => 'Deleted Successfully']);  

}

public function updatebasicchild(Request $request)
{
    $this->validate($request, [
        'id' => 'required',
        'lead_id' => 'required',
        'lead_basic_main_id' => 'required',
    ]);           

    $child = LeadBasicChild::where('id',$request->id)->first();
    $child->lead_id = $request->lead_id;
    $child->lead_basic_main_id = $request->lead_basic_main_id;
    $child->productcategory_id = $request->productcategory_id;
    $child->company_id = $request->company_id;
    $child->product_id = $request->product_id;
    $child->warehouse_id = $request->warehouse_id;
    $child->qty = $request->qty;
    $child->save();
    return response()->json(['success' => 1, 'message' => 'Updated Lead Basic Child Successfully']);
}

public function deletebasicchild(Request $request)
{
    $this->validate($request, [
        'id' => 'required',
    ]);

    $child =LeadBasicChild::where('id',$request->id)->delete();
    return response()->json(['success' => 1, 'message' => 'Lead Basic Child Deleted Successfully']);
}

public function deletelead(Request $request)
{
    $this->validate($request, [
        'id' => 'required',
    ]);
    $lead =Lead::where('id',$request->id)->delete();
    $main = LeadBasicMainChild::where('lead_id',$request->id)->delete();
    $child = LeadBasicChild::where('lead_id',$request->id)->delete();
    $design = LeadBasicDesignChild::where('lead_id',$request->id)->delete();
    return response()->json(['success' => 1, 'message' => 'Lead Deleted Successfully']);
}

public function gettask()
{
    $task = Task::with('employee','branch','assign_to')->orderBy('id', 'DESC')->get();
    if(JWTAuth::user()->role == 2)
    {
        $task = Task::with('employee','branch','assign_to')->where('branch_id',JWTAuth::user()->id)->orderBy('id', 'DESC')->get();
    }
    return response()->json(['success' => 1, 'data' => $task]);  
}

public function taskcreate(Request $request)
{
    $this->validate($request, [
        'employee_id' => 'required',
        'mobile' => 'required',
        'purpose' => 'required',
        'name' => 'required',
    ]);

    $task = new Task();
    $task->name = $request->name;
    if(JWTAuth::user()->role == 1)
    {
        $task->branch_id = $request->branch_id;
        $task->employee_id = $request->employee_id;
    }
    else if(JWTAuth::user()->role == 2)
    {
        $task->branch_id = JWTAuth::user()->id;
        $task->employee_id = $request->employee_id;
    }
    $task->mobile = $request->mobile;
    $task->purpose = $request->purpose;
    $task->assign_to = $request->assign_to;
    $task->is_completed = $request->is_completed ?? 0;
    $task->description = $request->description;
    $task->app_link = $request->app_link;
    $task->save();
    $datas = [
        'task' => $task,
    ];

    $employee = Employee::where('id',$task->assign_to)->first();
    $email = $employee->email;
    $text = 'New Task Created Name.' . $task->purpose;
    Mail::send('task.task_create',$datas, function($message) use($email,$text) {
        $message->to($email);
        $message->subject($text);
    });
    $stores_token = Employee::where('id',$employee->id)->value('push_token');
    if($stores_token)
    {
       $device_token = $stores_token;
       $urls = env('Task_employee_link');
       $leads_id = $task->id;
       $name = "Task";
       $title = 'Task Created !!';
       $body = 'New Task Created Name.' . $task->purpose;

       $this->sendnotification($device_token,$title,$body,$urls,$leads_id,$name);
   }

   return response()->json(['success' => 1, 'message' => 'Task created successfully.']);
}

public function taskupdate(Request $request)
{
    $this->validate($request, [
        'id' => 'required',
        'employee_id' => 'required',
        'mobile' => 'required',
        'purpose' => 'required',
        'name' => 'required',
    ]);

    $task = Task::where('id',$request->id)->first();
    $task->name = $request->name;
    if(JWTAuth::user()->role == 1)
    {
        $task->branch_id = $request->branch_id;
        $task->employee_id = $request->employee_id;
    }
    else if(JWTAuth::user()->role == 2)
    {
        $task->branch_id = JWTAuth::user()->id;
        $task->employee_id = $request->employee_id;
    }
    $task->description = $request->description;
    $task->mobile = $request->mobile;
    $task->purpose = $request->purpose;
    $task->assign_to = $request->assign_to;
        // $task->is_completed = $request->is_completed ?? 0;
    $task->save();
    $datas = [
        'task' => $task,
    ];

    $employee = Employee::where('id',$task->assign_to)->first();
    $email = $employee->email;
    $text = 'Task Updated Name.' . $task->purpose;
    Mail::send('task.task_create',$datas, function($message) use($email,$text) {
        $message->to($email);
        $message->subject($text);
    });
    $stores_token = Employee::where('id',$employee->id)->value('push_token');
    if($stores_token)
    {
       $device_token = $stores_token;
       $urls = env('Task_employee_link');
       $leads_id = $task->id;
       $name = 'Task';
       $title = 'Task Updated !!';
       $body = 'Task Updated Name.' . $task->purpose;

       $this->sendnotification($device_token,$title,$body,$urls,$leads_id,$name);
   }

   $datas = [
    'task' => $task,
];
$employee = Employee::where('id',$task->employee_id)->first();
$email = $employee->email;
$text = 'Task Updated' . $task->purpose;
Mail::send('task.task_create',$datas, function($message) use($email,$text) {
    $message->to($email);
    $message->subject($text);
});

$stores_token = Employee::where('id',$employee->id)->value('push_token');
if($stores_token)
{
   $device_token = $stores_token;
   $urls = env('Task_employee_link');
   $leads_id = $task->id;
   $name = 'Task';
   $title = 'Task Updated !!';
   $body = 'Task Updated Name.' . $task->purpose;

   $this->sendnotification($device_token,$title,$body,$urls,$leads_id,$name);
}
return response()->json(['success' => 1, 'message' => 'Task updated successfully.']); 
}

public function taskstatus(Request $request)
{
    $this->validate($request, [
        'id' => 'required',
        'status' => 'required',
    ]);

    $task = Task::where('id',$request->id)->first();
    $task->status = $request->status;
    $task->save();
    $datas = [
        'task' => $task,
    ];

    $employee = Employee::where('id',$task->assign_to)->first();
    $email = $employee->email;


    $text = 'Status Change Task Name.' . $task->purpose;
    Mail::send('task.task_change',$datas, function($message) use($email,$text) {
        $message->to($email);
        $message->subject($text);
    });
    $stores_token = Employee::where('id',$employee->id)->value('push_token');
    if($stores_token)
    {
     $device_token = $stores_token;
     $urls = env('Task_employee_link');
       $leads_id = $task->id;
       $name = 'Task';
     $title = 'Status Change Successfully !!';
     $body = 'Status Change Task No.' . $task->id;

     $this->sendnotification($device_token,$title,$body,$urls,$leads_id,$name);
 }

 $datas = [
    'task' => $task,
];

$employee = Employee::where('id',$task->employee_id)->first();
$email = $employee->email;


$text = 'Status Change Task Name. ' . $task->purpose;
Mail::send('task.task_change',$datas, function($message) use($email,$text) {
    $message->to($email);
    $message->subject($text);
});

$stores_token = Employee::where('id',$employee->id)->value('push_token');
if($stores_token)
{
 $device_token = $stores_token;
 $urls = env('Task_employee_link');
       $leads_id = $task->id;
       $name = 'Task';
 $title = 'Status Change Successfully !!';
 $body = 'Status Change Task Name.' . $task->purpose;

 $this->sendnotification($device_token,$title,$body,$urls,$leads_id,$name);
}
return response()->json(['success' => 1, 'message' => 'Status change successfully.']); 
}

public function taskdelete(Request $request)
{
    $this->validate($request, [
        'id' => 'required',         
    ]);           
    $task =Task::where('id',$request->id)->delete();
    return response()->json(['success' => 1, 'message' => 'Task delete successfully.']);               
}
}