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

                                    @if(isset($posts))
                                        {{ __('edit_category') }}
                                    @else
                                        {{ __('add_category') }}

                                    @endif
                                </h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form role="form" action="@if(isset($post)){{route('posts.update',$post->id) }} @else {{route('posts.store') }} @endif" method="POST" enctype="multipart/form-data">
                                @csrf
                                @if(isset($post))
                                    @method('PUT')
                                @endif
                                <div class="card-body">
                                    <div class="form-group row">
                                        <div class="col-sm-10">
                                            <input class="btn btn-purple" type="submit" @if(isset($post)) value="edit" @else value="add" @endif>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="example-text-input" class="col-sm-2 col-form-label">Title</label>
                                        <div class="col-sm-10">
                                            <input class="form-control" type="text" @if(isset($post)) value="{{$post->title}}" @endif name="title" id="example-text-input">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="example-text-input" class="col-sm-2 col-form-label">body</label>
                                        <div class="col-sm-10">
                                            <input class="form-control" type="text" @if(isset($post)) value="{{$post->body}}" @endif name="body" id="example-text-input1">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="example-text-input" class="col-sm-2 col-form-label">Post Type</label>
                                        <div class="col-sm-10">
                                            <select name="postTypeId" id="" @if(isset($post)) value="{{$post->postTypeId }}" @endif class="form-control">
                                                @if(count($arrays['PostType']) > 0)
                                                    @foreach($arrays['PostType'] as $postType)
                                                        <option value="{{$postType['id']}}">{{$postType['name']}}</option>
                                                    @endforeach
                                                 @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="example-text-input" class="col-sm-2 col-form-label">Post Privacy</label>
                                        <div class="col-sm-10">
                                            <select name="privacyId" id="" @if(isset($post)) value="{{$post->privacyId}}" @endif class="form-control">
                                                @if(count($arrays['Privacy']) > 0)
                                                    @foreach($arrays['Privacy'] as $postPrivacy)
                                                        <option value="{{$postPrivacy['id']}}">{{$postPrivacy['name']}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="example-text-input" class="col-sm-2 col-form-label">Post State </label>
                                        <div class="col-sm-10">
                                            <select name="stateId" id="" @if(isset($post)) value="{{$post->stateId }}" @endif class="form-control">
                                                @if(count($arrays['State']) > 0)
                                                    @foreach($arrays['State'] as $postPrivacy)
                                                        <option value="{{$postPrivacy['id']}}">{{$postPrivacy['name']}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="example-text-input" class="col-sm-2 col-form-label">Publisher</label>
                                        <div class="col-sm-10">
                                            <select name="publisherId" id="" @if(isset($post)) value="{{$post->publisherId }}" @endif class="form-control">
                                                @if(count($arrays['User']) > 0)
                                                    @foreach($arrays['User'] as $postPrivacy)
                                                        <option value="{{$postPrivacy['id']}}">{{$postPrivacy['name']}}</option>
                                                    @endforeach
                                                @endif                                              </select>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="example-text-input" class="col-sm-2 col-form-label">Category</label>
                                        <div class="col-sm-10">
                                            <select name="categoryId" id="" @if(isset($post)) value="{{$post->categoryId }}" @endif class="form-control">
                                                @if(count($arrays['Category']) > 0)
                                                    @foreach($arrays['Category'] as $postPrivacy)
                                                        <option value="{{$postPrivacy['id']}}">{{$postPrivacy['name']}}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>

                                </div>

                                <div class="card-body">


                                    <div class="form-group row">
                                        <label for="example-text-input" class="col-sm-2 col-form-label">Images</label>
                                        <div class="col-sm-10">
                                            <input  type="file" @if(isset($post)) value="{{$post->image}}" @endif name="image[]" multiple id="example-text-input1">
                                        </div>
                                    </div>




                                </div>
                            </form>
                        </div>
                    </div> <!-- end col -->
                </div> <!-- end row -->

            </div> <!-- container-fluid -->
        </div>


@endsection
