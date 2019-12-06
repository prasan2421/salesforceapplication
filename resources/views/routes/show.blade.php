@extends('layouts.master')

@section('title', 'Beat Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="form-group">
          {{ Form::label('division_id', 'Division') }}
          {{ Form::text('division_id', $route->division ? $route->division->name : '', [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('state_id', 'State') }}
          {{ Form::text('state_id', $route->state ? $route->state->name : '', [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('sap_code', 'Beat Code') }}
          {{ Form::text('sap_code', $route->sap_code, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('name', 'Beat Name') }}
          {{ Form::text('name', $route->name, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection