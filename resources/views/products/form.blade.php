{{--
<div class="form-group">
  {{ Form::label('category_id', 'Category') }}
  {{ Form::select('category_id', $categories, null, [ 'placeholder' => 'Select a category', 'class' => 'form-control' . ($errors->has('category_id') ? ' is-invalid' : '') ])}}
  @if($errors->has('category_id'))
  <div class="invalid-feedback">
    {{ $errors->first('category_id') }}
  </div>
  @endif
</div>
--}}

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
  {{ Form::label('brand_id', 'Brand') }}
  {{ Form::select('brand_id', [], null, [ 'placeholder' => 'Select a brand', 'class' => 'form-control' . ($errors->has('brand_id') ? ' is-invalid' : '') ])}}
  @if($errors->has('brand_id'))
  <div class="invalid-feedback">
    {{ $errors->first('brand_id') }}
  </div>
  @endif
</div>

<div class="form-group">
  {{ Form::label('sap_code', 'SAP Code') }}
  {{ Form::text('sap_code', null, [ 'class' => 'form-control' . ($errors->has('sap_code') ? ' is-invalid' : '') ])}}
  @if($errors->has('sap_code'))
  <div class="invalid-feedback">
    {{ $errors->first('sap_code') }}
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
  {{ Form::label('unit_id', 'Unit') }}
  {{ Form::select('unit_id', $units, null, [ 'placeholder' => 'Select a unit', 'class' => 'form-control' . ($errors->has('unit_id') ? ' is-invalid' : '') ])}}
  @if($errors->has('unit_id'))
  <div class="invalid-feedback">
    {{ $errors->first('unit_id') }}
  </div>
  @endif
</div>

<div class="form-group">
  <div class="form-check">
    {{ Form::checkbox('is_featured', '1', null, [ 'class' => 'form-check-input', 'id' => 'is_featured' ])}}
    {{ Form::label('is_featured', 'Is Featured Product?', [ 'class' => 'form-check-label' ]) }}
  </div>
</div>