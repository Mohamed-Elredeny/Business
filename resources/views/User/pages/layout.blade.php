@extends('layouts.site')

@section('content')
    <header class="header">
        <img src="{{asset('assets/images/pages/cover')}}/{{$page->cover_image}}" alt="group-header" class="d-block w-100 h-100 pointer" data-toggle="modal" data-target="#cover">
    </header>
    <div class="row">
        <div class="col-lg-9">
            <section class="page-section px-3">
                @include('User.pages.navigation')
                {{-- Content--}}
                @yield('sectionPages')
                <!--        End Of Content-->
            </section>
        </div>
        @include('User.pages.related')
    </div>
    <!-- Modal Cover-->
<div class="modal fade bd-example-modal-lg" id="cover" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabelCover" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content" style="background: rgba(0,0,0,0);border: none">
        <div class="modal-body" >
          <div class="owl-carousel owl-theme">
            <div class="group-img-container text-center post-modal">
              <img src="{{asset('assets/images/pages/cover')}}/{{$page->cover_image}}" alt="group-header" class="d-block w-100 h-100">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

