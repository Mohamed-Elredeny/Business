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
    ul {
        list-style-type: none;
    }
    a:link {
        color: black;
    }

    a:visited {
        color: black;
    }

    a:hover {
        color: black;

    }

    a:active {
        color: black;
    }

</style>
@section('content')

    {{--Musics--}}
    <section id="ez-body__center-content-music" class="col-lg-10 row profile-center-content">
        <div class="col-lg-12 profile-center-content">
            <div class="row">
                <div class="col-lg-12">
                    <input type="text" class="search-box" placeholder="Search about your music" id="search" value="Videos" disabled>

                    <section class="group-section px-3" id="renderMusic">
                        <div class="group-mygroups py-3">


                            <div id="all-musics" class=" tabcontent" >
                                <div  class="row ">
                                    @foreach($videos as $oneAllMusic)
                                        <div class="music col-sm-3 target ">
                                            <video  controls style="height: 200px;width: 95%">
                                                <source src="{{asset('media/'.$oneAllMusic->filename)}}" type="video/mp4">
                                            </video>
                                            <br> <br>
                                            <input type="hidden" id="state" value="0">
                                            <input type="hidden" name="musicId" id="musicId" value="{{$oneAllMusic->id}}">
                                        </div>

                                    @endforeach
                                </div>
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
