@extends('layouts.app')

@section('content')
<!-- @if ($message = Session::get('success'))
<div class="alert alert-success alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
</div>
@endif
@if ($message = Session::get('error'))
<div class="alert alert-danger alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
</div>
@endif -->
<div class="alert alert-danger d-none flash" role="alert">
    <strong class="alert-link flash_message"></strong>
</div>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login With OTP') }}</div>
                <div class="card-body">
                    <form method="POST" id="on_submit">
                        @csrf
                        <!-- email and mobile -->
                        <div class="first_row">
                            <div class="row mb-3">
                                <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                                <div class="col-md-6">
                                    <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" autocomplete="email">
                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                    <span class="text-danger" id="emailError"></span>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="mobile" class="col-md-4 col-form-label text-md-end">{{ __('Mobile Number') }}</label>

                                <div class="col-md-6">
                                    <input id="mobile" type="text" class="form-control @error('mobile') is-invalid @enderror" name="mobile" value="{{ old('mobile') }}" autocomplete="mobile">
                                    @error('mobile')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                    <span class="text-danger" id="mobileError"></span>
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="button" id="generate_otp_btn" class="btn btn-primary">
                                        {{ __('Generate Otp') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- end email and mobile -->

                        <!-- otp validation -->
                        <div class="second_row d-none">
                            <input type="hidden" id="id" value="">
                            <div class="row mb-3 mt-3">
                                <label for="user_otp" class="col-md-4 col-form-label text-md-end">{{ __('Enter OTP') }}</label>

                                <div class="col-md-6">
                                    <input id="user_otp" type="number" class="form-control @error('user_otp') is-invalid @enderror" name="user_otp" value="{{ old('user_otp') }}" autocomplete="user_otp">
                                    @error('user_otp')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                    <span class="text-danger" id="otpError"></span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-8 offset-md-4">
                                    <button type="button" id="otp" class="btn btn-primary margin-right">
                                        {{ __('Login') }}
                                    </button>
                                    <button type="button" id="resend_otp" class="btn btn-primary d-none">
                                        {{ __('Resend OTP') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- end otp validation -->
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        // for mobile email route
        $(document).on('click', '#generate_otp_btn', function(e) {
            e.preventDefault();
            $('#mobileError').text('');
            $('#emailError').text('');
            $('.flash_message').html('');
            $('.flash').addClass('d-none');
            let email = $('#email').val();
            let mobile = $('#mobile').val();
            $.ajax({
                url: "{{ route('loginWithOtp.store') }}",
                method: "post",
                data: {
                    email: email,
                    mobile: mobile
                },
                // contentType: false,
                // processData: false,
                beforeSend: function() {
                    $('#generate_otp_btn').html('Please Wait');
                    $('#generate_otp_btn').prop('disabled', true);
                },
                success: function(response) {
                    // let data1 = JSON.parse(response);
                    if (response.status == 200) {
                        $('.flash_message').html(response.message);
                        $('.flash').removeClass('alert-danger');
                        $('.flash').addClass('alert-success');
                        $('.flash').removeClass('d-none');
                        $('.first_row').addClass('d-none');
                        $('#resend_otp').removeClass('d-none');
                        $('.second_row').removeClass('d-none');
                        $('#id').val(response.data);
                    } else if (response.status == 402) {
                        $('.flash_message').html(response.message);
                        $('.flash').removeClass('alert-success');
                        $('.flash').addClass('alert-danger');
                        $('.flash').removeClass('d-none');
                    }
                },
                error: function(response) {
                    $('#mobileError').text(response.responseJSON.errors.mobile);
                    $('#emailError').text(response.responseJSON.errors.email);
                },
                complete: function() {
                    $('#generate_otp_btn').html('Generate otp');
                    $('#generate_otp_btn').prop('disabled', false);
                },
            });
        });

        // for otp route
        $(document).on('click', '#otp', function(e) {
            e.preventDefault();
            $('#otpError').text('');
            $('.flash_message').html('');
            $('.flash').addClass('d-none');
            let user_otp = $('#user_otp').val();
            let id = $('#id').val();
            $.ajax({
                url: "{{ route('verifyOtp') }}",
                method: "post",
                data: {
                    user_otp: user_otp,
                    id: id
                },
                // contentType: false,
                // processData: false,
                beforeSend: function() {
                    $('#otp').html('Please Wait');
                    $('#otp').removeClass('btn-primary');
                    $('#otp').addClass('btn-warning');
                    $('#otp').prop('disabled', true);
                },
                success: function(response) {
                    if (response.status == 200) {
                        window.location.href = "{{route('home')}}";
                    } else {
                        $('.flash_message').html(response.message);
                        $('.flash').removeClass('alert-success');
                        $('.flash').addClass('alert-danger');
                        $('.flash').removeClass('d-none');
                    }
                },
                error: function(response) {
                    $('#otpError').text(response.responseJSON.errors.user_otp);
                },
                complete: function() {
                    $('#otp').html('Login');
                    $('#otp').removeClass('btn-warning');
                    $('#otp').addClass('btn-primary');
                    $('#otp').prop('disabled', false);
                },
            });
        });

        // resend otp
        $(document).on('click', '#resend_otp', function(e) {
            e.preventDefault();
            $('.flash_message').html('');
            $('.flash').addClass('d-none');
            let id = $('#id').val();
            $.ajax({
                url: "{{ route('resendOtp') }}",
                method: "post",
                data: {
                    id: id
                },
                // contentType: false,
                // processData: false,
                beforeSend: function() {
                    $('#resend_otp').html('Please Wait');
                    $('#resend_otp').removeClass('btn-primary');
                    $('#resend_otp').addClass('btn-success');
                    $('#resend_otp').prop('disabled', true);
                },
                success: function(response) {
                    if (response.status == 200) {
                        $('.flash_message').html(response.message);
                        $('.flash').removeClass('alert-danger');
                        $('.flash').addClass('alert-success');
                        $('.flash').removeClass('d-none');
                    }
                },
                error: function(response) {
                    // $('#otpError').text(response.responseJSON.errors.user_otp);
                },
                complete: function() {
                    $('#resend_otp').html('Resend OTP');
                    $('#resend_otp').removeClass('btn-success');
                    $('#resend_otp').addClass('btn-primary');
                    $('#resend_otp').prop('disabled', false);
                },
            });
        });
    });
</script>
@endsection