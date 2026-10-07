@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card card-premium">
                    <div class="card-header text-center fw-bold">{{ __('Login') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="label-premium">{{ __('Email Address') }}</label>
                                <input id="email" type="email"
                                    class="form-control form-control-premium @error('email') is-invalid @enderror" name="email"
                                    value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="label-premium">{{ __('Password') }}</label>
                                <input id="password" type="password"
                                    class="form-control form-control-premium @error('password') is-invalid @enderror" name="password"
                                    required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mb-4 d-flex justify-content-between align-items-center">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                        {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label text-muted small" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                                @if (Route::has('password.request'))
                                    <a class="btn-link-premium small" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>

                            <div class="mb-3">
                                <button type="submit" class="btn btn-premium w-100 py-2">
                                    {{ __('Login') }}
                                </button>
                            </div>
                        </form>

                        <div class="d-flex align-items-center my-4">
                            <hr class="flex-grow-1 text-muted my-0">
                            <span class="mx-3 text-muted small text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">Or Login With</span>
                            <hr class="flex-grow-1 text-muted my-0">
                        </div>

                        <div class="d-grid gap-2">
                            <a href="{{ route('auth.google') }}" class="btn-social-google w-100 py-2">
                                <i class="fa fa-google text-danger"></i> {{ __('Google Account') }}
                            </a>
                            <div class="row g-2 mt-1">
                                <div class="col-md-6 col-12">
                                    <a class="btn-social-otp-email w-100 py-2" href="{{ route('login.with.otp') }}">
                                        <i class="fa fa-envelope"></i> {{ __('Email OTP') }}
                                    </a>
                                </div>
                                <div class="col-md-6 col-12">
                                    <a class="btn-social-otp-mobile w-100 py-2" href="{{ route('otp.login') }}">
                                        <i class="fa fa-mobile" style="font-size: 1.25rem; line-height: 1;"></i> {{ __('Mobile OTP') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
