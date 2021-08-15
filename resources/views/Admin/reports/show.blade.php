@extends('layouts.admin_layout')

@section('content')


    <div class="main-content">

        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Reported Model Info</h4>
                                <p class="card-title-desc"> publisher : {{$model->publisher->name}}</p>
                                <textarea id="elm1" name="area">
                                    {{$model->body}}
                                </textarea>
                                @if($model->publisher->stateId != "banned")
                                    <form action="{{ route('reports.ban', $report) }}" method="post">
                                        @csrf
                                        <input type="hidden" name="ban" value="banned">
                                        <button type="button" class="btn btn-purple" onclick="confirm('{{ __("Are you sure you want to ban that user?") }}') ? this.parentElement.submit() : ''" style="margin-top: 20px" class="btn btn-purple waves-effect waves-light">Ban publisher</button>
                                    </form>
                                @else
                                    <form action="{{ route('reports.ban', $report)}}" method="post">
                                        @csrf
                                        <input type="hidden" name="ban" value="allowed">
                                        <button type="button" class="btn btn-purple" onclick="confirm('{{ __("Are you sure you want to remove the ban?") }}') ? this.parentElement.submit() : ''" style="margin-top: 20px" class="btn btn-purple waves-effect waves-light">Remove Ban</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div> <!-- end col -->
                </div> <!-- end row -->

                @if($model->media != null)
                    <div class="row">

                        @foreach($model->media as $media)
                            <div class="col-lg-3 col-md-6">
                                @if($media->mediaType == "image")
                                    <img src="{{asset('media')}}/{{$media->filename}}" alt="img" class="gallery-thumb-img" style="height: 300px; width: 200px">
                                @else
                                    <video class="p-1" controls class="gallery-thumb-img" style="height: 300px; width: 200px">
                                        <source src="{{asset('media')}}/{{$media->filename}}" type="video/mp4">
                                        Your browser does not support HTML video.
                                    </video>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

            </div> <!-- container-fluid -->
        </div>

@endsection
