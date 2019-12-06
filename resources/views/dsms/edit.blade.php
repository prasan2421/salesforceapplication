@extends('layouts.master')

@section('title', 'Edit DSM')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      {{ Form::model($user, [ 'action' => [ 'DsmController@update', $user->id ], 'method' => 'PUT' ]) }}
      <div class="card-body">
        @include('dsms.form')
      </div>
      <div class="card-footer">
        <a href="{{ action('DsmController@index', Common::addMineParam()) }}" class="btn btn-primary">Back to List</a>
        <button class="btn btn-primary" type="submit">Update</button>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
  $(document).ready(function() {
    @role('admin')
    $('#sales_officer_id').select2({
      ajax: {
        url: '{{ action('SalesOfficerController@search') }}',
        dataType: 'json',
        delay: 250,
        data: function(params) {
          var query = {
            term: params.term,
            page: params.page || 1
          };

          return query;
        }
      }
    });

    @if(!$user->is_non_parakram)
    var verticals = JSON.parse('{!! $verticalsJson !!}');

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
    @endif
    @endrole

    $('#distributor_id').select2({
      ajax: {
        url: '{{ action('DistributorController@search') }}',
        dataType: 'json',
        delay: 250,
        data: function(params) {
          var query = {
            term: params.term,
            page: params.page || 1
          };

          return query;
        }
      }
    });

    $('form').submit(function() {
      @role('admin')
       var selection = $('#sales_officer_id').select2('data');
       if(selection.length > 0) {
        $('form').append('<input type="hidden" name="sales_officer_text" value="' + selection[0].text + '" />');
       }
      @endrole

       var selection1 = $('#distributor_id').select2('data');
       if(selection1.length > 0) {
        $('form').append('<input type="hidden" name="distributor_text" value="' + selection1[0].text + '" />');
       }
    });

    @if($errors->any())
      @role('admin')
      @if(old('sales_officer_id'))
        var html = '<option value="{{ old('sales_officer_id') }}" selected="selected">';
        html += '{{ old('sales_officer_text') }}';
        html += '</option>';

        $('#sales_officer_id').append(html);
      @endif

      @if(!$user->is_non_parakram && old('division_id'))
      $('#division_id').change();

        @if(old('vertical_ids'))
        $('#vertical_ids').val({!! json_encode(old('vertical_ids')) !!});
        $('#vertical_ids').change();
        @endif
      @endif
      @endrole

      @if(old('distributor_id'))
        var html1 = '<option value="{{ old('distributor_id') }}" selected="selected">';
        html1 += '{{ old('distributor_text') }}';
        html1 += '</option>';

        $('#distributor_id').append(html1);
      @endif
    @else
      @role('admin')
      @if($user->salesOfficer)
        var html = '<option value="{{ $user->salesOfficer->id }}" selected="selected">';
        html += '{{ $user->salesOfficer->emp_code . ' ' . $user->salesOfficer->name }}';
        html += '</option>';

        $('#sales_officer_id').append(html);
      @endif

      @if(!$user->is_non_parakram && $user->division_id)
      $('#division_id').change();

        @if($user->verticals()->count() > 0)
        $('#vertical_ids').val({!! json_encode($user->verticals()->pluck('_id')->toArray()) !!});
        $('#vertical_ids').change();
        @endif
      @endif
      @endrole

      @if($user->distributor)
        var html1 = '<option value="{{ $user->distributor->id }}" selected="selected">';
        html1 += '{{ $user->distributor->sap_code . ' ' . $user->distributor->name }}';
        html1 += '</option>';

        $('#distributor_id').append(html1);
      @endif
    @endif
  });
</script>
@endpush