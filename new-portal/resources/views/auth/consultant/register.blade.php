{{--
Author: Davi Mendes Pimentel
last modified date: 11/12/2019
 --}}

@extends('layouts.app')

@php
    $translated_nationalities;
    $translated_genders;

    foreach ($nationalities as $n_key => $n_value) {
        $translated_nationalities[$n_key] = __($n_value);
    }

    foreach ($genders as $g_key => $g_value) {
        $translated_genders[$g_key] = __($g_value);
    }
@endphp

@section('content')
    <div id="register-consultant-form"
         data-action_route="{{ route('register_consultant') }}"
         data-placeholder_first_name="{{ __('First Name') }}"
         data-placeholder_last_name="{{ __('Last Name') }}"
         data-placeholder_cpf="{{ __('CPF') }}"
         data-genders="{{ $translated_nationalities }}"
         data-nationalities="{{ $translated_genders }}"
         data-submit_value="{{ __('Submit') }}"></div>
@endsection
