<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\Media;
use App\Models\Report;
use App\Models\Story;
use App\User;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StoryController extends Controller
{
    use GeneralTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
        $user = auth()->user();
        $cover_file_to_store = null;
        $rules = [
            'body' => ['nullable'],
            'privacy_id' => 'required|integer',
            'media' => 'nullable',
            'media.*' => ['mimes:mpeg,ogg,mp4,webm,3gp,mov,flv,avi,wmv,ts,jpg,jpeg,png,svg,gif','max:100040'],
        ];

        $validator = Validator::make($request->all(),$rules);

        if ($validator->fails()) {
            return response()->json([
                'msg' => $validator->errors()->first()
            ],402);
        }


        if($request->hasFile('media')){

            $image_ext = ['jpg', 'png', 'jpeg', 'svg', 'gif','JPG'];

            $files = $request->file('media');

            foreach ($files as $file) {

                $story = Story::create([
                    'body' => $request->body,
                    'privacyId' => $request->privacy_id,
                    'publisherId' => $user->id,
                ]);

                $fileextension = $file->getClientOriginalExtension();

                if (in_array($fileextension, $image_ext)) {
                    $mediaType = 'image';
                } else {
                    $mediaType = 'video';
                }

                $filename = $file->getClientOriginalName();
                $file_to_store = time() . '_' . explode('.', $filename)[0] . '_.' . $fileextension;

                if($file->move('media', $file_to_store)) {
                    Media::create([
                        'filename' => $file_to_store,
                        'mediaType' => $mediaType,
                        'model_id' => $story->id,
                        'model_type' => "story"
                    ]);
                }
            }
        }
        else{
            $story = Story::create([
                'body' => $request->body,
                'privacyId' => $request->privacy_id,
                'publisherId' => $user->id,
            ]);
        }

        $user_stories = DB::table('stories')->where('publisherId',$user->id)->get();

        $user_stories->publisher = User::find($user->id);

        foreach ($user_stories as $inner_story){
            $inner_story->viewers = DB::select(DB::raw('select users.* from users,stories_views
                        where stories_views.story_id =' . $inner_story->id .
                ' AND stories_views.user_id = users.id'));
            $inner_story->media = DB::table('media')->where('model_id', $inner_story->id)->where('model_type', 'story')->first();
        }

        $story = $user_stories;

        $view = view('includes.partialstory', compact('story'));

        $sections = $view->renderSections(); // returns an associative array of 'content', 'pageHeading' etc

        return $sections['story'];
    }


    public function viewStory(Request $request)
    {
        //
        $user = auth()->user();

        $story = Story::find($request->story_id);

        if($user->id != $story->publisherId) {
            $story_viewed_before = DB::table('stories_views')
                ->where('story_id', $request->story_id)->where('user_id', $user->id)->exists();

            if ($story_viewed_before == false) {
                $story_created = DB::table('stories_views')->insert([
                    'story_id' => $request->story_id,
                    'user_id' => $user->id,
                ]);
            } else {
                $story_created = $story_viewed_before;
            }
        }

        if($story_created){
            return $this->returnSuccessMessage('success');
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
    public function update(Request $request, $story_id)
    {
        //
        $story = Story::find($story_id);

        $user = auth()->user();

        $rules = [
            'body' => 'required','min:10','not_regex:/([%\$#\*<>]+)/',
            'privacy_id' => 'required|integer',
            'media' => 'nullable',
            'media.*' => 'mimes:mpeg,ogg,mp4,webm,3gp,mov,flv,avi,wmv,ts,jpg,jpeg,png|max:100040',
        ];

        $this->validate($request,$rules);

        if($story){

            $story->update([
                'body' => $request->body,
                'privacyId' => $request->privacy_id,
                'publisherId' => $user->id,
            ]);

            if($request->hasFile('images')){

                $image_ext = ['jpg', 'png', 'jpeg'];

                $video_ext = ['mpeg', 'ogg', 'mp4', 'webm', '3gp', 'mov', 'flv', 'avi', 'wmv', 'ts'];

                $files = $request->file('media');

                foreach ($files as $file) {

                    $story_media = DB::table('media')->where('model_id',$request->story_id)->get();

                    foreach ($story_media as $media){
                        $media->delete();
                        unlink('media/' . $media->filename);
                    }

                    $fileextension = $file->getClientOriginalExtension();

                    if (in_array($fileextension, $image_ext)) {
                        $mediaType = 'image';
                    } else {
                        $mediaType = 'video';
                    }

                    $filename = $file->getClientOriginalName();
                    $file_to_store = time() . '_' . explode('.', $filename)[0] . '_.' . $fileextension;

                    if($file->move('media', $file_to_store)) {
                        Media::create([
                            'filename' => $file_to_store,
                            'mediaType' => $mediaType,
                            'model_id' => $request->model_id,
                            'model_type' => "story"
                        ]);
                    }
                }

            }

            if($request->has('checkedimages')){

                $post_media = [];

                foreach ($post_media as $media){
                    $post_media = $media->filename;
                }

                $checkedimages = $request->input('checkedimages');

                $deleted_media = array_diff($post_media, $checkedimages);

                if (!empty($deleted_media)) {
                    foreach ($deleted_media as $media) {
                        DB::table('media')->where('filename',$media)->delete();
                        unlink('product_images/' . $media);
                    }
                }
            }

            $story->publisher = User::find($story->publisherId);
            $story->viewers = DB::select(DB::raw('select users.* from users,stories_views
                        where stories_views.story_id ='. $story->id.
                ' AND stories_views.user_id = users.id'));
            $story->media = DB::table('media')->where('model_id',$story->id)->where('model_type','story')->first();
            $view = view('includes.partialstory', compact('story'));

            $sections = $view->renderSections(); // returns an associative array of 'content', 'pageHeading' etc

            return $sections['story'];
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
    public function destroy($story_id)
    {
        //
        $story = Story::find($story_id);

        if($story)
        {
            $story_media = DB::table('media')->where('model_id',$story->id)->where('model_type','story')->get();

            foreach ($story_media as $media){
                DB::table('media')->where('id',$media->id)->delete();
                unlink('media/' . $media->filename);
            }
            $story->delete();

            return $this->returnSuccessMessage('story successfully deleted');
        }
        else
        {
            return $this->returnError('something wrong happened',402);
        }
    }
}
