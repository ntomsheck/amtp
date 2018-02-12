@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-xs-12">
        <h1>Automated Mako Testing Platform</h1>
        {{ Form::open(array('url' => '/start')) }}
            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    Please fix the following errors
                </div>
            @endif

            {!! csrf_field() !!}
            <div class="form-group{{ $errors->has('mac_address') ? ' has-error' : '' }}">
                {!! Form::Label('mac_address', 'Mako ID') !!}
                {!! Form::text('mac_address', null, ['class' => 'form-control', 'placeholder' => 'Mako ID']) !!}
                @if($errors->has('mac_address'))
                    <span class="help-block">{{ $errors->first('mac_address') }}</span>
                @endif
            </div>
            <div class="form-group{{ $errors->has('model_id') ? ' has-error' : '' }}">
                {!! Form::Label('model_id', 'Model:') !!}
                {!! Form::select('model_id', $device_list, null, ['placeholder' => 'Select Model', 'class' => 'form-control']) !!}
                @if($errors->has('tester_name'))
                    <span class="help-block">{{ $errors->first('model_id') }}</span>
                @endif                    
            </div>
            <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                {!! Form::Label('username', 'Username') !!}
                {!! Form::text('username', null, ['class' => 'form-control', 'placeholder' => 'Username']) !!}
                @if($errors->has('username'))
                    <span class="help-block">{{ $errors->first('username') }}</span>
                @endif
            </div>
            <button type="submit" class="btn btn-default">Submit</button>
        {{ Form::close() }}
        </div>
    </div>
</div>
@endsection