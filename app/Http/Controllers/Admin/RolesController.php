<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\models\Permission;
use App\models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $roles = Role::orderBy('id', 'desc')->Where('name','!=','super_admin')->get();
        return view('Admin.roles.index',compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $permissions = Permission::all();
        return view('Admin.roles.create',compact('permissions'));
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
            'arab_name' => ['required','min:2','max:60','not_regex:/([%\$#\*<>]+)/'],
            'eng_name' => ['required','min:2','max:60','not_regex:/([%\$#\*<>]+)/'],
            'permission' => 'required',
        ]);


        $role = Role::create([
            'name' => explode(' ',$request->input('eng_name'))[0],
            'arab_name' => $request->input('arab_name'),
            'eng_name' => $request->input('eng_name')
        ]);
        $role->syncPermissions($request->input('permission'));

        if($role)
        {
            return redirect('admin/roles')->withStatus(__('role created successfully'));
        }
        return redirect('admin/roles')->withStatus(__('something wrong happened'));
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
        $role = Role::find($id);
        $permissions = Permission::all();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
            ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
            ->all();


        return view('Admin.roles.create',compact('role','permissions','rolePermissions'));
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
            'arab_name' => ['required','min:2','max:60','not_regex:/([%\$#\*<>]+)/'],
            'eng_name' => ['required','min:2','max:60','not_regex:/([%\$#\*<>]+)/'],
            'permission' => 'required',
        ]);


        $arab_name = $request->input('arab_name');
        $eng_name = $request->input('eng_name');


        $role = Role::find($id);


        if($role) {
            $role->update(['name' => $eng_name , 'arab_name' => $arab_name , 'eng_name' => $eng_name]);;
            $role->syncPermissions($request->input('permission'));


            return redirect('/admin/roles')->withStatus(__('role successfully updated.'));
        }
        return redirect('/admin/roles')->withStatus(__('something wrong happenned'));
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
        DB::table("roles")->where('id',$id)->delete();
        return redirect('/admin/roles')->withStatus(__('role deleted successfully'));
    }
}
