@extends('layouts.master')

@section('title', 'Sales Officer Beats')

@push('styles')
<style type="text/css">
#route_container {
  height: auto;
  padding: 5px;
}
#route_container:empty::before {
  content: 'No Beats';
  display: block;
  padding: 10px 15px;
}
.route-div {
  display: inline-block;
  padding: 10px 15px;
  margin: 5px;
  border-radius: 5px;
  background: #4f97bb;
  color: #fff;
}
.delete-route-btn {
  margin-left: 5px;
  color: #fff;
}
</style>
@endpush

@section('content')
<h2 class="section-title">Sales Officer: {{ $user->name }}</h2>

<div class="row">
  <div class="col-12">
    <div class="card">
      {{ Form::open([ 'action' => [ 'SalesOfficerController@saveRoutes', $user->id ] ]) }}
      <div class="card-body">
        <div class="form-group">
          {{ Form::label(null, 'Beats') }}
          <div id="route_container" class="form-control {{ $errors->has('route_ids') ? 'is-invalid' : '' }}"></div>
          @if($errors->has('route_ids'))
          <div class="invalid-feedback">
            {{ $errors->first('route_ids') }}
          </div>
          @endif
        </div>
      </div>
      <div class="card-footer">
        <a href="{{ action('SalesOfficerController@index') }}" class="btn btn-primary">Back to List</a>
        <button class="btn btn-primary" type="submit">Save</button>
      </div>
      {{ Form::close() }}
    </div>
  </div>
</div>

<h2 class="section-title">Add Beats</h2>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped" id="data-table" style="width: 100%">
            <thead>                                 
              <tr>
                <th>Beat Code</th>
                <th>Beat Name</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Action</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
  $(function() {
    var table = $('#data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ action("SalesOfficerController@getRoutesData")}}',
        columns: [
            { data: 'sap_code', name: 'sap_code' },
            { data: 'name', name: 'name' },
            { data: 'created_at', name: 'created_at' },
            { data: 'updated_at', name: 'updated_at' },
            { 
              data: 'action',
              defaultContent: '<button class="btn btn-icon btn-primary"><i class="fas fa-plus"></i></button>',
              orderable: false,
              searchable: false 
            }
        ],
        drawCallback: function(settings) {
          $('[data-toggle=tooltip]').tooltip();
        }
    });

    $('#data-table tbody').on('click', 'button', function() {
      var data = table.row($(this).parents('tr')).data();
      addBeat(data['_id'], data['sap_code'], data['name']);
    });

    function addBeat(id, sap_code, name) {
      if($('input[value=' + id + ']').length > 0) {
        return;
      }

      var html = '<div class="route-div">';
      html += '<input type="hidden" name="route_ids[]" value="' + id + '" />';
      html += '<input type="hidden" name="route_sap_codes[]" value="' + sap_code + '" />';
      html += '<input type="hidden" name="route_names[]" value="' + name + '" />';
      html += '<span>' + sap_code + ' ' + name + '</span>';
      html += '<a href="#" class="delete-route-btn"><i class="fas fa-times"></i></a>';
      html += '</div>';

      $('#route_container').append(html);
    }

    $('#route_container').on('click', '.delete-route-btn', function(e) {
      e.preventDefault();

      $(this).closest('.route-div').remove();
    });

    @if($errors->any())
      @if(is_array(old('route_ids')) && is_array(old('route_sap_codes')) && is_array(old('route_names')))
        @for($i = 0; $i < count(old('route_ids')); $i++)
          addBeat('{{ old("route_ids")[$i] }}', '{{ old("route_sap_codes")[$i] }}', '{{ old("route_names")[$i] }}');
        @endfor
      @endif
    @else
      @foreach($user->routes as $route)
        addBeat('{{ $route->_id }}', '{{ $route->sap_code }}', '{{ $route->name }}');
      @endforeach
    @endif
  });
</script>
@endpush
