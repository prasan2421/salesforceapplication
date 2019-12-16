@if(request()->mine)
{{ Form::hidden('mine', request()->mine) }}
@endif

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
  {{ Form::label('customer_type_id', 'Customer Type') }}
  {{ Form::select('customer_type_id', $customerTypes, null, [ 'placeholder' => 'Select a customer type', 'class' => 'form-control' . ($errors->has('customer_type_id') ? ' is-invalid' : '') ])}}
  @if($errors->has('customer_type_id'))
  <div class="invalid-feedback">
    {{ $errors->first('customer_type_id') }}
  </div>
  @endif
</div>



<div class="form-group">
  {{ Form::label('customer_class_id', 'Customer Class') }}
  {{ Form::select('customer_class_id', $customerClasses, null, [ 'placeholder' => 'Select a customer class', 'class' => 'form-control' . ($errors->has('customer_class_id') ? ' is-invalid' : '') ])}}
  @if($errors->has('customer_class_id'))
  <div class="invalid-feedback">
    {{ $errors->first('customer_class_id') }}
  </div>
  @endif
</div>

{{--
<div class="form-group">
  {{ Form::label('sap_code', 'Retailer Code') }}
  {{ Form::text('sap_code', null, [ 'class' => 'form-control' . ($errors->has('sap_code') ? ' is-invalid' : '') ])}}
  @if($errors->has('sap_code'))
  <div class="invalid-feedback">
    {{ $errors->first('sap_code') }}
  </div>
  @endif
</div>
--}}

<div class="form-group">
  {{ Form::label('name', 'Shop Name') }}
  {{ Form::text('name', null, [ 'class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : '') ])}}
  @if($errors->has('name'))
  <div class="invalid-feedback">
    {{ $errors->first('name') }}
  </div>
  @endif
</div>

{{--
<div class="form-group">
  {{ Form::label('class', 'Class') }}
  {{ Form::text('class', null, [ 'class' => 'form-control' . ($errors->has('class') ? ' is-invalid' : '') ])}}
  @if($errors->has('class'))
  <div class="invalid-feedback">
    {{ $errors->first('class') }}
  </div>
  @endif
</div>
--}}

<div class="form-group">
  {{ Form::label('gst_number', 'GST Number') }}
  {{ Form::text('gst_number', null, [ 'class' => 'form-control' . ($errors->has('gst_number') ? ' is-invalid' : '') ])}}
  @if($errors->has('gst_number'))
  <div class="invalid-feedback">
    {{ $errors->first('gst_number') }}
  </div>
  @endif
</div>

<div class="form-group">
  {{ Form::label('town', 'Town') }}
  {{ Form::text('town', null, [ 'class' => 'form-control' . ($errors->has('town') ? ' is-invalid' : '') ])}}
  @if($errors->has('town'))
  <div class="invalid-feedback">
    {{ $errors->first('town') }}
  </div>
  @endif
</div>

<div class="form-group">
  {{ Form::label('longitude', 'Longitude') }}
  {{ Form::text('longitude', null, [ 'class' => 'form-control' . ($errors->has('longitude') ? ' is-invalid' : '') ])}}
  @if($errors->has('longitude'))
  <div class="invalid-feedback">
    {{ $errors->first('longitude') }}
  </div>
  @endif
</div>

<div class="form-group">
  {{ Form::label('latitude', 'Latitude') }}
  {{ Form::text('latitude', null, [ 'class' => 'form-control' . ($errors->has('latitude') ? ' is-invalid' : '') ])}}
  @if($errors->has('latitude'))
  <div class="invalid-feedback">
    {{ $errors->first('latitude') }}
  </div>
  @endif
</div>

<div class="form-group">
  {{ Form::label('owner_name', 'Owner Name') }}
  {{ Form::text('owner_name', null, [ 'class' => 'form-control' . ($errors->has('owner_name') ? ' is-invalid' : '') ])}}
  @if($errors->has('owner_name'))
  <div class="invalid-feedback">
    {{ $errors->first('owner_name') }}
  </div>
  @endif
</div>

<div class="form-group">
  {{ Form::label('owner_email', 'Owner Email') }}
  {{ Form::text('owner_email', null, [ 'class' => 'form-control' . ($errors->has('owner_email') ? ' is-invalid' : '') ])}}
  @if($errors->has('owner_email'))
  <div class="invalid-feedback">
    {{ $errors->first('owner_email') }}
  </div>
  @endif
</div>

<div class="form-group">
  {{ Form::label('owner_contact_number', 'Owner Contact Number') }}
  {{ Form::text('owner_contact_number', null, [ 'class' => 'form-control' . ($errors->has('owner_contact_number') ? ' is-invalid' : '') ])}}
  @if($errors->has('owner_contact_number'))
  <div class="invalid-feedback">
    {{ $errors->first('owner_contact_number') }}
  </div>
  @endif
</div>

{{--
<div class="form-group">
  {{ Form::label('billing_state', 'Billing State') }}
  {{ Form::text('billing_state', null, [ 'class' => 'form-control' . ($errors->has('billing_state') ? ' is-invalid' : '') ])}}
  @if($errors->has('billing_state'))
  <div class="invalid-feedback">
    {{ $errors->first('billing_state') }}
  </div>
  @endif
</div>
--}}

<div class="form-group">
  {{ Form::label('billing_state_id', 'Billing State') }}
  {{ Form::select('billing_state_id', $states, null, [ 'placeholder' => 'Select a billing state', 'class' => 'form-control' . ($errors->has('billing_state_id') ? ' is-invalid' : '') ])}}
  @if($errors->has('billing_state_id'))
  <div class="invalid-feedback">
    {{ $errors->first('billing_state_id') }}
  </div>
  @endif
</div>

<div class="form-group">
  {{ Form::label('billing_district', 'Billing District') }}
  {{ Form::text('billing_district', null, [ 'class' => 'form-control' . ($errors->has('billing_district') ? ' is-invalid' : '') ])}}
  @if($errors->has('billing_district'))
  <div class="invalid-feedback">
    {{ $errors->first('billing_district') }}
  </div>
  @endif
</div>

<div class="form-group">
  {{ Form::label('billing_city', 'Billing City') }}
  {{ Form::text('billing_city', null, [ 'class' => 'form-control' . ($errors->has('billing_city') ? ' is-invalid' : '') ])}}
  @if($errors->has('billing_city'))
  <div class="invalid-feedback">
    {{ $errors->first('billing_city') }}
  </div>
  @endif
</div>

<div class="form-group">
  {{ Form::label('billing_address', 'Billing Address') }}
  {{ Form::text('billing_address', null, [ 'class' => 'form-control' . ($errors->has('billing_address') ? ' is-invalid' : '') ])}}
  @if($errors->has('billing_address'))
  <div class="invalid-feedback">
    {{ $errors->first('billing_address') }}
  </div>
  @endif
</div>

<div class="form-group">
  {{ Form::label('billing_pincode', 'Billing Pincode') }}
  {{ Form::text('billing_pincode', null, [ 'class' => 'form-control' . ($errors->has('billing_pincode') ? ' is-invalid' : '') ])}}
  @if($errors->has('billing_pincode'))
  <div class="invalid-feedback">
    {{ $errors->first('billing_pincode') }}
  </div>
  @endif
</div>

<div class="form-group">
  <button type="button" class="btn btn-primary" id="copy_billing_address">Copy Billing Address to Shipping Address</button>
</div>

{{--
<div class="form-group">
  {{ Form::label('shipping_state', 'Shipping State') }}
  {{ Form::text('shipping_state', null, [ 'class' => 'form-control' . ($errors->has('shipping_state') ? ' is-invalid' : '') ])}}
  @if($errors->has('shipping_state'))
  <div class="invalid-feedback">
    {{ $errors->first('shipping_state') }}
  </div>
  @endif
</div>
--}}

<div class="form-group">
  {{ Form::label('shipping_state_id', 'Shipping State') }}
  {{ Form::select('shipping_state_id', $states, null, [ 'placeholder' => 'Select a shipping state', 'class' => 'form-control' . ($errors->has('shipping_state_id') ? ' is-invalid' : '') ])}}
  @if($errors->has('shipping_state_id'))
  <div class="invalid-feedback">
    {{ $errors->first('shipping_state_id') }}
  </div>
  @endif
</div>

<div class="form-group">
  {{ Form::label('shipping_district', 'Shipping District') }}
  {{ Form::text('shipping_district', null, [ 'class' => 'form-control' . ($errors->has('shipping_district') ? ' is-invalid' : '') ])}}
  @if($errors->has('shipping_district'))
  <div class="invalid-feedback">
    {{ $errors->first('shipping_district') }}
  </div>
  @endif
</div>

<div class="form-group">
  {{ Form::label('shipping_city', 'Shipping City') }}
  {{ Form::text('shipping_city', null, [ 'class' => 'form-control' . ($errors->has('shipping_city') ? ' is-invalid' : '') ])}}
  @if($errors->has('shipping_city'))
  <div class="invalid-feedback">
    {{ $errors->first('shipping_city') }}
  </div>
  @endif
</div>

<div class="form-group">
  {{ Form::label('shipping_address', 'Shipping Address') }}
  {{ Form::text('shipping_address', null, [ 'class' => 'form-control' . ($errors->has('shipping_address') ? ' is-invalid' : '') ])}}
  @if($errors->has('shipping_address'))
  <div class="invalid-feedback">
    {{ $errors->first('shipping_address') }}
  </div>
  @endif
</div>

<div class="form-group">
  {{ Form::label('shipping_pincode', 'Shipping Pincode') }}
  {{ Form::text('shipping_pincode', null, [ 'class' => 'form-control' . ($errors->has('shipping_pincode') ? ' is-invalid' : '') ])}}
  @if($errors->has('shipping_pincode'))
  <div class="invalid-feedback">
    {{ $errors->first('shipping_pincode') }}
  </div>
  @endif
</div>
