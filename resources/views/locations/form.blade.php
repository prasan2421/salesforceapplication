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
  {{ Form::label('description', 'Description') }}
  {{ Form::textarea('description', null, [ 'class' => 'form-control' . ($errors->has('description') ? ' is-invalid' : '') ])}}
  @if($errors->has('description'))
  <div class="invalid-feedback">
    {{ $errors->first('description') }}
  </div>
  @endif
</div>