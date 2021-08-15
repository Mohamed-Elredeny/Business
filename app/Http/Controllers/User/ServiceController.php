<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\Category;
use App\Models\Company;
use App\models\Likes;
use App\Models\Media;
use App\Models\Phone;
use App\Models\Post;
use App\Models\React;
use App\Models\State;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Ratchet\App;

class ServiceController extends Controller
{

    use GeneralTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($category_id = null)
    {
        //
        $countries = DB::table('countries')->get();
        $categories = Category::where('type','service')->get();
        if($category_id != null) {

            $services = Post::where('postTypeId', 1)->where('categoryId',$category_id)->where('country',auth()->user()->country)->get();
            if (count($services) > 0) {
                foreach ($services as $service) {
                    $media = DB::table('media')->where('model_id',$service->id)->where('model_type','post')->get()->toArray();
                    $publisher = User::find($service->publisherId);
                    $follow = DB::table('following')->Where('followerId',2)->first();
                    $service->media = $media;
                    $service->publisher = $publisher;
                    $service->follow = $follow  ? 'true' : 'false';
                }
            }
        }
        else{
            $services = Post::where('postTypeId', 1)->where('country',auth()->user()->country)->get();
            if (count($services) > 0) {
                foreach ($services as $service) {
                    $media = DB::table('media')->where('model_id',$service->id)->where('model_type','post')->get()->toArray();
                    $publisher = User::find($service->publisherId);
                    $follow = DB::table('following')->Where('followerId',2)->first();
                    $service->media = $media;
                    $service->publisher = $publisher;
                    $service->follow = $follow  ? 'true' : 'false';
                }
            }
        }

        return view('User.services.show',compact('services','categories','category_id','countries'));
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
        $user = auth()->user();
        $rules = [
            'body' => ['required'],
            'price' => ['nullable','numeric'],
            'media' => 'nullable',
            'media.*' => 'mimes:mpeg,ogg,mp4,webm,3gp,mov,flv,avi,wmv,ts,jpg,jpeg,png,svg,gif|max:100040',
            'category_id' => 'required|integer',
            'country' => 'required|string'
        ];

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            return response()->json([
                'msg' => $validator->errors()->first()
            ],402);
        }


        $price =  $request->price != null ? $request->price : 0;


        $service = Post::create([
            'body' => $request->body,
            'price' => $price,
            'country' => $request->country,
            'privacyId' => 1,
            'postTypeId' => 1,
            'stateId' => 2,
            'publisherId' => $user->id,
            'categoryId' => $request->category_id,
        ]);

        if ($request->hasFile('media')) {

            $image_ext = ['jpg', 'png', 'jpeg', 'svg', 'gif','JPG'];

            $files = $request->file('media');

            foreach ($files as $file) {

                $fileextension = $file->getClientOriginalExtension();

                if (in_array($fileextension, $image_ext)) {
                    $mediaType = 'image';
                } else {
                    $mediaType = 'video';
                }

                $filename = $file->getClientOriginalName();
                $file_to_store = time() . '_' . explode('.', $filename)[0] . '_.' . $fileextension;


                if ($file->move('media', $file_to_store)) {
                    Media::create([
                        'filename' => $file_to_store,
                        'mediaType' => $mediaType,
                        'model_id' => $service->id,
                        'model_type' => "post"
                    ]);
                }
            }

        }

        $countries = DB::table('countries')->get();

        $media = DB::table('media')->where('model_id',$service->id)->where('model_type','post')->get()->toArray();
        $publisher = User::find($service->publisherId);
        $follow = DB::table('following')->Where('followerId',2)->first();
        $service->media = $media;
        $service->publisher = $publisher;
        $service->follow = $follow  ? 'true' : 'false';
        $categories = Category::where('type','service')->get();

        if($service){
            $view = view('includes.partialservice', compact('service','categories','countries'));

            $sections = $view->renderSections(); // returns an associative array of 'content', 'pageHeading' etc

            return $sections['service'];
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

        $service = Post::where('postTypeId',2)->where('id',$id)->first();
        $user = auth()->user();
        if( $service ) {
            $media = DB::table('media')->where('model_id',$service->id)->where('model_type','post')->get()->toArray();
            $publisher = User::find($service->publisherId);
            $follow = DB::table('following')->Where('followerId',2)->first();
            $service->media = $media;
            $service->publisher = $publisher;
            $service->follow = $follow  ? 'true' : 'false';
            return view('User.services.show',compact('service'));
        }else{
            return redirect()->route('services.show')->withStatus('no service with this id');
        }
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
    public function update(Request $request, $service_id)
    {
        //
        $service = Post::find($service_id);

        $user = auth()->user();

        $rules = [
            'body' => ['required'],
            'price' => ['nullable','numeric'],
            'media' => 'nullable',
            'media.*' => 'mimes:mpeg,ogg,mp4,webm,3gp,mov,flv,avi,wmv,ts,jpg,jpeg,png,svg,gif|max:100040',
            'category_id' => 'required|integer',
            'country' => 'required|string'
        ];

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            return $this->returnValidationError(402,$validator);
        }

        $price =  $request->price != null ? $request->price : 0;

        if($service){

            $service->update([
                'body' => $request->body,
                'price' => $price,
                'country' => $request->country,
                'privacyId' => 1,
                'postTypeId' => 1,
                'stateId' => 2,
                'publisherId' => $user->id,
                'categoryId' => $request->category_id,
            ]);

            if ($request->hasFile('media')) {

                $image_ext = ['jpg', 'png', 'jpeg', 'svg', 'gif','JPG'];

                $files = $request->file('media');

                foreach ($files as $file) {

                    $fileextension = $file->getClientOriginalExtension();

                    if (in_array($fileextension, $image_ext)) {
                        $mediaType = 'image';
                    } else {
                        $mediaType = 'video';
                    }

                    $filename = $file->getClientOriginalName();
                    $file_to_store = time() . '_' . explode('.', $filename)[0] . '_.' . $fileextension;


                    if ($file->move('media', $file_to_store)) {
                        Media::create([
                            'filename' => $file_to_store,
                            'mediaType' => $mediaType,
                            'model_id' => $service->id,
                            'model_type' => "post"
                        ]);
                    }
                }

            }

            if ($request->has('checkedimages')) {

                $medias = [];

                $post_media = DB::table('media')->where('model_id', $service_id)->get()->toArray();

                foreach ($post_media as $media){
                    array_push($medias,$media->filename);
                }

                $checkedimages = $request->input('checkedimages');


                $deleted_media = array_diff($medias, $checkedimages);


                if (!empty($deleted_media)) {
                    foreach ($deleted_media as $media) {
                        DB::table('media')->where('filename', $media)->delete();
                        unlink('media/' . $media);
                    }
                }
            }

            $countries = DB::table('countries')->get();

            $media = DB::table('media')->where('model_id',$service->id)->where('model_type','post')->get()->toArray();
            $publisher = User::find($service->publisherId);
            $follow = DB::table('following')->Where('followerId',2)->first();
            $service->media = $media;
            $service->publisher = $publisher;
            $service->follow = $follow  ? 'true' : 'false';
            $categories = Category::where('type','service')->get();


            $view = view('includes.partialservice', compact('service','categories','countries'));

            $sections = $view->renderSections(); // returns an associative array of 'content', 'pageHeading' etc

            return $sections['service'];
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
    public function destroy($service_id)
    {
        //
        $service = Post::find($service_id);

        if($service)
        {
            $service_media = DB::table('media')->where('model_id',$service->id)->where('model_type','post')->get();

            foreach ($service_media as $media){
                DB::table('media')->where('id',$media->id)->delete();
                unlink('media/' . $media->filename);
            }

            $service->delete();

            return $this->returnSuccessMessage('service successfully deleted');
        }
        else
        {
            return $this->returnError('something wrong happened',402);
        }
    }

    public function getCategories() {
        $categories = Category::where('type','service')->get();
        return view('User.services.index',compact('categories'));
    }

//    public function follow(Request $request) {
//
//        $followerId = $request->followerId;
//        $followingId = $request->followingId;
//
//        $following = DB::table('following')->insert([
//            'followerId' => $followerId,
//            'followingId' => $followingId
//        ]);
//
//        return $this->returnSuccessMessage('you are now following this user','');
//    }
//
//    public function postLikes($postId){
//        $reacts = React::get();
//        foreach ($reacts as $react){
//            $likes  []= [$react->name => Likes::where('postId',$postId)->where('reactId',$react->id)->get()];
//        }
//        return $likes;
//    }
}
