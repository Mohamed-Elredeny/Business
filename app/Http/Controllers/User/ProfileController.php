<?php
namespace App\Http\Controllers\User;

use App\Http\Requests\ProfileRequest;
use App\Http\Requests\PasswordRequest;
use App\Models\Following;
use App\Models\Friendship;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\User;

class ProfileController extends Controller
{
    protected $myProfile;
    public function index($user_id){
        $MyId =  Auth::user()->id;
        if($MyId == $user_id){
            $this->myProfile =1;
        }else{
            $this->myProfile =0;
        }
        $myProfile = $this->myProfile;
        $profileId =User::find($user_id);
        $friends = $this->GetFirends($user_id);
        $firendship_state = $this->CheckUserFriendshipState($MyId,$user_id);
        $following_state = $this->CheckUserFollowingState($MyId,$user_id);
        return view('User.profile.index_en',compact('myProfile','profileId','friends','firendship_state','following_state'));
    }
    ///Get All Friends
    public function GetFirends($user_id){
        $friend_list = Friendship::where('senderId',$user_id)->orWhere('receiverId',$user_id)->where('stateId',1)->get();
        return $friend_list;
    }
    public function CheckUserFriendshipState($user,$enemy){
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
            }else{
                return 'guest';
            }
        }
        //Guest
        //Didn't accept my request
        //i didn't accept his request
        #endregion
    }
    public function CheckUserFollowingState($user,$enemy){
        ////Following => Enemy
        ////Follower  => User
        $following = Following::where('followerId',$user)->where('followingId',$enemy)->get();
        if(count($following) > 0){
            return 1;
        }else{
            return 0;
        }
    }

    public function edit()
    {
        return view('private.admin.profile');
    }

    /**
     * Update the profile
     *
     * @param  \App\Http\Requests\ProfileRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProfileRequest $request)
    {
        auth()->user()->update($request->all());

        return back()->withStatus(__('Profile successfully updated.'));
    }

    /**
     * Change the password
     *
     * @param  \App\Http\Requests\PasswordRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function password(PasswordRequest $request)
    {
        auth()->user()->update(['password' => Hash::make($request->get('password'))]);

        return back()->withPasswordStatus(__('Password successfully updated.'));
    }

}
