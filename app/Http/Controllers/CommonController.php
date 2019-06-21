<?php

namespace App\Http\Controllers;

use App\Data;
use App\Measurement;
use Illuminate\Http\Request;

class CommonController extends Controller
{
    public function home()
    {
        return view('common.home');
    }

    public function contact()
    {
    	return view('common.contact');
    }

    public function documentation()
    {
        return view('common.documentation');
    }

    public function set($type)
    {
        var_dump($type);
        var_dump(request('value'));
        if ($type == 'voltage') {
            $value = (int)request('value');
            var_dump("python set.py $value");
            var_dump(passthru("python C:/xampp/htdocs/data_processing/python/set.pyset.py $value"));
        }
    }
}
