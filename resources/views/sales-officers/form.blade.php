{{--
<div class="form-group">
  {{ Form::label('route_ids[]', 'Beats') }}
  {{ Form::select('route_ids[]', $routes, null, [ 'multiple' => true, 'class' => 'form-control select2' . ($errors->has('route_ids') ? ' is-invalid' : '') ])}}
  @if($errors->has('route_ids'))
  <div class="invalid-feedback">
    {{ $errors->first('route_ids') }}
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
  {{ Form::label('vertical_ids[]', 'Verticals') }}
  {{ Form::select('vertical_ids[]', [], null, [ 'multiple' => true, 'id' => 'vertical_ids', 'class' => 'form-control select2' . ($errors->has('vertical_ids') ? ' is-invalid' : '') ])}}
  @if($errors->has('vertical_ids'))
  <div class="invalid-feedback">
    {{ $errors->first('vertical_ids') }}
  </div>
  @endif
</div>

<div class="form-group">
  {{ Form::label('state_id', 'State') }}
  {{ Form::select('state_id', $states, null, [ 'placeholder' => 'Select a state', 'class' => 'form-control' . ($errors->has('state_id') ? ' is-invalid' : '') ])}}
  @if($errors->has('state_id'))
  <div class="invalid-feedback">
    {{ $errors->first('state_id') }}
  </div>
  @endif
</div>

<div class="form-group">
  {{ Form::label('emp_code', 'Emp Code') }}
  {{ Form::text('emp_code', null, [ 'class' => 'form-control' . ($errors->has('emp_code') ? ' is-invalid' : '') ])}}
  @if($errors->has('emp_code'))
  <div class="invalid-feedback">
    {{ $errors->first('emp_code') }}
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
  {{ Form::label('username', 'Username') }}
  {{ Form::text('username', null, [ 'class' => 'form-control' . ($errors->has('username') ? ' is-invalid' : '') ])}}
  @if($errors->has('username'))
  <div class="invalid-feedback">
    {{ $errors->first('username') }}
  </div>
  @endif
</div>

<div class="form-group">
  {{ Form::label('password', 'Password') }}
  {{ Form::password('password', [ 'class' => 'form-control' . ($errors->has('password') ? ' is-invalid' : '') ])}}
  @if($errors->has('password'))
  <div class="invalid-feedback">
    {{ $errors->first('password') }}
  </div>
  @endif
</div>

<div class="form-group">
  {{ Form::label('password_confirmation', 'Confirm Password') }}
  {{ Form::password('password_confirmation', [ 'class' => 'form-control' ])}}
</div>