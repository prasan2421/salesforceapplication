@extends('layouts.master')

@section('title', 'Edit Customer Type')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      {{ Form::model($customerType, [ 'action' => [ 'CustomerTypeController@update', $customerType->id ], 'method' => 'PUT' ]) }}
      <div class="card-body">
        @include('customer-types.form')
      </div>
      <div class="card-footer">
        <a href="{{ action('CustomerTypeController@index') }}" class="btn btn-primary">Back to List</a>
        <button class="btn btn-primary" type="submit">Update</button>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>
@endsection