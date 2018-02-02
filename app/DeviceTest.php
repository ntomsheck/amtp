<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DeviceTest extends Model
{
    
    protected $table = 'device_test';
    
    protected $fillable = [
        'mac_address',
        'model_id',
        'tester_name',
    ];
    
    public function deviceModel()
    {
        return $this->hasOne('App\DeviceModel', 'id', 'model_id');
    }
    
    public function results()
    {
        return $this->hasMany('App\DeviceTestResult', 'device_test_id', 'id');
    }
    
    public function lastInterfaceNumber()
    {
        $result = $this->results()->orderBy('interface_number', 'DESC')->first();
        
        if($result)
            return $result->interface_number;
        
        return false;
    }
    
    public function lastInterfaceName()
    {
        if(!$interfaceNumber = $this->lastInterfaceNumber())
            return false;
        
        $interface = $this->deviceModel->interfaces()->where('interface_number', $interfaceNumber)->first();
        
        return $interface->interface_name;
    }
    
    public function nextInterface()
    {
        if(!$lastInterfaceNumber = $this->lastInterfaceNumber())
            $interfaceNumber = 1;
        else
            $interfaceNumber = $lastInterfaceNumber + 1;
        
        $interface = $this->deviceModel->interfaces()->where('interface_number', $interfaceNumber)->first();
        
        if($interface) {
            return ['number' => $interfaceNumber, 'name' => $interface->interface_name];
        } else {
            return false;
        }
    }
}
