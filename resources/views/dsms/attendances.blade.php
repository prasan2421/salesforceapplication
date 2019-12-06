@extends('layouts.master')

@section('title', 'Attendances')

@section('content')
<h2 class="section-title">DSM: {{ $user->name }}</h2>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        {{ Form::open([ 'action' => [ 'DsmController@attendances', $user->id ], 'method' => 'GET', 'class' => 'form-inline' ]) }}
        @if(request()->mine)
        {{ Form::hidden('mine', request()->mine) }}
        @endif
        {{ Form::text('date', $date, [ 'class' => 'form-control mb-2 mr-sm-2 datepicker' ]) }}
        <button class="btn btn-primary mb-2" type="submit">Submit</button>
        {{ Form::close() }}
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table table-striped" id="data-table" style="width: 100%">
            <thead>                                 
              <tr>
                <th>Punch In</th>
                <th>Punch Out</th>
              </tr>
            </thead>
            <tbody>
              @forelse($attendances as $attendance)
                <tr>
                  <td>{{ $attendance->punch_in_time }}</td>
                  <td>{{ $attendance->punch_out_time }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="3">No Data Available</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection