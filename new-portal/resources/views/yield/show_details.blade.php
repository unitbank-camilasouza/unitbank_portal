@extends('layouts.app')

@section('content')
    <p> ID do Rendimento: {{ $yield->id }} </p>
    <p> ID do Contrato Rendido: {{ $yield->id_contract }} </p>
    <p> Valor do Rendimento: {{ $yield->value }} </p>
    <p> Rendido em: {{ date_format(new DateTime($yield->yielded_at), 'd/m/Y') }} </p>
@endsection
