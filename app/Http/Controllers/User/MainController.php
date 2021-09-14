<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\Comment;
use App\Models\Group;
use App\Models\Page;
use App\Models\Post;
use App\Models\Report;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class MainController extends Controller
{

    use GeneralTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $take = null, $start = null)
    {
        $user = auth()->user();

        $friends_posts = [];
        $expected_ids = [];
        $groups_posts = [];
        $pages_posts = [];
        $user_groups_ids = [];
        $user_pages_ids = [];
        $friends_info = [];
        $limit = 5;
        $offset = 0;

        $user_sponsored_posts = $this->getSponsoredPosts();

        if ($request->ajax()) {
            $limit = $take;
            $offset = $start;
        }


        // friends posts he follows and are public and in groups you are in and in pages you liked
        $friends = DB::table('friendships')->where(function ($q) use ($user) {
            $q->where('senderId', $user->id)->orWhere('receiverId', $user->id);
        })->where('stateId', 2)->get();

        $friends_ids = [];

        foreach ($friends as $friend2) {
            $friend2_id = $friend2->receiverId == $user->id ? $friend2->senderId : $friend2->receiverId;
            array_push($friends_ids, $friend2_id);
        }

        array_push($friends_ids, $user->id);

        $user_groups = DB::select(DB::raw('select groups.*,group_members.state from groups,group_members
                        where group_members.group_id = groups.id
                        AND group_members.user_id = ' . $user->id . ' and group_members.state in (2,3)'));

        $user_pages = DB::select(DB::raw('select pages.* from pages,user_pages
                        where user_pages.page_id = pages.id
                        AND user_pages.user_id = ' . $user->id));

        $user_follows = DB::select(DB::raw('select users.* from users,following
                        where following.followingId = users.id
                        AND following.followerId = ' . $user->id));

        //his posts, shares , friends posts who are followed
        $user_posts = DB::table('posts')->where('publisherId', $user->id)->where('postTypeId', 2)->where('stateId', 2)
            ->whereNull(['page_id', 'group_id'])
            ->limit($limit)
            ->offset($offset)
            ->orderBy('created_at', 'desc')->get()->toArray();

        foreach ($friends as $friend) {
            $friend_id = $friend->receiverId == $user->id ? $friend->senderId : $friend->receiverId;
            if (($key = array_search($friend_id, $friends_ids)) !== false) {
                unset($friends_ids[$key]);
            }

            $friend_followed = DB::table('following')->where('followerId', $user->id)->where('followingId', $friend_id)->exists();

            if ($friend_followed) {
                $friend_posts = DB::table('posts')->where('publisherId', $friend_id)->where('postTypeId', 2)
                    ->where('stateId', 2)->where('privacyId', 1)
                    ->limit($limit)
                    ->offset($offset)
                    ->orderBy('created_at', 'desc')->get();
                foreach ($friend_posts as $post) {
                    $post->sponsored = false;
                    if ($post->page_id == null and $post->group_id == null) {
                        $post->reported = DB::table('reports')->where('user_id', $user->id)
                            ->where('model_id', $post->id)->where('model_type', 'post')->exists();
                        if ($post->reported == false) {
                            foreach ($user_sponsored_posts as $sponsored) {
                                if ($sponsored->id == $post->id) {
                                    $post->sponsored = true;
                                }
                            }
                            if ($post->sponsored == false) {
                                array_push($friends_posts, $post);
                            }
                        }
                    }
                }
            }

            $friends_of_friend = DB::table('friendships')->where(function ($q) use ($friend_id) {
                $q->where('senderId', $friend_id)->orWhere('receiverId', $friend_id);
            })->where('stateId', 2)->whereNotIn('receiverId', $friends_ids)->whereNotIn('senderId', $friends_ids)->get();


            array_push($friends_ids, $friend_id);

            foreach ($friends_of_friend as $user_friend) {
                $friend_of_friend_id = $user_friend->receiverId == $friend_id ? $user_friend->senderId : $user_friend->receiverId;
                $friend_request = DB::table('friendships')->where(function ($q) use ($user) {
                    $q->where('senderId', $user->id)->orWhere('receiverId', $user->id);
                })->where(function ($q) use ($friend_of_friend_id) {
                    $q->where('senderId', $friend_of_friend_id)->orWhere('receiverId', $friend_of_friend_id);
                })->whereIn('stateId', [1, 3])->exists();
                if ($friend_request == false) {
                    array_push($expected_ids, $friend_of_friend_id);
                }
            }

            $friend_info = DB::table('users')->select('id', 'name', 'cover_image', 'personal_image')->where('id', $friend_id)->first();

            array_push($friends_info, $friend_info);

        }


        foreach ($user_groups as $group) {
            if ($group->stateId == 2) {
                $group_posts = DB::table('posts')->where('group_id', $group->id)->get();
                foreach ($group_posts as $post) {
                    $post->reported = DB::table('reports')->where('user_id', $user->id)
                        ->where('model_id', $post->id)->where('model_type', 'post')->exists();
                    if ($post->reported == false) {
                        $post->type = $post->post_id == null ? "post" : "share";
                        array_push($groups_posts, $post);
                    }
                }
            }
            array_push($user_groups_ids, $group->id);
        }

        foreach ($user_pages as $page) {
            $page_posts = DB::table('posts')->where('page_id', $page->id)->get();
            array_push($user_pages_ids, $page->id);
            foreach ($page_posts as $post) {
                $post->type = $post->post_id == null ? "post" : "share";
                array_push($pages_posts, $post);
            }
        }

        $expected_users = $this->getExpectedFriends($user, $expected_ids,$friends);

        $posts = $this->getPosts($limit, $offset, $user, $user_follows, $friends_posts, $user_pages, $user_groups, $user_posts, $user_sponsored_posts);

        $stories = $this->getStories($user,5,0);

        $user_interests = DB::select(DB::raw('select categories.id from categories,user_categories
                        where user_categories.categories_id = categories.id
                        AND user_categories.user_id =' . $user->id . ' and categories.type = "post"'));

        $user_interests_array = [];

        foreach ($user_interests as $interest) {
            array_push($user_interests_array, $interest->id);
        }

        $expected_posts = $this->getExpectedPosts($user, $user_interests_array);

        $expected_groups = $this->getExpectedGroups($user_interests_array, $user_groups_ids);

        $expected_pages = $this->getExpectedPages($user_interests_array, $user_pages_ids);

        $privacy = DB::table('privacy_type')->get();

        $categories = DB::table('categories')->where('type', 'post')->get();

        $times = DB::table('sponsored_time')->get();

        $reaches = DB::table('sponsored_reach')->get();

        $ages = DB::table('sponsored_ages')->get();

        $reacts = DB::table('reacts')->get();

        $cities = DB::table('cities')->get();

        $countries = DB::table('countries')->get();

        foreach ($friends_info as $info) {
            $info->name = explode(' ', $info->name)[0];
        }

        $another_comments = 'exist';


        if ($request->ajax()) {

            if (count($posts) > 0) {

                $view = view('includes.partialpost', compact('posts', 'privacy', 'categories', 'times', 'ages', 'reaches', 'reacts', 'friends_info', 'cities', 'countries', 'another_comments'));

                $sections = $view->renderSections(); // returns an associative array of 'content', 'pageHeading' etc

                return $sections['post'];
            } else {
                return response()->json([
                    'msg' => 'end'
                ]);
            }
        }


        return view('home', compact('posts', 'stories', 'expected_users', 'expected_groups', 'expected_pages', 'expected_posts', 'privacy', 'categories', 'times', 'ages', 'reaches', 'reacts', 'friends_info', 'cities', 'countries', 'another_comments'));
    }


    public function getPosts($limit, $offset, $user, $user_follows, $friends_posts, $user_pages, $user_groups, $user_posts, $user_sponsored_posts)
    {

        $pages_posts = [];

        $user_pages_ids = [];

        $follows_posts = [];

        $user_groups_ids = [];

        $groups_posts = [];

        $auth_user_posts = [];

        foreach ($user_groups as $group) {

            if ($group->stateId == 2) {
                $group_posts = DB::table('posts')->where('group_id', $group->id)
                    ->limit($limit)
                    ->offset($offset)
                    ->orderBy('created_at', 'desc')->get();
                foreach ($group_posts as $post) {
                    $post->reported = DB::table('reports')->where('user_id', $user->id)
                        ->where('model_id', $post->id)->where('model_type', 'post')->exists();
                    if ($post->reported == false) {
                        $post->sponsored = false;
                        array_push($groups_posts, $post);
                    }
                }
            }

            array_push($user_groups_ids, $group->id);
        }


        foreach ($user_pages as $page) {
            $page_posts = DB::table('posts')->where('page_id', $page->id)
                ->limit($limit)
                ->offset($offset)
                ->orderBy('created_at', 'desc')->get();
            foreach ($page_posts as $post) {
                $post->reported = DB::table('reports')->where('user_id', $user->id)
                    ->where('model_id', $post->id)->where('model_type', 'post')->exists();
                if ($post->reported == false) {
                    $post->sponsored = false;
                    array_push($pages_posts, $post);
                }
            }
            array_push($user_pages_ids, $page->id);
        }

        foreach ($user_follows as $followed_user) {
            $is_friend = DB::table('friendships')->where(function ($q) use ($user) {
                $q->where('senderId', $user->id)->orWhere('receiverId', $user->id);
            })->where(function ($q) use ($followed_user) {
                $q->where('senderId', $followed_user->id)->orWhere('receiverId', $followed_user->id);
            })->where('stateId', 2)->exists();

            if ($is_friend == false) {
                $followed_user_posts = DB::table('posts')->where('publisherId', $followed_user->id)->where('postTypeId', 2)
                    ->where('stateId', 2)->where('privacyId', 1)
                    ->limit($limit)
                    ->offset($offset)
                    ->orderBy('created_at', 'desc')->get();
                foreach ($followed_user_posts as $post) {
                    $post->sponsored = false;
                    if ($post->page_id == null and $post->group_id == null) {
                        $post->reported = DB::table('reports')->where('user_id', $user->id)
                            ->where('model_id', $post->id)->where('model_type', 'post')->exists();
                        if ($post->reported == false) {
                            foreach ($user_sponsored_posts as $sponsored) {
                                if ($sponsored->id == $post->id) {
                                    $post->sponsored = true;
                                }
                            }
                            if ($post->sponsored == false) {
                                array_push($follows_posts, $post);
                            }
                        }
                    }
                }
            }
        }

        foreach ($user_posts as $post) {
            $post->sponsored = false;
            foreach ($user_sponsored_posts as $sponsored) {
                if ($sponsored->id == $post->id) {
                    $post->sponsored = true;
                }
            }
            if ($post->sponsored == false) {
                array_push($auth_user_posts, $post);
            }
        }


        $posts = array_merge($follows_posts, $auth_user_posts, $friends_posts, $groups_posts, $pages_posts, $user_sponsored_posts);

        shuffle($posts);

        foreach ($posts as $post) {

            if ($post->mentions != null) {
                $post->edit = $post->body;
                $mentions = explode(',', $post->mentions);
                foreach ($mentions as $mention) {
                    $post->body = str_replace('@' . $mention,
                        '<span style="color: #ffc107">' . $mention . '</span>',
                        $post->body);
                }
            }
            $post->publisher = User::find($post->publisherId);
            $comments = DB::table('comments')->where('model_id', $post->id)->where('model_type', 'post')->whereNull('comment_id')
                ->limit(5)
                ->offset(0)
                ->orderBy('created_at', 'desc')
                ->get();
            $total_comments_count = DB::table('comments')->where('model_id', $post->id)->where('model_type', 'post')->count();
            $likes = DB::table('likes')->where('model_id', $post->id)->where('model_type', 'post')->get();
            $shares = DB::table('posts')->where('post_id', $post->id)->get()->toArray();
            $post->comments_count = DB::table('comments')->where('model_id', $post->id)->where('model_type', 'post')->whereNull('comment_id')
                ->count();

            $post->comments = $comments;
            $post->likes = $likes;
            $post->type = $post->post_id != null ? 'share' : 'post';

            if (count($likes) > 0) {
                $like_stat = [];
                $love_stat = [];
                $haha_stat = [];
                $sad_stat = [];
                $angry_stat = [];
                foreach ($likes as $like) {
                    $reactname = DB::select(DB::raw('select reacts.name from likes,reacts
                        where likes.reactId = reacts.id
                    AND likes.reactId = ' . $like->reactId . ' AND likes.senderId = ' . $like->senderId . ' AND
                    likes.model_id = ' . $post->id . ' AND likes.model_type = "post"
                    '));

                    $like->publisher = User::find($like->senderId);

                    $stat = '_stat';

                    array_push(${$reactname[0]->name . $stat}, $like);
                }

                $post->like_stat = $like_stat;
                $post->love_stat = $love_stat;
                $post->haha_stat = $haha_stat;
                $post->sad_stat = $sad_stat;
                $post->angry_stat = $angry_stat;
            }

            if ($post->page_id != null) {
                $post->source = "page";
                $page = DB::table('pages')->where('id', $post->page_id)->first();
                $post->page = $page;
            } elseif ($post->group_id != null) {
                $post->source = "group";
                $group = DB::table('groups')->where('id', $post->group_id)->first();
                $post->group = $group;
            } else {
                $post->source = "normal post";
            }

            if ($post->tags != null) {
                $tags_ids = explode(',', $post->tags);
                $post->tags_ids = $tags_ids;
                $tags_info = [];
                $post->tagged = false;
                foreach ($tags_ids as $id) {
                    if ($id == $user->id) {
                        $post->tagged = true;
                    }
                    $tagged_friend = User::find($id);
                    array_push($tags_info, $tagged_friend);
                }
                $post->tags_info = $tags_info;
            }

            if ($post->type == 'share') {
                $shared_post = DB::table('posts')->where('id', $post->post_id)->first();
                if ($shared_post->mentions != null) {
                    $shared_post->edit = $shared_post->body;
                    $mentions = explode(',', $shared_post->mentions);
                    foreach ($mentions as $mention) {
                        $shared_post->body = str_replace('@' . $mention,
                            '<span style="color: #ffc107">' . $mention . '</span>',
                            $shared_post->body);
                    }
                }
                $post->media = DB::table('media')->where('model_id', $post->id)->where('model_type', 'post')->get();
                $shared_post->publisher = User::find($shared_post->publisherId);
                $shared_post->media = DB::table('media')->where('model_id', $shared_post->id)->where('model_type', 'post')->get();
                if ($shared_post->page_id != null) {
                    $shared_post->source = "page";
                    $page = DB::table('pages')->where('id', $shared_post->page_id)->first();
                    $shared_post->page = $page;
                } elseif ($shared_post->group_id != null) {
                    $shared_post->source = "group";
                    $group = DB::table('groups')->where('id', $shared_post->group_id)->first();
                    $shared_post->group = $group;
                } else {
                    $shared_post->source = "normal post";
                }

                if ($shared_post->tags != null) {
                    $tags_ids = explode(',', $shared_post->tags);
                    $shared_post->tags_ids = $tags_ids;
                    $tags_info = [];
                    $shared_post->tagged = false;
                    foreach ($tags_ids as $id) {
                        if ($id == $user->id) {
                            $shared_post->tagged = true;
                        }
                        $tagged_friend = User::find($id);
                        array_push($tags_info, $tagged_friend);
                    }
                    $shared_post->tags_info = $tags_info;
                }

                $shared_post->sponsored = false;

                $post->shared_post = $shared_post;
            } else {
                $post->media = DB::table('media')->where('model_id', $post->id)->where('model_type', 'post')->get();
            }

            $post->comments->count = $total_comments_count;
            $post->likes->count = count($likes);
            $post->shares = count($shares);
            $post->share_details = [];

            if ($post->shares > 0 && $post->type == "post") {
                foreach ($shares as $share) {
                    $share->publisher = User::find($share->publisherId);
                    array_push($post->share_details, $share);
                }
            }

            $post->liked = DB::table('likes')->where('model_id', $post->id)->where('model_type', 'post')->where('senderId', $user->id)->first();

            if ($post->liked) {
                $post->user_react = DB::table('reacts')->where('id', $post->liked->reactId)->get();
            }

            $post->saved = DB::table('saved_posts')->where('post_id', $post->id)->where('user_id', $user->id)->exists();

            if ($post->comments->count > 0) {
                foreach ($post->comments as $comment) {

                    $comment->reported = DB::table('reports')->where('user_id', $user->id)
                        ->where('model_id', $comment->id)->where('model_type', 'comment')->exists();

                    $comment->type = $comment->comment_id != null ? 'reply' : 'comment';

                    if ($comment->reported == false) {

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

                        if (count($comment->likes) > 0) {
                            $like_stat = [];
                            $love_stat = [];
                            $haha_stat = [];
                            $sad_stat = [];
                            $angry_stat = [];
                            foreach ($comment->likes as $like) {
                                $reactname = DB::select(DB::raw('select reacts.name from likes,reacts
                                                    where likes.reactId = reacts.id
                                                AND likes.reactId = ' . $like->reactId . ' AND likes.senderId = ' . $like->senderId . ' AND
                                                likes.model_id = ' . $comment->id . ' AND likes.model_type = "comment"
                                                '));

                                $like->publisher = User::find($like->senderId);

                                $stat = '_stat';

                                array_push(${$reactname[0]->name . $stat}, $like);
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

                                    if (count($reply->likes) > 0) {
                                        $like_stat = [];
                                        $love_stat = [];
                                        $haha_stat = [];
                                        $sad_stat = [];
                                        $angry_stat = [];
                                        foreach ($reply->likes as $like) {
                                            $reactname = DB::select(DB::raw('select reacts.name from likes,reacts
                                                    where likes.reactId = reacts.id
                                                AND likes.reactId = ' . $like->reactId . ' AND likes.senderId = ' . $like->senderId . ' AND
                                                likes.model_id = ' . $reply->id . ' AND likes.model_type = "comment"
                                                '));

                                            $like->publisher = User::find($like->senderId);

                                            $stat = '_stat';

                                            array_push(${$reactname[0]->name . $stat}, $like);
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
                    }
                }
            }
        }

        return $posts;
    }

    public function getStories($user,$limit,$offset,$ajax_request = false)
    {

        $friends_stories = [];

        $friends = DB::table('friendships')->where(function ($q) use ($user) {
            $q->where('senderId', $user->id)->orWhere('receiverId', $user->id);
        })->where('stateId', 2)
            ->limit($limit)
            ->offset($offset)
            ->get();

        foreach ($friends as $friend) {
            $friend_id = $friend->receiverId == $user->id ? $friend->senderId : $friend->receiverId;

            $friend_stories = DB::table('stories')->where('publisherId', $friend_id)->where('privacyId', 1)->get();

            if (count($friend_stories) > 0) {
                $friend_stories->publisher = User::find($friend_id);

                array_push($friends_stories, $friend_stories);
            }
        }

        if($ajax_request == false){
            $user_stories = DB::table('stories')->where('publisherId', $user->id)->get();

            if (count($user_stories) > 0) {
                $user_stories->publisher = User::find($user->id);
            }
            $auth_user_stories = [];

            if($ajax_request == false){
                array_push($auth_user_stories, $user_stories);
            }

            $stories = array_merge($auth_user_stories, $friends_stories);
        }
        else{
            $stories = $friends_stories;
        }

        if (count($stories)){
            foreach ($stories as $story) {
                foreach ($story as $inner_story) {
                    $inner_story->viewers = DB::select(DB::raw('select users.* from users,stories_views
                            where stories_views.story_id =' . $inner_story->id .
                        ' AND stories_views.user_id = users.id'));
                    $inner_story->media = DB::table('media')->where('model_id', $inner_story->id)->where('model_type', 'story')->first();
                }
            }
        }

        return $stories;
    }


    public function getExpectedFriends($user, $expected_ids,$friends)
    {

        $expected_friends = [];

        $expected_people = array_unique($expected_ids);

        foreach ($expected_people as $id) {
            $user_id = $user->id;
            $friendship = DB::table('friendships')->where(function ($q) use ($id) {
                $q->where('senderId', $id)->orWhere('receiverId', $id);
            })->where(function ($q) use ($user_id) {
                $q->where('senderId', $user_id)->orWhere('receiverId', $user_id);
            })->whereIn('stateId', [1, 3])->first();
            if ($friendship == null) {
                $friends_of_friend_info = DB::table('users')->where('id', $id)->first();
                array_push($expected_friends, $friends_of_friend_info);
            }
        }

        foreach ($expected_friends as $friend) {
            $friend->followers = DB::table('following')->where('followingId', $friend->id)->count();
        }

        shuffle($expected_friends);

        $expected_users = array_slice($expected_friends, 0, 3);

        if (count($friends) == 0) {
            $expected_people = DB::table('users')->where('country', auth()->user()->country)->where('id', '!=', auth()->user()->id)->get();
            foreach ($expected_people as $expected_user) {
                $expected_user_id = $expected_user->id;
                $expected_user->followers = DB::table('following')->where('followingId', $expected_user->id)->count();
                $friendship = DB::table('friendships')->where(function ($q) use ($expected_user_id) {
                    $q->where('senderId', $expected_user_id)->orWhere('receiverId', $expected_user_id);
                })->where(function ($q) {
                    $q->where('senderId', auth()->user()->id)->orWhere('receiverId', auth()->user()->id);
                })->whereIn('stateId', [1, 3])->first();
                if ($friendship == null) {
                    array_push($expected_users, $expected_user);
                }
            }
        }

        return $expected_users;
    }


    public function getSponsoredPosts()
    {
        $user_sponsored_posts = [];

        $all_sponsored_posts = DB::select(DB::raw('select sponsored.gender,sponsored.created_at as sponsored_at,sponsored_time.duration,posts.*,sponsored_reach.reach,countries.name as country_name,cities.name as city_name,sponsored_ages.from,sponsored_ages.to from
                                        posts,sponsored,sponsored_reach,sponsored_ages,countries,cities,sponsored_time
                                        where sponsored.postId = posts.id and sponsored.reachId = sponsored_reach.id
                                        and sponsored.age_id = sponsored_ages.id and sponsored.country_id = countries.id
                                        and sponsored.city_id = cities.id and sponsored.timeId = sponsored_time.id ORDER BY posts.created_at DESC'));

        foreach ($all_sponsored_posts as $post) {
            $post_users = DB::table('users')->whereBetween('age', [$post->from, $post->to])
                ->where('country', $post->country_name)
                ->where('city', $post->city_name)
                ->where('gender', $post->gender)
                ->limit($post->reach)->pluck('id')->toArray();

            if (in_array(auth()->user()->id, $post_users) != false && Carbon::parse($post->sponsored_at)->addDays($post->duration) >= Carbon::today()) {
                $post->reported = DB::table('reports')->where('user_id', auth()->user()->id)
                    ->where('model_id', $post->id)->where('model_type', 'post')->exists();
                if ($post->reported == false) {
                    $post->sponsored = true;
                    array_push($user_sponsored_posts, $post);
                }
            }
        }

        return $user_sponsored_posts;
    }

    public function getExpectedPosts($user, $user_interests_array)
    {

//public posts having same interest of user
        $expected_posts = DB::table('posts')->whereIn('categoryId', $user_interests_array)->where('publisherId', '!=', $user->id)->where('privacyId', 1)->where('postTypeId', 2)->whereNull(['post_id', 'page_id', 'group_id'])->limit(3)->get();
        foreach ($expected_posts as $post) {
            $post->publisher = User::find($post->publisherId);
            $post->media = DB::table('media')->where('model_id', $post->id)->where('model_type', 'post')->get();
        }

        return $expected_posts;
    }

    public function getExpectedGroups($user_interests_array, $user_groups_ids)
    {
        $expected_groups = Group::whereIn('category_id', $user_interests_array)->whereNotIn('id', $user_groups_ids)->limit(3)->get();

        foreach ($expected_groups as $group) {
            $group_members_count = DB::select(DB::raw('select count(group_members.id) as count from group_members
                        where group_members.group_id =' . $group->id . ' and group_members.stateId = 2'
            ))[0]->count;

            $group->members = $group_members_count;
        }

        return $expected_groups;
    }

    public function getExpectedPages($user_interests_array, $user_pages_ids)
    {

        $expected_pages = Page::whereIn('category_id', $user_interests_array)->whereNotIn('id', $user_pages_ids)->limit(3)->get();

        foreach ($expected_pages as $page) {
            $page_likes_count = DB::select(DB::raw('select count(user_pages.id) as count from user_pages
                        where user_pages.page_id =' . $page->id
            ))[0]->count;

            $page->members = $page_likes_count;
        }

        return $expected_pages;
    }

    public function report(Request $request)
    {

        $user = auth()->user();

        $rules = [
            'body' => ['required'],
        ];

        $validator = Validator::make($rules, $rules);

        if ($validator->fails()) {
            return $this->returnValidationError(402, $validator);
        }

        $report = Report::create([
            'body' => $request->body,
            'user_id' => $user->id,
            'state' => "pending",
            'model_id' => $request->model_id,
            'model_type' => $request->model_type,
        ]);

        if ($report) {
            if ($request->model_type == 'comment') {
                $comment = Comment::find($request->model_id);
                $post = Post::find($comment->model_id);
                $post->comments = DB::table('comments')->where('model_id', $post->id)->where('model_type', 'post')->get();
                if ($comment->comment_id != null) {
                    $type = 'reply';
                } else {
                    $type = 'comment';
                }

                return response()->json([
                    'type' => $type,
                    'msg' => "report sent successfully",
                    'count' => count($post->comments)
                ]);
            } else {
                return response()->json([
                    'msg' => "report sent successfully",
                ]);
            }
        } else {
            return $this->returnError('something wrong happened', 402);
        }
    }

    public function loadComments(Request $request, $post_id, $limit, $offset)
    {

        $user = auth()->user();

        $post = Post::find($post_id);

        $comments = DB::table('comments')->where('model_id', $post->id)->where('model_type', 'post')->whereNull('comment_id')
            ->limit($limit)
            ->offset($offset)
            ->orderBy('created_at', 'desc')
            ->get();

        if (count($comments) > 0) {

            foreach ($comments as $comment) {

                $comment->reported = DB::table('reports')->where('user_id', $user->id)
                    ->where('model_id', $comment->id)->where('model_type', 'comment')->exists();

                $comment->type = $comment->comment_id != null ? 'reply' : 'comment';

                if ($comment->reported == false) {

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

                    if (count($comment->likes) > 0) {
                        $like_stat = [];
                        $love_stat = [];
                        $haha_stat = [];
                        $sad_stat = [];
                        $angry_stat = [];
                        foreach ($comment->likes as $like) {
                            $reactname = DB::select(DB::raw('select reacts.name from likes,reacts
                                                    where likes.reactId = reacts.id
                                                AND likes.reactId = ' . $like->reactId . ' AND likes.senderId = ' . $like->senderId . ' AND
                                                likes.model_id = ' . $comment->id . ' AND likes.model_type = "comment"
                                                '));

                            $like->publisher = User::find($like->senderId);

                            $stat = '_stat';

                            array_push(${$reactname[0]->name . $stat}, $like);
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

                                if (count($reply->likes) > 0) {
                                    $like_stat = [];
                                    $love_stat = [];
                                    $haha_stat = [];
                                    $sad_stat = [];
                                    $angry_stat = [];
                                    foreach ($reply->likes as $like) {
                                        $reactname = DB::select(DB::raw('select reacts.name from likes,reacts
                                                    where likes.reactId = reacts.id
                                                AND likes.reactId = ' . $like->reactId . ' AND likes.senderId = ' . $like->senderId . ' AND
                                                likes.model_id = ' . $reply->id . ' AND likes.model_type = "comment"
                                                '));

                                        $like->publisher = User::find($like->senderId);

                                        $stat = '_stat';

                                        array_push(${$reactname[0]->name . $stat}, $like);
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
                }
            }

            $reacts = DB::table('reacts')->get();


            $another_comments = DB::table('comments')->where('model_id', $post->id)->where('model_type', 'post')->whereNull('comment_id')
                ->limit($limit)
                ->offset($offset + 5)
                ->get();


            if (count($another_comments) > 0) {

                $view = view('includes.partialcomment', compact('post', 'comments', 'reacts', 'another_comments'));
            } else {
                $view = view('includes.partialcomment', compact('post', 'comments', 'reacts'));
            }

            $sections = $view->renderSections(); // returns an associative array of 'content', 'pageHeading' etc

            return $sections['comment'];
        } else {
            return response()->json([
                'msg' => "end"
            ]);
        }
    }

    public function loadStories(Request $request,$limit,$offset)
    {

        $user = auth()->user();

        $stories = $this->getStories($user,$limit,$offset,true);

        $another_stories = $this->getStories($user,$limit,$offset+5);


        if (count($another_stories) > 0) {

            $view = view('includes.partialstories', compact('stories'));

            $sections = $view->renderSections(); // returns an associative array of 'content', 'pageHeading' etc

            return $sections['home_stories'];
        }
        else{
            return response()->json([
                'msg' => 'end'
            ]);
        }

    }

}
