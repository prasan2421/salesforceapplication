@extends('layouts.plain')

@section('title', __('Reset Password'))

@section('content')
<div class="card card-primary">
  <div class="card-header"><h4>{{ __('Reset Password') }}</h4></div>

  <div class="card-body">
    @if (session('status'))
      <div class="alert alert-success">
        {{ session('status') }}
      </div>
    @endif
    <form method="POST" action="{{ route('password.email') }}">
      @csrf
      
      <div class="form-group">
        <label for="email">{{ __('E-Mail Address') }}</label>
        <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" tabindex="1" required autofocus>
        @if ($errors->has('email'))
        <div class="invalid-feedback">
          {{ $errors->first('email') }}
        </div>
        @endif
      </div>

      <div class="form-group">
        <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
          {{ __('Send Password Reset Link') }}
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
