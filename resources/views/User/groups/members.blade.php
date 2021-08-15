@extends('User.groups.layout')

@section('sectionGroups')
@if($group['privacy'] == 0 && $isAdmin == 0 && $myState == 0)
<div class="group-about my-3">
    <div class="group-description">
        <h3 class="heading-tertiary">{{__('groups.privacy')}}</h3>
    </div>
</div> 
@else 
<div class="group-members my-3">
    <div class="invite-friends d-flex justify-content-between">
      <h3 class="heading-tertiary">{{__('groups.invite_friends')}}</h3>
      @if(Auth::guard('web')->user())
      <button class="button-4"  data-toggle="modal" data-target="#friends">{{__('groups.invite')}}</button>
      @else
      <a class="button-4" href="/login">{{__('groups.invite')}}</a>
      @endif
    </div>
    <h3 class="heading-tertiary my-5">{{__('groups.admins')}}:{{count($admins)}}</h3>
    <ul class="members-list list-unstyled" id="adddmin">
      @foreach($admins as $admin)
        <li class="members-item">
          <div class="group-member d-flex justify-content-between">
            <a href="#" class="group-member-link d-flex align-items-center">
              <img src="{{asset('assets/images/users')}}/{{$admin->member->personal_image}}" alt="#" class="member-img img-fluid">
              <span class="d-inline-block group-member-link_span">
                <p class="user-name">{{$admin->member->name}}</p>
                <p class="user-followers" id="{{$admin->user_id}}" > {{count($admin->member->followers)}} {{__('groups.follower')}}</p>
              </span>
            </a>
            <div>
              @if(Auth::guard('web')->user())
                @if($admin->user_id != Auth::guard('web')->user()->id)
                  @if($admin->friendship == 'guest')
                    <button class="button-4 totyFrientshep" id="add|{{$admin->user_id}}">{{__('groups.add_friend')}}</button>
                  @elseif($admin->friendship == 'accepted')
                    <button class="button-2 totyFrientshep" id="remove|{{$admin->user_id}}">{{__('groups.un_friend')}}</button>
                  @elseif($admin->friendship == 'pending')
                    <button class="button-2 totyFrientshep" id="remove|{{$admin->user_id}}">{{__('groups.un_friend')}}</button>
                  @elseif($admin->friendship == 'request')
                    <button class="button-4 totyFrientshep" id="confirm|{{$admin->user_id}}">{{__('groups.confirm_friend')}}</button>
                    {{-- <button class="button-2 totyFrientshep" id="remove|{{$admin->user_id}}">حذف طلب الصداقة</button> --}}
                  @endif

                  @if($admin->follow == 0)
                    <button class="button-4 totyFollowing " id="addFollowing|{{$admin->user_id}}">{{__('groups.add_following')}}</button>
                  @elseif($admin->follow == 1)
                    <button class="button-2 totyFollowing" id="removeFollowing|{{$admin->user_id}}">{{__('groups.un_following')}}</button>
                  @endif

                @endif
              @else
              <a class="button-4" href="/login">{{__('groups.add_friend')}}</a>
              <a class="button-4" href="/login">{{__('groups.add_following')}}</a>

              @endif
            </div>
          </div>
        </li>
      @endforeach
    </ul>
    <h3 class="heading-tertiary my-5">الاعضاء:{{count($accepteds)}}</h3>
    <ul class="members-list list-unstyled">
      @foreach($accepteds as $accepted)
      <li class="members-item" id="{{$accepted->user_id}}|{{$group['id']}}">
        <div class="group-member d-flex justify-content-between">
          <a href="#" class="group-member-link d-flex align-items-center">
            <img src="{{asset('assets/images/users')}}/{{$accepted->member->personal_image}}" alt="#" class="member-img img-fluid">
            <span class="d-inline-block group-member-link_span">
              <p class="user-name">{{$accepted->member->name}}</p>
              <p class="user-followers" id="{{$accepted->user_id}}" > {{count($accepted->member->followers)}} {{__('groups.follower')}}</p>
            </span>
          </a>
          <div>
            @if(Auth::guard('web')->user())
              @if($accepted->friendship == 'guest')
                <button class="button-4 totyFrientshep" id="add|{{$accepted->user_id}}">أضافة صديق</button>
              @elseif($accepted->friendship == 'accepted')
                <button class="button-2 totyFrientshep" id="remove|{{$accepted->user_id}}">{{__('groups.add_friend')}}</button>
              @elseif($accepted->friendship == 'pending')
                <button class="button-2 totyFrientshep" id="remove|{{$accepted->user_id}}">{{__('groups.un_friend')}}</button>
              @elseif($accepted->friendship == 'request')
                <button class="button-4 totyFrientshep" id="confirm|{{$accepted->user_id}}">{{__('groups.confirm_friend')}}</button>
                {{-- <button class="button-2 totyFrientshep" id="remove|{{$admin->user_id}}">حذف طلب الصداقة</button> --}}
              @endif

              @if($accepted->follow == 0)
                <button class="button-4 totyFollowing " id="addFollowing|{{$accepted->user_id}}">{{__('groups.add_following')}}</button>
              @elseif($accepted->follow == 1)
                <button class="button-2 totyFollowing" id="removeFollowing|{{$accepted->user_id}}">{{__('groups.un_following')}}</button>
              @endif
              @if($isAdmin == 1 )
              <button class="button-4 totyAdmin" id="addAdmin|{{$accepted->user_id}}|{{$group['id']}}">{{__('groups.as_admin')}}</button>
              <button class="button-4 totyAdmin" id="removeMember|{{$accepted->user_id}}|{{$group['id']}}">{{__('groups.delete_member')}}</button>
              @endif

              @else
              <a class="button-4" href="/login">{{__('groups.add_friend')}}</a>
              <a class="button-4" href="/login">{{__('groups.add_following')}}</a>
              @endif
          </div>
        </div>
      </li>
      @endforeach
    </ul>
  </div>
  @if(Auth::guard('web')->user())
  <!-- invite friends -->
  <div class="modal fade" id="friends" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title" id="exampleModalLabel">{{__('groups.friends')}}</h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin: 0px;">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" >
          <section class="group-section " style="min-height:auto">
            <div class="group-members my-3">
              <ul class="members-list list-unstyled">
                @foreach($myData->friends as $friend)
                @if($friend->stateId == 1 && $friend->userResever->groupmember['state'] != '1' && $friend->userResever->groupmember['state'] != '3'  && $friend->userResever->groupmember['isAdmin'] != '1')
                <li class="members-item" id="{{$friend->userResever->id}}|{{$group['id']}}">
                  <div class="group-member d-flex justify-content-between">
                    <a href="#" class="group-member-link d-flex align-items-center">
                      <img src="{{asset('assets/images/users')}}/{{$friend->userResever->personal_image}}" alt="#" class="member-img img-fluid">
                      <span class="d-inline-block group-member-link_span">
                          <p class="user-name">{{$friend->userResever->name}}</p>
                        </span>
                    </a>
                    <div>
                      <button class="button-4 totyAdmin" id="invite|{{$friend->userResever->id}}|{{$group['id']}}">{{__('groups.invite')}}</button>
                    </div>
                  </div>
                </li>
                @endif
                @endforeach
                @foreach($myData->myfriends as $myfriends)
                @if($myfriends->stateId == 1 && $myfriends->userSender->groupmember['state'] != '1' && $myfriends->userSender->groupmember['state'] != '2' && $myfriends->userSender->groupmember['state'] != '3' && $myfriends->userResever->groupmember['isAdmin'] != '1')
                <li class="members-item" id="{{$myfriends->userSender->id}}|{{$group['id']}}">
                  <div class="group-member d-flex justify-content-between">
                    <a href="#" class="group-member-link d-flex align-items-center">
                      <img src="{{asset('assets/images/users')}}/{{$myfriends->userSender->personal_image}}" alt="#" class="member-img img-fluid">
                      <span class="d-inline-block group-member-link_span">
                          <p class="user-name">{{$myfriends->userSender->name}}</p>
                        </span>
                    </a>
                    <div>
                      <button class="button-4 totyAdmin" id="invite|{{$myfriends->userSender->id}}|{{$group['id']}}">{{__('groups.invite')}}</button>
                    </div>
                  </div>
                </li>
                @endif
                @endforeach
              </ul>
            </div>
          </section>
        </div>
        <div class="modal-footer">
          <button type="button" class="button-4 " data-dismiss="modal">{{__('groups.close')}}</button>

        </div>
      </div>
    </div>
  </div>
  @endif
@endif
@if(Auth::guard('web')->user())
<script>
  $(document).ready(function(){
     $('.totyFrientshep').click(function(event){
         event.preventDefault();
         var id = $(this).attr('id');
         var splittable = id.split('|');
         var RequestType = splittable[0];
         var Enemy_id = splittable[1];
         console.log(RequestType);
         $.ajax({
         url:'http://127.0.0.1:8000/frientshep-group',
             method:"get",
             data:{requestType:RequestType,enemy_id:Enemy_id},
             dataType:"text",
             headers: {
                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
             },
             success:function(data){
              var str = data.split('|');
              var x = 'addFollowing|'+str[1];
              console.log(x);
              if(str[0] == 1)
                    {
                      document.getElementById(id).textContent = "{{__('groups.un_friend')}}";
                      document.getElementById(id).classList.remove("button-4");
                      document.getElementById(id).classList.add("button-2");
                      document.getElementById(id).id = 'remove|'+str[1];
                      document.getElementById(str[1]).textContent = str[2] + " {{__('groups.follower')}} ";

                      document.getElementById(x).textContent = "{{__('groups.un_following')}}";
                      document.getElementById(x).classList.remove("button-4");
                      document.getElementById(x).classList.add("button-2");
                      document.getElementById(x).id = 'removeFollowing|'+str[1];
                    }
                    if(str[0] == 2)
                    {
                      document.getElementById(id).textContent = "{{__('groups.un_friend_request')}}";
                      document.getElementById(id).classList.remove("button-4");
                      document.getElementById(id).classList.add("button-2");
                      document.getElementById(id).id = 'remove|'+str[1];
                      document.getElementById(str[1]).textContent = str[2] + " {{__('groups.follower')}} ";

                      document.getElementById(x).textContent = "{{__('groups.un_following')}}";
                      document.getElementById(x).classList.remove("button-4");
                      document.getElementById(x).classList.add("button-2");
                      document.getElementById(x).id = 'removeFollowing|'+str[1];
                    }
                    if(str[0] == 0)
                    {
                        document.getElementById(id).textContent = "{{__('groups.confirm_friend')}}";
                        document.getElementById(id).classList.remove("button-2");
                        document.getElementById(id).classList.add("button-4");
                        document.getElementById(id).id = 'add|'+str[1];
                        document.getElementById(str[1]).textContent = str[2] + " {{__('groups.follower')}} ";

                        document.getElementById('removeFollowing|'+str[1]).textContent = "{{__('groups.add_following')}}";
                        document.getElementById('removeFollowing|'+str[1]).classList.remove("button-2");
                        document.getElementById('removeFollowing|'+str[1]).classList.add("button-4");
                        document.getElementById('removeFollowing|'+str[1]).id = 'addFollowing|'+str[1];
                    }
             }
         });

     });

     $('.totyFollowing').click(function(event){
         event.preventDefault();
         var id = $(this).attr('id');
         var splittable = id.split('|');
         var RequestType = splittable[0];
         var Enemy_id = splittable[1];
         console.log(RequestType);
         $.ajax({
         url:'http://127.0.0.1:8000/following-group',
             method:"get",
             data:{requestType:RequestType,enemy_id:Enemy_id},
             dataType:"text",
             headers: {
                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
             },
             success:function(data){
              var str = data.split('|');
              if(str[0] == 1)
                {
                  document.getElementById(id).textContent = "{{__('groups.un_following')}}";
                  document.getElementById(id).classList.remove("button-4");
                  document.getElementById(id).classList.add("button-2");
                  document.getElementById(id).id = 'removeFollowing|'+str[1];
                  document.getElementById(str[1]).textContent = str[2] + "{{__('groups.follower')}}";
                }
                    
                if(str[0] == 0)
                {
                    document.getElementById(id).textContent =  "{{__('groups.add_following')}}";
                    document.getElementById(id).classList.remove("button-2");
                    document.getElementById(id).classList.add("button-4");
                    document.getElementById(id).id = 'addFollowing|'+str[1];
                    document.getElementById(str[1]).textContent = str[2] + "{{__('groups.follower')}}";
                }
              //  alert(data);
             }
         });

     });

     $('.totyAdmin').click(function(event){
         event.preventDefault();
         var id = $(this).attr('id');
         var splittable = id.split('|');
         var RequestType = splittable[0];
         var Enemy_id = splittable[1];
         var Group_id = splittable[2];
         console.log(RequestType);
         $.ajax({
         url:'http://127.0.0.1:8000/asignAdmin-group',
             method:"get",
             data:{requestType:RequestType,enemy_id:Enemy_id,group_id:Group_id},
             dataType:"text",
             headers: {
                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
             },
             success:function(data){
              var str = data.split('|');
              if(str[0] == 1)
                {
                  // var sad =  `<li class='members-item'>` + document.getElementById(Enemy_id+'|'+Group_id).innerHTML + `</li>`;
                  // console.log(sad);
                  // document.getElementById('adddmin').innerHTML +=  document.getElementById(Enemy_id+'|'+Group_id).innerHTML ;
                  // document.getElementById(Enemy_id+'|'+Group_id).style.display = "none";
                  document.getElementById(id).textContent =  "{{__('groups.admin')}}";
                  document.getElementById(id).classList.remove("button-4");
                  document.getElementById(id).classList.add("button-2");
                  // document.getElementById('addAdmin|'+Enemy_id+'|'+Group_id).style.display = "none";
                  document.getElementById('removeMember|'+Enemy_id+'|'+Group_id).style.display = "none";

                }
                    
                if(str[0] == 0)
                {
                  document.getElementById(Enemy_id+'|'+Group_id).style.display = "none";
                }
                
             }
         });

     });
 });
</script>
@endif
@endsection