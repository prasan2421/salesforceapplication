@extends('layouts.master')

@section('title', 'Add Vertical')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      {{ Form::open([ 'action' => 'VerticalController@store' ]) }}
      <div class="card-body">
        @include('verticals.form')
      </div>
      <div class="card-footer">
        <a href="{{ action('VerticalController@index') }}" class="btn btn-primary">Back to List</a>
        <button class="btn btn-primary" type="submit">Add</button>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>
@endsection