<div class="form-group">
  {{ Form::label('division_id', 'Division') }}
  {{ Form::select('division_id', $divisions, null, [ 'placeholder' => 'Select a division', 'class' => 'form-control' . ($errors->has('division_id') ? ' is-invalid' : '') ])}}
  @if($errors->has('division_id'))
  <div class="invalid-feedback">
    {{ $errors->first('division_id') }}
  </div>
  @endif
</div>

<div class="form-group">
  {{ Form::label('vertical_id', 'Vertical') }}
  {{ Form::select('vertical_id', [], null, [ 'placeholder' => 'Select a vertical', 'class' => 'form-control' . ($errors->has('vertical_id') ? ' is-invalid' : '') ])}}
  @if($errors->has('vertical_id'))
  <div class="invalid-feedback">
    {{ $errors->first('vertical_id') }}
  </div>
  @endif
</div>

<div class="form-group">
  {{ Form::label('name', 'Name') }}
  {{ Form::text('name', null, [ 'class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : '') ])}}
  @if($errors->has('name'))
  <div class="invalid-feedback">
    {{ $errors->first('name') }}
  </div>
  @endif
</div>