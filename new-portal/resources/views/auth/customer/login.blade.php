@extends('layouts.app')

@section('content')
    <div id="customer-login-form"
         data-action_route="{{ route('customer_login') }}"
         data-csrf="{{ csrf_token() }}"
         data-login_label="{{ __('CPF') }}"
         data-password_label="{{ __('Password') }}"
         data-placeholder_login={{ __('CPF') }}
         data-placeholder_password={{ __('Password') }}
         data-submit_value="{{ __('Submit') }}"></div>
@endsection
