<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Bathymetries;

class ApiController extends Controller
{
	private $formats = ['xyz', 'csv'];

    public function getBathymetries(Request $request){
    	$start = microtime(true);
    	$format = $request->get('format');
    	$coords = $request->get('coords');
    	$coordinates = array();
    	//Repetir la primera coordenada en el ultimo lugar para cerrar el polygono.
    	array_push($coords, $coords[0]);
    	for($i=0;$i<count($coords);$i++){
    		$set = array();
    		$pair = explode(",", $coords[$i]);
    		foreach($pair as $p){
    			array_push($set, (double) $p);
    		}
    		array_push($coordinates, $set);
    	}
    	if(in_array(strtolower($format), $this->formats)){
    		switch($format){
    			case 'xyz':
    				$separator = " ";
    			break;
    			default:
    				$separator = ", ";
    		}
    	}
    	$bathymetries = \App\Bathymetries::whereRaw([
				'geometry' => [
					'$geoWithin' => [
						'$geometry' => [
							'type' => "Polygon",
							'coordinates' => array($coordinates)
						]
					]
				]
		])->get();
		$result = "";
		foreach($bathymetries as $b){
			$result = $result . $b->getLatitude() . $separator . $b->getLongitude() . $separator . $b->getDepth() . "<br>";
		}
		$time_elapsed_secs = microtime(true) - $start;
		return response()->json(['count' => count($bathymetries), 'time' => $time_elapsed_secs,'results' => $result]);
    	//return response()->json(['count' => count($results), 'results' => $results]);
    	//return "invalid format";
    }
}
