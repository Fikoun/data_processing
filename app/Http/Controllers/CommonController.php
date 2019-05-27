<?php

namespace App\Http\Controllers;

use App\Data;
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
}
