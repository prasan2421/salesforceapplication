@extends('layouts.master')

@section('title', 'Add Brand')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      {{ Form::open([ 'action' => 'BrandController@store' ]) }}
      <div class="card-body">
        @include('brands.form')
      </div>
      <div class="card-footer">
        <a href="{{ action('BrandController@index') }}" class="btn btn-primary">Back to List</a>
        <button class="btn btn-primary" type="submit">Add</button>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
  var verticals = JSON.parse('{!! $verticalsJson !!}');

  $(document).ready(function() {
    $('#division_id').change(function() {
      var html = '<option value="">Select a vertical</option>';

      var division_id = $(this).val();
      if(division_id) {
        $.each(verticals[division_id], function(key, value) {
          html += '<option value="' + key + '">' + value + '</option>';
        });
      }

      $('#vertical_id').html(html);
    });

    @if($errors->any())
      @if(old('division_id'))
      $('#division_id').change();

        @if(old('vertical_id'))
        $('#vertical_id').val('{{ old('vertical_id') }}');
        @endif
      @endif
    @endif
  });
</script>
@endpush