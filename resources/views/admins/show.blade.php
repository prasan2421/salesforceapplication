@extends('layouts.master')

@section('title', 'Admin Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <a href="{{ action('AdminController@index') }}" class="btn btn-primary">Back to List</a>
      </div>
      <div class="card-body">
        <div class="form-group">
          {{ Form::label('name', 'Name') }}
          {{ Form::text('name', $user->name, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('email', 'Email') }}
          {{ Form::email('email', $user->email, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('username', 'Username') }}
          {{ Form::text('username', $user->username, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection