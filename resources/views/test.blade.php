@extends('layouts.app')
@section('content')
<style type="text/css">

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
    <div class="col">
        <div class="row">
            <h1>Test In Progress</h1>      
            @foreach ($testList as $test)
            <p id="test_{{ $test->machine_name }}" class="h4"><span class="oi oi-loop-circular"></span>&nbsp;{{ $test->test_name }}</p>
            @endforeach

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
        <p>Please connect your ethernet cable to <span id="if_name">{{ $testCase->nextInterface()['name'] }}</span> and wait 30 seconds.</p>
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
    ifList.push("{{ $interface->machine_name }}");
    interfaces.{{ $interface->machine_name }} = { index: {{ $interface->interface_number }}, name: "{{ $interface->interface_name }}" };
    @endforeach
    
    //using an array to guarantee the order    
    var tests = [];
    @foreach ($testList as $test)
    tests.push("{{ $test->machine_name }}");
    @endforeach
    
    var testResults = {
        
    };
    
    function nextTest(resultObj) {
        
        var portCount = Object.keys(obj).legnth;
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
    
    var parameters={ //custom test parameters. See doc.md for a complete list
        time_dl: 180, //download test lasts 10 seconds
        time_ul: 180, //upload test lasts 10 seconds
        count_ping: 35 //ping+jitter test does 20 pings
    };    

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
    
    var portHandler = {
        connectivity: function() {
            $.ajax({
                type:"post",
                url:"/test/connectivity",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },               
                async: true,
                cache: false,
                success: function(data, textStatus, XMLHttpRequest) {
                    updateResult(data);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    console.log(textStatus);
                }
            });

        },
        dhcp: function() {
            window.RTCPeerConnection = window.RTCPeerConnection || window.mozRTCPeerConnection || window.webkitRTCPeerConnection;//compatibility for Firefox and chrome
            var pc = new RTCPeerConnection({iceServers:[]}), noop = function(){};      
            pc.createDataChannel('');//create a bogus data channel
            pc.createOffer(pc.setLocalDescription.bind(pc), noop);// create offer and set local description
            pc.onicecandidate = function(ice)
            {
                if (ice && ice.candidate && ice.candidate.candidate){
                    var ipAddress = /([0-9]{1,3}(\.[0-9]{1,3}){3}|[a-f0-9]{1,4}(:[a-f0-9]{1,4}){7})/.exec(ice.candidate.candidate)[1]; 
                    portHandler.storeTestResult('/test/dhcp', {local_ip: ipAddress})
                    pc.onicecandidate = noop;
                }
            };            
             
        },
        routing: function() {
            $.ajax({
                type:"get",
                url:"https://httpbin.org/get",
                async: true,
                cache: false,
                dataType: "json",
                success: function(data, textStatus, XMLHttpRequest) {
                    portHandler.storeTestResult('/test/routing', {success: true})
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    portHandler.storeTestResult('/test/routing', {success: false})
                }
            });            
        },
        dns: function() {
            
            testUrl = 'http://' + makeid() + '.' + wildcardSuffix;
            
            $.ajax({
                type:"get",
                url:testUrl,             
                async: true,
                cache: false,
                success: function(data, textStatus, XMLHttpRequest) {
                    portHandler.storeTestResult('/test/dns', {success: true})
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    portHandler.storeTestResult('/test/dns', {success: false})
                }
            });
        },
        throughput: function() {
            w=new Worker('/js/speedtest_worker.js');
            w.postMessage('start '+JSON.stringify(parameters)); //Add optional parameters as a JSON object to this command
            //I("startStopBtn").className="running";
            w.onmessage=function(e){
                data=e.data.split(';');
                var status=Number(data[0]);
                if(status>=4){
                    console.log(data);
                    portHandler.storeTestResult('/test/throughput', {download: data[1]});
                    location.reload();                    
                    w=null;
                    updateUI(true);
                }
            };            
        },
        cancel: function() {
            portHandler.storeTestResult('/test/clear', {confirm: true});
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
        
        

    };
    
    $(document).ready(function() {
        
//        $('#test_connectivity').find('span.oi').removeClass('oi-loop-circular').addClass('oi-check');
        showInstructions();
        
        $('#connected').click(function() {
            $('#instructionModal').modal('hide');        
            portHandler.connectivity();
            portHandler.dhcp();
            portHandler.routing();
            portHandler.dns();
            portHandler.throughput();
            
        });
        
        $('#clear').click(function() {
            portHandler.cancel();
        })
    });
    
    function updateResult(response)
    {
        var testId = "test_" + response.test_id;
        
        if(response.success) {
            $('#' + testId).find('span.oi').removeClass('oi-loop-circular').addClass('oi-check text-success');
        }
    }
    
//    function checkConnection() {
//        var connected;
//        $.ajax({
//            type:"get",
//            url:"/test/connectivity",
//            cache: false,
//            async: false,
//            success: function(data, textStatus, XMLHttpRequest) {
//                connected = true;
//            },
//            error: function(XMLHttpRequest, textStatus, errorThrown) {
//                connected = false;
//            }
//        });
//        
//        return connected;
//    }
    function makeid() {
      var text = "";
      var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

      for (var i = 0; i < 20; i++)
        text += possible.charAt(Math.floor(Math.random() * possible.length));

      return text;
    }
   
    function beginTest() {
        $('#instructionModal').modal('hide');
    }
    
    function showInstructions() {        
        
        $('#instructionModal').modal('show');
    }
      
</script>
<script type="text/javascript">setTimeout(initUI,100);</script>

@endsection