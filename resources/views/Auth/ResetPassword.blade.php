<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<head>
    <!--====== Required meta tags ======-->
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <meta name="description" content="" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, shrink-to-fit=no"
    />

    <!--====== Title ======-->
    <title>Clear Building Solutions</title>

    <!--====== Favicon Icon ======-->
    <link
        rel="shortcut icon"
        href="{{asset('/assets/images/favicon.png')}}"
        type="image/png"
    />

    <!--====== Bootstrap css ======-->
    <link rel="stylesheet" href="{{asset('/assets/css/bootstrap.min.css')}}" />

    <!--====== Fontawesome css ======-->
    <link rel="stylesheet" href="{{asset('/assets/css/font-awesome.min.css')}}" />

    <!--====== Magnific Popup css ======-->
    <link rel="stylesheet" href="{{asset('/assets/css/animate.min.css')}}" />

    <!--====== Magnific Popup css ======-->
    <link rel="stylesheet" href="{{asset('/assets/css/magnific-popup.css')}}" />

    <!--====== Slick css ======-->
    <link rel="stylesheet" href="{{asset('/assets/css/slick.css')}}" />

    <!--====== Default css ======-->
    <link rel="stylesheet" href="{{asset('/assets/css/custom-animation.css')}}" />
    <link rel="stylesheet" href="{{asset('/assets/css/default.css')}}" />

    <!--====== Style css ======-->
    <link rel="stylesheet" href="{{asset('/assets/css/style.css')}}" />
</head>

<body>
<!--====== PART START ======-->

<header class="appie-header-area appie-sticky">
    <div class="container">
        <div class="header-nav-box">
            <div class="row align-items-center">
                <div class="col-lg-2 col-md-4 col-sm-5 col-6 order-1 order-sm-1">
                    <div class="appie-logo-box">
                        <a href="index.html">
                            <img src="{{asset('/assets/images/logo1.png')}}" alt="" />
                        </a>
                    </div>
                </div>

            </div>
        </div>

        <form method="POST" action="{{ url('/reset-password')}}">
        @csrf
        {{-- <input type="hidden" name="email_token" value="{{ $token }}" /> --}}
            <div class="form-group">
                <label for="exampleInputEmail1">Password</label>
                <input type="password" class="form-control" id="exampleInputEmail1" required name="password" aria-describedby="emailHelp" placeholder="Enter password">
                <small id="emailHelp" class="form-text text-muted">Change Password To The One You Will Remember.</small>
              </div>
              <div class="form-group">
                <label for="exampleInputPassword1">Confirm Password</label>
                <input type="password" name="password_confirmation" required class="form-control" id="exampleInputPassword1" placeholder="Password">
              </div>
            <button type="submit" class="btn btn-primary">Change Password</button>
        </form>
    </div>
</header>

<!--====== PART ENDS ======-->
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>


<div class="back-to-top">
    <a href="#"><i class="fal fa-arrow-up"></i></a>
</div>
<!--====== APPIE BACK TO TOP PART ENDS ======-->

<!--====== jquery js ======-->
<script src="{{asset('/asset/js/vendor/modernizr-3.6.0.min.js')}}"></script>
<script src="{{asset('/assets/js/vendor/jquery-1.12.4.min.js')}}"></script>

<!--====== Bootstrap js ======-->
<script src="{{asset('/assets/js/bootstrap.min.js')}}></script>
<script src="{{asset('/assets/js/popper.min.js')}}></script>

<!--====== wow js ======-->
<script src="/assets/js/wow.js"></script>

<!--====== Slick js ======-->
<script src="/assets/js/jquery.counterup.min.js"></script>
<script src="/assets/js/waypoints.min.js"></script>

<!--====== TweenMax js ======-->
<script src="/assets/js/TweenMax.min.js"></script>

<!--====== Slick js ======-->
<script src="/assets/js/slick.min.js"></script>

<!--====== Magnific Popup js ======-->
<script src="/assets/js/jquery.magnific-popup.min.js"></script>

<!--====== Main js ======-->
<script src="/assets/js/main.js"></script>
</body>

<!-- Mirrored from quomodosoft.com/html/appie/assets/appie-demo/error.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 14 Sep 2022 19:54:52 GMT -->
</html>
