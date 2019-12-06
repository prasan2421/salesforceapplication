@extends('layouts.master')

@section('title', 'Import Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="form-group">
          {{ Form::label('id', 'ID') }}
          {{ Form::text('id', $import->id, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('type', 'Type') }}
          {{ Form::text('type', $import->type, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('filename', 'Filename') }}
          {{ Form::text('filename', $import->filename, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('status', 'Status') }}
          {{ Form::text('status', $import->status, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('remarks', 'Remarks') }}
          {{ Form::text('remarks', $import->remarks, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('error_code', 'Error Code') }}
          {{ Form::text('error_code', $import->error_code, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('error_message', 'Error Message') }}
          {{ Form::text('error_message', $import->error_message, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('error_trace', 'Error Trace') }}
          {{ Form::textarea('error_trace', $import->error_trace, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <!-- <div class="form-group">
          {{ Form::label('is_success', 'Is Success') }}
          {{ Form::text('is_success', $import->is_success, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div> -->

        <div class="form-group">
          {{ Form::label('user_id', 'User') }}
          {{ Form::text('user_id', $import->user ? $import->user->name : '', [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection