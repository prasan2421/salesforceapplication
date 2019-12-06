@extends('layouts.master')

@section('title', 'Edit Customer Category')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      {{ Form::model($customerCategory, [ 'action' => [ 'CustomerCategoryController@update', $customerCategory->id ], 'method' => 'PUT' ]) }}
      <div class="card-body">
        @include('customer-categories.form')
      </div>
      <div class="card-footer">
        <a href="{{ action('CustomerCategoryController@index') }}" class="btn btn-primary">Back to List</a>
        <button class="btn btn-primary" type="submit">Update</button>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>
@endsection