@extends('layouts.admin_layout')

@section('content')


    <div class="main-content">

        <div class="page-content">
            <div class="container-fluid">

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">

                                {{-- <h4 class="card-title">Default Datatable</h4>
                                <p class="card-title-desc">DataTables has most features enabled by
                                    default, so all you need to do to use it with your own tables is to call
                                    the construction function: <code>$().DataTable();</code>.
                                </p> --}}
                                <table id="datatable" class="table table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                    
                                    @if(count($categories) != 0)
                                    <thead>
                                    <tr>
                                        <th>name</th>
                                        <th>Image</th>
                                        <th>controls</th>
                                    </tr>
                                    </thead>
                                    @endif
                                    @foreach($categories as $category)
                                        <tbody>
                                            <tr>
                                                <td>{{$category->name}}</td>
                                                <td><center><img src="{{ asset('assets/images/categories/'.$category->image) }}" style="width: 60%;height:200px"></center></td>
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
                                                                <a class="dropdown-item" href="  route('categories.create')">{{ __('add') }}</a>

                                               
                                                                <a class="dropdown-item" href="{{ route('categories.edit', $category->id) }}">{{ __('edit') }}</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                    @endforeach
                                    @if(count($categories) == 0)
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
