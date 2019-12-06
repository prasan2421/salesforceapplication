<div class="form-group">
  {{ Form::label('code', 'Code') }}
  {{ Form::text('code', null, [ 'class' => 'form-control' . ($errors->has('code') ? ' is-invalid' : '') ])}}
  @if($errors->has('code'))
  <div class="invalid-feedback">
    {{ $errors->first('code') }}
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

<div class="form-group">
  {{ Form::label('abbreviation', 'Abbreviation') }}
  {{ Form::text('abbreviation', null, [ 'class' => 'form-control' . ($errors->has('abbreviation') ? ' is-invalid' : '') ])}}
  @if($errors->has('abbreviation'))
  <div class="invalid-feedback">
    {{ $errors->first('abbreviation') }}
  </div>
  @endif
</div>