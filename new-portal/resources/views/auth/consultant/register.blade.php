{{--
Author: Davi Mendes Pimentel
last modified date: 11/12/2019
 --}}

@extends('layouts.app')

@section('content')
    <div id="register-consultant-form"
         data-placeholder_first_name="{{ __('First Name') }}"
         data-placeholder_last_name="{{ __('First Name') }}"
         data-placeholder_cpf="{{ __('First Name') }}"
         data-action_route="{{ route('register_consultant') }}"></div>
@endsection
