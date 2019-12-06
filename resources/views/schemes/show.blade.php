@extends('layouts.master')

@section('title', 'Scheme Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="form-group">
          {{ Form::label('product_id', 'Product') }}
          {{ Form::text('product_id', $scheme->product ? $scheme->product->name : '', [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('start_date', 'Start Date') }}
          {{ Form::text('start_date', $scheme->start_date, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('end_date', 'End Date') }}
          {{ Form::text('end_date', $scheme->end_date, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('discount', 'Discount (%)') }}
          {{ Form::text('discount', $scheme->discount, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection