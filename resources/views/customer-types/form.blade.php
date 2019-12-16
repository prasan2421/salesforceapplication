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
    {{ Form::label('product_type_id', 'Product Type') }}
    {{ Form::select('product_type_id', $productTypes, null, [ 'placeholder' => 'Select a product type', 'class' => 'form-control' . ($errors->has('product_type_id') ? ' is-invalid' : '') ])}}
    @if($errors->has('product_type_id'))
        <div class="invalid-feedback">
            {{ $errors->first('product_type_id') }}
        </div>
    @endif
</div>
