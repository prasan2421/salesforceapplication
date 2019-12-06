<div class="form-group">
  {{ Form::label('parent_id', 'Parent') }}
  @if(isset($user))
  {{ Form::text('parent_id', $user->parent ? $user->parent->name : 'No Parent', [ 'class' => 'form-control-plaintext', 'readonly' => true ]) }}
  @else
  {{ Form::select('parent_id', $users, null, [ 'placeholder' => 'Select a user', 'class' => 'form-control' . ($errors->has('parent_id') ? ' is-invalid' : '') ])}}
  @if($errors->has('parent_id'))
  <div class="invalid-feedback">
    {{ $errors->first('parent_id') }}
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