@extends('layouts.martina.site')

@section('content')

    <div class="row">
        <div class="col-lg-10">
            <section class="group-section px-3">
              <div class="group-new-group">
                <h2 class="heading-secondary">{{__('groups.edit')}}</h2>
                <form method="post" action="{{route('groups.update',['group'=>$group->id])}}" enctype="multipart/form-data">
                    @csrf
                    @method('put')
                  <input class="form-control form-control-lg my-5" type="text" placeholder="{{__('groups.name_group')}}" name="name" value="{{$group->name}}" required>
                  <select class="form-control form-control-lg my-5" name="privacy" required>
                    <option value="" disabled>{{__('groups.pivacy_add')}}</option>
                    @if($group->privacy == 1 )
                    <option value="1" selected>{{__('groups.public')}}</option>
                    <option value="0">{{__('groups.private')}}</option>
                    @else
                    <option value="0" selected>{{__('groups.private')}}</option>
                    <option value="1">{{__('groups.public')}}</option>
                    @endif
                  </select>
                  <select class="form-control form-control-lg my-5" name="category_id" required>
                    <option value="" disabled>{{__('groups.category')}}</option>
                    @foreach($categroys as $categroy)
                    @if($group->category_id == $categroy->id )
                    <option value="{{$categroy->id}}" selected>{{$categroy->name_ar}}</option>
                    @else
                    <option value="{{$categroy->id}}">{{$categroy->name_ar}}</option>
                    @endif
                    @endforeach

                  </select>
                  <div class="uimage-div my-5 text-center">
                    <canvas id= "canv1" style="width:50%;height:150px;border: none"></canvas><br>
                    <label class="select-image-label" for="finput" >{{__('groups.upload_profile')}}</label><br>
                    <input class="select-image-input" type="file" dir="rtl" multiple="false" name="profile_image" accept="image/*" id=finput  onchange="uploadimg()">

                  </div>
                  <div class="uimage-div my-5 text-center" >
                    <canvas id= "canv2" style="width: 100%;height: 150px;border: none"></canvas><br>
                    <label class="select-image-label" for="finput2" >{{__('groups.upload_cover')}}</label><br>
                    <input class="select-image-input" type="file" multiple="false" accept="image/*" name="cover_image" id=finput2 onchange="uploadcover()"  >
                  </div>
                  <textarea dir="rtl" class="form-control form-control-lg my-5" placeholder="وصف المجموعة" name="description" required>{{$group->description}}</textarea>
                  <textarea dir="rtl" class="form-control form-control-lg my-5" placeholder="قواعد المجموعة" name="rules" required>{{$group->rules}}</textarea>

                  <button type="submit" class="button-4 mt-5">{{__('groups.update')}}</button>
                </form>
              </div>
            </section>
      </div>
        {{-- @include('User.groups.related') --}}
    </div>
    <script src="https://www.dukelearntoprogram.com/course1/common/js/image/SimpleImage.js"></script>
    <script>
      function draw() {
        var ctx = document.getElementById('canv2').getContext('2d');
        var img = new Image();
        ctx.canvas.width = window.innerWidth;
        ctx.canvas.height = window.innerHeight;
        img.onload = function() {
          ctx.drawImage(img, 0, 0,window.innerWidth,window.innerHeight);
        };
      img.src = "{{asset('assets/images/groups/cover')}}/{{$group->cover_image}}";

      var ctx1 = document.getElementById('canv1').getContext('2d');
        var img1 = new Image();
        ctx1.canvas.width = window.innerWidth;
        ctx1.canvas.height = window.innerHeight;
        img1.onload = function() {
          ctx1.drawImage(img1, 0, 0,window.innerWidth,window.innerHeight);
        };
      img1.src = "{{asset('assets/images/groups/profile')}}/{{$group->profile_image}}";
      }
      draw();
    </script>
@endsection

