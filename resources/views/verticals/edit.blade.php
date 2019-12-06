@extends('layouts.master')

@section('title', 'Edit Vertical')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      {{ Form::model($vertical, [ 'action' => [ 'VerticalController@update', $vertical->id ], 'method' => 'PUT' ]) }}
      <div class="card-body">
        @include('verticals.form')
      </div>
      <div class="card-footer">
        <a href="{{ action('VerticalController@index') }}" class="btn btn-primary">Back to List</a>
        <button class="btn btn-primary" type="submit">Update</button>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>
@endsection