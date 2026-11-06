<!DOCTYPE html>
<html>
<head>
     <style type="text/css">
          body{
               font-family: "verdana", sans-serif;
               font-size: 12px !important;
          }
          td,th{
               font-family: "verdana", sans-serif;
               font-size: 12px !important;
               border-collapse: collapse;
               border: 1px solid #737373 !important;
               padding: 2px !important; 
          }
          .no-break {
               page-break-before: always !important;
          }
          ol li{
               text-align: justify;
               font-size:70% !important;
          }
          {
               box-sizing: border-box;
          }
          .row {
               margin-left:-5px;
               margin-right:-5px;
          }
          .column {
               width: 100%;
          }
          .column1 {
               float: left;
               width: 50%;
          }
          table {
               border-collapse: collapse;
               border-spacing: 0;
               width: 100%;
               border: 1px;
          }
     </style>
     <title>Interior Visit</title>
</head> 
<div class="row">
     <div class="column">
          <table>
               @if(Auth::guard('user')->check())
               @if(Auth::user()->role == 1)
               <tr><th colspan="5" style="font-size: 18px !important;"><center>Interior Visit</center></th></tr>
               @endif
               @endif
               @if(Auth::guard('user')->check())
               @if(Auth::user()->role == 2)
               <tr><th colspan="4" style="font-size: 18px !important;"><center>Interior Visit</center></th></tr>
               @endif
               @endif
               <tr>
                    <th width="5%">Sr No.</th>
                    @if(Auth::guard('user')->check())
                    @if(Auth::user()->role == 1)
                    <th>Branch Name</th>
                    @endif
                    @endif
                    <th>Employee Name</th>
                    <th>Architect Name</th>
                    <th>Date</th>
               </tr>
               @if(Auth::guard('user')->check())
               @if(Auth::user()->role == 1)
               @if($from_date && $to_date)
               <?php
               $from = date('Y-m-d', strtotime($from_date));
               $to = date('Y-m-d', strtotime($to_date));
               $data = App\Visits::whereDate('date','>=',$from)->whereDate('date','<=',$to)->get();
               $number = 1;
               ?>

               @foreach($data as $info)
               <?php 
               $branch = App\User::where('id',$info->branch_id)->value('name');
               $employee = App\Employee::where('id',$info->employee_id)->value('name');
               $architect = App\SourceName::where('id',$info->architect_id)->value('name');
               $date = date('d/m/Y', strtotime($info->date)); 
               ?>
               <tr>
                    <td><center>{{ $number++ }}</center></td>
                    <td>{{ $branch }}</td>
                    <td>{{ $employee }}</td>
                    <td>{{ $architect }}</td>
                    <td>{{ $date }}</td>
               </tr>
               @endforeach
               @elseif($branch_id && $employee_id && $source_name)
               <?php
               $data = App\Visits::where('branch_id',$branch_id)->where('employee_id',$employee_id)->where('architect_id',$source_name)->get();
               $number = 1;
               ?>

               @foreach($data as $info)
               <?php 
               $branch = App\User::where('id',$info->branch_id)->value('name');
               $employee = App\Employee::where('id',$info->employee_id)->value('name');
               $architect = App\SourceName::where('id',$info->architect_id)->value('name');
               $date = date('d/m/Y', strtotime($info->date)); 
               ?>
               <tr>
                    <td><center>{{ $number++ }}</center></td>
                    <td>{{ $branch }}</td>
                    <td>{{ $employee }}</td>
                    <td>{{ $architect }}</td>
                    <td>{{ $date }}</td>
               </tr>
               @endforeach
               @elseif($branch_id && $employee_id)
               <?php
               $data = App\Visits::where('branch_id',$branch_id)->where('employee_id',$employee_id)->get();
               $number = 1;
               ?>

               @foreach($data as $info)
               <?php 
               $branch = App\User::where('id',$info->branch_id)->value('name');
               $employee = App\Employee::where('id',$info->employee_id)->value('name');
               $architect = App\SourceName::where('id',$info->architect_id)->value('name');
               $date = date('d/m/Y', strtotime($info->date)); 
               ?>
               <tr>
                    <td><center>{{ $number++ }}</center></td>
                    <td>{{ $branch }}</td>
                    <td>{{ $employee }}</td>
                    <td>{{ $architect }}</td>
                    <td>{{ $date }}</td>
               </tr>
               @endforeach
               @elseif($branch_id && $source_name)
               <?php
               $data = App\Visits::where('branch_id',$branch_id)->where('architect_id',$source_name)->get();
               $number = 1;
               ?>

               @foreach($data as $info)
               <?php 
               $branch = App\User::where('id',$info->branch_id)->value('name');
               $employee = App\Employee::where('id',$info->employee_id)->value('name');
               $architect = App\SourceName::where('id',$info->architect_id)->value('name');
               $date = date('d/m/Y', strtotime($info->date)); 
               ?>
               <tr>
                    <td><center>{{ $number++ }}</center></td>
                    <td>{{ $branch }}</td>
                    <td>{{ $employee }}</td>
                    <td>{{ $architect }}</td>
                    <td>{{ $date }}</td>
               </tr>
               @endforeach
               @elseif($branch_id)
               <?php
               $data = App\Visits::where('branch_id',$branch_id)->get();
               $number = 1;
               ?>

               @foreach($data as $info)
               <?php 
               $branch = App\User::where('id',$info->branch_id)->value('name');
               $employee = App\Employee::where('id',$info->employee_id)->value('name');
               $architect = App\SourceName::where('id',$info->architect_id)->value('name');
               $date = date('d/m/Y', strtotime($info->date)); 
               ?>
               <tr>
                    <td><center>{{ $number++ }}</center></td>
                    <td>{{ $branch }}</td>
                    <td>{{ $employee }}</td>
                    <td>{{ $architect }}</td>
                    <td>{{ $date }}</td>
               </tr>
               @endforeach
               @else
               <?php
               $data = App\Visits::get();
               $number = 1;
               ?>

               @foreach($data as $info)
               <?php 
               $branch = App\User::where('id',$info->branch_id)->value('name');
               $employee = App\Employee::where('id',$info->employee_id)->value('name');
               $architect = App\SourceName::where('id',$info->architect_id)->value('name');
               $date = date('d/m/Y', strtotime($info->date)); 
               ?>
               <tr>
                    <td><center>{{ $number++ }}</center></td>
                    <td>{{ $branch }}</td>
                    <td>{{ $employee }}</td>
                    <td>{{ $architect }}</td>
                    <td>{{ $date }}</td>
               </tr>
               @endforeach
               @endif
               @endif
               @endif
               @if(Auth::guard('user')->check())
               @if(Auth::user()->role == 2)
               @if($from_date && $to_date)
               <?php
               $from = date('Y-m-d', strtotime($from_date));
               $to = date('Y-m-d', strtotime($to_date));
               $data = App\Visits::where('branch_id',Auth::user()->id)->whereDate('date','>=',$from)->whereDate('date','<=',$to)->get();
               $number = 1;
               ?>

               @foreach($data as $info)
               <?php 
               $employee = App\Employee::where('id',$info->employee_id)->value('name');
               $architect = App\SourceName::where('id',$info->architect_id)->value('name');
               $date = date('d/m/Y', strtotime($info->date)); 
               ?>
               <tr>
                    <td><center>{{ $number++ }}</center></td>
                    <td>{{ $employee }}</td>
                    <td>{{ $architect }}</td>
                    <td>{{ $date }}</td>
               </tr>
               @endforeach
               @elseif($employee_id && $source_name)
               <?php
               $data = App\Visits::where('branch_id',Auth::user()->id)->where('employee_id',$employee_id)->where('architect_id',$source_name)->get();
               $number = 1;
               ?>

               @foreach($data as $info)
               <?php 
               $employee = App\Employee::where('id',$info->employee_id)->value('name');
               $architect = App\SourceName::where('id',$info->architect_id)->value('name');
               $date = date('d/m/Y', strtotime($info->date)); 
               ?>
               <tr>
                    <td><center>{{ $number++ }}</center></td>
                    <td>{{ $employee }}</td>
                    <td>{{ $architect }}</td>
                    <td>{{ $date }}</td>
               </tr>
               @endforeach
               @elseif($employee_id)
               <?php
               $data = App\Visits::where('branch_id',Auth::user()->id)->where('employee_id',$employee_id)->get();
               $number = 1;
               ?>

               @foreach($data as $info)
               <?php 
               $employee = App\Employee::where('id',$info->employee_id)->value('name');
               $architect = App\SourceName::where('id',$info->architect_id)->value('name');
               $date = date('d/m/Y', strtotime($info->date)); 
               ?>
               <tr>
                    <td><center>{{ $number++ }}</center></td>
                    <td>{{ $employee }}</td>
                    <td>{{ $architect }}</td>
                    <td>{{ $date }}</td>
               </tr>
               @endforeach
               @elseif($source_name)
               <?php
               $data = App\Visits::where('branch_id',Auth::user()->id)->where('architect_id',$source_name)->get();
               $number = 1;
               ?>

               @foreach($data as $info)
               <?php 
               $employee = App\Employee::where('id',$info->employee_id)->value('name');
               $architect = App\SourceName::where('id',$info->architect_id)->value('name');
               $date = date('d/m/Y', strtotime($info->date)); 
               ?>
               <tr>
                    <td><center>{{ $number++ }}</center></td>
                    <td>{{ $employee }}</td>
                    <td>{{ $architect }}</td>
                    <td>{{ $date }}</td>
               </tr>
               @endforeach
               @else
               <?php
               $data = App\Visits::where('branch_id',Auth::user()->id)->get();
               $number = 1;
               ?>

               @foreach($data as $info)
               <?php 
               $employee = App\Employee::where('id',$info->employee_id)->value('name');
               $architect = App\SourceName::where('id',$info->architect_id)->value('name');
               $date = date('d/m/Y', strtotime($info->date)); 
               ?>
               <tr>
                    <td><center>{{ $number++ }}</center></td>
                    <td>{{ $employee }}</td>
                    <td>{{ $architect }}</td>
                    <td>{{ $date }}</td>
               </tr>
               @endforeach
               @endif
               @endif
               @endif
          </table>
     </div>
</div>
</html>