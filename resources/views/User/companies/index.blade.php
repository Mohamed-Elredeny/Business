@extends('layouts.app')

@section('content')
     <section id="ez-body__center-content" class="col-lg-8 mt-3">
            <div class="search-bar">
                <input type="text" placeholder="Search" />
            </div>
            @foreach($companies as $company)
                <div class="trade-div">
                    <div class="row">
                        <div class="col-md-3 col-12">
                            @if($company->image == null)
                                <img src="{{asset('media')}}/images.png">
                            @else
                                <img src="{{asset('media')}}/{{$company->image}}">
                            @endif
                        </div>
                        <div class="col-md-9 col-12 row">
                            <h4 class="col-12">
                                @if(App::getlocale() == 'en')
                                    {{$company->name_en}}
                                @else
                                    {{$company->name_ar}}
                                @endif
                            </h4>
                            @foreach($company->phones as $phone)
                                <p class="col-3">{{$phone->phoneNumber}}</p>
                                <span  class="col-3 ">
                                  <p class="call-icon"><i class="fas fa-mobile-alt"></i> Call</p>
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </section>
@endsection
