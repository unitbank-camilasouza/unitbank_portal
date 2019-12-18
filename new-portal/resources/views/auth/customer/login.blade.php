@extends('layouts.app')

@section('content')
    @if ($errors->any())

        @foreach ($errors->all() as $error_message)
            <p> {{ $error_message }} </p>
        @endforeach
    @endif
    <div id="customer-login-form"
         data-action_route="{{ route('customer_login') }}"
         data-csrf="{{ csrf_token() }}"
         data-login_label="{{ __('CPF') }}"
         data-password_label="{{ __('Password') }}"
         data-placeholder_login={{ __('CPF') }}
         data-placeholder_password={{ __('Password') }}
         data-submit_value="{{ __('Submit') }}"></div>
@endsection
