@extends('layouts.master')

@section('title', 'DSMs')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      @role('admin|sales-officer')
      <div class="card-header">
        @role('admin|sales-officer')
        <a href="{{ action('DsmController@create', Common::addMineParam()) }}" class="btn btn-primary marginRight-10">Add DSM</a>
        @endrole
        @role('admin')
        {{--
        <a href="{{ action('DsmController@exportExcel') }}" class="btn btn-primary">Export to Excel</a>
        --}}
        <a href="{{ action('DsmController@exportCsv') }}" class="btn btn-primary marginRight-10">Export to CSV</a>
        <a href="{{ action('DsmController@exportWithBeatsCsv') }}" class="btn btn-primary marginRight-10">Export with Beats to CSV</a>
        @endrole
        @role('admin')
        {{--
        <a href="{{ action('DsmController@exportAttendancesExcel') }}" class="btn btn-primary">Export Attendances to Excel</a>
        --}}
        <a href="{{ action('DsmController@exportAttendancesCsv') }}" class="btn btn-primary marginRight-10">Export Attendances to CSV</a>
        <a href="{{ action('DsmController@exportCustomerVisitsCsv') }}" class="btn btn-primary">Export Customer Visits to CSV</a>
        @endrole
      </div>
      @endrole
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped" id="data-table" style="width: 100%">
            <thead>                                 
              <tr>
                <th>Name</th>
                <th>Username</th>
                <th>Email</th>
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
	        ajax: '{{ action("DsmController@getData", Common::addMineParam()) }}',
	        columns: [
	            { data: 'name', name: 'name' },
              { data: 'username', name: 'username' },
	            { data: 'email', name: 'email' },
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
