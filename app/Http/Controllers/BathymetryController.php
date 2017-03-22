<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BathymetryController extends Controller
{
    public function view(Request $request){
    	return view('bathymetries.view');
    }
}
