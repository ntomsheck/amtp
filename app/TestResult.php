<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TestResult extends Model
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
    
    public function newFromBuilder($attributes = array(), $connection = null)
    {
        $namespacedClass = '\App\Results\\' . ucfirst($attributes->test_name);
        
        if(!class_exists($namespacedClass)) {
            $namespacedClass = '\App\TestResult';
        }
        
        $model = new $namespacedClass;
        $model->exists = true;
        $model->setRawAttributes((array) $attributes, true);
        $model->setConnection($connection ?: $this->getConnectionName());
        $model->fireModelEvent('retrieved', false);
        
        return $model;
    }
}
