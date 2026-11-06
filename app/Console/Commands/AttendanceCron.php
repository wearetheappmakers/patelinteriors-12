<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Attendance;
use App\Employee;
use DB;
use Excel;
use Mail;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AttendanceCron extends Command
{
    protected $signature = 'AttendanceCron:check';

    protected $description = 'ABC';

    public function __construct()
    {
        parent::__construct();
    }
    function sendnotification($pushtoken,$title,$body,$name)
    {
        $firebaseToken = [$pushtoken];
        $SERVER_API_KEY = env('Employee_server');
        $data = [
            "registration_ids" => $firebaseToken,
            "notification" => [
                "title" => $title,
                "body" => $body,
            ],
            "data" => [
                'name' => $name,
            ]
        ];
        $dataString = json_encode($data);
        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        $response = curl_exec($ch);
    }
    public function handle()
    {
        // if(!(date('l') == 'Sunday'))
        // {
        //     $employee = Employee::where('status',1)->get();
        //     foreach ($employee as $key => $value)
        //     {
        //         $attendance = Attendance::where('employee_id',$value->id)->where('date',date('Y-m-d'))->first();
        //         if(!isset($attendance))
        //         {
        //             if($value->stores_token)
        //             {
        //                 $device_token = $value->stores_token;
        //                 $name = 'Attedance';
        //                 $title = 'Today('.date("d-m-Y").') !!';
        //                 $body = 'Your Attendance not Marked.';
        //                 $this->sendnotification($device_token,$title,$body,$name);
        //             }
        //             if($value->email)
        //             {
        //                 $branch = User::where('id',$value->branch_id)->first();
        //                 $datas = [
        //                     'employee' => $value->name,
        //                     'branch_name' => $branch->name,
        //                 ];
        //                 $email = $value->email;
        //                 $text = 'Your Attendance not Marked - '.$branch->name;
        //                 Mail::send('attendance.notmarked',$datas, function($message) use($email,$text) {
        //                     $message->to($email);
        //                     $message->subject($text);
        //                 });
        //             }
        //         }
        //     }
        // }

        if (Carbon::today()->isSunday()) {
            return;
        }

        $today = Carbon::today()->format('Y-m-d');
        $employees = Employee::where('status', 1)->get();

        foreach ($employees as $employee) {
            $attendanceExists = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->exists();

            if ($attendanceExists) {
                continue;
            }
            if (!empty($employee->stores_token)) {
                $device_token = $employee->stores_token;
                $title = 'Today (' . Carbon::today()->format('d-m-Y') . ') !!';
                $body = 'Your attendance is not marked.';
                $name = 'Attendance';

                $this->sendnotification($device_token, $title, $body, $name);
            }
            if (!empty($employee->email)) {

                $branchName = '';

                if (!empty($employee->branch_id)) {
                    $branch = User::where('id', $employee->branch_id)->first();
                    $branchName = $branch ? $branch->name : '';
                }

                $datas = [
                    'employee'   => $employee->name,
                    'branch_name'=> $branchName,
                ];

                $email = $employee->email;
                $subject = 'Your Attendance Not Marked - ' . $branchName;

                Mail::send('attendance.notmarked', $datas, function ($message) use ($email, $subject) {
                    $message->to($email);
                    $message->subject($subject);
                });

                Log::info('Email sent to: ' . $employee->email .' Emaployee ID: ' . $employee->id . '');
            }
        }
    }
}
