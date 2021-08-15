<?php

namespace App\Http\Controllers\Api;

//use App\Friendship;
use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\models\Category;
use App\models\Company;
use App\models\Following;
use App\models\Likes;
use App\models\Media;
use App\models\Phone;
use App\models\Post;
use App\models\React;
use App\models\State;
use App\models\userInterests;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    #region Check
    public $valid_token;
    public $user_verified;
    public $user;
    public function __construct()
    {
        if (auth('api')->user()) {
            $this->valid_token = 1;
            $this->user = auth('api')->user();
            $this->user_verified = $this->user['email_verified_at'];
        } else {
            $this->valid_token = 0;
        }
    }
    public function unValidToken($state)
    {
        if ($state == 0) {
            return $this->returnError(404, 'Token is invalid, User is not authenticated');
        }
    }
    public function unVerified($state)
    {
        if ($state == null) {
            return $this->returnError(404, 'User is not verified check your email');
        }
    }
    #endregion
    use GeneralTrait;
    public function getCompanies(Request $request)
    {
        $lang = $request->header('lang');

        $activeStateId = State::where('name', 'active')->first();
        if ($activeStateId) {
            $companies = Company::select('packaging_companies.id', 'packaging_companies.name_' . $lang .' AS name', 'packaging_companies.details_' . $lang . ' As details ', 'packaging_companies.image')
                ->where('stateId','1')->get();

            foreach ($companies as $company) {
                $phones = [];
                $company->image = asset('assets/images/companies/' . $company->image);
                $companyPhones = DB::table('packaging_companies_phones')->where('packaging_company_id', $company->id)->select('phoneNumber')->get();
                if (count($companyPhones) > 0) {
                    foreach ($companyPhones as $phone) {
                        array_push($phones, $phone->phoneNumber);
                    }
                }
                $company->phones = $phones;
            }
            $msg = 'There is ' . count($companies) . ' company';
            return $this->returnData(['companies'], [$companies], $msg);
        } else {
            $companies = [];
            $msg = 'There is ' . count($companies) . ' company';
            return $this->returnData(['companies'], [$companies], $msg);
        }
    }
    public function getCategories(Request $request)
    {
        $lang = $request->lang;
        $categories = Category::where('type', 'service')->select('categories.name_' . $lang . ' AS name', 'image', 'id')->get();
        foreach ($categories as $category) {
           // $company->image = asset('assets/images/companies/' . $company->image);
            $category->image = asset('assets/images/categories/' . $category->image);

        }
        $msg = 'There is ' . count($categories) . ' categories';
        return $this->returnData(['categories'], [$categories]);
    }
    public function getServices(Request $request)
    {
        $lang = $request->lang;
        $category_id = $request->category_id;
        if ($category_id != null) {
            $services = Post::select('*')->where('postTypeId', 'service')->where('categoryId', $category_id)->get();
            if (count($services) > 0) {
                foreach ($services as $service) {
                    $service_media = [];
                    $medias = DB::table('media')->where('model_id', $service->id)->where('model_type', 'post')->get();
                    if (count($medias) > 0) {
                        foreach ($medias as $media) {
                            array_push($service_media,  $media->filename);
                        }
                    }
                    $service->media = $service_media;
                    unset(
                        $service->created_at,
                        $service->updated_at,
                        $service->group_id,
                        $service->page_id,
                        $service->post_id,
                        $service->stateId,
                        $service->privacyId,
                    );
                    $user = $this->getUserById($service->publisherId);
                    $service->publisherId = [
                        'name'=>$user->name,
                        'personal_image'=>$user->personal_image
                    ];
                    $sad= Category::find( $service->categoryId);
                    $service->categoryId = $sad['name_'.$lang];

                }
            }
        } else {
            $services = Post::select('*')->where('postTypeId', 'service')->get();
            if (count($services) > 0) {
                foreach ($services as $service) {
                    $service_media = [];
                    $medias = DB::table('media')->where('model_id', $service->id)->where('model_type', 'post')->get();
                    if (count($medias) > 0) {
                        foreach ($medias as $media) {
                            array_push($service_media,  $media->filename);
                        }
                    }
                    $service->media = $service_media;
                    unset(
                        $service->created_at,
                        $service->updated_at,
                        $service->group_id,
                        $service->page_id,
                        $service->post_id,
                        $service->stateId,
                        $service->privacyId,

                    );
                    $user = $this->getUserById($service->publisherId);
                    $service->publisherId = [
                        'name'=>$user->name,
                        'personal_image'=>$user->personal_image
                    ];
                    $sad= Category::find( $service->categoryId);
                    $service->categoryId = $sad['name_'.$lang];

                }
            }
        }


        return $this->returnData(['services'], [$services]);
    }
    public function showService(Request $request)
    {
        $id = $request->id;
        $service = Post::select('id', 'body', 'price', 'publisherId')->where('id', $id)->first();

//        $token = $request->header('token');
//
//        $user = User::where('remember_token',$token)->first();

        if ($service) {
            $service_media = [];
            $medias = DB::table('media')->where('model_id', $service->id)->where('model_type', 'post')->get();
            $publisher = User::select('id', 'name', 'personal_image')->where('id', $service->publisherId)->first();
            $follow = DB::table('following')->Where('followerId', 4)->where('followingId', $service->publisherId)->first();
            $service->publisher = $publisher;
            $service->follow = $follow ? 'true' : 'false';
            if (count($medias) > 0) {
                foreach ($medias as $media) {
                    array_push($service_media, asset('images/' . $media->filename));
                }
            }
            $service->media = $service_media;
            return $this->returnData(['service'], [$service]);
        } else {
            return $this->returnError(422, 'no service found with this id');
        }
    }
    public function searchService(Request $request)
    {
        $lang = $request->lang;
        return $lang;
        $services = Post::where('body', 'like', '%' . $request->search . '%')->orWhere('title', 'like', '%' . $request->search . '%')->get();
        if (count($services) > 0) {
            foreach ($services as $service) {
                $service_media = [];
                $medias = DB::table('media')->where('model_id', $service->id)->where('model_type', 'post')->get();
                if (count($medias) > 0) {
                    foreach ($medias as $media) {
                        array_push($service_media,  $media->filename);
                    }
                }
                $service->media = $service_media;
                unset(
                    $service->created_at,
                    $service->updated_at,
                    $service->group_id,
                    $service->page_id,
                    $service->post_id,
                    $service->stateId,
                    $service->privacyId,

                );
                $user = $this->getUserById($service->publisherId);
                $service->publisherId = [
                    'name'=>$user->name,
                    'personal_image'=>$user->personal_image
                ];
                $sad= Category::find( $service->categoryId);
                $service->categoryId = $sad['name_'.$lang];

            }
            return $this->returnData(['service'], [$services]);
        }
    }
    public function follow(Request $request)
    {
        if ($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        } else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }
            $auth_user = intval($this->user->id);
            $followingId = $request->followingId;
            $following = DB::table('following')->insert([
                'followerId' => $auth_user,
                'followingId' => $followingId
            ]);
            return $this->returnSuccessMessage('you are now following this user', 200);
        }
    }
    public function unfollow(Request $request)
    {
        if ($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        } else {
            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }

            $auth_user = intval($this->user->id);
            $followingId = intval($request->followingId);
            //following
            //followerId
            //followingId
            $following = Following::where('followerId', $auth_user)->where('followingId', $followingId)->get();
            foreach ($following as $follow) {
                $follow->delete();
            }
            return $this->returnSuccessMessage('you are now unfollowing this user', 200);
        }

    }
    //Add Post in a group
    public function addService(Request $request)
    {

        if($this->valid_token == 0) {
            return $this->unValidToken($this->valid_token);
        }else {

            if (!$this->user_verified) {
                return $this->unVerified($this->user_verified);
            }

            // $user_id = User::where('remember_token',$request->token)->get()[0]->id;
            $user_id = $this->user->id;

            //$category = Category::find($request->group_id)->id;

            $post = Post::create([
                'body' => $request->body,
                'privacyId' => 1,
                'postTypeId' => 'service',
                'stateId' => 1,
                'publisherId' => $user_id,
                'categoryId' => $request->category_id,
                'group_id' => null,
                'page_id' =>  null,
                'post_id' =>  null,
                'price' => $request->price,
                'title' => $request->title

            ]);

            $msg = '';
            if ($post) {
                $this->media($request,$post->id);
                $msg = 'post has created successfully';
                return $this->returnSuccessMessageWithStatus($msg,200,true);
            } else {
                $msg = 'Error during creating your post';
                return $this->returnSuccessMessageWithStatus($msg,200,true);

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
                        'filename' => 'https://businesskalied.com/api/business/public/assets/'.$mediaType.'s/posts/'.$file_to_store,
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
    public function getUserById($id){
        return User::find($id);
    }
}
