<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Mail\Verification;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    use GeneralTrait;
    #region Check
    public $valid_token;
    public $user_verified;
    public $user;
    public function __construct(){
        if(auth('api')->user()){
            $this->valid_token =1;
            $this->user = auth('api')->user();
            $this->user_verified =  $this->user['email_verified_at'];
        }else{
            $this->valid_token =0;
        }
    }
    public function unValidToken($state){
        if($state == 0){
            return $this->returnError(401, 'Token is invalid, User is not authenticated');
        }
    }
    public function unVerified($state){
        if($state == null){
            return $this->returnError( 'User is not verified check your email',401);
        }
    }
    #endregion
    public function verifycode(Request $request)
    {

        $email = $request->email;
        $code = intval($request->code);

        try {
            $user = User::where('email', $email)->firstOrFail();
            $credentials = $request->only(['email', 'password']);
            $token = auth('api')->attempt($credentials);
        } catch (\Exception $e) {
            return $this->returnError(200, 'Client Not Found');
        }
        if ($user->verification_code == $code) {
            $user->update([
                'email_verified_at' => Carbon::now(),
                'remember_token'=>$token
            ]);
            return $this->returnData(['client'], [$user], 'the code is valid');
        } else {
            return $this->returnError(200,'this code is invalid please check the code sent to your mobile');
        }
    }
    public function login(Request $request)
    {

        $credentials = $request->only(['email', 'password']);
        $token = auth('api')->attempt($credentials);
        if ($token) {
            $user = auth('api')->user();
            if (!$user->email_verified_at) {
                return $this->unVerified($user->email_verified_at);
            }
            $msg = 'you have been logged in successfully';
            $user->update(['remember_token' => $token]);
            return $this->returnData(['client'], [$user], $msg);
        }
        return $this->returnError(200, 'These credentials are not in our records');
    }
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'min:2', 'max:60', 'not_regex:/([%\$#\*<>]+)/'],
            'email' => ['required', 'email', Rule::unique((new User)->getTable()), 'regex:/^[\w\-\.]+@([\w\-]+\.)+[\w\-]{2,3}$/'],
            'phone' => ['required', Rule::unique('users', 'phone')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'birthDate' => ['required','date'],
            'jobTitle' => ['required', 'string', 'max:255' , 'not_regex:/([%\$#\*<>]+)/'],
            'city' => ['required', 'string', 'max:255' , 'not_regex:/([%\$#\*<>]+)/'],
            'country' => ['required', 'string', 'max:255' , 'not_regex:/([%\$#\*<>]+)/'],
            'gender' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->returnValidationError(422, $validator);
        }
        if($request->personal_image) {
            $personal_image = time() . '.' . $request->personal_image->extension();
            $request->personal_image->move(public_path('assets/images/personal_image'), $personal_image);
        }else{
            $personal_image =null;
        }
        if($request->cover_image) {
            $cover_image = time() . '.' . $request->cover_image->extension();
            $request->cover_image->move(public_path('assets/images/cover_image'), $cover_image);
        }else{
            $cover_image=null;
        }
        $code =  strval(mt_rand(100000, 999999));

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'birthDate' => $request->birthDate,
            'gender' => $request->gender,
            'jobTitle' => $request->jobTitle,
            'city' => $request->city,
            'country' => $request->country,
            'type' => 0,
            'personal_image' => $personal_image,
            'cover_image' => $cover_image,
            'verification_code' => $code
        ]);

        $verification_msg = 'your verification code is : '.$code;
        $this->send_mail($user->email,$verification_msg);
        $msg = 'verification code has been sent to your email';
        $user =User::find($user->id);
        return $this->returnData(['client'], [$user], $msg);

    }

    public function send_mail($email,$msg)
    {
        $user = User::where('email', $email)->first();

        $name = $user->name;
        $email = $user->email;
        $subject = 'Buisness verification code';
        $message = $msg;

        Mail::to($user->email)->send(new Verification($name,$email,$subject,$message));
    }
    public function forgetpassword(Request $request)
    {
        $code =  strval(mt_rand(100000, 999999));

        $validator = Validator::make($request->all(), [
            'email' => ['required','email'],
        ]);

        if ($validator->fails()) {
            return $this->returnValidationError(200, $validator);
        }
        $email = $request->email;
        try {
            $user = User::where('email', $email)->firstOrFail();
            $user->update(['verification_code' => $code]);
            $verification_msg = 'your verification code is : '.$code;
            $this->send_mail($user->email,$verification_msg);
            $msg = $code;
            return $this->returnData(['client'], [$user], $msg);

        } catch (\Exception $e) {
            return $this->returnError(200, 'email Not Found');
        }

    }//Forget 1
    public function resetpassword(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'verification_code'=>['required'],
            'email' => ['required','email'],
            //'new_password'  => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return $this->returnValidationError(200, $validator);
        }

        $user = User::where('email',$request->email)->first();
        if (isset($user)) {
            if($user->verification_code  == $request->verification_code){
                //$user->update(['password' => Hash::make($request->new_password)]);
                return 1;
            }else{
                return 0;

            }
        }
        else{
            return $this->returnError(200,'Make Sure You have entered Correct Email ');
        }
    } //Forget 2
    public function newPassword(Request $request){
        $validator = Validator::make($request->all(), [
            //'verification_code'=>['required'],
            'email' => ['required','email'],
            'new_password'  => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return $this->returnValidationError(200, $validator);
        }

        $user = User::where('email', $request->email)->first();
        if (isset($user)) {
            $user->update(['password' => Hash::make($request->new_password)]);
            return $this->returnSuccessMessage(200,'user password updated');
        }

    }

    public function logout()
    {
        if($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        }else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $this->user->update(['remember_token' => null]);
            $this->guard()->logout();
            return response()->json(['message' => 'Successfully logged out']);
        }


    }
    public function delete_user(Request $request)
    {
        $email = $request->email;
        $user = User::where('email', $email)->get();
        if(count($user)>0){
            $user[0]->delete();
            return $this->returnSuccessMessage('User deleted successfully ',200);
        }else{
            return $this->returnSuccessMessage('There is no users to delete with this email',200);
        }
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        if($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        }else{
            if(!$this->user_verified){
                return $this->unVerified($this->user_verified);
            }
            return $this->respondWithToken($this->guard()->refresh());
        }
    }
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ]);
    }
    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return Auth::guard('api');
    }



}
