@extends('layouts.master')

@section('title', 'Import from Excel')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      {{ Form::open([ 'action' => 'ImportExcelController@store', 'files' => true ]) }}
      <div class="card-body">
        <div class="form-group">
          {{ Form::label('table', 'Table') }}
          {{ Form::select('table', $tables, null, [ 'placeholder' => 'Select a table', 'class' => 'form-control' . ($errors->has('table') ? ' is-invalid' : '') ])}}
          @if($errors->has('table'))
          <div class="invalid-feedback">
            {{ $errors->first('table') }}
          </div>
          @endif
        </div>

        <div class="form-group">
          {{ Form::label('excel', 'Excel File') }}
          {{ Form::file('excel', [ 'class' => 'form-control' . ($errors->has('excel') ? ' is-invalid' : '') ])}}
          @if($errors->has('excel'))
          <div class="invalid-feedback">
            {{ $errors->first('excel') }}
          </div>
          @endif
        </div>

        <div class="form-group">
          {{ Form::label('remarks', 'Remarks') }}
          {{ Form::text('remarks', null, [ 'class' => 'form-control' . ($errors->has('remarks') ? ' is-invalid' : '') ])}}
          @if($errors->has('remarks'))
          <div class="invalid-feedback">
            {{ $errors->first('remarks') }}
          </div>
          @endif
        </div>

        <div class="form-group">
          {{ Form::label('file_type', 'File Type') }}
          {{ Form::select('file_type', $fileTypes, null, [ 'placeholder' => 'Select a file type', 'class' => 'form-control' . ($errors->has('file_type') ? ' is-invalid' : '') ])}}
          @if($errors->has('file_type'))
          <div class="invalid-feedback">
            {{ $errors->first('file_type') }}
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