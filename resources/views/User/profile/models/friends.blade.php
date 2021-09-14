<?php
use App\Models\Following;
use App\models\Friendship;
?>
<!-- Friends -->
<div class="modal fade bd-example-modal-lg" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle"> {{$profileId->name}} Friends</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body " >
                <div class="row">
                    <div class="col-sm-12 text-center  ">
                        <center>
                            <div class="col-sm-2 button-4 totyAdmin mr-1">
                                My Friends
                            </div>
                            <div class="col-sm-2 button-4 totyAdmin mr-1">
                                My Requests
                            </div>
                            <div class="col-sm-2 button-4 totyAdmin mr-1">
                                Pending
                            </div>
                            <div class="col-sm-2 button-4 totyAdmin mr-1">
                                Add Friend
                            </div>
                        </center>
                    </div>
                </div>

<!--   Note         -->

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!--Followings-->
<div class="modal fade" id="followings" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="exampleModalLabel">My Following List</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin: 0px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <!--            -->
            <div class="modal-body myULxx" >
                <section class="group-section " style="min-height:auto">
                    <div class="group-members my-3">
                        <ul class="members-list list-unstyled" >
                            @if(count($Myfollowings)  > 0)
                            @foreach($Myfollowings as $following)
                            <li class="members-item" id="">
                                <div class="group-member d-flex justify-content-between">
                                    <a href="#" class="group-member-link d-flex align-items-center">
                                        <img src="{{asset('assets/images/users/'.$following->following->personal_image)}}" alt="#" class="member-img img-fluid">
                                        <span class="d-inline-block group-member-link_span">
                                              <p class="user-name">{{$following->following->name}}</p>
                                            </span>
                                    </a>
                                    <div>
                                        @if(count(\App\Models\Following::where('followerId',auth::user()->id)->where('followingId',$following->following->id)->get()) > 0)
                                        <button class="button-4 totyAdmin unfollow{{$following->id}}" >Un Follow</button>
                                        <input type="hidden" class="friendshipId{{$following->id}}" value="{{followingId($profileId->id,$following->following->id)}}">
                                        @endif
                                        <button class="button-4 totyAdmin "  ><a href="{{route('user.view.profile',['user_id'=>$following->following->id] )}}">View Profile</a> </button>

                                    </div>
                                </div>
                            </li>
                            @endforeach
                            @else
                                <center>
                                    <h2>There is no followings yet!</h2>

                                </center>
                            @endif
                        </ul>
                    </div>
                </section>
            </div>
            <div class="modal-footer">
                <button type="button" class="button-4 " data-dismiss="modal">Colse</button>

            </div>
        </div>
    </div>
</div>
<!--Followers-->
<div class="modal fade" id="followers" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="exampleModalLabel">My Followers List</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin: 0px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <!--            -->
            <div class="modal-body myULxx" >
                <section class="group-section " style="min-height:auto">
                    <div class="group-members my-3">
                        <ul class="members-list list-unstyled" >
                            @if(count($Myfollowers)  > 0)
                                @foreach($Myfollowers as $following)
                                    <li class="members-item" id="">
                                        <div class="group-member d-flex justify-content-between">
                                            <a href="#" class="group-member-link d-flex align-items-center">
                                                <img src="{{asset('assets/images/users/'.$following->following->personal_image)}}" alt="#" class="member-img img-fluid">
                                                <span class="d-inline-block group-member-link_span">
                                                <p class="user-name">{{$following->follower->name}}</p>
                                            </span>
                                            </a>
                                            <div>
                                                @if(count(\App\Models\Following::where('followingId',$profileId->id)->where('followerId',$following->follower->id)->get()) > 0)
{{--
                                                    <button class="button-4 totyAdmin unfollow{{$following->id}}" >Un Follow</button>
--}}
                                                    <input type="hidden" class="friendshipId{{$following->id}}" value="{{followingId($profileId->id,$following->follower->id)}}">
                                                @endif
                                                <button class="button-4 totyAdmin "  ><a href="{{route('user.view.profile',['user_id'=>$following->follower->id] )}}">View Profile</a> </button>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            @else
                                <center>
                                    <h2>There is no followings yet!</h2>

                                </center>
                            @endif
                        </ul>
                    </div>
                </section>
            </div>
            <div class="modal-footer">
                <button type="button" class="button-4 " data-dismiss="modal">Colse</button>

            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>


<script>
    function myFriends(){
        document.getElementById("myFriends").style.display = "block";
        document.getElementById("allFriend").style.display = "none";
        document.getElementById("addFriend").style.display = "none";
    }
    function myPending(){
        document.getElementById("myFriends").style.display = "none";
        document.getElementById("myPending").style.display = "block";
        document.getElementById("myRequests").style.display = "none";
        document.getElementById("addFriend").style.display = "none";

    }
    function myRequests(){
        document.getElementById("myFriends").style.display = "none";
        document.getElementById("myPending").style.display = "none";
        document.getElementById("myRequests").style.display = "block";
        document.getElementById("addFriend").style.display = "none";
    }
    function addFriend(){
        document.getElementById("myFriends").style.display = "none";
        document.getElementById("myPending").style.display = "none";
        document.getElementById("myRequests").style.display = "none";
        document.getElementById("addFriend").style.display = "block";
    }
    function myFunction() {
        var input, filter, ul, li, a, i, txtValue;
        input = document.getElementById("myInput");
        filter = input.value.toUpperCase();
        ul = document.getElementById("myUL");
        li = ul.getElementsByTagName("li");
        for (i = 0; i < li.length; i++) {
            a = li[i].getElementsByTagName("a")[0];
            txtValue = a.textContent || a.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                li[i].style.display = "";
            } else {
                li[i].style.display = "none";
            }
        }
    }
    @foreach($followings as $following)
    $(document).on('click','.refuse{{$following->id}}',function () {
        var friendshipId = document.getElementsByClassName('friendshipId{{$following->id}}')[0].value;
        alert(friendshipId);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{route('redeny.user.refuse.friend')}}",
            method: "post",
            data: {friendshipId: friendshipId, type: 'followId'},
            dataType: "script",
            success: function (data) {
                //$('#li'+userId).remove();
                $('.myULxx').html(data);

            },
            error: function (data) {
                alert("fail");
            }

        });
    });
    $(document).on('click','.cancel{{$following->id}}',function(){
        var friendshipId = document.getElementsByClassName('friendshipId{{$following->id}}')[0].value;
        //alert(friendshipId);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{route('redeny.user.refuse.friend')}}",
            method:"post",
            data:{friendshipId:friendshipId,type:'followId'},
            dataType: "text",
            success:function(data){
                //$('#li'+userId).remove();
                console.log(data);
                $('.myULxx').html(data);
            },
            error: function(){
                alert("fail");
            }

        });
    });
    $(document).on('click','.add{{$following->id}}',function () {
            var receiverId = document.getElementsByClassName('receiver{{$following->id}}')[0].value;
            var senderId = {{auth::user()->id}};
            //alert(receiverId);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('redeny.user.add.friend')}}",
                method: "post",
                data: {receiverId: receiverId, senderId: senderId},
                dataType: "text",
                success: function (data) {
                    $('.myULxx').html(data);

                },
                error: function (data) {
                    alert("fail");
                    console.log(data);
                }

            });
        });
    $(document).on('click','.accept{{$following->id}}',function () {
            var friendshipId = document.getElementsByClassName('friendshipId{{$following->id}}')[0].value;
            //alert(friendshipId);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('redeny.user.refuse.friend')}}",
                method: "post",
                data: {friendshipId: friendshipId, type: 'followId'},
                dataType: "text",
                success: function () {
                    //$('#li'+userId).remove();
                    $('.myULxx').html(data);

                },
                error: function () {
                    alert("fail");
                }

            });
        });
    $(document).on('click','.follow{{$following->id}}',function () {
        var receiverId = document.getElementsByClassName('receiver{{$following->id}}')[0].value;
        var senderId = {{auth::user()->id}};
        //alert(receiverId);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{route('redeny.user.follow.friend')}}",
            method: "post",
            data: {receiverId: receiverId, senderId: senderId},
            dataType: "text",
            success: function (data) {
                $('.myULxx').html(data);

            },
            error: function (data) {
                alert("fail");
                console.log(data);
            }

        });
    });
    $(document).on('click','.unfollow{{$following->id}}',function () {
        var friendshipId = document.getElementsByClassName('friendshipId{{$following->id}}')[0].value;
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{route('redeny.user.unfollow.friend')}}",
            method: "post",
            data: {friendshipId: friendshipId, type: 'followId'},
            dataType: "script",
            success: function (data) {
                //$('#li'+userId).remove();
                $('.myULxx').html(data);

            },
            error: function (data) {
                alert("fail");
            }

        });
    });
    @endforeach


</script>


