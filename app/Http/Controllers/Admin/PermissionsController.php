<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\models\Permission;
use Illuminate\Http\Request;

class PermissionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $permissions = Permission::orderBy('id', 'desc')->get();
        return view('Admin.permissions.index',compact('permissions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('Admin.permissions.create');
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
        $this->validate($request, [
            'name' => 'required|unique:permissions,name',
            'arab_name' => ['required','min:2','max:60','not_regex:/([%\$#\*<>]+)/'],
            'eng_name' => ['required','min:2','max:60','not_regex:/([%\$#\*<>]+)/'],
        ]);


        $permission = Permission::create([
            'name' => $request->input('name'),
            'arab_name' => $request->input('arab_name'),
            'eng_name' => $request->input('eng_name'),
            'group_name_ar' => explode(' ', $request->input('arab_name'))[1],
            'group_name_en' => explode('-', $request->input('eng_name'))[0]
        ]);

        if($permission)
        {
            return redirect('admin/permissions')->withStatus(__('permission created successfully'));
        }
        return redirect('admin/permissions')->withStatus(__('something wrong happened'));
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
        $permission = Permission::find($id);

        if($permission)
        {
            return view('Admin.permissions.create', compact('permission'));
        }
        else
        {
            return redirect('admin/permissions')->withStatus('no permission have this id');
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
        $this->validate($request, [
            'name' => ['required','min:2','max:60','not_regex:/([%\$#\*<>]+)/'],
            'arab_name' => ['required','min:2','max:60','not_regex:/([%\$#\*<>]+)/'],
            'eng_name' => ['required','min:2','max:60','not_regex:/([%\$#\*<>]+)/'],
        ]);

        $permission = Permission::find($id);

        if($permission)
        {
            $permission->update([
                'name' => $request->input('name'),
                'arab_name' => $request->input('arab_name'),
                'eng_name' => $request->input('eng_name'),
                'group_name_ar' => explode(' ', $request->input('arab_name'))[1],
                'group_name_en' => explode('-', $request->input('eng_name'))[0]
            ]);
            return redirect('admin/permissions')->withStatus(__('permisssion updated successfully'));
        }
        return redirect('admin/permissions')->withStatus(__('something wrong happened'));
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
        $permission = Permission::findOrFail($id);

        if($permission)
        {
            $permission->delete();
            return redirect('/admin/permissions')->withStatus(__('permission successfully deleted.'));
        }
        return redirect('/admin/permissions')->withStatus(__('this id is not in our database'));
    }
}
