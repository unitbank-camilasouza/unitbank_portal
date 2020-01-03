@extends('layouts.app')

@section('content')
    <p style="text-align: center;"> {{ $contract->id }} </p>
    <p style="text-align: center;"> {{ $current_contract->current_value }} </p>

    Yields:
    @forelse ($yields as $y)
    <br>
        <p onclick="window.location.href='{{ route('show_yield_details', ['yield' => encrypt($y->id)]) }}'"> Yield: {{ $y->id }} </p>
    @empty
        No Yield found
    @endforelse
    <br>

    Withdrawals:
    @forelse ($withdrawals as $w)
    <br>
        <p onclick="window.location.href='{{ route('show_withdraw_details', ['withdraw' => encrypt($w->id)]) }}'"> Withdraw: {{ $w->id }} </p>
    @empty
        No Withdrawn found
    @endforelse
    <br>

    Customers:
    @forelse ($customers as $c)
    <br>
        Customer: {{ $c->id }}
    @empty
        No Customer found
    @endforelse
@endsection
