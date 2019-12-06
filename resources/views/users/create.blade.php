@extends('layouts.master')

@section('title', 'Add User')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      {{ Form::open([ 'action' => 'UserController@store' ]) }}
      <div class="card-body">
        @include('users.form')
      </div>
      <div class="card-footer">
        <a href="{{ action('UserController@index') }}" class="btn btn-primary">Back to List</a>
        <button class="btn btn-primary" type="submit">Add</button>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>
@endsection