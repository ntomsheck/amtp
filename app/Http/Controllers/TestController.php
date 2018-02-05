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
        header('Access-Control-Allow-Origin: *');
        
        if(!$testId = $this->currentTest($request))
                return redirect('/');
        
        $test = \App\DeviceTest::find($testId);
        $testList = \App\Test::all();          
        $interfaces = $test->deviceModel->interfaces;
        
        return view('test', ['testCase' => $test, 'testList' => $testList, 'interfaces' => $interfaces]);
        
    }
    
    public function connectivity(Request $request)
    {
//        if(!$request->ajax()) {
//            redirect('/test');
//        }
        
        if(!$testId = $this->currentTest($request)) {
            return abort(403);
        }
        
        $deviceTest = \App\DeviceTest::find($testId);
        
        //This means that all tests are complete for the current interface yet we're trying to complete another
        if(!$interface = $deviceTest->currentInterface()) {
            abort(401, 'Consistency has somehow failed.  Please contact your administrator.');
        }
        
        $testResult = new \App\DeviceTestResult();
        $testResult->device_test_id = $testId;
        $testResult->interface_number = $interface;
        $testResult->test_id = 1;
        $testResult->result = true;
        
        if($testResult->save()) {
            return response()->json(['success' => true, 'test_id' => 1]);
        }
        
        abort(400);
    }
    
    public function dhcp(Request $request)
    {
        if(!$testId = $this->currentTest($request)) {
            return abort(403);
        }
        
        $deviceTest = \App\DeviceTest::find($testId);
        
        if(!$interface = $deviceTest->currentInterface()) {
            abort(401, 'Consistency has somehow failed.  Please contact your administrator.');
        }
        
        $testResult = new \App\DeviceTestResult();
        $testResult->device_test_id = $testId;
        $testResult->interface_number = $interface;
        $testResult->test_id = 2;
        $testResult->result = $request->input('local_ip');
        
        if($testResult->save()) {
            return response()->json(['success' => true, 'test_id' => 2]);
        }
        
        abort(400);        
    }
    
    public function routing(Request $request)
    {
        if(!$testId = $this->currentTest($request)) {
            return abort(403);
        }
        
        $deviceTest = \App\DeviceTest::find($testId);
        
        if(!$interface = $deviceTest->currentInterface()) {
            abort(401, 'Consistency has somehow failed.  Please contact your administrator.');
        }
        
        $testResult = new \App\DeviceTestResult();
        $testResult->device_test_id = $testId;
        $testResult->interface_number = $interface;
        $testResult->test_id = 3;
        $testResult->result = $request->input('success');
        
        if($testResult->save()) {
            return response()->json(['success' => true, 'test_id' => 3]);
        }
        
        abort(400);
    }
    
    public function dns(Request $request)
    {
        if(!$testId = $this->currentTest($request)) {
            return abort(403);
        }
        
        $deviceTest = \App\DeviceTest::find($testId);
        
        if(!$interface = $deviceTest->currentInterface()) {
            abort(401, 'Consistency has somehow failed.  Please contact your administrator.');
        }
        
        $testResult = new \App\DeviceTestResult();
        $testResult->device_test_id = $testId;
        $testResult->interface_number = $interface;
        $testResult->test_id = 4;
        $testResult->result = $request->input('success');
        
        if($testResult->save()) {
            return response()->json(['success' => true, 'test_id' => 4]);
        }
        
        abort(400);    
    }
    
    public function checkDns(Request $request)
    {
        
        header('Access-Control-Allow-Origin: *');

        $host = $_SERVER['HTTP_HOST'];
        
        return response()->json(['success' => true, 'hostname' => $host]);
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
