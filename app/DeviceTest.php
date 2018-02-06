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
    
    public function currentInterface()
    {
        if(!$lastInterfaceNumber = $this->lastInterfaceNumber())
            return 1;
        
        $testsCompleted = $this->getTestsForInterface($lastInterfaceNumber);
               
        $lastTest = $testsCompleted->first();
                
        $nextTest = $this->getNextTest($lastInterfaceNumber, $lastTest->id);
        
        if($nextTest)
            return $lastInterfaceNumber;
        elseif($nextInterface = $nextInterface = $this->nextInterface())
            return $nextInterface['number'];
        

        return false;
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
    
    public function getTestsForInterface($interface)
    {
        $results = DeviceTestResult::select(\DB::raw('*'))
                ->join('test', 'device_test_result.test_id', '=', 'test.id')
                ->where('device_test_result.interface_number', $interface)
                ->orderBy('order', 'DESC')
                ->get();
        
        return $results;
    }
    
    public function getNextTest($interface, $lastTestId = null)
    {
        if(!is_null($lastTestId)) {
            $lastTest = Test::find($lastTestId);
            $orderStart = $lastTest->order;
        } else {
            $orderStart = 0;
        }
                
        $nextTest = Test::where('order', '>', $orderStart)->first();
                
        return $nextTest;
    }
    
    
    
    
}
