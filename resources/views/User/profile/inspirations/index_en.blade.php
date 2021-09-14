<?php

use App\Models\Following;
use App\models\Friendship;
use App\Models\Inspiration;use App\Models\UserMusic;

function CheckUserFriendshipState($user, $enemy){
    //Different users
    //1. User => From token
    //2. Enemy=> The person i want to check my friendship with
    /*
     *
     */
    #region different States
    //Friend


    //pending login => request  cancel request
    //cancel
    //accepted => cancel request
    //guest  => add request


    $friendship = Friendship::where('senderId',$user)->where('receiverId',$enemy)->get();
    if(count($friendship) > 0){
        switch($friendship[0]->stateId){
            case '2':
                return 'pending';
            case '1':
                return 'accepted';
        }
    }else {
        $friendship = Friendship::where('senderId', $enemy)->where('receiverId', $user)->get();
        if (count($friendship) > 0) {
            switch($friendship[0]->stateId){
                case '2':
                    return 'cancel';
                case '1':
                    return 'accepted';
            }
        }
    }

    return 'guest';

    //Guest
    //Didn't accept my request
    //i didn't accept his request
    #endregion
}
function CheckUserFollowingState($user, $enemy)
{
    ////Following => Enemy
    ////Follower  => User
    $following = Following::where('followerId', $user)->where('followingId', $enemy)->get();
    if (count($following) > 0) {
        return 1;
    } else {
        return 0;
    }
}
function CheckInspiration($user,$enemy){
    $records = Inspiration::where('user_id',$user)->where('inspirerende_id',$enemy)->get();
    if(count($records) > 0 ){
        return 1;
    }else{
        return 0;
    }
}
function friendshipId($user, $enemy){
    $friendship = Friendship::where('senderId',$user)->where('receiverId',$enemy)->get();
    if(count($friendship)>0){
        return $friendship[0]->id;
    }else{
        $friendship = Friendship::where('senderId',$enemy)->where('receiverId',$user)->get();
        if(count($friendship)>0){
            return $friendship[0]->id;
        }
    }
    return 99;

}
function followingId($user, $enemy){
    $friendship = Following::where('followerId',$user)->where('followingId',$enemy)->get();
    if(count($friendship)>0){
        return $friendship[0]->id;
    }else{
        $friendship = Following::where('followingId',$enemy)->where('followerId',$user)->get();
        if(count($friendship)>0){
            return $friendship[0]->id;
        }else{
            return  99;
        }
    }
}
function inspirationId($user, $enemy){
    $records =  Inspiration::where('user_id',$user)->where('inspirerende_id',$enemy)->get();
    if(count($records)>0){
        return $records[0]->id;
    }else{
        return 99;
    }
}

?>
@extends('layouts.profile')
<style>

    .a_redo_link a {
        text-decoration: none;
    }
</style>
@section('content')

    {{--Musics--}}
    <section id="ez-body__center-content-music" class="col-lg-10 row profile-center-content">
        <div class="col-lg-12 profile-center-content">
            <div class="row">
                <div class="col-lg-12">
                    <input type="text" class="search-box" placeholder="Search about your inspirations" id="search" onkeyup="myFunction()" >

                    <section class="group-section px-3" id="renderMusic">


                                <div  id="my-musics" class=" tabcontent target" >
                                            <div  class="row  ">
                                                @foreach($inspirations['myInspiration'] as $oneAllMusic)
                                                    <div class="music col-sm-4 ">
                                                        <form action="{{route('redeny.user.list.inspiration')}}" method="post" style="display:inline;" class="a_redo_link">
                                                            @csrf
                                                            <img src="{{asset('assets/images/users/'.$oneAllMusic->music->personal_image)}}" alt="" style="height:250px;width: 100%">
                                                            <input type="hidden" name="relation" id="relation" value="{{$oneAllMusic->id}}">
                                                            <input type="hidden" name="musicId" id="musicId" value="{{$oneAllMusic->music->id}}">
                                                            <center>
                                                                <h2>
                                                                    {{$oneAllMusic->music->name}}
                                                                </h2>
                                                                @if($myProfile == 1)
                                                                    <input type="hidden" id="state" name="state" value="1">
                                                                    <button  class="button-4 col-5  mx-auto tablinks ">Remove </button>
                                                                <a  class="button-4 col-5  mx-auto   " href="{{route('user.view.profile',['user_id'=>$oneAllMusic->music->id])}}">View Profile </a>
                                                                @else
                                                                    <?php $inMyCokkection = \App\Models\UserInspiration::where('user_id',auth::user()->id)->where('inspirerende_id',$oneAllMusic->music->id)->get() ?>
                                                                    @if(count($inMyCokkection) > 0)
                                                                        <input type="hidden" id="state"  name="state"  value="1">
                                                                            <button  class="button-4 col-5  mx-auto tablinks" type="submit">Remove </button>
                                                                            <a  class="button-4 col-5  mx-auto   " href="{{route('user.view.profile',['user_id'=>$oneAllMusic->music->id])}}">View Profile </a>
                                                                        @else
                                                                        <input type="hidden" id="state"  name="state"  value="0">
                                                                            <button  class="button-4 col-5  mx-auto tablinks" type="submit">Add To List </button>
                                                                            <a  class="button-4 col-5  mx-auto   " href="{{route('user.view.profile',['user_id'=>$oneAllMusic->music->id])}}">View Profile </a>
                                                                        @endif
                                                                @endif
                                                                <br><br>
                                                            </center>
                                                        </form>
                                                    </div>
                                                @endforeach
                                            </div>

                                        </div>





                    </section>
                </div>
                {{-- @include('User.groups.related') --}}
            </div>
        </div>
    </section>
    {{--End Of Musics--}}
@endsection
@include('User.profile.models.friends')
@include('User.profile.scripts.main')
@include('User.profile.scripts.friendFollowIns')
@include('User.profile.scripts.musics')
