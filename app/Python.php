<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Python extends Model
{
    public static function server_start()
    {
        system("python C:/xampp/htdocs/data_processing/python/server.py");
        return true;
    }

    public static function server_stop()
    {
    	$url = "http://127.0.0.1:8080/stop";
		file_get_contents($url);
		return true;
    }

    public static function server_status()
    {
    	$url = "http://127.0.0.1:8080/status";
    	if(@get_headers($url)){
			return json_decode(file_get_contents($url), true);
    	}
    	return ['status' => false];
    }
}
