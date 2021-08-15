<?php

namespace App\Http\Controllers\Api\Posts;

use App\Http\Controllers\Controller;
use App\models\Likes;
use App\models\React;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function get_post_likes(Request $request)
    {
        $post_id = $request->post_id;
        $likes = Likes::where('model_type','post')->where('model_id',$post_id)->get();
        return count($likes);
    }
    public function reacts($model_id,$lang,$model_type){
        $reacts =  React::select('id','name_'.$lang.' AS name')->get();
        foreach($reacts as $react){
            $react_likes = Likes::select('id','reactId','senderId')->where('model_type',$model_type)->where('reactId',$react->id)->where('model_id',$model_id)->get();
            foreach($react_likes as $likes){
                $user = $this->getUserById($likes->senderId);
                $likes->user = [
                    'name'=>$user->name,
                    'personal_image'=>$user->personal_image
                ];
            }
            $react->likes =$react_likes;
            $react->likesNumber = count($react_likes);
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

    public function get_post_comments()
    {
        //count
        //likes
    }
    public function get_post_shares()
    {
        //count
        //likes
    }
}
