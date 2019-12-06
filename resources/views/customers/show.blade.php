@extends('layouts.master')

@section('title', 'Customer Details')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="form-group">
          {{ Form::label('route_id', 'Route') }}
          {{ Form::text('route_id', $customer->route ? $customer->route->name : '', [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('customer_type_id', 'Customer Type') }}
          {{ Form::text('customer_type_id', $customer->customerType ? $customer->customerType->name : '', [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('customer_class_id', 'Customer Class') }}
          {{ Form::text('customer_class_id', $customer->customerClass ? $customer->customerClass->name : '', [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('sap_code', 'Retailer Code') }}
          {{ Form::text('sap_code', $customer->sap_code, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('name', 'Shop Name') }}
          {{ Form::text('name', $customer->name, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        {{--
        <div class="form-group">
          {{ Form::label('class', 'Class') }}
          {{ Form::text('class', $customer->class, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>
        --}}

        <div class="form-group">
          {{ Form::label('gst_number', 'Gst Number') }}
          {{ Form::text('gst_number', $customer->gst_number, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('town', 'Town') }}
          {{ Form::text('town', $customer->town, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('longitude', 'Longitude') }}
          {{ Form::text('longitude', $customer->longitude, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('latitude', 'Latitude') }}
          {{ Form::text('latitude', $customer->latitude, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('owner_name', 'Owner Name') }}
          {{ Form::text('owner_name', $customer->owner_name, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('owner_email', 'Owner Email') }}
          {{ Form::text('owner_email', $customer->owner_email, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('owner_contact_number', 'Owner Contact Number') }}
          {{ Form::text('owner_contact_number', $customer->owner_contact_number, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        {{--
        <div class="form-group">
          {{ Form::label('billing_state', 'Billing State') }}
          {{ Form::text('billing_state', $customer->billing_state, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>
        --}}

        <div class="form-group">
          {{ Form::label('billing_state_id', 'Billing State') }}
          {{ Form::text('billing_state_id', $customer->billingState ? $customer->billingState->name : '', [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('billing_district', 'Billing District') }}
          {{ Form::text('billing_district', $customer->billing_district, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('billing_city', 'Billing City') }}
          {{ Form::text('billing_city', $customer->billing_city, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('billing_address', 'Billing Address') }}
          {{ Form::text('billing_address', $customer->billing_address, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('billing_pincode', 'Billing Pincode') }}
          {{ Form::text('billing_pincode', $customer->billing_pincode, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        {{--
        <div class="form-group">
          {{ Form::label('shipping_state', 'Shipping State') }}
          {{ Form::text('shipping_state', $customer->shipping_state, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>
        --}}

        <div class="form-group">
          {{ Form::label('shipping_state_id', 'Shipping State') }}
          {{ Form::text('shipping_state_id', $customer->shippingState ? $customer->shippingState->name : '', [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('shipping_district', 'Shipping District') }}
          {{ Form::text('shipping_district', $customer->shipping_district, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('shipping_city', 'Shipping City') }}
          {{ Form::text('shipping_city', $customer->shipping_city, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('shipping_address', 'Shipping Address') }}
          {{ Form::text('shipping_address', $customer->shipping_address, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('shipping_pincode', 'Shipping Pincode') }}
          {{ Form::text('shipping_pincode', $customer->shipping_pincode, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>

        <div class="form-group">
          {{ Form::label('user', 'Added By') }}
          {{ Form::text('user', $customer->user ? $customer->user->name : '', [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection