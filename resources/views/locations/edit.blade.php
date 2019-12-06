@extends('layouts.master')

@section('title', 'Edit Location')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      {{ Form::model($location, [ 'action' => [ 'LocationController@update', $location->id ], 'method' => 'PUT' ]) }}
      <div class="card-body">
        @include('locations.form')
      </div>
      <div class="card-footer">
        <a href="{{ action('LocationController@index') }}" class="btn btn-primary">Back to List</a>
        <button class="btn btn-primary" type="submit">Update</button>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>
@endsection