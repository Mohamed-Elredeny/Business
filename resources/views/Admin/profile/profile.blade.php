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
                                    @if(isset($admin))
                                        {{ __('admin.edit') }}
                                    @else
                                        {{ __('admin.add') }}
                                    @endif
                                </h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form role="form" enctype="multipart/form-data"
                                  action="@if(isset($admin)){{route('admins.update',$admin->id) }} @else {{route('admins.store') }} @endif"
                                  method="POST">
                                @csrf
                                @if(isset($admin))
                                    @method('PUT')
                                @endif

                                <div class="card-body">
                                    <!-- Name -->
                                    <div class="form-group">
                                        <label for="name">{{ __("admin_name")  }}</label>
                                        <input class="form-control" type="text"
                                               value="{{ old( "name", isset( $admin ) ?? $admin->name ) }}"
                                               id="name" name="name">
                                        @error("name")
                                            {{ $message }}
                                        @enderror
                                    </div>

                                    <!-- Email -->
                                    <div class="form-group">
                                        <label for="email">{{ __("admin_email")  }}</label>
                                        <input class="form-control" type="text"
                                               value="{{ old("email", isset( $admin ) ?? $admin->email ) }}"
                                               id="email" name="email">
                                        @error("email")
                                            {{ $message }}
                                        @enderror
                                    </div>

                                    <!-- State -->
                                    <div class="form-group">
                                        <label for="state">{{ __("admin_state")  }}</label>
                                        <select class="form-control" name="state" id="state">
                                            @foreach($states as $state)
                                                <option value="{{ $state->id }}"
                                                        @if(isset($admin) && $admin->stateId == $state->id )
                                                        selected
                                                        @endif
                                                >{{ $state->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Password -->
                                    <div class="form-group">
                                        <label for="password">{{ __("admin_password")  }}</label>
                                        <input class="form-control" type="password" name="password" id="password">
                                        @error("password")
                                            {{ $message }}
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="password2">{{ __("admin_password")  }}</label>
                                        <input class="form-control" type="password" name="password2" id="password2">
                                    </div>

                                    <!-- Phone -->
                                    <div class="form-group">
                                        <label for="phone">{{ __("admin_phone")  }}</label>
                                        <input class="form-control" type="text"
                                               value="{{ old( "name", isset( $admin ) ?? $admin->phone ) }}"
                                               name="phone" id="phone">
                                        @error( "phone" )
                                            {{ $message }}
                                        @enderror
                                    </div>
                                </div>

                                <!-- Submit -->
                                <div class="card-footer" style="background-color: white">
                                    <input class="btn btn-purple" type="submit"
                                           @if(isset($admin)) value="{{ __("edit") }}"
                                           @else value="{{ __('add') }}" @endif>
                                </div>
                            </form>
                        </div>
                    </div> <!-- end col -->
                </div> <!-- end row -->
            </div> <!-- container-fluid -->
        </div>
    </div>

@endsection
