@extends('layouts.master')

@section('title', 'Sales Officer Beat Details')

@section('content')
<h2 class="section-title">Sales Officer: {{ $user->name }}</h2>

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