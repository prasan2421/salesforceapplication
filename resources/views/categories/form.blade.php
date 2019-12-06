<div class="form-group">
  {{ Form::label('parent_id', 'Parent') }}
  {{ Form::select('parent_id', $categories, null, [ 'placeholder' => 'Select a category', 'class' => 'form-control' . ($errors->has('parent_id') ? ' is-invalid' : '') ])}}
  @if($errors->has('parent_id'))
  <div class="invalid-feedback">
    {{ $errors->first('parent_id') }}
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