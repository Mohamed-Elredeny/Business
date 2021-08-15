<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\models\Category;
use App\models\Media;
use App\models\Post;
use App\models\PostType;
use App\models\Privacy;
use App\models\State;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;


class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::orderBy('id', 'desc')->get();
        //Convert ALL Ids to Names.
        if(count($posts) > 0){
            $idsToNames = $this->HandelIds($posts);
            return view('Admin.posts.index',compact('posts','idsToNames'));
        }else{
            $idsToNames = [];
            return view('Admin.posts.index',compact('posts','idsToNames'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $arrays = $this->sendAllRecordsOfDifferentTables();
        if(count($arrays) > 0){
            return view('Admin.posts.create',compact('arrays'));
        }else{
            $arrays = [];
            return view('Admin.posts.create',compact('arrays'));

        }

    }

    #region redeny
    public function imageUploadPost(Request $request,$postId)
    {
        $images = array('jpg', 'JPG', 'png' ,'PNG' ,'jpeg' ,'JPEG');
        $videos = array('mp4', 'mov', 'mpg', 'flv','wmv');

        if($request->hasFile('image')){
            $i=0;
            foreach ($request->image as $file){
                $fileName = time().$i.'.'.$file->extension();
                $extention = $file->extension();

                $file->move(public_path('assets/images/posts/'.$postId .'/'), $fileName);
                $url = 'assets/images/posts/'.$postId.'/'.$fileName;
                if(in_array($extention, $images)){
                    $mediaType='image';
                }else{
                    $mediaType='videos';
                }

                $media = Media::create([
                    'postId'=>$postId,
                    'url'=>$url,
                    'mediaType'=>$mediaType,
                ]);
                $i++;
            }
        }

    }

    public function removeImage($id)
    {
        $media = Media::orderBy('id', 'desc')->where('postId', $id)->get();

        foreach ($media as $m) {
            //echo $m['url'] . "<br>";
            if (file_exists(public_path($m['url']))) {
                unlink(public_path($m['url']));
            }
        }

        $media->delete();



    }
    #endregion

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {

        #region AddPost
        $title= $request->title;
        $body = $request->body;
        $postTypeId = $request->postTypeId;
        $privacyId = $request->privacyId;
        $stateId  = $request->stateId;
        $publisherId = $request->publisherId;
        $categoryId = $request->categoryId;

        $post = Post::create([
            'title' => $title,
            'body'=>$body,
            'postTypeId'=>$postTypeId,
            'privacyId'=>$privacyId,
            'stateId'=>$stateId,
            'publisherId'=>$publisherId,
            'categoryId'=>$categoryId
        ]);

        #endregion
        #region AddMedia
        $this->imageUploadPost($request,$post->id);
        #endregiom

       return redirect()->back();



    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $media =  Media::orderBy('id', 'desc')->where('postId',$id)->get();

        $post[] = Post::find($id);
        $idsToNames = $this->HandelIds($post);
        return view('Admin.posts.show',compact('post','idsToNames','media'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post = Post::find($id);

        if($post)
        {
            return view('Admin.posts.create',compact('post'));
        }
        else
        {
            return redirect('admin/posts')->withStatus('no word have this id');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $rules = [
            'name' => ['required','min:2','max:60','not_regex:/([%\$#\*<>]+)/'],
        ];


        $this->validate($request,$rules);

        $currentPost = Post::find($id);

        if ($currentPost) {
            //Remove old image
            $this->removeImage($currentPost->image);
            //Add new image
            $imageName = $this->imageUploadPost($request);
        }


        // $category = $currentPost->update([
        //     'name' => $request->name,
        //     'image'=>$imageName,
        // ]);
        // if($category){
        //     return redirect('/admin/categories')->withStatus('word successfully updated');
        //    // return 'done';
        // }
        // else
        // {
        //     return redirect('/admin/categories')->withStatus('no word have this id');
        //    //return 'sad';

        // }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $posts = Post::find($id);

        if($posts)
        {
            $this->removeImage($id);
            $posts->delete();
            return redirect('admin/posts')->withStatus(__('category successfully deleted.'));
        }
        else
        {
            return redirect('admin/posts')->withStatus('no category have this id');
        }
    }

    #region redeny
    public function HandelIds($posts){
        foreach($posts as $post){
            $postTypeId = PostType::find($post->postTypeId);
            $privacyId = Privacy::find($post->privacyId);
            $stateId = State::find($post->stateId);
            $publisherId = User::find($post->publisherId);
            $categoryId  = Category::find($post->categoryId);


            $names[]=[
                'postType' => $postTypeId->name,
                'privacyId' => $privacyId->name,
                'stateId' => $stateId->name,
                'publisherId' => $publisherId->name,
                'categoryId'=>$categoryId->name
            ];
        }
        return $names;

    }
    public function getAnyRecordWithId($table , $id){
        $table = $table::find($id);
        return $table->name;
    }

    public function sendAllRecordsOfDifferentTables(){
        $PostType = PostType::orderBy('id', 'desc')->get();
        $Privacy = Privacy::orderBy('id', 'desc')->get();
        $State = State::orderBy('id', 'desc')->get();
        $User = User::orderBy('id', 'desc')->get();
        $Category = Category::orderBy('id', 'desc')->get();

        $arrays =
            [
                'PostType' => $PostType ,
                'Privacy'=>$Privacy,
                'State'=>$State,
                'User'=>$User,
                'Category'=>$Category
            ];
        return $arrays;
    }
    #endregion
}
