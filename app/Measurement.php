<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Measurement extends Model
{
    public function data()
    {
    	return $this->hasMany(Data::class);
    }

    public function temp()
    {
    	return $this->hasMany(Data::class)->where('type', '=', 'temp');
    }

    public function layer()
    {
    	return $this->hasMany(Data::class)->where('type', '=', 'layer');
    }

    public function press()
    {
    	return $this->hasMany(Data::class)->where('type', '=', 'press');
    }

    public function volt()
    {
        return $this->hasMany(Data::class)->where('type', '=', 'volt');
    }
}
