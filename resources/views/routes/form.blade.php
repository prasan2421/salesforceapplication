@role('admin')
<div class="form-group">
  {{ Form::label('division_id', 'Division') }}
  @if(isset($route))
  {{ Form::text('division_id', $route->division ? $route->division->name : '', [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
  @else
  {{ Form::select('division_id', $divisions, null, [ 'placeholder' => 'Select a division', 'class' => 'form-control' . ($errors->has('division_id') ? ' is-invalid' : '') ])}}
  @if($errors->has('division_id'))
  <div class="invalid-feedback">
    {{ $errors->first('division_id') }}
  </div>
  @endif
  @endif
</div>
@endrole

<div class="form-group">
  {{ Form::label('state_id', 'State') }}
  @if(isset($route))
  {{ Form::text('state_id', $route->state ? $route->state->name : '', [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
  @else
  {{ Form::select('state_id', $states, null, [ 'placeholder' => 'Select a state', 'class' => 'form-control' . ($errors->has('state_id') ? ' is-invalid' : '') ])}}
  @if($errors->has('state_id'))
  <div class="invalid-feedback">
    {{ $errors->first('state_id') }}
  </div>
  @endif
  @endif
</div>

{{--
<div class="form-group">
  {{ Form::label('sap_code', 'Beat Code') }}
  {{ Form::text('sap_code', null, [ 'class' => 'form-control' . ($errors->has('sap_code') ? ' is-invalid' : '') ])}}
  @if($errors->has('sap_code'))
  <div class="invalid-feedback">
    {{ $errors->first('sap_code') }}
  </div>
  @endif
</div>
--}}

<div class="form-group">
  {{ Form::label('name', 'Beat Name') }}
  {{ Form::text('name', null, [ 'class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : '') ])}}
  @if($errors->has('name'))
  <div class="invalid-feedback">
    {{ $errors->first('name') }}
  </div>
  @endif
</div>