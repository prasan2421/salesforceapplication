@extends('layouts.master')

@section('title', 'Add Distributor')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      {{ Form::open([ 'action' => 'DistributorController@store' ]) }}
      <div class="card-body">
        @include('distributors.form')
      </div>
      <div class="card-footer">
        <a href="{{ action('DistributorController@index') }}" class="btn btn-primary">Back to List</a>
        <button class="btn btn-primary" type="submit">Add</button>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>
@endsection