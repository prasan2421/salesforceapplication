@extends('layouts.master')

@section('title', 'Import Products')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      {{ Form::open([ 'action' => 'ProductController@saveImport', 'files' => true ]) }}
      <div class="card-body">
        <div class="form-group">
          {{ Form::label('csv', 'CSV File') }}
          {{ Form::file('csv', [ 'class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : '') ])}}
          @if($errors->has('csv'))
          <div class="invalid-feedback">
            {{ $errors->first('csv') }}
          </div>
          @endif
        </div>
      </div>
      <div class="card-footer">
        <button class="btn btn-primary" type="submit">Import</button>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>
@endsection