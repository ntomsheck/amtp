<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeviceTestResult extends Model
{
    protected $table = 'device_test_result';
    
    public function test()
    {
        return $this->hasOne('App\Test', 'id', 'test_id')->orderBy('order');
    }
    
    public function passed()
    {
        if($this->result == true)
            return true;
        
        return false;
    }
}
