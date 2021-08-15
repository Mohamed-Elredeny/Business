@extends('User.groups.layout')

@section('sectionGroups')
@if($group['privacy'] == 0 && $isAdmin == 0 && $myState == 0)
<div class="group-about my-3">
    <div class="group-description">
        <h3 class="heading-tertiary">{{__('groups.privacy')}}</h3>
    </div> 
</div> 
@else
<div class="group-about my-3">
    <div class="group-description">
        <h3 class="heading-tertiary">{{__('groups.describe')}}</h3>
        <p class="paragraph">
            {{$group->description}}
        </p>
    </div>

    <div class="group-rules">
        <h3 class="heading-tertiary">{{__('groups.rules')}}</h3>
        <p class="paragraph">
            {{$group->rules}}
        </p>
    </div>
</div> 
@endif
  
@endsection