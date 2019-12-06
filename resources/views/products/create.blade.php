@extends('layouts.master')

@section('title', 'Add Product')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      {{ Form::open([ 'action' => 'ProductController@store' ]) }}
      <div class="card-body">
        @include('products.form')
      </div>
      <div class="card-footer">
        <a href="{{ action('ProductController@index') }}" class="btn btn-primary">Back to List</a>
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
  var brands = JSON.parse('{!! $brandsJson !!}');

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
      $('#vertical_id').change();
    });

    $('#vertical_id').change(function() {
      var html = '<option value="">Select a brand</option>';

      var vertical_id = $(this).val();
      if(vertical_id) {
        $.each(brands[vertical_id], function(key, value) {
          html += '<option value="' + key + '">' + value + '</option>';
        });
      }

      $('#brand_id').html(html);
    });

    @if($errors->any())
      @if(old('division_id'))
      $('#division_id').change();

        @if(old('vertical_id'))
        $('#vertical_id').val('{{ old('vertical_id') }}');
        $('#vertical_id').change();

          @if(old('brand_id'))
          $('#brand_id').val('{{ old('brand_id') }}');
          @endif
        @endif
      @endif
    @endif
  });
</script>
@endpush