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
                                    @if(isset($company))
                                        {{ __('company.edit') }}
                                    @else
                                        {{ __('company.add') }}
                                    @endif
                                </h3>
                            </div>
                            <!-- /.card-header -->
                            <!-- form start -->
                            <form role="form" enctype="multipart/form-data"
                                  action="@if(isset($company)){{route('companies.update',$company->id) }} @else {{route('companies.store') }} @endif"
                                  method="POST">
                                @csrf
                                @if(isset($company))
                                    @method('PUT')
                                @endif

                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="name">{{ __("company_name")  }}</label>
                                        <input class="form-control" type="text"
                                               value="{{ old("name", isset($company) ? $company->name : "") }}"
                                               name="name" id="name">
                                        @error("name")
                                            {{ $message }}
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="details">{{ __("company_details")  }}</label>
                                        <textarea class="form-control" name="details" id="details">
                                            {{ old("details", isset($company) ? $company->details : "") }}
                                        </textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="state">{{ __("company_state")  }}</label>
                                        <select class="form-control" name="state" id="state">
                                            @foreach($states as $state)
                                                <option value="{{ $state->id }}"
                                                        @if(isset($company) && $company->stateId == $state->id )
                                                            selected
                                                        @endif
                                                >{{ $state->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="row">
                                        @for($i = 0; $i < 4; $i++)
                                            <div class="col-md-6 col-lg-3 col-12">
                                                <div class="form-group">
                                                    <label for="phone{{ $i + 1 }}">{{ __("company_phone" . ( $i + 1 ) )  }}</label>
                                                    <input class="form-control" type="text"
                                                           value="{{ old("phone" . ( $i + 1 ), isset($company) && isset($company->phone[$i]->phoneNumber) ? $company->phone[$i]->phoneNumber : "" ) }}"
                                                           name="phone{{ $i + 1 }}" id="phone{{ $i + 1 }}">
                                                    @error("phone1")
                                                        {{ $message }}
                                                    @enderror
                                                </div>
                                            </div>
                                        @endfor
                                    </div>

                                    <div class="form-group">
                                        <label for="image">{{ __("company_image")  }}</label>
                                        <input type="file" class="form-control" name="image" id="image">
                                    </div>
                                </div>

                                <div class="card-footer" style="background-color: white">
                                    <input class="btn btn-purple" type="submit"
                                           @if(isset($company)) value="{{ __("edit") }}"
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
