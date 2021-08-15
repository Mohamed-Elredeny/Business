<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\models\Media;
use App\models\Post;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PostsController extends Controller
{
    //
    use GeneralTrait;


    public function getSavedPosts(Request $request) {

        $token = $request->header('token');

        $user = User::where('remember_token',$token)->first();

        $saved_posts = DB::select(DB::raw('select posts.* from posts,saved_posts
                        where saved_posts.post_id = posts.id
                        AND saved_posts.user_id ='.$user->id));

        return $this->returnData(['saved_posts'],[$saved_posts]);
    }
    public function savePost(Request $request) {

        $token = $request->header('token');

        $post_id = $request->post_id;

        $user = User::where('remember_token',$token)->first();

        $flag = $request->flag;

        if($flag == 0) {

            DB::table('saved_posts')->insert([
                'post_id' => $post_id,
                'user_id' => $user->id,
            ]);

            return $this->returnSuccessMessage('you have saved this post', 200);
        }
        else{

            $user_post = DB::table('saved_posts')->where('post_id',$post_id)->where('user_id',$user->id)->first();

            DB::table('saved_posts')->delete($user_post->id);

            return $this->returnSuccessMessage('you have unsaved this post', 200);
        }
    }
}
