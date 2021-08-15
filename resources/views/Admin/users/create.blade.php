@extends('layouts.admin_layout')

@section('content')
    <div class="main-content">
        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    @if(isset($user))
                                        {{ __('user.edit') }}
                                    @else
                                        {{ __('user.add') }}
                                    @endif
                                </h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form role="form" enctype="multipart/form-data"
                                  action="@if(isset($user)){{route('users.update',$user->id) }} @else {{route('users.store') }} @endif"
                                  method="POST">
                                @csrf
                                @if(isset($user))
                                    @method('PUT')
                                @endif


                                <div class="card-body">
                                    <!-- Name -->
                                    <div class="form-group">
                                        <label for="name">{{ __("user_name")  }}</label>
                                        <input class="form-control" type="text"
                                               value="{{ old( "name", isset( $user ) ? $user->name : "" ) }}"
                                               name="name" id="name">
                                    </div>

                                    <!-- Email -->
                                    <div class="form-group">
                                        <label for="email">{{ __("user_email")  }}</label>
                                        <input class="form-control" type="text"
                                               value="{{ old( "email", isset( $user ) ? $user->email : "" ) }}"
                                               name="email" id="email">
                                    </div>

                                    <!-- State -->
                                    <div class="form-group">
                                        <label for="state">{{ __("user_state")  }}</label>
                                        <select class="form-control" name="state" id="state">
                                            @foreach($states as $state)
                                                <option value="{{ $state->id }}"
                                                        @if(isset($user) && $user->stateId == $state->id )
                                                        selected
                                                        @endif
                                                >{{ $state->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    @if( !isset($user) )
                                    <!-- Password -->
                                    <div class="form-group">
                                        <label for="password">{{ __("user_password")  }}</label>
                                        <input class="form-control" type="password" name="password" id="password">
                                    </div>
                                    <div class="form-group">
                                        <label for="password2">{{ __("user_password")  }}</label>
                                        <input class="form-control" type="password" name="password2" id="password2">
                                    </div>
                                    @endif

                                    <!-- Birthdate-->
                                    <div class="form-group">
                                        <label for="birthdate">{{ __("user_birthdate")  }}</label>
                                        <input class="form-control" type="date"
                                               value="{{ old( "birthDate", isset( $user ) ? $user->birthDate : "" ) }}"
                                               name="birthdate" id="birthdate">
                                    </div>

                                    <!-- Phone -->
                                    <div class="form-group">
                                        <label for="phone">{{ __("user_phone")  }}</label>
                                        <input class="form-control" type="text"
                                               value="{{ old( "phone", isset( $user ) ? $user->phone : "" ) }}"
                                               name="phone" id="phone">
                                    </div>

                                    <!-- Job Title -->
                                    <div class="form-group">
                                        <label for="jobTitle">{{ __("user_jobTitle")  }}</label>
                                        <input class="form-control" type="text"
                                               value="{{ old( "jobTitle", isset( $user ) ? $user->jobTitle : "" ) }}"
                                               name="jobTitle" id="jobTitle">
                                    </div>

                                    <!-- Country -->
                                    <div class="form-group">
                                        <label for="country">{{ __("user_country")  }}</label>
                                        <input class="form-control" type="text"
                                               value="{{ old( "country", isset( $user ) ? $user->country : "" ) }}"
                                               name="country" id="country">
                                    </div>

                                    <!-- City -->
                                    <div class="form-group">
                                        <label for="city">{{ __("user_city")  }}</label>
                                        <input class="form-control" type="text"
                                               value="{{ old( "city", isset( $user ) ? $user->city : "" ) }}"
                                               name="city" id="city">
                                    </div>

                                    <!-- Gender -->
                                    <div class="form-group">
                                        <label for="gender">{{ __("user_gender")  }}</label>
                                        <input class="form-control" type="text"
                                               value="{{ old( "gender", isset( $user ) ? $user->gender : "" ) }}"
                                               name="gender" id="gender">
                                    </div>

                                    <!-- Image -->
                                    <div class="form-group">
                                        <label for="image">{{ __("user_image")  }}</label>
                                        <input type="file" class="form-control" name="image" id="image">
                                    </div>
                                </div>

                                <div class="card-footer" style="background-color: white">
                                    <input class="btn btn-purple" type="submit"
                                           @if(isset($user)) value="{{ __("edit") }}"
                                           @else value="{{ __('add') }}" @endif>
                                </div>
                            </form>
                        </div>
                    </div> <!-- end col -->
                </div> <!-- end row -->
            </div> <!-- container-fluid -->
        </div>
    </div>

    @if( isset( $user ) )
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">{{ __("update_pass") }}</h3>
                                </div>
                                <!-- /.card-header -->
                                <!-- form start -->
                                <form role="form" enctype="multipart/form-data"
                                      method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="card-body">
                                        <!-- Password -->
                                        <div class="form-group">
                                            <label for="password">{{ __("user_password")  }}</label>
                                            <input class="form-control" type="password" name="password" id="password">
                                        </div>
                                        <div class="form-group">
                                                <label for="password2">{{ __("user_password")  }}</label>
                                                <input class="form-control" type="password" name="password2" id="password2">
                                            </div>
                                    </div>

                                    <div class="card-footer" style="background-color: white">
                                        <input class="btn btn-purple" type="submit" value="{{ __("edit") }}">
                                    </div>
                                </form>
                            </div>
                        </div> <!-- end col -->
                    </div> <!-- end row -->
                </div> <!-- container-fluid -->
            </div>
        </div>
    @endif

@endsection
