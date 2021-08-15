@extends('User.pages.layout')
 
@section('sectionPages')
@if($page['privacy'] == 0 && $isAdmin == 0 && $myState == 0)
<div class="group-about my-3">
    <div class="group-description">
        <h3 class="heading-tertiary">{{__('pages.privacy')}}</h3>
    </div>
</div> 
@else
<div class="page-members my-3">
    <div class="invite-friends d-flex justify-content-between">
      <h3 class="heading-tertiary">{{__('pages.invite_friends')}}</h3>
      @if(Auth::guard('web')->user())
      <button class="button-4"  data-toggle="modal" data-target="#friends">{{__('pages.invite')}}</button>
      @else
      <a class="button-4" href="/login">{{__('pages.invite')}}</a>
      @endif
    </div>
    <h3 class="heading-tertiary my-5">{{__('pages.admins')}}:{{count($admins)}}</h3>
    <ul class="members-list list-unstyled" id="adddmin">
      @foreach($admins as $admin)
      <li class="members-item">
        <div class="page-member d-flex justify-content-between">
              <a href="#" class="page-member-link d-flex align-items-center">
                <img src="{{asset('assets/images/users')}}/{{$admin->member->personal_image}}" alt="#" class="member-img img-fluid">
                <span class="d-inline-block page-member-link_span">
                  <p class="user-name">{{$admin->member->name}}</p>
                  <p class="user-followers" id="{{$admin->user_id}}" > {{count($admin->member->followers)}} {{__('pages.follower')}}</p>
                </span>
              </a>
            <div>
              @if(Auth::guard('web')->user())
                @if($admin->user_id != Auth::guard('web')->user()->id)
                  @if($admin->friendship == 'guest')
                    <button class="button-4 totyFrientshep" id="add|{{$admin->user_id}}">{{__('pages.add_friend')}}</button>
                  @elseif($admin->friendship == 'accepted')
                    <button class="button-2 totyFrientshep" id="remove|{{$admin->user_id}}">{{__('pages.un_friend')}}</button>
                  @elseif($admin->friendship == 'pending')
                    <button class="button-2 totyFrientshep" id="remove|{{$admin->user_id}}">{{__('pages.un_friend')}}</button>
                  @elseif($admin->friendship == 'request')
                    <button class="button-4 totyFrientshep" id="confirm|{{$admin->user_id}}">{{__('pages.confirm_friend')}}</button>
                  @endif

                  @if($admin->follow == 0)
                    <button class="button-4 totyFollowing " id="addFollowing|{{$admin->user_id}}">{{__('pages.add_following')}}</button>
                  @elseif($admin->follow == 1)
                    <button class="button-2 totyFollowing" id="removeFollowing|{{$admin->user_id}}">{{__('pages.un_following')}}</button>
                  @endif

                  
                @endif
                @else
                <a class="button-4" href="/login">{{__('pages.add_friend')}}</a>
                <a class="button-4" href="/login">{{__('pages.add_following')}}</a>

              @endif
              
            </div>
          </div>
      </li>
      @endforeach
    </ul>
    <h3 class="heading-tertiary my-5">الاعضاء:{{count($accepteds)}}</h3>
    <ul class="members-list list-unstyled">
      @foreach($accepteds as $accepted)
      <li class="members-item" id="{{$accepted->user_id}}|{{$page['id']}}">
        <div class="page-member d-flex justify-content-between">
          <a href="#" class="page-member-link d-flex align-items-center">
            <img src="{{asset('assets/images/users')}}/{{$accepted->member->personal_image}}" alt="#" class="member-img img-fluid">
            <span class="d-inline-block page-member-link_span">
              <p class="user-name">{{$accepted->member->name}}</p>
              <p class="user-followers" id="{{$accepted->user_id}}" > {{count($accepted->member->followers)}} {{__('pages.follower')}}</p>
            </span>
          </a>
          <div>
            @if(Auth::guard('web')->user())
              @if($accepted->friendship == 'guest')
                <button class="button-4 totyFrientshep" id="add|{{$accepted->user_id}}">{{__('pages.add_friend')}}</button>
              @elseif($accepted->friendship == 'accepted')
                <button class="button-2 totyFrientshep" id="remove|{{$accepted->user_id}}">{{__('pages.un_friend')}}</button>
              @elseif($accepted->friendship == 'pending')
                <button class="button-2 totyFrientshep" id="remove|{{$accepted->user_id}}">{{__('pages.un_friend_request')}}</button>
              @elseif($accepted->friendship == 'request')
                <button class="button-4 totyFrientshep" id="confirm|{{$accepted->user_id}}">{{__('pages.confirm_friend')}}</button>
                {{-- <button class="button-2 totyFrientshep" id="remove|{{$admin->user_id}}">حذف طلب الصداقة</button> --}}
              @endif

              @if($accepted->follow == 0)
                <button class="button-4 totyFollowing " id="addFollowing|{{$accepted->user_id}}">{{__('pages.add_following')}}</button>
              @elseif($accepted->follow == 1)
                <button class="button-2 totyFollowing" id="removeFollowing|{{$accepted->user_id}}">{{__('pages.un_following')}}</button>
              @endif
              @if($isAdmin == 1 )
              <button class="button-4 totyAdmin" id="addAdmin|{{$accepted->user_id}}|{{$page['id']}}">{{__('pages.as_admin')}}</button>
              <button class="button-4 totyAdmin" id="removeMember|{{$accepted->user_id}}|{{$page['id']}}">{{__('pages.delete_member')}}</button>
              @endif

              @else
              <a class="button-4" href="/login">{{__('pages.add_friend')}}</a>
              <a class="button-4" href="/login">{{__('pages.add_following')}}</a>
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
          <h3 class="modal-title" id="exampleModalLabel">{{__('pages.friends')}}</h3>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin: 0px;">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" >
          <section class="group-section " style="min-height:auto">
            <div class="group-members my-3">
              <ul class="members-list list-unstyled">
                @foreach($myData->friends as $friend)
                @if($friend->stateId == 1 && $friend->userResever->pagemember['state'] != '1' && $friend->userResever->pagemember['state'] != '3'  && $friend->userResever->groupmember['isAdmin'] != '1')
                <li class="members-item" id="{{$friend->userResever->id}}|{{$page['id']}}">
                  <div class="group-member d-flex justify-content-between">
                    <a href="#" class="group-member-link d-flex align-items-center">
                      <img src="{{asset('assets/images/users')}}/{{$friend->userResever->personal_image}}" alt="#" class="member-img img-fluid">
                      <span class="d-inline-block group-member-link_span">
                          <p class="user-name">{{$friend->userResever->name}}</p>
                        </span>
                    </a>
                    <div>
                      <button class="button-4 totyAdmin" id="invite|{{$friend->userResever->id}}|{{$page['id']}}">{{__('pages.invite')}}</button>
                    </div>
                  </div>
                </li>
                @endif
                @endforeach
                @foreach($myData->myfriends as $myfriends)
                @if($myfriends->stateId == 1 && $myfriends->userSender->pagemember['state'] != '1' && $myfriends->userSender->pagemember['state'] != '2' && $myfriends->userSender->groupmember['state'] != '3' && $myfriends->userResever->groupmember['isAdmin'] != '1')
                <li class="members-item" id="{{$myfriends->userSender->id}}|{{$page['id']}}">
                  <div class="page-member d-flex justify-content-between">
                    <a href="#" class="page-member-link d-flex align-items-center">
                      <img src="{{asset('assets/images/users')}}/{{$myfriends->userSender->personal_image}}" alt="#" class="member-img img-fluid">
                      <span class="d-inline-block page-member-link_span">
                          <p class="user-name">{{$myfriends->userSender->name}}</p>
                        </span>
                    </a>
                    <div>
                      <button class="button-4 totyAdmin" id="invite|{{$myfriends->userSender->id}}|{{$page['id']}}">{{__('pages.invite')}}</button>
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
          <button type="button" class="button-4 " data-dismiss="modal">{{__('pages.close')}}</button>

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
         url:'http://127.0.0.1:8000/frientshep-page',
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
                      document.getElementById(id).textContent = "{{__('pages.un_friend')}}";
                      document.getElementById(id).classList.remove("button-4");
                      document.getElementById(id).classList.add("button-2");
                      document.getElementById(id).id = 'remove|'+str[1];
                      document.getElementById(str[1]).textContent = str[2] + " {{__('pages.follower')}} ";

                      document.getElementById(x).textContent = "{{__('pages.un_following')}}";
                      document.getElementById(x).classList.remove("button-4");
                      document.getElementById(x).classList.add("button-2");
                      document.getElementById(x).id = 'removeFollowing|'+str[1];
                    }
                    if(str[0] == 2)
                    {
                      document.getElementById(id).textContent = "{{__('pages.un_friend_request')}}";
                      document.getElementById(id).classList.remove("button-4");
                      document.getElementById(id).classList.add("button-2");
                      document.getElementById(id).id = 'remove|'+str[1];
                      document.getElementById(str[1]).textContent = str[2] + " {{__('pages.follower')}} ";

                      document.getElementById(x).textContent = "{{__('pages.un_following')}}";
                      document.getElementById(x).classList.remove("button-4");
                      document.getElementById(x).classList.add("button-2");
                      document.getElementById(x).id = 'removeFollowing|'+str[1];
                    }
                    if(str[0] == 0)
                    {
                        document.getElementById(id).textContent = "{{__('pages.confirm_friend')}}";
                        document.getElementById(id).classList.remove("button-2");
                        document.getElementById(id).classList.add("button-4");
                        document.getElementById(id).id = 'add|'+str[1];
                        document.getElementById(str[1]).textContent = str[2] + "{{__('pages.follower')}}";

                        document.getElementById('removeFollowing|'+str[1]).textContent = "{{__('pages.add_following')}}";
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
         url:'http://127.0.0.1:8000/following-page',
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
                  document.getElementById(id).textContent = "{{__('pages.un_following')}}";
                  document.getElementById(id).classList.remove("button-4");
                  document.getElementById(id).classList.add("button-2");
                  document.getElementById(id).id = 'removeFollowing|'+str[1];
                  document.getElementById(str[1]).textContent = str[2] + "{{__('pages.follower')}}";
                }
                    
                if(str[0] == 0)
                {
                    document.getElementById(id).textContent = "{{__('pages.add_following')}}";
                    document.getElementById(id).classList.remove("button-2");
                    document.getElementById(id).classList.add("button-4");
                    document.getElementById(id).id = 'addFollowing|'+str[1];
                    document.getElementById(str[1]).textContent = str[2] + "{{__('pages.follower')}}";
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
         var Page_id = splittable[2];
         console.log(RequestType);
         $.ajax({
         url:'http://127.0.0.1:8000/asignAdmin-page',
             method:"get",
             data:{requestType:RequestType,enemy_id:Enemy_id,page_id:Page_id},
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
                  document.getElementById(id).textContent = "{{__('pages.admin')}}";
                  document.getElementById(id).classList.remove("button-4");
                  document.getElementById(id).classList.add("button-2");
                  // document.getElementById('addAdmin|'+Enemy_id+'|'+Group_id).style.display = "none";
                  document.getElementById('removeMember|'+Enemy_id+'|'+Page_id).style.display = "none";

                }
                    
                if(str[0] == 0)
                {
                  document.getElementById(Enemy_id+'|'+Page_id).style.display = "none";
                }
                
             }
         });

     });
 });
</script>
@endif
@endsection