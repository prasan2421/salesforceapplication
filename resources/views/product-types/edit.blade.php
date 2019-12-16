@extends('layouts.master')

@section('title', 'Edit Product Type')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      {{ Form::model($productType, [ 'action' => [ 'ProductTypeController', $productType->id ], 'method' => 'PUT' ]) }}
      <div class="card-body">
        @include('product-types.form')
      </div>
      <div class="card-footer">
        <a href="{{ action('ProductTypeController@index') }}" class="btn btn-primary">Back to List</a>
        <button class="btn btn-primary" type="submit">Update</button>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>
@endsection
