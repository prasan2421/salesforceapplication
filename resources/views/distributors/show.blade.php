@extends('layouts.master')

@section('title', 'Distributor Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="form-group">
          {{ Form::label('vertical_ids', 'Verticals') }}
          {{ Form::textarea('vertical_ids', implode(', ', $distributor->verticals()->pluck('name')->toArray()), [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('sap_code', 'SAP Code') }}
          {{ Form::text('sap_code', $distributor->sap_code, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('name', 'Name') }}
          {{ Form::text('name', $distributor->name, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('email', 'Email') }}
          {{ Form::text('email', $distributor->email, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('contact_number', 'Contact Number') }}
          {{ Form::text('contact_number', $distributor->contact_number, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('city', 'City') }}
          {{ Form::text('city', $distributor->city, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('district', 'District') }}
          {{ Form::text('district', $distributor->district, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>
        
        <div class="form-group">
          {{ Form::label('state', 'State') }}
          {{ Form::text('state', $distributor->state, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>
        
        <div class="form-group">
          {{ Form::label('region', 'Region') }}
          {{ Form::text('region', $distributor->region, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection