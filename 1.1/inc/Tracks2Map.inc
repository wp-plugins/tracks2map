<?php

class Tracks2Map
{
	public $points = array();
	public $file = '';
	
	public $tolerance = 0.005;
	
	public function __construct($file = null)
	{
		if(null != $file){
			$this->file = $file;
			$this->convert($this->file);
		}
	}

	public function convert($file) 
	{
		$this->file = $file;
		$xml = simplexml_load_file($file);
		for ( $i=0, $size=count($xml->trk->trkseg->trkpt); $i<$size; $i++ ) {
			$this->points[$i] = array(
				floatval($xml->trk->trkseg->trkpt[$i]['lat']),
				floatval($xml->trk->trkseg->trkpt[$i]['lon'])
			);
		}
		
		$this->reduced_points = $this->get_reduced();
		return $this;
	}
	
	public function get_reduced($points = array(), $tolerance = null)
	{
		if(empty($points))
			$points = $this->points;
		
		if(null == $tolerance)
			$tolerance = $this->tolerance;
		
		$reducer = new PolylineReducer( $this->points2geo($this->points) );
		$reduced = $reducer->SimplerLine($tolerance);
		return $this->geo2points($reduced);
	}
	
	public function write_json($filename = null)
	{
		if(null == $filename)
			$filename = str_replace(".gpx",".json",$this->file);
		
		$fh = fopen($filename, 'w');
		fwrite($fh, json_encode($this->points));
		return $this;
	}
	
	public function write_reduced_json($tolerance = null, $filename = null)
	{
		if(null == $filename)
			$filename = str_replace(".gpx","-reduced.json",$this->file);
		
		if(null == $tolerance)
			$tolerance = $this->tolerance;
		
		$reduced = $this->get_reduced($this->points, $tolerance);
		
		$reduced = array_map(function($point){
			return array($point->latitude, $point->longitude);
		}, $reduced);
		
		$fh = fopen($filename, 'w');
		fwrite($fh, json_encode($reduced));
		return $this;
	}
	
	public function read($filename = null)
	{
		if(null == $filename)
			$filename = $this->file;
		
		$fh = fopen($filename, 'r');
		$json = fread($fh, filesize($filename));
		
		return $json;
		$points = $this->json2points($json);
	}
	
	function points2geo($in)
	{
		$points = is_string($in) ? json_decode($in) : $in;
		
		$points = array_map(function($point){
			return (object) array(
				'latitude' => $point[0],
				'longitude' => $point[1],
			);
		}, $points);
		return $points;
	}
	
	function geo2points($points)
	{
		if(is_string($points))
			$points = json_decode($points);
		
		$points = array_map(function($point){
			return array(
				floatval($point->latitude),
				floatval($point->longitude)
			);
		}, $points);
		return $points;
	}
}


