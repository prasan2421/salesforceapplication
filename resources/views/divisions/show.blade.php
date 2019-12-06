@extends('layouts.master')

@section('title', 'Division Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="form-group">
          {{ Form::label('name', 'Name') }}
          {{ Form::text('name', $division->name, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('abbreviation', 'Abbreviation') }}
          {{ Form::text('abbreviation', $division->abbreviation, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection