@extends('layouts.master')

@section('title', 'User Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="form-group">
          {{ Form::label('parent_id', 'Parent') }}
          {{ Form::text('parent_id', $user->parent ? $user->parent->name : 'No Parent', [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>
        
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