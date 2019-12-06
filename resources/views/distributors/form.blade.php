<div class="form-group">
  {{ Form::label('vertical_ids[]', 'Verticals') }}
  {{ Form::select('vertical_ids[]', $verticals, null, [ 'multiple' => true, 'class' => 'form-control select2' . ($errors->has('vertical_ids') ? ' is-invalid' : '') ])}}
  @if($errors->has('vertical_ids'))
  <div class="invalid-feedback">
    {{ $errors->first('vertical_ids') }}
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
  {{ Form::label('email', 'Email') }}
  {{ Form::email('email', null, [ 'class' => 'form-control' . ($errors->has('email') ? ' is-invalid' : '') ])}}
  @if($errors->has('email'))
  <div class="invalid-feedback">
    {{ $errors->first('email') }}
  </div>
  @endif
</div>

<div class="form-group">
  {{ Form::label('contact_number', 'Contact Number') }}
  {{ Form::text('contact_number', null, [ 'class' => 'form-control' . ($errors->has('contact_number') ? ' is-invalid' : '') ])}}
  @if($errors->has('contact_number'))
  <div class="invalid-feedback">
    {{ $errors->first('contact_number') }}
  </div>
  @endif
</div>

<div class="form-group">
  {{ Form::label('city', 'City') }}
  {{ Form::text('city', null, [ 'class' => 'form-control' . ($errors->has('city') ? ' is-invalid' : '') ])}}
  @if($errors->has('city'))
  <div class="invalid-feedback">
    {{ $errors->first('city') }}
  </div>
  @endif
</div>

<div class="form-group">
  {{ Form::label('district', 'District') }}
  {{ Form::text('district', null, [ 'class' => 'form-control' . ($errors->has('district') ? ' is-invalid' : '') ])}}
  @if($errors->has('district'))
  <div class="invalid-feedback">
    {{ $errors->first('district') }}
  </div>
  @endif
</div>

<div class="form-group">
  {{ Form::label('state', 'State') }}
  {{ Form::text('state', null, [ 'class' => 'form-control' . ($errors->has('state') ? ' is-invalid' : '') ])}}
  @if($errors->has('state'))
  <div class="invalid-feedback">
    {{ $errors->first('state') }}
  </div>
  @endif
</div>

<div class="form-group">
  {{ Form::label('region', 'Region') }}
  {{ Form::text('region', null, [ 'class' => 'form-control' . ($errors->has('region') ? ' is-invalid' : '') ])}}
  @if($errors->has('region'))
  <div class="invalid-feedback">
    {{ $errors->first('region') }}
  </div>
  @endif
</div>