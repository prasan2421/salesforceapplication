@extends('layouts.master')

@section('title', 'Orders')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        @role('admin|sales-officer')
        {{--
        <a href="{{ action('OrderController@exportExcel') }}" class="btn btn-primary">Export to Excel</a>
        <a href="{{ action('OrderController@exportSummaryExcel') }}" class="btn btn-primary">Export Summary to Excel</a>
        --}}
        <a href="{{ action('OrderController@exportCsv') }}" class="btn btn-primary" style="
    margin-right: 10px;
">Export to CSV</a>
        <a href="{{ action('OrderController@exportSummaryCsv') }}" class="btn btn-primary">Export Summary to CSV</a>
        @endrole
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped" id="data-table" style="width: 100%">
            <thead>                                 
              <tr>
                <th>Order Number</th>
                <!-- <th>DSM</th> -->
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
	        ajax: '{{ action("OrderController@getData")}}',
	        columns: [
	            { data: '_id', name: '_id' },
              // { data: 'user_name', name: 'user_name' },
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
