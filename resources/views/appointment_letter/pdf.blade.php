<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Appointment Letter - Zeqon</title>
    <style>
        @page {
            margin: 0.5in 0.5in 0.8in 0.5in; /* Bottom margin increased for footer */
        }
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 14px;
            color: #000;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }

        /* HEADER STYLES */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .logo-cell {
            width: 30%; /* Reduced width */
            vertical-align: middle;
            padding-bottom: 10px;
        }

        .logo-cell img {
            width: 130px; /* DECREASED IMAGE SIZE */
        }

        .vertical-line-cell {
            width: 1px;
            border-left: 1.5px solid #000; /* DECREASED THICKNESS */
            padding: 0;
        }

        .company-details-cell {
            width: 69%;
            text-align: left;
            padding-left: 15px;
            vertical-align: middle;
            padding-bottom: 10px;
        }

        .company-name {
            font-size: 20px;
            font-weight: bold;
            margin: 0;
            display: block;
        }

        .address-text {
            font-size: 11px;
            margin: 3px 0;
            color: #333;
        }

        .contact-text {
            font-size: 11px;
            margin: 0;
        }

        .gold-label {
            color: #b79a55;
            font-weight: bold;
        }

        .header-bottom-border {
            border-bottom: 2px solid #000;
            width: 100%;
            margin-bottom: 25px;
        }

        /* CONTENT STYLES */
        .title {
            text-align: center;
            text-decoration: underline;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .content-table { width: 100%; margin-bottom: 15px; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        ol li { margin-bottom: 8px; text-align: justify; }

        /* FOOTER STYLES */
        .footer {
            position: fixed;
            bottom: -0.5in; /* Moves it to the absolute edge of the page */
            left: -0.5in;
            right: -0.5in;
            background-color: #222222; /* Dark black/grey bar */
            color: #ffffff;
            text-align: center;
            padding: 15px 0;
            font-size: 11px;
        }
        /* ELSE HEADER COMPACT STYLE */
        .else-header {
            width: 100%;
            background: #2b2b2b;
            color: #fff;
            padding: 10px 18px; /* REDUCED */
        }

        .else-header td {
            vertical-align: middle;
        }

        .else-left {
            font-size: 11px; /* smaller */
            line-height: 1.4; /* tighter */
        }

        .else-right img {
            height: 100px; /* controlled logo */
        }

        .else-logo-text {
            font-size: 18px; 
            font-weight: bold; 
            color: #b79a55;
        }
    </style>
</head>
<body>
@if($data->branch_id == 6)
@php
$user = \App\User::where('id', $data->branch_id)->first();
@endphp

{{-- HEADER SECTION --}}
<table class="header-table">
    <tr>
        <td class="logo-cell">
            @if(!empty($user->image))
                <img src="{{ public_path('user/'.$user->image) }}">
            @else
                <div style="font-size: 24px; font-weight: bold; color: #b79a55;">ZEQON</div>
            @endif
        </td>

        <td class="vertical-line-cell"></td>

        <td class="company-details-cell">
            <span class="company-name">Zeqon Luxury Manor LLP</span> 
            <p class="address-text">
                165, The View, Dr Anne Besant Rd, Office Num 2-4, Worli, Mumbai 400018.
            </p>
            <p class="contact-text">
                <strong>T:</strong> {{ $user->number }}
                &nbsp;&nbsp;
                <span class="gold-label">Email:</span> {{ $user->email }}
            </p>
        </td>
    </tr>
</table>

<div class="header-bottom-border"></div>

<div class="title">APPOINTMENT LETTER</div> 

<table class="content-table">
    <tr>
        <td></td>
        <td class="text-right">
            <strong>Date:</strong> {{ \Carbon\Carbon::parse($data->date)->format('jS F, Y') }}
        </td>
    </tr>
</table>

<p><span class="bold">Employee Name:</span> {{ $data->employee_name }} </p>
<p><span class="bold">Address:</span> {{ $data->address }}</p>

<p style="text-align:center;">
    <strong>Sub: Appointment for the position of “{{ $data->position }}”</strong> 
</p>

<p>
    We are pleased to inform you that you have successfully cleared the interview process and have been selected
    for the position of “{{ $data->position }}” in our organization. 
</p>

<ol>
    <li>Your employment with us has commenced on <strong>{{ \Carbon\Carbon::parse($data->joining_date)->format('jS F, Y') }}</strong>.</li>
    <li>You will be initially based at our Mumbai location. </li>
    <li>You will be reporting to your concerned authority. However, you will also be answerable to the Senior Management of “ZI”. </li>
    <li>Your monthly remuneration will be Rs. <strong>{{ number_format($data->amount) }}</strong>/- and shall be subject to statutory tax deduction as and if applicable.</li>
    <li>Your primary duties and responsibilities are mentioned in detail in the KRA (Key Result Areas).</li>
    <li>You will be on probation for a period of three months from the time of joining which can be extended upon the discretion of the management. </li>
    <li>The notice period shall be 15 days during probation and 30 days thereafter.</li>
    <li>Your employment will be governed by the company’s policies policies (attached with this document), rules, regulations, and such practices, systems, policies, and procedures as they exist now or as amended from time to time. </li>
</ol>

<p>
    As a token of your acceptance of the above terms and conditions of your employment, KRAs and Company Policies,
    kindly sign the duplicate copy of this letter and return to us. We are happy to welcome you to
    <strong>“{{ $user->name }}”</strong> and wish you success in your new role. 
</p>

<p>Thanking you,</p>
<p class="bold">“{{ $user->name }}”</p>

<br>
<p>
    {{ $data->hr_admin }}<br> 
    Manager HR & Admin 
</p>

<br>

<p>
    I, __________________ have read the above terms and conditions and the attached KRA sheet and accept the appointment order. 
</p>

<br><br>

{{-- SIGNATURE SECTION ABOVE FOOTER --}}
<table class="content-table" style="margin-bottom: 40px;">
<tr>

<td>
<strong>Date:</strong>
@if($data->date)
    {{ \Carbon\Carbon::parse($data->date)->format('jS F, Y') }}
@else
    ___________
@endif
</td>

<td class="text-right" style="text-align:center;">

<strong>Signature</strong>

</td>

</tr>
</table>

{{-- BLACK BAR FOOTER --}}
<div class="footer">
    Address – Zeqon Luxury Manor LLP, 165, The View, Dr Anne Besant Rd, Office Num 2-4, Worli, Mumbai 400018.
</div>

{{-- ================= END MANOR FORMAT ================= --}}
@elseif($data->branch_id == 5)
@php
$user = \App\User::where('id', $data->branch_id)->first();
@endphp


{{-- BLACK HEADER (COMPACT) --}}
<div class="else-header">

    <table width="100%" style="border:none;">
        <tr style="border:none;">
            
            {{-- LEFT SIDE --}}
            <td class="else-left" style="width:65%; border:none;">
                <div>www.zeqon.co.in</div>
                <div>Zeqon International LLP</div>
                <div>Vrundawan City, F.F Shop 17</div>
                <div>Rajkot Gujarat - 360004</div>
            </td>

            {{-- RIGHT SIDE --}}
            <td class="else-right" style="width:35%; text-align:right; border:none;">
                @if(!empty($user->image))
                    <img src="{{ public_path('user/'.$user->image) }}">
                @else
                    <div class="else-logo-text">ZEQON</div>
                @endif
            </td>

        </tr>
    </table>

</div>

{{-- TITLE --}}
<div style="text-align:center; margin:30px 0 20px 0;">
    <strong style="text-decoration: underline; font-size:16px;">APPOINTMENT LETTER</strong>
</div>

<table class="content-table">
    <tr>
        <td></td>
        <td class="text-right">
            <strong> {{ \Carbon\Carbon::parse($data->date)->format('jS F, Y') }} </strong>
        </td>
    </tr>
</table>

<p><span class="bold">{{ $data->employee_name }}</span> </p>
<p><span class="bold">Address:</span> {{ $data->address }}</p>

<p style="text-align:center;">
    <strong>Sub: Appointment for the position of “{{ $data->position }}”</strong> 
</p>

<p>
    We are pleased to inform you that you have successfully cleared the interview process and have been selected
    for the position of “{{ $data->position }}” in our organization. 
</p>

<ol>
    <li>Your employment with us has commenced on <strong>{{ \Carbon\Carbon::parse($data->joining_date)->format('jS F, Y') }}</strong>.</li>
    <li>You will be initially based at our Mumbai location.</li>
    <li>You will be reporting to your concerned authority. However, you will also be answerable to the Senior Management of “ZI”.</li>
    <li>Your monthly remuneration will be Rs. <strong>{{ number_format($data->amount) }}</strong>/- and shall be subject to statutory tax deduction as and if applicable.</li>
    <li>Your primary duties and responsibilities are mentioned in detail in the KRA (Key Result Areas).</li>
    <li>You will be on probation for a period of three months from the time of joining which can be extended upon the discretion of the management.</li>
    <li>The notice period shall be 15 days during probation and 30 days thereafter.</li>
    <li>Your employment will be governed by the company’s policies (attached with this document), rules, regulations, and such practices, systems, policies, and procedures as they exist now or as amended from time to time.</li>
</ol>

<p>
    As a token of your acceptance of the above terms and conditions of your employment, KRAs and Company Policies,
    kindly sign the duplicate copy of this letter and return to us. We are happy to welcome you to
    <strong>“{{ $user->name }}”</strong> and wish you success in your new role.
</p>

<p>Thanking you,</p>
<p class="bold">“{{ $user->name }}”</p>

<br>

<p>
    {{ $data->hr_admin }}<br> 
    Manager HR & Admin 
</p>

<br><br>

<p>
    I, {{ $data->employee_name }} have read the above terms and conditions and the attached KRA sheet and accept the appointment order.
</p>

<br><br>

{{-- SIGNATURE SECTION --}}
<table class="content-table" style="margin-bottom: 40px;">
<tr>

<td>
<strong>Date:</strong>
@if($data->date)
    {{ \Carbon\Carbon::parse($data->date)->format('jS F, Y') }}
@else
    ___________
@endif
</td>

<td class="text-right" style="text-align:center;">
    <strong>Signature</strong><br>

</td>

</tr>
</table>

{{-- FOOTER (ONLY ONCE) --}}
<div class="footer">
    Address – Zeqon Interernational LLP, Vrundawan City, F.F Shop 17, Rajkot Gujarat - 360004 
</div>

@else
@endif
</body>
</html>