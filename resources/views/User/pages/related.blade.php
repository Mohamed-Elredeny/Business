<div class="col-lg-3 d-none d-lg-block">
    <div class="sticky-top">
        <section class="group-suggestions-section py-4 px-2">
            <h2 class="heading-secondary mb-3">{{__('pages.anthe_pages')}}</h2>
            @foreach($related_pages as $relate_page)
            @if($relate_page->id != $page->id)
            <div class="card">
                <a href="/pages/{{$relate_page->id}}">
                    <img src="{{asset('assets/images/pages/profile')}}/{{$relate_page->profile_image}}" class="card-img-top" alt="...">
                </a>
                <div class="d-flex justify-content-between">
                    <div class="card-body">
                        <a href="pages/{{$relate_page->id}}" style="color:black !important">
                            <h3 class="card-title">{{$relate_page->name}}</h3>
                        </a>
                        <p class="card-text"><small class="text-muted" id="{{$relate_page->id}}">
                            <?php 
                                $member = App\models\PageMember::where('page_id',$relate_page->id)->count();  
                                echo $member;  
                            ?>  
                            {{__('pages.member')}}</small>
                        </p>
                    </div>
                    <div class="p-2">
                        @if(Auth::guard('web')->user())
                            <?php 
                                $checkState = App\models\PageMember::where('page_id',$relate_page->id)->where('user_id',auth::user()->id)->get();  
                            ?>
                            @if (count($checkState)==0)
                            <div class="p-2">
                                    <button class="button-4 toty" id="join|{{$relate_page->id}}" >{{__('pages.like')}} </button>
                            </div>

                            @elseif (count($checkState)>0)
                                @if ($checkState[0]->state == 1)
                                    <div class="p-2">
                                            <button class="button-2 toty" id="leave|{{$relate_page->id}}">{{__('pages.dislike')}}</button>
                                    </div>

                                @elseif ($checkState[0]->state == 2)
                                    <div class="p-2">
                                        <button class="button-2 toty" id="leave|{{$relate_page->id}}">{{__('pages.dislike_request')}}</button>                                        
                                    </div>

                                @elseif ($checkState[0]->isAdmin == 1)
                                    <div class="p-2">
                                        <button class="button-2">{{__('pages.admin')}}</button>                                        
                                    </div>
                                @endif
                            @endif
                        @else
                            <form action="/login" method="post">
                                @csrf
                                <button class="button-4">{{__('pages.like')}}</button>
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
            var Page_id = splittable[1];
            var User_id = {{auth::user()->id}};
            console.log(RequestType);
            $.ajax({
            url:'http://127.0.0.1:8000/join-page',
                method:"get",
                data:{requestType:RequestType,page_id:Page_id, user_id:User_id},
                dataType:"text",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success:function(data){
                    var str = data.split('|');
                    if(str[0] == 1)
                    {
                        document.getElementById(id).textContent = "{{__('pages.dislike')}}";
                        document.getElementById(id).classList.remove("button-4");
                        document.getElementById(id).classList.add("button-2");
                        document.getElementById(id).id = 'leave|'+str[1];
                        document.getElementById(str[1]).textContent = str[2];
                    }
                    if(str[0] == 2)
                    {
                        document.getElementById(id).textContent = "{{__('pages.dislike_request')}}";
                        document.getElementById(id).classList.remove("button-4");
                        document.getElementById(id).classList.add("button-2");
                        document.getElementById(id).id = 'leave|'+str[1];
                        document.getElementById(str[1]).textContent = str[2];
                    }
                    if(str[0] == 0)
                    {
                        document.getElementById(id).textContent = "{{__('pages.like')}}";
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