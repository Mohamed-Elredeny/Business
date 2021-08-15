<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Phone;
use Illuminate\Http\Request;
use App\Models\Company;
use Illuminate\Support\Facades\DB;

class CompaniesController extends Controller
{
    public function index()
    {
		$companies = Company::select("packaging_companies.*", "states.name AS state")
			->join("states", "packaging_companies.stateId", "=", "states.id")
			->get();

		return view('Admin.companies.index',compact('companies'));
	}

    public function create()
    {
		$states = DB::Table("states")->get();
		return view('Admin.companies.create', compact("states"));
    }

    public function store(Request $request)
    {
        $rules = $this->getRules();
		$this->validate( $request, $rules );

		$file_to_store = $this->handleImageAndGetFileToStore( $request, "image" );

		$company = Company::create([
			"name" => $request->name,
			"details" => $request->details,
			"stateId" => $request->state,
			"image" => $file_to_store
		]);

		$companyId = $company->id;

		$phones = [ $request->phone1, $request->phone2, $request->phone3, $request->phone4 ];

		foreach ($phones as $phoneNumber) {
			$phone = Phone::create([
				"packaging_company_id" => $companyId,
				"phoneNumber" => $phoneNumber
			]);
		}

		return redirect('admin/companies');
	}

    public function show($id)
    {
    	$company = Company::select("packaging_companies.*", "states.name as state")
			->join("states", "packaging_companies.stateId", "=", "states.id")
			->where("packaging_companies.id" , "=", $id)
			->firstOrFail();

        return view("admin.companies.show", compact("company"));
    }

    public function edit($id)
    {
		$states = DB::Table("states")->get();
		$company = Company::select("packaging_companies.*", "states.name as state")
			->join("states", "packaging_companies.stateId", "=", "states.id")
			->where("packaging_companies.id" , "=", $id)
			->firstOrFail();
		return view('Admin.companies.create', compact("states", "company"));
    }

    public function update(Request $request, $id)
    {
		$rules = $this->getRules();
		$this->validate($request,$rules);

		$file_to_store = $this->handleImageAndGetFileToStore($request, "image");

		$company = Company::find($id);
		$company->name = $request->name;
		$company->details = $request->details;
		$company->stateId = $request->state;

		if ( $file_to_store ) {
			$company->image = $file_to_store;
		}

		$company->save();

		$companyId = $company->id;

		$phones = [ $request->phone1, $request->phone2, $request->phone3, $request->phone4 ];
		$phones = array_diff( array_unique(  $phones ), ['', " "] );

		$companyPhones = Phone::where("packaging_company_id", "=", $companyId)->delete();

		foreach ($phones as $phoneNumber) {
			$phone = Phone::create([
				"packaging_company_id" => $companyId,
				"phoneNumber" => $phoneNumber
			]);
		}

		return redirect('admin/companies');
	}

    public function destroy($id)
    {
        $company = Company::findOrFail($id);
        if ($company) {
			$company->delete();
		}
        redirect("admin/companies");
    }

    private function getRules() {
    	return [
    		"name" => ['required','min:2','max:60','not_regex:/([%\$#\*<>]+)/'],
			"details" => ['required','min:2','max:255','not_regex:/([%\$#\*<>]+)/']
			// "image" => ['required', "image", "mimes:jpeg,png,jpg", "max:2048"],
		];
	}

	private function handleImageAndGetFileToStore( $request,$key ) {
    	if ( $request->file( $key ) ) {
			$image = $request->file( $key );
			$filename = $image->getClientOriginalName();
			$fileExtension = $image->getClientOriginalExtension();
			$file_to_store = time() . '' . explode('.', $filename )[0] . '.' . $fileExtension;

			$image->move( 'public/assets/images/companies', $file_to_store );

			return $file_to_store;
		} else {
    		return false;
		}
	}
}
