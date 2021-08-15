<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\models\source;
use App\models\Report;
use App\models\sourceTypes;
use App\models\State;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reports = DB::select(DB::raw('select reports.*,users.name as user_name from reports,users
                        where reports.user_id = users.id ORDER BY reports.id DESC '));
        return view('Admin.reports.index',compact('reports'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($report_id)
    {
        //

        $report = Report::find($report_id);

        if($report->model_type == "post"){
            $model = Post::find($report->model_id);
            $model->publisher = User::find($model->publisherId);
            $model->media = DB::table('media')->where('model_id',$model->id)->where('model_type','post')->get();
        }
        else{
            $model = Comment::find($report->model_id);
            $model->publisher = User::find($model->user_id);
            $model->media = DB::table('media')->where('model_id',$model->id)->where('model_type','comment')->first();
        }

        return view('admin.reports.show',compact('model','report'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$report_id)
    {
        $report = Report::find($report_id);

        if($report){

            if($request->state == "accepted") {
                if($report->model_type == "post"){
                    $post = Post::find($report->model_id);

                    $post_media = DB::table('media')->where('model_id',$post->id)->where('model_type','post')->get();

                    foreach ($post_media as $media){
                        DB::table('media')->where('id',$media->id)->delete();
                        unlink('media/' . $media->filename);
                    }

                    $post->delete();
                }
                else{
                    $comment = Comment::find($report->model_id);
                    $comment_media = DB::table('media')->where('model_id',$comment->id)->where('model_type','comment')->get();

                    foreach ($comment_media as $media){
                        DB::table('media')->where('id',$media->id)->delete();
                        unlink('media/' . $media->filename);
                    }
                    $comment->delete();
                }

                $report->delete();
            }


            $report->update([
                'state' => $request->state
            ]);

            return redirect()->route('reports.index')->withStatus('report state updated successfully');
        }
        else{
            return redirect()->route('reports.index')->withStatus('something wrong happened');
        }
    }

    public function userBan(Request $request,$report_id)
    {

        $report = Report::find($report_id);
        $user = User::find($report->user_id);

        if($user){

            $user->update([
                'stateId' => $request->ban
            ]);

            return redirect()->route('reports.show',$report)->withStatus('user banned successfully');
        }
        else{
            return redirect()->route('reports.index')->withStatus('something wrong happened');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $report = Report::find($id);

        if($report){

            $report->delete();

            return redirect()->route('reports.index')->withStatus('report deleted successfully');
        }
        else{
            return redirect()->route('reports.index')->withStatus('something wrong happened');
        }
    }
}
