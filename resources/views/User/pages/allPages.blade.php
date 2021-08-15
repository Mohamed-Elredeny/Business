@extends('layouts.site')

@section('content')
    
    <div class="row"> 
        <div class="col-lg-12">
            <input type="text" class="search-box" placeholder="{{__('pages.search')}}">
            <section class="group-section px-3">
              <div class="group-mygroups py-3">
                <div class="row text-center ">
                    @if(Auth::guard('web')->user())
                  <a  class="button-4 col-5 mx-auto tablinks" href="/my-page">{{__('pages.my_pages')}}</a>
                  @else
                  <a  class="button-4 col-5 mx-auto tablinks" href="/login">{{__('pages.my_pages')}}</a>
                  @endif
                  <a class="button-4 col-5  mx-auto tablinks" href="/all-page">{{__('pages.all_pages')}}</a>
                  
                </div><br>
  
                <div id="all-pages" class=" tabcontent" >
                  <div  class="row ">
                      @foreach($all_pages as $all_page)
                        <div class="col-lg-4">
                            <div class="card">
                                <a href="/pages/{{$all_page->id}}">
                                    <img src="{{asset('assets/images/pages/profile')}}/{{$all_page->profile_image}}" class="card-img-top" width="200" height="200" alt="...">
                                </a>
                                <div class="d-flex justify-content-between">
                                    <div class="card-body">
                                        <a href="pages/{{$all_page->id}}" style="color:black !important">
                                            <h3 class="card-title">{{$all_page->name}}</h3>
                                        </a>
                                        <p class="card-text"><small class="text-muted" id="{{$all_page->id}}">
                                            <?php 
                                                $member = App\models\PageMember::where('page_id',$all_page->id)->count();  
                                                echo $member;  
                                            ?>  
                                            {{__('pages.member')}}</small>
                                        </p>
                                    </div>
                                    <div class="p-2">
                                        @if(Auth::guard('web')->user())
                                            <?php 
                                                $checkState = App\models\PageMember::where('page_id',$all_page->id)->where('user_id',auth::user()->id)->get();  
                                            ?>
                                            @if (count($checkState)==0)
                                            <div class="p-2">
                                                    <button class="button-4 totyAllpages" id="join|{{$all_page->id}}" >{{__('pages.like')}} </button>
                                            </div>
                
                                            @elseif (count($checkState)>0)
                                                @if ($checkState[0]->state == 1)
                                                    <div class="p-2">
                                                            <button class="button-2 totyAllpages" id="leave|{{$all_page->id}}">{{__('pages.dislike')}}</button>
                                                    </div>
                
                                                @elseif ($checkState[0]->state == 2)
                                                    <div class="p-2">
                                                        <button class="button-2 totyAllpages" id="leave|{{$all_page->id}}">{{__('pages.dislike_request')}}</button>                                        
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

                        </div>

                        @endforeach

                        <div class="w-100">
                            {{ $all_pages->links() }}
                        </div>
                    
                  </div>
                </div>

  
              </div>
            </section>
      </div>
        {{-- @include('User.pages.related') --}}
    </div>
<script
  src="https://code.jquery.com/jquery-3.4.1.slim.min.js"
  integrity="sha256-pasqAKBDmFT4eHoN2ndd6lN370kFiGUFyTiUHWhU7k8="
  crossorigin="anonymous"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  @if(Auth::guard('web')->user())

    <script>
        $(document).ready(function(){
           $('.totyAllpages').click(function(event){
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
@endsection

