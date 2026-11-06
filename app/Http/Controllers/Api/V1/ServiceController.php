<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\User;
use DataTables;
use App\Task;
use App\Service;
use App\ProductCategory;
use App\ServiceChild;
use App\ServiceImage;
use App\Employee;
use App\Client;


use Auth;
use Mail;
use Illuminate\Http\Request;
use JWTAuth;
use JWTAuthException;

class ServiceController extends Controller
{
    public function __construct(Employee $employee)
    {
        $this->user = $employee;
        \Config::set('jwt.user', "App\Employee");
        \Config::set('auth.providers', ['users' => [
            'driver' => 'eloquent',
            'model' => \App\Employee::class,
        ]]);
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
    public function getservice()
    {
       
        $service = Service::with('child.company','child.product','child.category','client','image')->where('employee_id',JWTAuth::user()->id)->latest()->get();
        
        return response()->json(['success'=>1,'data' => $service]);
    }

    public function createservice(Request $request)
    {
        $service = new Service();

       
           
                $service->branch_id = JWTAuth::user()->branch_id;
                $service->employee_id = JWTAuth::user()->id;
            

       

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

        


        return response()->json(['success'=>1,'message' => 'Service Created SuccessFully.']);
    }

   

    public function updateservice(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',


        ]);
       

         $service = Service::where('id',$request->id)->first();

       
           
                $service->branch_id = JWTAuth::user()->branch_id;
                $service->employee_id = JWTAuth::user()->id;
            

       

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

       

        return response()->json(['success'=>1,'message' => 'Service Updated SuccessFully.']);
    }

    public function createservicechild(Request $request)
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

        


        return response()->json(['success'=>1,'message' => 'Service Created child SuccessFully.']);
    }

    public function updateservicechild(Request $request)
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

        
         return response()->json(['success'=>1,'message' => 'Service Updated child SuccessFully.']);

    }

    public function servicedelete(Request $request)
    {
         $this->validate($request, [
            'service_id' => 'required',
           

        ]);

         $service = Service::where('id',$request->service_id)->first();
         $servicechild = ServiceChild::where('service_id',$request->service_id)->delete();
         $service->delete();

          return response()->json(['success'=>1,'message' => 'Service Deleted SuccessFully.']);

    }

     public function servicedeletechild(Request $request)
    {
         $this->validate($request, [
            'id' => 'required',
           

        ]);

         
         $servicechild = ServiceChild::where('id',$request->id)->delete();
        

          return response()->json(['success'=>1,'message' => 'Service Child SuccessFully.']);

    }

    public function servicestatus(Request $request)
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

        


    	 return response()->json(['success'=>1,'message' => 'Change Status SuccessFully.']);
    }

    public function servicedeleteimage(Request $request)
    {
         $this->validate($request, [
            'id' => 'required',
           

        ]);

         
         $serviceimage = ServiceImage::where('id',$request->id)->delete();
        

          return response()->json(['success'=>1,'message' => 'Service Image Deleted SuccessFully.']);

    }

     public function service(Request $request)
    {
         $this->validate($request, [
            'id' => 'required',
           

        ]);

         
         $service = Service::with('child.company','child.product','child.category','client','image')->where('id',$request->id)->first();
        

          return response()->json(['success'=>1,'data' => $service]);

    }

   

}