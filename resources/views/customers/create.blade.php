@extends('layouts.master')

@section('title', 'Add Customer')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      {{ Form::open([ 'action' => 'CustomerController@store' ]) }}
      <div class="card-body">
        @include('customers.form')
      </div>
      <div class="card-footer">
        <a href="{{ action('CustomerController@index', Common::addMineParam()) }}" class="btn btn-primary">Back to List</a>
        <button class="btn btn-primary" type="submit">Add</button>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
  $(document).ready(function() {
    $('#copy_billing_address').click(function() {
      // $('#shipping_state').val($('#billing_state').val());
      $('#shipping_state_id').val($('#billing_state_id').val());
      $('#shipping_district').val($('#billing_district').val());
      $('#shipping_city').val($('#billing_city').val());
      $('#shipping_address').val($('#billing_address').val());
      $('#shipping_pincode').val($('#billing_pincode').val());
    });

    $('#route_id').select2({
      ajax: {
        url: '{{ action('RouteController@search') }}',
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
       var selection = $('#route_id').select2('data');
       if(selection.length > 0) {
        $('form').append('<input type="hidden" name="route_text" value="' + selection[0].text + '" />');
       }
    });

    @if($errors->any())
      @if(old('route_id'))
        var html = '<option value="{{ old('route_id') }}" selected="selected">';
        html += '{{ old('route_text') }}';
        html += '</option>';

        $('#route_id').append(html);
      @endif
    @endif
  });
</script>
@endpush