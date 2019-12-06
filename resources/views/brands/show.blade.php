@extends('layouts.master')

@section('title', 'Brand Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <a href="{{ action('BrandController@index') }}" class="btn btn-primary">Back to List</a>
      </div>
      <div class="card-body">
        <div class="form-group">
          {{ Form::label('division_id', 'Division') }}
          {{ Form::text('division_id', $brand->division ? $brand->division->name : '', [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('vertical_id', 'Vertical') }}
          {{ Form::text('vertical_id', $brand->vertical ? $brand->vertical->name : '', [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('name', 'Name') }}
          {{ Form::text('name', $brand->name, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection