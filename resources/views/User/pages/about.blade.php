@extends('User.pages.layout')

@section('sectionPages')
@if($page['privacy'] == 0 && $isAdmin == 0 && $myState == 0)
<div class="group-about my-3">
    <div class="group-description">
        <h3 class="heading-tertiary">{{__('pages.privacy')}}</h3>
    </div>
</div> 
@else 
<div class="group-about my-3">
    <div class="group-description">
        <h3 class="heading-tertiary">{{__('pages.describe')}}</h3>
        <p class="paragraph">
            {{$page->description}}
        </p>
    </div>

    <div class="group-rules">
        <h3 class="heading-tertiary">{{__('pages.rules')}}</h3>
        <p class="paragraph">
            {{$page->rules}}
        </p>
    </div>
</div> 
@endif
  
@endsection