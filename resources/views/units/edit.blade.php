@extends('layouts.master')

@section('title', 'Edit Unit')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      {{ Form::model($unit, [ 'action' => [ 'UnitController@update', $unit->id ], 'method' => 'PUT' ]) }}
      <div class="card-body">
        @include('units.form')
      </div>
      <div class="card-footer">
        <a href="{{ action('UnitController@index') }}" class="btn btn-primary">Back to List</a>
        <button class="btn btn-primary" type="submit">Update</button>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>
@endsection