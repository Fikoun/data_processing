<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Python;


class PythonController extends Controller
{
    public function action($action)
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
}
