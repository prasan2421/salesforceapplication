@extends('layouts.master')

@section('title', 'Add My Beat')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      {{ Form::open([ 'action' => 'MyRouteController@store' ]) }}
      <div class="card-body">
        @include('my-routes.form')
      </div>
      <div class="card-footer">
        <a href="{{ action('MyRouteController@index') }}" class="btn btn-primary">Back to List</a>
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
    $('#route_id').select2({
      ajax: {
        url: '{{ action('RouteController@search') }}',
        dataType: 'json',
        delay: 250,
        data: function(params) {
          var query = {
            state_id: $('#state_id').val().trim(),
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