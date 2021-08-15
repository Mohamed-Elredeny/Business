@extends('layouts.martina.site')

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <input type="text" class="search-box" placeholder="{{__('groups.search')}}">
            <section class="group-section px-3">
              <div class="group-mygroups py-3">
                <div class="row text-center ">
                    @if(Auth::guard('web')->user())
                  <a  class="button-4 col-5 mx-auto tablinks" href="/my-group">{{__('groups.my_groups')}}</a>
                  @else
                  <a  class="button-4 col-5 mx-auto tablinks" href="/login">{{__('groups.my_groups')}}</a>
                  @endif
                  <a class="button-4 col-5  mx-auto tablinks" href="/all-group">{{__('groups.all_groups')}}</a>

                </div><br>


                @if(Auth::guard('web')->user())

                <div id="my-groups" class=" tabcontent" >
                    <div  class="row ">
                        @foreach($my_groups as $my_group)
                          <div class="col-lg-4" id="{{$my_group->group->id}}|0">
                              <div class="card">
                                  <a href="/groups/{{$my_group->group->id}}">
                                      <img src="{{asset('assets/images/groups/profile')}}/{{$my_group->group->profile_image}}" class="card-img-top" width="200" height="200" alt="...">
                                  </a>
                                  <div class="d-flex justify-content-between">
                                      <div class="card-body">
                                          <a href="groups/{{$my_group->group->id}}" style="color:black !important">
                                              <h3 class="card-title">{{$my_group->name}}</h3>
                                          </a>
                                          <p class="card-text"><small class="text-muted" id="{{$my_group->group->id}}">
                                              <?php
                                                  $member = App\models\GroupMember::where('group_id',$my_group->group->id)->count();
                                                  echo $member;
                                              ?>
                                              {{__('groups.member')}}</small>
                                          </p>
                                      </div>
                                      <div class="p-2">
                                          @if(Auth::guard('web')->user())
                                              <?php
                                                  $checkState = App\models\GroupMember::where('group_id',$my_group->group->id)->where('user_id',auth::user()->id)->get();
                                              ?>
                                              @if (count($checkState)==0)
                                              <div class="p-2">
                                                      <button class="button-4 totyAllgroups" id="join|{{$my_group->group->id}}" >{{__('groups.join')}} </button>
                                              </div>

                                              @elseif (count($checkState)>0)
                                                  @if ($checkState[0]->state == 1)
                                                      <div class="p-2">
                                                              <button class="button-2 totyAllgroups" id="leave|{{$my_group->group->id}}">{{__('groups.left')}}</button>
                                                      </div>

                                                  @elseif ($checkState[0]->state == 2)
                                                      <div class="p-2">
                                                          <button class="button-2 totyAllgroups" id="leave|{{$my_group->group->id}}|0">{{__('groups.left_request')}}</button>
                                                      </div>

                                                  @elseif ($checkState[0]->isAdmin == 1)
                                                      <div class="p-2">
                                                          <button class="button-2">{{__('groups.admin')}}</button>
                                                      </div>
                                                  @endif
                                              @endif
                                          @else
                                              <form action="/login" method="post">
                                                  @csrf
                                                  <button class="button-4">{{__('groups.join')}}</button>
                                              </form>
                                          @endif
                                      </div>
                                  </div>
                              </div>

                          </div>

                          @endforeach



                    </div>
                </div>

                @endif

              </div>
            </section>
      </div>
        {{-- @include('User.groups.related') --}}
    </div>
<script
  src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
  integrity="sha256-pasqAKBDmFT4eHoN2ndd6lN370kFiGUFyTiUHWhU7k8="
  crossorigin="anonymous"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  @if(Auth::guard('web')->user())

    <script>
        $(document).ready(function(){
           $('.totyAllgroups').click(function(event){
               event.preventDefault();
               var id = $(this).attr('id');
               var splittable = id.split('|');
               var RequestType = splittable[0];
               var Group_id = splittable[1];
               var User_id = {{auth::user()->id}};
               console.log(RequestType);
               $.ajax({
               url:'http://127.0.0.1:8000/join-group',
                   method:"get",
                   data:{requestType:RequestType,group_id:Group_id, user_id:User_id},
                   dataType:"text",
                   headers: {
                       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                   },
                   success:function(data){
                       var str = data.split('|');
                       if(str[0] == 1)
                       {
                           document.getElementById(id).textContent = "{{__('groups.left')}}";
                           document.getElementById(id).classList.remove("button-4");
                           document.getElementById(id).classList.add("button-2");
                           document.getElementById(id).id = 'leave|'+str[1];
                           document.getElementById(str[1]).textContent = str[2];
                       }
                       if(str[0] == 2)
                       {
                           document.getElementById(id).textContent = "{{__('groups.left_request')}}";
                           document.getElementById(id).classList.remove("button-4");
                           document.getElementById(id).classList.add("button-2");
                           document.getElementById(id).id = 'leave|'+str[1];
                           document.getElementById(str[1]).textContent = str[2];
                       }
                       if(str[0] == 0)
                       {
                           document.getElementById(id).textContent = "{{__('groups.join')}}";
                           document.getElementById(id).classList.remove("button-2");
                           document.getElementById(id).classList.add("button-4");
                           document.getElementById(id).id = 'join|'+str[1];
                           document.getElementById(str[1]+'|0').style.display = 'none';
                       }

                       // alert(data);
                   }
               });

           });
       });
   </script>
   @endif
@endsection

