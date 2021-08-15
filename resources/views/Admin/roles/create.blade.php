@extends('layouts.admin_layout')

@section('content')


    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>edit role</h1>
                        @include('includes.errors')
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('roles.index')}}">Roles</a></li>
                            <li class="breadcrumb-item active">General Form</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <!-- left column -->
                    <div class="col-md-12">
                        <!-- general form elements -->
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">

                                    @if(isset($role))
                                        edit role
                                    @else
                                        create role

                                    @endif
                                </h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form role="form" action="@if(isset($role)){{route('roles.update',$role->id) }} @else {{route('roles.store') }} @endif" method="POST" enctype="multipart/form-data">
                                @csrf

                                @if(isset($role))

                                    @method('PUT')

                                @endif

                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">{{__('admin.product_arabname')}}</label>
                                        <input type="text" value="@if(isset($role)){{$role->arab_name }} @endif" name="arab_name" class=" @error('arab_name') is-invalid @enderror form-control" required>
                                        @error('arab_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">{{__('admin.product_engname')}}</label>
                                        <input type="text" name="eng_name" value="@if(isset($role)){{$role->eng_name }} @endif" class=" @error('eng_name') is-invalid @enderror form-control" required>
                                        @error('eng_name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="exampleInputEmail1">{{__('admin.role.permissions')}}</label>

                                        @if(isset($role))


                                    
                                         

                                            <div class=" row form-group mb-30">
                                    @foreach($permissions as $permission)
                                                  
                                            
                                                @if(  explode("-", $permission->name)[1] == 'list' )

                                                  

                                                     <label class="col-md-12 control-label mb-10 text-left"> {{ explode("-", $permission->name)[0] }}
                                                     </label>
           
                                                  
                                                @endif
                                            
                                      


                                            <div class="col-md-3">
                                                <div class="form-check" style="margin-left: 20px">
                                                    <input value="{{$permission->name}}" class="form-check-input" name="permission[]" type="checkbox" <?php if(in_array($permission->id, $rolePermissions)) echo 'checked' ?>>
                                                    <label class="form-check-label">

                                                        @if(App()->getLocale() == 'ar')
                                                            {{$permission->arab_name}}
                                                        @else
                                                            {{$permission->eng_name}}
                                                        @endif
                                                    </label>
                                                </div>
                                            </div>
                                                 
                                      @endforeach
                                        </div>
                                               


                                        @else


                                            <div class=" row form-group mb-30">
                                    @foreach($permissions as $permission)
                                                  
                                            
                                                @if(  explode("-", $permission->name)[1] == 'list' )

                                                  

                                                     <label class="col-md-12 control-label mb-10 text-left"> {{ explode("-", $permission->name)[0] }}
                                                     </label>
           
                                                  
                                                @endif
                                            
                                      


                                            <div class="col-md-3">
                                                <div class="form-check" style="margin-left: 20px">
                                                    <input value="{{$permission->name}}" class="form-check-input" name="permission[]" type="checkbox" >
                                                    <label class="form-check-label">

                                                        @if(App()->getLocale() == 'ar')
                                                            {{$permission->arab_name}}
                                                        @else
                                                            {{$permission->eng_name}}
                                                        @endif
                                                    </label>
                                                </div>
                                            </div>
                                                 
                                      @endforeach
                                        </div>
                                               

                                          {{--   @foreach($permissions as $permission)
                                                <div class="form-check" style="margin-bottom: 10px">
                                                    <label class="form-check-label">

                                                        @if(App::getLocale() == 'ar')
                                                            {{$permission->group_name_ar}}
                                                        @else
                                                            {{$permission->group_name_en}}
                                                        @endif
                                                    </label>
                                                    <div class="row">
                                                        @foreach(\App\Models\Permission::where('group_name_en',$permission->group_name_en)->get() as $permission)
                                                            <div class="form-check" style="margin-left: 20px">
                                                                <input value="{{$permission->name}}" class="form-check-input" name="permission[]" type="checkbox">
                                                                <label class="form-check-label">

                                                                    @if(App::getLocale() == 'ar')
                                                                        {{$permission->arab_name}}
                                                                    @else
                                                                        {{$permission->eng_name}}
                                                                    @endif
                                                                </label>
                                                            </div>

                                                        @endforeach
                                                    </div>
                                                </div>

                                            @endforeach --}}

                                        @endif

                                    </div>

                                </div>
                                <!-- /.card-body -->

                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
            </div>
        </section>


    @endsection


