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

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required="please your enter name" placeholder="{{__('user.name')}}" autocomplete="name">

                    @error('name')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required="please your email" placeholder="{{__('user.email')}}">

                    @error('email')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                    <input id="password-field" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="{{__('user.password')}}" required="enter your password">
                    <span toggle="#password-field" class="fa fa-fw fa-eye @if(App::getLocale() == 'ar') field-icon @else field-icon-en @endif toggle-password" ></span>
                    @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>


                <div class="form-group{{ $errors->has('password_confirmation') ? ' has-danger' : '' }}">
                    <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" id="password-confirmation" placeholder="{{__('user.confirm_password')}}" required="enter your password" >
                    <span toggle="#password-confirmation" class="fa fa-fw fa-eye @if(App::getLocale() == 'ar') field-icon @else field-icon-en @endif toggle-password" ></span>
                    @error('password_confirmation')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group{{ $errors->has('bith_date') ? ' has-danger' : '' }}">
                    <input type="date" name="birthDate" id="input-date" class="form-control form-control-alternative{{ $errors->has('birthDate') ? ' is-invalid' : '' }}" placeholder="{{__('user.birthdate')}}" value="{{ old('birth_date') }}" required>

                    @if ($errors->has('birth_date'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('birth_date') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="row">
                    <div class="col-6 form-group{{ $errors->has('phone') ? ' has-danger' : '' }}">
                        <input type="tel" name="phone" id="phone" class="form-control form-control-alternative{{ $errors->has('phone') ? ' is-invalid' : '' }}" placeholder="{{__('user.phone')}}" value="{{ old('phone') }}" required>

                        @error('phone')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('phone') }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-6 form-group{{ $errors->has('gender') ? ' has-danger' : '' }}">
                        <select style="height: 55px" class="form-control @error('gender') is-invalid @enderror" name="gender" data-placeholder="{{__('user.gender')}}" required>
                            <option value="male">male</option>
                            <option value="female">female</option>
                        </select>
                    </div>
                </div>

                <div class="form-group{{ $errors->has('city') ? ' has-danger' : '' }}">
                    <input id="city" type="text" class="form-control @error('city') is-invalid @enderror" name="city" value="{{ old('city') }}" required="please your enter city" placeholder="{{__('user.city')}}" >

                    @error('city')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group{{ $errors->has('country') ? ' has-danger' : '' }}">
                    <input id="country" type="text" class="form-control @error('country') is-invalid @enderror" name="country" value="{{ old('country') }}" required="please your enter country" placeholder="{{__('user.country')}}" >

                    @error('country')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <div class="form-group{{ $errors->has('job_title') ? ' has-danger' : '' }}">
                    <input id="job_title" type="text" class="form-control @error('job_title') is-invalid @enderror" name="jobTitle" value="{{ old('job_title') }}" required="please your enter job_title" placeholder="{{__('user.job_title')}}" >

                    @error('job_title')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>

                <a  href="#" style="color: #fcbc22;margin-bottom: 30px; float: left;"  data-toggle="modal" data-target="#exampleModal">{{__('user.policies')}}</a>


                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div  class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">{{__('user.terms')}}</h5>

                            </div>
                            <div  class="modal-body" style="text-align: left;">
                                cccccwccjkkcjklcjkcjcklqc
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">close</button>

                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" style="margin-top: -7px" class="btn" name="submit" value="register">{{__('user.register')}}</button>

            </form>
        </div>
        <!--right-->
        <div class="offset-md-2 col-5 image-data">
            <div class="image">
                <img src="http://localhost/crosswords/public/images/about-us-img.svg" width="100%" height="100%" />
            </div>
            <br>
            <div class="text">
                <h3 >{{__('user.make_new_friends')}}</h3>
            </div>
        </div>
    </div>
@endsection
