<?php

namespace App\Http\Controllers\Admin;


use App\models\Badword;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BadWordsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $badwords = Badword::orderBy('id', 'desc')->get();
        return view('Admin.badwords.index',compact('badwords'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('Admin.badwords.create');
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
        $rules = [
            'name' => ['required','min:2','max:60','not_regex:/([%\$#\*<>]+)/'],
        ];

        $this->validate($request,$rules);

        $badword = Badword::create([

            'name' => $request->name,
        ]);

        if($badword)
        {
            return redirect('admin/badwords')->withStatus('word successfully created');
        }
        else
        {
            return redirect('admin/badwords')->withStatus('something went wrong, try again');
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
        $badword = Badword::find($id);

        if($badword)
        {
            return view('Admin.badwords.create',compact('badword'));
        }
        else
        {
            return redirect('admin/badwords')->withStatus('no word have this id');
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
        //
        $rules = [
            'name' => ['required','min:2','max:60','not_regex:/([%\$#\*<>]+)/'],
        ];


        $this->validate($request,$rules);

        $badword = Badword::find($id);

        if($badword) {

            $badword->update([
                'name' => $request->name,
            ]);

            return redirect('/admin/badwords')->withStatus('word successfully updated');
        }
        else
        {
            return redirect('/admin/badwords')->withStatus('no word have this id');
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
        $badword = Badword::find($id);

        if($badword)
        {
            $badword->delete();
            return redirect('admin/badwords')->withStatus(__('word successfully deleted.'));
        }
        else
        {
            return redirect('admin/badwords')->withStatus('no word have this id');
        }
    }
}
