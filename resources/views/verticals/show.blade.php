@extends('layouts.master')

@section('title', 'Vertical Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="form-group">
          {{ Form::label('division_id', 'Division') }}
          {{ Form::text('division_id', $vertical->division ? $vertical->division->name : '', [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('name', 'Name') }}
          {{ Form::text('name', $vertical->name, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection