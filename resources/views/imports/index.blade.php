@extends('layouts.master')

@section('title', 'Import History')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped" id="data-table" style="width: 100%">
            <thead>                                 
              <tr>
                <th>ID</th>
                <th>Type</th>
                <th>Filename</th>
                <th>Status</th>
                <th>Remarks</th>
                <!-- <th>Is Success</th> -->
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
	        ajax: '{{ action("ImportController@getData")}}',
	        columns: [
	            { data: '_id', name: '_id' },
              { data: 'type', name: 'type' },
              { data: 'filename', name: 'filename' },
              { data: 'status', name: 'status' },
              { data: 'remarks', name: 'remarks' },
              // { data: 'is_success', name: 'is_success' },
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
