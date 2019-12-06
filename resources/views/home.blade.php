@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
<div class="row">
	<div class="col-lg-3 col-md-6 col-sm-6 col-12">
	  <div class="card card-statistic-1">
	    <div class="card-icon bg-primary">
	      <i class="fas fa-tags"></i>
	    </div>
	    <div class="card-wrap">
	      <div class="card-header">
	        <h4>Products</h4>
	      </div>
	      <div class="card-body">
	        87
	      </div>
	    </div>
	  </div>
	</div>
	<div class="col-lg-3 col-md-6 col-sm-6 col-12">
	  <div class="card card-statistic-1">
	    <div class="card-icon bg-danger">
	      <i class="fas fa-store-alt"></i>
	    </div>
	    <div class="card-wrap">
	      <div class="card-header">
	        <h4>Customers</h4>
	      </div>
	      <div class="card-body">
	        12
	      </div>
	    </div>
	  </div>
	</div>
	<div class="col-lg-3 col-md-6 col-sm-6 col-12">
	  <div class="card card-statistic-1">
	    <div class="card-icon bg-warning">
	      <i class="fas fa-list"></i>
	    </div>
	    <div class="card-wrap">
	      <div class="card-header">
	        <h4>Orders</h4>
	      </div>
	      <div class="card-body">
	        50
	      </div>
	    </div>
	  </div>
	</div>
	<div class="col-lg-3 col-md-6 col-sm-6 col-12">
	  <div class="card card-statistic-1">
	    <div class="card-icon bg-success">
	      <i class="fas fa-users"></i>
	    </div>
	    <div class="card-wrap">
	      <div class="card-header">
	        <h4>Users</h4>
	      </div>
	      <div class="card-body">
	        25
	      </div>
	    </div>
	  </div>
	</div>
</div>

<div class="row">
  <div class="col-12 col-md-6 col-lg-6">
    <div class="card">
      <div class="card-header">
        <h4>Daily Visitors</h4>
      </div>
      <div class="card-body">
        <canvas id="myChart"></canvas>
      </div>
    </div>
  </div>
  <div class="col-12 col-md-6 col-lg-6">
    <div class="card">
      <div class="card-header">
        <h4>Monthly Visitors</h4>
      </div>
      <div class="card-body">
        <canvas id="myChart2"></canvas>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
$(document).ready(function() {
	var ctx = document.getElementById("myChart").getContext('2d');
	var myChart = new Chart(ctx, {
	  type: 'line',
	  data: {
	    labels: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
	    datasets: [{
	      label: 'Statistics',
	      data: [460, 458, 330, 502, 430, 610, 488],
	      borderWidth: 2,
	      backgroundColor: '#4f97bb',
	      borderColor: '#4f97bb',
	      borderWidth: 2.5,
	      pointBackgroundColor: '#ffffff',
	      pointRadius: 4
	    }]
	  },
	  options: {
	    legend: {
	      display: false
	    },
	    scales: {
	      yAxes: [{
	        gridLines: {
	          drawBorder: false,
	          color: '#f2f2f2',
	        },
	        ticks: {
	          beginAtZero: true,
	          stepSize: 150
	        }
	      }],
	      xAxes: [{
	        ticks: {
	          display: false
	        },
	        gridLines: {
	          display: false
	        }
	      }]
	    },
	  }
	});

	var ctx = document.getElementById("myChart2").getContext('2d');
	var myChart = new Chart(ctx, {
	  type: 'bar',
	  data: {
	    labels: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
	    datasets: [{
	      label: 'Statistics',
	      data: [460, 458, 330, 502, 430, 610, 488, 575, 404, 340, 450, 550],
	      borderWidth: 2,
	      backgroundColor: '#4f97bb',
	      borderColor: '#4f97bb',
	      borderWidth: 2.5,
	      pointBackgroundColor: '#ffffff',
	      pointRadius: 4
	    }]
	  },
	  options: {
	    legend: {
	      display: false
	    },
	    scales: {
	      yAxes: [{
	        gridLines: {
	          drawBorder: false,
	          color: '#f2f2f2',
	        },
	        ticks: {
	          beginAtZero: true,
	          stepSize: 150
	        }
	      }],
	      xAxes: [{
	        ticks: {
	          display: false
	        },
	        gridLines: {
	          display: false
	        }
	      }]
	    },
	  }
	});
});
</script>
@endpush
