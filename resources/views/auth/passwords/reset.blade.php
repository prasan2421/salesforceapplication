@extends('layouts.plain')

@section('title', __('Reset Password'))

@section('content')
<div class="card card-primary">
  <div class="card-header"><h4>{{ __('Reset Password') }}</h4></div>

  <div class="card-body">
    <form method="POST" action="{{ route('password.update') }}">
      @csrf

      <input type="hidden" name="token" value="{{ $token }}">

      <div class="form-group">
        <label for="email">{{ __('E-Mail Address') }}</label>
        <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ $email ?? old('email') }}" tabindex="1" required autofocus>
        @if ($errors->has('email'))
        <div class="invalid-feedback">
          {{ $errors->first('email') }}
        </div>
        @endif
      </div>

      <div class="form-group">
        <label for="password">{{ __('Password') }}</label>
        <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" tabindex="2" required>
        @if ($errors->has('password'))
        <div class="invalid-feedback">
          {{ $errors->first('password') }}
        </div>
        @endif
      </div>

      <div class="form-group">
        <label for="password-confirm">{{ __('Confirm Password') }}</label>
        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" tabindex="2" required>
      </div>

      <div class="form-group">
        <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
          {{ __('Reset Password') }}
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
