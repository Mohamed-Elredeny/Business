<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\models\Following;
use App\models\Friendship;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FriendshipController extends Controller
{
    #region Check
    public $valid_token;
    public $user_verified;
    public function __construct(){
        if(auth('api')->user()){
            $this->valid_token =1;
            $this->user = auth('api')->user();
            $this->user_verified = $this->user['email_verified_at'];
        }else{
            $this->valid_token =0;
        }
    }
    public function unValidToken($state){
        if($state == 0){
            return $this->returnError(404, 'Token is invalid, User is not authenticated');
        }
    }
    public function unVerified($state){
        if($state == null){
            return $this->returnError(404, 'User is not verified check your email');
        }
    }
    #endregion
    use GeneralTrait;
    public function friendship(Request $request)
    {
        if($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        }else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $requestType = $request->requestType;
            switch ($requestType) {
                case 'addFriendRequest':
                    //Iam the sender
                    $senderId = $this->user->id;
                    $receiverId = $request->receiverId;
                    $state = 2;
                    $is_there_relation = Friendship::where('senderId',$senderId)->where('receiverId',$receiverId)->get();
                    if(count($is_there_relation) > 0){
                        $data = [
                           'msg'=> 'You have sent your request successfully'
                        ];
                        return $this->returnSuccessMessageWithStatusLikes($data, 200,false);

                    }else{
                        $friendRequest = Friendship::create([
                            'senderId' => $senderId,
                            'receiverId' => $receiverId,
                            'stateId' => $state,
                        ]);
                    }

                    //Sender is the follower
                    //Receiver is the following
                    $follower = $senderId;
                    $following = $receiverId;
                    $followingRequest = Following::create([
                        'followerId' => $follower,
                        'followingId' => $following,
                    ]);
                    $msg = 'You have sent your request successfully';
                    $data = [
                        'msg'=>$msg,
                        'friendship'=>$friendRequest,
                        'following'=>$followingRequest
                    ];
                    return $this->returnSuccessMessageWithStatusLikes($data, 200,true);
                case 'acceptFriendRequest':
                    // Iam the receiver
                    $friendshipId = $request->friendshipId;
                    $friendshipRecord = Friendship::find($friendshipId);
                    $senderId = $friendshipRecord->senderId;
                    $receiverId = $friendshipRecord->receiverId;
                    //Receiver is the follower
                    //Sender is the following
                    $follower = $receiverId;
                    $following = $senderId;
                    $followingRequest = Following::create([
                        'followerId' => $follower,
                        'followingId' => $following,
                    ]);
                    $friendshipRecord->stateId = 1;
                    $friendshipRecord->save();
                    $msg = 'You have accepted the request successfully';
                    return $this->returnSuccessMessage($msg, 200);
                case 'refuseFriendRequest':
                    $friendshipId = $request->friendshipId;
                    $friendshipRecord = Friendship::destroy($friendshipId);
                    $msg = 'You have refused the request successfully';
                    return $this->returnSuccessMessage($msg, 200);
                case 'sentRequest':
                    $senderId = $request->senderId;
                    $friendshipRecord = Friendship::where('senderId',$senderId)->get();
                    $msg = 'sentRequest';
                    return $this->returnData(['sentRequest'], [$friendshipRecord], 'sentRequest');
                case 'receivedRequest':
                    $receiverId = $request->receiverId;
                    $friendshipRecord = Friendship::where('receiverId',$receiverId)->get();
                    $msg = 'receivedRequest';
                    return $this->returnData(['receivedRequest'], [$friendshipRecord], 'receivedRequests');
            }
        }
        // return 'hi';
    }
    private function showFriendsToInvite(){

    }
    public function follow(Request $request)
    {
        if ($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        } else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $auth_user = $this->user->id;
            $followingId = $request->followingId;
            $following = DB::table('following')->insert([
                'followerId' => $auth_user,
                'followingId' => $followingId
            ]);
            return $this->returnSuccessMessage('you are now following this user', 200);
        }
    }
    public function unfollow(Request $request)
    {
        if ($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        } else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $auth_user = $this->user->id;
            $followingId = intval($request->followingId);
            //following
            //followerId
            //followingId
            $following = Following::where('followerId', $auth_user)->where('followingId', $followingId)->get();
            foreach ($following as $follow) {
                $follow->delete();
            }
            return $this->returnSuccessMessage('you are now unfollowing this user', 200);
        }

    }
}
