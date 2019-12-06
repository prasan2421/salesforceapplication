@if(request()->mine)
{{ Form::hidden('mine', request()->mine) }}
@endif

@role('admin')
<div class="form-group">
  {{ Form::label('sales_officer_id', 'Sales Officer') }}
  {{ Form::select('sales_officer_id', [], null, [ 'class' => 'form-control' . ($errors->has('sales_officer_id') ? ' is-invalid' : '') ])}}
  @if($errors->has('sales_officer_id'))
  <div class="invalid-feedback">
    {{ $errors->first('sales_officer_id') }}
  </div>
  @endif
</div>
@endrole

@role('sales-officer')
@if(isset($user))
<div class="form-group">
  {{ Form::label('sales_officer_id', 'Sales Officer') }}
  {{ Form::text('sales_officer_id', $user->salesOfficer ? $user->salesOfficer->name : '', [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
</div>
<div class="form-group">
  <div class="form-check">
    {{ Form::checkbox('set_sales_officer', '1', null, [ 'class' => 'form-check-input', 'id' => 'set_sales_officer' ])}}
    {{ Form::label('set_sales_officer', 'Set myself as Sales Officer', [ 'class' => 'form-check-label' ]) }}
  </div>
</div>
@endif
@endrole

<div class="form-group">
  {{ Form::label('distributor_id', 'Distributor') }}
  {{ Form::select('distributor_id', [], null, [ 'class' => 'form-control' . ($errors->has('distributor_id') ? ' is-invalid' : '') ])}}
  @if($errors->has('distributor_id'))
  <div class="invalid-feedback">
    {{ $errors->first('distributor_id') }}
  </div>
  @endif
</div>

@role('admin')
<div class="form-group">
  {{ Form::label('division_id', 'Division') }}
  @if(isset($user) && $user->is_non_parakram)
  {{ Form::text('division_id', $user->division ? $user->division->name : '', [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
  @else
  {{ Form::select('division_id', $divisions, null, [ 'placeholder' => 'Select a division', 'class' => 'form-control' . ($errors->has('division_id') ? ' is-invalid' : '') ])}}
  @if($errors->has('division_id'))
  <div class="invalid-feedback">
    {{ $errors->first('division_id') }}
  </div>
  @endif
  @endif
</div>

<div class="form-group">
  {{ Form::label('vertical_ids[]', 'Verticals') }}
  @if(isset($user) && $user->is_non_parakram)
  {{ Form::select('vertical_ids[]', $verticals, null, [ 'multiple' => true, 'class' => 'form-control select2' . ($errors->has('vertical_ids') ? ' is-invalid' : '') ])}}
  @else
  {{ Form::select('vertical_ids[]', [], null, [ 'multiple' => true, 'id' => 'vertical_ids', 'class' => 'form-control select2' . ($errors->has('vertical_ids') ? ' is-invalid' : '') ])}}
  @endif
  @if($errors->has('vertical_ids'))
  <div class="invalid-feedback">
    {{ $errors->first('vertical_ids') }}
  </div>
  @endif
</div>
@endrole

@role('sales-officer')
<div class="form-group">
  {{ Form::label('vertical_ids[]', 'Verticals') }}
  {{ Form::select('vertical_ids[]', $verticals, null, [ 'multiple' => true, 'class' => 'form-control select2' . ($errors->has('vertical_ids') ? ' is-invalid' : '') ])}}
  @if($errors->has('vertical_ids'))
  <div class="invalid-feedback">
    {{ $errors->first('vertical_ids') }}
  </div>
  @endif
</div>
@endrole

<div class="form-group">
  {{ Form::label('state_id', 'State') }}
  @if(isset($user) && $user->is_non_parakram)
  {{ Form::text('state_id', $user->state ? $user->state->name : '', [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
  @else
  {{ Form::select('state_id', $states, null, [ 'placeholder' => 'Select a state', 'class' => 'form-control' . ($errors->has('state_id') ? ' is-invalid' : '') ])}}
  @if($errors->has('state_id'))
  <div class="invalid-feedback">
    {{ $errors->first('state_id') }}
  </div>
  @endif
  @endif
</div>

@if(isset($user))
<div class="form-group">
  {{ Form::label('is_non_parakram', 'Is Non Parakram?') }}
  {{ Form::text('is_non_parakram', $user->is_non_parakram ? 'Yes' : 'No', [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
</div>
@else
<div class="form-group">
  <div class="form-check">
    {{ Form::checkbox('is_non_parakram', '1', null, [ 'class' => 'form-check-input', 'id' => 'is_non_parakram' ])}}
    {{ Form::label('is_non_parakram', 'Is Non Parakram?', [ 'class' => 'form-check-label' ]) }}
  </div>
</div>
@endif

<div class="form-group">
  {{ Form::label('emp_code', 'Emp Code') }}
  @if(isset($user) && $user->is_non_parakram)
  {{ Form::text('emp_code', $user->emp_code, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
  @else
  {{ Form::text('emp_code', null, [ 'class' => 'form-control' . ($errors->has('emp_code') ? ' is-invalid' : '') ])}}
  @if($errors->has('emp_code'))
  <div class="invalid-feedback">
    {{ $errors->first('emp_code') }}
  </div>
  @endif
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
  @if(isset($user) && $user->is_non_parakram)
  {{ Form::text('username', $user->username, [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
  @else
  {{ Form::text('username', null, [ 'class' => 'form-control' . ($errors->has('username') ? ' is-invalid' : '') ])}}
  @if($errors->has('username'))
  <div class="invalid-feedback">
    {{ $errors->first('username') }}
  </div>
  @endif
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