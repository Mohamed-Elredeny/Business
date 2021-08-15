<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;

class Check extends Controller
{
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

}
