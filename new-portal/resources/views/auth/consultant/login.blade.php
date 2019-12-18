@extends('layouts.app')

@section('content')
<div className="form-control">
    <form id="login" action="{{ route('consultant_login') }}" method="post">
        @csrf

        @error('cpf')
            <p> {{ $message }} </p>
        @enderror
        <label htmlFor="cpf"> {{ _('CPF') }} </label>
        <input type="text" name="cpf" id="cpf" class="@error('cpf')is-invalid @enderror"
               placeholder="{{ _('CPF') }}" required autoFocus/>


        <label htmlFor="password"> {{ _('Password') }} </label>
        <input type="text" name="password" id="password"
               placeholder="{{ _('Password') }}" required/>

        <input id="submit_button" type="submit" value={{ _('Submit') }}/>

        <script>
            $(document).ready(function () {
                $('#cpf').focusout(function () {
                    // TODO: finish the validation
                })
            });
        </script>
    </form>
</div>
@endsection
