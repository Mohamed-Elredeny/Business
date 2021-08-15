<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- BOOTSTRAP -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
          integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <!-- CSS -->

    {{-- <link rel="stylesheet" href="{{asset('assets/css/owl.carousel.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/owl.theme.default.css')}}"> --}}
    <link rel="stylesheet" href="{{asset('assets/css/style_mar.css')}}">

    @if (  LaravelLocalization::getCurrentLocaleName() == 'English')
    <!-- CSS ENGLISH -->
     <link rel="stylesheet" href="{{asset('assets/css/styleEn_mar.css')}}">
    @endif
    <title>Business</title>
</head>

<body>
    <!--Top Nav-->
    @include('includes.martina.site.topnav')
    <!--End of Top Nav-->

<main>
    <div class="container-fluid">
        <div class="row">
            <!--    Right Nav          -->
                @include('includes.martina.site.rightnav')
            <!--  End of Right Nav          -->

            <div class="col-lg-10 col-md-12">
                @yield('content')
            </div>
        </div>
    </div>
</main>

    <!-- JQUERY -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
    integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj"
    crossorigin="anonymous"></script>
    <!-- BOOTSTRAP WITH POPPER -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"
    integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN"
    crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js"
    integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s"
    crossorigin="anonymous"></script>
    <!-- FONT AWESOME -->
    <script src="https://kit.fontawesome.com/5d2df7d4f7.js"></script>
    <!--Owl js-->
    {{-- <script src="{{asset('assets/site/js/owl.carousel.min.js')}}"></script> --}}
    <!-- js file-->
    <script src="https://www.dukelearntoprogram.com/course1/common/js/image/SimpleImage.js"></script>

    <script src="{{asset('assets/js/script_mar.js')}}"></script>
    <script
  src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
  integrity="sha256-pasqAKBDmFT4eHoN2ndd6lN370kFiGUFyTiUHWhU7k8="
  crossorigin="anonymous"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

</body>
</html>
