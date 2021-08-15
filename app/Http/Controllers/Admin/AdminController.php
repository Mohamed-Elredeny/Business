<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        $admins = User::where("type", "=", 1)
			->select("users.*", "states.name AS state")
			->join("states", "users.stateId", "=", "states.id")
			->get();

        return view("admin.admins.index", compact("admins"));
    }


    public function create()
    {
		$states = DB::Table("states")->get();
		return view('Admin.admins.create', compact("states"));
	}

    public function store(Request $request)
    {
		$rules = $this->getRules();

		$this->validate( $request, $rules );

		$admin = User::create([
			"name" => $request->name,
			"email" => $request->email,
			"stateId" => $request->state,
			"password" => Hash::make($request->getPassword()),
			"type" => 1,
			"phone" => $request->phone,
		]);

		return redirect("admin/admins");
    }

    public function show($id)
    {
		$admin = User::select("users.*", "states.name AS state")
			->join("states", "users.stateId", "=", "states.id")
			->where("users.id", "=", $id)
			->firstOrFail();

		return view("admin.admins.show", compact("admin"));
	}

    public function edit($id)
    {
		$states = DB::Table("states")->get();
		$admin = User::findOrFaIL($id);

		return view("Admin.admins.create", compact("states", "admin"));
	}

    public function update(Request $request, $id)
    {
		$rules = $this->getRules();

		$admin = User::findOrFail($id);

		$admin->name = $request->name;
		$admin->email = $request->email;
		$admin->stateId = $request->state;
		$admin->phone = $request->phone;

		$admin->save();

		return redirect("admin/admins");
	}

    public function destroy($id)
    {
		$admin = User::findOrFail($id);
		if ( $admin ) {
			$admin->delete();
		}

		redirect("admin/admins");
	}

	private function getRules() {
		return [
جججج
		];
	}

}
