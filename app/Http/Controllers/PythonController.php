<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Python;
use App\Measurement;

class PythonController extends Controller
{
    public function server($action)
    {
       	switch ($action) {
       		case 'start':
       			$response = Python::server_start();
       			break;

       		case 'stop':
       			$response = ['status' => Python::server_stop()];
       			break;

       		case 'status':
       			$response = Python::server_status();
       			break;
       	}
        
        return response()->json($response);
  		
    }

    public function measurement($id, $duration)
    {
		$measurement = Measurement::findOrFail($id);

		if ($measurement->status == "running") 
			return;

		$measurement->status = "running";	
    	$measurement->save();
    	
		$response = Python::measurement_start($id, $duration);
	
        return response()->json($response);
    }
}
