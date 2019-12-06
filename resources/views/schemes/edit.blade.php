@extends('layouts.master')

@section('title', 'Edit Scheme')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      {{ Form::model($scheme, [ 'action' => [ 'SchemeController@update', $scheme->id ], 'method' => 'PUT' ]) }}
      <div class="card-body">
        @include('schemes.form')
      </div>
      <div class="card-footer">
        <a href="{{ action('SchemeController@index') }}" class="btn btn-primary">Back to List</a>
        <button class="btn btn-primary" type="submit">Update</button>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>
@endsection