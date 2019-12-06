@extends('layouts.master')

@section('title', 'My Beat Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="form-group">
          {{ Form::label('route_id', 'Beat') }}
          {{ Form::text('route_id', $routeUser->route ? $routeUser->route->name : '', [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('frequency', 'Frequency') }}
          {{ Form::text('frequency', $routeUser->frequency, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('day', 'Day') }}
          {{ Form::text('day', $routeUser->day, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection