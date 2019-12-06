@extends('layouts.master')

@section('title', 'Product Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        {{--
        <div class="form-group">
          {{ Form::label('category_id', 'Category') }}
          {{ Form::text('category_id', $product->category ? $product->category->name : '', [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>
        --}}

        <div class="form-group">
          {{ Form::label('division_id', 'Division') }}
          {{ Form::text('division_id', $product->division ? $product->division->name : '', [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('vertical_id', 'Vertical') }}
          {{ Form::text('vertical_id', $product->vertical ? $product->vertical->name : '', [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('brand_id', 'Brand') }}
          {{ Form::text('brand_id', $product->brand ? $product->brand->name : '', [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('sap_code', 'SAP Code') }}
          {{ Form::text('sap_code', $product->sap_code, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('name', 'Name') }}
          {{ Form::text('name', $product->name, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('unit_id', 'Unit') }}
          {{ Form::text('unit_id', $product->unit ? $product->unit->name : '', [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('is_featured', 'Is Featured Product?') }}
          {{ Form::text('is_featured', $product->is_featured ? 'Yes' : 'No', [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection