@extends('layouts.app')

@section('content')
    <div id="admin-login-form"
         data-action_route="{{ route('admin_login') }}"
         data-csrf="{{ csrf_token() }}"
         data-login_label="{{ __('Login') }}"
         data-password_label="{{ __('Password') }}"
         data-placeholder_login={{ __('Login') }}
         data-placeholder_password={{ __('Password') }}
         data-submit_value="{{ __('Submit') }}"></div>
@endsection
