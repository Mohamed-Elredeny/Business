<?php

namespace App\Http\Controllers\User;

use App\Events\MessageSent;
use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index(){
        return view('User.chat.sad');
    }
    public function chats(Request $request){
       $user_id = Auth::user()->id;
       $chats =  Chat::get();
       $contacts = [];
       foreach($chats  as $chat){
          if(in_array($user_id, explode('|', $chat->contacts))){
            $contacts []= Chat::find($chat->id);
          }
       }
       return $contacts;
    }//Get All Chat Room For Current Auth User


    public function messages(Request $request){
        $chatId=1;
       $messages =  Message::where('chatId',$chatId)->orderBy('created_at','DESC')->get();
       return $messages;
    }//Get All Chat Messages

    public function addMessage(Request $request){
        $chatId=1;
     $user = Auth::user();
     $newMessage=   Message::create([
            'message'=>$request->message,
            'userId'=>Auth::user()->id,
            'chatId'=>$chatId
        ]);
        broadcast(new MessageSent($user, $newMessage))->toOthers();

        return $newMessage;
    }//Add New Meesage
}
