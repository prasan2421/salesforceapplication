@extends('layouts.master')

@section('title', 'Edit Distributor')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      {{ Form::model($distributor, [ 'action' => [ 'DistributorController@update', $distributor->id ], 'method' => 'PUT' ]) }}
      <div class="card-body">
        @include('distributors.form')
      </div>
      <div class="card-footer">
        <a href="{{ action('DistributorController@index') }}" class="btn btn-primary">Back to List</a>
        <button class="btn btn-primary" type="submit">Update</button>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>
@endsection