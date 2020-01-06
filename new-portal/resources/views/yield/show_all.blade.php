@extends('layouts.app')

@section('content')

    @forelse ($yields as $yield)
        <p onclick="window.location.href='{{ route('show_yield_details', ['yield' => encrypt($yield->id)]) }}'">
            Yield: {{ $yield->id }}; Valor: {{ $yield->value }}
        </p>
    @empty
        <p> Not Yield Found </p>
    @endforelse
@endsection
