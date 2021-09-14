<script>
    $(document).on('click','.uploadMusic',function () {
        var musicName = document.getElementById('musicName').value;
        var musicCover = document.getElementById('musicCover').value;
        var music = document.getElementById('music').value;
        alert(musicName + ' ' + musicCover + ' '+music);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{route('redeny.user.add.music')}}",
            method:"post",
            data:{musicName:musicName,musicCover:musicCover,music:music},
            dataType: "text",
            success:function(){
                //$('#li'+userId).remove();
                //console.log(data);
                //$('.profileButtonsDiv').html(data);
                alert('3a4');
            },
            error: function(){
                alert("fail");
                //console.log(data);
            }

        });
    });//ok
    @foreach($musics['allMusics'] as $oneMusic)
    $(document).on('click','#addRemoveMusic{{$oneMusic->id}}',function (e) {
        event.stopPropagation();
        var musicId= {{$oneMusic->id}};
        var userId = {{Auth::user()->id}};
        var state = document.getElementById('state'+{{$oneMusic->id}}).value;
        alert(musicId + ' ' + userId + ' '+state);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url:"{{route('redeny.user.add.music')}}",
            method:"post",
            data:{musicId:musicId,userId:userId,state:state},
            dataType: "text",
            success:function(data){
                //$('#li'+userId).remove();
                //console.log(data);
                $('#renderMusic').html(data);
            },
            error: function(){
                alert("fail");
                //console.log(data);
            }

        });
    });//ok
    @endforeach

</script>
