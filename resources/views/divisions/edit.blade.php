@extends('layouts.master')

@section('title', 'Edit Division')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      {{ Form::model($division, [ 'action' => [ 'DivisionController@update', $division->id ], 'method' => 'PUT' ]) }}
      <div class="card-body">
        @include('divisions.form')
      </div>
      <div class="card-footer">
        <a href="{{ action('DivisionController@index') }}" class="btn btn-primary">Back to List</a>
        <button class="btn btn-primary" type="submit">Update</button>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>
@endsection