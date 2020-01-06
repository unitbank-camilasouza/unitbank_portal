@extends('layouts.app')

@section('content')
    @forelse ($withdrawals as $withdraw)
        <p onclick="window.location.href='{{ route('show_withdraw_details', ['withdraw' => encrypt($withdraw->id)]) }}'">
            Withdraw: {{ $withdraw->id }}; Valor: {{ $withdraw->value }}
        </p>
    @empty
        <p> Not Withdraw Found </p>
    @endforelse
@endsection
