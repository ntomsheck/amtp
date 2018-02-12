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
        <h1>Test In Progress</h1>      
        @foreach ($testList as $test)
        <p class="test_item" id="test_{{ $test->name }}" class="h4"><span class="oi oi-loop-circular"></span>&nbsp;{{ $test->description }}</p>
        @endforeach
        </div>
    </div>
    <div class="row throughput-panel">
        <div class="col-xs-3 throughput-test">
                <div class="name">Download</div>
                <canvas id="dlMeter" class="meter"></canvas>
                <div id="dlText" class="result"></div>
                <div class="unit">Mbps</div>
        </div>
        <div class="col-xs-3 throughput-test">
                <div class="name">Upload</div>
                <canvas id="ulMeter" class="meter"></canvas>
                <div id="ulText" class="result"></div>
                <div class="unit">Mbps</div>
        </div>

        <div class="col-xs-3 throughput-test">
                <div class="name">Ping</div>
                <canvas id="pingMeter" class="meter"></canvas>
                <div id="pingText" class="result"></div>
                <div class="unit">ms</div>
        </div>
        <div class="col-xs-3 throughput-test">
                <div class="name">Jitter</div>
                <canvas id="jitMeter" class="meter"></canvas>
                <div id="jitText" class="result"></div>
                <div class="unit">ms</div>
        </div>
    </div>
</div>
<div id="instructionModal" class="modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Next Step</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div id="instruction" class="modal-body">
        <p>Please connect to <span class="font-weight-bold" id="if_name">{{ $testCase->nextInterface()['name'] }}</span> and wait 30 seconds.</p>
      </div>
      <div class="modal-footer">
        <button id="connected" type="button" class="btn btn-success">Done</button>
        <button id="clear" type="button" class="btn btn-danger" data-dismiss="modal">Cancel Test</button>
      </div>
    </div>
  </div>
</div>
<form>
    <input id="hiddenIP" value="" type="hidden" />
</form>
<script type="text/javascript">   
    //set this variable server side for easy use by javascript
    var wildcardSuffix = "<?php echo $_SERVER['SERVER_ADDR']; ?>.xip.io/test/checkDns";
    
    var ifList = [];  //using an array to guarantee the order
    var interfaces = new Object(); //use an object here to store information
    
    @foreach ($interfaces as $interface)
    ifList.push("{{ $interface->name }}");
    interfaces.{{ $interface->name }} = { index: {{ $interface->index }}, name: "{{ $interface->description }}" };
    @endforeach
    
    //using an array to guarantee the order    
    var tests = [];
    @foreach ($testList as $test)
    tests.push("{{ $test->name }}");
    @endforeach
    
    var results = {
        interfaces: {},
        
    };
    
    //function to (re)initialize UI
    function initUI(){
        drawMeter(I("dlMeter"),0,meterBk,dlColor,0);
        drawMeter(I("ulMeter"),0,meterBk,ulColor,0);
        drawMeter(I("pingMeter"),0,meterBk,pingColor,0);
        drawMeter(I("jitMeter"),0,meterBk,jitColor,0);
        I("dlText").textContent="";
        I("ulText").textContent="";
        I("pingText").textContent="";
        I("jitText").textContent="";
    }      
    
    var ui = {
        tests: {
            resetAll: function() {
                $('.test_item span').removeClass().addClass('oi oi-clock');
            },
            inProgress: function(test_id) {
                var divId = '#test_' + test_id + ' span';
                $(divId).removeClass().addClass('oi oi-loop-circular text-info');                
            },
            pass: function(test_id) {
                var divId = '#test_' + test_id + ' span';
                $(divId).removeClass().addClass('oi oi-check text-success');
            },
            fail: function(test_id) {
                var divId = '#test_' + test_id + ' span';
                $(divId).removeClass().addClass('oi oi-x text-danger');
            }
        },
        throughput: {
            resetAll: function() {
                initUI();
            }
        },
        resetAll: function() {
            ui.tests.resetAll();
            ui.throughput.resetAll();
        }
    };
    
    var resultHandler = {
        interfaceIndex: function(obj) 
        {
            var interfaceCount = Object.keys(obj).length;
            //index is zero-indexed but .length always starts at 1
            return --interfaceCount;
        },
        interfaceName: function(index)
        {
            if(typeof ifList[index] === 'undefined') {
                return false;
            } else {
                return ifList[index];
            }
        },
        testIndex: function(obj, interfaceName)
        {
            return Object.keys(obj[interfaceName]).length;
        },
        testName: function(index)
        {
            if(typeof tests[index] === 'undefined') {
                return false;
            } else {
                return tests[index];
            }
        }
        
    };
    
    function saveResults(obj)
    {
        $.ajax({
            type: "post",
            url: "/test/save",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            cache: false,
            data: {'test_results' : obj},
            dataType: "json",
            success: saveResultsSuccess,
            error: saveResultsError
        });            
    }

    function saveResultsSuccess(data)
    {
        console.log(data);
    }
    
    function saveResultsError(XMLHttpRequest, textStatus, errorThrown) {
        console.log(textStatus);
    }

    //interfaces only appear if tests have been STARTED
    function currentInterfaceIndex(obj) 
    {
        var interfaceCount = Object.keys(obj).length;
        //index is zero-indexed but .length always starts at 1
        return --interfaceCount;
    }
    
    //tests only appear if the test has been COMPLETED
    function nextTestIndex(obj, interfaceName)
    {
        return Object.keys(obj[interfaceName]).length;
    }
    
    function interfaceName(index)
    {
        if(typeof ifList[index] === 'undefined') {
            return false;
        } else {
            return ifList[index];
        }        
    }
    
    function testName(index)
    {
        if(typeof tests[index] === 'undefined') {
            return false;
        } else {
            return tests[index];
        }
    }

    function getTest(obj)
    {        
        var ifIndex = currentInterfaceIndex(obj);
        
        if(ifIndex < 0) {
            initializeNextInterface(obj);
            return false;
        }
        
        var ifName = interfaceName(ifIndex);
                
        var testIndex = nextTestIndex(obj, ifName);
        
        var testId = testName(testIndex);
        
        if(testId === false) { //all tests complete for interface
            finalizeInterface();
            initializeNextInterface(obj);
            return false;
        }
                
        return {interface: ifName, test: testId};
    }
    
    function finalizeInterface()
    {
        saveResults(results);
    }
    
    function initializeNextInterface(obj)
    {
        var ifIndex = currentInterfaceIndex(obj);
        ifIndex++;
        
        var interface = interfaceName(ifIndex);
        
        if(interface === false) {
            return testsCompleted();
        }
        
        obj[interface] = {};
        
        interfaceDescription = interfaces[interface].name;
        
        $('#if_name').text(interfaceDescription);
        $('#instructionModal').modal('show');
        
        return true;
    }
    
    function testsCompleted()
    {
        alert('all tests completed');
        return;
    }
    
    function recordTestResult(test, result)
    {
        currentTest = getTest(results.interfaces);
        
        var uiMethod = ((result.success) ? 'pass' : 'fail');
        
        ui.tests[uiMethod](test);

        results.interfaces[currentTest.interface][test] = result;
        console.log(results);
        
        runTest();
    }
    
    function runTest()
    {
        var testSet = getTest(results.interfaces);
        
        //this will happen when the next interface has to be set up
        if(testSet === false) {
            return false;
        }
        
        ui.tests.inProgress(testSet.test);
        testHandler[testSet.test].run();
    }
    
    var w=null; //speedtest worker
    var data=null; //data from worker    
    function I(id){return document.getElementById(id);}
    var meterBk="#E0E0E0";
    var dlColor="#6060AA",
        ulColor="#309030",
        pingColor="#AA6060",
        jitColor="#AA6060";
    var progColor="#EEEEEE";  

    //CODE FOR GAUGES
    function drawMeter(c,amount,bk,fg,progress,prog){
        var ctx=c.getContext("2d");
        var dp=window.devicePixelRatio||1;
        var cw=c.clientWidth*dp, ch=c.clientHeight*dp;
        var sizScale=ch*0.0055;
        if(c.width==cw&&c.height==ch){
            ctx.clearRect(0,0,cw,ch);
        }else{
            c.width=cw;
            c.height=ch;
        }
        ctx.beginPath();
        ctx.strokeStyle=bk;
        ctx.lineWidth=16*sizScale;
        ctx.arc(c.width/2,c.height-58*sizScale,c.height/1.8-ctx.lineWidth,-Math.PI*1.1,Math.PI*0.1);
        ctx.stroke();
        ctx.beginPath();
        ctx.strokeStyle=fg;
        ctx.lineWidth=16*sizScale;
        ctx.arc(c.width/2,c.height-58*sizScale,c.height/1.8-ctx.lineWidth,-Math.PI*1.1,amount*Math.PI*1.2-Math.PI*1.1);
        ctx.stroke();
        if(typeof progress !== "undefined"){
            ctx.fillStyle=prog;
            ctx.fillRect(c.width*0.3,c.height-16*sizScale,c.width*0.4*progress,4*sizScale);
        }
    }
    function mbpsToAmount(s){
        return 1-(1/(Math.pow(1.3,Math.sqrt(s))));
    }
    function msToAmount(s){
        return 1-(1/(Math.pow(1.08,Math.sqrt(s))));
    }

    //SPEEDTEST AND UI CODE

    //this function reads the data sent back by the worker and updates the UI
    function updateUI(forced){
        if(!forced&&(!data||!w)) return;
        var status=Number(data[0]);
        //I("ip").textContent=data[4];
        I("dlText").textContent=(status==1&&data[1]==0)?"...":data[1];
        drawMeter(I("dlMeter"),mbpsToAmount(Number(data[1]*(status==1?oscillate():1))),meterBk,dlColor,Number(data[6]),progColor);
        I("ulText").textContent=(status==3&&data[2]==0)?"...":data[2];
        drawMeter(I("ulMeter"),mbpsToAmount(Number(data[2]*(status==3?oscillate():1))),meterBk,ulColor,Number(data[7]),progColor);
        I("pingText").textContent=data[3];
        drawMeter(I("pingMeter"),msToAmount(Number(data[3]*(status==2?oscillate():1))),meterBk,pingColor,Number(data[8]),progColor);
        I("jitText").textContent=data[5];
        drawMeter(I("jitMeter"),msToAmount(Number(data[5]*(status==2?oscillate():1))),meterBk,jitColor,Number(data[8]),progColor);
    }
    function oscillate(){
        return 1+0.02*Math.sin(Date.now()/100);
    }
    //poll the status from the worker (this will call updateUI)
    setInterval(function(){
        if(w) w.postMessage('status');
    },200);
    //update the UI every frame
    window.requestAnimationFrame=window.requestAnimationFrame||window.webkitRequestAnimationFrame||window.mozRequestAnimationFrame||window.msRequestAnimationFrame||(function(callback,element){setTimeout(callback,1000/60);});
    function frame(){
        requestAnimationFrame(frame);
        updateUI();
    }
    frame(); //start frame loop
    
    
    /**
     * testHandler object must follow the following structure:
     * testName: {
     *     run: {},
     *     success: {},
     *     error: {},
     * }
     */
    var testHandler = {
        connectivity: {
            run: function() {
                testHandler.ajaxRequest("/test/connectivity", "post", {}, 'connectivity');
            },
            success: function(data) {
                recordTestResult('connectivity', {success: true});
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                recordTestResult('connectivity', {success: false});
            }
        },
        dhcp: {
            run: function() {
                window.RTCPeerConnection = window.RTCPeerConnection || window.mozRTCPeerConnection || window.webkitRTCPeerConnection;//compatibility for Firefox and chrome
                var pc = new RTCPeerConnection({iceServers:[]}), noop = function(){};      
                pc.createDataChannel('');//create a bogus data channel
                pc.createOffer(pc.setLocalDescription.bind(pc), noop);// create offer and set local description
                pc.onicecandidate = function(ice)
                {
                    if (ice && ice.candidate && ice.candidate.candidate){
                        var ipAddress = /([0-9]{1,3}(\.[0-9]{1,3}){3}|[a-f0-9]{1,4}(:[a-f0-9]{1,4}){7})/.exec(ice.candidate.candidate)[1]; 
                        testHandler.dhcp.success({ip: ipAddress});
                        pc.onicecandidate = noop;
                    }
                };
            },
            success: function(data) {
                recordTestResult('dhcp', {success: true, ip: data.ip});
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                //not currently called
            }
             
        },
        routing: {
            run: function() {
                testHandler.ajaxRequest("https://httpbin.org/get", "get", {}, 'routing');
            },
            success: function(data) {
                recordTestResult('routing', {success: true});
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                recordTestResult('routing', {success: false});
            }
        },
        dns: {
            run: function() {
                testUrl = 'http://' + makeid() + '.' + wildcardSuffix;
                testHandler.ajaxRequest(testUrl, "get", {}, 'dns');
            },
            success: function(data) {
                recordTestResult('dns', {success: true});
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                recordTestResult('dns', {success: false});
            }
        },
        throughput: {
            run: function() {
                return testHandler.throughput.success({"skip" : true});
                var parameters = { //custom test parameters. See doc.md for a complete list
                    time_dl: 1, //download test lasts 10 seconds
                    time_ul: 1, //upload test lasts 10 seconds
                    count_ping: 1 //ping+jitter test does 20 pings
                };

                w=new Worker('/js/speedtest_worker.js');
                w.postMessage('start '+JSON.stringify(parameters)); //Add optional parameters as a JSON object to this command
                //I("startStopBtn").className="running";
                w.onmessage=function(e){
                    data=e.data.split(';');
                    var status=Number(data[0]);
                    if(status>=4){
                        resultData = {
                            download: data[1],
                            upload: data[2],
                            ping: data[3],
                            jitter: data[5],                            
                            ip: data[4],
                            download_duration: parameters.time_dl,
                            upload_duration: parameters.time_ul,
                            ping_count: parameters.count_ping
                        };
                        testHandler.throughput.success(resultData);
                        w=null;
                        updateUI(true);
                    }
                };
            },
            success: function(data) {
                data.success = true;            
                recordTestResult('throughput', data);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                recordTestResult('throughput', {success: false});
            }
        },
        cancel: function() {
            testHandler.storeTestResult('/test/clear', {confirm: true});
            window.location = "/";
        },
        storeTestResult: function(postUrl, postData) {
            $.ajax({
                type:"post",
                url: postUrl,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },               
                async: true,
                cache: false,
                data: postData,
                success: function(data, textStatus, XMLHttpRequest) {
                    updateResult(data);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    console.log(textStatus);
                }
            });             
        },
        ajaxRequest: function(requestUrl, requestType, requestData, testBranch)
        {
            $.ajax({
                type: requestType,
                url: requestUrl,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                cache: false,
                data: requestData,
                dataType: "json",
                success: testHandler[testBranch].success,
                error: testHandler[testBranch].error
            });
        }
        
        

    };
    
    $(document).ready(function() {
         
        ui.resetAll();
        runTest();
        
        $('#connected').click(function() {
            $('#instructionModal').modal('hide');
            ui.resetAll();
            runTest();            
        });
        
        $('#clear').click(function() {
            testHandler.cancel();
        })
    });
        
    function makeid() {
      var text = "";
      var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

      for (var i = 0; i < 20; i++)
        text += possible.charAt(Math.floor(Math.random() * possible.length));

      return text;
    }      
</script>
<script type="text/javascript">setTimeout(initUI,100);</script>

@endsection