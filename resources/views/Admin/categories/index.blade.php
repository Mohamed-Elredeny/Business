@extends('layouts.admin_layout')

@section('content')


    <div class="main-content">

        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12" style="margin-bottom: 20px;">
                        <button style="float: right" type="button" class="btn btn-primary waves-effect waves-light" onclick="window.location.href='{{route('categories.create')}}'">add category</button>
                    </div> <!-- end col -->
                </div> <!-- end row -->
                <div class="col-12">
                    @if(session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('status') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Categories</h3>
                            </div>
                            <div class="card-body">
                                <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">

                                    @if(count($categories) > 0)
                                        <thead>
                                            <tr>
                                                @if(App::getlocale() == 'ar')
                                                    <th>name_ar</th>
                                                @else
                                                    <th>name_en</th>
                                                @endif
                                                <th>Image</th>
                                                <th>controls</th>
                                            </tr>
                                        </thead>

                                        @foreach($categories as $category)
                                            <tbody>
                                                <tr>
                                                    @if(App::getlocale() == 'ar')
                                                        <td>{{$category->name_ar}}</td>
                                                    @else
                                                        <td>{{$category->name_en}}</td>
                                                    @endif

                                                    <td><center><img src="{{ asset('category_images/'.$category->image) }}" style="width: 20%;height:200px"></center></td>

                                                    <td>
                                                        <div class="dropdown">
                                                            <button type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="drop-down-button">
                                                                <i class="fas fa-ellipsis-v"></i>
                                                            </button>
                                                            <div class="dropdown-menu" aria-labelledby="dropdownMenu2">
                                                                <form action="{{ route('categories.destroy', $category->id) }}" method="post">
                                                                    @csrf
                                                                    @method('delete')

                                                                    <button type="button" class="dropdown-item" onclick="confirm('{{ __("Are you sure you want to delete this vendor?") }}') ? this.parentElement.submit() : ''">{{ __('delete') }}</button>

                                                                </form>
                                                                <a class="dropdown-item" href="{{ route('categories.edit', $category->id) }}">{{ __('edit') }}</a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                </tbody>
                                        @endforeach

                                    @else

                                        <tbody>
                                            <tr>
                                                <td colspan="3">
                                                    <center>
                                                        <h3>There is no categories yet!</h3>
                                                        <a class="btn btn-danger" href="{{ route('categories.create')}}">{{ __('add') }}</a>
                                                    </center>
                                                </td>
                                            </tr>
                                        </tbody>

                                    @endif

                                </table>

                            </div>
                        </div>
                    </div> <!-- end col -->
                </div> <!-- end row -->



            </div> <!-- container-fluid -->
        </div>

@endsection
