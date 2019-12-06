@extends('layouts.master')

@section('title', 'Edit Admin')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      {{ Form::model($user, [ 'action' => [ 'AdminController@update', $user->id ], 'method' => 'PUT' ]) }}
      <div class="card-body">
        @include('admins.form')
      </div>
      <div class="card-footer">
        <a href="{{ action('AdminController@index') }}" class="btn btn-primary">Back to List</a>
        <button class="btn btn-primary" type="submit">Update</button>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>
@endsection