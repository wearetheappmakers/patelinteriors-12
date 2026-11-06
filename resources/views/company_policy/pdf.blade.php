<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Company Policy</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            color: #000;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }

        /* The double border frame seen in the image */
        .page-border {
            border: 3px solid #000;
            padding: 5px; 
            min-height: 95vh;
        }

        .inner-border {
            border: 1px solid #000;
            padding: 40px;
            min-height: 90vh;
            display: flex;
            flex-direction: column;
        }

        /* Header Layout */
        .header-container {
            text-align: right;
            margin-bottom: 50px;
        }

        .logo {
            width: 90px;    
            height: 70px;     
            object-fit: contain; 
        }

        /* Centered Titles */
        .company-name {
            text-align: center;
            font-size: 28px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 60px;
        }

        .main-title {
            text-align: center;
            font-size: 32px;
            text-decoration: underline;
            margin-bottom: 60px;
        }

        .section-heading {
            text-align: center;
            font-size: 18px;
            text-decoration: underline;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .welcome-text {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 40px;
        }

        .content {
            text-align: justify;
            font-size: 16px;
            flex-grow: 1; /* Pushes the thank you to the bottom if content is short */
        }

    
    </style>
</head>
<body>

<div class="page-border">
    <div class="inner-border">
        
        <div class="header-container">
            @if(!empty($branchlogo))
                <img src="{{ public_path('user/'.$branchlogo) }}" class="logo">
            @else
                <div style="height: 80px;"></div> 
            @endif
        </div>

        <div class="company-name">
            {{ $branchname }}
        </div>

        <div class="main-title">
            Company Policy
        </div>

        <div class="section-heading">
            ABOUT US
        </div>

        <div class="welcome-text">
            Welcome to {{ $branchname }}
        </div>

        <div class="content">
            {!! $data->policy_description !!}
        </div>

      
    </div>
</div>

</body>
</html>