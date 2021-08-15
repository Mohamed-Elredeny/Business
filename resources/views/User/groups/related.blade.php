<div class="col-lg-3 d-none d-lg-block">
    <div class="sticky-top">
        <section class="group-suggestions-section py-4 px-2">
            <h2 class="heading-secondary mb-3">{{__('groups.anthe_groups')}}</h2>
            @foreach($related_groups as $relate_group)
            @if($relate_group->id != $group->id)
            <div class="card"> 
                <a href="/groups/{{$relate_group->id}}">
                    <img src="{{asset('assets/images/groups/profile')}}/{{$relate_group->profile_image}}" class="card-img-top" alt="...">
                </a>
                <div class="d-flex justify-content-between">
                    <div class="card-body">
                        <a href="groups/{{$relate_group->id}}" style="color:black !important">
                            <h3 class="card-title">{{$relate_group->name}}</h3>
                        </a>
                        <p class="card-text"><small class="text-muted" id="{{$relate_group->id}}">
                            <?php 
                                $member = App\models\GroupMember::where('group_id',$relate_group->id)->count();  
                                echo $member;  
                            ?>  
                            {{__('groups.member')}}</small>
                        </p>
                    </div>
                    <div class="p-2">
                        @if(Auth::guard('web')->user())
                            <?php 
                                $checkState = App\models\GroupMember::where('group_id',$relate_group->id)->where('user_id',auth::user()->id)->get();  
                            ?>
                            @if (count($checkState)==0)
                            <div class="p-2">
                                    <button class="button-4 toty" id="join|{{$relate_group->id}}" >{{__('groups.join')}} </button>
                            </div>

                            @elseif (count($checkState)>0)
                                @if ($checkState[0]->state == 1)
                                    <div class="p-2">
                                            <button class="button-2 toty" id="leave|{{$relate_group->id}}">{{__('groups.left')}}</button>
                                    </div>

                                @elseif ($checkState[0]->state == 2)
                                    <div class="p-2">
                                        <button class="button-2 toty" id="leave|{{$relate_group->id}}">{{__('groups.left_request')}}</button>                                        
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
            @endif
            @endforeach
        </section>
    </div>
</div>

@if(Auth::guard('web')->user())
{{-- <script
  src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
  integrity="sha256-pasqAKBDmFT4eHoN2ndd6lN370kFiGUFyTiUHWhU7k8="
  crossorigin="anonymous"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> --}}

<script>
     $(document).ready(function(){
        $('.toty').click(function(event){
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
                        document.getElementById(str[1]).textContent = str[2];
                    }
                    
                    // alert(data);
                }
            });

        });
    });
</script>
@endif