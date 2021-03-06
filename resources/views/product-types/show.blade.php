@extends('layouts.master')

@section('title', 'Product Type Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">        
        <div class="form-group">
          {{ Form::label('name', 'Name') }}
          {{ Form::text('name', $productType->name, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
