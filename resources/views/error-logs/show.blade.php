@extends('layouts.master')

@section('title', 'Error Log Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="form-group">
          {{ Form::label('user_id', 'User') }}
          {{ Form::text('user_id', $errorLog->user ? $errorLog->user->name : '', [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('url', 'URL') }}
          {{ Form::text('url', $errorLog->url, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('method', 'Method') }}
          {{ Form::text('method', $errorLog->method, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('status_code', 'Response Status Code') }}
          {{ Form::text('status_code', $errorLog->status_code, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('body', 'Response Body') }}
          <iframe width="100%" height="600px" srcdoc="{{ $errorLog->body }}" />
        </div>
      </div>
    </div>
  </div>
</div>
@endsection