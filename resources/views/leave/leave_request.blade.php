
<h4>Subject - {{$leave->subject}}</h4>
<p> Start Date - {{date('d-m-Y', strtotime($leave->from_date))}}</p>
<p>	End Date - {{date('d-m-Y', strtotime($leave->to_date))}}</p>
<p> Reason - {{$leave->reason}}</p>
