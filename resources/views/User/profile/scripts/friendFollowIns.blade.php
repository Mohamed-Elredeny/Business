<script>
    $(document).on('click','.addfirend',function () {
        var receiverId = document.getElementsByClassName('receiverFriend')[0].value;
        var senderId = {{auth::user()->id}};
        //alert(receiverId);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{route('redeny.user.add.friend.profile')}}",
            method: "post",
            data: {receiverId: receiverId, senderId: senderId},
            dataType: "text",
            success: function (data) {
                $('.profileButtonsDiv').html(data);

            },
            error: function (data) {
                alert("fail");
                console.log(data);
            }

        });
    });//ok
    $(document).on('click','.cancelrequest',function () {
        var friendshipId = document.getElementsByClassName('friendshipId')[0].value;
        var receiverFriend = document.getElementsByClassName('receiverFriend')[0].value;
        //alert(receiverFriend + ' ' + friendshipId);
        //alert(friendshipId);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{route('redeny.user.refuse.friend.profile')}}",
            method:"post",
            data:{friendshipId:friendshipId,receiverFriend:receiverFriend},
            dataType: "text",
            success:function(data){
                //$('#li'+userId).remove();
                $('.profileButtonsDiv').html(data);
            },
            error: function(data){
                alert("fail");
                console.log(data);

            }

        });
    });//ok
    $(document).on('click','.acceptfriend',function () {
        var friendshipId = document.getElementsByClassName('friendshipId')[0].value;
        //alert(friendshipId);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{route('redeny.user.accept.friend.profile')}}",
            method: "post",
            data: {friendshipId: friendshipId, type: 'followId'},
            dataType: "text",
            success: function (data) {
                //$('#li'+userId).remove();
                $('.profileButtonsDiv').html(data);
            },
            error: function () {
                alert("fail");
            }

        });
    });//ok
    $(document).on('click','.followfriend',function () {
        var receiverId = document.getElementsByClassName('receiverFriend')[0].value;
        var senderId = {{auth::user()->id}};
        // alert(receiverId + ' ' + senderId);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{route('redeny.user.follow.friend.profile')}}",
            method: "post",
            data: {receiverId: receiverId, senderId: senderId},
            dataType: "text",
            success: function (data) {
                $('.profileButtonsDiv').html(data);
            },
            error: function (data) {
                alert("fail");
                console.log(data);
            }

        });
    });//ok
    $(document).on('click','.unfollowfriend',function () {
        var friendshipId = document.getElementsByClassName('followingId')[0].value;
        var receiverFriend = document.getElementsByClassName('receiverFriend')[0].value;
        // alert(receiverFriend + ' ' + friendshipId);

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{route('redeny.user.unfollow.friend.profile')}}",
            method: "post",
            data: {friendshipId: friendshipId,receiverFriend:receiverFriend},
            dataType: "script",
            success: function (data) {
                //$('#li'+userId).remove();
                $('.profileButtonsDiv').html(data);
            },
            error: function (data) {
                alert("fail");
                console.log(data);
            }

        });
    });//ok



    $(document).on('click','.removeinspiration',function () {
        var friendshipId = document.getElementsByClassName('inspirationId')[0].value;
        // alert(friendshipId);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{route('redeny.user.remove.inspiration.profile')}}",
            method:"post",
            data:{friendshipId:friendshipId,type:'followId'},
            dataType: "text",
            success:function(data){
                //$('#li'+userId).remove();
                //console.log(data);
                $('.profileButtonsDiv').html(data);
            },
            error: function(){
                alert("fail");
            }

        });
    });//ok
    $(document).on('click','.addinspiration',function () {
        var receiverId = document.getElementsByClassName('receiverFriend')[0].value;
        var senderId = {{auth::user()->id}};
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{route('redeny.user.add.inspiration.profile')}}",
            method:"post",
            data:{receiverId:receiverId,senderId:senderId},
            dataType: "text",
            success:function(data){
                //$('#li'+userId).remove();
                //console.log(data);
                $('.profileButtonsDiv').html(data);
            },
            error: function(data){
                alert("fail");
                console.log(data);
            }

        });
    });//ok


</script>
