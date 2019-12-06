@extends('layouts.master')

@section('title', 'Add Customer Category')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      {{ Form::open([ 'action' => 'CustomerCategoryController@store' ]) }}
      <div class="card-body">
        @include('customer-categories.form')
      </div>
      <div class="card-footer">
        <a href="{{ action('CustomerCategoryController@index') }}" class="btn btn-primary">Back to List</a>
        <button class="btn btn-primary" type="submit">Add</button>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>
@endsection