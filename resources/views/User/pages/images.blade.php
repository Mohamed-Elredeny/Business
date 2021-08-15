@extends('User.groups.layout')

@section('sectionGroups')
@if($group['privacy'] == 0)
<div class="group-about my-3">
    <div class="group-description">
        <h3 class="heading-tertiary">{{__('pages.privacy')}}</h3>
    </div>
</div> 
@else
<div class="group-images my-3">
    <div class="row">
        @foreach($images as $image)
        <div class="col-md-4 my-2">
            <div class="group-img-container">
                <img src="{{asset('assets/images/posts')}}/{{$image}}" alt="" class="group-img img-fluid pointer" data-toggle="modal" data-target="#images{{$image}}">
            </div>
        </div>
        @endforeach
    </div>
    
    <!-- Modal Posts-->
    @foreach($images as $imagemodel)
    <div class="modal fade bd-example-modal-lg" id="images{{$imagemodel}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="background: rgba(0,0,0,0);border: none">
          <div class="modal-body" >
              <div class="group-img-container text-center post-modal">
                <img  src="{{asset('assets/images/posts')}}/{{$imagemodel}}" alt="" class="group-img img-fluid " ><br>
                <div class="modal-post" >
                  <a href="group-single-post.html" style="color: #FFF">البوست</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    @endforeach
  </div>
@endif
  
@endsection