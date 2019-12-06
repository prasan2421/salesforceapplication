@extends('layouts.master')

@section('title', 'Export Orders Summary to Excel')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      {{ Form::open([ 'action' => 'OrderController@submitExportSummaryExcel' ]) }}
      <div class="card-body">
        <div class="form-group">
          {{ Form::label('start_date', 'Start Date') }}
          {{ Form::text('start_date', null, [ 'class' => 'form-control datepicker' . ($errors->has('start_date') ? ' is-invalid' : '') ])}}
          @if($errors->has('start_date'))
          <div class="invalid-feedback">
            {{ $errors->first('start_date') }}
          </div>
          @endif
        </div>

        <div class="form-group">
          {{ Form::label('end_date', 'End Date') }}
          {{ Form::text('end_date', null, [ 'class' => 'form-control datepicker' . ($errors->has('end_date') ? ' is-invalid' : '') ])}}
          @if($errors->has('end_date'))
          <div class="invalid-feedback">
            {{ $errors->first('end_date') }}
          </div>
          @endif
        </div>
      </div>
      <div class="card-footer">
        <a href="{{ action('OrderController@index') }}" class="btn btn-primary">Back to List</a>
        <button class="btn btn-primary" type="submit">Export</button>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
  $(document).ready(function() {
    $('form').submit(function() {
      $('.is-invalid').removeClass('is-invalid');
      $('.invalid-feedback').remove();
    });
  });
</script>
@endpush