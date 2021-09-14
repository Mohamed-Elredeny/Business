<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\models\Category;
use App\models\Group;
use App\models\GroupMember;
use App\models\Page;
use App\models\PageMember;
use App\models\UserPage;
use App\User;
use App\models\Friendship;
use App\models\Comment;
use App\models\Following;
use App\models\Likes;
use App\models\Media;
use App\models\Post;
use App\Models\Privacy;
use App\models\React;
use App\models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PageController extends Controller
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
            return $this->returnError( 'Token is invalid, User is not authenticated',404);
        }
    }
    public function unVerified($state){
        if($state == null){
            return $this->returnError('User is not verified check your email',404);
        }
    }
    #endregion
    use GeneralTrait;

    public function getPages(Request $request,$flag) {
        if($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        }else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $token = $request->token;
            if ($flag == 0) {
                $pages = Page::all();
                $count = $pages->count();
                foreach ($pages as $page) {
                    $user_page = DB::table('page_members')
                        ->where([['page_id', $page->id], ['user_id', $this->user->id]])
                        ->first();
                    $page['members_count'] = $this->likesNumber($page->id);

                    $page['profile_image'] = asset('assets/images/pages/' . $page->profile_image);
                    $page['cover_image'] = asset('assets/images/pages/' . $page->cover_image);
                    if ($user_page) {
                        $page['entered'] = 1;
                    } else {
                        $page['entered'] = 0;
                    }
                    $page->privacy = 1;

                    unset(
                        $page->created_at,
                        $page->updated_at,
                        $page->category_id,
                        $page->publisher_id,
                        $page->cover_image,
                        $page->description,
                        $page->rules
                    );

                }
            } else {
                $pages = DB::select(DB::raw('select pages.* from pages,page_members
                        where page_members.page_id = pages.id
                        AND page_members.user_id =
                        ' . $this->user->id));
                foreach ($pages as $page) {
                    $page->id = intval($page->id);
                    $user_page = DB::table('page_members')
                        ->where([['page_id', $page->id], ['user_id', $this->user->id]])
                        ->first();
                    $page->members_count = $this->likesNumber($page->id);

                    $page->profile_image = asset('assets/images/pages/' . $page->profile_image);
                    $page->cover_image = asset('assets/images/pages/' . $page->cover_image);
                    if ($user_page) {
                        $page->entered = 1;
                    } else {
                        $page->entered = 0;
                    }
                    $page->privacy = 1;

                    unset(
                        $page->created_at,
                        $page->updated_at,
                        $page->category_id,
                        $page->publisher_id,
                        $page->cover_image,
                        $page->description,
                        $page->rules
                    );
                }
                $count = DB::select(DB::raw('select count(pages.id) as count from pages,page_members
                        where page_members.page_id = pages.id
                        AND page_members.user_id =
                        ' . $this->user->id))[0]->count;
            }

            return $this->returnData(['pages', 'count'], [$pages, $count]);
        }
    }//Get All pages & My pages
    public function likePage(Request $request) {
        if($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        }else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $token = $request->token;
            $page_id = $request->page_id;
            $flag = $request->flag;
            if ($flag == 0) {
                $liked_before = PageMember::where('page_id', $page_id)->where('user_id', $this->user->id)->get();
                if (count($liked_before) > 0) {
                    $enteredId = [
                        'enteredId'=>1
                    ];
                    return $this->returnData(['user'], [$enteredId],'you have liked this  page  before');
                } else {
                    DB::table('page_members')->insert([
                        'page_id' => $page_id,
                        'user_id' => $this->user->id,
                        'isAdmin'=>0,
                        'state'=>1
                    ]);
                    $enteredId = [
                        'enteredId'=>1
                    ];
                    return $this->returnData(['user'], [$enteredId],'you have liked the page successfully');
                }

            } else {

                $user_page = DB::table('page_members')->where('page_id', $page_id)->where('user_id', $this->user->id)->get();
                foreach ($user_page as $upage) {
                    DB::table('page_members')->delete($upage->id);
                }
                $enteredId = [
                    'enteredId'=>0
                ];
                return $this->returnData(['user'], [$enteredId],'you have disliked the page successfully');
            }
        }
    }//Like & dislike
    public function likesNumber($page_id){
        $likes = PageMember::where('page_id',$page_id)->get();
        return count($likes);
    }//Likes number
    public function membersGroup($id){
        $group = Group::find($id);
        //$related_groups = $this->relatedGroups($group->category_id);
        $group_members = PageMember::where('page_id',$id)->get();
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
    public function addPage(Request $request){
        if($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        }else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $profile_image = time().'.'.$request->profile_image->extension();
            $request->profile_image->move(public_path('assets/images/pages'), $profile_image);

            $cover_image = time().'.'.$request->cover_image->extension();
            $request->cover_image->move(public_path('assets/images/pages'), $cover_image);
            $name = $request->name;
            $description = $request->description;
            $category_id  = $request->category_id;
            $publisher_id  = $this->user->id;
            $rules = $request->rules;
            $privacy = $request->privacy;
            $group= Page::create([
                'name'=>$name,
                'description'=>$description,
                'category_id'=>$category_id,
                'publisher_id'=>$publisher_id,
                'profile_image'=>$profile_image,
                'cover_image'=>$cover_image,
                'rules'=>$rules,
                'privacy'=>$privacy
            ]);
            $page_admin = DB::table('page_members')->insert([
                'page_id' => $group->id,
                'user_id' => $publisher_id,
                'isAdmin'=>1,
                'state'=>1
            ]);
            if ($group) {
                $url_profile_image = $group['profile_image'];
                $url_cover_image = $group['cover_image'];
                $group['profile_image'] = 'https://businesskalied.com/api/business/public/assets/images/pages/'.$url_profile_image;
                $group['cover_image'] = 'https://businesskalied.com/api/business/public/assets/images/pages/'.$url_cover_image;
                $group['members_count'] = $this->membersGroup($group->id);
                $group->privacy = intval($group->privacy);
                $user_group = DB::table('page_members')
                    ->where([['page_id',$group->id],['user_id',$this->user->id]])
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

                return $this->returnDataWithStatus(['page'], [$group], true,'page details');
            }
            return $this->returnDataWithStatus(['page'], [[]], false,'page details');

        }
    }
    public function updatePage(Request $request){
        if($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        }else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $group_id = $request->group_id;
            $group = Page::find($group_id);
            if(isset($request->profile_image)){
                $profile_image = time().'.'.$request->profile_image->extension();
                $request->profile_image->move(public_path('assets/images/pages'), $profile_image);
            }else{
                $profile_image = $group->profile_image;
            }
            if(isset($request->cover_image)) {
                $cover_image = time() . '.' . $request->cover_image->extension();
                $request->cover_image->move(public_path('assets/images/pages'), $cover_image);
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
            $msg = 'page updated successfully';
            return $this->returnSuccessMessage($msg,200);
        }
    }
    public function getUserById($id){
        return User::find($id);
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
            $group = Page::find($group_id);
            $url_profile_image = $group['profile_image'];
            $url_cover_image = $group['cover_image'];
            if($group->privacy == 1){
                $group->privacy = 'public';
            }elseif($group->privacy == 2){
                $group->privacy = 'private';
            }
            $group->category_id = Category::find($group->category_id);
            $group['profile_image'] = 'https://businesskalied.com/api/business/public/assets/images/pages/'.$url_profile_image;
            $group['cover_image'] = 'https://businesskalied.com/api/business/public/assets/images/pages/'.$url_cover_image;
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
                $group_members = PageMember::where('page_id', $group_id)->get();
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
                    $isAdmin = PageMember::where('page_id',$group_id)->where('user_id',$user_id)->get();
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
                return $this->returnData(['page'], [$response], 'page details');

                //return view('User.groups.index', compact('group', 'group_members', 'register', 'group_posts', 'related_groups'));
            }else{
                return $this->returnError(404,'Page not found');
            }
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
    public function membersPageWithCollections(Request $request){
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
            $pending = PageMember::where('page_id', $group_id)->where('state', 2)->get();
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

            $accepted = PageMember::where('page_id', $group_id)->where('state', 1)->where('isAdmin',0)->get();
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

            $admins = PageMember::where('page_id', $group_id)->where('state', 1)->where('isAdmin',1)->get();
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
            return $this->returnData(['page_members'], [$group_members]);
        }
    }
    public function GetPageMedia(Request $request){
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
            $posts = Post::where('page_id', $group_id)->where('postTypeId','post')->get();
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
    public function addPost(Request $request)
    {

        if($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        }else {

            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $user_id = $this->user->id;
            $group = Page::find($request->page_id);
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
    public function assignPageAdmin(Request $request)
    {
        $state = $request->state;
        $page_member_id = $request->page_member_id;

        $group_member = UserPage::find($page_member_id);
        $group_member->update([
            'isAdmin' => $state
        ]);
        $msg = 'Done  successfully';
        return $this->returnSuccessMessage($msg, 200);
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
    public function getPostshareNumber($group_id,$post_id){
        $posts = Post::where('group_id',$group_id)->where('post_id',$post_id)->get();

        return count($posts);
    }
    public function removePage(Request $request){
        $group_id =$request->group_id;
        //Posts
        //Media
        $group = Page::find($group_id);
        $posts  = Post::where('page_id',$group_id)->get();
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
        return $this->returnSuccessMessageWithStatus('Page has been successfully deleted',200,true);
    }

}
