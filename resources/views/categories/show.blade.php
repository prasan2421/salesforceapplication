@extends('layouts.master')

@section('title', 'Category Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="form-group">
          {{ Form::label('parent_id', 'Parent') }}
          {{ Form::text('parent_id', $category->parent ? $category->parent->name : 'No Parent', [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>
        
        <div class="form-group">
          {{ Form::label('name', 'Name') }}
          {{ Form::text('name', $category->name, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection