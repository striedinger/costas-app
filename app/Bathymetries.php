<?php

namespace App;

//use Illuminate\Database\Eloquent\Model;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Bathymetries extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'batimetrias';

    public function getLatitude(){
    	return $this["geometry"]["coordinates"][0];
    }

    public function getLongitude(){
    	return $this["geometry"]["coordinates"][1];
    }

    public function getDepth(){
    	return $this["properties"]["depth"];
    }
}
