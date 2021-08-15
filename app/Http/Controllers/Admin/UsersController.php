<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function index()
    {
    	$users = User::select("users.*", "states.name AS state")
			->join("states", "users.stateId", "=", "states.id")
			->get();

    	return view("Admin.users.index", compact("users"));

    }

    public function create()
    {
		$states = DB::Table("states")->get();
		return view('Admin.users.create', compact("states"));
    }

    public function store(Request $request)
    {
		// $rules = $this->getRules();

		$file_to_store = $this->handleImageAndGetFileToStore($request, "image");
		//	dd($request);
		$user = User::create([
			"name" => $request->name,
			"email" => $request->email,
			"stateId" => $request->state,
			"password" => Hash::make($request->getPassword()),
			"birthDate" => $request->birthdate,
			"type" => 0,
			"phone" => $request->phone,
			"jobTitle" => $request->jobTitle,
			"country" => $request->country,
			"city" => $request->city,
			"gender" => $request->gender,
			"image" => $file_to_store
		]);

		return redirect("admin/users");
    }

    public function show($id)
    {
		$user = User::select("users.*", "states.name AS state")
			->join("states", "users.stateId", "=", "states.id")
			->where("users.id", "=", $id)
			->firstOrFail();

		return view("admin.users.show", compact("user"));
    }

    public function edit($id)
    {
		$states = DB::Table("states")->get();
		$user = User::findOrFaIL($id);

		return view("Admin.users.create", compact("states", "user"));
    }

    public function update(Request $request, $id)
    {
        $rules = $this->getRules();

        $file_to_store = $this->handleImageAndGetFileToStore($request, "image");

        $user = User::findOrFail($id);
        $user->name = $request->name;

		$user->email = $request->email;
		$user->stateId = $request->state;
		$user->birthDate = $request->birthdate;
		$user->phone = $request->phone;
		$user->jobTitle = $request->jobTitle;
		$user->country = $request->country;
		$user->city = $request->city;
		$user->gender = $request->gender;

		if ( isset( $user->password ) && !empty( $user->password ) ) {
			$user->password = Hash::make($request->getPassword());
		}

		if ( $file_to_store ) {
			$user->image = $file_to_store;
		}

		$user->save();

		return redirect("admin/users");
    }

    public function destroy($id)
    {
		$user = User::find($id);
		if ($user) {
			$user->delete();
		}
		redirect("admin/users");
    }

	private function getRules() {
		return [
			"name" => ['required','min:2','max:60','not_regex:/([%\$#\*<>]+)/'],
			"phone" => ["match:/[0-9]+,/"],
			"password" => ["same:password2"],
			"email" => ["required", "email", "unique:App\Model\User,email"],
		];
	}

	private function handleImageAndGetFileToStore( $request,$key ) {
		if ( $request->file( $key ) ) {
			$image = $request->file($key);
			$filename = $image->getClientOriginalName();
			$fileExtension = $image->getClientOriginalExtension();
			$file_to_store = time() . '' . explode('.', $filename)[0] . '.' . $fileExtension;

			$image->move('public/assets/images/users', $file_to_store);

			return $file_to_store;
		} else {
			return false;
		}
	}
}
