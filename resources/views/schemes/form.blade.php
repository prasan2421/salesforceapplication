<div class="form-group">
  {{ Form::label('product_id', 'Product') }}
  {{ Form::select('product_id', $products, null, [ 'placeholder' => 'Select a product', 'class' => 'form-control' . ($errors->has('product_id') ? ' is-invalid' : '') ])}}
  @if($errors->has('product_id'))
  <div class="invalid-feedback">
    {{ $errors->first('product_id') }}
  </div>
  @endif
</div>

<div class="form-group">
  {{ Form::label('start_date', 'Start Date') }}
  {{ Form::text('start_date', null, [ 'class' => 'form-control datepicker' . ($errors->has('start_date') ? ' is-invalid' : '') ])}}
  @if($errors->has('start_date'))
  <div class="invalid-feedback">
    {{ $errors->first('start_date') }}
  </div>
  @endif
</div>

<div class="form-group">
  {{ Form::label('end_date', 'End Date') }}
  {{ Form::text('end_date', null, [ 'class' => 'form-control datepicker' . ($errors->has('end_date') ? ' is-invalid' : '') ])}}
  @if($errors->has('end_date'))
  <div class="invalid-feedback">
    {{ $errors->first('end_date') }}
  </div>
  @endif
</div>

<div class="form-group">
  {{ Form::label('discount', 'Discount (%)') }}
  {{ Form::text('discount', null, [ 'class' => 'form-control' . ($errors->has('discount') ? ' is-invalid' : '') ])}}
  @if($errors->has('discount'))
  <div class="invalid-feedback">
    {{ $errors->first('discount') }}
  </div>
  @endif
</div>