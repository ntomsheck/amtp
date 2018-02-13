<?php
/* var $test \App\DeviceTest */
?>
@extends('layouts.app')
@section('content')
<style type="text/css">
    .font-weight-bold {
        font-weight: bold;
    }
    .throughput-panel{
        margin-top:2em;
    }
    .throughput-test{
        position:relative;
        box-sizing:border-box;
        width:16em;
        height:12.5em;                

    }
    .throughput-test .name {
        position:absolute;
        top:0.1em; left:0;
        width:100%;
        text-align: center;
        z-index:9;
    }
    .throughput-test .result {
        position:absolute;
        bottom:1.55em; left:0;
        text-align: center;                
        width:100%;
        font-size:2.5em;
        z-index:9;
    }
    .throughput-test .result:empty:before{
        content:"0.00";
    }
    .throughput-test .unit{
        position:absolute;
        bottom:2em; left:0;
        text-align: center;
        width:100%;
        z-index:9;
    }
    .throughput-test canvas{
        position:absolute;
        top:0; left:0; width:100%; height:100%;
        z-index:1;
    }
/*	div.testGroup{
		display:inline-block;
	}*/
</style>
<div class="container">
    <div class="row">
        <div class="col-xs-12">
            <h1>Test Results</h1>
            <p class="h5">Model: {{ $test->device->model_name }}</p>
            <p class="h5">MAC Address: {{ $test->mac_address }}</p>
            <p class="h5">Test performed by: {{ $test->username }}</p>
        </div>
    </div>
    <div class="row">
    @foreach($results as $interface => $tests)        
        <div class="col-xs-6">
            <p class="h4">{{ strtoupper($interface) }}</p>
            @foreach($tests as $name => $details)
            <p class="h5"><strong>{{ strtoupper($name) }}: {{ (($details->success) ? 'pass' : 'fail') }}</strong></p>
            <ul>
                @foreach($details as $property => $value)
                @if ($property != "success")
                <li class="h5">{{ $property }}: {{ $details->$property }}</li>
                @endif
                @endforeach
            </ul>
            @endforeach
        </div>
    @endforeach    
    </div>
    
</div>

@endsection