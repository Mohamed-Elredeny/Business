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
    {{--Profile--}}
    <section id="ez-body__center-content-profile" class="col-lg-10 row profile-center-content">
        <div class="col-lg-12 profile-center-content">
            <center>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

            </center>

            <div class="profile-cover">
                <img
                    src="{{asset('assets/images/users/'.$profileId->cover_image)}}"
                    alt="" width="100%" height="400px" />
            </div>
            <div class="people p-2 mt-2">
                <div class="people-info d-flex col-sm-5">
                    <img class="profile-figure rounded-circle"
                         src="{{asset('assets/images/users/'.$profileId->personal_image)}}"
                         alt="User Profile Pic" />
                    <div class="d-flex flex-column align-items-center text-center">
                        <p><b>{{$profileId->name}}</b></p>
                        <?php $profileFollowers = Following::where('followingId',$profileId->id)->get(); ?>
                        <p>{{count($profileFollowers)}} Follower</p>
                    </div>
                </div>
            <!--   My Profile  -->
            <!--   Any Profile  -->
                @if($myProfile != 1)
                <div class="col-sm-9 profileButtonsDiv" >
                    <input type="hidden" class="receiverFriend" value="{{$profileId->id}}">
                    @if(CheckUserFollowingState(Auth::user()->id,$profileId->id) == 1)
                        <input type="hidden" class="followingId" value="{{followingId(Auth::user()->id,$profileId->id)}}">
                        <button class="button-4 totyAdmin unfollowfriend" style="width: 180px;">Un Follow</button>
                    @else
                        <button class="button-4 totyAdmin followfriend" style="width: 180px;">Follow</button>
                    @endif
                    @if(CheckUserFriendshipState(Auth::user()->id,$profileId->id) == 'guest' )
                        <button style="width: 180px;" class="button-4 totyAdmin addfirend" >Add Friend </button>
                    @elseif(CheckUserFriendshipState(Auth::user()->id,$profileId->id) == 'pending' )
                            <input type="hidden" class="friendshipId" value="{{friendshipId(Auth::user()->id,$profileId->id)}}">
                            <button style="width: 180px;" class="button-4 totyAdmin cancelrequest">Cancel Request </button>
                    @elseif(CheckUserFriendshipState(Auth::user()->id,$profileId->id) == 'cancel' )
                            <input type="hidden" class="friendshipId" value="{{friendshipId(Auth::user()->id,$profileId->id)}}">
                            <button style="width: 150px;" class="button-4 totyAdmin cancelrequest">Refuse Request </button>
                            <button style="width: 150px;" class="button-4 totyAdmin acceptfriend">Accept Request </button>
                    @elseif(CheckUserFriendshipState(Auth::user()->id,$profileId->id) == 'accepted' )
                            <input type="hidden" class="friendshipId" value="{{friendshipId(Auth::user()->id,$profileId->id)}}">
                            <button style="width: 180px;" class="button-4 totyAdmin cancelrequest">Remove Friend </button>
                    @endif
                    @if(CheckInspiration(Auth::user()->id,$profileId->id)== 1)
                        <input type="hidden" class="inspirationId" value="{{inspirationId(Auth::user()->id,$profileId->id)}}">
                        <button class="button-4 totyAdmin removeinspiration "  style="width: 180px;">Remove inspiration</button>
                    @else
                        <button class="button-4 totyAdmin addinspiration"  style="width: 180px;">Add inspiration</button>
                    @endif
                </div>
                @endif

            </div>

            <div class="add-post-container d-flex justify-content-between align-items-center">
                <div class="add-post mt-3" style="width: 85%">
                    <!-- Add Post Modal -->
                    <div class="modal fade" id="add-post-modal" tabindex="-1" aria-labelledby="exampleModalLabel"
                         aria-hidden="true">
                        <div class="modal-dialog" style="margin-top: 22vh">
                            <div class="modal-content">
                                <div class="modal-header d-flex justify-content-between">
                                    <span></span>
                                    <h5 class="modal-title" id="exampleModalLabel">
                                        Add Post
                                    </h5>
                                    <button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form action="" class="container" enctype="multipart/form-data">
                                        <!-- Select Post Type -->
                                        <div class="post-type d-flex justify-content-between align-items-center m-auto w-75">
                                            <div>Post As:</div>
                                            <div class="d-flex align-items-center">
                                                <input type="radio" name="post-type" value="post" id="post" />
                                                <span class="pl-2">Post</span>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <input class="m-0" type="radio" name="post-type" value="service" id="service" />
                                                <span class="pl-2">Service</span>
                                            </div>
                                        </div>
                                        <!-- Select post Privacy -->
                                        <div class="post-privacy d-flex justify-content-between align-items-center m-auto w-75">
                                            <label for="cars">Choose Post Privacy:</label>
                                            <select id="post-privacy" name="privacy">
                                                <option value="volvo">Volvo</option>
                                                <option value="saab">Saab</option>
                                                <option value="fiat">Fiat</option>
                                                <option value="audi">Audi</option>
                                            </select>
                                        </div>
                                        <!-- Select post Category -->
                                        <div class="post-category d-flex justify-content-between align-items-center m-auto w-75">
                                            <label for="cars">Choose A Category:</label>
                                            <select id="post-category" name="category">
                                                <option value="volvo">Volvo</option>
                                                <option value="saab">Saab</option>
                                                <option value="fiat">Fiat</option>
                                                <option value="audi">Audi</option>
                                            </select>
                                        </div>
                                        <!-- Post Desc -->
                                        <div class="post-desc d-flex justify-content-center mt-2">
                          <textarea name="post-text" id="post-text" cols="200" rows="4"
                                    placeholder="Start Typing..."></textarea>
                                        </div>
                                        <!-- Post Images -->
                                        <div class="post-desc d-flex justify-content-center mt-2">
                                            <input class="form-control w-75 mt-2" type="file" name="imgs" id="imgs" accept="image/*"
                                                   multiple />
                                        </div>
                                        <!-- Add Post Btn -->
                                        <div class="post-add-btn d-flex justify-content-center mt-4">
                                            <button type="button" class="btn btn-secondary btn-block w-75" data-dismiss="modal">
                                                Save
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="text" placeholder="Add New Post" class="w-100" data-toggle="modal"
                           data-target="#add-post-modal" />
                </div>

                @if($myProfile == 1)
                <div class="settings d-flex justify-content-center align-items-center mt-3" data-toggle="modal"
                     data-target="#settingsModal">
                    <i class="fas fa-cogs"></i>
                </div>
                @endif
            </div>
            <!-- Settings Modal -->
            <div class="modal fade" id="settingsModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                 aria-hidden="true" style="margin-top: 2vh;">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header" style="border: none;">
                            <h5 class="modal-title" id="exampleModalLabel">Edit Profile</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form class="h5">
                                <div class="cover-pic w-100">
                                    <img class="w-100"
                                         src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80"
                                         alt="User Profile Pic" height="150" />
                                    <label for="upload-cover-pic"><i class="fas fa-camera"></i></label>
                                    <input class="d-none" type="file" name="cover-pic" id="upload-cover-pic">
                                </div>
                                <div class="profile-pic d-flex justify-content-center align-items-end w-100 pb-4">
                                    <img class="profile-figure rounded-circle"
                                         src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixid=MXwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHw%3D&ixlib=rb-1.2.1&auto=format&fit=crop&w=1050&q=80"
                                         alt="User Profile Pic" width="150" height="150" />
                                    <label for="upload-profile-pic"><i class="fas fa-camera"></i></label>
                                    <input class="d-none" type="file" name="profile-pic" id="upload-profile-pic">
                                </div>
                                <div class="form-group">
                                        Name
                                        <input type="text" class="form-control" placeholder="name" name="name">
                                </div>
                                <div class="form-group">
                                    User Name
                                    <input type="text" class="form-control" placeholder="username" name="username">
                                </div>
                                <div class="form-group">
                                    Email
                                    <input type="email" class="form-control" placeholder="email" name="email">
                                </div>
                                <div class="form-group">
                                    Password
                                    <input type="password" class="form-control" placeholder="password" name="password">
                                </div>


                                <div class="form-group">
                                    Birth Date
                                    <input type="date" class="form-control" placeholder="password" name="password">
                                </div>

                                <div class="form-group">
                                    Gender
                                    <select type="date" class="form-control" placeholder="password" name="password">
                                        <option value="male">male</option>
                                        <option value="female">female</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    PhoneNumber
                                    <input type="number" class="form-control" placeholder="Mobile">
                                </div>
                                <div class="form-group">
                                    jobTitle
                                    <input type="number" class="form-control" placeholder="Mobile">
                                </div>
                                <div class="form-group">
                                    Specialty
                                    <input type="number" class="form-control" placeholder="Mobile">
                                </div>
                                <div class="form-group">
                                    businessType
                                    <input type="number" class="form-control" placeholder="Mobile">
                                </div>
                                <div class="form-group">
                                    Country
                                    <select class="form-control">
                                        <option selected>Country</option>
                                        <option>2</option>
                                        <option>3</option>
                                        <option>4</option>
                                        <option>5</option>
                                    </select>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        City
                                        <select class="form-control">
                                            <option selected>City</option>
                                            <option>2</option>
                                            <option>3</option>
                                            <option>4</option>
                                            <option>5</option>
                                        </select>
                                    </div>
                                </div>
                                <br>

                                <div class="form-group upload-cover d-flex justify-content-between align-items-center ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            Profile Image
                                        </div>
                                        <div class="col-sm-6">
                                            <input  type="file" name="cover-pic" placeholder="image">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group upload-cover d-flex justify-content-between align-items-center ">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            Cover Image
                                        </div>
                                        <div class="col-sm-6">
                                            <input  type="file" name="cover-pic" placeholder="image">
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer" style="border: none;">
                                    <button type="button" class="btn btn-warning mt-2 b-block w-100 text-white">Save changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="section-navigator d-flex justify-content-between mt-3">
                <button class="btn btn-warning">posts</button>
                <button class="btn btn-light">Services</button>
            </div>
            <div id="app">
                <!-- posts goes here -->
            </div>
        </div>
    </section>
    {{--End Of Profile--}}

@endsection
@include('User.profile.models.friends')
@include('User.profile.scripts.main')
@include('User.profile.scripts.friendFollowIns')
@include('User.profile.scripts.musics')
