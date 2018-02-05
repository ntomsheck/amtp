@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <h1>Test In Progress</h1>
        <ul>        
            @foreach ($testList as $test)
            <li>{{ $test->test_name }}</li>
            @endforeach
        </ul>
            
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
        <p>Please connect your ethernet cable to {{ $testCase->nextInterface()['name'] }}.</p>
      </div>
      <div class="modal-footer">
        <button id="connected" type="button" class="btn btn-success">Done</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel Test</button>
      </div>
    </div>
  </div>
</div>
<form>
    <input id="hiddenIP" value="" type="hidden" />
</form>
<script type="text/javascript">
    var connectionAttempt = 0;
    
    var wildcardSuffix = "<?php echo $_SERVER['SERVER_ADDR']; ?>.xip.io/test/checkDns";
    
    var ifIndex = new Object();
    @foreach ($interfaces as $interface)
        ifIndex.if{{ $interface->interface_number }} = "{{ $interface->interface_name }}";
    @endforeach
    
    var testResults = {
        
    };
        
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
                    console.log(data);
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
                    console.log(data);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    console.log(textStatus);
                }
            });             
        },
        
        

    };
    
    $(document).ready(function() {
        
        
        showInstructions();
        
        $('#connected').click(function() {
            portHandler.connectivity();
            portHandler.dhcp();
            portHandler.routing();
            portHandler.dns();
        });
    });
    
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

    console.log(makeid());
    
    function beginTest() {
        $('#instructionModal').modal('hide');
    }
    
    function showInstructions() {
        var statement = instructionMessage(connectionAttempt);
        
        $('#instruction').empty().html('<p>' + statement + '</p>');
        
        $('#instructionModal').modal('show');
    }
    
    function instructionMessage(index) {
        
        var connectionInstructions = [
            "Please connect your ethernet cable to {{ $testCase->nextInterface()['name'] }}.",
            'No connection.  Please wait 30 seconds and try again.',
            'Abort.  Please return your ethernet cable to a working device.'
        ];
        
        console.log(connectionInstructions.length);
        
        if(index >= connectionInstructions.length) {
            return connectionInstructions[(connectionInstructions.length - 1)];
        } else {
            return connectionInstructions[index];
        }
        
    }
  
</script>
@endsection