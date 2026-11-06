<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\User;
use DataTables;
use App\Task;
use App\Attendance;
use App\Quotation;
use App\Service;
use App\ProductCategory;
use App\ServiceChild;
use App\ServiceImage;
use App\Employee;
use App\Client;
use App\Leave;

use Auth;
use Mail;
use Illuminate\Http\Request;
use JWTAuth;
use JWTAuthException;

class AdminServiceController extends Controller
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
   
    public function admingetservice()
    {
       
       if(JWTAuth::user()->role == 2)
       {

        $service = Service::with('child.company','child.product','child.category','client','branch','employee','image')->where('branch_id',JWTAuth::user()->id)->latest()->get();
       }else{

        $service = Service::with('child.company','child.product','child.category','client','branch','employee','image')->latest()->get();
       }
        
        return response()->json(['success'=>1,'data' => $service]);
    }

    public function admincreateservice(Request $request)
    {
        $service = new Service();

       if(JWTAuth::user()->role == 1)
       {
            $service->branch_id = $request->branch_id;
            $service->employee_id = $request->employee_id;
           
                
       }
       else
       {
            $service->branch_id = JWTAuth::user()->id;
            $service->employee_id = $request->employee_id;
       }
            

       

        $service->client_id = $request->client_id;
        $service->phone_no = $request->phone_no;
        $service->address = $request->address;
        $service->complain_no = $request->complain_no;
        $service->register_date = $request->register_date;
        $service->remark = $request->remark;
        $service->completed_date = $request->completed_date;
        $service->status = 2;
        $service->save();

        if($request->productcategory_id)
        {
            foreach ($request->productcategory_id as $key => $value) {
                if($value != '')
                {
                    $child = new ServiceChild();
                    $child->service_id = $service->id;
                    $child->category_id = $value;
                    $child->company_id = $request->company_id[$key];
                    $child->product_id = $request->product_id[$key];

                    $child->qty = $request->qty[$key];
                    $child->save();

                }
            }
        }

         if($request->image)
        {
            foreach ($request->image as $key => $value) {
                if($value != '')
                {
                    $service_image = new ServiceImage();
                    $service_image->service_id = $service->id;
                    if(isset($value))
                    {
                        $imageName = time().'-'.$key.'.'. $value->getClientOriginalExtension();
                        $value->move(public_path('service/'),$imageName);
                        $service_image->image = $imageName;
                    }
                  
                   
                    $service_image->save();

                }
            }
        }

        $datas = [
            'service' => $service,
        ];


        $admin = User::where('id',1)->first();
        $client = Client::where('id',$service->client_id)->value('member');

        $email = $admin->email;



        $text = 'Service Created Name.' . $client;

        Mail::send('service.service_create',$datas, function($message) use($email,$text) {
            $message->to($email);
            $message->subject($text);

        });

        $client = Client::where('id',$service->client_id)->value('member');
        $stores_token = User::where('id',1)->value('push_token');
        if($stores_token)
        {
           $device_token = $stores_token;
           $urls = env('Service_admin_link');
           $leads_id = $service->id;
           $name = 'Service';
           $title = 'Service Created !!';
           $body = 'Service Created Name.' . $client;

           $this->adminsendnotification($device_token,$title,$body,$urls,$leads_id,$name);
       }

        $datas = [
            'service' => $service,
        ];


        $branch = User::where('id',$service->branch_id)->first();
        $client = Client::where('id',$service->client_id)->value('member');

        $email = $branch->email;



        $text = 'Service Created Name.' . $client;

        Mail::send('service.service_create',$datas, function($message) use($email,$text) {
            $message->to($email);
            $message->subject($text);

        });

        $client = Client::where('id',$service->client_id)->value('member');
        $stores_token = User::where('id',$branch->id)->value('push_token');
        if($stores_token)
        {
           $device_token = $stores_token;
           $urls = env('Service_admin_link');
           $leads_id = $service->id;
           $name = 'Service';
           $title = 'Service Created !!';
           $body = 'Service Created Name.' . $client;

           $this->adminsendnotification($device_token,$title,$body,$urls,$leads_id,$name);
       }

         $datas = [
            'service' => $service,
        ];


        $employee = Employee::where('id',$service->employee_id)->first();
        $client = Client::where('id',$service->client_id)->value('member');

        $email = $branch->email;



        $text = 'Service Created Name.' . $client;

        Mail::send('service.service_create',$datas, function($message) use($email,$text) {
            $message->to($email);
            $message->subject($text);

        });

        $client = Client::where('id',$service->client_id)->value('member');
        $stores_token = Employee::where('id',$employee->id)->value('push_token');
        if($stores_token)
        {
           $device_token = $stores_token;
           $urls = env('Service_employee_link');
           $leads_id = $service->id;
           $name = 'Service';
           $title = 'Service Created !!';
           $body = 'Service Created Name.' . $client;

           $this->sendnotification($device_token,$title,$body,$urls,$leads_id,$name);
       }


        return response()->json(['success'=>1,'message' => 'Service Created SuccessFully.']);
    }

   

    public function adminupdateservice(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',


        ]);
       

         $service = Service::where('id',$request->id)->first();

       if(JWTAuth::user()->role == 1)
       {
            $service->branch_id = $request->branch_id;
            $service->employee_id = $request->employee_id;
           
                
       }
       else
       {
            $service->branch_id = JWTAuth::user()->id;
            $service->employee_id = $request->employee_id;
       }
       

        $service->client_id = $request->client_id;
        $service->phone_no = $request->phone_no;
        $service->address = $request->address;
        $service->complain_no = $request->complain_no;
        $service->register_date = $request->register_date;
        $service->remark = $request->remark;
        $service->completed_date = $request->completed_date;
      
        $service->save();

         if($request->image)
        {
            foreach ($request->image as $key => $value) {
                if($value != '')
                {
                    $service_image = new ServiceImage();
                    $service_image->service_id = $service->id;
                    if(isset($value))
                    {
                        $imageName = time().'-'.$key.'.'. $value->getClientOriginalExtension();
                        $value->move(public_path('service/'),$imageName);
                        $service_image->image = $imageName;
                    }
                  
                   
                    $service_image->save();

                }
            }
        }

                $datas = [
            'service' => $service,
        ];


        $admin = User::where('id',1)->first();
        $client = Client::where('id',$service->client_id)->value('member');

        $email = $admin->email;



        $text = 'Service Updated Name.' . $client;

        Mail::send('service.service_create',$datas, function($message) use($email,$text) {
            $message->to($email);
            $message->subject($text);

        });

        $client = Client::where('id',$service->client_id)->value('member');
        $stores_token = User::where('id',1)->value('push_token');
        if($stores_token)
        {
           $device_token = $stores_token;
           $urls = env('Service_admin_link');
           $leads_id = $service->id;
           $name = 'Service';
           $title = 'Service Updated !!';
           $body = 'Service Updated Name.' . $client;

           $this->adminsendnotification($device_token,$title,$body,$urls,$leads_id,$name);
       }

        $datas = [
            'service' => $service,
        ];


        $branch = User::where('id',$service->branch_id)->first();
        $client = Client::where('id',$service->client_id)->value('member');

        $email = $branch->email;



        $text = 'Service Updated Name.' . $client;

        Mail::send('service.service_create',$datas, function($message) use($email,$text) {
            $message->to($email);
            $message->subject($text);

        });

        $client = Client::where('id',$service->client_id)->value('member');
        $stores_token = User::where('id',$branch->id)->value('push_token');
        if($stores_token)
        {
           $device_token = $stores_token;
           $urls = env('Service_admin_link');
           $leads_id = $service->id;
           $name = 'Service';
           $title = 'Service Updated !!';
           $body = 'Service Updated Name.' . $client;

           $this->adminsendnotification($device_token,$title,$body,$urls,$leads_id,$name);
       }

         $datas = [
            'service' => $service,
        ];


        $employee = Employee::where('id',$service->employee_id)->first();
        $client = Client::where('id',$service->client_id)->value('member');

        $email = $branch->email;



        $text = 'Service Updated Name.' . $client;

        Mail::send('service.service_create',$datas, function($message) use($email,$text) {
            $message->to($email);
            $message->subject($text);

        });

        $client = Client::where('id',$service->client_id)->value('member');
        $stores_token = Employee::where('id',$employee->id)->value('push_token');
        if($stores_token)
        {
           $device_token = $stores_token;
           $urls = env('Service_employee_link');
           $leads_id = $service->id;
           $name = 'Service';
           $title = 'Service Updated !!';
           $body = 'Service Updated Name.' . $client;

           $this->sendnotification($device_token,$title,$body,$urls,$leads_id,$name);
       }

        return response()->json(['success'=>1,'message' => 'Service Updated SuccessFully.']);
    }

    public function admincreateservicechild(Request $request)
    {
         $this->validate($request, [
            'service_id' => 'required',


        ]);
         if($request->productcategory_id)
        {
            foreach ($request->productcategory_id as $key => $value) {
                if($value != '')
                {
                    $child = new ServiceChild();
                    $child->service_id = $request->service_id;
                    $child->category_id = $value;
                    $child->company_id = $request->company_id[$key];
                    $child->product_id = $request->product_id[$key];

                    $child->qty = $request->qty[$key];
                    $child->save();

                }
            }
        }

        $service = Service::where('id',$request->service_id)->first();

                $datas = [
            'service' => $service,
        ];


        $admin = User::where('id',1)->first();
        $client = Client::where('id',$service->client_id)->value('member');

        $email = $admin->email;



        $text = 'Service Child Created Name.' . $client;

        Mail::send('service.service_create',$datas, function($message) use($email,$text) {
            $message->to($email);
            $message->subject($text);

        });

        $client = Client::where('id',$service->client_id)->value('member');
        $stores_token = User::where('id',1)->value('push_token');
        if($stores_token)
        {
           $device_token = $stores_token;
           $urls = env('Service_admin_link');
           $leads_id = $service->id;
           $name = 'Service';
           $title = 'Service Child Created !!';
           $body = 'Service Child Created Name.' . $client;

           $this->adminsendnotification($device_token,$title,$body,$urls,$leads_id,$name);
       }

        $datas = [
            'service' => $service,
        ];


        $branch = User::where('id',$service->branch_id)->first();
        $client = Client::where('id',$service->client_id)->value('member');

        $email = $branch->email;



        $text = 'Service Child Created Name.' . $client;

        Mail::send('service.service_create',$datas, function($message) use($email,$text) {
            $message->to($email);
            $message->subject($text);

        });

        $client = Client::where('id',$service->client_id)->value('member');
        $stores_token = User::where('id',$branch->id)->value('push_token');
        if($stores_token)
        {
           $device_token = $stores_token;
           $urls = env('Service_admin_link');
           $leads_id = $service->id;
           $name = 'Service';
           $title = 'Service Child Created !!';
           $body = 'Service Child Created Name.' . $client;

           $this->adminsendnotification($device_token,$title,$body,$urls,$leads_id,$name);
       }

         $datas = [
            'service' => $service,
        ];


        $employee = Employee::where('id',$service->employee_id)->first();
        $client = Client::where('id',$service->client_id)->value('member');

        $email = $branch->email;



        $text = 'Service Child Created Name.' . $client;

        Mail::send('service.service_create',$datas, function($message) use($email,$text) {
            $message->to($email);
            $message->subject($text);

        });

        $client = Client::where('id',$service->client_id)->value('member');
        $stores_token = Employee::where('id',$employee->id)->value('push_token');
        if($stores_token)
        {
           $device_token = $stores_token;
           $urls = env('Service_employee_link');
           $leads_id = $service->id;
           $name = 'Service';
           $title = 'Service Child Created !!';
           $body = 'Service Child Created Name.' . $client;

           $this->sendnotification($device_token,$title,$body,$urls,$leads_id,$name);
       }

        return response()->json(['success'=>1,'message' => 'Service Created child SuccessFully.']);
    }

    public function adminupdateservicechild(Request $request)
    {
         $this->validate($request, [
            'service_id' => 'required',
            'id' => 'required',

        ]);

         $child = ServiceChild::where('id',$request->id)->first();
         $child->service_id = $request->service_id;
         $child->category_id = $request->productcategory_id;
         $child->company_id = $request->company_id;
         $child->product_id = $request->product_id;

         $child->qty = $request->qty;
         $child->save();

         $service = Service::where('id',$request->service_id)->first();

                 $datas = [
            'service' => $service,
        ];


        $admin = User::where('id',1)->first();
        $client = Client::where('id',$service->client_id)->value('member');

        $email = $admin->email;



        $text = 'Service Child Updated Name.' . $client;

        Mail::send('service.service_create',$datas, function($message) use($email,$text) {
            $message->to($email);
            $message->subject($text);

        });

        $client = Client::where('id',$service->client_id)->value('member');
        $stores_token = User::where('id',1)->value('push_token');
        if($stores_token)
        {
           $device_token = $stores_token;
           $urls = env('Service_admin_link');
           $leads_id = $service->id;
           $name = 'Service';
           $title = 'Service Child Updated !!';
           $body = 'Service Child Updated Name.' . $client;

           $this->adminsendnotification($device_token,$title,$body,$urls,$leads_id,$name);
       }

        $datas = [
            'service' => $service,
        ];


        $branch = User::where('id',$service->branch_id)->first();
        $client = Client::where('id',$service->client_id)->value('member');

        $email = $branch->email;



        $text = 'Service Child Updated Name.' . $client;

        Mail::send('service.service_create',$datas, function($message) use($email,$text) {
            $message->to($email);
            $message->subject($text);

        });

        $client = Client::where('id',$service->client_id)->value('member');
        $stores_token = User::where('id',$branch->id)->value('push_token');
        if($stores_token)
        {
           $device_token = $stores_token;
           $urls = env('Service_admin_link');
           $leads_id = $service->id;
           $name = 'Service';
           $title = 'Service Child Updated !!';
           $body = 'Service Child Updated.' . $client;

           $this->adminsendnotification($device_token,$title,$body,$urls,$leads_id,$name);
       }

         $datas = [
            'service' => $service,
        ];


        $employee = Employee::where('id',$service->employee_id)->first();
        $client = Client::where('id',$service->client_id)->value('member');

        $email = $branch->email;



        $text = 'Service Child Updated Name.' . $client;

        Mail::send('service.service_create',$datas, function($message) use($email,$text) {
            $message->to($email);
            $message->subject($text);

        });

        $client = Client::where('id',$service->client_id)->value('member');
        $stores_token = Employee::where('id',$employee->id)->value('push_token');
        if($stores_token)
        {
           $device_token = $stores_token;
           $urls = env('Service_employee_link');
           $leads_id = $service->id;
           $name = 'Service';
           $title = 'Service Child Updated !!';
           $body = 'Service Child Updated Name.' . $client;

           $this->sendnotification($device_token,$title,$body,$urls,$leads_id,$name);
       }

         return response()->json(['success'=>1,'message' => 'Service Updated child SuccessFully.']);

    }

    public function adminservicedelete(Request $request)
    {
         $this->validate($request, [
            'service_id' => 'required',
           

        ]);

         $service = Service::where('id',$request->service_id)->first();
         $servicechild = ServiceChild::where('service_id',$request->service_id)->delete();
         $service->delete();

          return response()->json(['success'=>1,'message' => 'Service Deleted SuccessFully.']);

    }

     public function adminservicedeletechild(Request $request)
    {
         $this->validate($request, [
            'id' => 'required',
           

        ]);

         
         $servicechild = ServiceChild::where('id',$request->id)->delete();
        

          return response()->json(['success'=>1,'message' => 'Service Child SuccessFully.']);

    }

     public function adminservicedeleteimage(Request $request)
    {
         $this->validate($request, [
            'id' => 'required',
           

        ]);

         
         $serviceimage = ServiceImage::where('id',$request->id)->delete();
        

          return response()->json(['success'=>1,'message' => 'Service Image Deleted SuccessFully.']);

    }

     public function adminservicestatus(Request $request)
    {
         $this->validate($request, [
            'id' => 'required',
            'status' => 'required',
           

        ]);

         $service = Service::where('id',$request->id)->first();
         $service->status = $request->status;
         $service->save();

                 $datas = [
            'service' => $service,
        ];


        $admin = User::where('id',1)->first();
        $client = Client::where('id',$service->client_id)->value('member');

        $email = $admin->email;



        $text = 'Service Status Change .' ;

        Mail::send('service.service_create',$datas, function($message) use($email,$text) {
            $message->to($email);
            $message->subject($text);

        });

        $client = Client::where('id',$service->client_id)->value('member');
        $stores_token = User::where('id',1)->value('push_token');
        if($stores_token)
        {
           $device_token = $stores_token;
           $urls = env('Service_admin_link');
           $leads_id = $service->id;
           $name = 'Service';
           $title = 'Service Status Change !!';
           $body = 'Service Status Change.';

           $this->adminsendnotification($device_token,$title,$body,$urls,$leads_id,$name);
       }

        $datas = [
            'service' => $service,
        ];


        $branch = User::where('id',$service->branch_id)->first();
        $client = Client::where('id',$service->client_id)->value('member');

        $email = $branch->email;


        $text = 'Service Status Change.';

        Mail::send('service.service_create',$datas, function($message) use($email,$text) {
            $message->to($email);
            $message->subject($text);

        });

        $client = Client::where('id',$service->client_id)->value('member');
        $stores_token = User::where('id',$branch->id)->value('push_token');
        if($stores_token)
        {
           $device_token = $stores_token;
           $urls = env('Service_admin_link');
           $leads_id = $service->id;
           $name = 'Service';
           $title = 'Service Status Change !!';
           $body = 'Service Status Change.';

           $this->adminsendnotification($device_token,$title,$body,$urls,$leads_id,$name);
       }

         $datas = [
            'service' => $service,
        ];


        $employee = Employee::where('id',$service->employee_id)->first();
        $client = Client::where('id',$service->client_id)->value('member');

        $email = $branch->email;



        $text = 'Service Status Change.';

        Mail::send('service.service_create',$datas, function($message) use($email,$text) {
            $message->to($email);
            $message->subject($text);

        });

        $client = Client::where('id',$service->client_id)->value('member');
        $stores_token = Employee::where('id',$employee->id)->value('push_token');
        if($stores_token)
        {
           $device_token = $stores_token;
           $urls = env('Service_employee_link');
           $leads_id = $service->id;
           $name = 'Service';
           $title = 'Service Status Change !!';
           $body = 'Service Status Change.';

           $this->sendnotification($device_token,$title,$body,$urls,$leads_id,$name);
       }

         return response()->json(['success'=>1,'message' => 'Change Status SuccessFully.']);
    }

    public function adminservice(Request $request)
    {
         $this->validate($request, [
            'id' => 'required',
           

        ]);

         
         $service = Service::with('child.company','child.product','child.category','client','branch','employee','image')->where('id',$request->id)->first();
        

          return response()->json(['success'=>1,'data' => $service]);

    }

    public function servicefilter(Request $request)
    {
        if(JWTAuth::user()->role == 1)
        {
            if($request->branch_id)
            {
                 $service = Service::with('child.company','child.product','child.category','client','branch','employee','image')->where('branch_id',$request->branch_id)->latest()->get();
            }elseif ($request->employee_id) {
                 $service = Service::with('child.company','child.product','child.category','client','branch','employee','image')->where('employee_id',$request->employee_id)->latest()->get();
            }elseif ($request->branch_id && $request->employee_id) {
                 $service = Service::with('child.company','child.product','child.category','client','branch','employee','image')->where('branch_id',$request->branch_id)->where('employee_id',$request->employee_id)->latest()->get();
            }else
            {
               $service = Service::with('child.company','child.product','child.category','client','branch','employee','image')->latest()->get();
            }
        }
        elseif(JWTAuth::user()->role == 2)
        {
            if ($request->employee_id) {
                 $service = Service::with('child.company','child.product','child.category','client','branch','employee','image')->where('employee_id',$request->employee_id)->latest()->get();
            }else
            {
               $service = Service::with('child.company','child.product','child.category','client','branch','employee','image')->where('branch_id',JWTAuth::user()->id)->latest()->get();
            }
        }

         return response()->json(['success'=>1,'data' => $service]);
    }

     public function taskfilter(Request $request)
    {
        if(JWTAuth::user()->role == 1)
        {
            if($request->branch_id)
            {
                 $task = Task::with('employee','branch','assign_to')->where('branch_id',$request->branch_id)->latest()->get();
            }elseif ($request->employee_id) {
                 $task = Task::with('employee','branch','assign_to')->where('employee_id',$request->employee_id)->latest()->get();
            }elseif ($request->branch_id && $request->employee_id) {
                 $task = Task::with('employee','branch','assign_to')->where('branch_id',$request->branch_id)->where('employee_id',$request->employee_id)->latest()->get();
            }else
            {
               $task = Task::latest()->get();
            }
        }
        elseif(JWTAuth::user()->role == 2)
        {
            if ($request->employee_id) {
                 $task = Task::with('employee','branch','assign_to')->where('employee_id',$request->employee_id)->latest()->get();
            }else
            {
               $task = Task::with('employee','branch','assign_to')->where('branch_id',JWTAuth::user()->id)->latest()->get();
            }
        }

         return response()->json(['success'=>1,'data' => $task]);
    }
   
    public function quotationfilter(Request $request)
    {
        if(JWTAuth::user()->role == 1)
        {
            if($request->branch_id)
            {
             $quotation = Quotation::with('client','branch','employee','delivery')->with('mainchild.quotationchild','mainchild.employees','mainchild.quotationchild.products','mainchild.quotationchild.productcategory','mainchild.quotationchild.companys','mainchild.quotationchild.warehouses','mainchild.quotationchild.gsts')->where('branch_id',$request->branch_id)->latest()->get();
            }elseif ($request->employee_id) {
                 $quotation = Quotation::with('client','branch','employee','delivery')->with('mainchild.quotationchild','mainchild.employees','mainchild.quotationchild.products','mainchild.quotationchild.productcategory','mainchild.quotationchild.companys','mainchild.quotationchild.warehouses','mainchild.quotationchild.gsts')->where('employee_id',$request->employee_id)->latest()->get();
            }elseif ($request->branch_id && $request->employee_id) {
                  $quotation = Quotation::with('client','branch','employee','delivery')->with('mainchild.quotationchild','mainchild.employees','mainchild.quotationchild.products','mainchild.quotationchild.productcategory','mainchild.quotationchild.companys','mainchild.quotationchild.warehouses','mainchild.quotationchild.gsts')->where('branch_id',$request->branch_id)->where('employee_id',$request->employee_id)->latest()->get();
            }elseif ($request->status) {
                  $quotation = Quotation::with('client','branch','employee','delivery')->with('mainchild.quotationchild','mainchild.employees','mainchild.quotationchild.products','mainchild.quotationchild.productcategory','mainchild.quotationchild.companys','mainchild.quotationchild.warehouses','mainchild.quotationchild.gsts')->where('status',$request->status)->latest()->get();
            }else
            {
               $quotation = Quotation::with('client','branch','employee','delivery')->with('mainchild.quotationchild','mainchild.employees','mainchild.quotationchild.products','mainchild.quotationchild.productcategory','mainchild.quotationchild.companys','mainchild.quotationchild.warehouses','mainchild.quotationchild.gsts')->latest()->get();
            }
        }
        elseif(JWTAuth::user()->role == 2)
        {
            if ($request->employee_id) {
                  $quotation = Quotation::with('client','branch','employee','delivery')->with('mainchild.quotationchild','mainchild.employees','mainchild.quotationchild.products','mainchild.quotationchild.productcategory','mainchild.quotationchild.companys','mainchild.quotationchild.warehouses','mainchild.quotationchild.gsts')->where('employee_id',$request->employee_id)->latest()->get();
            }elseif ($request->status) {
               $quotation = Quotation::with('client','branch','employee','delivery')->with('mainchild.quotationchild','mainchild.employees','mainchild.quotationchild.products','mainchild.quotationchild.productcategory','mainchild.quotationchild.companys','mainchild.quotationchild.warehouses','mainchild.quotationchild.gsts')->where('status',$request->status)->latest()->get();
            }
            else
            {
                 $quotation = Quotation::with('client','branch','employee','delivery')->with('mainchild.quotationchild','mainchild.employees','mainchild.quotationchild.products','mainchild.quotationchild.productcategory','mainchild.quotationchild.companys','mainchild.quotationchild.warehouses','mainchild.quotationchild.gsts')->where('branch_id',JWTAuth::user()->id)->latest()->get();
         
            }
        }

         return response()->json(['success'=>1,'data' => $quotation]);
    }

     public function attendancefilter(Request $request)
    {
        if(JWTAuth::user()->role == 1)
        {
            if($request->branch_id)
            {
                 $attendance = Attendance::with('employee','branch')->where('branch_id',$request->branch_id)->latest()->get();
            }elseif ($request->employee_id) {
                 $attendance = Attendance::with('employee','branch')->where('employee_id',$request->employee_id)->latest()->get();
            }elseif ($request->branch_id && $request->employee_id) {
                 $attendance = Attendance::with('employee','branch')->where('branch_id',$request->branch_id)->where('employee_id',$request->employee_id)->latest()->get();
            }else
            {
               $attendance = Attendance::with('employee','branch')->latest()->get();
            }
        }
        elseif(JWTAuth::user()->role == 2)
        {
            if ($request->employee_id) {
                 $attendance = Attendance::with('employee','branch')->where('employee_id',$request->employee_id)->latest()->get();
            }else
            {
               $attendance = Attendance::with('employee','branch')->where('branch_id',JWTAuth::user()->id)->latest()->get();
            }
        }

         return response()->json(['success'=>1,'data' => $attendance]);
    }

    public function leavefilter(Request $request)
    {
        if(JWTAuth::user()->role == 1)
        {
            if($request->branch_id)
            {

                 $leave = Leave::with('employee','branch')->where('branch_id',$request->branch_id)->latest()->get();
            }elseif ($request->employee_id) {

                 $leave = Leave::with('employee','branch')->where('employee_id',$request->employee_id)->latest()->get();
            }else
            {
               $leave = Leave::with('employee','branch')->latest()->get();
            }
        }
        elseif(JWTAuth::user()->role == 2)
        {
            if ($request->employee_id) {
                 $leave = Leave::with('employee','branch')->where('employee_id',$request->employee_id)->latest()->get();
            }else
            {
               $leave = Leave::with('employee','branch')->where('branch_id',JWTAuth::user()->id)->latest()->get();
            }
        }

         return response()->json(['success'=>1,'data' => $leave]);
    }

    public function clientfilter(Request $request)
    {
        if(JWTAuth::user()->role == 1)
        {
            if($request->branch_id)
            {
                 $client = CLient::with('branch','employee','clientdocument')->where('branch_id',$request->branch_id)->latest()->get();
            }elseif ($request->status) {
                 $client = CLient::with('branch','employee','clientdocument')->where('status',$request->status)->latest()->get();
            }elseif ($request->branch_id && $request->status) {
                 $client = CLient::with('branch','employee','clientdocument')->where('branch_id',$request->branch_id)->where('status',$request->status)->latest()->get();
            }else
            {
               $client = CLient::with('branch')->latest()->get();
            }
        }
        elseif(JWTAuth::user()->role == 2)
        {
            if ($request->status) {
                 $client = CLient::with('branch','employee','clientdocument')->where('status',$request->status)->latest()->get();
            }else
            {
               $client = CLient::with('branch','employee','clientdocument')->where('branch_id',JWTAuth::user()->id)->latest()->get();
            }
        }

         return response()->json(['success'=>1,'data' => $client]);
    }


}