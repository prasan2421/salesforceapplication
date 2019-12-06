@extends('layouts.master')

@section('title', 'Edit State')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      {{ Form::model($state, [ 'action' => [ 'StateController@update', $state->id ], 'method' => 'PUT' ]) }}
      <div class="card-body">
        @include('states.form')
      </div>
      <div class="card-footer">
        <a href="{{ action('StateController@index') }}" class="btn btn-primary">Back to List</a>
        <button class="btn btn-primary" type="submit">Update</button>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>
@endsection