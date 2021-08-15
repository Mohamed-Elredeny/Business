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
    <table class="table table-hover">
        <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">{{__('groups.image_word')}}</th>
          <th scope="col">{{__('groups.name_word')}}</th>
          <th scope="col">{{__('groups.stutes_word')}}</th>
        </tr>
        </thead>
        <tbody>
            <?php $i = 0; ?> 
            @foreach($group_requests as $group_request)
            <?php $i = $i+1; ?> 
                <tr id="{{$group_request->id}}">
                    <th scope="row">{{$i}}</th>
                    <td>
                    <img class="circle" src="{{asset('assets/images/users')}}/{{$group_request->member->personal_image}}" width="60" height="60">
                    </td>
                    <td>
                    <a href="#" class="link-cust" >{{$group_request->member->name}}</a>
                    </td>
                    <td>
                    <button class="button-4 totyRequest" id="conferm|{{$group_request->id}}" >{{__('groups.confirm_request')}}</button>
                    <button class="button-5 totyRequest" id="delete|{{$group_request->id}}">{{__('groups.refuse_request')}}</button>
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
          url:'http://127.0.0.1:8000/changeRequest-group',
              method:"get",
              data:{requestType:RequestType,request_id:Request_id},
              dataType:"text",
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              success:function(data){
                document.getElementById(data).style.display = "none";
              }
          });

      });
  });
</script>
@endsection