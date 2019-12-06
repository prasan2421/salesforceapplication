@extends('layouts.master')

@section('title', 'Map')

@push('scripts')
<style type="text/css">
#map {
  height: 400px;
}
</style>
@endpush

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div id="map"></div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
var map;

function initMap() {
  map = new google.maps.Map(document.getElementById('map'), {
    center: {
      lat: {{ count($geolocations) > 0 ? $geolocations[0]->latitude : 19.130968 }},
      lng: {{ count($geolocations) > 0 ? $geolocations[0]->longitude : 72.873064 }}
    },
    zoom: 18
  });

  @foreach($geolocations as $geolocation)
  new google.maps.Marker({
    position: {
      lat: {{ $geolocation->latitude }},
      lng: {{ $geolocation->longitude }}
    },
    map: map
  });
  @endforeach

  var coordinates = [];
  @foreach($geolocations as $geolocation)
    coordinates.push({
      lat: {{ $geolocation->latitude }},
      lng: {{ $geolocation->longitude }}
    });
  @endforeach
  var polyline = new google.maps.Polyline({
    path: coordinates,
    geodesic: true,
    strokeColor: '#FF0000',
    strokeOpacity: 1.0,
    strokeWeight: 2
  });

  polyline.setMap(map);
}
</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBYa_eq6Tu4zbFTx5X2CgGDFgh5OxCLGis&callback=initMap"
    async defer></script>
@endpush