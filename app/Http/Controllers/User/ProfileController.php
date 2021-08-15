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
        $unRelatedUsersIds = $this->getUnRelatedUsersIds($user_id);

        $myProfile = $this->myProfile;
        $profileId =User::find($user_id);
        $friends = $this->GetFirends($user_id);
        $firendship_state = $this->CheckUserFriendshipState($MyId,$user_id);
        $following_state = $this->CheckUserFollowingState($MyId,$user_id);
        $pending_send = $this->GetPendingSend($user_id);
        $pending_receive = $this->GetPendingReceive($user_id);
        return view('User.profile.index_en',compact('unRelatedUsersIds','myProfile','profileId','friends','pending_send','pending_receive','firendship_state','following_state'));
    }
    ///Get All Friends
    public function GetFirends($user_id){
        $friend_list = Friendship::where('senderId',$user_id)->orWhere('receiverId',$user_id)->where('stateId',1)->get();
        return $friend_list;
    }
    public function GetAllRelatedUsersSender($user_id){
        $friend_list = Friendship::where('senderId',$user_id)->get();
        return $friend_list;
    }
    public function GetAllRelatedUsersReceiver($user_id){
        $friend_list = Friendship::where('receiverId',$user_id)->get();
        return $friend_list;
    }
    public function GetPendingSend($user_id){
        $friend_list = Friendship::where('senderId',$user_id)->where('stateId',2)->get();
        return $friend_list;
    }
    public function GetPendingReceive($user_id){
        $friend_list = Friendship::where('receiverId',$user_id)->where('stateId',2)->get();
        return $friend_list;
    }
    public function getUnRelatedUsersIds($user_id){
        $system_users = User::get();
        $ids =[];
        $unrealed =[];
        //Sender => Receivers
        $GetAllRelatedUsersSender = $this->GetAllRelatedUsersSender($user_id);
        foreach($GetAllRelatedUsersSender as $sender){
           $ids [] =  $sender->receiverId;
        }
        //Receivers => Senders
        $GetAllRelatedUsersReceiver = $this->GetAllRelatedUsersReceiver($user_id);
        foreach($GetAllRelatedUsersReceiver as $receiver){
            $ids [] =  $receiver->senderId;
        }

        foreach ($system_users as $user) {
            if (!in_array( $user->id , $ids)) {
                $unrealed [] = $user;
            }

        }
        return $unrealed;
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
