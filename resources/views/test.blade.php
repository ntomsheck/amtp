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
        interfaces: {
//            'lan1': {
//                'connectivity' : {'success': true},
//                'dns' : {'success' : true},
//            },
        },
        unsavedRecords: 0, //incremental counter
        
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
            skip: function(test_id) {
                var divId = '#test_' + test_id + ' span';
                $(divId).removeClass().addClass('oi oi-warning text-warning');                 
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
        instruct: {
            interface: function(iface) {
                var description = interfaces[iface].name;                
                $('#if_name').text(description);
                $('#instructionModal').modal('show');                
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
    
    //
    var progressHandler = {
        interfacesNotStarted: function(obj)
        {
            var initialized = Object.keys(obj);
            var diff = $(ifList).not(initialized).get();
            return diff;
        },
        interfaceInProgress: function(obj)
        {
            var interfaces = Object.keys(obj);
            if(interfaces.length === 0) {
                return false;
            }
            
            for(i = 0; i < interfaces.length; i++) {
                remaining = progressHandler.testsRemaining(obj, interfaces[i]);
                if(remaining.length > 0) {
                    return interfaces[i];
                }
            }
            
            return false;
        },
        testsRemaining: function(obj, interfaceName)
        {
            if(typeof obj[interfaceName] === 'undefined') {
                return tests;
            }
            var completed = Object.keys(obj[interfaceName]);
            var diff = $(tests).not(completed).get();
            return diff;
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
        results.unsavedRecords = 0;
    }
    
    function saveResultsError(XMLHttpRequest, textStatus, errorThrown) {
        console.log(textStatus);
    }
    
    function testsCompleted()
    {
        if(results.unsavedRecords > 0) {
            recordsNotSaved();
        } else {
            window.location = '/test/complete';
        }
        return;
    }
    
    function recordsNotSaved()
    {
        $('#instruction p').empty().text('Please reconnect to a working network.');
        $('#instructionModal').modal('show');
    }
    
    function recordTestResult(iface, test, result)
    {       
        var uiMethod = ((result.success) ? 'pass' : 'fail');
        
        if(result.skipped === true) {
            uiMethod = 'skip';
        }
        
        ui.tests[uiMethod](test);

        //failed ajax calls will sometimes try to callback multiple times
        if(typeof results.interfaces[iface][test] === 'undefined') {
            results.interfaces[iface][test] = result;
            results.unsavedRecords++;

            if(results.unsavedRecords >= 5) {
                saveResults(results);
            }

            runTest();
        }
    }
    
    function runTest()
    {
        var obj = results.interfaces;
        
        iface = progressHandler.interfaceInProgress(obj);
        
        if(iface === false) { //no 'in progress' interfaces
            var interfaces = progressHandler.interfacesNotStarted(obj);
            
            if(interfaces.length == 0) { //no interfaces left to test
                return testsCompleted();
            }

            obj[interfaces[0]] = {};

            ui.instruct.interface(interfaces[0]);
            
            return true;
        }
        
        var remaining = progressHandler.testsRemaining(obj, iface);

        ui.tests.inProgress(remaining[0]);
        testHandler[remaining[0]].run(iface);
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
            run: function(iface) {
                testHandler.ajaxRequest("/test/connectivity", "post", {}, 'connectivity', iface);
            },
            success: function(data, textStatus, jqXHR, iface) {
                recordTestResult(iface, 'connectivity', {success: true});
            },
            error: function(XMLHttpRequest, textStatus, errorThrown, iface) {
                recordTestResult(iface, 'connectivity', {success: false});
                recordTestResult(iface, 'throughput', {success: false, skipped: true});
            }
        },
        dhcp: {
            run: function(iface) {
                window.RTCPeerConnection = window.RTCPeerConnection || window.mozRTCPeerConnection || window.webkitRTCPeerConnection;//compatibility for Firefox and chrome
                var pc = new RTCPeerConnection({iceServers:[]}), noop = function(){};      
                pc.createDataChannel('');//create a bogus data channel
                pc.createOffer(pc.setLocalDescription.bind(pc), noop);// create offer and set local description
                pc.onicecandidate = function(ice)
                {
                    if (ice && ice.candidate && ice.candidate.candidate){
                        var ipAddress = /([0-9]{1,3}(\.[0-9]{1,3}){3}|[a-f0-9]{1,4}(:[a-f0-9]{1,4}){7})/.exec(ice.candidate.candidate)[1]; 
                        testHandler.dhcp.success({ip: ipAddress}, null, null, iface);
                        pc.onicecandidate = noop;
                    }
                };
            },
            success: function(data, textStatus, jqXHR, iface) {
                recordTestResult(iface, 'dhcp', {success: true, ip: data.ip});
            },
            error: function(XMLHttpRequest, textStatus, errorThrown, iface) {
                //not currently called
            }
             
        },
        routing: {
            run: function(iface) {
                testHandler.ajaxRequest("https://httpbin.org/get", "get", {}, 'routing', iface);
            },
            success: function(data, textStatus, jqXHR, iface) {
                recordTestResult(iface, 'routing', {success: true});
            },
            error: function(XMLHttpRequest, textStatus, errorThrown, iface) {
                recordTestResult(iface, 'routing', {success: false});
            }
        },
        dns: {
            run: function(iface) {
                testUrl = 'http://' + makeid() + '.' + wildcardSuffix;
                testHandler.ajaxRequest(testUrl, "get", {}, 'dns', iface);
            },
            success: function(data, textStatus, jqXHR, iface) {
                var response = {
                    success: true,
                    hostname: data.hostname,
                };
                recordTestResult(iface, 'dns', response);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown, iface) {
                recordTestResult(iface, 'dns', {success: false});
            }
        },
        throughput: {
            run: function(iface) {
                //return testHandler.throughput.success({"skip" : true});
                var parameters = { //custom test parameters. See doc.md for a complete list
                    time_dl: 10, //download test lasts 10 seconds
                    time_ul: 10, //upload test lasts 10 seconds
                    count_ping: 35 //ping+jitter test does 20 pings
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
                        testHandler.throughput.success(resultData, null, null, iface);
                        w=null;
                        updateUI(true);
                    }
                };
            },
            success: function(data, textStatus, jqXHR, iface) {
                data.success = true;            
                recordTestResult(iface, 'throughput', data);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown, iface) {
                recordTestResult(iface, 'throughput', {success: false});
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
        ajaxRequest: function(requestUrl, requestType, requestData, testBranch, interface)
        {
            $.ajax({
                type: requestType,
                url: requestUrl,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                cache: false,
                async: true,  
                data: requestData,
                dataType: "json",
                success: function(data, textStatus, jqXHR) {
                    testHandler[testBranch].success(data, textStatus, jqXHR, interface);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    testHandler[testBranch].error(jqXHR, textStatus, errorThrown, interface);
                }
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