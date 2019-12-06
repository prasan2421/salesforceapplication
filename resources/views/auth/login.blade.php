@extends('layouts.plain')

@section('title', __('Login'))

@section('content')
<div class="card card-primary">
  <div class="card-header"><h4>{{ __('Login') }}</h4></div>

  <div class="card-body">
    <form method="POST" action="{{ route('login') }}">
      @csrf
      
      <div class="form-group">
        <label for="username">{{ __('Username') }}</label>
        <input id="username" type="text" class="form-control{{ $errors->has('username') ? ' is-invalid' : '' }}" name="username" value="{{ old('username') }}" tabindex="1" required autofocus>
        @if ($errors->has('username'))
        <div class="invalid-feedback">
          {{ $errors->first('username') }}
        </div>
        @endif
      </div>

      <div class="form-group">
        <div class="d-block">
            <label for="password" class="control-label">{{ __('Password') }}</label>
            <div class="float-right">
              @if (Route::has('password.request'))
              <a href="{{ route('password.request') }}" class="text-small">
                {{ __('Forgot Password?') }}
              </a>
              @endif
            </div>
        </div>
        <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" tabindex="2" required>
        @if ($errors->has('password'))
        <div class="invalid-feedback">
          {{ $errors->first('password') }}
        </div>
        @endif
      </div>

      <div class="form-group">
        <div class="custom-control custom-checkbox">
          <input type="checkbox" name="remember" class="custom-control-input" tabindex="3" id="remember-me" {{ old('remember') ? 'checked' : '' }}>
          <label class="custom-control-label" for="remember-me">{{ __('Remember Me') }}</label>
        </div>
      </div>

      <div class="form-group">
        <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
          {{ __('Login') }}
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
