
<h4>Subject - {{$leave->subject}}</h4>
<p>Request Start Date - {{date('d-m-Y', strtotime($leave->from_date))}}</p>
<p>Request End Date - {{date('d-m-Y', strtotime($leave->to_date))}}</p>

<p>Approved Start Date - {{date('d-m-Y', strtotime($leave->approved_from_date))}}</p>
<p>Approved End Date - {{date('d-m-Y', strtotime($leave->approved_to_date))}}</p> 
