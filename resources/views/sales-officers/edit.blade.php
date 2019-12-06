@extends('layouts.master')

@section('title', 'Edit Sales Officer')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      {{ Form::model($user, [ 'action' => [ 'SalesOfficerController@update', $user->id ], 'method' => 'PUT' ]) }}
      <div class="card-body">
        @include('sales-officers.form')
      </div>
      <div class="card-footer">
        <a href="{{ action('SalesOfficerController@index') }}" class="btn btn-primary">Back to List</a>
        <button class="btn btn-primary" type="submit">Update</button>
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
      var html = '';

      var division_id = $(this).val();
      if(division_id) {
        $.each(verticals[division_id], function(key, value) {
          html += '<option value="' + key + '">' + value + '</option>';
        });
      }

      $('#vertical_ids').html(html);
    });

    @if($errors->any())
      @if(old('division_id'))
      $('#division_id').change();

        @if(old('vertical_ids'))
        $('#vertical_ids').val({!! json_encode(old('vertical_ids')) !!});
        $('#vertical_ids').change();
        @endif
      @endif
    @else
      @if($user->division_id)
      $('#division_id').change();

        @if($user->verticals()->count() > 0)
        $('#vertical_ids').val({!! json_encode($user->verticals()->pluck('_id')->toArray()) !!});
        $('#vertical_ids').change();
        @endif
      @endif
    @endif
  });
</script>
@endpush