<?php

namespace App\Http\Controllers\User;

use App\User;
use App\Models\Page;
use App\Models\Post;
use App\Models\Group;
use App\models\Media;
use App\models\Category;
use App\models\Following;
use App\Models\Friendship;
use App\models\PageMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PagesController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categroys = Category::get();
        return view('User.pages.create', compact('categroys'));
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
        $request->profile_image->move(public_path('assets/images/pages/profile'), $profile_image);

        $cover_image = time() . '.' . $request->cover_image->extension();
        $request->cover_image->move(public_path('assets/images/pages/cover'), $cover_image);
       
        $page = Page::insertGetId([
            'name' => $request->name,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'publisher_id' => Auth::guard('web')->user()->id,
            'profile_image' => $profile_image,
            'cover_image' => $cover_image,
            'rules' =>  $request->rules,
            'privacy' => $request->privacy
        ]);
        $page_admin = DB::table('page_members')->insert([
            'page_id' => $page,
            'user_id' => Auth::guard('web')->user()->id,
            'state' => 0,
            'isAdmin'=>1
        ]);

        if($page){
            return redirect('pages/'.$page);
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
            $page = Page::find($id);
            $isAdmin = $this->isAdmin($id);
            if($isAdmin == 1)
            {
                return view('User.pages.edit', compact('categroys','page'));
            }
            else
            {
                return redirect()->back();
            }
            // return $isAdmin;
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
        $page = Page::find($id);
        if (isset($request->profile_image)) {
            $profile_image = time() . '.' . $request->profile_image->extension();
            $request->profile_image->move(public_path('assets/images/pages/profile'), $profile_image);
        } else {
            $profile_image = $page->profile_image;
        }
        if (isset($request->cover_image)) {
            $cover_image = time() . '.' . $request->cover_image->extension();
            $request->cover_image->move(public_path('assets/images/pages/cover'), $cover_image);
        } else {
            $cover_image  = $page->cover_image;
        }

        $page->update([
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
        $page_id = $id;
        //Posts
        //Media
        $page = Page::find($page_id);
        $posts  = Post::where('page_id',$page_id)->get();
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
        $page->delete();
       return redirect('home');
    }

    //Get Related pages
    public function relatedPages($category){
        $related_pages = Page::where('category_id',$category)->inRandomOrder()->limit(3)->get();
        return $related_pages;
    }

    public function memberState($id)
    {
        $myState=0;
        if(Auth::guard('web')->user())
        {
            $state = PageMember::where('page_id',$id)->where('user_id',Auth::guard('web')->user()->id)->get();
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
            $state = PageMember::where('page_id',$id)->where('user_id',Auth::guard('web')->user()->id)->where('isAdmin',1)->get();
            // $publisher = Page::where('id', $id)->where('publisher_id', Auth::guard('web')->user()->id)->get();
            if(count($state)>0)
            {
                $isAdmin = 1;
            }
            
        }
        return $isAdmin;
    }

    public function aboutPage($id){
        $page = Page::find($id);
        $related_pages = $this->relatedPages($page->category_id);
        $myState = $this->memberState($id);
        $isAdmin = $this->isAdmin($id);
        $page_members = PageMember::where('page_id',$id)->get();
        return view('User.pages.about',compact('page','page_members','myState', 'isAdmin', 'related_pages'));
    }

    public function pageMedia($type, $id)
    {
        //0 image
        //1 video
        $media_ids = [];
        $page_posts = Post::where('page_id', $id)->orderBy('created_at', 'ASC')->get();
        switch ($type) {
            case 0:
                $media = Media::where('mediaType', $type)->where('model_type', 'post')->get();
                break;
            case 1:
                $media = Media::where('mediaType', $type)->where('model_type', 'post')->get();
                break;
        }
        foreach ($page_posts as $gro) {
            $page_posts_ids[] = $gro->id;
        }
        foreach ($media as $med) {
            $media_post_id = $med->model_id;
            if (in_array($media_post_id, $page_posts_ids)) {
                $media_ids[] = $med->id;
            }
        }
        return $media_ids;
    }

    public function imagesPage($id){
        $page = Page::find($id);
        $related_pages = $this->relatedPages($page->category_id);
        $myState = $this->memberState($id);
        $isAdmin = $this->isAdmin($id);
        $page_members = PageMember::where('page_id',$id)->get();
        //0 image
        //1 video
        $images = [];
        $media = $this->pageMedia(0, $id);
        for ($i = 0; $i < count($media); $i++) {
            $images[] = Media::find($media[$i]);
        }
        return view('User.pages.images',compact('page','page_members','myState', 'isAdmin', 'related_pages', 'images'));
    }
    
    public function joinPage(Request $request){
        //Leave & Join
        $requestType = $request->requestType;
        $page_id = intval($request->page_id);
        $user_id = intval($request->user_id);

        switch($requestType){
            case 'join':
                #region join
                //If the page is public users will join directly otherwise they will waite for the admin.
                //1 public
                //0 private
                $current_page = Page::find($page_id);
                if($current_page->privacy == 1){
                    //1 Public page
                    //State wil be 1 => accepted
                    $new_member = PageMember::create([
                        'user_id'=>  $user_id,
                        'page_id'=>$page_id,
                        'state'=>1
                    ]);
                    $page_members = PageMember::where('page_id',$page_id)->count();
                    return 1 .'|'.$page_id.'|'. 1;
                }
                else{
                    //0 Private page
                    //State will be 2 pending
                    $new_member = PageMember::create([
                        'user_id'=>$user_id,
                        'page_id'=>$page_id,
                        'state'=>2
                    ]);
                    $page_members = PageMember::where('page_id',$page_id)->count();
                    return 2 .'|'.$page_id.'|'.$page_members;
                }
                #endregion
            break;
            case 'leave':
                #region leave
                $current_page = PageMember::where('page_id',$page_id)->where('user_id',$user_id)->get();
                $current_page_id = $current_page[0]->id;
                $current_page = PageMember::find($current_page_id);
                $current_page->delete();
                #endregion
                $page_members = PageMember::where('page_id',$page_id)->count();
                return 0 .'|'.$page_id.'|'.$page_members;
            break;

            case 'confirm':
                $current_page = PageMember::where('page_id',$page_id)->where('user_id',$user_id)->get();
                $current_page_id = $current_page[0]->id;
                $current_page = PageMember::find($current_page_id);
                $current_page->update([
                    'state'=>1
                ]);

                $page_members = PageMember::where('page_id',$page_id)->count();
                return 1 .'|'.$page_id.'|'.$page_members;
            break;
        }
        // return redirect()->back()->with('message','Done Successfully');
        // return $requestType;
    }

    public function requestsPage($id)
    {
        $page = Page::find($id);
        $related_pages = $this->relatedPages($page->category_id);
        $myState = $this->memberState($id);
        $isAdmin = $this->isAdmin($id);
        $page_members = PageMember::where('page_id',$id)->get();
        $page_requests = PageMember::where('page_id',$id)->where('state',2)->get();
        return view('User.pages.requests',compact('page','page_members','myState', 'isAdmin', 'related_pages','page_requests'));
    }

    public function changePage(Request $request)
    {
        $page = PageMember::find($request->request_id);
        if($request->requestType == 'delete') 
        {
            $page->delete();
            return $request->request_id;
        }
        elseif($request->requestType == 'conferm') 
        {
            $page->update([
                'state' => 1
            ]);
            return $request->request_id;
        }
    }

    public function adminLeft($id)
    {
        $admins = PageMember::where('page_id',$id)->where('isAdmin',1)->get();
        if(count($admins) > 1)
        {
            $admin = PageMember::where('user_id',Auth::guard('web')->user()->id)->get();
            $admin[0]->delete();
            return redirect('pages');
        }
        else
        {
            return redirect()->back()->with('error', "you can't left page asign anther admin or remove page");
        }
    }

    public function membersPage($id)
    {
        $page = Page::find($id);
        $related_pages = $this->relatedPages($page->category_id);
        $myState = $this->memberState($id);
        $isAdmin = $this->isAdmin($id);
        $page_members = PageMember::where('page_id',$id)->get();
        
        //$admins = PageMember::where('page_id',$id)->where('isAdmin',1)->get();
        
        $accepteds = [];
        $admins = [];
        // $myFriends =[];

        $accepteds = PageMember::where('page_id', $id)->where('state', 1)->get();
        

        $admins = PageMember::where('page_id', $id)->where('isAdmin',1)->get();
       
        if(Auth::guard('web')->user())
        {
            foreach( $accepteds as $enemy){
                $enemy_id= $enemy->user_id;
                $enemy->friendship = $this->CheckUserFriendshipState(Auth::guard('web')->user()->id,$enemy_id);
                $enemy->follow = $this->CheckUserFollowingState(Auth::guard('web')->user()->id,$enemy_id);
            }
            
            foreach( $admins as $enemyy){
                $enemy_id= $enemyy->user_id;
                $enemyy->friendship = $this->CheckUserFriendshipState(Auth::guard('web')->user()->id,$enemy_id);
                $enemyy->follow = $this->CheckUserFollowingState(Auth::guard('web')->user()->id,$enemy_id);
            }
            $myData = User::find(Auth::guard('web')->user()->id);
            return view('User.pages.members',compact('page','page_members', 'myData', 'admins', 'accepteds', 'myState', 'isAdmin', 'related_pages'));

        }
        else
        {
            return view('User.pages.members',compact('page','page_members', 'admins', 'accepteds', 'myState', 'isAdmin', 'related_pages'));
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

    public function frirndshipPage(Request $request){
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

    public function followingPage(Request $request){
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
        $page_id = $request->page_id;

        switch($requestType){
            case 'addAdmin':
                $current_member = PageMember::where('page_id',$page_id)->where('user_id',$enemy_id)->get();
                $current_member = PageMember::find($current_member[0]->id);
                $current_member->update([
                    'isAdmin' =>1,
                    'state' =>0,
                ]);

                return 1;

            break;

            case 'removeMember':
                $current_member = PageMember::where('page_id',$page_id)->where('user_id',$enemy_id)->get();
                $current_member = PageMember::find($current_member[0]->id);
                $current_member->delete();

                return '0|' . $enemy_id ;
            break;

            case 'invite':
                $current_member = PageMember::where('page_id',$page_id)->where('user_id',$enemy_id)->get();
                if(count($current_member)>0)
                {
                    $current_member = PageMember::find($current_member[0]->id);
                    $current_member->update([
                        'state' =>3,
                    ]);
                }
                if(count($current_member) == 0)
                {
                    PageMember::create([
                        'user_id'=>$enemy_id,
                        'page_id'=>$page_id,
                        'state' =>3,
                    ]);
                }

                return '0|' . $enemy_id ;
            break;

        }
        
    }

    public function allPage()
    {
        $related_pages = Page::limit(3)->get();
        $all_pages = Page::paginate(30);
        
        return view('User.pages.allPages',compact('related_pages','all_pages'));
    }

    public function myPage()
    {
        $related_pages = Page::limit(3)->get();
        if(Auth::guard('web')->user())
        {
            $my_pages = PageMember::where('user_id',Auth::guard('web')->user()->id)->get();
            return view('User.pages.myPages',compact('related_pages','my_pages'));
        }

        return view('User.pages.myPages',compact('related_pages','all_pages'));
    }
}
