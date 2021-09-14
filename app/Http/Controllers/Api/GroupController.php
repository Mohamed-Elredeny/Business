<?php
namespace App\Http\Controllers\Api;
use App\models\Friendship;
use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\models\Category;
use App\models\Comment;
use App\models\Following;
use App\models\Group;
use App\models\GroupMember;
use App\models\Likes;
use App\models\Media;
use App\models\Post;
use App\Models\Privacy;
use App\models\React;
use App\models\Report;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Object_;

class GroupController extends Controller
{
    #region Check
    public $valid_token;
    public $user_verified;
    public $user;
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
    //Normal User
    //1. Add Group
    public function addGroup(Request $request){
        if($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        }else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $lang = 'ar';
            $profile_image = time().'.'.$request->profile_image->extension();
            $request->profile_image->move(public_path('assets/images/groups'), $profile_image);

            $cover_image = time().'.'.$request->cover_image->extension();
            $request->cover_image->move(public_path('assets/images/groups'), $cover_image);
            $user_id = User::where('remember_token',$request->token)->get();
            $name = $request->name;
            $description = $request->description;
            $category_id  = $request->category_id;
            $publisher_id  = $this->user->id;
            $rules = $request->rules;
            $privacy = $request->privacy;

            $group= Group::create([
                'name'=>$name,
                'description'=>$description,
                'category_id'=>$category_id,
                'publisher_id'=>$publisher_id,
                'profile_image'=>$profile_image,
                'cover_image'=>$cover_image,
                'rules'=>$rules,
                'privacy'=>$privacy
            ]);
            $group_admin = DB::table('group_members')->insert([
                'group_id' => $group->id,
                'user_id' => $publisher_id,
                'state' => 1,
                'isAdmin'=>1
            ]);


            #region groupdata


            if ($group) {
                $url_profile_image = $group['profile_image'];
                $url_cover_image = $group['cover_image'];
                $group['profile_image'] = 'https://businesskalied.com/api/business/public/assets/images/groups/'.$url_profile_image;
                $group['cover_image'] = 'https://businesskalied.com/api/business/public/assets/images/groups/'.$url_cover_image;
                $group['members_count'] = $this->membersGroup($group->id);
                $group->privacy = intval($group->privacy);
                $user_group = DB::table('group_members')
                    ->where([['group_id',$group->id],['user_id',$this->user->id]])
                    ->first();
                if ($user_group) {
                    $group['entered'] = intval($user_group->state);
                }
                else{
                    $group['entered'] = 0;
                }
                unset(
                    $group->created_at,
                    $group->updated_at,
                    $group->category_id,
                    $group->publisher_id,
                    $group->cover_image,
                    $group->description,
                    $group->rules
                );

                //return $this->returnData(['groups','count'],[$groups,$count]);

                return $this->returnDataWithStatus(['group'], [$group], true,'group details');
            }
            return $this->returnDataWithStatus(['group'], [[]], false,'group details');

        }

    }

    public function updateGroup(Request $request){
        if($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        }else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $group_id = $request->group_id;
            $group = Group::find($group_id);
            if(isset($request->profile_image)){
                $profile_image = time().'.'.$request->profile_image->extension();
                $request->profile_image->move(public_path('assets/images/groups'), $profile_image);
            }else{
                $profile_image = $group->profile_image;
            }
            if(isset($request->cover_image)) {
                $cover_image = time() . '.' . $request->cover_image->extension();
                $request->cover_image->move(public_path('assets/images/groups'), $cover_image);
            }else{
                $cover_image  =$group->cover_image;
            }
            if(isset($request->name)){
                $name = $request->name;
            }else{
                $name = $group->name;
            }

            if(isset($request->description)) {
                $description = $request->description;
            }else{
                $description = $group->description;
            }
            if(isset($request->category_id)){
                $category_id  = $request->category_id;
            }else{
                $category_id  = $group->category_id;
            }
            if(isset($request->publisher_id)){
                $publisher_id  = $request->publisher_id;
            }else{
                $publisher_id  = $group->publisher_id;
            }
            if(isset($request->rules )){
                $rules = $request->rules;
            }else{
                $rules = $group->rules;
            }
            if(isset($request->privacy)){
                $privacy = $request->privacy;
            }else{
                $privacy = $group->privacy;
            }

            $group->update([
                'name'=>$name,
                'description'=>$description,
                'category_id'=>$category_id,
                'publisher_id'=>$publisher_id,
                'profile_image'=>$profile_image,
                'cover_image'=>$cover_image,
                'rules'=>$rules,
                'privacy'=>$privacy
            ]);
            $msg = 'group updated successfully';
            return $this->returnSuccessMessage($msg,200);
        }
    }
    public function removeGroup(Request $request){
        $group_id =$request->group_id;
        //Posts
        //Media
        $group = Group::find($group_id);
        $posts  = Post::where('group_id',$group_id)->get();
        if(count($posts) > 0) {
            foreach ($posts as $post) {
                $post_media = Media::where('model_type','post')->where('model_id',$post->id)->get();
                foreach($post_media as $media) {
                    $file_pointer = $media->filename;
                    /*if (!unlink($file_pointer)) {
                        echo("$file_pointer cannot be deleted due to an error");
                    } else {
                        echo 'sad';
                    }*/
                    $media->delete();

                }
                $post->delete();
            }
        }
        $group->delete();
        return $this->returnSuccessMessageWithStatus('Group has been successfully deleted',200,true);
    }
    //2. View all groups
    //3. View my groups
    // To see my groups you have to see pending and accepted groups
    public function getGroups(Request $request,$flag) {
        if($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        }else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $token = $request->token;
            /*            $user = User::where('remember_token',$token)->first();*/
            if($flag == 0) {
                $groups = Group::all();
                $count = $groups->count();
                foreach ($groups as $group) {
                    $url_profile_image = $group['profile_image'];
                    $url_cover_image = $group['cover_image'];
                    $group['profile_image'] = 'https://businesskalied.com/api/business/public/assets/images/groups/'.$url_profile_image;
                    $group['cover_image'] = 'https://businesskalied.com/api/business/public/assets/images/groups/'.$url_cover_image;
                    $group['members_count'] = $this->membersGroup($group->id);
                    $group->privacy = intval($group->privacy);
                    $user_group = DB::table('group_members')
                        ->where([['group_id',$group->id],['user_id',$this->user->id]])
                        ->first();
                    if ($user_group) {
                        $group['entered'] = intval($user_group->state);
                    }
                    else{
                        $group['entered'] = 0;
                    }
                    unset(
                        $group->created_at,
                        $group->updated_at,
                        $group->category_id,
                        $group->publisher_id,
                        $group->cover_image,
                        $group->description,
                        $group->rules
                    );


                }
                return $this->returnData(['groups','count'],[$groups,$count]);

            }
            else{

                $groups = DB::select(DB::raw('select groups.* from groups,group_members
                        where group_members.group_id = groups.id
                        AND group_members.user_id ='.$this->user->id));

                $count =  DB::select(DB::raw('select count(groups.id) as count from groups,group_members
                        where group_members.group_id = groups.id
                        AND group_members.user_id =
                        '.$this->user->id))[0]->count;
            }
            //return $this->returnData(['groups','count'],[$this->groupState($request),$count]);
            foreach($groups as $group) {
                $group->id = intval( $group->id);
                $url_profile_image = $group->profile_image;
                $url_cover_image = $group->cover_image;
                $group->profile_image = 'https://businesskalied.com/api/business/public/assets/images/groups/' . $url_profile_image;
                $group->cover_image = 'https://businesskalied.com/api/business/public/assets/images/groups/' . $url_cover_image;
                $group->members_count = $this->membersGroup($group->id);
                $group->entered = 1;
                $group->privacy = intval($group->privacy);
                unset(
                    $group->created_at,
                    $group->updated_at,
                    $group->category_id,
                    $group->publisher_id,
                    $group->cover_image,
                    $group->description,
                    $group->rules
                );

            }
            return $this->returnData(['groups','count'],[$groups,intval($count)]);

        }

    }
    public function groupState(Request $request){
        $pending = [];
        $accepted= [];
        $token = $request->token;
        /*        $user = User::where('remember_token',$token)->get();*/
        $user_groups_state =  GroupMember::where('user_id', $this->user->id)->get();
        foreach($user_groups_state as $groups){

            switch ($groups->state){
                case '2':
                    $group = Group::find($groups->group_id);
                    $url_profile_image = $group['profile_image'];
                    $url_cover_image = $group['cover_image'];
                    $group['profile_image'] = 'https://businesskalied.com/api/business/public/assets/images/groups/'.$url_profile_image;
                    $group['cover_image'] = 'https://businesskalied.com/api/business/public/assets/images/groups/'.$url_cover_image;
                    $group['members_count'] = $this->membersGroup($group->id);
                    $group['entered']=2;
                    $pending []=$group;
                    break;
                case '1':
                    $group = Group::find($groups->group_id);
                    $url_profile_image = $group['profile_image'];
                    $url_cover_image = $group['cover_image'];
                    $group['profile_image'] = 'https://businesskalied.com/api/business/public/assets/images/groups/'.$url_profile_image;
                    $group['cover_image'] = 'https://businesskalied.com/api/business/public/assets/images/groups/'.$url_cover_image;
                    $group['members_count'] = $this->membersGroup($group->id);
                    $group['entered']=1;
                    $accepted []= $group;
                    break;
            }
        }
        $msg = [
            'pending'=>$pending,
            'accepted'=>$accepted
        ];
        return $msg;

    }
    public function getAllGroups(){
        $groups = Group::get();
        return $this->returnData(['groups'],[$groups]);
    }
    //4. Enter group flag 0
    //5.Exit group flag 1
    public function enterGroup(Request $request) {
        if($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        }else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $token = $request->token;
            $group_id = $request->group_id;
            $flag = $request->flag;
            /*            $user = User::where('remember_token', $token)->get();*/
            $group = Group::find($group_id);
            if ($flag == 0) {
                $is_member = GroupMember::where('user_id',$this->user->id)->where('group_id',$group_id)->get();
                if(count($is_member) == 0) {
                    if ($group->privacy == 1) {
                        DB::table('group_members')->insert([
                            'group_id' => $group_id,
                            'user_id' => $this->user->id,
                            'state' => 2,
                            'isAdmin'=>0

                        ]);
                        $enteredId = [
                            'enteredId'=>2
                        ];
                        return $this->returnData(['user'], [$enteredId],'your request has been sent');
                        //return $this->returnSuccessMessage('your request has been sent', 200);
                    } else {
                        DB::table('group_members')->insert([
                            'group_id' => $group_id,
                            'user_id' => $this->user->id,
                            'state' => 1,
                            'isAdmin'=>0
                        ]);
                        $enteredId = [
                            'enteredId'=>1
                        ];
                        return $this->returnData(['user'], [$enteredId],'you have entered the group successfully');
                        //return $this->returnSuccessMessage('you have entered the group successfully', 200);
                    }
                }else{
                    $enteredId = [
                        'enteredId'=>2
                    ];
                    return $this->returnData(['user'], [$enteredId],'you have already sent a request before');
                    //return $this->returnSuccessMessageWithStatus('you have already sent a request before', 200,false);

                }

            } else {
                $id= $this->user->id;
                #region leave
                $current_group = GroupMember::where('group_id',$group_id)->where('user_id',$id)->get();
                $current_group_id = $current_group[0]->id;
                if($this->isGroupAdmin($current_group_id) == 1){
                    if($this->groupAdmins($group_id) > 1 ){
                        $current_group = GroupMember::find($current_group_id);
                        $current_group->delete();
                        $enteredId = [
                            'enteredId'=>0
                        ];
                        return $this->returnData(['user'], [$enteredId],'you have already exit the group');
                        //return $this->returnSuccessMessageWithStatus('Done Successfully',200,true);
                    }else{
                        $enteredId = [
                            'enteredId'=>1
                        ];
                        return $this->returnDataWithStatus(['user'], [$enteredId],false,'group must have at least one admin');
                        //return $this->returnSuccessMessageWithStatus('group must have at least one admin',200,false);
                    }
                }else{
                    $current_group = GroupMember::find($current_group_id);
                    $current_group->delete();
                    $enteredId = [
                        'enteredId'=>0
                    ];
                    return $this->returnData(['user'], [$enteredId],'you exit successfully');
                    //return $this->returnSuccessMessageWithStatus('Done Successfully',200,true);
                }
                #endregion
            }
        }
    }
    //6. Group members
    public function membersGroup($id){
        $group = Group::find($id);
        //$related_groups = $this->relatedGroups($group->category_id);
        $group_members = GroupMember::where('group_id',$id)->get();
        /* $register=0;
         foreach($group_members as $member){
             if((Auth::user()->id) == $member->user_id){
                 $register = 1;
             }
         }*/
        //$isAdmin =1;
        //return view('User.groups.members',compact('group','group_members','register','isAdmin','related_groups'));
        return count($group_members);
    }
    public function membersGroupWithState(Request $request){
        if($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        }else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $group_id = $request->group_id;
            $group_state = $request->state;
            /*$group = Group::find($group_id);*/
            $group_members = GroupMember::where('group_id', $group_id)->where('state', $group_state)->get();
            return $this->returnData(['group_members'], [$group_members]);
        }
    }
    public function membersGroupWithCollections(Request $request){
        if($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        }else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $group_id = $request->group_id;
            //$group_state = $request->state;

            $pending = [];
            $accepted = [];
            $admins = [];
            /*$group = Group::find($group_id);*/
            $pending = GroupMember::where('group_id', $group_id)->where('state', 2)->get();
            $user = $this->user->id;
            foreach( $pending as $enemy){
                $enemy_id= $enemy->id;
                $enemy->friendship = $this->CheckUserFriendshipState($user,$enemy_id);
                if($enemy->friendship == 'guest'){
                    $enemy->friendship_id = 99;
                }else{
                    $enemy->friendship_id =  Friendship::where('senderId',$user)->where('receiverId',$enemy)->get()[0]->id;
                }
                $enemy->follow = $this->CheckUserFollowingState($user,$enemy_id);
            }

            $accepted = GroupMember::where('group_id', $group_id)->where('state', 1)->where('isAdmin',0)->get();
            foreach( $accepted as $enemy){
                $enemy_id= $enemy->id;
                $enemy->friendship = $this->CheckUserFriendshipState($user,$enemy_id);
                if($enemy->friendship == 'guest'){
                    $enemy->friendship_id = 99;
                }else{
                    $enemy->friendship_id =  Friendship::where('senderId',$user)->where('receiverId',$enemy)->get()[0]->id;
                }
                $enemy->follow = $this->CheckUserFollowingState($user,$enemy_id);
                $user = $this->getUserById($enemy->user_id);
                $enemy->user_id = [
                    'name'=>$user->name,
                    'personal_image'=>$user->personal_image,
                    'user_id'=>$user->id
                ];
                unset(
                    $enemy->created_at,
                    $enemy->updated_at
                );
            }

            $admins = GroupMember::where('group_id', $group_id)->where('state', 1)->where('isAdmin',1)->get();
            foreach( $admins as $enemy){
                $enemy_id= $enemy->id;
                $enemy->friendship = $this->CheckUserFriendshipState($user,$enemy_id);
                if($enemy->friendship == 'guest'){
                    $enemy->friendship_id = 99;
                }else{
                    $enemy->friendship_id =  Friendship::where('senderId',$user)->where('receiverId',$enemy)->get()[0]->id;
                }
                $enemy->follow = $this->CheckUserFollowingState($user,$enemy_id);
                $user = $this->getUserById($enemy->user_id);
                $enemy->user_id = [
                    'name'=>$user->name,
                    'personal_image'=>$user->personal_image,
                    'user_id'=>$user->id
                    //  'friendship_id'=> Friendship::where('senderId', );
                ];
                unset(
                    $enemy->created_at,
                    $enemy->updated_at
                );
            }
            $group_members =[
                'admins'=>$admins,
                //'pending'=>$pending,
                'accepted'=>$accepted
            ];
            return $this->returnData(['group_members'], [$group_members]);
        }
    }
    public function membersGroupWithCollectionsPending(Request $request){
        if($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        }else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $group_id = $request->group_id;
            //$group_state = $request->state;

            $pending = [];
            /*  $accepted = [];
              $admins = [];*/
            /*$group = Group::find($group_id);*/
            $pending = GroupMember::where('group_id', $group_id)->where('state', 2)->get();
            $user = $this->user->id;
            foreach( $pending as $enemy){
                $enemy_id= $enemy->id;
                $enemy->friendship = $this->CheckUserFriendshipState($user,$enemy_id);
                if($enemy->friendship == 'guest'){
                    $enemy->friendship_id = 99;
                }else{
                    $enemy->friendship_id =  Friendship::where('senderId',$user)->where('receiverId',$enemy)->get()[0]->id;
                }
                $enemy->follow = $this->CheckUserFollowingState($user,$enemy_id);
                $user = $this->getUserById($enemy->user_id);
                $enemy->user_id = [
                    'name'=>$user->name,
                    'personal_image'=>$user->personal_image,
                    'user_id'=>$user->id
                ];
                unset(
                    $enemy->created_at,
                    $enemy->updated_at
                );
            }
            /*$accepted = GroupMember::where('group_id', $group_id)->where('state', 1)->where('isAdmin',0)->get();
            foreach( $accepted as $enemy){
                $enemy_id= $enemy->id;
                $enemy->friendship = $this->CheckUserFriendshipState($user,$enemy_id);
                $enemy->follow = $this->CheckUserFollowingState($user,$enemy_id);
            }

            $admins = GroupMember::where('group_id', $group_id)->where('state', 1)->where('isAdmin',1)->get();
            foreach( $admins as $enemy){
                $enemy_id= $enemy->id;
                $enemy->friendship = $this->CheckUserFriendshipState($user,$enemy_id);
                $enemy->follow = $this->CheckUserFollowingState($user,$enemy_id);
            }*/
            $group_members =[
                'pending'=>$pending,
            ];
            return $this->returnData(['group_members'], [$group_members]);
        }
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

    public function handleGroupMembersRequests(Request $request){
        if($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        }else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $request_id = $request->request_id;
            $state = intval($request->state);
            $group = GroupMember::find($request_id);
            if($state == 0) {
                $group->delete();
                $msg = 'request has delete successfully';
                return $this->returnDataWithStatus(['request'], [['msg'=>$msg]],true, $msg);

                /*            return $this->returnSuccessMessageWithStatus($msg,200,true);*/
            }else {
                $group->update([
                    'state' => $state
                ]);
                $msg = 'request has updated successfully';
                //return $this->returnSuccessMessageWithStatus($msg,200,true);
                return $this->returnDataWithStatus(['request'], [['msg'=>$msg]],true, $msg);

            }
        }
    }


    public function relatedGroups($category,$user_id){
        $related_groups = Group::where('category_id',$category)->inRandomOrder()->limit(3)->get();

        foreach($related_groups as $group) {
            $group_members = GroupMember::where('group_id', $group->id)->get();
            $register =0;
            foreach ($group_members as $member) {
                if ($user_id == $member->user_id) {
                    $register = 1;
                }
            }
            $user = $this->getUserById($group->publisher_id);
            $group->user = [
                'name'=>$user->name,
                'personal_image'=>$user->personal_image,
                'user_id'=>$user->id
            ];
            $group['register']= $register;
            $group['members_count'] = $this->membersGroup($group->id);
            $url_profile_image = $group['profile_image'];
            $url_cover_image = $group['cover_image'];
            $group['profile_image'] = 'https://businesskalied.com/api/business/public/assets/images/groups/'.$url_profile_image;
            $group['cover_image'] = 'https://businesskalied.com/api/business/public/assets/images/groups/'.$url_cover_image;
        }
        return $related_groups;

    }
    public function show(Request  $request)
    {
        if($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        }else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $lang =  $request->header('lang');
            /*            $user_id = User::where('remember_token',$request->token)->get();*/
            $user_id =$this->user->id;
            $group_id = $request->group_id;
            $group = Group::find($group_id);
            $url_profile_image = $group['profile_image'];
            $url_cover_image = $group['cover_image'];
            if($group->privacy == 1){
                $group->privacy = 'public';
            }elseif($group->privacy == 2){
                $group->privacy = 'private';
            }
            $group->category_id = Category::find($group->category_id);
            $group['profile_image'] = 'https://businesskalied.com/api/business/public/assets/images/groups/'.$url_profile_image;
            $group['cover_image'] = 'https://businesskalied.com/api/business/public/assets/images/groups/'.$url_cover_image;
            $group->category_id->image = 'https://businesskalied.com/api/business/public/assets/images/categories/'.$group->category_id->image ;
            $group->category_id['name'] = $group->category_id['name_'.$lang];
            unset(
                $group->created_at,
                $group->updated_at,
                $group->profile_image,
                $group->category_id->created_at,
                $group->category_id->updated_at,
                $group->category_id->type,
                $group->category_id->name_ar,
                $group->category_id->name_en,
            );

            if ($group) {
                $user = $this->getUserById($group->publisher_id);
                $group->publisher_id = [
                    'name'=>$user->name,
                    'personal_image'=>$user->personal_image,
                    'user_id'=>$user->id
                ];

                //$related_groups = $this->relatedGroups($group->category_id,$user_id);
                $group_members = GroupMember::where('group_id', $group_id)->get();
                /*$group_posts = Post::where('group_id', $group_id)->orderBy('created_at', 'ASC')->get();
                foreach($group_posts as $groupx){
                    $user = $this->getUserById($groupx->publisherId);
                    $groupx->user = [
                        'name'=>$user->name,
                        'personal_image'=>$user->personal_image,
                        'user_id'=>$user->id
                    ];
                    unset(
                        $groupx->postTypeId,
                        $groupx->categoryId,
                        $groupx->group_id,
                        $groupx->price,
                        $groupx->title,
                        $groupx->privacyId,
                        $groupx->stateId,
                        $groupx->publisherId,
                        $groupx->page_id,
                        $groupx->post_id,
                        $groupx->created_at,
                        $groupx->updated_at,
                    );

                    $groupx->likes_num = count(Likes::where('model_type','post')->where('model_id',$groupx->id)->get());
                    $groupx->react_num =Likes::where('model_type','post')->where('model_id',$groupx->id)->where('senderId',$user_id)->get();
                    if(count($groupx->react_num) > 0){
                        $groupx->react_num =intval($groupx->react_num[0]->reactId);
                    }else{
                        $groupx->react_num = 99;
                    }
                    $groupx->comments_num = $this->GetCommentsNumber($groupx->id,'post');
                    $groupx->shares_num = $this->getPostshareNumber($group_id,$groupx->id);

                    $groupx->images = $this->Fetchmedia($groupx->id, 'image');
                    $groupx->videos = $this->Fetchmedia($groupx->id, 'video');
                }*/

                //dd($group_members);
                $register = 0;
                foreach ($group_members as $member) {
                    if ($user_id == $member->user_id) {
                        $register = 1;
                    }
                }
                $group['members_count'] = $this->membersGroup($group->id);
                if($register == 1){
                    $isAdmin = GroupMember::where('group_id',$group_id)->where('user_id',$user_id)->get();
                    $is_admin = intval($isAdmin[0]->isAdmin);
                }else{
                    $is_admin=0;
                }
                $response = [
                    'details'=>$group,
                    //'group_members'=>$group_members,
                    //'member_count'=>$group['members_count'],
                    'register'=>$register,
                    'isAdmin'=>$is_admin,
                    //'related_groups'=>$related_groups
                ];
                return $this->returnData(['group'], [$response], 'group details');

                //return view('User.groups.index', compact('group', 'group_members', 'register', 'group_posts', 'related_groups'));
            }else{
                return $this->returnError(404,'Group not found');
            }
        }

    }
    public function GetCommentsNumber($post_id,$model_type){
        $res =0;
        $comments = Comment::where('model_type',$model_type)->where('model_id',$post_id)->get();
        $res += count($comments);
        if(count($comments)>0){
            foreach($comments as $comment){
                $commentsx = Comment::where('model_type','comment')->where('model_id',$comment->id)->get();
                $res += count($commentsx);
            }
        }
        return $res;
    }
    public function assignGroupAdmin(Request $request){
        $state = $request->state;
        $group_member_id = $request->group_member_id;

        $group_member =  GroupMember::find($group_member_id);
        $group_member->update([
            'isAdmin'=>$state
        ]);
        $msg = 'Done  successfully';
        return $this->returnSuccessMessage($msg,200);
    }
    public function isGroupAdmin($member_id){
        $group_member =  GroupMember::find($member_id);
        return $group_member->isAdmin;
    }
    public function groupAdmins($group_id){
        $group_admins =  GroupMember::where('group_id',$group_id)->get();
        return count($group_admins);
    }

    public function aboutGroup($id){
        $group = Group::find($id);
        $related_groups = $this->relatedGroups($group->category_id);
        $group_members = GroupMember::where('group_id',$id)->get();
        $register=0;
        foreach($group_members as $member){
            if((Auth::user()->id) == $member->user_id){
                $register = 1;
            }
        }
        return view('User.groups.about',compact('group','group_members','register','related_groups'));
    }


    //Add Post in a group
    public function addPost(Request $request)
    {

        if($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        }else {

            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $user_id = $this->user->id;
            $group = Group::find($request->group_id);
            $category = Category::find($group->category_id)->id;
            if($category) {
                $groupx = Post::create([
                    //'title' => $request->title,
                    'body' => $request->body,
                    'privacyId' => 1,
                    'postTypeId' => 'post',
                    'stateId' => 1,
                    'publisherId' => $user_id,
                    'categoryId' => $category,
                    'group_id' => $request->group_id,
                    'page_id' => $request->page_id,
                    'post_id' => $request->post_id,
                    'price' => null
                ]);

                $user = $this->getUserById($groupx->publisherId);
                $groupx->user = [
                    'name'=>$user->name,
                    'personal_image'=>$user->personal_image,
                    'user_id'=>$user->id
                ];
                unset(
                    $groupx->postTypeId,
                    $groupx->categoryId,
                    $groupx->group_id,
                    $groupx->price,
                    $groupx->title,
                    $groupx->privacyId,
                    $groupx->stateId,
                    $groupx->publisherId,
                    $groupx->page_id,
                    $groupx->post_id,
                    $groupx->created_at,
                    $groupx->updated_at,
                );

                $groupx->likes_num = count(Likes::where('model_type','post')->where('model_id',$groupx->id)->get());
                $groupx->react_num = count(Likes::where('model_type','post')->where('model_id',$groupx->id)->where('senderId',$user_id)->get());
                if($groupx->react_num > 0){
                    $groupx->react_num =intval( $groupx->react_num[0]->reactId);
                }else{
                    $groupx->react_num = 99;
                }
                $groupx->comments_num = $this->GetCommentsNumber($groupx->id,'post');
                $groupx->shares_num = $this->getPostshareNumber($groupx->group_id,$groupx->id);
                $this->media($request,$groupx->id);

                $groupx->images = $this->Fetchmedia($groupx->id, 'image');
                $groupx->videos = $this->Fetchmedia($groupx->id, 'video');

                /*
                   $this->media($request,$groupx->id);
                   $groupx->images = $this->Fetchmedia($groupx->id, 'image');
                   $groupx->videos = $this->Fetchmedia($groupx->id, 'video');*/
            }else{
                $groupx = null;
            }
            $msg = '';
            if ($groupx) {
                $msg = 'post has created successfully';
                //return $this->returnSuccessMessageWithStatus($msg,200,true);
                return $this->returnDataWithStatus(['post'], [$groupx],true,$msg);
                // return $this->returnData([], $values,$msg);
            } else {
                $msg = 'Error during creating your post';
                return $this->returnDataWithStatus(['post'], [$groupx],true,$msg);
                // return $this->returnSuccessMessageWithStatus($msg,200,true);

            }
        }
        //

    }
    public function removePost(Request $request){

        if($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        }else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $post = Post::find($request->post_id);

            if ($post) {
                $post_media = Media::where('model_type','post')->where('model_id',$post->id)->get();
                if(count($post_media) > 0){
                    foreach ($post_media as $res){
                        if (file_exists($res->filename)) {
                            unlink($res->filename);
                        }
                        $res->delete();
                    }

                }
                $post->delete();
                $msg = '';
                $msg = 'post has deleted successfully';
                return $this->returnDataWithStatus(['msg'], [$msg],true,$msg);

                /*                return $this->returnSuccessMessageWithStatus($msg, 200, true);*/
            } else {
                $msg = 'Error during deleting your post';
                return $this->returnDataWithStatus(['msg'], [$msg],false,$msg);

                /*                return $this->returnSuccessMessageWithStatus($msg, 200, false);*/

            }
        }
    }
    public function getMyPosts(Request $request){
        if($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        }else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            /*            $user_id = User::where('remember_token',$request->token)->get()[0]->id;*/
            $user_id =$this->user->id;

            $group_id = $request->group_id;
            $myPosts = Post::where('group_id',$group_id)->where('publisherId',$user_id)->get();
            foreach($myPosts as $groupx){
                $groupx->images = $this->Fetchmedia($groupx->id, 'image');
                $groupx->videos = $this->Fetchmedia($groupx->id, 'video');
            }
            return $this->returnData(['my posts'], [$myPosts], 'my posts');
        }
    }
    //Comments
    public function addComment(Request $request){
        if($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        }else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $model_type = $request->model_type;
            $model_id = $request->model_id;
            $body = $request->body;
            $comment=  Comment::create([
                'user_id' => $this->user->id,
                'body'=>$body,
                'model_type'=>$model_type,
                'model_id'=>$model_id
            ]);
            $comment->model_id = intval($comment->model_id);
            $msg = 'your comment has been added successfully';
            $user = $this->getUserById($comment->user_id);
            unset(
                $comment->user_id,
                $comment->created_at,
                $comment->updated_at
            );
            $comment->user = [
                'id' => $user->id,
                'name' => $user->name,
                'image' => $user->personal_image,
            ];
            /* $postCommentsRelated =Comment::where('model_type','comment')->where('model_id',$comment->id)->get();
             foreach($postCommentsRelated as $commentRelated) {
                 $user =  $this->getUserById($commentRelated->user_id);
                 $commentRelated->likes = $likes;
                 $commentRelated->isLiked = $isliked;
                 $commentRelated->user =[
                     'id'=>$user->id,
                     'name'=>$user->name,
                     'image'=>$user->personal_image,
                     'belongs_to'=> $this->getUserById(Comment::find($commentRelated->model_id)->user_id)->name,
                     'reacts'=>$this->reacts($commentRelated->id,$lang,'comment')
                 ];
             }*/
            //Number of Likes for each comment
            $comment->likes_num = count(Likes::where('model_type', $model_type)->where('model_id', $model_id)->get());
            $comment->comments_num = count(Comment::where('model_type', $model_type)->where('model_id', $model_id)->get());
            if(count(Likes::where('model_type', $model_type)->where('model_id', $model_id)->where('senderId',$this->user->id)->get()) > 0){
                $isliked = 1;
            }else{
                $isliked=0;
            }
            $mayBeMine = Comment::where('model_type', $model_type)->where('user_id', $this->user->id)->get();
            if(count($mayBeMine) > 0){
                $comment->myComment = 1;
            }else{
                $comment->myComment = 0;
            }
            //Check if the current user has liked or not
            $comment->isLiked = $isliked;
            //$comment->postCommentsRelated = $postCommentsRelated;
            //$comment->reacts= $this->reacts($comment->id,$lang,'comment');


            return $this->returnDataWithStatus(['comment'], [$comment],true,$msg);

            /*            return $this->returnSuccessMessageWithStatus(,200,true);*/
        }

    }
    public function updateComment(Request $request){
        if($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        }else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $body = $request->body;
            $comment_id = $request->comment_id;
            $comment = Comment::find($comment_id);
            $comment->update([
                'body'=>$body
            ]);
            return $this->returnSuccessMessageWithStatus('your comment has been updated successfully',200,true);
        }
    }
    public function removeComment(Request $request){
        if($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        }else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $comment_id = $request->comment_id;
            $comment = Comment::find($comment_id);
            $comment->delete();

            return $this->returnSuccessMessageWithStatus('your comment has been deleted successfully',200,true);
        }

    }
    public function getPostComments(Request $request){
        if($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        }else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $lang = 'ar';
            $model_type = $request->model_type;
            $model_id = $request->model_id;

            $isliked = '';
            $postComments = Comment::where('model_type', $model_type)->where('model_id', $model_id)->get();
            //Each comment
            // => User name
            // =>User image
            // =>User id
            // =>Related comments
            foreach ($postComments as $comment) {
                $user = $this->getUserById($comment->user_id);
                $comment->model_id = intval($comment->model_id);
                unset(
                    $comment->user_id,
                    $comment->created_at,
                    $comment->updated_at
                );
                $comment->user = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'image' => $user->personal_image,
                ];

                /* $postCommentsRelated =Comment::where('model_type','comment')->where('model_id',$comment->id)->get();
                 foreach($postCommentsRelated as $commentRelated) {
                     $user =  $this->getUserById($commentRelated->user_id);
                     $commentRelated->likes = $likes;
                     $commentRelated->isLiked = $isliked;
                     $commentRelated->user =[
                         'id'=>$user->id,
                         'name'=>$user->name,
                         'image'=>$user->personal_image,
                         'belongs_to'=> $this->getUserById(Comment::find($commentRelated->model_id)->user_id)->name,
                         'reacts'=>$this->reacts($commentRelated->id,$lang,'comment')
                     ];
                 }*/

                //Number of Likes for each comment
                $reacts =Likes::where('model_type',$model_type)->where('model_id',$model_id)->where('senderId',$this->user->id)->get();
                if(count($reacts) > 0){
                    $comment->react_num =intval( $reacts[0]->reactId);
                }else{
                    $comment->react_num = 99;
                }
                $comment->likes_num = count(Likes::where('model_type', 'comment')->where('model_id', $comment->id)->get());
                $comment->comments_num = count(Comment::where('model_type', 'comment')->where('model_id', $comment->id)->get());
                $mayBeMine = Comment::where('model_type', 'comment')->where('user_id', $this->user->id)->get();
                if(count($mayBeMine) > 0){
                    $comment->myComment = 1;
                }else{
                    $comment->myComment = 0;
                }
                if(count(Likes::where('model_type', 'comment')->where('model_id', $comment->id)->where('senderId',$this->user->id)->get()) > 0){
                    $isliked = 1;
                }else{
                    $isliked=0;
                }
                //Check if the current user has liked or not
                $comment->isLiked = $isliked;
                //$comment->postCommentsRelated = $postCommentsRelated;
                //$comment->reacts= $this->reacts($comment->id,$lang,'comment');

            }
            //return $postComments;
            return $this->returnData(['comments'], [$postComments]);
        }
    }
    //Likes
    public function reacts($model_id,$lang,$model_type){
        $reacts =  React::select('id','name_'.$lang.' AS name')->get();
        foreach($reacts as $react){
            $react_likes = Likes::select('id','reactId','senderId')->where('model_type',$model_type)->where('reactId',$react->id)->where('model_id',$model_id)->get();
            foreach($react_likes as $likes){
                $user = $this->getUserById($likes->senderId);
                $likes->user = [
                    'name'=>$user->name,
                    'personal_image'=>$user->personal_image,
                    'user_id'=>$user->id
                ];
            }
            $react->likes =$react_likes;
            $react->likes_number = count($react_likes);
        }
        $likesNumber = count(Likes::where('model_type',$model_type)->where('model_id',$model_id)->get());
        //null & react number
        $myReact = Likes::where('model_type',$model_type)->where('model_id',$model_id)->where('senderId',$this->user->id)->get();
        if(count($myReact)>0) {
            $myReact =  $myReact[0]->reactId;
            $myReact =
                [
                    'name'=> React::find($myReact)['name_'.$lang],
                    'image'=>React::find($myReact)['image'],
                ];
        }else{

            $myReact = [
                'name'=> null,
                'image'=>null
            ];

        }


        $res = [
            'reacts' =>$reacts,
            'likesNumber'=>$likesNumber,
            'myReact'=>$myReact
        ];

        return $res;

    }
    public function getUserById($id){
        return User::find($id);
    }
    public function addLike(Request $request){
        if($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        }else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            //Post or comment
            $model_type = $request->model_type;
            $model_id = $request->model_id;
            $reactId = $request->reactId;
            $senderId = $this->user->id;
            $old_likes = Likes::where('model_type',$model_type)->where('model_id',$model_id)->where('senderId',$senderId)->get();
            if(count($old_likes) > 0) {
                if($reactId == 99 ){
                    foreach($old_likes as $old_like_remove){
                        $old_like_remove->delete();
                    }
                    $data =[
                        'msg'=>'your like has been deleted successfully',
                        'like'=>null
                    ];
                    return $this->returnSuccessMessageWithStatusLikes($data,200,true);

                }else {
                    $old_likes[0]->update([
                        'model_id' => $model_id,
                        'model_type' => $model_type,
                        'reactId' => $reactId,
                        'senderId' => $senderId
                    ]);
                    $data = [
                        'msg' => 'your like has been updated successfully',
                        'like' => $old_likes[0]
                    ];
                    return $this->returnSuccessMessageWithStatusLikes($data, 200, true);
                }
            }else {
                //	id		model_type	reactId	senderId	created_at	updated_at
                $new_like=  Likes::create([
                    'model_id' => $model_id,
                    'model_type' => $model_type,
                    'reactId' => $reactId,
                    'senderId' => $senderId
                ]);
                $data =[
                    'msg'=>'your like has been added successfully',
                    'like'=>$new_like
                ];

            }
            return $this->returnSuccessMessageWithStatusLikes($data,200,true);

        }
    }
    public function removeLike(Request $request){
        if($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        }else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            //comment_id
            $like_id = $request->like_id;
            $like = Likes::find($like_id);
            $like->delete();
            return $this->returnSuccessMessageWithStatus('your like has been deleted successfully',200,true);
        }
    }
    public function updateLike(Request $request){
        if($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        }else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $like_id = $request->like_id;
            $reactId = $request->reactId;
            $like = Likes::find($like_id);
            $like->update([
                'reactId'=>$reactId
            ]);
            return $this->returnSuccessMessageWithStatus('your like has been updated successfully',200,true);
        }
    }
    //Reports
    public function addReport(Request $request){
        if($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        }else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $body = $request->body;
            $model_type = $request->model_type;
            $model_id = $request->model_id;
            Report::create([
                'body' => $body,
                'model_type' => $model_type,
                'model_id' => $model_id,
                'stateId' => 2,
                'sender_id' => $this->user->id
            ]);
            $msg = 'Your report has sent  successfully';
            return $this->returnSuccessMessageWithStatus($msg,200,true);
        }
    }

    public function media(Request $request,$model_id)
    {
        /*  //return 1;
        if(!$request->hasFile('fileName')) {
            return response()->json(['upload_file_not_found'], 400);
        }
        $files = $request->file('fileName');
        $filesName ='';
        foreach($files as $file){
            $new_name = rand().'.'.$file->getClientOriginalExtension();
            $file->move(public_path('/assets/images/posts'),$new_name);
            $filesName = $filesName .$new_name.",";
        }
        $files = explode(',',$filesName);
        $j=0;
        if(count($files) > 0){
            for($i=0;$i<count($files) -1 ;$i++){
                Media::create([
                    'filename'=>$files[$i],
                    'mediaType'=>'image',
                    'model_type'=>'post',
                    'model_id'=>1
                ]);
            }
        }*/
        if ($request->hasFile('images')) {

            $image_ext = ['jpg', 'png', 'jpeg'];

            $video_ext = ['mpeg', 'ogg', 'mp4', 'webm', '3gp', 'mov', 'flv', 'avi', 'wmv', 'ts'];

            $files = $request->file('images');
            foreach ($files as $file) {
                /*
                                $post_media = DB::table('media')->where('model_id', $model_id)->get();

                                foreach ($post_media as $media) {
                                    $media->delete();
                                    unlink('media/' . $media->filename);
                                }*/

                $fileextension = $file->getClientOriginalExtension();

                if (in_array($fileextension, $image_ext)) {
                    $mediaType = 'image';
                } else {
                    $mediaType = 'video';
                }

                $filename = $file->getClientOriginalName();
                $file_to_store = time() . '_' . explode('.', $filename)[0] . '_.' . $fileextension;

                $test = $file->move(public_path('assets/'.$mediaType.'s/posts/'), $file_to_store);

                if ($test) {
                    Media::create([
                        'filename' => 'https://businesskalied.com/api/business/public/assets/'.$mediaType.'s'.'/posts/'.$file_to_store,
                        'mediaType' => $mediaType,
                        'model_id' => $model_id,
                        'model_type' => 'post'
                    ]);
                }
            }

        }

        if ($request->has('checkedimages')) {

            $post_media = [];

            foreach ($post_media as $media) {
                $post_media = $media->filename;
            }

            $checkedimages = $request->input('checkedimages');

            $deleted_media = array_diff($post_media, $checkedimages);

            if (!empty($deleted_media)) {
                foreach ($deleted_media as $media) {
                    DB::table('media')->where('filename', $media)->delete();
                    unlink('product_images/' . $media);
                }
            }
        }
    }
    public function Fetchmedia($modal_id,$media_type){
        $media = Media::where('model_type','post')->where('model_id',$modal_id)->where('mediaType',$media_type)->get();
        return $media;

    }

    public function GetGroupMedia(Request $request){
        if($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        }else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            //Get Group posts id
            //Get media type
            //1. image
            // or
            //2. video
            $group_id = $request->group_id;
            $media_type = $request->media_type;
            $posts = Post::where('group_id', $group_id)->where('postTypeId','post')->get();
            // return $posts;
            $ids=[];
            foreach($posts as $post) {
                $ids [] = $post->id;
            }
            $media = Media::where('mediaType',$media_type)->where('model_type','post')->get();
            $result =[];
            if(count($media)>0) {
                foreach ($media as $media_iteam)
                    if (in_array($media_iteam->model_id,$ids )) {
                        $result [] = $media_iteam;
                    }
            }
            //  return $result;

            return $this->returnData([$media_type], [$result]);
        }

    }
    public function shareGroupPost(Request $request){
        if($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        }else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $post_id = $request->post_id;
            $group_id = Post::find($post_id)->group_id;
            $body = $request->body;
            $group = Group::find($group_id);
            $category = Category::find($group->category_id)->id;
            $categoryId = $category;
            $publisherId  = $this->user->id;

            $title = null;
            $price = null;
            $page_id = null;
            $postTypeId = 'post';
            $privacyId = 1;
            $stateId = 1;
            $groupx =Post::create([
                'title' => $title,
                'body' => $body,
                'postTypeId' => $postTypeId,
                'privacyId' => $privacyId,
                'stateId' => $stateId,
                'publisherId' => $publisherId,
                'categoryId'=>$categoryId,
                'group_id'=>$group_id,
                'page_id'=>$page_id,
                'post_id'=>$post_id,
                'price'=>$price
            ]);
            if ($groupx) {
                $msg = 'post has shared successfully';
                //return $this->returnSuccessMessageWithStatus($msg,200,true);
                return $this->returnDataWithStatus(['post'], [$groupx],true,$msg);
                // return $this->returnData([], $values,$msg);
            } else {
                $msg = 'Error during shareing your post';
                return $this->returnDataWithStatus(['msg'], [$msg],false,$msg);
                // return $this->returnSuccessMessageWithStatus($msg,200,true);

            }
            //return $this->returnSuccessMessage('Post Shared Successfully to your profile',200);
        }
    }
    public function getShareGroupPost(Request $request){
        if($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        }else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }

            $shares = Post::where('post_id',$request->post_id)->get();
            foreach($shares as $groupx){
                $user = $this->getUserById($groupx->publisherId);
                $groupx->publisherId = [
                    'id'=>$user->id,
                    'name'=>$user->name,
                    'personal_image'=>$user->personal_image
                ];
                unset(
                    $groupx->body,
                    $groupx->postTypeId,
                    $groupx->categoryId,
                    $groupx->group_id,
                    $groupx->price,
                    $groupx->title,
                    $groupx->privacyId,
                    $groupx->stateId,
                    $groupx->page_id,
                    $groupx->post_id,
                    $groupx->created_at,
                    $groupx->updated_at,
                );

            }
            return $this->returnData(['shares'], [$shares],'group shares posts');
        }
    }
    public function getPostshareNumber($group_id,$post_id){
        $posts = Post::where('group_id',$group_id)->where('post_id',$post_id)->get();

        return count($posts);
    }

    public function getPostLikes(Request $request){
        if($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        }else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $model_type = $request->model_type;
            $model_id = $request->model_id;
            $likes = Likes::where('model_type', $model_type)->where('model_id', $model_id)->get();
            foreach($likes as $groupx){
                $user = $this->getUserById($groupx->senderId);
                $groupx->user = [
                    'name'=>$user->name,
                    'personal_image'=>$user->personal_image,
                    'user_id'=>$user->id
                ];
                $groupx->reactId = intval($groupx->reactId);
                unset(
                    $groupx->created_at,
                    $groupx->updated_at,
                    $groupx->senderId
                );
            }
            return $this->returnData(['likes'], [$likes]);
        }
    }
    public function one_react($react_id,$lang){
        $react = React::find($react_id);
        return [
            'id'=>$react['id'],
            'name'=> $react['name_'.$lang],
            'image'=>$react['image']
        ];
    }
    public function get_all_reacts(){
        return $this->returnData(['reacts'], [React::get()]) ;
    }

    public function getPosts(Request $request){
        if($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        }else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }

            $model_type = $request->model_type . '_id';
            $model_id = $request->model_id;
            $group_posts = Post::where($model_type, $model_id)->orderBy('created_at', 'ASC')->get();
            foreach($group_posts as $groupx){
                $user = $this->getUserById($groupx->publisherId);
                $groupx->user = [
                    'name'=>$user->name,
                    'personal_image'=>$user->personal_image,
                    'user_id'=>$user->id
                ];
                unset(
                    $groupx->postTypeId,
                    $groupx->categoryId,
                    $groupx->group_id,
                    $groupx->price,
                    $groupx->title,
                    $groupx->privacyId,
                    $groupx->stateId,
                    $groupx->publisherId,
                    $groupx->page_id,
                    $groupx->post_id,
                    $groupx->created_at,
                    $groupx->updated_at,
                );

                $groupx->likes_num = count(Likes::where('model_type','post')->where('model_id',$groupx->id)->get());
                $groupx->react_num =Likes::where('model_type','post')->where('model_id',$groupx->id)->where('senderId',$this->user->id)->get();
                if(count($groupx->react_num) > 0){
                    $groupx->react_num =intval($groupx->react_num[0]->reactId);
                }else{
                    $groupx->react_num = 99;
                }
                $groupx->comments_num = $this->GetCommentsNumber($groupx->id,'post');
                $groupx->shares_num = $this->getPostshareNumber($model_id,$groupx->id);

                $groupx->images = $this->Fetchmedia($groupx->id, 'image');
                $groupx->videos = $this->Fetchmedia($groupx->id, 'video');
            }
        /*    return $this->returndata(['posts'], $group_posts,'posts for'.$model_type);*/
                return $this->returnDataWithStatusList($group_posts, true,'posts for '. $model_type);
        }

    }

}
