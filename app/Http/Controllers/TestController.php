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
            'username' => 'required',
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
        
        if(!$test) {
            return $this->flushTest($request);
        }
        
        $testList = \App\Test::orderBy('order', 'ASC')->get();          
        $interfaces = $test->deviceModel->interfaces;
        
        return view('test', ['testCase' => $test, 'testList' => $testList, 'interfaces' => $interfaces]);
        
    }
    
    public function connectivity(Request $request)
    {
        //return abort(500);
        return response()->json(['success' => true]);
    }
       
    public function checkDns(Request $request)
    {
        
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: x-csrf-token');

        $host = $_SERVER['HTTP_HOST'];
        
        return response()->json(['success' => true, 'hostname' => $host]);
    }
    
    public function throughputDown(Request $request)
    {
        // Disable Compression
        @ini_set('zlib.output_compression', 'Off');
        @ini_set('output_buffering', 'Off');
        @ini_set('output_handler', '');
        // Headers
        header('HTTP/1.1 200 OK');
        // Download follows...
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=random.dat');
        header('Content-Transfer-Encoding: binary');
        // Never cache me
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        // Generate data
        $data=openssl_random_pseudo_bytes(1048576);
        // Deliver chunks of 1048576 bytes
        $chunks=isset($_GET['ckSize']) ? intval($_GET['ckSize']) : 4;
        if(empty($chunks)){$chunks = 4;}
        if($chunks>100){$chunks = 100;}
        for($i=0;$i<$chunks;$i++){
            echo $data;
            flush();
        }        
    }
    
    public function throughputGetIP()
    {
        header('Content-Type: text/plain; charset=utf-8');
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            echo $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['X-Real-IP'])) {
            echo $_SERVER['X-Real-IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            echo $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            echo $_SERVER['REMOTE_ADDR'];
        }
    }
    
    public function throughputEmpty()
    {
        header( "HTTP/1.1 200 OK" );
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header("Connection: keep-alive");
        
    }
    
    public function cancelTest(Request $request)
    {
        if(!$testId = $this->currentTest($request)) {
            return abort(403);
        }
        
        $request->session()->flush();
        
        
        $deviceTest = \App\DeviceTest::find($testId);
        if($deviceTest->delete()) {
            return response()->json(['success' => true]);
        } else {
            abort(401);
        }
        
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
        if(!$testId = $this->currentTest($request)) {
            return abort(403);
        }
        
        $test = \App\DeviceTest::find($testId);
        
        //clean slate
        $test->results()->delete();
            
        $results = $request->input('test_results')['interfaces'];
        
        foreach($results as $interface => $interfaceTests) {
            foreach($interfaceTests as $test => $testResults){
                
                $result = new \App\TestResult();
                $result->device_test_id = $testId;
                $result->interface = $interface;
                $result->test_name = $test;
                $result->result = json_encode($testResults);
                if(!$result->save()) {
                    return response()->json(['success' => false, 'message' => "Could not save test results for " . $test]);
                }
                
            }
            
        }
        
        return response()->json(['success' => true]);   
        
    }
    
    public function complete(Request $request)
    {
        if(!$testId = $this->currentTest($request)) {
            return abort(403);
        }
        
        $test = \App\DeviceTest::find($testId);

        //prevents people from accessing this page prematurely
        if(!$test->isComplete()) {
            redirect('/test');
        }
        //update the timestamp in the database
        $test->save();
        
        //flush the test so they can start another after viewing results
        $request->session()->flush();
        
        return redirect()->route('test.results', ['id' => $testId]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\DeviceTest  $deviceTest
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        if(!$testId = $this->currentTest($request)) {
            if(!$testId = $request->input('id')) {
                return abort(403);
            }
        } else {
            
        }
        
        $test = \App\DeviceTest::find($testId);
        
        $results = $test->results;
        
        $testResults = [];
        
        foreach($results as $result) {
            $testResults[$result->interface][$result->test_name] = json_decode($result->result);
        }
        
        return view('results', ['test' => $test, 'results' => $testResults]);
        
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
    
    protected function flushTest(Request $request)
    {
        if(!$request->session()->has('test_id'))
            return false;
                
        $request->session()->flush();
        
        return redirect('/');
    }
}
