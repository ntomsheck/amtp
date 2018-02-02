<?php

namespace App\Http\Controllers;

use App\DeviceTest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->session()->has('test_id')) {
            return redirect('/test');
        }
        
        $device_list = \App\DeviceModel::pluck('model_name', 'id');
    
        return view('begin')->with('device_list', $device_list);
    }
    
    /**
     * Begin testing procedures a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function start(Request $request)
    {
        
        $validated = $request->validate([
            'mac_address' => [
                'required',
                'regex:/^[a-f0-9]{12}$/i'
            ],
            'model_id' => 'required',
            'tester_name' => 'required',
        ]);
        
        $deviceTest = tap(new \App\DeviceTest($validated))->save();
        
        $request->session()->put('test_id', $deviceTest->id);
        
        return redirect('/test');

    }
    
    public function portTest(Request $request)
    {
        if(!$testId = $this->currentTest($request))
                return redirect('/');
        
        $test = \App\DeviceTest::find($testId);
        
        return view('test', ['testCase' => $test]);
        
    }
    
    public function connectivity(Request $request)
    {
        return response()->json(['ok' => true]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\DeviceTest  $deviceTest
     * @return \Illuminate\Http\Response
     */
    public function show(DeviceTest $deviceTest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\DeviceTest  $deviceTest
     * @return \Illuminate\Http\Response
     */
    public function edit(DeviceTest $deviceTest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\DeviceTest  $deviceTest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DeviceTest $deviceTest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\DeviceTest  $deviceTest
     * @return \Illuminate\Http\Response
     */
    public function destroy(DeviceTest $deviceTest)
    {
        //
    }
    
    protected function currentTest(Request $request)
    {
        if(!$request->session()->has('test_id'))
            return false;
        
        return $request->session()->get('test_id');
    }
}
