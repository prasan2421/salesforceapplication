@extends('layouts.master')

@section('title', 'Edit DSM Beat')

@section('content')
<h2 class="section-title">DSM: {{ $user->name }}</h2>

<div class="row">
  <div class="col-12">
    <div class="card">
      {{ Form::model($routeUser, [ 'action' => [ 'DsmRouteController@update', request()->user_id, $routeUser->id ], 'method' => 'PUT' ]) }}
      <div class="card-body">
        @include('dsm-routes.form')
      </div>
      <div class="card-footer">
        <a href="{{ action('DsmRouteController@index', request()->user_id) }}" class="btn btn-primary">Back to List</a>
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
    @else
      @if($routeUser->route)
        var html = '<option value="{{ $routeUser->route->id }}" selected="selected">';
        html += '{{ $routeUser->route->sap_code . ' ' . $routeUser->route->name }}';
        html += '</option>';

        $('#route_id').append(html);
      @endif
    @endif
  });
</script>
@endpush