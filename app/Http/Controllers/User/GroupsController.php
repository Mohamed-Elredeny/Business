<?php

namespace App\Http\Controllers\User;

use App\User;
use App\models\Post;
use App\Models\Group;
use App\models\Media;
use App\models\Category;
use App\Models\Friendship;
use App\models\GroupMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\models\Following;
use Illuminate\Support\Facades\Auth;

class GroupsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($flag)
    {

        $user = auth()->user();

        if($flag == 0) {
            $groups = Group::all();

            foreach ($groups as $group) {
                $user_group = DB::table('group_members')
                    ->where([['group_id',$group->id],['user_id',1],['state',1]])
                    ->first();

                $groups_users = DB::table('group_members')
                    ->where('group_id',$group->id)->where('state',1)
                    ->count();

                $group->users = $groups_users;

                if ($user_group) {
                    $group['entered'] = 1;
                }
                else{
                    $group['entered'] = 0;
                }
            }
        }
        else{
            $groups = DB::select(DB::raw('select groups.* from groups,group_members
                        where group_members.group_id = groups.id
                        AND group_members.user_id = 1 and group_members.state = 1'));

            foreach ($groups as $group) {
                $groups_users = DB::table('group_members')
                    ->where('group_id',$group->id)->where('state',1)
                    ->count();

                $group->users = $groups_users;
            }
        }

        $user_interests = DB::select(DB::raw('select categories.id from categories,user_categories
                        where user_categories.categoryId = categories.id
                        AND user_categories.userId = 1 and categories.type = 0'));

        $user_interests_array = [];

        foreach ($user_interests as $interest){
            array_push($user_interests_array,$interest->id);
        }

        $expected_groups = Group::whereIn('category_id',$user_interests_array)->limit(3);

        foreach ($expected_groups as $group) {
            $groups_users = DB::table('group_members')
                ->where('group_id',$group->id)->where('state',1)
                ->count();

            $group->users = $groups_users;
        }

        return view('User.groups.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categroys = Category::get();
        return view('User.groups.create', compact('categroys'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'privacy' => ['required'],
        ];

        $this->validate($request,$rules);
        $profile_image = time() . '.' . $request->profile_image->extension();
        $request->profile_image->move(public_path('assets/images/groups/profile'), $profile_image);

        $cover_image = time() . '.' . $request->cover_image->extension();
        $request->cover_image->move(public_path('assets/images/groups/cover'), $cover_image);
       
        $group = Group::insertGetId([
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'publisher_id' => Auth::guard('web')->user()->id,
            'profile_image' => $profile_image,
            'cover_image' => $cover_image,
            'rules' =>  $request->rules,
            'privacy' => $request->privacy
        ]);
        $group_admin = DB::table('group_members')->insert([
            'group_id' => $group,
            'user_id' => Auth::guard('web')->user()->id,
            'state' => 0,
            'isAdmin'=>1
        ]);

        if($group){
            return redirect('groups/'.$group);
        }
        else
        {
            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(Auth::guard('web')->user())
        {
            $categroys = Category::get();
            $group = Group::find($id);
            $isAdmin = $this->isAdmin($id);
            if($isAdmin == 1)
            {
                return view('User.groups.edit', compact('categroys','group'));
            }
            else
            {
                return redirect()->back();
            }
        }
        else
        {
            return redirect('login');
        }
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $group = Group::find($id);
        if (isset($request->profile_image)) {
            $profile_image = time() . '.' . $request->profile_image->extension();
            $request->profile_image->move(public_path('assets/images/groups/profile'), $profile_image);
        } else {
            $profile_image = $group->profile_image;
        }
        if (isset($request->cover_image)) {
            $cover_image = time() . '.' . $request->cover_image->extension();
            $request->cover_image->move(public_path('assets/images/groups/cover'), $cover_image);
        } else {
            $cover_image  = $group->cover_image;
        }

        $group->update([
            'name' => $request->name,
            'description' => $request->description,
            'category_id' =>$request->category_id,
            'publisher_id' => $request->publisher_id,
            'profile_image' => $profile_image,
            'cover_image' => $cover_image,
            'rules' => $request->rules,
            'privacy' => $request->privacy
        ]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $group_id = $id;
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
       return redirect('home');
    }

    //Get Related Groups
    public function relatedGroups($category){
        $related_groups = Group::where('category_id',$category)->inRandomOrder()->limit(3)->get();
        return $related_groups;
    }

    public function memberState($id)
    {
        $myState=0;
        if(Auth::guard('web')->user())
        {
            $state = GroupMember::where('group_id',$id)->where('user_id',Auth::guard('web')->user()->id)->get();
            if(count($state)>0)
            {
                $myState = $state[0]['state'];
            }
        }
        return $myState;
    }

    public function isAdmin($id)
    {
        $isAdmin=0;
        if(Auth::guard('web')->user())
        {
            $state = GroupMember::where('group_id',$id)->where('user_id',Auth::guard('web')->user()->id)->where('isAdmin',1)->get();
            // $publisher = Group::where('id', $id)->where('publisher_id', Auth::guard('web')->user()->id)->get();
            if(count($state)>0)
            {
                $isAdmin = 1;
            }
            
        }
        return $isAdmin;
    }

    public function aboutGroup($id){
        $group = Group::find($id);
        $related_groups = $this->relatedGroups($group->category_id);
        $myState = $this->memberState($id);
        $isAdmin = $this->isAdmin($id);
        $group_members = GroupMember::where('group_id',$id)->get();
        return view('User.groups.about',compact('group','group_members','myState', 'isAdmin', 'related_groups'));
    }

    public function groupMedia($type, $id)
    {
        //0 image
        //1 video
        $media_ids = [];
        $group_posts = Post::where('group_id', $id)->orderBy('created_at', 'ASC')->get();
        switch ($type) {
            case 0:
                $media = Media::where('mediaType', $type)->where('model_type', 'post')->get();
                break;
            case 1:
                $media = Media::where('mediaType', $type)->where('model_type', 'post')->get();
                break;
        }
        foreach ($group_posts as $gro) {
            $group_posts_ids[] = $gro->id;
        }
        foreach ($media as $med) {
            $media_post_id = $med->model_id;
            if (in_array($media_post_id, $group_posts_ids)) {
                $media_ids[] = $med->id;
            }
        }
        return $media_ids;
    }

    public function imagesGroup($id){
        $group = Group::find($id);
        $related_groups = $this->relatedGroups($group->category_id);
        $myState = $this->memberState($id);
        $isAdmin = $this->isAdmin($id);
        $group_members = GroupMember::where('group_id',$id)->get();
        //0 image
        //1 video
        $images = [];
        $media = $this->groupMedia(0, $id);
        for ($i = 0; $i < count($media); $i++) {
            $images[] = Media::find($media[$i]);
        }
        return view('User.groups.images',compact('group','group_members','myState', 'isAdmin', 'related_groups', 'images'));
    }
    
    public function joinGroup(Request $request){
        //Leave & Join
        $requestType = $request->requestType;
        $group_id = $request->group_id;
        $user_id = $request->user_id;

        switch($requestType){
            case 'join':
                #region join
                //If the Group is public users will join directly otherwise they will waite for the admin.
                //1 public
                //0 private
                $current_group = Group::find($group_id);
                if($current_group->privacy == 1){
                    //1 Public Group
                    //State wil be 1 => accepted
                    $new_member = GroupMember::create([
                        'user_id'=>$user_id,
                        'group_id'=>$group_id,
                        'state'=>1
                    ]);
                    $group_members = GroupMember::where('group_id',$group_id)->count();
                    return 1 .'|'.$group_id.'|'.$group_members;
                }else{
                    //0 Private Group
                    //State will be 2 pending
                    $new_member = GroupMember::create([
                        'user_id'=>$user_id,
                        'group_id'=>$group_id,
                        'state'=>2
                    ]);
                    $group_members = GroupMember::where('group_id',$group_id)->count();
                    return 2 .'|'.$group_id.'|'.$group_members;
                }
                #endregion
            break;
            case 'leave':
                #region leave
                $current_group = GroupMember::where('group_id',$group_id)->where('user_id',$user_id)->get();
                $current_group_id = $current_group[0]->id;
                $current_group = GroupMember::find($current_group_id);
                $current_group->delete();
                #endregion
                $group_members = GroupMember::where('group_id',$group_id)->count();
                return 0 .'|'.$group_id.'|'.$group_members;
            break;

            case 'confirm':
                $current_group = GroupMember::where('group_id',$group_id)->where('user_id',$user_id)->get();
                $current_group_id = $current_group[0]->id;
                $current_group = GroupMember::find($current_group_id);
                $current_group->update([
                    'state'=>1
                ]);

                $group_members = GroupMember::where('group_id',$group_id)->count();
                return 1 .'|'.$group_id.'|'.$group_members;
            break;
        }
        // return redirect()->back()->with('message','Done Successfully');
        // return $requestType;
    }

    public function requestsGroup($id)
    {
        $group = Group::find($id);
        $related_groups = $this->relatedGroups($group->category_id);
        $myState = $this->memberState($id);
        $isAdmin = $this->isAdmin($id);
        $group_members = GroupMember::where('group_id',$id)->get();
        $group_requests = GroupMember::where('group_id',$id)->where('state',2)->get();
        return view('User.groups.requests',compact('group','group_members','myState', 'isAdmin', 'related_groups','group_requests'));
    }

    public function changeRequest(Request $request)
    {
        $group = GroupMember::find($request->request_id);
        if($request->requestType == 'delete') 
        {
            $group->delete();
            return $request->request_id;
        }
        elseif($request->requestType == 'conferm') 
        {
            $group->update([
                'state' => 1
            ]);
            return $request->request_id;
        }
    }

    public function adminLeft($id)
    {
        $admins = GroupMember::where('group_id',$id)->where('isAdmin',1)->get();
        if(count($admins) > 1)
        {
            $admin = GroupMember::where('user_id',Auth::guard('web')->user()->id)->get();
            $admin[0]->delete();
            return redirect('groups');
        }
        else
        {
            return redirect()->back()->with('error', "you can't left group asign anther admin or remove group");
        }
    }

    public function membersGroup($id)
    {
        $group = Group::find($id);
        $related_groups = $this->relatedGroups($group->category_id);
        $myState = $this->memberState($id);
        $isAdmin = $this->isAdmin($id);
        $group_members = GroupMember::where('group_id',$id)->get();
        //$admins = GroupMember::where('group_id',$id)->where('isAdmin',1)->get();
        
        $accepteds = [];
        $admins = [];
        // $myFriends =[];

        $accepteds = GroupMember::where('group_id', $id)->where('state', 1)->get();
        $admins = GroupMember::where('group_id', $id)->where('isAdmin',1)->get();

        if(Auth::guard('web')->user())
        {
            foreach( $accepteds as $enemy){
                $enemy_id= $enemy->user_id;
                $enemy->friendship = $this->CheckUserFriendshipState(Auth::guard('web')->user()->id,$enemy_id);
                $enemy->follow = $this->CheckUserFollowingState(Auth::guard('web')->user()->id,$enemy_id);
            }
            $myData = User::find(Auth::guard('web')->user()->id);

            foreach( $admins as $enemyy){
                $enemy_id= $enemyy->user_id;
                $enemyy->friendship = $this->CheckUserFriendshipState(Auth::guard('web')->user()->id,$enemy_id);
                $enemyy->follow = $this->CheckUserFollowingState(Auth::guard('web')->user()->id,$enemy_id);
            }
            return view('User.groups.members',compact('group','group_members', 'myData', 'admins', 'accepteds', 'myState', 'isAdmin', 'related_groups'));
        }
        else
        {
            return view('User.groups.members',compact('group','group_members', 'admins', 'accepteds', 'myState', 'isAdmin', 'related_groups'));
        }
    }

    public function CheckUserFriendshipState($user,$enemy){
        //Different users
        //1. User => From token
        //2. Enemy=> The person i want to check my friendship with
        /*
         * D
         */
        #region different States
        //Friend
        //pending login => request  cancel request
        //cancel
        //accepted => cancel request
        //guest  => add request

        //return $user . '|' . $enemy;

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
                        return 'request';
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

    public function frirndshipGroup(Request $request){
        //return $request->requestType . $request->enemy_id . Auth::guard('web')->user()->id;
        $requestType = $request->requestType;
        $enemy_id = $request->enemy_id;
        $user_id = Auth::guard('web')->user()->id;

        switch($requestType){
            case 'add':
                Friendship::create([
                    'senderId'=>$user_id,
                    'receiverId'=>$enemy_id,
                    'stateId'=>2
                ]);
                $current_following = Following::where('followerId',$user_id)->where('followingId',$enemy_id)->get();
                if(count($current_following) == 0)
                {
                    Following::create([
                        'followerId'=>$user_id,
                        'followingId'=>$enemy_id,
                    ]);
                }

                $followers = Following::where('followingId',$enemy_id)->count();
                return '2|' . $enemy_id . '|' . $followers;
            break;

            case 'remove':
                $current_friendship = Friendship::where('senderId',$user_id)->where('receiverId',$enemy_id)->get();
                if(count($current_friendship) > 0)
                {
                    $current_friendship_id = $current_friendship[0]->id;
                    $current_friendship = Friendship::find($current_friendship_id);
                    $current_friendship->delete();
                }
                else
                {
                    $current_friendship = Friendship::where('receiverId',$user_id)->where('senderId',$enemy_id)->get();
                    $current_friendship_id = $current_friendship[0]->id;
                    $current_friendship = Friendship::find($current_friendship_id);
                    $current_friendship->delete();
                }
                $current_following = Following::where('followerId',$user_id)->where('followingId',$enemy_id)->get();
                if(count($current_following) > 0)
                {
                    $current_following = Following::find($current_following[0]->id);
                    $current_following->delete();
                }
                $followers = Following::where('followingId',$enemy_id)->count();
                return 0 . '|' . $enemy_id . '|' . $followers;
            break;

            case 'confirm':
                $current_friendship = Friendship::where('receiverId',$user_id)->where('senderId',$enemy_id)->get();
                $current_friendship_id = $current_friendship[0]->id;
                $current_friendship = Friendship::find($current_friendship_id);
                $current_friendship->update([
                    'stateId' =>1
                ]);

                $current_following = Following::where('followerId',$user_id)->where('followingId',$enemy_id)->get();
                if(count($current_following) == 0)
                {
                    Following::create([
                        'followerId'=>$user_id,
                        'followingId'=>$enemy_id,
                    ]);
                }
                $followers = Following::where('followingId',$enemy_id)->count();
                return 1 . $enemy_id . '|' . $followers;
            break;
        }
        
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

    public function followingGroup(Request $request){
        $requestType = $request->requestType;
        $enemy_id = $request->enemy_id;
        $user_id = Auth::guard('web')->user()->id;

        switch($requestType){
            case 'addFollowing':
                Following::create([
                    'followerId'=>$user_id,
                    'followingId'=>$enemy_id,
                ]);

                $followers = Following::where('followingId',$enemy_id)->count();
                return '1|' . $enemy_id . '|' . $followers;
            break;

            case 'removeFollowing':                
                $current_following = Following::where('followerId',$user_id)->where('followingId',$enemy_id)->get();
                $current_following = Following::find($current_following[0]->id);
                $current_following->delete();
                
                $followers = Following::where('followingId',$enemy_id)->count();
                return 0 . '|' . $enemy_id . '|' . $followers;
            break;
        }
        
    }

    public function asignAdmin(Request $request){
        $requestType = $request->requestType;
        $enemy_id = $request->enemy_id;
        $group_id = $request->group_id;

        switch($requestType){
            case 'addAdmin':
                $current_member = GroupMember::where('group_id',$group_id)->where('user_id',$enemy_id)->get();
                $current_member = GroupMember::find($current_member[0]->id);
                $current_member->update([
                    'isAdmin' =>1,
                    'state' =>0,
                ]);

                return 1;

            break;

            case 'removeMember':
                $current_member = GroupMember::where('group_id',$group_id)->where('user_id',$enemy_id)->get();
                $current_member = GroupMember::find($current_member[0]->id);
                $current_member->delete();

                return '0|' . $enemy_id ;
            break;

            case 'invite':
                $current_member = GroupMember::where('group_id',$group_id)->where('user_id',$enemy_id)->get();
                if(count($current_member)>0)
                {
                    $current_member = GroupMember::find($current_member[0]->id);
                    $current_member->update([
                        'state' =>3,
                    ]);
                }
                if(count($current_member) == 0)
                {
                    GroupMember::create([
                        'user_id'=>$enemy_id,
                        'group_id'=>$group_id,
                        'state' =>3,
                    ]);
                }

                return '0|' . $enemy_id ;
            break;

        }
        
    }

    public function allGroup()
    {
        $related_groups = Group::limit(3)->get();
        $all_groups = Group::paginate(30);
        
        return view('User.groups.allGroups',compact('related_groups','all_groups'));
    }

    public function myGroup()
    {
        $related_groups = Group::limit(3)->get();
        if(Auth::guard('web')->user())
        {
            $my_groups = GroupMember::where('user_id',Auth::guard('web')->user()->id)->get();
            return view('User.groups.myGroups',compact('related_groups','my_groups'));
        }

        return view('User.groups.myGroups',compact('related_groups','all_groups'));
    }
}
