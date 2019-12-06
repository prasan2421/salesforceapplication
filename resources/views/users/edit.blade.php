@extends('layouts.master')

@section('title', 'Edit User')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      {{ Form::model($user, [ 'action' => [ 'UserController@update', $user->id ], 'method' => 'PUT' ]) }}
      <div class="card-body">
        @include('users.form')
      </div>
      <div class="card-footer">
        <a href="{{ action('UserController@index') }}" class="btn btn-primary">Back to List</a>
        <button class="btn btn-primary" type="submit">Update</button>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>
@endsection