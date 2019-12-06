@extends('layouts.master')

@section('title', 'Sales Officer Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        {{--
        <div class="form-group">
          {{ Form::label('route_ids', 'Beats') }}
          {{ Form::textarea('route_ids', implode(', ', $user->routes()->pluck('name')->toArray()), [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>
        --}}

        <div class="form-group">
          {{ Form::label('vertical_ids', 'Verticals') }}
          {{ Form::textarea('vertical_ids', implode(', ', $user->verticals()->pluck('name')->toArray()), [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('emp_code', 'Emp Code') }}
          {{ Form::text('emp_code', $user->emp_code, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
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