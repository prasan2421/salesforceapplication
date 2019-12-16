@extends('layouts.master')

@section('title', 'Customer Type Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">        
        <div class="form-group">
          {{ Form::label('name', 'Name') }}
          {{ Form::text('name', $customerType->name, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>
        <div class="form-group">
              {{ Form::label('name', 'Product type') }}
              {{ Form::text('name', $customerType->productType ? $customerType->productType->name : '', [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
          </div>
      </div>
    </div>
  </div>
</div>
@endsection
