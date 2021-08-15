<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CitiesController extends Controller
{
    public function getCities(){
        $string = file_get_contents(asset('assets/countries.json'));
        return $string;
        $json_file = json_decode($string, true);
        return $json_file;
    }
}
