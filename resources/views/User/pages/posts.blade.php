@extends('User.groups.layout')

@section('sectionGroups')
@if($group['privacy'] == 0)
<div class="group-about my-3">
    <div class="group-description">
        <h3 class="heading-tertiary">هذه المجموعة خاصة</h3>
    </div>
</div> 
@else
<div class="group-about my-3">
    <div class="group-description">
        <h3 class="heading-tertiary">وصف المجموعة</h3>
        <p class="paragraph">
            {{$group->description}}
        </p>
    </div>

    <div class="group-rules">
        <h3 class="heading-tertiary">قوانين المجموعة</h3>
        <p class="paragraph">
            {{$group->rules}}
        </p>
    </div>
</div> 
@endif
  
@endsection