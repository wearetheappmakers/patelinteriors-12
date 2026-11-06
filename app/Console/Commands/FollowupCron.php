<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Employee;
use App\Lead;
use App\Client;
use Mail;
use App\LeadFollowUp;
use DB;

class FollowupCron extends Command
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

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'FollowupCron:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ABC';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $followup = LeadFollowUp::whereDate('date',date('Y-m-d'))->get();
        foreach ($followup as $key => $value) {


            $lead = Lead::where('id',$value->lead_id)->first();
            $datas = [

                'lead' => $lead,
                'employee' => $value,

            ];

            $client = Client::where('id',$lead->client_id)->value('member');

            $employee = Employee::where('id',$value->employee_id)->first();

            $email = $employee->email;

            $text = 'Followup' . $client;



            Mail::send('lead.followup_email',$datas, function($message) use($email,$text) {

                $message->to($email);

                $message->subject($text);

            });



            $stores_token = Employee::where('id',$employee->id)->value('push_token');

            if($stores_token)

            {

                $device_token = $stores_token;

                $urls = env('Lead_employee_link');

                $leads_id = $lead->id;

                $name = 'Followup';

                $title = 'Todays Followup  !!';

                $body = 'Today Followup.' . $client;



                $this->sendnotification($device_token,$title,$body,$urls,$leads_id,$name);

            }

        }
    }
}
