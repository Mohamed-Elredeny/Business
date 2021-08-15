<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="stylesheet" href="{{ asset('css') }}/bootstrap.min.css">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">

    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/css/bootstrap-select.min.css" rel="stylesheet" />
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css"
    />

    <!-- Styles -->
    <link href="{{ asset('css/styles/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/styles/style-mar.css') }}" rel="stylesheet">
    <link href="{{ asset('css/styles/styleEn.css') }}" rel="stylesheet">

    @if(App::getLocale() == 'ar')
        <link href="{{ asset('css/styles/style.css') }}" rel="stylesheet">
        <link href="{{ asset('css/styles/style-mar.css') }}" rel="stylesheet">
    @endif
</head>
<body style="background-color: white;">
    <div id="app">
        <nav class="navbar navbar-expand-lg navbar-dark ez-navbar">
            <div class="container-fluid">
                <a class="navbar-brand p-2" href="{{route('welcome')}}">Business</a>
                <div class="right-side d-flex flex-row">
                    <a class="d-none d-lg-block mr-4" href="#"><i class="fas fa-headset p"></i> Support</a>
                    <a class="dropdown-toggle d-none d-lg-block" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-language"></i> Language
                    </a>
                    <ul class="dropdown-menu rounded-5" aria-labelledby="navbarDropdown">
                        @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                            <li>
                                <a href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}"> {{ $properties['native'] }}
                                    <span class="sr-only">(current)</span>
                                </a>
                            </li>
                        @endforeach
                        @auth
                            <li>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                                <a onclick="event.preventDefault();
                                  document.getElementById('logout-form').submit();" href="{{ route('logout') }}"><span>logout</span></a>
                            </li>
                        @endauth
                    </ul>
                    <button class="navbar-toggler" type="button" onclick="openSidenav()">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
            </div>
        </nav>
        <div class="w3-sidebar w3-bar-block" id="sideNavbar">
            <div class="d-flex flex-row-reverse justify-content-between">
                <div class="w-25 d-flex flex-row-reverse">
                    <button class="close-sidebar btn btn-danger p-3 m-3" onclick="closeSidenav()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <ul class="w-75 navbar-nav me-auto mb-2 mb-lg-0 p-3">
                    <li class="nav-item">
                        <a class="nav-link active" href="#"><i class="fas fa-headset"></i> Support</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                           aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-language"></i> Language
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                                <li>
                                    <a href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}"> {{ $properties['native'] }}
                                        <span class="sr-only">(current)</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        <main>
            @yield('content')
        </main>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/index.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js"
            integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s"
            crossorigin="anonymous"></script>
    <script src="{{ asset('assets') }}/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets') }}/libs/jquery/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/js/bootstrap-select.min.js"></script>
    <!-- BOOTSTRAP WITH POPPER -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"
            integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN"
            crossorigin="anonymous"></script>
    <!-- FONT AWESOME -->
    <script src="https://kit.fontawesome.com/5d2df7d4f7.js"></script>
    <script>
        $(".toggle-password").click(function() {
            var input = $($(this).attr("toggle"));
            if (input.attr("type") == "password") {
                $(this).toggleClass("fa-eye-slash fa-eye");
                input.attr("type", "text");
            } else {
                $(this).toggleClass("fa-eye fa-eye-slash ");
                input.attr("type", "password");
            }
        });
    </script>

    <script>
        $(function() {
            $('.selectpicker').selectpicker();
        });
    </script>

    <script>
        const phoneInputField = document.querySelector("#phone");
        const phoneInput = window.intlTelInput(phoneInputField, {
            utilsScript:
                "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
        });
    </script>
    @auth
        <script>
            window.user = @json(
            [
                'user'=> auth()->user()->load('notifications'),
            ]
            );
        </script>
    @endauth
</body>
</html>
