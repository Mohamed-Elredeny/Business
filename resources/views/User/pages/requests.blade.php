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
    <table class="table table-hover">
        <thead>
        <tr> 
          <th scope="col">#</th>
          <th scope="col">{{__('pages.image_word')}}</th>
          <th scope="col">{{__('pages.name_word')}}</th>
          <th scope="col">{{__('pages.stutes_word')}}</th>
        </tr>
        </thead>
        <tbody>
            <?php $i = 0; ?> 
            @foreach($page_requests as $page_request)
            <?php $i = $i+1; ?> 
                <tr id="{{$page_request->id}}">
                    <th scope="row">{{$i}}</th>
                    <td>
                    <img class="circle" src="{{asset('assets/images/users')}}/{{$page_request->member->personal_image}}" width="60" height="60">
                    </td>
                    <td>
                    <a href="#" class="link-cust" >{{$page_request->member->name}}</a>
                    </td>
                    <td>
                    <button class="button-4 totyRequest" id="conferm|{{$page_request->id}}" >{{__('pages.confirm_request')}}</button>
                    <button class="button-5 totyRequest" id="delete|{{$page_request->id}}">{{__('pages.refuse_request')}}</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
      </table>
</div> 
@endif

<script>
   $(document).ready(function(){
      $('.totyRequest').click(function(event){
          event.preventDefault();
          var id = $(this).attr('id');
          var splittable = id.split('|');
          var RequestType = splittable[0];
          var Request_id = splittable[1];
          console.log(RequestType);
          $.ajax({
          url:'http://127.0.0.1:8000/changeRequest-page',
              method:"get",
              data:{requestType:RequestType,request_id:Request_id},
              dataType:"text",
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              success:function(data){
                document.getElementById(data).style.display = "none";
                // alert(data);
              }
          });

      });
  });
</script>
@endsection