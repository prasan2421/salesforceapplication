@extends('layouts.master')

@section('title', 'Add Unit')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      {{ Form::open([ 'action' => 'UnitController@store' ]) }}
      <div class="card-body">
        @include('units.form')
      </div>
      <div class="card-footer">
        <a href="{{ action('UnitController@index') }}" class="btn btn-primary">Back to List</a>
        <button class="btn btn-primary" type="submit">Add</button>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>
@endsection