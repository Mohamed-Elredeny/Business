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

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Styles -->
    <link href="{{ asset('css/styles/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/styles/style-mar.css') }}" rel="stylesheet">
    <link href="{{ asset('css/styles/styleEn.css') }}" rel="stylesheet">

    @if(App::getLocale() == 'ar')
        <link href="{{ asset('css/styles/style.css') }}" rel="stylesheet">
        <link href="{{ asset('css/styles/style-mar.css') }}" rel="stylesheet">
        <link href="{{ asset('css/styles/style-rtl.css') }}" rel="stylesheet">
    @endif
    <style>
        .totyAdmin a{
            color:white;
            text-decoration: none
        }
        a:hover{
            color:black
        }
    </style>
</head>
<body>
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
                <li>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                    <a onclick="event.preventDefault();
                              document.getElementById('logout-form').submit();" href="{{ route('logout') }}"><span>logout</span></a>
                </li>
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
            <li class="mt-2">
                <a href="{{route('user.view.profile',['user_id'=>auth()->user()->id])}}"><img class="profile-figure"
                                                                                              src="{{auth()->user()->personal_image}}"
                                                                                              alt="My Profile Pic" />
                    <span>{{ \Str::limit(auth()->user()->name, 10) }}</span></a>
            </li>
            <li class="more-details mt-3 mb-3">
                <ul class="m-auto">
                    <li><b>Job:</b></li>
                    <li><b>Specialty:</b></li>
                    <li><b>Business Type:</b></li>
                    <li><b>Friends:</b></li>
                    <li>
                        <div class="container d-flex justify-content-around">
                            <div class="followers">
                                <b>Followers:</b> <span> 20</span>
                            </div>
                            <span class="spacer">|</span>
                            <div class="following">
                                <b>Following:</b> <span> 4</span>
                            </div>
                        </div>
                    </li>
                    <li class="mt-3 text-center last-li">
                        <i class="fas fa-headphones"></i>
                        <i class="fas fa-film"></i>
                        <i class="fas fa-camera-retro"></i>
                        <i class="fas fa-dumbbell"></i>
                        <i class="far fa-lightbulb"></i>
                        <i class="fas fa-gamepad"></i>
                    </li>
                </ul>
            </li>

            <li class="nav-item">
                <a class="nav-link active" aria-current="page" href="{{route('home')}}}"><i class="fas fa-home"></i> Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="{{route('service_categories')}}"><i class="fas fa-hand-holding-usd"></i> Services</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="#"><i class="fas fa-users"></i> Groups</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="{{route('companies.index')}}"><i class="fas fa-truck"></i> Delivery</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="#"><i class="far fa-copy"></i> Pages</a>
            </li>
            <li class="nav-item" style="display: flex; align-items: center">
                <a class="nav-link active" href="#"><i class="fas fa-bell"></i> Notifications</a>
                <span class="badge bg-warning text-dark">2</span>
            </li>
            <li class="nav-item" style="display: flex; align-items: center">
                <a class="nav-link active" href="#"><i class="fas fa-comment-dots"></i> Messages</a>
                <span class="badge bg-warning text-dark">3</span>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="{{route('savedposts')}}}"><i class="fas fa-bookmark"></i> Saved Posts</a>
            </li>
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
            @if(auth()->user()->type == 1)
                <li class="nav-item">
                    <a class="nav-link active" href="{{url('admin/dashboard')}}"><i class="fas fa-users-cog"></i>  Dashboard</a>
                </li>
            @endif
        </ul>
    </div>
</div>
<section class="container-fluid ez-main">
    <section class="ez-body row">
        <section id="ez-body__left-sidebar" class="col-lg-2 ez-sidebar">
            <ul id="left-sidebar__items">
                <li class="mt-2">
                    <a href="{{route('user.view.profile',['user_id'=>auth()->user()->id])}}"><img class="profile-figure"
                                                src="{{asset('assets/images/users/'.auth()->user()->personal_image)}}"
                                                alt="My Profile Pic" />
                        <span>{{ \Str::limit(auth()->user()->name, 10) }}</span></a>
                </li>
                <li class="more-details mt-3 mb-3">
                    <ul class="m-auto">
                        <li><b>Job:&#160; {{auth::user()->jobTitle}}</b></li>
                        <li><b>Specialty:&#160; {{auth::user()->Specialty}}</b></li>
                        <li><b>Business Type:&#160; {{auth::user()->businessType}}</b></li>
                        <li>
                            <a style="color:#212529;cursor: pointer" data-toggle="modal" data-target="#exampleModalCenter">
                                <b>Friends: {{count($friends)}} Friends</b>
                            </a>
                        </li>
                        <li>
                            <div class="container d-flex justify-content-around">
                                <div class="followers">
                                    <a style="color:#212529;cursor: pointer" data-toggle="modal" data-target="#followers">
                                    <b>Followers:</b> <span> {{count($Myfollowers)}}</span>
                                    </a>
                                </div>
                                <span class="spacer">|</span>
                                <div class="following">
                                    <a style="color:#212529;cursor: pointer" data-toggle="modal" data-target="#followings">
                                    <b>Following:</b> <span> {{count($Myfollowings)}}</span>
                                    </a>
                                </div>
                            </div>
                        </li>
                        <li class="mt-3 text-center last-li col-sm-12" >
                            <i>
                                <a href="{{route('redeny.view.component',['user_id'=>$user_id,'component'=>'musics'])}}">
                                    <img src="{{asset('assets/images/icons/headphones.png')}}" width="18" height="20" style="cursor:pointer">
                                </a>
                            </i>

                            <i>
                                <a href="{{route('redeny.view.component',['user_id'=>$user_id,'component'=>'videos'])}}">
                                <img src="{{asset('assets/images/icons/video-player.png')}}" width="18" height="20" style="cursor:pointer" >
                                </a>
                            </i>
                            <i>
                                <a href="{{route('redeny.view.component',['user_id'=>$user_id,'component'=>'images'])}}">
                                <img src="{{asset('assets/images/icons/photo.png')}}" width="18" height="20" style="cursor:pointer">
                                </a>
                            </i>
                            <i>
                                <a href="{{route('redeny.view.component',['user_id'=>$user_id,'component'=>'sports'])}}">
                                    <img src="{{asset('assets/images/icons/dumbbell.png')}}" width="18" height="20" style="cursor:pointer">
                                </a>
                            </i>
                            <i>
                                <a href="{{route('redeny.view.component',['user_id'=>$user_id,'component'=>'inspirations'])}}">
                                    <img src="{{asset('assets/images/icons/mentor.png')}}" width="18" height="20" style="cursor:pointer">
                                </a>
                            </i>
                            <i>
                                <a href="{{route('redeny.view.component',['user_id'=>$user_id,'component'=>'hoppies'])}}">
                                    <img src="{{asset('assets/images/icons/joystick.png')}}" width="18" height="20" style="cursor:pointer">
                                </a>
                            </i>

                        </li>

                    </ul>
                </li>

                <li>
                    <a aria-current="page" href="{{route('home')}}"><i class="fas fa-home p"></i> Home</a>
                </li>
                <li>
                    <a href="{{route('service_categories')}}"><i class="fas fa-hand-holding-usd p"></i> Services</a>
                </li>
                <li>
                    <a href="#"><i class="fas fa-users p"></i> Groups</a>
                </li>
                <li>
                    <a href="{{route('companies.index')}}"><i class="fas fa-truck p"></i> Delivery</a>
                </li>
                <li>
                    <a href="#"><i class="far fa-copy p"></i> Pages</a>
                </li>
                <li style="
                display: flex;
                justify-content: space-between;
                align-items: center;
              ">
                    <a href="#"><i class="fas fa-bell p"></i> Notifications</a><span class="badge bg-warning text-dark">2</span>
                </li>
                <li style="
                display: flex;
                justify-content: space-between;
                align-items: center;
              ">
                    <a href="#"><i class="fas fa-comment-dots p"></i> Messages</a>
                    <span class="badge bg-warning text-dark">3</span>
                </li>
                <li>
                    <a href="#"><i class="fas fa-bookmark p"></i> Saved Posts</a>
                </li>
                @if(auth()->user()->type == 1)
                    <li class="nav-item">
                        <a href="{{url('admin/dashboard')}}"><i class="fas fa-users-cog"></i> Dashboard</a>
                    </li>
                @endif
            </ul>
        </section>

        @yield('content')

    </section>
</section>
<!--


<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('js/index.js') }}"></script>
<script src="{{ asset('js/vue.js') }}"></script>
-->
<script src="{{ asset('assets') }}/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('assets') }}/libs/jquery/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script src="{{ asset('js/script.js') }}"></script>
<!-- FONT AWESOME -->
<script src="https://kit.fontawesome.com/5d2df7d4f7.js"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

</body>
</html>

