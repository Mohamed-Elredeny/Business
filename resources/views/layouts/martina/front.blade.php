<!DOCTYPE html>
<html lang="en">
<head>
    <title>@yield("title", "Ejada")</title>
    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="{{asset("images/icon.png")}}">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800'
          rel='stylesheet' type='text/css'>
    <!-- FontAwesome JS -->
    <script defer src="{{asset("fontawesome/js/all.js")}}"></script>
    <!-- Global CSS -->
    <link rel="stylesheet" href="{{asset("plugins/bootstrap/css/bootstrap.min.css")}}">
    <!-- Plugins CSS -->
    <link rel="stylesheet" href="{{asset("plugins/prism/prism.css")}}">
    <link rel="stylesheet" href="{{asset("plugins/elegant_font/css/style.css")}}">
    @yield("style")
    <!-- Theme CSS -->
    <link id="theme-style" rel="stylesheet" href="{{asset("css/styles.css")}}">

</head>

<body class="@yield("bodyclass", "landing-page")">

<!-- GITHUB BUTTON JS -->
<script async defer src="https://buttons.github.io/buttons.js"></script>

<!--FACEBOOK LIKE BUTTON JAVASCRIPT SDK-->
<div id="fb-root"></div>
<script async defer crossorigin="anonymous"
        src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v6.0&appId=994736563909741&autoLogAppEvents=1"></script>

<div class="page-wrapper">

    @yield("content")
</div><!--//page-wrapper-->

<footer class="footer text-center">
    <div class="container">

    </div><!--//container-->
</footer><!--//footer-->


<!-- Main Javascript -->
<script type="text/javascript" src="{{asset("plugins/jquery-3.4.1.min.js")}}"></script>
<script type="text/javascript" src="{{asset("plugins/bootstrap/js/bootstrap.min.js")}}"></script>
<script type="text/javascript" src="{{asset("plugins/stickyfill/dist/stickyfill.min.js")}}"></script>
@yield("script")
<script type="text/javascript" src="{{asset("js/main.js")}}"></script>

</body>
</html>

