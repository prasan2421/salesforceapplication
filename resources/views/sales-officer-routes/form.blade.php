<div class="form-group">
  {{ Form::label('state_id', 'State') }}
  {{ Form::select(null, $states, null, [ 'placeholder' => 'All States', 'class' => 'form-control', 'id' => 'state_id' ])}}
</div>

<div class="form-group">
  {{ Form::label('route_id', 'Beat') }}
  {{ Form::select('route_id', [], null, [ 'class' => 'form-control' . ($errors->has('route_id') ? ' is-invalid' : '') ])}}
  @if($errors->has('route_id'))
  <div class="invalid-feedback">
    {{ $errors->first('route_id') }}
  </div>
  @endif
</div>

<div class="form-group">
  {{ Form::label('frequency', 'Frequency') }}
  {{ Form::select('frequency', $frequencies, null, [ 'placeholder' => 'Select a frequency', 'class' => 'form-control' . ($errors->has('frequency') ? ' is-invalid' : '') ])}}
  @if($errors->has('frequency'))
  <div class="invalid-feedback">
    {{ $errors->first('frequency') }}
  </div>
  @endif
</div>

<div class="form-group">
  {{ Form::label('day', 'Day') }}
  {{ Form::select('day', $days, null, [ 'placeholder' => 'Select a day', 'class' => 'form-control' . ($errors->has('day') ? ' is-invalid' : '') ])}}
  @if($errors->has('day'))
  <div class="invalid-feedback">
    {{ $errors->first('day') }}
  </div>
  @endif
</div>