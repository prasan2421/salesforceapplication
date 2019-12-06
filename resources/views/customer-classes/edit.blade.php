@extends('layouts.master')

@section('title', 'Edit Customer Class')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      {{ Form::model($customerClass, [ 'action' => [ 'CustomerClassController@update', $customerClass->id ], 'method' => 'PUT' ]) }}
      <div class="card-body">
        @include('customer-classes.form')
      </div>
      <div class="card-footer">
        <a href="{{ action('CustomerClassController@index') }}" class="btn btn-primary">Back to List</a>
        <button class="btn btn-primary" type="submit">Update</button>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>
@endsection