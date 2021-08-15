@extends('layouts.admin_layout')


@section('title')

    Bad Words

@endsection

@section('content')

    <div class="main-content">

        <div class="page-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="card">

                            <div class="card-header">
                                <h3 class="card-title">

                                    @if(isset($badword))
                                        {{ __('badWord.edit') }}
                                    @else
                                        {{ __('badWord.add') }}

                                    @endif
                                </h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form role="form" action="@if(isset($badword)){{route('badwords.update',$badword->id) }} @else {{route('badwords.store') }} @endif" method="POST">
                                @csrf

                                @if(isset($badword))

                                    @method('PUT')

                                @endif
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Word</label>
                                        <input class="form-control @error('name') is-invalid @enderror" type="text" @if(isset($badword)) value="{{old('name',$badword->name)}}" @else value="{{ old('name') }}" @endif name="name" id="example-text-input">
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="card-footer" style="background-color: white">
                                    <input class="btn btn-purple" type="submit" @if(isset($badword)) value="edit" @else value="add" @endif>
                                </div>
                            </form>
                        </div>
                    </div> <!-- end col -->
                </div> <!-- end row -->



            </div> <!-- container-fluid -->
        </div>
    </div>

@endsection
