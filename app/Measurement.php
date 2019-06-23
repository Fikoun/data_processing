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
    	return $this->hasMany(Data::class)->where('type', '=', 'temp')->orderBy('created_at', 'asc');
    }

    public function layer()
    {
    	return $this->hasMany(Data::class)->where('type', '=', 'layer')->orderBy('created_at', 'asc');
    }

    public function press()
    {
    	return $this->hasMany(Data::class)->where('type', '=', 'press')->orderBy('created_at', 'asc');
    }

    public function volt()
    {
        return $this->hasMany(Data::class)->where('type', '=', 'volt');
    }
}
