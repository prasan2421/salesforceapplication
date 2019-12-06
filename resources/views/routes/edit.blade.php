@extends('layouts.master')

@section('title', 'Edit Beat')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      {{ Form::model($route, [ 'action' => [ 'RouteController@update', $route->id ], 'method' => 'PUT' ]) }}
      <div class="card-body">
        @include('routes.form')
      </div>
      <div class="card-footer">
        <a href="{{ action('RouteController@index') }}" class="btn btn-primary">Back to List</a>
        <button class="btn btn-primary" type="submit">Update</button>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>
@endsection