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

                                   View Post Details
                                </h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form role="form" action="" method="POST" enctype="multipart/form-data">

                                <div class="card-body">


                                    <div class="form-group row">
                                        <label for="example-text-input" class="col-sm-2 col-form-label">Title</label>
                                        <div class="col-sm-10">
                                            <h6 class="btn btn-dark" style="text-align: left;width: 100%">  {{$post[0]->title }}</h6>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="example-text-input" class="col-sm-2 col-form-label">Body</label>
                                        <div class="col-sm-10">
                                            <h6 class="btn btn-dark" style="text-align: left;width: 100%"> {{$post[0]->body }}</h6>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="example-text-input" class="col-sm-2 col-form-label">Type</label>
                                        <div class="col-sm-10">
                                            <h6 class="btn btn-dark" style="text-align: left;width: 100%">  {{$idsToNames[0]['postType']}}</h6>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="example-text-input" class="col-sm-2 col-form-label">Publisher</label>
                                        <div class="col-sm-10">
                                            <h6 class="btn btn-dark" style="text-align: left;width: 100%"> {{$idsToNames[0]['publisherId']}}</h6>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="example-text-input" class="col-sm-2 col-form-label">Privacy</label>
                                        <div class="col-sm-10">
                                            <h6 class="btn btn-dark" style="text-align: left;width: 100%"> {{$idsToNames[0]['privacyId']}} </h6>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="example-text-input" class="col-sm-2 col-form-label">State</label>
                                        <div class="col-sm-10">
                                            <h6 class="btn btn-dark" style="text-align: left;width: 100%">{{$idsToNames[0]['stateId']}}</h6>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="example-text-input" class="col-sm-2 col-form-label">Category</label>
                                        <div class="col-sm-10">
                                            <h6 class="btn btn-dark" style="text-align: left;width: 100%">{{$idsToNames[0]['categoryId']}}</h6>
                                        </div>
                                    </div>
                                    @if(count($media)>0)
                                    <div class="form-group row">
                                        <label for="example-text-input" class="col-sm-2 col-form-label">Images</label>
                                        <div class="col-sm-10">
                                            @foreach($media as $image)
                                                @if($image->mediaType == 'image')
                                                   <img class="d-inline-block w-25 mr-5 mb-5" src="{{asset($image->url)}}" alt="First slide" style="height: 200px">
                                                @endif
                                            @endforeach

                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="example-text-input" class="col-sm-2 col-form-label">Videos</label>
                                        <div class="col-sm-10">
                                            @foreach($media as $image)
                                                @if($image->mediaType == 'videos')
                                                    <div class="embed-responsive embed-responsive-16by9" style="width: 300px;height: 300px">
                                                        <iframe class="embed-responsive-item" src="{{ asset($image->url)}}"  ></iframe>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif



                                    <div class="form-group row">
                                        <label for="example-text-input" class="col-sm-2 col-form-label">Likes</label>
                                        <div class="col-sm-10">
                                            <table>
                                                @for($i=0;$i<4;$i++)
                                                <tr>
                                                    <td>
                                                        <h6 class="btn btn-dark" style="text-align: left;"> Love </h6>
                                                    </td>
                                                    <td>
                                                        <h6 class="btn btn-light" style="text-align: left;"> 0 </h6>
                                                    </td>
                                                    <td>
                                                        <h6 class="btn btn-danger" style="text-align: left;"> View </h6>
                                                    </td>
                                                </tr>
                                                @endfor
                                            </table>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="example-text-input" class="col-sm-2 col-form-label">Shares</label>
                                        <div class="col-sm-10">
                                            <table>
                                                    <tr>

                                                        <td>
                                                            <h6 class="btn btn-light" style="text-align: left;"> 0 </h6>
                                                        </td>
                                                        <td>
                                                            <h6 class="btn btn-danger" style="text-align: left;"> View </h6>
                                                        </td>
                                                    </tr>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="example-text-input" class="col-sm-2 col-form-label">Mention</label>
                                        <div class="col-sm-10">
                                            <table>
                                                <tr>

                                                    <td>
                                                        <h6 class="btn btn-light" style="text-align: left;"> 0 </h6>

                                                    </td>
                                                    <td>
                                                        <h6 class="btn btn-danger" style="text-align: left;"> View </h6>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>



                                </div>

                                <div class="card-body">





                                </div>
                            </form>
                        </div>
                    </div> <!-- end col -->
                </div> <!-- end row -->

            </div> <!-- container-fluid -->
        </div>


@endsection

{{--<h1>{{ $post[0]->id }}</h1>--}}
{{--<h1>{{ $post[0]->title }}</h1>--}}
{{--<h1>{{ $post[0]->body }}</h1>--}}
{{--<h1>{{ $idsToNames[0]['privacyId'] }}</h1>--}}
{{--<h1>{{ $idsToNames[0]['postType'] }}</h1>--}}
{{--<h1>{{ $idsToNames[0]['stateId'] }}</h1>--}}
{{--<h1>{{ $idsToNames[0]['publisherId'] }}</h1>--}}
{{--<h1>{{ $idsToNames[0]['categoryId'] }}</h1>--}}

{{--<br><br><br>--}}
{{--<h1>Reach</h1>--}}
{{--<h1>Comments</h1>--}}
{{--<h1>Shares</h1>--}}
{{--<h1>Likes</h1>--}}

{{--<div id="demo" class="carousel slide" data-ride="carousel">--}}
{{--    <!-- Indicators -->--}}
{{--    <ul class="carousel-indicators">--}}
{{--        <li data-target="#demo" data-slide-to="0" class="active"></li>--}}
{{--        <li data-target="#demo" data-slide-to="1"></li>--}}
{{--        <li data-target="#demo" data-slide-to="2"></li>--}}
{{--    </ul>--}}

{{--    <!-- The slideshow -->--}}
{{--    <div class="carousel-inner">--}}

{{--        <div class="carousel-item active">--}}
{{--            <img src="la.jpg" alt="Los Angeles" width="1100" height="500">--}}
{{--        </div>--}}
{{--        <div class="carousel-item">--}}
{{--            <img src="chicago.jpg" alt="Chicago" width="1100" height="500">--}}
{{--        </div>--}}
{{--        <div class="carousel-item">--}}
{{--            <img src="ny.jpg" alt="New York" width="1100" height="500">--}}
{{--        </div>--}}
{{--    </div>--}}

{{--    <!-- Left and right controls -->--}}
{{--    <a class="carousel-control-prev" href="#demo" data-slide="prev">--}}
{{--        <span class="carousel-control-prev-icon"></span>--}}
{{--    </a>--}}
{{--    <a class="carousel-control-next" href="#demo" data-slide="next">--}}
{{--        <span class="carousel-control-next-icon"></span>--}}
{{--    </a>--}}
{{--</div>--}}


