@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
            <h1>Test In Progress</h1>
            
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
<script type="text/javascript">
    var connectionAttempt = 0;
    
    $(document).ready(function() {
        
        
        showInstructions();
        
        $('#connected').click(function() {
            if(checkConnection()) {
                console.log('good connection');
                beginTest();
            }else{
                connectionAttempt++;
                console.log('bad connection');
                showInstructions();
            }
                    
        });
    });
    
    function checkConnection() {
        var connected;
        $.ajax({
            type:"get",
            url:"/test/connectivity",
            cache: false,
            async: false,
            success: function(data, textStatus, XMLHttpRequest) {
                connected = true;
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                connected = false;
            }
        });
        
        return connected;
    }
    
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