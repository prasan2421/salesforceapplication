@extends('layouts.master')

@section('title', 'Add Beat')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      {{ Form::open([ 'action' => 'RouteController@store' ]) }}
      <div class="card-body">
        @include('routes.form')
      </div>
      <div class="card-footer">
        <a href="{{ action('RouteController@index') }}" class="btn btn-primary">Back to List</a>
        <button class="btn btn-primary" type="submit">Add</button>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>
@endsection