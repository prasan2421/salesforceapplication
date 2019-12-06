@extends('layouts.master')

@section('title', 'Edit Category')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      {{ Form::model($category, [ 'action' => [ 'CategoryController@update', $category->id ], 'method' => 'PUT' ]) }}
      <div class="card-body">
        @include('categories.form')
      </div>
      <div class="card-footer">
        <a href="{{ action('CategoryController@index') }}" class="btn btn-primary">Back to List</a>
        <button class="btn btn-primary" type="submit">Update</button>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>
@endsection