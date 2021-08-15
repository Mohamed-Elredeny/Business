<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\models\Page;
use App\models\UserPage;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PageController extends Controller
{
    #region Check
    public $valid_token;
    public $user_verified;
    public function __construct(){
        if(auth('api')->user()){
            $this->valid_token =1;
            $user = auth('api')->user();
            $this->user_verified = $user['email_verified_at'];
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
    public function getPages(Request $request,$flag) {
        if($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        }else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }

            $token = $request->token;
            $user = User::where('remember_token', $token)->get()[0];
            if ($flag == 0) {
                $pages = Page::all();
                $count = $pages->count();
                foreach ($pages as $page) {
                    $user_page = DB::table('user_pages')
                        ->where([['page_id', $page->id], ['user_id', $user->id]])
                        ->first();
                    $page['members_count'] = $this->likesNumber($page->id);

                    $page['profile_image'] = asset('assets/images/pages/' . $page->profile_image);
                    $page['cover_image'] = asset('assets/images/pages/' . $page->cover_image);
                    if ($user_page) {
                        $page['liked'] = 1;
                    } else {
                        $page['liked'] = 0;
                    }
                }
            } else {
                $pages = DB::select(DB::raw('select pages.* from pages,user_pages
                        where user_pages.page_id = pages.id
                        AND user_pages.user_id =
                        ' . $user->id));
                foreach ($pages as $page) {
                    $user_page = DB::table('user_pages')
                        ->where([['page_id', $page->id], ['user_id', $user->id]])
                        ->first();
                    $page->members_count = $this->likesNumber($page->id);

                    $page->profile_image = asset('assets/images/pages/' . $page->profile_image);
                    $page->cover_image = asset('assets/images/pages/' . $page->cover_image);
                    if ($user_page) {
                        $page->liked = 1;
                    } else {
                        $page->liked = 0;
                    }
                }
                $count = DB::select(DB::raw('select count(pages.id) as count from pages,user_pages
                        where user_pages.page_id = pages.id
                        AND user_pages.user_id =
                        ' . $user->id))[0]->count;
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

            $user = User::where('remember_token', $token)->get()[0];
            $flag = $request->flag;


            if ($flag == 0) {
                $liked_before = UserPage::where('page_id', $page_id)->where('user_id', $user->id)->get();
                if (count($liked_before) > 0) {
                    return $this->returnSuccessMessage('you have liked this page before', 200);

                } else {
                    DB::table('user_pages')->insert([
                        'page_id' => $page_id,
                        'user_id' => $user->id,
                        'isAdmin'=>0
                    ]);

                    return $this->returnSuccessMessage('you have liked this page', 200);
                }

            } else {

                $user_page = DB::table('user_pages')->where('page_id', $page_id)->where('user_id', $user->id)->get();
                foreach ($user_page as $upage) {
                    DB::table('user_pages')->delete($upage->id);
                }

                return $this->returnSuccessMessage('you have unliked this page', 200);
            }
        }
    }//Like & dislike
    public function likesNumber($page_id){
        $likes = UserPage::where('page_id',$page_id)->get();
        return count($likes);
    }//Likes number
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
            $user_id = User::where('remember_token',$request->token)->get();
            $name = $request->name;
            $description = $request->description;
            $category_id  = $request->category_id;
            $publisher_id  = $user_id[0]->id;
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
            $page_admin = DB::table('user_pages')->insert([
                'page_id' => $group->id,
                'user_id' => $publisher_id,
                'isAdmin'=>1
            ]);

            $msg = 'page created successfully';
            return $this->returnSuccessMessage($msg,200);
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

}
