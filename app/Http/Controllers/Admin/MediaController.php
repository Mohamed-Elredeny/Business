<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function imageUpload()
    {
        //return view('dashboard.categories.image');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function imageUploadPost(Request $request)
    {
        $uploadType = $request->input('requestType');
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $imageName = time().'.'.$request->image->extension();
        switch ($uploadType){
            case 'categories':
                $request->image->move(public_path('images/categories/'), $imageName);
            break;
            case 'users':
                $userId = $request->input('userId');
                $request->image->move(public_path('images/users/'.$userId), $imageName);
                break;
            default:
                $request->image->move(public_path('images/'), $imageName);
            break;
        }

        return back()
            ->with('success','You have successfully upload image.')
            ->with('image',$imageName);

    }
}
