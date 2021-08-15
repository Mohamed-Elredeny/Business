@extends('layouts.auth')

@section('content')
    <div class="container-fluid row" style="margin-top: 45px">

        <!--left-->
        <div class="margin col-lg-4 col-md-4 col-sm-12 login-data">
            <div class="img">
                <img src="{{asset('assets')}}/images/login.jpg" alt="Add image" width="100%" height="100%">
            </div>

            <div class="login-title row" style="margin-bottom: 25px">
                <a class="active col-lg-5 col-sm-6" href="{{route('login')}}">{{__('user.login')}}</a>
                <a class="col-lg-5 col-sm-6" href="{{route('register')}}">{{__('user.new_user')}}</a>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required="please your email" placeholder="{{__('user.email')}}" autocomplete="email">

                    @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <input id="password-field" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="{{__('user.password')}}" required="enter your password" autocomplete="new-password">
                    <span toggle="#password-field" class="fa fa-fw fa-eye @if(App::getLocale() == 'ar') field-icon @else field-icon-en @endif toggle-password" ></span>
                    @error('password')
                    <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                    @enderror
                </div>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" style="color: #fcbc22" class="form-link d-inline-block float-right">
                        {{__('user.forgot_password')}}
                    </a>
                @endif

                <button type="submit" style="margin-top: 25px" class="btn" name="submit" value="login">{{__('user.login')}}</button>
            </form>
        </div>
        <!--right-->
        <div class="offset-md-2 col-5 image-data">
            <div class="image">
                <img src="http://localhost/crosswords/public/images/about-us-img.svg" width="100%" height="100%" />
            </div>
            <br>
            <div class="text">
                <h3 >{{__('user.communicate_with_friends')}}</h3>
            </div>
        </div>
    </div>
@endsection
