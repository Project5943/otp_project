@extends('layouts.app')

@section('content')
@if ($message = Session::get('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <strong>{{ $message }}</strong>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if ($message = Session::get('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <strong>{{ $message }}</strong>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login With OTP') }}</div>

                <div class="card-body">
                    <form method="get" action="{{ route('verifyOtpLoad') }}">
                        @csrf
                        <input type="hidden" name="id" @if ($row) value ="{{ $row }}" @endif>
                        <div class="row mb-3">
                            <label for="user_otp" class="col-md-4 col-form-label text-md-end">{{ __('Enter OTP') }}</label>

                            <div class="col-md-6">
                                <input id="user_otp" type="text" class="form-control @error('user_otp') is-invalid @enderror" name="user_otp" value="{{ old('user_otp') }}" autocomplete="user_otp" autofocus>
                                @error('user_otp')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection