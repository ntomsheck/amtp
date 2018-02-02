<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeviceModel extends Model
{
    protected $table = 'device_model';
    
    public function deviceTest()
    {
        return $this->belongsTo('App\DeviceTest', 'model_id');
    }
    
    public function interfaces()
    {
        return $this->hasMany('App\DeviceModelInterface', 'model_id', 'id');
    }
}
