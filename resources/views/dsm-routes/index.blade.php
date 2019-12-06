@extends('layouts.master')

@section('title', 'DSM Beats')

@section('content')
<h2 class="section-title">DSM: {{ $user->name }}</h2>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <a href="{{ action('DsmRouteController@create', request()->user_id) }}" class="btn btn-primary">Add DSM Beat</a>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped" id="data-table" style="width: 100%">
            <thead>                                 
              <tr>
                <th>Beat Code</th>
                <th>Beat Name</th>
                <th>Frequency</th>
                <th>Day</th>
                <th>Is Active?</th>
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
		$('#data-table').DataTable({
	        processing: true,
	        serverSide: true,
	        ajax: '{{ action("DsmRouteController@getData", request()->user_id)}}',
	        columns: [
	            { data: 'route_sap_code', name: 'route_sap_code' },
              { data: 'route_name', name: 'route_name' },
              { data: 'frequency', name: 'frequency' },
              { data: 'day', name: 'day' },
              { data: 'is_active', name: 'is_active' },
	            { data: 'created_at', name: 'created_at' },
	            { data: 'updated_at', name: 'updated_at' },
              { data: 'action', name: 'action', orderable: false, searchable: false }
	        ],
          drawCallback: function(settings) {
            $('[data-toggle=tooltip]').tooltip();
          }
	    });
    });
</script>
@endpush
