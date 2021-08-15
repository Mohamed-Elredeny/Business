<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\Comment;
use App\Models\Post;
use App\Notifications\CommentCreated;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{

    use GeneralTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($post_id)
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //

        $post_id = $request->post_id;

        $post = Post::find($post_id);

        $user_mentions = [];

        $user = auth()->user();

        $rules = [
            'body' => ['required'],
        ];

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            return $this->returnValidationError(402,$validator);
        }

        preg_match_all("/(@\w+)/", $request->body, $mentions);

        if(count($mentions[0]) > 0) {
            foreach ($mentions[0] as $mention) {
                $user_name = str_replace('@', '', $mention);
                $user_exist = DB::table('users')->whereRaw("name like '$user_name%'")->exists();
                if($user_exist) {
                    array_push($user_mentions, $user_name);
                }
            }
        }

        $comment_mentions = implode(',',$user_mentions);

        $comment = Comment::create([
            'body' => $request->body,
            'user_id' => $user->id,
            'model_id' => $post->id,
            'model_type' => "post",
            'mentions' => $comment_mentions,
            'comment_id' => $request->comment_id
        ]);


        if($comment){

            $reacts = DB::table('reacts')->get();

            $post_comments = DB::table('comments')->where('model_id',$post->id)->where('model_type','post')->get();

            $post->comments = $post_comments;

            $comment = $this->getComment($user,$comment,$post);

            if($comment->comment_id != null) {
                $reply = $comment;
                $parent_comment = Comment::find($comment->comment_id);
                $comment = $parent_comment;
                $view = view('includes.partialreply', compact('post','comment','reply','reacts'));

                $sections = $view->renderSections(); // returns an associative array of 'content', 'pageHeading' etc

                return $sections['reply'];
            }
            else{
                $another_comments = 'exist';
                $comments[] = $comment;
                $view = view('includes.partialcomment', compact('comments','post','reacts','another_comments'));

                $sections = $view->renderSections(); // returns an associative array of 'content', 'pageHeading' etc

                return $sections['comment'];
            }
        }
        else{
            return $this->returnError('something wrong happened',402);
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $comment_id)
    {
        //
        $post = Post::find($request->model_id);

        $user = auth()->user();

        $user_mentions = [];

        $comment = Comment::find($comment_id);

        $rules = [
            'body' => ['required'],
        ];

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            return response()->json([
                'msg' => $validator->errors()->first()
            ],402);
        }

        if($comment){

            preg_match_all("/(@\w+)/", $request->body, $mentions);

            if(count($mentions[0]) > 0) {
                foreach ($mentions[0] as $mention) {
                    $user_name = str_replace('@', '', $mention);
                    $user_exist = DB::table('users')->whereRaw("name like '$user_name%'")->exists();
                    if($user_exist) {
                        array_push($user_mentions, $user_name);
                    }
                }
            }

            $comment_mentions = implode(',',$user_mentions);

            $comment->update([
                'body' => $request->body,
                'user_id' => $user->id,
                'model_id' => $request->model_id,
                'model_type' => "post",
                'mentions' => $comment_mentions
            ]);

            $reacts = DB::table('reacts')->get();

            $post_comments = DB::table('comments')->where('model_id',$post->id)->where('model_type','post')->get();

            $post->comments = $post_comments;

            $comment = $this->getComment($user,$comment,$post);

            if($comment->comment_id != null) {
                $reply = $comment;
                $parent_comment = Comment::find($comment->comment_id);
                $comment = $parent_comment;
                $view = view('includes.partialreply', compact('post','comment','reacts'));

                $sections = $view->renderSections(); // returns an associative array of 'content', 'pageHeading' etc

                return $sections['reply'];
            }
            else{
                $post = $this->getPost($user,$post);
                $another_comments = 'exist';
                $comments[] = $comment;
                $view = view('includes.partialcomment', compact('comments','post','reacts','another_comments'));

                $sections = $view->renderSections(); // returns an associative array of 'content', 'pageHeading' etc

                return $sections['comment'];
            }
        }
        else{
            return $this->returnError('something wrong happened',402);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($comment_id)
    {
        //
        $comment = Comment::find($comment_id);

        if($comment)
        {
            $comment_media = DB::table('media')->where('model_id',$comment->id)->where('model_type','comment')->get();

            foreach ($comment_media as $media){
                DB::table('media')->where('id',$media->id)->delete();
                unlink('media/' . $media->filename);
            }
            $comment->delete();
            $post = Post::find($comment->model_id);
            $post->comments = DB::table('comments')->where('model_id',$post->id)->where('model_type','post')->get();
            if ($comment->comment_id != null) {
                $type = 'reply';
            } else {
                $type = 'comment';
            }

            return response()->json([
                'type' => $type,
                'msg' => "comment deleted successfully",
                'count' => count($post->comments)
            ]);
        }
        else
        {
            return $this->returnError('something wrong happened',402);
        }
    }


    private function getComment($user,$comment,$post){

        $comment->reported = false;

        $comment->type = $comment->comment_id != null ? 'reply' : 'comment';

        if ($comment->mentions != null) {
            $comment->edit = $comment->body;
            $mentions = explode(',', $comment->mentions);
            foreach ($mentions as $mention) {
                $comment->body = str_replace('@' . $mention,
                    '<span style="color: #ffc107">' . $mention . '</span>',
                    $comment->body);
            }
        }
        $comment->publisher = User::find($comment->user_id);
        $comment->media = DB::table('media')->where('model_id', $comment->id)->where('model_type', 'comment')->first();
        $comment->replies = DB::table('comments')->where('model_id', $post->id)->where('model_type', 'post')->where('comment_id', $comment->id)->get();
        $comment->likes = DB::table('likes')->where('model_id', $comment->id)->where('model_type', 'comment')->get();
        $comment->replies->count = count($comment->replies);
        $comment->likes->count = count($comment->likes);
        $comment->liked = DB::table('likes')->where('model_id', $comment->id)->where('model_type', 'comment')->where('senderId', $user->id)->first();

        if ($comment->liked) {
            $comment->user_react = DB::table('reacts')->where('id', $comment->liked->reactId)->get();
        }

        if(count($comment->likes) > 0){
            $like_stat = [];
            $love_stat = [];
            $haha_stat = [];
            $sad_stat = [];
            $angry_stat = [];
            foreach ($comment->likes as $like){
                $reactname = DB::select(DB::raw('select reacts.name from likes,reacts
                                                    where likes.reactId = reacts.id
                                                AND likes.reactId = ' . $like->reactId . ' AND likes.senderId = '. $like->senderId . ' AND
                                                likes.model_id = '.$comment->id.' AND likes.model_type = "comment"
                                                '));

                $like->publisher = User::find($like->senderId);

                $stat = '_stat';

                array_push(${$reactname[0]->name.$stat},$like);
            }

            $comment->like_stat = $like_stat;
            $comment->love_stat = $love_stat;
            $comment->haha_stat = $haha_stat;
            $comment->sad_stat = $sad_stat;
            $comment->angry_stat = $angry_stat;
        }

        if (count($comment->replies) > 0) {
            foreach ($comment->replies as $reply) {

                $reply->reported = DB::table('reports')->where('user_id', $user->id)
                    ->where('model_id', $reply->id)->where('model_type', 'comment')->exists();

                if ($reply->reported == false) {

                    if ($reply->mentions != null) {
                        $reply->edit = $reply->body;
                        $mentions = explode(',', $reply->mentions);
                        foreach ($mentions as $mention) {
                            $reply->body = str_replace('@' . $mention,
                                '<span style="color: #ffc107">' . $mention . '</span>',
                                $reply->body);
                        }
                    }
                    $reply->publisher = User::find($reply->user_id);
                    $reply->media = DB::table('media')->where('model_id', $reply->id)->where('model_type', 'comment')->first();
                    $reply->likes = DB::table('likes')->where('model_id', $reply->id)->where('model_type', 'comment')->get();
                    $reply->likes->count = count($reply->likes);
                    $reply->liked = DB::table('likes')->where('model_id', $reply->id)->where('model_type', 'comment')->where('senderId', $user->id)->first();

                    if ($reply->liked) {
                        $reply->user_react = DB::table('reacts')->where('id', $reply->liked->reactId)->get();
                    }

                    if(count($reply->likes) > 0){
                        $like_stat = [];
                        $love_stat = [];
                        $haha_stat = [];
                        $sad_stat = [];
                        $angry_stat = [];
                        foreach ($reply->likes as $like){
                            $reactname = DB::select(DB::raw('select reacts.name from likes,reacts
                                                    where likes.reactId = reacts.id
                                                AND likes.reactId = ' . $like->reactId . ' AND likes.senderId = '. $like->senderId . ' AND
                                                likes.model_id = '.$reply->id.' AND likes.model_type = "comment"
                                                '));

                            $like->publisher = User::find($like->senderId);

                            $stat = '_stat';

                            array_push(${$reactname[0]->name.$stat},$like);
                        }

                        $reply->like_stat = $like_stat;
                        $reply->love_stat = $love_stat;
                        $reply->haha_stat = $haha_stat;
                        $reply->sad_stat = $sad_stat;
                        $reply->angry_stat = $angry_stat;
                    }
                }
            }
        }

        return $comment;
    }
}
