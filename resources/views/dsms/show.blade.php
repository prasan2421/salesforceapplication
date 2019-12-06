@extends('layouts.master')

@section('title', 'DSM Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        {{--@role('admin')--}}
        <div class="form-group">
          {{ Form::label('sales_officer_id', 'Sales Officer') }}
          {{ Form::text('sales_officer_id', $user->salesOfficer ? $user->salesOfficer->name : '', [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>
        {{--@endrole--}}

        <div class="form-group">
          {{ Form::label('distributor_id', 'Distributor') }}
          {{ Form::text('distributor_id', $user->distributor ? $user->distributor->name : '', [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

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