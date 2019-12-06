@extends('layouts.master')

@section('title', 'Task Log Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="form-group">
          {{ Form::label('id', 'ID') }}
          {{ Form::text('id', $taskLog->id, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('command', 'Command') }}
          {{ Form::text('command', $taskLog->command, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('filename', 'Filename') }}
          {{ Form::text('filename', $taskLog->filename, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection