<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pdf Template</title>

    <style>
        /* @font-face {
            font-family: myFirstFont;
            src: url(assets/css/ReadexPro-Light.ttf);
        } */


        body {
            font-family: 'Arial', sans-serif;
            margin: 0px;
        }

        .aside {
            width: 20%;
            height: 100%;
            background-color: #EEEFF0;
            float: right;
            /* align items center top */
            /* padding-left: 20px; */



        }

        .brand {
            padding-left: 20px;
            padding-top: 70px;
        }

        .brand p {
            font-size: 15px;
            color: #BAC0FF;
            /* float: right; */
            /* margin-left: 40px; */
            padding: 0px;
            margin-top: 0px;
        }

        .main {
            width: 80%;
            height: 100vh;
            background-color: white;
            float: left;
        }

        ul {
            list-style-type: none;
            border-left: 3px solid #BAC0FF;
        }

        /* add opacity to header headings */
        .header h1 {
            opacity: 0.2;
            font-size: 50px;
            margin: 0px;
        }

        .header {
            padding-left: 60px;
            padding-top: 60px;
            padding-bottom: 60px;


        }

        .list ul {
            padding-left: 20px;
        }

        .list {
            padding-left: 60px;
        }

        .site-data {
            padding-left: 60px;
            padding-right: 60px;
            padding-bottom: 60px;
            /*  center elements */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;

        }
    </style>
</head>

<body>
<div class="container">
    <div class="main">
        <div class="header">
            <h1>3 Bids</h1>
            <h1>1 Source</h1>
        </div>

        <div class="list">
            <ul>
                <li>Project Title : {{$title}}</li>
                <li>Project Rate : {{$cost}}</li>
                <li>Service Type: {{$service}}</li>
                <li>Project Location: Project location goes here</li>
                <li>Project Timeline: {{ $start_date }}-{{$end_date}}</li>
            </ul>
        </div>
        <div class="site-data">
            <div class="item">
                <h3><strong>Site Info</strong></h3>
                <p>
                    {!! $site_info !!}
                </p>
            </div>
            <div class="item">
                <h3><strong>Exceution Plan</strong></h3>
                <p>
                    {!! $execution_plan !!}
                </p>
            </div>
            <div class="item">
                <h3><strong>Contract Terms Conditions</strong></h3>
                <p>
                    {!! $contract_terms_conditions !!}
                </p>
            </div>
        </div>
    </div>
    <div class="aside">
        <div class="brand">
            <img width="100" height="30" src="assets/images/logo1.png" />
            <p>3 Bids 1 Source</p>
        </div>
    </div>
</div>
</body>

</html>
